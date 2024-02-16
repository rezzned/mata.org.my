<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Event Ticket</title>
    <!-- Latest compiled and minified CSS -->
    @include('pdf.style.bootstarp4')
    <style>
        .list-group-item span { float: right; }
        #wrapper { margin: 0 auto; }
    </style>
</head>

<body>
    <div id="wrapper">
        <div style="">
            <div style="width: 40%; float: left">
                <img src="{{ asset('assets/front/img/' . $bs->logo) }}" alt="" width="120" style="display: block">
            </div>
            <div style="width: 60%; float: right">
                <p class="mb-0">Malaysia Association of Tax Accountants (M.A.T.A)</p>
                <p class="mb-0">Block 1D, 27-1, Jalan Wangsa Delima 12, Wangsa Link, Pusat Bandar Wangsa Maju, Kuala Lumpur, 53300, Setapak, WP Kuala Lumpur</p>
            </div>
            <div style="clear: both;"></div>
        </div>
        <div class="mt-3 p-2">
            <strong class="h6 font-weight-bold">Invoice #{{ strtoupper($payment->invoice_id) }} </strong>
            <p class="mb-0">Invoice Date: {{ dateFormat($payment->created_at) }}</p>
        </div>
        <div class="row justify-content-between" style="margin:30px 0;">
            <div class="col-6">
                <p class="small mb-0"><strong>{{$payment->user->full_name}}</strong></p>
                <p class="small mb-0">{{ $payment->user->email }}</p>
                <p class="small mb-0">{{ $payment->user->address }}</p>
            </div>
            <div class="col-12">
                <div class="float-right">
                    <strong>Member ID: </strong>{{ $payment->user->membership_id }}
                </div>
                <p class="small mb-0">{{ $payment->user->city }}, {{ $payment->user->state }}, {{ $payment->user->country }}</p>
                @if ($payment->user->personal_phone)
                <p class="small mb-0">
                    Contact no: {{ $payment->user->personal_phone }}
                </p>
                @endif
            </div>

            {{-- <div class="col-xs-6">
                <img class="float-right" src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(80)->generate($payment->invoice_id)) !!} ">
            </div> --}}
        </div>
        <div class="mb-4">
            <table class="table table-sm table-bordered">
                <thead>
                    <tr class="text-center">
                        <th class="py-0">Description</th>
                        <th class="py-0">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @if (gettype($payment->item) == 'array')
                    @foreach ($payment->item as $item)
                    <tr>
                        <td>{{ $item->title ?? $item->name }}</td>
                        <td class="text-right">{{ $payment->currency }} {{ number_format($item->price,2) }}</td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td>{{ $payment->item->title ?? $payment->item->name }}</td>
                        <td class="text-right">{{ $payment->currency }} {{ number_format($payment->item->price,2) }}</td>
                    </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr class="text-right bg-light">
                        <td class="py-0"><strong>Sub Total</strong></td>
                        <td class="py-0">{{ $payment->currency }} @if (gettype($payment->item) == 'array') {{ number_format($payment->item->sum('price'),2) }} @else  {{ number_format($payment->item->price,2) }}  @endif</td>
                    </tr>
                    <tr class="text-right bg-light">
                        <td class="py-0"><strong>Tax</strong></td>
                        <td class="py-0">RM 0.00</td>
                    </tr>
                    <tr class="text-right bg-light">
                        <td class="py-0"><strong>Credit</strong></td>
                        <td class="py-0">RM 0.00</td>
                    </tr>
                    <tr class="text-right bg-light">
                        <td><strong>Total</strong></td>
                        <td>{{ $payment->currency }} {{ $payment->amount }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="mt-4">
            <h5>Transactions</h5>
            <table class="table table-sm table-bordered">
                <thead>
                    <tr class="text-center">
                        <th class="py-0">Transaction Date</th>
                        <th class="py-0">Gateway</th>
                        <th class="py-0">Transaction ID</th>
                        <th class="py-0">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        @if ($payment->status == 1)
                        <td>{{ dateFormat($payment->created_at) }}</td>
                        <td class="text-center">{{ convertUtf8($payment->gateway) }}</td>
                        <td class="text-center">{{ convertUtf8($payment->trnx_id) }}</td>
                        <td class="text-right">{{ $payment->currency . " " . $payment->amount }}</td>
                        @else
                        <td colspan="4" class="text-center">No related transaction found</td>
                        @endif
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="text-right bg-light">
                        <td colspan="3"><strong>Balance</strong></td>
                        <td>{{ $payment->currency }}
                            @if ($payment->status == 1)
                            @if (gettype($payment->item) == 'array')
                            {{ number_format($payment->item->sum('price') - $payment->amount,2) }}
                            @else {{ number_format($payment->item->price - $payment->amount, 2) }} @endif
                            @else {{ number_format($payment->amount, 2) }} @endif
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="mt-5">
            <p class="small">This is automatic generated invoice, no signature required.</p>
            <p class="small">For any offline payment, you may proceed for online transfer or cheque to our account Maybank Islamic: 5648 1051 2104
                <br>rhb islamic : 2640 580000 9140 </p>
            <p class="small">All cheques should be crossed and made to <b>PERSATUAN AKAUNTAN PERCUKAIAN MALAYSIA</b></p>
        </div>
    </div>
</body>

</html>
