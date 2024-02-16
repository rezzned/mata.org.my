<?php

namespace App\Http\Controllers\User;

use App\User;
use App\Event;
use App\Payment;
use App\Language;
use App\BasicExtra;
use App\CpdRequired;
use App\EventDetail;
use App\SlideBanner;
use App\PackageOrder;
use App\ProductOrder;
use App\Subscription;
use App\CpdTransaction;
use App\CpdExternalPoint;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\OfflineGateway;
use App\PaymentGateway;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Session;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use function Doctrine\Common\Cache\Psr6\get;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data['user'] = Auth::user();
        $data['orders'] = ProductOrder::where('user_id', Auth::user()->id)->orderby('id', 'desc')->limit(10)->get();
        $data['activeSub'] = Auth::user()->subscription;
        $data['banners'] = SlideBanner::all();
        $data['cpd_point_reqired'] = CpdRequired::where('user_id', auth()->id())->where('year', date('Y'))->first();
        // update last_logged_in to current timestamps
        $user = User::find(Auth::user()->id);
        $user->last_logged_in = Carbon::now();
        $user->save();

        return view('user.dashboard', $data);
    }

    public function notification()
    {
        $data['notifications'] = request()->user()->notifications()->latest()->get();
        return view('user.notification', $data);
    }

    public function notificationAllRead()
    {
        request()->user()->unreadNotifications->markAsRead();

        Session::flash('success', 'Notification all mark as read successfully');

        return back();
    }
    public function notificationRead($id)
    {
        request()->user()->unreadNotifications->where('id', $id)->markAsRead();

        Session::flash('success', 'Notification mark as read successfully');
        return back();
    }

    public function notificationDelete($id)
    {
        request()->user()->notifications()->where('id', $id)->delete();

        Session::flash('success', 'Notification deleted successfully');
        return back();
    }
    public function notificationTrashed()
    {
        request()->user()->notifications()->delete();

        Session::flash('success', 'All Notification trashed successfully');
        return back();
    }


    public function packages()
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $data['user'] = auth()->user();
        $data['packages'] = $currentLang->packages->where('status', 1);
        $data['active_sub'] = auth()->user()->subscription;
        // dd($data['active_sub'],auth()->id());

        return view('user.packages', $data);
    }

    public function packagesPayment($id)
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $bs = $currentLang->basic_setting;
        $be = $currentLang->basic_extended;

        $data['subscription'] = Subscription::find($id);

        if ($data['subscription']->payment_status == 1) {
            Session::flash('success', 'Subscription paid already.');
            return redirect()->route('user-packages');
        }

        $data['package'] = $data['subscription']->current_package;
        $data['bs'] = $bs;
        $data['gateways']  = PaymentGateway::whereStatus(1)->whereType('automatic')->get();

        $data['ogateways']  = OfflineGateway::wherePackageOrderStatus(1)->orderBy('serial_number', 'ASC')->get();
        $paystackData = PaymentGateway::whereKeyword('paystack')->first();
        $data['paystack'] = $paystackData->convertAutoData();
        $data['activeSub'] = Subscription::where('user_id', Auth::user()->id)->where('status', 1);

        $version = $be->theme_version;

        if ($version == 'dark') {
            $version = 'default';
        }

        $data['version'] = $version;

        // dd($data);

        return view('user.package-order-payment', $data);
    }



    public function profile()
    {
        $user = Auth::user();
        return view('user.profile', compact('user'));
    }

    public function profileupdate(Request $request)
    {
        // dd(request()->all());
        $file = $request->file('photo');
        $image_width = '';
        $image_height = '';
        if ($file) {
            $image_info = getimagesize($file);
            $image_width = $image_info[0];
            $image_height = $image_info[1];
        }

        $validate = [
            'username' => [
                'required',
                Rule::unique('users')->ignore(auth()->user()->id),
            ],
            'fname' => 'required',
            'lname' => 'required',
            'city' => 'required',
            'personal_phone' => 'required',
            'state' => 'required',
            'country' => 'required',
            'address' => 'required',
            // 'photo' =>  ['dimensions: ratio=1/1'], */
        ];

        $messages = [
            // 'photo.dimensions' => 'You need to upload a squared profile picture this image dimension is ' . $image_width . 'x' . $image_height
        ];

        $request->validate($validate, $messages);

        //--- Validation Section Ends
        $input = $request->all();
        $data = Auth::user();
        $input['license_expire_notify'] = 'yes';
        $licenseExpireDate = Carbon::createFromFormat('d-m-Y', request('license_expire_date'));
        if ($licenseExpireDate->subDays(7)->format('Y-m-d') >=  now()->subDays(7)->format('Y-m-d')) {
            $input['license_expire_notify'] = 'no';
        }
        $input['license_expire_date'] = $licenseExpireDate->format('Y-m-d');
        $input['license_expire_notify_date'] = $licenseExpireDate->subDays(7)->format('Y-m-d');
        if ($file) {
            $filename = time() . $file->getClientOriginalName();
            // $file->move('assets/front/img/user/', $name);

            $directory = User::IMAGE_PATH;
            if (!is_dir($directory)) {
                @mkdir($directory, 0775, true);
            }
            if ($data->photo) {
                @unlink($directory . $data->photo);
            }

            $img = Image::make($request['photo']);
            $img->fit(208, 208)->save($directory . '/' . $filename);
            $input['photo'] = $filename;
        }
        unset($input['date_of_birth']);
        if (!empty($request->date_of_birth)) {
            $input['date_of_birth'] = Carbon::createFromFormat('d-m-Y', $request->date_of_birth)->format('Y-m-d');
        }
        $data->update($input);

        Session::flash('success', 'Profile Update Successfully!');
        return back();
    }

    public function resetform()
    {
        return view('user.reset');
    }

    public function reset(Request $request)
    {
        $messages = [
            'cpass.required' => 'Current password is required',
            'npass.required' => 'New password is required',
            'npass.regex' => 'New :Must be at least have 1 Capital letter, symbol and number',
            'cfpass.required' => 'Confirm password is required',
        ];

        $request->validate([
            'cpass' => 'required',
            'npass' => ['required', 'regex:' . User::PASSWORD_REGEX],
            'cfpass' => 'required',
        ], $messages, [
            'npass' => 'password'
        ]);


        $user = Auth::user();
        if ($request->cpass) {
            if (Hash::check($request->cpass, $user->password)) {
                if ($request->npass == $request->cfpass) {
                    $input['password'] = Hash::make($request->npass);
                    $input['is_password_expire'] = 'no';
                    $input['password_expire_date'] = Carbon::now()->addDays(30);
                } else {
                    return back()->with('err', __('Confirm password does not match.'));
                }
            } else {
                return back()->with('err', __('Current password Does not match.'));
            }
        }

        $user->update($input);

        Session::flash('success', 'Successfully change your password');
        return back();
    }


    public function shippingdetails()
    {
        $bex = BasicExtra::first();

        if ($bex->is_shop == 0) {
            return back();
        }

        $user = Auth::user();

        return view('user.shipping_details', compact('user'));
    }

    public function shippingupdate(Request $request)
    {
        $request->validate([
            "shpping_fname" => 'required',
            "shpping_lname" => 'required',
            "shpping_email" => 'required',
            "shpping_number" => 'required',
            "shpping_city" => 'required',
            "shpping_state" => 'required',
            "shpping_address" => 'required',
            "shpping_country" => 'required',
        ]);


        Auth::user()->update($request->all());

        Session::flash('success', 'Shipping Details Update Successfully.');
        return back();
    }

    public function billingdetails()
    {
        $bex = BasicExtra::first();

        if ($bex->is_shop == 0) {
            return back();
        }

        $user = Auth::user();

        return view('user.billing_details', compact('user'));
    }

    public function billingupdate(Request $request)
    {
        $request->validate([
            "billing_fname" => 'required',
            "billing_lname" => 'required',
            "billing_email" => 'required',
            "billing_number" => 'required',
            "billing_city" => 'required',
            "billing_state" => 'required',
            "billing_address" => 'required',
            "billing_country" => 'required',
        ]);

        Auth::user()->update($request->all());

        Session::flash('success', 'Billing Details Update Successfully.');
        return back();
    }

    public function payments()
    {
        $data['payments'] = Payment::whereUserId(auth()->id())->latest()->get();
        return view('user.custom.my-payments', $data);
    }

    public function cpdhours()
    {
        // $data['events'] = EventDetail::with('event')->latest()->get();
        $currentYear = date('Y');
        $lastYear = $currentYear - 1;
        $beforeLastYear = $lastYear - 1;
        $data['years'] = [$currentYear, $lastYear, $beforeLastYear];


        $user_id = auth()->id();
        $required_cpds = CpdRequired::whereUserId($user_id)->orderBy('year', 'desc')->get()->reverse();


        $required_cpds_data = [];
        $required_cpds_data_years = [];
        foreach ($required_cpds as $required_cpd) {

            $internal_cpd = floatval(CpdTransaction::where(['user_id' => $user_id, 'trx_type' => '+'])->whereYear('created_at', $required_cpd->year)->sum('amount'))
                - floatval(CpdTransaction::where(['user_id' => $user_id, 'trx_type' => '-'])->whereYear('created_at', $required_cpd->year)->sum('amount'));

            $externel_cpd = floatval(CpdExternalPoint::whereYear('start_date', $required_cpd->year)->whereUserId($user_id)->whereNotIn('status', [0, 2])->sum('amount'));
            // + floatval(EventDetail::whereYear('created_at', $required_cpd->year)->whereUserId($user_id)->where(['attendance' => 'attend'])->sum('cpd_points'));

            $cpd_total = $internal_cpd + $externel_cpd;

            $required_cpds_data_years[] = $required_cpd->year;
            $required_cpds_data[] = [
                'required_cpds' => $required_cpd,
                'internal_cpd' => $internal_cpd,
                'external_cpd' => $externel_cpd,
                'cpd_total' => $cpd_total,
                'cpd_status' => ($cpd_total >= $required_cpd->required_points) ? 'Compiled' : 'Short'
            ];
        }

        $data['required_cpds_data'] = $required_cpds_data;
        $data['required_cpds_data_years'] = $required_cpds_data_years;

        // dd($required_cpds_data,$required_cpds_data_years);

        // starting
        // $reqCpdThisYear = CpdRequired::where('year', $currentYear)->where('user_id', $user_id)->first();
        // $reqCpdLastYear = CpdRequired::where('year', $lastYear)->where('user_id', $user_id)->first();
        // $reqCpdBeforeLastYear = CpdRequired::where('year', $beforeLastYear)->where('user_id', $user_id)->first();
        // $data['req_cpd_data'] = [$reqCpdThisYear ?? null, $reqCpdLastYear ?? null, $reqCpdBeforeLastYear ?? null];
        // $data['req_cpd'] = [$reqCpdThisYear->required_points ?? 0, $reqCpdLastYear->required_points ?? 0, $reqCpdBeforeLastYear->required_points ?? 0];
        //
        // $icpdThisYearAdd = floatval(CpdTransaction::where(DB::raw('YEAR(created_at)'), $currentYear)->where('trx_type', '+')->where('user_id', $user_id)->sum('amount'));
        // $icpdThisYearSub = floatval(CpdTransaction::where(DB::raw('YEAR(created_at)'), $currentYear)->where('trx_type', '-')->where('user_id', $user_id)->sum('amount'));
        // $icpdThisYear = $icpdThisYearAdd - $icpdThisYearSub;
        // $icpdLastYearAdd = floatval(CpdTransaction::where(DB::raw('YEAR(created_at)'), $lastYear)->where('trx_type', '+')->where('user_id', $user_id)->sum('amount'));
        // $icpdLastYearSub = floatval(CpdTransaction::where(DB::raw('YEAR(created_at)'), $lastYear)->where('trx_type', '-')->where('user_id', $user_id)->sum('amount'));
        // $icpdLastYear = $icpdLastYearAdd - $icpdLastYearSub;
        // $icpdBeforeLastYearAdd = floatval(CpdTransaction::where(DB::raw('YEAR(created_at)'), $beforeLastYear)->where('trx_type', '+')->where('user_id', $user_id)->sum('amount'));
        // $icpdBeforeLastYearSub = floatval(CpdTransaction::where(DB::raw('YEAR(created_at)'), $beforeLastYear)->where('trx_type', '-')->where('user_id', $user_id)->sum('amount'));
        // $icpdBeforeLastYear = $icpdBeforeLastYearAdd - $icpdBeforeLastYearSub;
        // $data['internal_cpd'] = [$icpdThisYear, $icpdLastYear, $icpdBeforeLastYear];
        //
        // $ecpdThisYear = floatval(CpdExternalPoint::where(DB::raw('YEAR(start_date)'), $currentYear)->where('user_id', $user_id)->whereNotIn('status', [0, 2])->sum('amount'));
        // $ecpdLastYear = floatval(CpdExternalPoint::where(DB::raw('YEAR(start_date)'), $lastYear)->where('user_id', $user_id)->whereNotIn('status', [0, 2])->sum('amount'));
        // $ecpdBeforeLastYear = floatval(CpdExternalPoint::where(DB::raw('YEAR(start_date)'), $beforeLastYear)->where('user_id', $user_id)->whereNotIn('status', [0, 2])->sum('amount'));
        // $data['external_cpd'] = [$ecpdThisYear, $ecpdLastYear, $ecpdBeforeLastYear];
        //
        // $data['cpd_total'] = [$icpdThisYear + $ecpdThisYear, $icpdLastYear + $ecpdLastYear, $icpdBeforeLastYear + $ecpdBeforeLastYear];
        //
        // $thisYearKey = array_search($currentYear, $data['years']);
        // $lastYearKey = array_search($lastYear, $data['years']);
        // $beforeLastYearKey = array_search($beforeLastYear, $data['years']);
        //
        // $cpdThisYearStatus = $data['cpd_total'][$thisYearKey] >= $data['req_cpd'][$thisYearKey] ? 'Compiled' : 'Short';
        // $cpdLastYearStatus = $data['cpd_total'][$lastYearKey] >= $data['req_cpd'][$lastYearKey] ? 'Compiled' : 'Short';
        // $cpdBeforeLastYearStatus = $data['cpd_total'][$beforeLastYearKey] >= $data['req_cpd'][$beforeLastYearKey] ? 'Compiled' : 'Short';
        //
        // $data['cpd_status'] = [$cpdThisYearStatus, $cpdLastYearStatus, $cpdBeforeLastYearStatus];
        // ending

        $data['externalCpdPoints'] = CpdExternalPoint::whereUserId(auth()->id())->whereNotIn('status', [2])->latest()->get();

        return view('user.custom.my-cpd-hours', $data);
    }

    public function extCertDown()
    {
        $cpd = CpdExternalPoint::findOrFail(request('cert'));
        $filePath = storage_path('app/member_external_cert/' . $cpd->certificate);
        return response()->download($filePath);
    }

    public function reqExtCPD(Request $request)
    {
        $this->validate($request, [
            'amount' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'training_title' => 'required',
            'organized_by' => 'required',
            'certificate' => 'required',
        ]);

        if ($request->hasFile('certificate')) {
            $file = $request->file('certificate');
            $filename = uniqid(auth()->id() . '_' . time() . '-') . '.' . $file->getClientOriginalExtension();
            $file->move(storage_path('app/member_external_cert/'), $filename);
            $cert = $filename;
        }
        CpdExternalPoint::create([
            'user_id' => auth()->id(),
            'amount' => $request->amount,
            'trx_type' => '+',
            'start_date' => date('Y-m-d', strtotime($request->start_date)),
            'end_date' =>   date('Y-m-d', strtotime($request->end_date)),
            'training_title' => $request->training_title,
            'organized_by' => $request->organized_by,
            'certificate' => $cert ?? null,
            'details' => $request->details,
            'remarks' => "External CPD Point by Admin",
            'status' => 0
        ]);
        return back()->with('success', 'External CPD Point added request submited');
    }

    public function payinvoice(Request $request)
    {
        ini_set("memory_limit", "999M");
        ini_set("max_execution_time", "999");
        $this->validate($request, ['model' => 'required']);
        if ($request->model == 'payments') {
            if (!$request->has('paymentid')) {
                return back()->with('error', 'Invalid request');
            }
            $paymentId = decrypt($request->get('paymentid'));
            $payment = Payment::with('user')->find($paymentId);
            $data['payment'] = $payment->append('item', 'order')->makeHidden('item_info', 'order_info', 'payment_details');
            // return $data;
            // return view('pdf.custom.member', $data);
            $invoice = $payment->invoice ?? Str::random(10) . ".pdf";
            $payment->invoice = $invoice;
            $payment->save();
            return Pdf::loadView('pdf.custom.member', $data)
                ->save('assets/front/invoices/' . $invoice)
                ->stream('invoice_' . $invoice);
        }
        if ($request->model == 'event') {
            if (!$request->has('ticketid')) {
                return back()->with('error', 'Invalid request');
            }
            $ticketId = decrypt($request->get('ticketid'));
            $ticket = EventDetail::with(['user', 'event'])->find($ticketId);
            $ticket->gateway = $ticket->paymentGateway;
            $ticket->transaction = json_decode($ticket->transaction_details);
            $data['ticket'] = $ticket->makeHidden(['transaction_details', 'bex_details']);
            // $data['qr_code_image'] = 'data:image/png;base64,' . base64_encode(QrCode::format('png')->size(80)->generate(strtoupper($ticket->transaction_id)));
            // return $data;
            // return view('pdf.custom.training', $data);
            $fileName = 'invoice_' . $ticket->transaction_id . '.pdf';
            $invoicePath = 'assets/front/invoices/' . $fileName;
            if (file_exists($invoicePath)) {
                return Response::download($invoicePath);
            }
            return Pdf::loadView('pdf.custom.training', $data)
                ->save($invoicePath)
                ->stream($fileName);
        }
    }

    public function upcomingEvents()
    {
        $data['events'] = Event::with(['eventTicket'])
            ->active()
            ->where('date', '>', today())
            ->latest()->get();
        return view('user.custom.upcmming-training', $data);
    }

    public function updateRequiredCpdhours(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'required_points' => 'required',
        ]);
        CpdRequired::where(['user_id' => auth()->id(), 'id' => $request->id])
            ->update(['required_points' => $request->required_points]);
        return back()->with('success', "Required CPD Point updated successfully");
    }

    public function saveRequiredCpdhours(Request $request)
    {
        $this->validate($request, [
            'required_points' => 'required',
        ]);
        CpdRequired::create(['user_id' => auth()->id(), 'year' => date('Y'), 'required_points' => $request->required_points]);
        return back()->with('success', "Required CPD Point added successfully");
    }

    public function cancelMembership()
    {
        Subscription::where('id', request('subscription_id'))->update(['status' => 3, 'current_package_id' => null]);
        return redirect()->route('user-dashboard')->with('success', "Subscription cancel successfully");
    }
}
