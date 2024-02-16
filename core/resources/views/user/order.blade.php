@extends('user.layout')

@section('pagename')
 - {{__('Orders')}}
@endsection

@section('content')

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
                                    <h4>{{__('Publication Orders')}}</h4>
                                </div>
                                <div class="main-info">
                                    <div class="main-table">
                                    <div class="table-responsiv">
                                        <table id="ordersTable" class="dataTables_wrapper dt-responsive table-striped dt-bootstrap4" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>{{__('Order number')}}</th>
                                                    <th>{{__('Date')}}</th>
                                                    <th>{{__('Total Price')}}</th>
                                                    <th>{{__('Status')}}</th>
                                                    <th>{{__('Action')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if($orders)
                                                @foreach ($orders as $order)
                                                <tr>
                                                <td>{{$order->order_number}}</td>
                                                    <td>{{$order->created_at->format('d-m-Y')}}</td>
                                                    <td>{{$bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : ''}} {{$order->total}} {{$bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : ''}}</td>
                                                    <td>
                                                        @switch($order->order_status)
                                                            @case("pending")
                                                                <span class="text-warning">Pending</span>
                                                                @break
                                                            @case("processing")
                                                                <span class="text-info">Processing</span>
                                                                @break
                                                            @case("completed")
                                                                <span class="text-success">Completed</span>
                                                                @break
                                                            @case("Cancelled")
                                                                <span class="text-success">Cancelled</span>
                                                                @break
                                                            @case("rejected")
                                                            @default
                                                                @if ($order->payment_status == "Canceled")
                                                                <span class="text-danger">Canceled</span>
                                                                @else
                                                                <span class="text-danger">Rejected</span>
                                                                @endif
                                                        @endswitch
                                                    </td>
                                                    <td><a href="{{route('user-orders-details',$order->id)}}" class="btn base-bg text-white">{{__('Details')}}</a></td>
                                                </tr>
                                                @endforeach
                                                @else
                                                <tr class="text-center">
                                                    <td colspan="4">
                                                        {{__('No Orders')}}
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
        $('#ordersTable').DataTable({
            responsive: true,
            ordering: false
        });
    });
</script>
@endsection

