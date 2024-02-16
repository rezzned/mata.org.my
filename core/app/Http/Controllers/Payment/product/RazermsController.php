<?php

namespace App\Http\Controllers\Payment\product;

use App\Language;
use App\OrderPayment;
use App\ProductOrder;
use Razorpay\Api\Api;
use App\PaymentGateway;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Payment\product\PaymentController;
use App\Product;
use Illuminate\Support\Facades\Config;

class RazermsController extends PaymentController
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
        if (!Session::has('cart')) {
            return view('errors.404');
        }

        $cart = Session::get('cart');

        $total = $this->orderTotal($request->shipping_charge);

        // Validation Starts
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $bex = $currentLang->basic_extra;
        $be = $currentLang->basic_extended;
        $bs = $currentLang->basic_setting;


        if ($validated = $this->orderValidation($request)) {
            return $validated;
        }
        // Validation Ends


        $txnId = 'txn_' . Str::random(8) . time();
        $chargeId = 'ch_' . Str::random(9) . time();

        $order = $this->saveOrder($request, $txnId, $chargeId);

        $order_id = $order->id;

        $this->saveOrderedItems($order_id);


        $orderInfo['title'] = $bs->website_title . " Order";
        $orderInfo['item_number'] = Str::random(4) . time();
        $orderInfo['item_amount'] = $total;
        $orderInfo['order_id'] = $order_id;

        $order = OrderPayment::create([
            'model' => ProductOrder::class,
            'model_id' => $order_id,
            'order_id' => 'P#' . rand(100, 999) . time(),
            'order_data' => $orderInfo,
            'item_model' => Product::class,
            'amount' => $total,
            'payment_method' => 'RazerMS',
        ]);

        $order_id = $order->order_id;

        $cancel_url = route('product.payment.cancel', ['order_id' => $order_id]);
        $notify_url = route('product.razerms.notify', ['order_id' => $order_id]);

        $merchantid = $this->merchantId;    // Change to your merchant ID
        $vkey = $this->verifyKey;    // Change to your verify key
        $price = $total;

        $user = auth()->user();

        $orderid = $order_id;
        $vcodeHash = md5($price . $merchantid . $orderid . $vkey);
        $query = [
            "channel" => $request->payment_options,
            "amount" => $price,
            "cur" => "MYR",
            "orderid" => $orderid,
            "bill_name" => $user->full_name ?? ($request->shpping_fname . " " . $request->shpping_lname),
            "bill_email" => $user->email ?? $request->shpping_email,
            "bill_mobile" => $user->personal_phone ?? $request->shpping_number,
            "bill_desc" => $orderInfo['title'],
            "b_addr1" => $user->billing_address ?? $user->address ?? ($request->shpping_address . ", " . $request->shpping_city),
            "country" => "MY",
            "vcode" => $vcodeHash,
            "returnurl" => $notify_url
        ];

        Session::put('order_data', $orderInfo);
        Session::put('order_id', $orderid);

        $requestUri = join([$this->endpoint, "?", http_build_query($query)]);

        return redirect($requestUri);
    }

    public function notify(Request $request)
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

        $success_url = route('product.payment.return', ['order_id' => $orderid]);
        $cancel_url = route('product.razerms.cancel', ['order_id' => $orderid]);

        $key0 = md5($tranID . $orderid . $status . $domain . $amount . $currency);
        $key1 = md5($paydate . $domain . $key0 . $appcode . $sec_key);

        // Invalid transaction.
        if ($skey != $key1) {
            return redirect()->route('product.razerms.cancel', ['order_id' => $orderid]);
        }
        // Merchant might issue a re-query to PG to double-check payment status
        if ($status == "00") {
            if (session()->has('lang')) {
                $currentLang = Language::where('code', session()->get('lang'))->first();
            } else {
                $currentLang = Language::where('is_default', 1)->first();
            }

            $be = $currentLang->basic_extended;

            $order = OrderPayment::whereItemModel(Product::class)->whereOrderId($orderid)->firstOrFail();
            $orderid = $order->model_id;
            /** Get the payment ID before session clear **/

            $po = ProductOrder::findOrFail($orderid);
            $po->payment_status = "Completed";
            $po->save();

            $order->payment_data = $request->all();
            $order->trx_id = $tranID;
            $order->status = 'complete';
            $order->save();

            // Send Mail to Buyer
            $this->sendMails($po);

            Session::forget('order_data');

            return redirect($success_url);
        }

        return redirect($cancel_url);
    }

    public function cancel()
    {
        $order_id = Session::get('order_id') ?? request('order_id');
        ProductOrder::where('id', $order_id)->update([
            'payment_status' => 'cancelled',
            'order_status' => 'Cancelled'
        ]);

        return redirect()->route('front.product')->with('unsuccess', 'Payment Unsuccess');
    }
}
