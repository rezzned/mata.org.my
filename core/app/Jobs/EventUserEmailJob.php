<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PHPMailer\PHPMailer\PHPMailer;

class EventUserEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $be;
    public $event_details;
    public $status;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($be, $event_details, $status)
    {
        $this->be = $be;
        $this->event_details = $event_details;
        $this->status = $status;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $sub = "Ticket Booking Status Changed";
        $mail = new PHPMailer(true);
        if ($this->be->is_smtp == 1) {
            try {
                $mail->isSMTP();
                $mail->Host       = $this->be->smtp_host;
                $mail->SMTPAuth   = true;
                $mail->Username   = $this->be->smtp_username;
                $mail->Password   = $this->be->smtp_password;
                $mail->SMTPSecure = $this->be->encryption;
                $mail->Port       = $this->be->smtp_port;

                //Recipients
                $mail->setFrom($this->be->from_mail, $this->be->from_name);
                $mail->addAddress($this->event_details->email, $this->event_details->name);

                // Content
                $mail->isHTML(true);
                $mail->Subject = $sub;
                $mail->Body    = 'Hello <strong>' . $this->event_details->name . '</strong>,<br/><br>Your ticket booking status of <strong>' . $this->event_details->event->title . '</strong> is changed to: <strong>' . ucfirst($this->status) . '</strong>.<br/><br>Thank you.';
                $mail->send();
            } catch (\Exception $e) {
                // die($e->getMessage());
            }
        } else {
            try {

                //Recipients
                $mail->setFrom($this->be->from_mail, $this->be->from_name);
                $mail->addAddress($this->event_details->email, $this->event_details->name);

                // Content
                $mail->isHTML(true);
                $mail->Subject = $sub;
                $mail->Body    = 'Hello <strong>' . $this->event_details->name . '</strong>,<br/><br>Your ticket booking status of <strong>' . $this->event_details->event->title . '</strong> is changed to: <strong>' . ucfirst($this->status) . '</strong>.<br/><br>Thank you.';
                $mail->send();
            } catch (\Exception $e) {
                // die($e->getMessage());
            }
        }
    }
}
