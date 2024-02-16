<?php

namespace App\Jobs;

use App\Http\Helpers\KreativMailer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EventStatusEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $email;
    public $name;
    public $file_name;
    public $event_title;
    public $event_detail_id;
    public $transaction_id;
    public $bs;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email, $name, $file_name, $event_title, $event_detail_id, $transaction_id, $bs)
    {
        $this->email = $email;
        $this->name = $name;
        $this->file_name = $file_name;
        $this->event_title = $event_title;
        $this->event_detail_id = $event_detail_id;
        $this->transaction_id = $transaction_id;
        $this->bs = $bs;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $mailer = new KreativMailer;
        $data = [
            'toMail' => $this->email,
            'toName' => $this->name,
            'attachment' => $this->file_name,
            'customer_name' => $this->name,
            'event_name' => $this->event_title,
            'ticket_id' => $this->event_detail_id,
            'order_link' => "<strong>Order Details:</strong> <a href='" . route('user-event-details', $this->event_detail_id) . "'>" . route('user-event-details', $this->event_detail_id) . "</a>",
            'website_title' => $this->bs->website_title,
            'templateType' => 'event_ticket',
            'type' => 'eventTicket'
        ];

        try {
            $mailer->mailFromAdmin($data);
        } catch (\Throwable $th) {
            $this->fail();
        }
    }
}
