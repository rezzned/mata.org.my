@extends('user.layout')

@section('pagename')
- {{__('My Payments')}}
@endsection

@section('content')
<style>
    button.btn-none {
        background: transparent !important;
        padding: 5px 10px !important;
    }
</style>
<!--   hero area start   -->
<!--   hero area end    -->


<!--====== CHECKOUT PART START ======-->
<section class="user-dashbord">
    <div class="container">
        <div class="row">
            @include('user.inc.site_bar')
            <div class="col-lg-9">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="user-profile-details">
                            <div class="account-info">
                                <div class="title">
                                    <h4>{{__('My Payments')}}</h4>
                                </div>
                                <div class="main-info">
                                    <div class="main-table">
                                        <div class="table-responsive">
                                            <table id="packagesTable"
                                                class="dataTables_wrapper dt-responsive table-striped dt-bootstrap4"
                                                style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th>{{__('Description')}}</th>
                                                        <th>{{__('Date')}}</th>
                                                        <th class="text-center">{{__('Amount')}}</th>
                                                        <th>{{__('Status')}}</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if($payments)
                                                    @foreach ($payments as $payment)
                                                    <tr>
                                                        <td>{{$payment->item->title}}</td>
                                                        <td>{{dateFormat($payment->created_at, null, true)}}</td>
                                                        <td class="text-right">{{$bex->base_currency_symbol_position ==
                                                            'left' ? $bex->base_currency_symbol : ''}}
                                                            {{ number_format($payment->amount, 2) }}
                                                            {{$bex->base_currency_symbol_position == 'right' ?
                                                            $bex->base_currency_symbol : ''}}</td>
                                                        <td>@switch((string)$payment->status)
                                                            @case('0') Pending @break
                                                            @case('1') Completed @break
                                                            @case('2') Failed @break
                                                            @endswitch</td>
                                                        <td>
                                                            @if ($payment->status != '0')
                                                                @if(!file_exists(root_path('assets/front/invoices/' . $payment->invoice)))
                                                                <form action="{{ route('user-invoice', ['paymentid' => encrypt($payment->id)]) }}"
                                                                    method="post">@csrf
                                                                    <input type="hidden" name="model" value="payments">
                                                                    <button type="submit" class="btn-none">{{('Invoice')}}</button>
                                                                </form>
                                                                @else
                                                                <form action="{{ asset('assets/front/invoices/' . $payment->invoice) }}" method="get">
                                                                    <button type="submit" class="btn-none">INVOICE</button>
                                                                </form>
                                                                @endif
                                                            @else
                                                            @php
                                                                $package_id = (isset($payment->order->pending_package_id)) ? $payment->order->pending_package_id : $payment->order->current_package_id;
                                                            @endphp
                                                            @if (isset($package_id))
                                                                <form action="{{route('front.packageorder.index',$package_id)}}" method="get">
                                                                    <button type="submit" class="btn-none">{{('Pay')}}</button>
                                                                </form>
                                                            @endif
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                    @else
                                                    <tr class="text-center">
                                                        <td colspan="4">
                                                            {{__('No payments')}}
                                                        </td>
                                                    </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--    footer section start   -->
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#packagesTable').DataTable({
            responsive: true,
            ordering: false
        });
    });
</script>
@endsection
