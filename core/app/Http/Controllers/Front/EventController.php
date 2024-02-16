<?php


namespace App\Http\Controllers\Front;

use App\BasicExtra;
use App\BasicSetting;
use App\Coupon;
use App\Event;
use App\EventDetail;
use App\EventTicket;
use App\Language;
use App\OfflineGateway;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Payment\causes\PaypalController;
use App\Http\Controllers\Payment\causes\RazerMsController;
use App\Http\Controllers\Payment\causes\StripeController;
use App\Http\Helpers\KreativMailer;
use App\Jobs\EventStatusEmailJob;
use App\Rules\LimitEventBooking;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

class EventController extends Controller
{
    public function makePayment(Request $request)
    {
        // dd($request->all());
        // $rules['ic_number'] = ['required', new LimitEventBooking($request->event_id)];
        try {
            $gateway = OfflineGateway::find($request->payment_method);
            if (isset($gateway) && $gateway->is_receipt == 1) {
                $rules['receipt'] = [
                    'required',
                    function ($attribute, $value, $fail) use ($request) {
                        $ext = $request->file('receipt')->getClientOriginalExtension();
                        if (!in_array($ext, array('jpg', 'png', 'jpeg'))) {
                            return $fail("Only png, jpg, jpeg image is allowed");
                        }
                    },
                ];

                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }
            }

            $event = Event::findOrFail($request->event_id);
            $event_ticket = EventTicket::findOrFail($request->event_ticket_id);
            if ($event_ticket->available < $request->ticket_quantity) {
                if ($event->available_tickets == 0 || $event->available_tickets < 0) {
                    $request->session()->flash('error', 'No Tickets Available');
                } else {
                    $request->session()->flash('error', 'Only ' . $event->available_tickets . ' Tickets Available');
                }
                return back();
            }
            $currentLang = session()->has('lang') ? (Language::where('code', session()->get('lang'))->first())
                : (Language::where('is_default', 1)->first());
            $bs = $currentLang->basic_setting;
            $be = $currentLang->basic_extended;
            $bex = $currentLang->basic_extra;
            if ($bex->event_guest_checkout == 0 && !Auth::check()) {
                return redirect()->route('user.login', ['redirected' => 'event']);
            }
            if ($request->payment_method == "0") {
                return redirect()->back()->with('error', 'Choose a payment method')->withInput();
            }

            $offline_payment_gateways = OfflineGateway::query()->pluck('id')->toArray();
            Session::put('paymentFor', 'Event');
            $title = "You are purchasing an event ticket";
            $description = "Congratulation you are going to join our event.Please make a payment for confirming your ticket now!";
            if ($request->payment_method == 14) {
                $rules = [
                    'name' => 'required',
                    'email' => 'required|email',
                    'event_id' => 'required',
                    'ic_number' => ['required', new LimitEventBooking($request->event_id)],
                    'company_name' => 'required',
                    'ticket_quantity' => 'required',
                    'total_cost' => 'required',
                    'card_number' => 'required',
                    'card_month' => 'required',
                    'card_year' => 'required',
                    'card_cvv' => 'required',
                ];
                if (!Auth::check()) {
                    $rules['professional_member'] = 'nullable';
                    $rules['address'] = 'required';
                }
                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator->errors())->withInput();
                }
                $amount = round(($request->total_cost / $bex->base_currency_rate), 2);
                $request['status'] = "Success";
                $request['receipt_name'] = null;
                $stripe = new StripeController($request->payment_method);
                return $stripe->processPayment($request, $amount, $request->total_cost, $description, $bex, $be);
            } elseif ($request->payment_method == 20) {
                $rules = [
                    'name' => 'required',
                    'email' => 'required|email',
                    'phone' => 'required',
                    'event_id' => 'required',
                    'ic_number' => ['required', new LimitEventBooking($request->event_id)],
                    'company_name' => 'required',
                    'ticket_quantity' => 'required',
                    'total_cost' => 'required',
                    'rms_payment_options' => 'required'
                ];
                if (!Auth::check()) {
                    $rules['professional_member'] = 'nullable';
                    $rules['address'] = 'required';
                }
                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator->errors())->withInput();
                }
                // $amount = round(($request->total_cost / $bex->base_currency_rate), 2);
                $amount = round($request->total_cost, 2);
                $request['status'] = "Success";
                $request['receipt_name'] = null;
                $razerms = new RazerMsController;
                $cancel_url = route('donation.razerms.cancel', ['eid' => $request->event_id]);
                $success_url = route('donation.razerms.success', ['eid' => $request->event_id]);
                return $razerms->paymentProcess($request, $amount, $request->total_cost, $title, $success_url, $cancel_url);
            } elseif ($request->payment_method == 15) {
                $rules = [
                    'name' => 'required',
                    'email' => 'required|email',
                    'event_id' => 'required',
                    'ic_number' => ['required', new LimitEventBooking($request->event_id)],
                    'company_name' => 'required',
                    'ticket_quantity' => 'required',
                    'total_cost' => 'required',
                ];
                if (!Auth::check()) {
                    $rules['professional_member'] = 'nullable';
                    $rules['address'] = 'required';
                }
                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator->errors())->withInput();
                }
                $amount = round(($request->total_cost / $bex->base_currency_rate), 2);
                $request['status'] = "Success";
                $request['receipt_name'] = null;
                $paypal = new PaypalController;
                $cancel_url = route('donation.paypal.cancel');
                $success_url = route('donation.paypal.success');
                return $paypal->paymentProcess($request, $amount, $request->total_cost, $title, $success_url, $cancel_url);
            } elseif (in_array($request->payment_method, $offline_payment_gateways)) {
                $rules = [
                    'name' => 'required',
                    'email' => 'required|email',
                    'event_id' => 'required',
                    'ic_number' => ['required', new LimitEventBooking($request->event_id)],
                    'ticket_quantity' => 'required',
                    'total_cost' => 'required',
                    'receipt' => $request->is_receipt == 1 ? 'required | mimes:jpeg,jpg,png' : '',
                ];
                if (!Auth::check()) {
                    $rules['professional_member'] = 'nullable';
                    $rules['address'] = 'required';
                }
                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator->errors())->withInput();
                }
                $request['status'] = "Pending";
                $request['receipt_name'] = null;
                if ($request->has('receipt')) {
                    $filename = time() . '.' . $request->file('receipt')->getClientOriginalExtension();
                    $directory = "./assets/front/img/events/receipt";
                    if (!file_exists($directory)) mkdir($directory, 0777, true);
                    $request->file('receipt')->move($directory, $filename);
                    $request['receipt_name'] = $filename;
                }
                $amount = $request->total_cost;
                $transaction_id = uniqid('#');
                $transaction_details = "offline";
                $this->store($request, $transaction_id, json_encode($transaction_details), $amount, $bex);
                session()->flash('success', 'Payment recorder! Admin will confirm soon');
                return redirect()->route('front.event_details', [$request->event_slug]);
            }
        } catch (\Throwable $th) {
            //throw $th;
            // dd($th);
            session()->flash('error', 'Something wrong, Please try again later.');
            return back();
        }
    }
    public function store($request, $transaction_id, $transaction_details, $amount, $bex)
    {
        $event = Event::query()->findOrFail($request["event_id"]);
        $event_ticket = EventTicket::findOrFail($request["event_ticket_id"]);
        $eventCpdPoint = $event->cpd_points ?? 0;
        $user_id = Auth::check() ? Auth::user()->id : NULL;
        $event_details = EventDetail::create([
            'user_id' => $user_id,
            'name' => $request["name"],
            'email' => $request["email"],
            'phone' => $request["phone"],
            'ic_number' =>  $request["ic_number"],
            'company_name' =>  $request["company_name"],
            'professional_member' =>  $request["professional_member"] ?? null,
            'address' =>  $request["address"] ?? null,
            'event_ticket_id' => $request["event_ticket_id"],
            'amount' => $amount,
            'quantity' => $request["ticket_quantity"],
            'currency' => $bex->base_currency_text ? $bex->base_currency_text : "USD",
            'currency_symbol' => $bex->base_currency_symbol ? $bex->base_currency_symbol : $bex->base_currency_text,
            'payment_method' => $request["payment_method"],
            'transaction_id' => uniqid(),
            'status' => $request["status"] ? $request["status"] : "success",
            'receipt' => $request["receipt_name"] ? $request["receipt_name"] : null,
            'transaction_details' => $transaction_details ? json_encode($transaction_details) : null,
            'bex_details' => json_encode($bex),
            'event_id' => $request["event_id"],
            'cpd_points' => $eventCpdPoint
        ]);
        // updateCpdPoint($user_id, $eventCpdPoint * $request["ticket_quantity"]);
        $event_ticket->available = $event_ticket->available - $request["ticket_quantity"];
        $event_ticket->save();

        return $event_details;
    }
    public function makeInvoice($event, $fileName = null)
    {
        ini_set("memory_limit", "999M");
        ini_set("max_execution_time", "999");
        Session::put('event_details_id', $event->id);
        $file_name = "Event#" . $event->transaction_id . ".pdf";
        $event->invoice = $file_name;
        $event->save();
        $event->gateway = $event->paymentGateway;
        $pdf = Pdf::setOptions([
            'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
            'logOutputFile' => storage_path('logs/log.htm'),
            'tempDir' => storage_path('logs/')
        ])->loadView('pdf.custom.training', [
            'ticket' => $event
        ])->save(root_path('assets/front/invoices/' . $file_name));
        // $output = $pdf->output();
        // file_put_contents('assets/front/invoices/' . $file_name, $output);
        return $file_name;
    }

    public function sendMailPHPMailer($request, $file_name, $be)
    {
        $eventDetailsId = Session::get('event_details_id');
        $eventDetails = EventDetail::findOrFail($eventDetailsId);
        $event = Event::findOrFail($eventDetails->event_id);
        $bs = BasicSetting::firstOrFail();

        EventStatusEmailJob::dispatch(
            $request['email'],
            $request['name'],
            $file_name,
            $event->title,
            $eventDetailsId,
            $eventDetails->transaction_id,
            $bs
        )->delay(now()->addSeconds(5));

        Session::forget('event_details_id');
    }

    public function coupon(Request $request)
    {
        $coupon = Coupon::where(['code' => $request->coupon, 'coupon_type' => 'training']);
        $bex = BasicExtra::first();

        if ($coupon->count() == 0) {
            return response()->json(['status' => 'error', 'message' => "Coupon is not valid"]);
        } else {
            $coupon = $coupon->first();
            if ($request->cart_total < $coupon->minimum_spend) {
                return response()->json(['status' => 'error', 'message' => "Cart Total must be minimum " . $coupon->minimum_spend . " " . $bex->base_currency_text]);
            }
            $start = Carbon::parse($coupon->start_date);
            $end = Carbon::parse($coupon->end_date);
            $today = Carbon::now();
            // return response()->json($end->lessThan($today));

            // if coupon is active
            if ($today->greaterThanOrEqualTo($start) && $today->lessThan($end)) {
                $cartTotal = $request->cart_total;
                $value = $coupon->value;
                $type = $coupon->type;

                if ($type == 'fixed') {
                    if ($value > $request->cart_total) {
                        return response()->json(['status' => 'error', 'message' => "Coupon discount is greater than cart total"]);
                    }
                    $couponAmount = $value;
                } else {
                    $couponAmount = ($cartTotal * $value) / 100;
                }
                // session()->put('coupon', round($couponAmount, 2));

                return response()->json(['status' => 'success', 'coupon' => round($couponAmount, 2), 'message' => "Coupon applied successfully"]);
            } else {
                return response()->json(['status' => 'error', 'message' => "Coupon is not valid"]);
            }
        }
    }

    public function addToCart(Request $request)
    {
        $data = $this->validate($request, [
            'event_id'        => 'required',
            'event_ticket_id' => 'required',
            'quantity'        => 'required',
        ]);

        $event = Event::findOrFail($data['event_id']);
        $eventTicket = $event->eventTicket()->findOrFail($data['event_ticket_id']);

        $cart = Session::get('event_cart');
        $collect = collect($cart ?? []);

        $cartItem = null;
        $quantity = $data['quantity'];

        if ($collect->where('id', $event->id)->first()) {
            $cartItem = collect($collect->where('id', $event->id)->first());
            $quantity += $cartItem->get('qty', 0);
        }

        if ($eventTicket->available < $quantity) {
            Session::flash('error', 'Tickets are not available');
            return back();
        }

        $item = [
            'id' => $event->id,
            'tkId' => $eventTicket->id,
            'qty' => $quantity,
            'cost' => $eventTicket->cost,
            'title' => $event->title,
            'photo' => $event->image
        ];

        if ($cartItem) {
            $shiftItem = $collect->where('id', $event->id)->shift();
            $collect = $collect->filter(function ($item) use ($shiftItem) {
                return $item != $shiftItem;
            });
        }

        $collect->push($item);

        Session::put('event_cart', $collect->toArray());

        return back()->with('success', "Event added to the cart");
    }

    public function cartEventRemove($eventId)
    {
        $cart = Session::get('event_cart');
        $cart = collect($cart ?? []);
        $cartItem = $cart->where('id', $eventId)->first();
        $cart = $cart->filter(function ($item) use ($cartItem) {
            return $item != $cartItem;
        });
        Session::put('event_cart', $cart->toArray());
        return response()->json([
            'message' => 'Event removed successfully',
            'count' => CartItemTotal(),
            'total' => cartTotal(),
            'product_count' => CartProductCount(),
            'event_count' => CartEventCount(),
        ]);
    }

    public function updateCart(Request $request)
    {
        if (Session::get('event_cart')) {
            $cart = Session::get('event_cart');
            $cart = collect($cart ?? []);
            foreach ($request->event_id as $key => $id) {
                $event = Event::findOrFail($id);
                $ticket = $event->eventTicket()->findOrFail($request->ticket_id[$key]);
                if ($ticket->available < $request->event_qty[$key]) {
                    return [false, $event->title . ' ticket not available'];
                }
                $cartItem = $cart->where('id', $id)->first();
                $cart = $cart->filter(function ($item) use ($cartItem) {
                    return $item != $cartItem;
                });
                $cartItem['qty'] = $request->event_qty[$key];
                $cart->push($cartItem);
            }
            Session::put('event_cart', $cart->toArray());
        }
        return [true];
    }
}
