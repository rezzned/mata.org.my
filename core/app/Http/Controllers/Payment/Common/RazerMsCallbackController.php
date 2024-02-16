<?php

namespace App\Http\Controllers\Payment\Common;

use App\EventDetail;
use App\Http\Controllers\Controller;
use App\OrderPayment;
use App\PaymentGateway;
use App\ProductOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RazerMsCallbackController extends Controller
{
    private $merchantId;
    private $verifyKey;
    private $secretKey;
    private $endpoint;

    public function __construct()
    {
        $data = PaymentGateway::whereKeyword('razerms')->first();
        $paydata = $data->convertAutoData();
        $rms['sandbox'] = $paydata['sandbox'];
        $this->merchantId = $paydata['merchantid'];
        $this->verifyKey = $paydata['key'];
        $this->secretKey = $paydata['secret'];
        $sandbox_uri = "https://sandbox.merchant.razer.com/RMS/pay/" . $this->merchantId;
        $live_uri = "https://pay.merchant.razer.com/RMS/pay/" . $this->merchantId;
        $this->endpoint = $paydata['sandbox'] == 1 ? $sandbox_uri : $live_uri;
    }

    public function returnCallback(Request $request)
    {
        $sec_key = $this->secretKey; //Secret Key

        $nbcb     = $request->post('nbcb');
        $tranID   = $request->post('tranID');
        $orderId  = $request->post('orderid');
        $status   = $request->post('status');
        $domain   = $request->post('domain');
        $amount   = $request->post('amount');
        $currency = $request->post('currency');
        $appcode  = $request->post('appcode');
        $payDate  = $request->post('paydate');
        $skey     = $request->post('skey');

        try {
            /***********************************************************
             * To verify the data integrity sending by PG
             ************************************************************/
            $key0 = md5( $tranID.$orderId.$status.$domain.$amount.$currency );
            $key1 = md5( $payDate.$domain.$key0.$appcode.$sec_key );

            if( $skey != $key1 ) $status= -1; // Invalid transaction

            if ( $status == "00" ) {

                $order = OrderPayment::whereOrderId($orderId)->firstOrFail();

                $order->payment_data = $request->all();
                $order->trx_id = $tranID;
                $order->status = 'complete';

                /** Get the payment ID before session clear **/
                if ($order->model == ProductOrder::class){
                    $po = ProductOrder::findOrFail($order->model_id);
                    $po->payment_status = "Completed";
                    $po->save();

                    $order->save();
                }

                if ($order->model == EventDetail::class) {
                    $po = EventDetail::findOrFail($order->model_id);
                    $po->trx_id = $tranID;
                    $po->status = "Success";
                    $po->save();

                    $order->save();
                }


            } else {
                // failure action

                Log::build(['driver' => 'single', 'path' => root_path('callback.log')]);
                Log::info("Log: Failed");
                echo 'fail';
                // write your script here .....
            }

            if ( $nbcb==1 ) {
                //callback IPN feedback to notified PG
                echo "CBTOKEN:MPSTATOK"; exit;
            }else{
                //normal IPN and redirection
            }
        } catch (\Exception $exception) {
            Log::build(['driver' => 'single', 'path' => root_path('callback.log')]);
            Log::info("Log: " . $exception->getMessage() . " File: " . $exception->getFile() . " Line: " . $exception->getLine());
        }
    }
}
