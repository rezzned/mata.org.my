<?php

namespace App\Http\Controllers\Payment\causes;

use App\Event;
use App\EventDetail;
use App\Http\Controllers\Front\EventController;
use App\OrderPayment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Front\CausesController;
use App\Language;
use App\PaymentGateway;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Illuminate\Support\Facades\Session;

class RazerMsController extends Controller
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

    public function paymentProcess(Request $request, $_amount, $_actual_amount, $_title, $_success_url, $_cancel_url)
    {
        $currentLang = session()->has('lang') ?
            (Language::where('code', session()->get('lang'))->first())
            : (Language::where('is_default', 1)->first());
        $be = $currentLang->basic_extended;
        $bex = $currentLang->basic_extra;

        $title = $_title;
        $price = $_amount;
        $price = round($price, 2);
        $cancel_url = $_cancel_url;
        $success_url = $_success_url;

        $merchantid = $this->merchantId;    // Change to your merchant ID
        $vkey = $this->verifyKey;    // Change to your verify key

        $user = auth()->user();

        $event = new EventController;
        $event_details = $event->store($request->all(), null, null, $_actual_amount, $bex);
        $event_details->status = "Pending";
        $event_details->save();

        //        $orderid = "EVENT_#" . $event_details->id;

        $order = OrderPayment::create([
            'model' => EventDetail::class,
            'model_id' => $event_details->id,
            'order_id' => 'E#' . rand(100, 999) . time(),
            'item_model' => Event::class,
            'amount' => $price,
            'payment_method' => 'RazerMS',
        ]);

        $orderid = $order->order_id;

        $vcodeHash = md5($price . $merchantid . $orderid . $vkey);
        $query = [
            "channel" => $request->payment_options,
            "amount" => $price,
            "cur" => "MYR",
            "orderid" => $orderid,
            "bill_name" => $request->post('name'),
            "bill_email" => $request->post('email'),
            "bill_mobile" => $request->phone ?? $user->personal_phone,
            "bill_desc" => $title,
            "b_addr1" => $user->billing_address ?? $user->address ?? '',
            "country" => "MY",
            "vcode" => $vcodeHash,
            "returnurl" => $success_url
        ];

        Session::put('order_id', $orderid);
        Session::put('event_id', $event_details->event_id);
        Session::put('request', $request->all());
        Session::put('actual_amount', $_actual_amount);

        $requestUri = join([$this->endpoint, "?", http_build_query($query)]);

        return redirect($requestUri);
        // return redirect()->back()->with('error', 'Unknown error occurred');
    }

    public function successPayment(Request $request)
    {
        $requestData = Session::get('request');
        $currentLang = session()->has('lang') ?
            (Language::where('code', session()->get('lang'))->first())
            : (Language::where('is_default', 1)->first());
        $be = $currentLang->basic_extended;
        $bex = $currentLang->basic_extra;


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

        $cancel_url = route('donation.razerms.cancel', [
            'eid' => $requestData['event_id'] ?? $request->get('eid'),
            'oid' => str_replace("EVENT_#", '', $orderid)
        ]);

        $key0 = md5($tranID . $orderid . $status . $domain . $amount . $currency);
        $key1 = md5($paydate . $domain . $key0 . $appcode . $sec_key);

        // Invalid transaction.
        if ($skey != $key1 || $status != "00") {
            return redirect($cancel_url);
        }
        // Merchant might issue a requery to PG to double-check payment status
        if ($status == "00") {
            $paymentFor = Session::get('paymentFor');
            $response = $request->all();
            $actualAmount = Session::get('actual_amount');
            if ($paymentFor == "Cause") {
                $cause = new CausesController;
                $donation = $cause->store($requestData, $tranID, $response, $actualAmount, $bex);
                if (!is_null($requestData["email"])) {
                    $file_name = $cause->makeInvoice($donation);
                    $cause->sendMailPHPMailer($requestData, $file_name, $be);
                }
                session()->flash('success', __('Payment completed!'));
                Session::forget('request');
                Session::forget('actual_amount');
                Session::forget('paymentFor');
                return redirect()->route('front.cause_details', [$requestData["donation_slug"]]);
            } elseif ($paymentFor == "Event") {
                $event = new EventController;

                $order = OrderPayment::whereItemModel(Event::class)->whereOrderId($orderid)->firstOrFail();
                //$orderid = str_replace("EVENT_#", '', $orderid);
                $orderId = $order->model_id;

                $event_details = EventDetail::findOrFail($orderId);
                $event_details->trx_id = $tranID;
                $event_details->transaction_details = json_encode($response);
                $event_details->status = "Success";
                $event_details->save();
                $file_name = $event->makeInvoice($event_details);
                $event->sendMailPHPMailer($requestData, $file_name, $be);
                session()->flash('success', __('Payment completed! We sent you an email'));
                Session::forget('request');
                Session::forget('paymentFor');
                Session::forget('order_id');
                Session::forget('event_id');
                return redirect()->route('front.event_details', [$requestData["event_slug"]]);
            }
        }
        return redirect($cancel_url);
    }

    public function cancelPayment()
    {
        $msg = __('Something went wrong. Payment was failed or canceled');
        try {
            $event_id = Session::get('event_id') ?? request('eid');

            $event = Event::find($event_id);

            $orderId = Session::get('order_id', request('oid'));
            $order = OrderPayment::whereItemModel(Event::class)->whereOrderId($orderId)->firstOrFail();

            $booking = EventDetail::find($order->model_id);

            $booking->status = "Canceled";
            $booking->save();
            if (!$event) {
                return redirect()->route('front.events')->with('error', $msg);
            }
            return redirect()->route('front.event_details', $event->slug)
                ->with('error', $msg)->withInput();
        } catch (\Throwable $th) {
            return redirect()->route('front.events')->with('error', $msg);
        }
    }
}
