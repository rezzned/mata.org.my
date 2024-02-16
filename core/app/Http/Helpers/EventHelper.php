<?php

namespace App\Http\Helpers;

use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\Pdf;

trait EventHelper
{
    public static function makeInvoice($event)
    {
        ini_set("memory_limit", "999M");
        ini_set("max_execution_time", "999");
        Session::put('event_details_id', $event->id);
        $file_name = "Event#" . $event->transaction_id . ".pdf";
        $event->invoice = $file_name;
        $event->save();
        $pdf = Pdf::setOptions([
            'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
            'logOutputFile' => storage_path('logs/log.htm'),
            'tempDir' => storage_path('logs/')
        ])->loadView('pdf.event', compact('event'));
        $output = $pdf->output();
        file_put_contents('assets/front/invoices/' . $file_name, $output);
        return $file_name;
    }

    /**
     * Create a new job instance.
     *
     * @param array $dataArray Array containing the necessary params.
     *    $dataArray = [
     *      'uuid'          => (string) transaction_id. required.\n
     *      'name'          => (string) Name. Required.\n
     *      'event_title'   => (string) event->title. Required.\n
     *      'venue'         => (string) Vanue. Required.\n
     *      'date'          => (string) Date. Required.\n
     *      'ic_number'     => (string) IC Number. Required.\n
     *      'ic_number'     => (string) IC Number. Required.\n
     *      'cpd_points'    => (int|float) CPD Point. Default: 0.
     *    ]
     * @return void
     */
    public static function makeCertificate(array $data): void
    {
        $data = (object) $data;
        $folder_path = 'assets/front/certificate';
        $file_path = root_path($folder_path . '/' . $data->certificate_file);

        if (!file_exists(root_path($folder_path))) {
            @mkdir(root_path($folder_path));
        }

        Pdf::setOptions([
            'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
            'logOutputFile' => storage_path('logs/log.htm'),
            'tempDir' => storage_path('logs/')
        ])->loadView('pdf.custom.training-attend-cert', compact('data'))
            ->save(root_path($folder_path . '/' . $data->certificate_file));
    }
}
