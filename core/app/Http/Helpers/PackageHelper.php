<?php

namespace App\Http\Helpers;

use App\Package;
use App\Payment;
use App\PackageOrder;
use App\Subscription;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Session;

class PackageHelper
{
    public static function submitOrder($packageId, $request = null, $be = null, $fields = [], $adminRequest = false)
    {
        ini_set("memory_limit", "999M");
        ini_set("max_execution_time", "999");
        $jsonfields = json_encode($fields);
        $jsonfields = str_replace("\/", "/", $jsonfields);

        $package = Package::findOrFail($packageId);

        $in = gettype($request) == 'object' ? $request->all() : [];
        $in['name'] = $request->name ?? $request['name'];
        $in['email'] = $request->email ?? $request['email'];
        $in['fields'] = $jsonfields;

        $in['package_title'] = $package->title;
        $in['package_currency'] = $package->currency;
        $in['package_price'] = packageTotalPrice($package);
        $in['package_description'] = $package->description;
        $fileName = Str::random(4) . time() . '.pdf';
        $in['invoice'] = $fileName;
        $po = PackageOrder::create($in);


        // saving order number
        $po->order_number = $po->id + 1000000000;
        $po->save();


        // sending datas to view to make invoice PDF
        $fields = json_decode($po->fields, true);
        $data['packageOrder'] = $po;
        $data['fields'] = $fields;


        // generate pdf from view using dynamic datas
        // Pdf::loadView('pdf.package', $data)->save('assets/front/invoices/' . $fileName);


        // Send Mail to Buyer
        $mail = new PHPMailer(true);

        if ($be->is_smtp == 1) {
            try {
                //Server settings
                // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
                $mail->isSMTP();                                            // Send using SMTP
                $mail->Host       = $be->smtp_host;                    // Set the SMTP server to send through
                $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
                $mail->Username   = $be->smtp_username;                     // SMTP username
                $mail->Password   = $be->smtp_password;                               // SMTP password
                $mail->SMTPSecure = $be->encryption;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
                $mail->Port       = $be->smtp_port;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

                //Recipients
                $mail->setFrom($be->from_mail, $be->from_name);
                $mail->addAddress($request->email, $request->name);     // Add a recipient

                // Attachments
                $mail->addAttachment('assets/front/invoices/' . $fileName);         // Add attachments

                // Content
                $mail->isHTML(true);                                  // Set email format to HTML
                $mail->Subject = "Order placed for " . $package->title;
                $mail->Body    = 'Hello <strong>' . $request->name . '</strong>,<br/>Your order has been placed successfully. We have attached an invoice in this mail.<br/>Thank you.';

                $mail->send();
            } catch (Exception $e) {
                // die($e->getMessage());
            }
        } else {
            try {

                //Recipients
                $mail->setFrom($be->from_mail, $be->from_name);
                $mail->addAddress($request->email, $request->name);     // Add a recipient

                // Attachments
                $mail->addAttachment('assets/front/invoices/' . $fileName);         // Add attachments

                // Content
                $mail->isHTML(true);                                  // Set email format to HTML
                $mail->Subject = "Order placed for " . $package->title;
                $mail->Body    = 'Hello <strong>' . $request->name . '</strong>,<br/>Your order has been placed successfully. We have attached an invoice in this mail.<br/>Thank you.';

                $mail->send();
            } catch (Exception $e) {
                // die($e->getMessage());
            }
        }

        // send mail to Admin
        try {
            $mail = new PHPMailer(true);
            $mail->setFrom($po->email, $po->name);
            $mail->addAddress($be->from_mail);     // Add a recipient

            // Attachments
            $mail->addAttachment('assets/front/invoices/' . $fileName);         // Add attachments

            // Content
            $mail->isHTML(true);  // Set email format to HTML
            $mail->Subject = "Order placed for " . $package->title;
            $mail->Body    = 'A new order has been placed.<br/><strong>Order Number: </strong>' . $po->order_number;

            $mail->send();
        } catch (\Exception $e) {
            // die($e->getMessage());
        }

        if ($adminRequest) {
            return (object) compact('po', 'package');
        }

        Session::flash('success', 'Order placed successfully! Admin will notify you shortly');
        return redirect()->route('user-packages');
        // return redirect()->route('front.packageorder.confirmation', [$package->id, $po->id]);
    }

    public static function createPayment($package, $gateway, $amount, $order, $currency = null)
    {
        $payment = new Payment([
            'user_id' => auth()->user()->id ?? null,
            'gateway' => $gateway,
            'item_model' => get_class($package),
            'item_info' => $package,
            'order_model' => get_class($order),
            'order_info' => $order,
            'amount' => $amount,
            'currency' => $currency
        ]);
        $payment->save();
        $payment->invoice_id = strtoupper(Str::random(2) . strval(100000 + $payment->id));
        $payment->save();
        Session::put("payment{$package->id}{$order->id}", $payment->id);
    }

    public static function updatePayment($packageId, $orderId, array $attrs)
    {
        $sessionKey = "payment{$packageId}{$orderId}";
        $payment = Payment::find(Session::get($sessionKey));
        foreach ($attrs as $key => $value) {
            $payment->$key = $value;
        }
        if ($payment->save()) {
            if ($payment->status == 1) {
                Session::forget($sessionKey);
            }
        }
    }
}
