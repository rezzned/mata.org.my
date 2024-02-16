<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    @include('pdf.style.style')
</head>
<body>
    <div class="order-comfirmation">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="logo text-center" style="margin-bottom: 20px; padding-top: 30px;">
                        <img src="{{asset('assets/front/img/' . $bs->logo)}}" alt="" height="80">
                    </div>
                    <div class="confirmation-message bg-primary" style="padding: 5px 0px;margin-bottom: 40px;">
                        <h2 class="text-center"><strong>{{__('ORDER INVOICE')}}</strong></h2>
                    </div>
                    <div class="row">
                        <div class="col-lg-4" style="width: 33.33%;float: left;">
                            <div>
                                <h3><strong>Order Details</strong></h3>
                            </div>
                            <p>
                                <strong>Order Number:</strong>
                                #{{$packageOrder->order_number}}
                            </p>
                            <p>
                                <strong>Order Date:</strong>
                                {{$packageOrder->created_at}}
                            </p>
                            @if ($packageOrder->method != 'Flutterwave')
                                <p>
                                    <strong>Payment Status:</strong>
                                    {{$packageOrder->payment_status == 1 ? 'Completed' : 'Pending'}}
                                </p>
                            @endif
                            <p>
                                <strong>Payment Method:</strong>
                                @if (!empty($packageOrder->method))
                                    {{$packageOrder->method}}
                                @else
                                -
                                @endif
                            </p>
                        </div>
                        <div style="width: 33.33%; float: left;" class="col-lg-4">
                            <div>
                                <h3><strong>Package Details</strong></h3>
                            </div>
                            <p>
                                <strong>Title:</strong>
                                {{$packageOrder->package_title}}
                            </p>
                            <p>
                                <strong>Price:</strong>
                                {{$bex->base_currency_text_position == 'left' ? $bex->base_currency_text : ''}} {{$packageOrder->package_price}} {{$bex->base_currency_text_position == 'right' ? $bex->base_currency_text : ''}}
                            </p>
                        </div>
                        <div class="col-lg-4" style="width: 33.33%; float: left;">
                            <div>
                                <h3><strong>Client Details</strong></h3>
                            </div>
                            <p>
                                <strong>Client Name:</strong>
                                {{$packageOrder->name}}
                            </p>
                            <p>
                                <strong>Client Email:</strong>
                                {{$packageOrder->email}}
                            </p>
                            @foreach ($fields as $key => $field)
                                @php
                                if (is_array($field['value'])) {
                                    $str = implode(", ", $field['value']);
                                    $value = $str;
                                } else {
                                    $value = $field['value'];
                                }
                                @endphp
                                @if ($field['type'] != 5)
                                    <p>
                                        <strong>{{str_replace("_"," ",$key)}}:</strong>
                                        {{$value}}
                                    </p>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
