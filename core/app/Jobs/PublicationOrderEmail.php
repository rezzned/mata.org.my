<?php

namespace App\Jobs;

use App\Http\Helpers\KreativMailer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PublicationOrderEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $bs;
    public $order;
    public $fileName;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($bs, $order, $fileName)
    {
        $this->bs = $bs;
        $this->order = $order;
        $this->fileName = $fileName;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Send Mail to Buyer
        $mailer = new KreativMailer;
        $data = [
            'toMail' => $this->order->shpping_email,
            'toName' => $this->order->shpping_fname,
            'attachment' => $this->fileName,
            'customer_name' => $this->order->shpping_fname,
            'order_number' => $this->order->order_number,
            'order_link' => !empty($this->order->user_id) ? "<strong>Order Details:</strong> <a href='" . route('user-orders-details', $this->order->id) . "'>" . route('user-orders-details', $this->order->id) . "</a>" : "",
            'website_title' => $this->bs->website_title,
            'templateType' => 'product_order',
            'type' => 'productOrder'
        ];

        $mailer->mailFromAdmin($data);
    }
}
