<?php

namespace App\Jobs;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PublicationOrderPDF implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $data;
    public $fileName;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $fileName)
    {
        $this->data = $data;
        $this->fileName = $fileName;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        info($this->data);
        $path = 'assets/front/invoices/product/' . $this->fileName;
        Pdf::loadView('pdf.product', $this->data)->save($path);
    }
}
