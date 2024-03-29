<?php

namespace App\Http\Controllers\Payment\product;

use App\BasicExtended;
use App\BasicExtra;
use App\BasicSetting;
use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use App\Http\Controllers\Controller;
use App\Http\Helpers\KreativMailer;
use App\Jobs\PublicationOrderEmail;
use App\Jobs\PublicationOrderPDF;
use App\Language;
use App\OfflineGateway;
use App\OrderItem;
use App\Product;
use App\ProductOrder;
use App\ShippingCharge;
use Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function paycancle()
    {
        return redirect()->back()->with('unsuccess', 'Payment Cancelled.');
    }

    public function payreturn()
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

        return view('front.product.success', $data);
    }

    public function orderValidation($request, $gtype = 'online') {
        $rules = [
            // 'billing_fname' => 'required',
            // 'billing_lname' => 'required',
            // 'billing_address' => 'required',
            // 'billing_city' => 'required',
            // 'billing_country' => 'required',
            // 'billing_number' => 'required',
            // 'billing_email' => 'required',
            'shpping_fname' => 'required',
            'shpping_lname' => 'required',
            'shpping_address' => 'required',
            'shpping_city' => 'required',
            'shpping_country' => 'required',
            'shpping_number' => 'required',
            'shpping_email' => 'required',
        ];

        if ($gtype == 'offline') {
            $gateway = OfflineGateway::find($request->method);
            if ($gateway->is_receipt == 1) {
                $rules['receipt'] = [
                    'required',
                    function ($attribute, $value, $fail) use ($request) {
                        $ext = $request->file('receipt')->getClientOriginalExtension();
                        if (!in_array($ext, array('jpg', 'png', 'jpeg'))) {
                            return $fail("Only png, jpg, jpeg image is allowed");
                        }
                    },
                ];
            }
        }

        $request->validate($rules);
    }

    public function orderTotal($shippig_charge) {
        // if ($shipping != 0) {
        //     $shipping = ShippingCharge::findOrFail($shipping);
        //     $shippig_charge = $shipping->charge;
        // } else {
        //     $shippig_charge = 0;
        // }

        $total = round((cartTotal() - coupon()) + $shippig_charge + tax(), 2);
        return round($total, 2);
    }


    public function saveOrder($request, $txnId, $chargeId, $paymentStatus = 'Pending', $gtype = 'online') {
        $bex = BasicExtra::first();
        $shippig_charge = $request["shipping_charge"];
        $total = $this->orderTotal($shippig_charge);
        // if ($request['shipping_charge'] != 0) {
        //     $shipping = ShippingCharge::findOrFail($request['shipping_charge']);
        //     $shippig_charge = $shipping->charge;
        //     $shipping_method = $shipping->title;
        // } else {
        //     $shippig_charge = 0;
        //     $shipping_method = NULL;
        // }

        $order = new ProductOrder;
        // $order->billing_fname = $request['billing_fname'];
        // $order->billing_lname = $request['billing_lname'];
        // $order->billing_email = $request['billing_email'];
        // $order->billing_address = $request['billing_address'];
        // $order->billing_city = $request['billing_city'];
        // $order->billing_country = $request['billing_country'];
        // $order->billing_number = $request['billing_number'];
        $order->shpping_fname = $request['shpping_fname'];
        $order->shpping_lname = $request['shpping_lname'];
        $order->shpping_email = $request['shpping_email'];
        $order->shpping_address = $request['shpping_address'];
        $order->shpping_city = $request['shpping_city'];
        $order->shpping_country = $request['shpping_country'];
        $order->shpping_number = $request['shpping_number'];
        $order->postal_fee = $request['postal_fee'];
        $order->gateway_type = $gtype;


        $order->cart_total = cartTotal();
        $order->tax = tax();
        $order->discount = coupon();
        $order->total = $total;
        // $order->shipping_method = $shipping_method;
        $order->shipping_charge = round($shippig_charge, 2);
        if ($gtype == 'online') {
            $order->method = $request['method'];
        } elseif ($gtype == 'offline') {
            $gateway = OfflineGateway::find($request['method']);
            $order->method = $gateway->name;

            if ($request->hasFile('receipt')) {
                // store the receipt in folder & database
                $receipt = uniqid() . '.' . $request->file('receipt')->getClientOriginalExtension();
                $request->file('receipt')->move('assets/front/receipt/', $receipt);
                $order->receipt = $receipt;
            } else {
                $order->receipt = NULL;
            }
        }
        $order->currency_code = $bex->base_currency_text;
        $order['order_number'] = \Str::random(4) . time();
        $order['payment_status'] = $paymentStatus;
        $order['txnid'] = $txnId;
        $order['charge_id'] = $chargeId;
        $order['user_id'] = Auth::check() ? Auth::user()->id : NULL;
        $order->save();

        return $order;
    }


    public function saveOrderedItems($orderId) {
        $cart = Session::get('cart');
        $product_ids = [];
        $qty = [];
        foreach ($cart as $id => $item) {
            $qty[] = $item['qty'];
            $product_ids[] = $id;
        }

        $products = Product::whereIn('id',$product_ids)->get();

        foreach ($products as $key => $product) {
            if (!empty($product->category)) {
                $category = $product->category->name;
            } else {
                $category = '';
            }
            OrderItem::insert([
                'product_order_id' => $orderId,
                'product_id' => $product->id,
                'user_id' => auth()->check() ? auth()->user()->id : NULL,
                'title' => $product->title,
                'sku' => $product->sku,
                'qty' => $qty[$key],
                'category' => $category,
                'price' => $product->current_price,
                'previous_price' => $product->previous_price,
                'image' => $product->feature_image,
                'summary' => $product->summary,
                'description' => $product->description,
                'created_at' => Carbon::now(),
                'shipping_price' => $product->shipping_price,
                'postal_fee' => $product->postal_fee
            ]);
        }

        foreach ($cart as $id => $item) {
            $product = Product::findOrFail($id);
            $stock = $product->stock - $item['qty'];
            Product::where('id', $id)->update([
                'stock' => $stock
            ]);
        }
    }

    public function sendMails($order) {
        ini_set("memory_limit", "999M");
        ini_set("max_execution_time", "999");
        $bs = BasicSetting::first();
        $data['order']  = $order;

        $fileName = Str::random(4) . time() . '.pdf';

        ProductOrder::where('id', $order->id)->update([
            'invoice_number' => $fileName
        ]);

        $path = 'assets/front/invoices/product/' . $fileName;
        Pdf::loadView('pdf.product', $data)->save($path);
        // PublicationOrderPDF::dispatch($data, $fileName);
        PublicationOrderEmail::dispatch($bs, $order, $fileName)->delay(now()->addMinutes(5));

        Session::forget('cart');
        Session::forget('coupon');
    }
}
