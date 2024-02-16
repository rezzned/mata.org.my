@extends('user.layout')

@section('pagename')
 - {{__('Orders')}}
@endsection

@section('content')
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
                                    <h4>{{__('Event Bookings')}}</h4>
                                </div>
                                <div class="main-info">
                                    <div class="main-table">
                                    <div class="table-responsiv">
                                        <table id="eventsTable" class="dataTables_wrapper dt-responsive table-striped dt-bootstrap4" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>{{__('Ticket ID')}}</th>
                                                    <th>{{__('Event')}}</th>
                                                    <th class="text-center">{{__('Quantity')}}</th>
                                                    <th class="text-center">{{__('CPD Points')}}</th>
                                                    <th>{{__('Cost')}}</th>
                                                    <th>{{__('Status')}}</th>
                                                    <th>{{__('Details')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if($events)
                                                @foreach ($events as $event)
                                                <tr>
                                                    <td>{{$event->transaction_id}}</td>
                                                    <td>{{strlen($event->event->title) > 30 ? mb_substr($event->event->title,0,30,'utf-8') . '...' : $event->event->title}}</td>
                                                    <td class="text-center">{{$event->quantity}}</td>
                                                    <td class="text-center">{{$event->quantity * $event->cpd_points}}</td>
                                                    <td>{{ $bex->base_currency_symbol}} {{$event->amount}}</td>
                                                    <td>
                                                        @switch($event->status)
                                                            @case("Pending")
                                                                <span class="text-warning">Pending</span>
                                                                @break
                                                            @case("Success")
                                                                <span class="text-success">Accepted</span>
                                                                @break
                                                            @case("Canceled")
                                                                <span class="text-danger">Canceled</span>
                                                                @break
                                                            @case("Rejected")
                                                            @default
                                                                <span class="text-danger">Rejected</span>
                                                        @endswitch
                                                    </td>
                                                    <td>
                                                        <a href="{{route('user-event-details', $event->id)}}" class="btn base-bg text-white py-1">{{__('Details')}}</a>
                                                    </td>
                                                </tr>
                                                @endforeach
                                                @else
                                                <tr class="text-center">
                                                    <td colspan="4">
                                                        {{__('No Booking Found')}}
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
        $('#eventsTable').DataTable({
            responsive: true,
            ordering: false
        });
    });
</script>
@endsection

