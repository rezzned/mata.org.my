<?php

namespace App\Jobs;

use App\Http\Helpers\KreativMailer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UserVerifiedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $bs;
    public $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($bs, $user)
    {
        $this->user = $user;
        $this->bs = $bs;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $email = $this->user->email;
        $name = $this->user->name;
        $bs = $this->bs;

        // Send Mail to Buyer
        $mailer = new KreativMailer;

        // $loginLink = "http://m.mata.org.my";
        $data = [
            'toMail' => $email,
            'toName' => $name,
            'customer_name' => $name,
            'website_title' => $bs->website_title,
            'status' => $this->user->email_verified,
            // 'action_btn_link' => "<a href=\"{$loginLink}\">Login</a><br><a href=\"{$loginLink}\">{$loginLink}</a> ",
            'templateType' => 'email_verified_by_admin',
        ];

        $mailer->mailFromAdmin($data);
    }
}
