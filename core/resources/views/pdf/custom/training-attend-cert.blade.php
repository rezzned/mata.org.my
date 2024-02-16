<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Certificate</title>
    <!-- Latest compiled and minified CSS -->
    @include('pdf.style.bootstarp4')
    <style>
        #wrapper { margin: 10px auto; }
        h1.certificate_title {
            font-family: Arial;
            font-size: 56px;
            color: #0D0D0D;
        }

        @supports (-webkit-text-stroke: 3px #A5A5A5) {
            h1.certificate_title {
                -webkit-text-stroke: 3px #A5A5A5;
                -webkit-text-fill-color: #0D0D0D;
            }
        }
    </style>
</head>

<body class="border">
    <div id="wrapper">
        <header class="text-center">
            <p class="text-right pr-3 pb-1" id="cert-no-and-year">
                @php($cert_id = str_pad(strval($data->cert_id), 3, "0", STR_PAD_LEFT))
                <strong><em>{{ $cert_id }}/{{ dateFormat($data->date, 'Y') }}{{ $data->short_form ?? "PCK" }}</em></strong>
            </p>
            <div class="pb-2">
                <img src="{{ asset('assets/logo-for-cert.png') }}" alt="" width="150">
            </div>

            <div class="certificate_title py-3">
                <img src="{{ asset('assets/certificate_title.png') }}" alt="" height="70">
            </div>
        </header>
        <main class="py-2">
            <p class="text-center">Dengan ini diperakui bahawa</p>

            <section class="pb-3">
                <div class="py-2">
                    <h3 class="text-center">{{ $data->name }}</h3>
                    <p class="text-center h3 mb-0">{{ $data->ic_number }}</p>
                </div>

                <div class="py-2">
                    <p class="text-center mb-0">Telah menyertai</p>
                    <?php
                        $event_title = $data->event_title;
                        if (strlen($event_title) > 35) {
                            $__titleWords = explode(' ', $event_title);
                            $event_title = [];
                            for ($i = 0; $i < count($__titleWords); $i++) {
                                if ($i%5==0) {
                                    $event_title[] = '<br>';
                                }
                                $event_title[] = $__titleWords[$i];
                            }
                            $event_title = implode(' ', $event_title);
                        }
                    ?>
                    <p class="text-center h3 mn-0">{!! $event_title !!}</p>
                </div>

                <div class="py-2">
                    <p class="text-center mb-0">Sebagai</p>
                    <p class="text-center h4 mb-0">PESERTA</p>
                </div>

                <div class="py-2">
                    <p class="text-center mb-0">Anjuran :</p>
                    <p class="text-center mb-0 h5">Persatuan Akauntan Percukaian Malaysia</p>
                    <p class="text-center h5 mb-0"><em>[Malaysian Association of Tax Accountants]</em> (M.A.T.A)</p>
                </div>

                <p class="text-center py-2">
                    Bertempat di <strong>{{ $data->venue }}</strong> Pada <strong>{{ dateFormat($data->date, 'M d, Y') }}</strong>
                    @if(!empty($data->date2))
                        &amp; <strong>{{ dateFormat($data->date2, 'M d, Y') }}</strong>
                    @endif
                </p>

                <div class="py-2">
                    <p class="text-center h5 mb-0">{{ round($data->cpd_points) }} MATA CPD</p>
                </div>
            </section>
        </main>
        <footer class="mb-0">
            <div class="d-flex justify-content-between">
                <div class="pl-3 float-left">
                    <div class="text-center" style="border-bottom: 3px dotted #000">
                        <img src="{{ asset('assets/signature_of_president.png') }}" alt="signature" style="max-height:70px;max-width:150px">
                    </div>
                    <div>
                        <p class="mb-0 font-weight-bold">(DATO&rsquo; HJ. ABD. AZIZ BIN ABU BAKAR)</p>
                        <p class="text-center">Presiden</p>
                    </div>
                </div>
                <div class="pr-4 float-right">
                    <div class="text-center">
                        <img src="{{ asset('assets/logo-for-cert.png') }}" alt="" width="130">
                    </div>
                    <p class="text-center pt-3">
                        <strong>{{ date('d-m-Y') }}</strong>
                    </p>
                </div>
            </div>
        </footer>
    </div>
</body>

</html>
