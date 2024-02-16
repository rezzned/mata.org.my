<?php

namespace App\Http\Controllers\Admin;

use App\Package;
use App\Payment;
use Carbon\Carbon;
use App\Subscription;
use App\BasicExtended;
use App\OfflineGateway;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Notifications\SubscriptionNotify;
use Illuminate\Support\Facades\Validator;
use App\Notifications\LicenseExpireNotify;
use Barryvdh\DomPDF\Facade\Pdf;

class SubscriptionController extends Controller
{
    public function subscriptions(Request $request)
    {
        $data['packages'] = Package::all();

        $type = $request->type;
        $term = $request->term;
        $package = $request->package;

        $sub = Subscription::when($type, function ($query, $type) {
            if ($type == 'all') {
                return $query->where('status', '<>', 3);
            } elseif ($type == 'active') {
                return $query->where('status', 1);
            } elseif ($type == 'expired') {
                return $query->where('status', 0);
            } elseif ($type == 'request') {
                return $query->whereNotNull('pending_package_id');
            }
        })->when($term, function ($query, $term) {
            $query->where('name', 'like', '%' . $term . '%');
        })->when($package, function ($query, $package) {
            $query->where('current_package_id', $package);
        })->orderBy('id', 'DESC')->paginate(10);

        $data['subscriptions'] = $sub;
        return view('admin.package.subscriptions', $data);
    }

    public function subscriptionChangeDate(Request $request)
    {
        $request->validate([
            'subscription_id' => 'required',
            'expire_date' => 'required',
            'start_date' => 'required'
        ]);

        $sub = Subscription::findOrFail(request('subscription_id'));
        $sub->expire_date = Carbon::parse($request->expire_date)->format('Y-m-d');
        $sub->save();
        if ($sub->user) {
            $sub->user->associate_member_start_date == Carbon::parse($request->expire_date)->format('Y-m-d');
            $sub->user->save();
            //            $sub->user()->update([
            //                'associate_member_start_date' => Carbon::parse($request->start_date)->format('Y-m-d'),
            //                'license_expire_notify_date' => Carbon::parse($request->expire_date)->subDays(7)->format('Y-m-d'),
            //                'license_expire_date' => Carbon::parse($request->expire_date)->format('Y-m-d')
            //            ]);
            //            if (Carbon::parse($request->expire_date)->subDays(7) <= today()) {
            //                $sub->user->license_expire_notify = 'yes';
            //                $sub->user->save();
            //                $sub->user->notify(new LicenseExpireNotify);
            //            } elseif (Carbon::parse($request->expire_date) <= today()) {
            //                $sub->user->notify(new LicenseExpireNotify);
            //            }
        }

        session()->flash('success', 'Change date successfully');
        return back();
    }

    public function changePackage(Request $request, $id)
    {
        $this->validate($request, ['package_id' => 'required']);

        $sub = Subscription::findOrFail($id);
        $pack = Package::findOrFail($request->package_id);

        if ($sub && $pack) {
            $sub->current_package_id = $pack->id;
            $sub->save();

            session()->flash('success', 'Membership changed successfully');
            return back();
        }
    }

    public function subDelete(Request $request)
    {
        $sub = Subscription::findOrFail($request->subscription_id);

        //        $sub->user->update([
        //            'license_expire_date' => null,
        //            'license_expire_notify' => 'no'
        //        ]);

        $sub->delete();

        $request->session()->flash('success', 'Subscription deleted successfully');
        return back();
    }

