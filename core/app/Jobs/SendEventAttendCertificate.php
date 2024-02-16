<?php

namespace App\Jobs;

use App\Http\Helpers\KreativMailer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendEventAttendCertificate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;
    /**
     * Create a new job instance.
     *
     * @param array $dataArray Array containing the necessary params.
     *    $dataArray = [
     *      'uuid'          => (string) transaction_id. required.\n
     *      'name'          => (string) Name. required.\n
     *      'event_title'   => (string) event->title. Required.\n
     *      'venue'         => (string) Vanue. Required.\n
     *      'date'          => (string) Date. Required.\n
     *      'ic_number'     => (string) IC Number. Required.\n
     *      'ic_number'     => (string) IC Number. Required.\n
     *      'company_name'  => (string) Company Name. Required.\n
     *      'phone'         => (string) Contact Number. Required.\n
     *      'email'         => (string) Email Address. Required.\n
     *      'cpd_points'    => (int|float) CPD Point. Default: 0.
     *    ]
     * @return void
     */
    public function __construct(array $dataArray)
    {
        $this->data = (object) $dataArray;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $tempPath = base_path('templates/attend_certificate_email.html');
        $attachment = 'cert_' . $this->data->uuid . '.pdf';
        $mailer = new KreativMailer;
        $data = [
            'toMail' => $this->data->email,
            'toName' => $this->data->name,
            'attachment' => $attachment,
            'event_title' => $this->data->event_title,
            'email_temp' => file_get_contents($tempPath),
            'email_subject' => "Attendence Certificate [M.A.T.A.]",
            'templateType' => 'event_cert',
            'type' => 'event_cert'
        ];

        try {
            $mailer->mailFromAdmin($data);
        } catch (\Exception $e) {
            Log::error("SendEventAttendCertificateError: " . $e->getMessage());
            $this->fail();
        }
    }
}
