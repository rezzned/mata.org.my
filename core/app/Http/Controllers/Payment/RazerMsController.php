<?php

namespace App\Http\Controllers\Payment;

use App\BasicExtra;
use App\Http\Controllers\Payment\PaymentController;
use App\Http\Helpers\PackageHelper;
use App\Language;
use App\Notifications\SubscriptionNotify;
use App\OrderPayment;
use App\Package;
use App\PackageOrder;
use App\PaymentGateway;
use App\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class RazerMsController extends PaymentController
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


    public function store(Request $request)
    {
        // Validation Starts
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }
        $bex = $currentLang->basic_extra;
        $package_inputs = $currentLang->package_inputs;


        $validation = $this->orderValidation($request, $package_inputs);
        if ($validation) {
            return $validation;
        }

        // // save order
        // $po = $this->saveOrder($request, $package_inputs, 0);
        $po = Subscription::find(request('subscription_id'));

        $package = Package::find($request->package_id);
        $packageid = $package->id;

        $user = auth()->user();
        $price = packageTotalPrice($package); // / $bex->base_currency_rate;
        $price = round($price, 2);
        $cancel_url = route('front.payment.cancle', $packageid);
        $notify_url = route('front.razerms.notify', ['package_id' => $packageid]);

        try {
            if ($request->has("payment_options") && $request->payment_options != "") {
                $merchantid = $this->merchantId;    // Change to your merchant ID
                $vkey = $this->verifyKey;    // Change to your verify key

                // Put your own code/process HERE. (Eg: Insert data to DB)
                $process_status = true;
                $order = OrderPayment::create([
                    'model'          => Package::class,
                    'model_id'       => $package->id,
                    'order_id'       => 'M#' . rand(100, 999) . time(),
                    'order_data'     => null,
                    'amount'         => $price,
                    'payment_method' => 'RazerMS',
                    'payment_data'   => null,
                ]);

                $orderid = $po->id;
                $vcodeHash = md5($price . $merchantid . $orderid . $vkey);
                $title = "Order Membership Package " . $package->title . PHP_EOL . "Order ID: " . $orderid;
                $query = [
                    "channel" => $request->payment_options,
                    "amount" => $price,
                    "cur" => "MYR",
                    "orderid" => $orderid,
                    "bill_name" => $request->post('name'),
                    "bill_email" => $request->post('email'),
                    "bill_mobile" => $user->personal_phone,
                    "bill_desc" => $title,
                    "b_addr1" => $user->billing_address ?? $user->address,
                    "country" => "MY",
                    "vcode" => $vcodeHash,
                    "returnurl" => $notify_url
                ];

                Session::put('order_id', $po->id);
                Session::put('package_id', $packageid);

                $requestUri = join([$this->endpoint, "?", http_build_query($query)]);

                return redirect($requestUri);
            }
            // Session::put('paypal_payment_id', $payment->getId());
            return;
        } catch (\Exception $e) {
        }

        return redirect()->back()->with('error', 'Unknown error occurred');
    }


    public function notify(Request $request)
    {
        $order_id = Session::get('order_id');
        $package_id = Session::get('package_id', $request->get('package_id'));

        $cancel_url = route('front.payment.cancle', $package_id);

        $sec_key = $this->secretKey;

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
            PackageHelper::updatePayment($package_id, $order_id, [
                'trnx_id' => $tranID,
                'status' => 2
            ]);
            return redirect($cancel_url);
        }
        // Merchant might issue a re-query to PG to double-check payment status
        if ($status == "00") {
            if (session()->has('lang')) {
                $currentLang = Language::where('code', session()->get('lang'))->first();
            } else {
                $currentLang = Language::where('is_default', 1)->first();
            }
            $bex = $currentLang->basic_extra;
            $package = Package::find($package_id);
            $price = packageTotalPrice($package); // / $bex->base_currency_rate;
            $price = round($price, 2);

            if ($price == $amount) {
                $be = $currentLang->basic_extended;
                $bex = BasicExtra::first();

                $paymentDetails = [$request->all()];
                $po = Subscription::find($order_id);
                if ($bex->recurring_billing == 1) {
                    $po->payment_status = 1;
                    $po->save();
                    $po = $this->subFinalize($po, $package);
                    $itemInfo['subscription'] = $po->toArray();
                } else {
                    $po = PackageOrder::findOrFail($order_id);
                    $po->payment_status = 1;
                    $po->save();
                    $itemInfo['pacakge_order'] = $po->toArray();
                }

                // generate invoice & send mail
                $invoice = $this->sendMails($po, $be, $bex);

                // save payment update
                PackageHelper::updatePayment($package_id, $order_id, [
                    'payment_details' => $paymentDetails,
                    'trnx_id' => $tranID,
                    'invoice' => $invoice,
                    'status' => 1
                ]);

                Session::forget('order_id');
                Session::forget('package_id');

                $subject = "Subscription Request Accepted";
                $body = "Hello <strong>$po->name</strong>,<br>Your subscription payment complete <strong>";
                $body .=  "<br>Thank you.";

                $po->user->notify(new SubscriptionNotify($request->status, $po));

                // unlink previous receipt image
                @unlink('assets/front/receipt/' . $po->receipt);

                // Send Mail to Buyer
                $this->sendEmail($be, $po->email, $po->name, $subject, $body);

                return redirect()->route('front.packageorder.confirmation', [$package_id, $po->id]);
            }
        }
        return redirect($cancel_url);
    }
}
