<?php

namespace App\Http\Controllers\Admin;

use App\BasicExtra;
use App\BasicSetting;
use App\User;
use App\CpdRequired;
use App\CpdExternalPoint;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Helpers\PackageHelper;
use App\Jobs\PasswordChangeJob;
use App\Jobs\RegisterEmailJob;
use App\Jobs\UserActiveJob;
use App\Jobs\UserVerifiedJob;
use App\Language;
use App\Notifications\CPDExternalPointReviewNotify;
use App\Notifications\CPDPointUpdateNotify;
use App\Notifications\PasswordChangeNotify;
use App\Package;
use App\PackageOrder;
use App\Subscription;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class RegisterUserController extends Controller
{
    public function index(Request $request)
    {
        $term = $request->term;
        $order = $request->order;
        $packages = Package::where('status', '!=', 0)->get();

        $users = User::when($term, function ($query, $term) {
            $query->where('username', 'like', '%' . $term . '%')
                ->orWhere('email', 'like', '%' . $term . '%');
        })->when($order, function ($q, $order) {
            if ($order == 'oldest') {
                return $q->oldest();
            } elseif ($order == 'a_z') {
                return $q->orderBy('fname', 'asc');
            } elseif ($order == 'z_a') {
                return $q->orderBy('fname', 'desc');
            } else {
                return $q->latest();
            }
        })->when(!$order, function ($builder, $order) {
            return $builder->orderBy('id', 'desc');
        })
            ->with(['subscription', 'subscription.current_package'])
            ->paginate(10);
        return view('admin.register_user.index', compact('users', 'packages'));
    }

    public function view($id)
    {
        $user = User::findOrFail($id);
        $orders = $user->orders()->paginate(10);
        return view('admin.register_user.details', compact('user', 'orders'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.register_user.edit', compact('user'));
    }

    public function updateMemberId()
    {

        request()->validate([
            'id' => ['required'],
            'membershipid' => ['required', "unique:users,membership_id," . request('id')]
        ]);

        $user = User::findorFail(request('id'));
        $user->membership_id = request('membershipid');
        $user->save();

        Session::flash('success', $user->username . ' membership ID update successfully!');
        return "success";
    }

    public function updateCpdPoint(Request $request)
    {
        $this->validate($request, [
            'type' => 'required',
            'amount' => 'required',
            'id' => 'required',
            'cpdtype' => 'nullable',
            'start_date' => 'required_if:cpdtype,external',
            'end_date' => 'required_if:cpdtype,external',
            'training_title' => 'required_if:cpdtype,external',
            'organized_by' => 'required_if:cpdtype,external',
            'certificate' => 'required_if:cpdtype,external',
        ], [
            'amount.required' => "Please enter the CPD amount first",
            '*.required_if' => "The :attribute field is required for external CPD point",
        ]);
        $fdoyear = date('Y') . '-01-01';
        $ldoyear = date('Y') . '-12-31';
        $fDay = Carbon::parse($fdoyear);
        $lDay = Carbon::parse($ldoyear);
        $user = User::findOrFail($request->id);

        $cpdtype = $request->has('cpdtype') ? $request->cpdtype : null;

        if ($cpdtype == 'external') {
            if (Carbon::parse($request->end_date)->isBetween($fDay, $lDay)) {
                updateCpdPoint($user->id, $request->amount, $request->type, $cpdtype);
            }
            if ($request->hasFile('certificate')) {
                $file = $request->file('certificate');
                $filename = uniqid($user->id . '_' . time() . '-') . '.' . $file->getClientOriginalExtension();
                $file->move(storage_path('app/member_external_cert/'), $filename);
                $cert = $filename;
            }
            $external = CpdExternalPoint::create([
                'user_id' => $user->id,
                'amount' => $request->amount,
                'trx_type' => $request->type,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'training_title' => $request->training_title,
                'organized_by' => $request->organized_by,
                'certificate' => $cert ?? null,
                'details' => $request->details,
                'remarks' => "External CPD Point by Admin",
                'status' => 1,
            ]);

            $title = trans('Your external CPD point ') . request('amount') . ' ' . ($request->type == '-' ? trans('subtract') : trans('added')) . (' by Admin.');
            $user->notify(new CPDPointUpdateNotify($title, 'External', $external));

            return back()->with('success', 'External CPD Point ' . ($request->type == '-' ? trans('subtract') : trans('added')) . ' successfully');
        }

        updateCpdPoint($user->id, $request->amount, $request->type, $cpdtype);

        $title = trans('Your internal CPD point ') . request('amount') . ' ' . ($request->type == '-' ? trans('subtract') : trans('added')) . (' by Admin.');
        $user->notify(new CPDPointUpdateNotify($title, 'Internal', ''));

        Session::flash('success', "CPD Point updated successfully for '$user->username' user");

        return back();
    }

    public function update(Request $request, $id)
    {

        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $bs = $currentLang->basic_setting;
        // $be = $currentLang->basic_extended;

        $messages = [];

        $rules = [
            'fname' => 'required',
            'lname' => 'required',
            'email'           => 'required|email|unique:users,email,' . $id,
            'date_of_birth'   => 'required',
            'address'       => 'required',
            'gender'          => 'required',
            'nation'          => 'required',
            'personal_phone'  => 'required',
            'country'         => 'required',
        ];

        $request->validate($rules, $messages);

        $user = User::findorfail($id);
        $input = $request->all();

        if ($request->fname != $user->fname || $request->lname != $user->lname) {
            $input['username'] = Str::replace(' ', '', $request->fname . $request->lname . rand(0, 999));
            while (User::where('username', $input['username'])->count()) {
                $input['username'] =  Str::replace(' ', '', $request->fname . $request->lname . rand(0, 99) . Str::random(2));
            }
        }

        // $input['date_of_birth'] = Carbon::parse(Carbon::createFromFormat("d-m-Y", $request->date_of_birth))->format('Y-m-d');
        $input['date_of_birth'] = Carbon::parse($request->date_of_birth)->format('Y-m-d');

        $user->fill($input)->save();
        $user->membership_id = 'M' . str_pad($user->id, 5, '0', STR_PAD_LEFT) . strtoupper(Str::random(4));
        $user->save();

        $user->cpd_required()->firstOrCreate(['year' => date('Y'),], [
            'required_points' => $bs->def_required_cpd_point,
        ]);

        Session::flash('success', $user->username . ' status update successfully!');
        return back();
    }

    public function userban(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        $user->update([
            'status' => $request->status,
        ]);

        $bs = BasicSetting::first();
        UserActiveJob::dispatch($bs, $user)->delay(now()->addSecond(5));

        Session::flash('success', $user->username . ' status update successfully!');
        return back();
    }

    public function emailStatus(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        $user->update([
            'email_verified' => $request->email_verified,
        ]);

        $bs = BasicSetting::first();
        UserVerifiedJob::dispatch($bs, $user)->delay(now()->addSecond(5));

        Session::flash('success', 'Email status updated for ' . $user->username);
        return back();
    }

    public function delete(Request $request)
    {
        $user = User::findOrFail($request->user_id);

        if ($user->conversations()->count() > 0) {
            $convs = $user->conversations()->get();
            foreach ($convs as $key => $conv) {
                @unlink('assets/front/user-suppor-file/' . $conv->file);
                $conv->delete();
            }
        }

        if ($user->courseOrder()->count() > 0) {
            $coursePurchases = $user->courseOrder()->get();
            foreach ($coursePurchases as $key => $cp) {
                @unlink('assets/front/receipt/' . $cp->receipt);
                @unlink('assets/front/invoices/course/' . $cp->invoice);
                $cp->delete();
            }
        }

        if ($user->course_reviews()->count() > 0) {
            $user->course_reviews()->delete();
        }

        if ($user->donation_details()->count() > 0) {
            $donations = $user->donation_details()->get();
            foreach ($donations as $key => $donation) {
                @unlink('assets/front/receipt/' . $donation->receipt);
                $donation->delete();
            }
        }

        if ($user->event_details()->count() > 0) {
            $bookings = $user->event_details()->get();
            foreach ($bookings as $key => $booking) {
                @unlink('assets/front/receipt/' . $booking->receipt);
                @unlink('assets/front/invoices/' . $booking->invoice);
                $booking->delete();
            }
        }

        if ($user->order_items()->count() > 0) {
            $user->order_items()->delete();
        }

        if ($user->package_orders()->count() > 0) {
            $pos = $user->package_orders()->get();
            foreach ($pos as $key => $po) {
                @unlink('assets/front/receipt/' . $po->receipt);
                @unlink('assets/front/invoices/' . $po->invoice);
                $po->delete();
            }
        }

        if ($user->orders()->count() > 0) {
            $orders = $user->orders()->get();
            foreach ($orders as $key => $order) {
                @unlink('assets/front/receipt/' . $order->receipt);
                @unlink('assets/front/invoices/product/' . $order->invoice_number);
                $order->delete();
            }
        }

        if ($user->product_reviews()->count() > 0) {
            $user->product_reviews()->delete();
        }

        if ($user->subscription()->count() > 0) {
            @unlink('assets/front/receipt/' . $user->subscription->receipt);
            @unlink('assets/front/invoices/' . $user->subscription->invoice);
            $user->subscription()->delete();
        }

        if ($user->tickets()->count() > 0) {
            $tickets = $user->tickets()->get();
            foreach ($tickets as $key => $ticket) {
                @unlink('assets/front/user-suppor-file/' . $ticket->zip_file);
                $ticket->delete();
            }
        }

        @unlink('assets/front/img/user/' . $user->photo);
        $user->delete();

        Session::flash('success', 'User deleted successfully!');
        return back();
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;

        foreach ($ids as $id) {
            $user = User::findOrFail($id);

            if ($user->conversations()->count() > 0) {
                $convs = $user->conversations()->get();
                foreach ($convs as $key => $conv) {
                    @unlink('assets/front/user-suppor-file/' . $conv->file);
                    $conv->delete();
                }
            }

            if ($user->courseOrder()->count() > 0) {
                $coursePurchases = $user->courseOrder()->get();
                foreach ($coursePurchases as $key => $cp) {
                    @unlink('assets/front/receipt/' . $cp->receipt);
                    @unlink('assets/front/invoices/course/' . $cp->invoice);
                    $cp->delete();
                }
            }

            if ($user->course_reviews()->count() > 0) {
                $user->course_reviews()->delete();
            }

            if ($user->donation_details()->count() > 0) {
                $donations = $user->donation_details()->get();
                foreach ($donations as $key => $donation) {
                    @unlink('assets/front/receipt/' . $donation->receipt);
                    $donation->delete();
                }
            }

            if ($user->event_details()->count() > 0) {
                $bookings = $user->event_details()->get();
                foreach ($bookings as $key => $booking) {
                    @unlink('assets/front/receipt/' . $booking->receipt);
                    @unlink('assets/front/invoices/' . $booking->invoice);
                    $booking->delete();
                }
            }

            if ($user->order_items()->count() > 0) {
                $user->order_items()->delete();
            }

            if ($user->package_orders()->count() > 0) {
                $pos = $user->package_orders()->get();
                foreach ($pos as $key => $po) {
                    @unlink('assets/front/receipt/' . $po->receipt);
                    @unlink('assets/front/invoices/' . $po->invoice);
                    $po->delete();
                }
            }

            if ($user->orders()->count() > 0) {
                $orders = $user->orders()->get();
                foreach ($orders as $key => $order) {
                    @unlink('assets/front/receipt/' . $order->receipt);
                    @unlink('assets/front/invoices/product/' . $order->invoice_number);
                    $order->delete();
                }
            }

            if ($user->product_reviews()->count() > 0) {
                $user->product_reviews()->delete();
            }

            if ($user->subscription()->count() > 0) {
                @unlink('assets/front/receipt/' . $user->subscription->receipt);
                @unlink('assets/front/invoices/' . $user->subscription->invoice);
                $user->subscription()->delete();
            }

            if ($user->tickets()->count() > 0) {
                $tickets = $user->tickets()->get();
                foreach ($tickets as $key => $ticket) {
                    @unlink('assets/front/user-suppor-file/' . $ticket->zip_file);
                    $ticket->delete();
                }
            }

            @unlink('assets/front/img/user/' . $user->photo);
            $user->delete();
        }

        Session::flash('success', 'Users deleted successfully!');
        return "success";
    }

    public function changePass($id)
    {
        $data['user'] = User::findOrFail($id);
        return view('admin.register_user.password', $data);
    }

    public function updatePassword(Request $request)
    {
        $messages = [
            'password.required' => 'New password is required',
            'password.regex' => 'New :attribute uppercase, lowercase, number, min 6 characters, must contain a special character',
            'password_confirmation.required' => 'Confirm password is required',
        ];

        $request->validate([
            'password'  => [
                'required', 'confirmed',
                'regex:' . User::PASSWORD_REGEX
                // uppercase, lowercase, number, 10 characters, must contain a special character
            ],
            'password_confirmation' => 'required',
        ], $messages);


        $user = User::findOrFail($request->user_id);
        if ($request->password != $request->password_confirmation) {
            return back()->with('error', __('Confirm password does not match.'));
        }
        $input['password'] = Hash::make($request->password);
        $input['open_password'] = $request->password;
        $input['is_password_expire'] = 'no';
        $input['password_expire_date'] = Carbon::now()->addMonth(1)->format('Y-m-d');
        $user->update($input);
        $title = trans('Your password change by admin. Please check your email');

        $bs = BasicSetting::first();

        $user->notify(new PasswordChangeNotify($title));
        PasswordChangeJob::dispatch($bs, $user, $request->password);

        Session::flash('success', 'Password update for ' . $user->username);
        return back();
    }

    public function addUser()
    {
        return view('admin.register_user.add');
    }

    public function saveUser(Request $request)
    {
        $messages = [
            'g-recaptcha-response.required' => 'Please verify that you are not a robot.',
            'g-recaptcha-response.captcha' => 'Captcha error! try again later or contact site admin.',
            'password.regex' => 'Password must be uppercase, lowercase, number, symbol and min 10 characters'
        ];

        $rules = [
            'fname' => 'required',
            'lname' => 'required',
            // 'username' => 'required|unique:users',
            'email'           => 'required|email|unique:users',
            'password'        => [
                'required', 'confirmed',
                'regex:' . User::PASSWORD_REGEX
                // uppercase, lowercase, number, 10 characters, must contain a special character
            ],
            'date_of_birth'   => 'required',
            'age'             => 'nullable',
            'gender'          => 'required',
            'nation'          => 'required',
            'personal_phone'  => 'required',
            'country'         => 'required',
            'company_fax'     => 'required',
            // 'idcard_no'       => 'required',
        ];


        $request->validate($rules, $messages);

        $user = new User;
        $input = $request->all();
        $input['status'] = 0;
        $input['password'] = bcrypt($request['password']);
        $input['open_password'] = $request['password'];
        $input['password_expire_date'] = Carbon::now()->addMonth(1)->format('Y-m-d');
        // $input['date_of_birth'] = Carbon::parse(Carbon::createFromFormat("d-m-Y", $request->date_of_birth))->format('Y-m-d');
        $input['date_of_birth'] = Carbon::parse($request->date_of_birth)->format('Y-m-d');
        $token = md5(time() . $request->fname . $request->lname . $request->email);
        $input['verification_link'] = $token;
        $input['cpd_point'] = $bs->def_required_cpd_point;
        $input['status'] = 1;
        $input['email_verified'] = 'Yes';
        $input['username'] = Str::replace(' ', '', $request->fname . $request->lname . rand(0, 999));
        while (User::where('username', $input['username'])->count()) {
            $input['username'] =  Str::replace(' ', '', $request->fname . $request->lname . rand(0, 99) . Str::random(2));
        }
        $user->fill($input)->save();
        $user->membership_id = 'M' . str_pad($user->id, 5, '0', STR_PAD_LEFT) . strtoupper(Str::random(4));
        $user->save();

        $bs = BasicSetting::first();

        $user->cpd_required()->create([
            'year' => date('Y'),
            'required_points' => $bs->def_required_cpd_point,
        ]);

        Session::flash('success', "User save successfully");
        return redirect()->route('admin.register.user');
    }

    public function membershipTracker(Request $request)
    {
        if (request()->ajax()) {
            $cpd_point = CpdRequired::where(['user_id' => request('user_id')])->get();

            return response()->json($cpd_point);
        }

        $users = User::when($request->term, function ($query, $term) {
            $query->where('username', 'like', '%' . $term . '%')
                ->orWhere('fname', 'like', '%' . $term . '%')
                ->orWhere('lname', 'like', '%' . $term . '%')
                ->orWhere('email', 'like', '%' . $term . '%');
        })->with(['cpd_required'])->paginate(10);

        return view('admin.register_user.membership-tracker', compact('users'));
    }

    public function externalCpdPoint(Request $request)
    {
        $cpd = CpdExternalPoint::where('user_id', $request->user_id)->where('status', 1)->get();

        if ($cpd->count() > 0) {
            $html = [];
            foreach ($cpd as $cp) {
                $html[] = '<tr>';
                $html[] = '<td>' . dateFormat($cp->start_date) . '</td>';
                $html[] = '<td>' . dateFormat($cp->end_date) . '</td>';
                $html[] = "<td>{$cp->training_title}</td>";
                $html[] = "<td>{$cp->amount}</td>";
                $html[] = "<td>{$cp->organized_by}</td>";
                $ruri = route('admin.cpd.external.cert', ['cert' => $cp->id]);
                $html[] = "<td><form action='{$ruri}' method=\"POST\">";
                $html[] = csrf_field();
                $html[] = "<button type='submit' class='btn btn-primary btn-sm'>Certificate</button>";
                $html[] = "</form></td>";
                $html[] = "<td><span title=\"{$cp->details}\">" . Str::limit($cp->details, 25) . "</span></td>";
                $html[] = '</tr>';
            }
            return join($html);
        }
        return '<tr><td colspan="7" class="text-center">No data found</td></tr>';
    }

    public function requestedExtCpdPoint(Request $request)
    {
        $data['cpd_list'] = CpdExternalPoint::where('status', 0)->with('user')->get();
        return view('admin.register_user.requested-ext-cpd', $data);
    }

    public function responseRequestedExtCpd(Request $request)
    {
        $this->validate($request, [
            'action' => 'required',
            'id' => 'required',
        ]);
        $cpd = CpdExternalPoint::findOrFail($request->id);
        $title = trans('Your external CPD Point rejected');
        if ($request->action == 'accept') {
            $cpd->status = 1;
            $cpd->save();
            $fDay = Carbon::parse(date('Y-01-01'));
            $lDay = Carbon::parse(date('Y-12-31'));
            if (Carbon::parse($cpd->end_date)->isBetween($fDay, $lDay)) {
                $user = User::findOrFail($cpd->user_id);
                $user->cpd_point += $cpd->amount;
                $user->save();
            }
            $title = trans('Your external CPD Point accepted');
            $cpd->user->notify(new CPDExternalPointReviewNotify($title, $cpd));
            Session::flash('success', 'External CPD Point accepted successfully');
            return back();
        }
        $cpd->status = 2;
        $cpd->save();

        $cpd->user->notify(new CPDExternalPointReviewNotify($title, $cpd));
        Session::flash('success', 'External CPD Point rejected successfully');
        return back();
    }

    public function extAttendenceCert()
    {
        $cpd = CpdExternalPoint::findOrFail(request('cert'));
        $filePath = storage_path('app/member_external_cert/' . $cpd->certificate);

        if(!file_exists($filePath)) {
            abort(404);
        }

        return response()->download($filePath);
    }

    public function addReqCpdPoint(Request $request)
    {
        $this->validate($request, [
            'id' => 'nullable',
            'user_id' => 'required',
            'year' => 'required',
            'required_points' => 'required',
        ]);
        if (!empty($request->id)) {
            $cpd = CpdRequired::find($request->id);
            $cpd->required_points = $request->required_points;
            $cpd->year = $request->year;
            $cpd->save();
            return back()->with('success', "Required CPD Point updated successfully");
        }


        if (CpdRequired::where(['user_id' => $request->user_id, 'year' => $request->year])->exists()) {
            return back()
                ->withErrors(['year' => __('This year already exists in this user CPD')]);
        }

        $cpd = new CpdRequired();
        $cpd->user_id = $request->user_id;
        $cpd->required_points = $request->required_points;
        $cpd->year = $request->year;
        $cpd->save();
        return back()->with('success', "Required CPD Point added successfully");
    }

    public function activeMembership(Request $request)
    {
        $request->validate([
            'package_id' => 'required',
            'user_id' => 'required'
        ]);

        $user = User::findOrFail($request->user_id);
        $request->merge(['name' => $user->full_name, 'email' => $user->email]);


        $fields = [];

        $package = Package::findOrFail($request->package_id);

        $sub = Subscription::where('user_id', $request->user_id);
        $activeSub = Subscription::where('user_id', $request->user_id)->where('status', 1);

        if ($sub->count() > 0) {
            $sub = $sub->first();
        } else {
            $sub = new Subscription;
        }
        $sub->name = $request->name;
        $sub->email = $request->email;
        $sub->is_upgrade = $request->is_upgrade ?? 0;
        $sub->user_id = $request->user_id ?? null;

        $sub->status = '1';

        $sub->fields = json_encode($fields);
        $sub->gateway_type = 'offline';
        $sub->current_package_id = $package->id;
        switch ($package->duration) {
            case 'monthly':
                $sub->expire_date = today()->addMonth(1);
                break;
            case 'yearly':
            default:
                $sub->expire_date = today()->addYear(1);
        }

        $document_file = [];

        $sub->document_file = json_encode($document_file);

        $method = "offline";
        if ($activeSub->count() == 0) {
            $sub->current_payment_method = $method;
        } elseif ($activeSub->count() > 0) {
            $sub->next_payment_method = $method;
        } else {
            $sub->pending_payment_method = $method;
        }

        $sub->save();

//        $user->license_expire_date = $sub->expire_date;
//        $user->license_expire_notify_date = Carbon::parse($sub->expire_date)->subDays(7);
//        $user->license_expire_notify = 'no';
//        $user->save();

        return redirect()->back();
    }

    public function updateDefReqCpdPoint(Request $request) {
        $request->validate([
            'def_required_cpd_point' => 'required'
        ]);

        $bs = BasicSetting::first();
        $bs->def_required_cpd_point = $request->get('def_required_cpd_point');
        $bs->saveQuietly();

        return back();
    }
}
