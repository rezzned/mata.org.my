<?php

namespace App\Jobs;

use App\Http\Helpers\KreativMailer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RegisterEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $bs;
    public $token;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($bs, $user, $token)
    {
        $this->bs = $bs;
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $mailer = new KreativMailer;
        $verificationLink = "http://m.mata.org.my/register/verify/{$this->token}";
        $data = [
            'toMail' => $this->user->email,
            'toName' => $this->user->username,
            'customer_username' => $this->user->username,
            'verification_link' => "<a href='{$verificationLink}'>{$verificationLink}</a>",
            'website_title' => $this->bs->website_title,
            'templateType' => 'email_verification',
            'type' => 'emailVerification'
        ];
        $mailer->mailFromAdmin($data);
    }
}
