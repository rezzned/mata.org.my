<?php

namespace App\Http\Controllers\Payment\Course;

use App\Course;
use App\CoursePurchase;
use App\Http\Controllers\Controller;
use App\Language;
use App\PaymentGateway;
use Barryvdh\DomPDF\Facade\Pdf;
use Cartalyst\Stripe\Laravel\Facades\Stripe;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class RazerMsGatewayController extends Controller
{
    private $merchantId;
    private $verifyKey;
    private $secretKey;
    private $endpoint;
    public function __construct()
    {
        $data = PaymentGateway::whereKeyword('razerms')->first();
        $paydata = $data->convertAutoData();
        $rms = Config::get('razerms');
        $rms['vkey'] = $paydata['key'];
        $rms['skey'] = $paydata['secret'];
        $rms['merchantid'] = $paydata['merchantid'];
        $rms['sandbox'] = $paydata['sandbox'];
        $this->merchantId = $rms['merchantid'];
        $this->verifyKey = $rms['vkey'];
        $this->secretKey = $rms['skey'];
        $sandbox_uri = "https://sandbox.merchant.razer.com/RMS/pay/" . $this->merchantId;
        $live_uri = "https://pay.merchant.razer.com/RMS/pay/" . $this->merchantId;
        $this->endpoint = $rms['sandbox'] == 1 ? $sandbox_uri : $live_uri;
    }

    public function redirectToRazerMs(Request $request)
    {
        ini_set("memory_limit", "999M");
        ini_set("max_execution_time", "999");
        $course = Course::findOrFail($request->course_id);
        if (!Auth::user()) {
            Session::put('link', route('course_details', ['slug' => $course->slug]));
            return redirect()->route('user.login');
        }

        $rules = ['payment_options' => 'required'];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }

        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $bs = $currentLang->basic_setting;
        $logo = $bs->logo;
        $bse = $currentLang->basic_extra;

        $price = $course->current_price;
        if (Auth::check()) {
            if (Auth::user()->is_member('associate_member')) {
                $price = $course->assoc_price;
            }
            if (Auth::user()->is_member('standard_member')) {
                $price = $course->stand_price;
            }
        }

        // $total = $price;
        $title = 'Purchase Course';

        // changing the currency before sending the total price to Stripe
        // if ($bse->base_currency_text !== 'USD') {
        //     $base_rate = intval($bse->base_currency_rate);
        //     $total = $total / $base_rate;
        // }

        $course_purchase = new CoursePurchase;
        $course_purchase->user_id = Auth::user()->id;
        $course_purchase->order_number = rand(100, 500) . time();
        $course_purchase->first_name = Auth::user()->fname;
        $course_purchase->last_name = Auth::user()->lname;
        $course_purchase->email = Auth::user()->email;
        $course_purchase->course_id = $course->id;
        $course_purchase->currency_code = "MYR";
        $course_purchase->current_price = $price;
        $course_purchase->previous_price = $course->previous_price;
        $course_purchase->payment_method = 'RazerMS';
        $course_purchase->payment_status = 'Pending';
        $course_purchase->save();

        $success_url = route('course.razerms.return');

        $merchantid = $this->merchantId;    // Change to your merchant ID
        $vkey = $this->verifyKey;    // Change to your verify key
        // $price = $total;

        $user = auth()->user();

        $orderid = $course_purchase->order_number;
        $vcodeHash = md5($price . $merchantid . $orderid . $vkey);
        $query = [
            "channel" => $request->payment_options,
            "amount" => $price,
            "cur" => "MYR",
            "orderid" => $orderid,
            "bill_name" => $user->full_name,
            "bill_email" => $user->email,
            "bill_mobile" => $user->personal_phone,
            "bill_desc" => $title,
            "b_addr1" => $user->billing_address ?? $user->address ?? '',
            "country" => "MY",
            "vcode" => $vcodeHash,
            "returnurl" => $success_url
        ];

        Session::put('course_id', $course->id);
        Session::put('course_order_id', $orderid);
        Session::put('course_purchase_id', $course_purchase->id);

        $requestUri = join([$this->endpoint, "?", http_build_query($query)]);

        return redirect($requestUri);
    }

    public function razermsReturn(Request $request)
    {
        $sec_key = $this->secretKey; //Replace xxxxxxxxxxxx with Secret_Key

        $tranID = $request->post('tranID');
        $orderid = $request->post('orderid');
        $status = $request->post('status');
        $domain = $request->post('domain');
        $amount = $request->post('amount');
        $currency = $request->post('currency');
        $appcode = $request->post('appcode');
        $paydate = $request->post('paydate');
        $skey = $request->post('skey');

        $key0 = md5($tranID . $orderid . $status . $domain . $amount . $currency);
        $key1 = md5($paydate . $domain . $key0 . $appcode . $sec_key);

        // Invalid transaction.
        if ($skey != $key1 || $status != "00") {
            return redirect()->route('course.razerms.cancel');
        }
        // Merchant might issue a requery to PG to double check payment status
        if ($status == "00") {
            if (session()->has('lang')) {
                $currentLang = Language::where('code', session()->get('lang'))->first();
            } else {
                $currentLang = Language::where('is_default', 1)->first();
            }

            $bs = $currentLang->basic_setting;
            $logo = $bs->logo;
            $bse = $currentLang->basic_extra;

            $purchaseId = Session::get('course_purchase_id');
            // generate an invoice in pdf format
            $course_purchase = CoursePurchase::find($purchaseId);
            $course_purchase->update([
                'payment_status' => 'Completed'
            ]);
            $fileName = $course_purchase->order_number . '.pdf';
            $directory = 'assets/front/invoices/course/';
            @mkdir($directory, 0775, true);
            $fileLocated = $directory . $fileName;
            $order_info = $course_purchase;
            Pdf::loadView('pdf.course', compact('order_info', 'logo', 'bse'))
                ->setPaper('a4', 'landscape')->save($fileLocated);

            // store invoice in database
            $course_purchase->update([
                'invoice' => $fileName
            ]);

            // send a mail to the buyer
            MailController::sendMail($course_purchase);

            Session::forget('course_id');
            Session::forget('course_order_id');
            Session::forget('course_purchase_id');

            return redirect()->route('course.razerms.complete');
        }
        return redirect()->route('course.razerms.cancel');
    }

    public function complete()
    {
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

        return view('front.course.success', $data);
    }
    public function cancel()
    {
        $courseId = Session::get('course_id');
        $course = Course::find($courseId);
        $course_order_id = Session::get('course_purchase_id');
        CoursePurchase::where('id', $course_order_id)->update(['payment_status' => 'Cancelled']);

        return redirect()->route('course_details', $course->slug)->with('unsuccess', 'Payment Unsuccess');
    }
}
