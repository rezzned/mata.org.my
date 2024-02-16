@extends('user.layout')

@section('pagename')
 - {{__('Orders')}}
@endsection

@section('content')
  <!--   hero area start   -->
  {{-- <div class="breadcrumb-area services service-bg" style="background-image: url('{{asset  ('assets/front/img/' . $bs->breadcrumb)}}');background-size:cover;">
    <div class="container">
        <div class="breadcrumb-txt">
            <div class="row">
                <div class="col-xl-7 col-lg-8 col-sm-10">
                    <h1>{{__('Upcoming Events')}}</h1>
                    <ul class="breadcumb">
                        <li><a href="{{route('user-dashboard')}}">{{__('Dashboard')}}</a></li>
                        <li>{{__('Upcoming Events')}}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="breadcrumb-area-overlay"></div>
</div> --}}
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
                                    <h4>{{__('Upcoming Events')}}</h4>
                                </div>
                                <div class="main-info">
                                    <div class="main-table">
                                    <div class="table-responsiv">
                                        <table id="eventsTable" class="dataTables_wrapper dt-responsive table-striped dt-bootstrap4" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>{{__('Event')}}</th>
                                                    <th>{{__('Date')}}</th>
                                                    <th>{{__('Cost/Ticket')}}</th>
                                                    <th>{{__('Booking')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if($events)
                                                @foreach ($events as $event)
                                                @php
                                                    $event_ticket = $event->eventTicket;
                                                @endphp
                                                <tr>
                                                    <td>{{$event->title}}</td>
                                                    <td>{{ dateFormat($event->date) }}</td>
                                                    <td>
                                                        @foreach ($event_ticket as $eventTicket)
                                                            <p class="mb-0 d-flex justify-content-between">
                                                                <span>
                                                                    {{ App\EventTicket::$members[$eventTicket->type] }}
                                                                </span>
                                                                <span>{{ $bex->base_currency_symbol}} {{$eventTicket->cost}}</span>
                                                            </p>
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                        <a href="{{route('front.event_details', $event->slug)}}" class="btn base-bg text-white">{{__('Book Ticket')}}</a>
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

