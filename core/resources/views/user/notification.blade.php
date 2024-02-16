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
                    <h1>{{__('Event Bookings')}}</h1>
                    <ul class="breadcumb">
                        <li><a href="{{route('user-dashboard')}}">{{__('Dashboard')}}</a></li>
                        <li>{{__('Event Bookings')}}</li>
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
                                <div class="title d-flex justify-content-between">
                                    <h4>{{__('Notifications')}}</h4>
                                    <div>
                                        <a class="btn btn-sm btn-info mr-1" href="{{ route('user-notification-all-read') }}">{{ __('Mark all Read') }}</a>
                                        <a class="btn btn-sm btn-danger" href="{{ route('user-notification-trashed') }}">{{ __('Trashed all') }}</a>
                                    </div>
                                </div>
                                <div class="main-info">
                                    <div class="main-table">
                                    <div class="table-responsiv">
                                        <table id="eventsTable" class="dataTables_wrapper dt-responsive table-striped dt-bootstrap4" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>{{__('Event')}}</th>
                                                    <th width="100">{{ __('Posted') }}</th>
                                                    <th width="60">{{ __('Status') }}</th>
                                                    <th width="80" class="text-center">{{ __('Action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if($notifications)
                                                @foreach ($notifications as $notification)
                                                <tr>
                                                    <td>{{$notification->data['title']}}</td>
                                                    <td>
                                                        {{Carbon\Carbon::parse($notification->created_at)->diffForHumans()}}
                                                    </td>
                                                    <td>
                                                        @if ($notification->read_at == '')
                                                            <a href="{{ route('user-notification-read',$notification->id) }}">
                                                            {{ 'Mark as Read'}}
                                                            </a>
                                                        @else                   
                                                        {{ 'Read'}}
                                                        @endif
                                                    </a>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="{{ route('user-notification-delete',$notification->id) }}" class="btn btn-sm btn-danger">{{__('Delete')}}</a>
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