    public function status(Request $request)
    {
        $sub = Subscription::findOrFail($request->subscription_id);
        $be = BasicExtended::first();
        $pendingPackage = $sub->pending_package->title;
        $user = $sub->user;

        // if accepted
        if ($request->status == 'accept') {

            if ($sub->pending_package->type == 'associate_member') {
                if (is_null($user->associate_member_start_date)) {
                    $sub->user()->update(['associate_member_start_date' => today()->format('Y-m-d')]);
                }
            }

            $pending_package_id = $sub->pending_package_id;

            if ($sub->pending_package->type == 'associate_to_standard_member') {
                # code...
                $package = Package::where(['type' => 'standard_member'])->first();

                $sub->current_package_id = $package->id;
                $pending_package_id = $package->id;
            }

            // if active subscription does not exist
            if ($sub->status != 1) {
                // current package will be pending package
                $sub->current_package_id = $pending_package_id;
                $sub->current_payment_method = $sub->pending_payment_method;
                $sub->pending_package_id = NULL;
                $sub->pending_payment_method = NULL;
                $sub->next_package_id = NULL;
                $sub->next_payment_method = NULL;
                $sub->status = 1;

                $activationDate = Carbon::now();
                // calc new expire date & save in database
                $duration = $sub->current_package->duration;
                if ($duration == 'monthly') {
                    $days = 30;
                } else {
                    $days = 365;
                }
                $expiryDate = Carbon::now()->addDays($days);
                $sub->expire_date = $expiryDate;
                $sub->is_upgrade = 0;
                $sub->save();
            }
            // if active subscription exists
            else {

                // next package will be pending package
                $sub->next_package_id = $pending_package_id;
                $sub->next_payment_method = $sub->pending_payment_method;
                $sub->pending_package_id = NULL;
                $sub->pending_payment_method = NULL;


                $activationDate = Carbon::parse($sub->expire_date);
                // calc new expire date & save in database
                $duration = $sub->current_package->duration;
                if ($duration == 'monthly') {
                    $days = 30;
                } else {
                    $days = 365;
                }
                $expiryDate = Carbon::parse($sub->expire_date)->addDays($days);

                $sub->expire_date = $expiryDate;
                $sub->is_upgrade = 0;
                $sub->save();
            }

            //            $user->license_expire_date = $sub->expire_date;
            //            $user->license_expire_notify_date = Carbon::parse($sub->expire_date)->subDays(7);
            //            $user->license_expire_notify = 'no';
            $user->save();

            // send mail mentioning activation date & expire date
            $subject = "Subscription Request Accepted";
            $body = "Hello <strong>$sub->name</strong>,<br>Your subscription request of <strong>" . $pendingPackage;
            $body .=  "</strong> has been accepted.<br><strong>Activation Date:</strong>" . $activationDate->toFormattedDateString() . ".<br><strong>Expire Date:</strong>" . $expiryDate->toFormattedDateString();
            $body .=  "<br>Please pay for you subscription <a href=" . route('user-packages.payment', $sub->id) . ">Click Here</a>.";
            $body .=  "<br>Thank you.";
        }
        // if rejected
        elseif ($request->status == 'reject') {
            $sub->pending_package_id = NULL;
            $sub->pending_payment_method = NULL;
            $sub->is_upgrade = 0;
            $sub->save();

            // send mail notification about rejection
            $subject = "Subscription Request Rejected";
            $body = "Hello <strong>$sub->name</strong>,<br>Your subscription request of <strong>$pendingPackage</strong> has been rejected.<br>Thank you.";
        }

        $sub->user->notify(new SubscriptionNotify($request->status, $sub));

        // unlink previous receipt image
        @unlink('assets/front/receipt/' . $sub->receipt);

        // Send Mail to Buyer
        $this->sendEmail($be, $sub->email, $sub->name, $subject, $body);

        $request->session()->flash('success', 'Status updated successfully');
        return back();
    }


    public function mail(Request $request)
    {
        $rules = [
            'email' => 'required',
            'subject' => 'required',
            'message' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $be = BasicExtended::first();
        $from = $be->from_mail;

        $sub = $request->subject;
        $msg = $request->message;
        $to = $request->email;

        // Send Mail
        $mail = new PHPMailer(true);

        if ($be->is_smtp == 1) {
            try {
                $mail->isSMTP();
                $mail->Host       = $be->smtp_host;
                $mail->SMTPAuth   = true;
                $mail->Username   = $be->smtp_username;
                $mail->Password   = $be->smtp_password;
                $mail->SMTPSecure = $be->encryption;
                $mail->Port       = $be->smtp_port;

                //Recipients
                $mail->setFrom($from);
                $mail->addAddress($to);

                // Content
                $mail->isHTML(true);
                $mail->Subject = $sub;
                $mail->Body    = $msg;

                $mail->send();
            } catch (Exception $e) {
            }
        } else {
            try {

                //Recipients
                $mail->setFrom($from);
                $mail->addAddress($to);

                // Content
                $mail->isHTML(true);
                $mail->Subject = $sub;
                $mail->Body    = $msg;

                $mail->send();
            } catch (Exception $e) {
            }
        }

        Session::flash('success', 'Mail sent successfully!');
        return "success";
    }

    public function bulkSubDelete(Request $request)
    {
        $ids = $request->ids;

        foreach ($ids as $id) {
            $sub = Subscription::findOrFail($id);
            @unlink('assets/front/receipt/' . $sub->receipt);
            $sub->delete();
        }

        Session::flash('success', 'Subscription deleted successfully!');
        return "success";
    }

    public function MakeInvoice(Request $request)
    {
        ini_set("memory_limit", "999M");
        ini_set("max_execution_time", "999");

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

    public function sendEmail($be, $email, $name, $subject, $body)
    {
        $mail = new PHPMailer(true);
        if ($be->is_smtp == 1) {
            try {
                $mail->isSMTP();
                $mail->Host       = $be->smtp_host;
                $mail->SMTPAuth   = true;
                $mail->Username   = $be->smtp_username;
                $mail->Password   = $be->smtp_password;
                $mail->SMTPSecure = $be->encryption;
                $mail->Port       = $be->smtp_port;

                //Recipients
                $mail->setFrom($be->from_mail, $be->from_name);
                $mail->addAddress($email, $name);

                // Content
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $body;
                $mail->send();
            } catch (Exception $e) {
                // die($e->getMessage());
            }
        } else {
            try {

                //Recipients
                $mail->setFrom($be->from_mail, $be->from_name);
                $mail->addAddress($email, $name);


                // Content
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $body;

                $mail->send();
            } catch (Exception $e) {
                // die($e->getMessage());
            }
        }
    }
}
