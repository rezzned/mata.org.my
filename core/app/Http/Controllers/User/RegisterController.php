<?php

namespace App\Http\Controllers\User;

use App\User;
use App\Language;
use App\BasicExtra;
use App\BasicSetting as BS;
use Illuminate\Support\Str;
use App\BasicExtended as BE;
use Illuminate\Http\Request;
use App\Http\Helpers\KreativMailer;
use App\Http\Controllers\Controller;
use App\Jobs\RegisterEmailJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class RegisterController extends Controller
{

    public function __construct()
    {
        $bs = BS::first();
        $be = BE::first();

        Config::set('captcha.sitekey', $bs->google_recaptcha_site_key);
        Config::set('captcha.secret', $bs->google_recaptcha_secret_key);
    }

    public function registerPage()
    {
        $bex = BasicExtra::first();

        if ($bex->is_user_panel == 0) {
            return back();
        }

        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $be = $currentLang->basic_extended;
        $version = $be->theme_version;

        if ($version == 'dark') {
            $version = 'default';
        }

        $data['version'] = $version;

        return view('front.register', $data);
    }

    public function register(Request $request)
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $bs = $currentLang->basic_setting;
        // $be = $currentLang->basic_extended;

        $messages = [
            'g-recaptcha-response.required' => 'Please verify that you are not a robot.',
            'g-recaptcha-response.captcha' => 'Captcha error! try again later or contact site admin.',
            'password.regex' => 'Password must be uppercase, lowercase, number, symbol and min 10 characters'
        ];

        $rules = [
            // 'username' => 'required|unique:users',
            'email'           => 'required|email|unique:users',
            'password'        => [
                'required', 'confirmed',
                'regex:' . User::PASSWORD_REGEX
                // uppercase, lowercase, number, 10 characters, must contain a special character
            ],
            'date_of_birth'   => 'required',
            // 'age'             => 'required',
            'gender'          => 'required',
            'nation'          => 'required',
            'personal_phone'  => 'required',
            'country'         => 'required',
            'company_fax'         => 'required',
        ];

        if ($bs->is_recaptcha == 1) {
            $rules['g-recaptcha-response'] = 'required|captcha';
        }

        $request->validate($rules, $messages);

        $user = new User;
        $input = $request->all();
        $input['status'] = 0;
        $input['password'] = bcrypt($request['password']);
        $input['open_password'] = $request['password'];
        $input['password_expire_date'] = Carbon::now()->addMonth(1)->format('Y-m-d');

        unset($input['date_of_birth']);
        if (!empty($request->date_of_birth)) {
            $input['date_of_birth'] = Carbon::createFromFormat('d-m-Y', $request->date_of_birth)->format('Y-m-d');
        }
        $token = md5(time() . $request->fname . $request->lname . $request->email);
        $input['verification_link'] = $token;
        $input['cpd_point'] = 0;
        $input['username'] = Str::replace(' ', '', $request->fname . $request->lname . rand(0, 999));
        while (User::where('username', $input['username'])->count()) {
            $input['username'] =  Str::replace(' ', '', $request->fname . $request->lname . rand(0, 99) . Str::random(2));
        }
        $user->fill($input)->save();
        $user->membership_id = 'M' . str_pad($user->id, 5, '0', STR_PAD_LEFT) . strtoupper(Str::random(4));
        $user->save();

        $user->cpd_required()->create([
            'year' => date('Y'),
            'required_points' => $bs->def_required_cpd_point,
        ]);

        // register email queue job
        RegisterEmailJob::dispatch($bs, $user, $token)->delay(now()->addSecond(1));

        // return back()->with('sendmail', 'We will notify you through email when your account has been verified and activated.');
        return back()->with('sendmail', 'Verify Your Email Address. To confirm your email address, please click on the link in the email we sent you.');
    }


    public function token($token)
    {
        $user = User::where('verification_link', $token)->first();
        if (isset($user)) {
            $user->email_verified = 'Yes';
            $user->update();
            Auth::guard('web')->login($user);
            Session::flash('success', 'Email Verified Successfully');
            return redirect()->route('user-dashboard');
        }
    }
}
