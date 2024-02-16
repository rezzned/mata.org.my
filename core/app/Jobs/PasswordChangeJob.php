<?php

namespace App\Jobs;

use App\Http\Helpers\KreativMailer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PasswordChangeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $bs;
    public $password;
    public $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($bs, $user, $password)
    {
        $this->bs = $bs;
        $this->password = $password;
        $this->user = $user;
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
            'toMail' => $this->user->email,
            'toName' => $this->user->username,
            'customer_name' => $this->user->full_name,
            'password' => $this->password,
            'website_title' => $this->bs->website_title,
            'templateType' => 'password_update_by_admin',
        ];
        $mailer->mailFromAdmin($data);
    }
}
