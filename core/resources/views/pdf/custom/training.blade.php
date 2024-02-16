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
        #wrapper { margin: 10px auto; }
    </style>
</head>

<body>
    <div id="wrapper">
        <div>
            <div style="width:40%; float: left;">
                <img src="{{ asset('assets/front/img/' . $bs->logo) }}" alt="" width="120" style="display: block">
                <div class="mt-3 p-2">
                    <strong class="h6 font-weight-bold">Invoice #{{ strtoupper($ticket->transaction_id) }} </strong>
                    <p class="mb-0">Invoice Date: {{ dateFormat($ticket->created_at) }}</p>
                </div>
            </div>
            <div style="width:60%; float: right;">
                <p class="mb-0">Malaysia Association of Tax Accountants (M.A.T.A)</p>
                <p class="mb-0">Block 1D, 27-1, Jalan Wangsa Delima 12, Wangsa Link, Pusat Bandar Wangsa Maju, Kuala Lumpur, 53300, Setapak, WP Kuala Lumpur</p>
            </div>
            <div style="clear: both;"></div>
        </div>
        <div class="row justify-content-between" style="margin:30px 0;">
            <div class="col-6">
                <p class="small mb-0"><strong>{{$ticket->name}}</strong></p>
                <p class="small mb-0">{{ $ticket->ic_number }}</p>
                <p class="small mb-0">{{ $ticket->email }}</p>
                <p class="small mb-0">{{ $ticket->company_name }}</p>
                @if ($ticket->address)
                    <p class="small mb-0">Address: {{ $ticket->address }}</p>
                @endif
                @if ($ticket->user)
                    <p class="small mb-0">Address: {{ $ticket->user->address }}</p>
                @endif
            </div>
            <div class="col-12">
                <div class="float-right">
                    <strong>Member ID: </strong>{{ $ticket->user->membership_id ?? "N/A" }}
                </div>
                @if ($ticket->user)
                <p class="small mb-0">{{ $ticket->user->city }}, {{ $ticket->user->state }}, {{ $ticket->user->country }}</p>
                @endif
                <p class="small mb-0">Contact no: {{ $ticket->phone }}</p>
            </div>
            {{-- <div class="col-xs-6">
                <img class="float-right" src="{{ $qr_code_image }}">
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
                    <tr>
                        <td>{{ $ticket->event->title }}</td>
                        <td class="text-right">{{ $ticket->quantity }} &times; {{ $ticket->currency }} {{ number_format($ticket->amount/$ticket->quantity,2) }} = {{ $ticket->currency }} {{ number_format($ticket->amount,2) }}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="text-right bg-light">
                        <td><strong>Sub Total</strong></td>
                        <td>{{ $ticket->currency }} {{ number_format($ticket->amount,2) }}</td>
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
                        <td>{{ $ticket->currency }} {{ number_format($ticket->amount,2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="mt-3">
            <h5 class="font-weight-bold"> Transactions</h5>
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
                        @if (strtolower($ticket->status) == 'success')
                        <td>{{ dateFormat($ticket->created_at) }}</td>
                        <td class="text-center">
                            {{-- {{ $ticket->gateway }} --}}
                            {{ convertUtf8($ticket->gateway->name ?? $ticket->payment_method ?? 'Unknown') }}
                        </td>
                        <td class="text-center">
                            @if ($ticket->transaction != '"offline"')
                            {{ convertUtf8($ticket->transaction->id ?? $ticket->transaction->tranID ?? '-') }}
                            @else
                            {{ __('N/A') }}
                            @endif
                        </td>
                        <td class="text-right">{{ $ticket->currency . " " . number_format($ticket->amount,2) }}</td>
                        @else
                        <td colspan="4" class="text-center">No related transaction found</td>
                        @endif
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="text-right bg-light">
                        <td colspan="3"><strong>Balance</strong></td>
                        <td>{{ $ticket->currency }} @if (strtolower($ticket->status) != 'success')
                            {{ number_format($ticket->amount, 2) }} @else 0.00
                            @endif</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="mt-5">
            <p class="small">This is automatic generated invoice, no signature required.</p>
            <p class="small">
                For any offline payment, you may proceed for online transfer or cheque to our account:
                <br><b>Maybank Islamic: 5648 1051 2104</b>
                <br><b>RHB Islamic : 2640 580000 9140</b>
            </p>
            <p class="small">All cheques should be crossed and made to <b>PERSATUAN AKAUNTAN PERCUKAIAN MALAYSIA</b></p>
        </div>
    </div>
</body>

</html>
