@extends('user.layout')

@section('pagename')
    - {{ __('Orders') }}
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
                                        <h4>{{ __('Attendance History') }}</h4>
                                    </div>
                                    <div class="main-info">
                                        <div class="main-table">
                                            <div class="table-responsiv">
                                                <table id="eventsTable"
                                                    class="dataTables_wrapper dt-responsive table-striped dt-bootstrap4"
                                                    style="width:100%">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('Event') }}</th>
                                                            <th>{{ __('Organizer') }}</th>
                                                            <th class="text-center">{{ __('Date') }}</th>
                                                            <th class="text-center">{{ __('Time') }}</th>
                                                            <th>{{ __('Status') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if ($events)
                                                            @foreach ($events as $event)
                                                                <tr>
                                                                    <td>{{ strlen($event->event->title) > 30 ? mb_substr($event->event->title, 0, 30, 'utf-8') . '...' : $event->event->title }}
                                                                    </td>
                                                                    <td>{{ $event->event->organizer }}</td>
                                                                    <td class="text-center">{{ $event->event->date }}</td>
                                                                    <td class="text-center">{{ $event->event->time }}</td>
                                                                    <td>
                                                                        @if ($event->attendances)
                                                                            <span class="badge badge-info">Attend</span>
                                                                        @else
                                                                            {{-- {{ dd($event->attendance) }} --}}
                                                                            <a href="{{ route('user-attendance.save', $event->id) }}"
                                                                                class="btn btn-sm btn-success">{{ __('Attend') }}</a>
                                                                        @endif
                                                                        {{-- @if ($event->attendance)
                                                                            <p>Attendance: {{ $event->attendance->name }}
                                                                            </p>
                                                                        @elseif (!is_null($event->attendance))
                                                                            <p>Attendance data is not available for this
                                                                                event.</p>
                                                                        @else
                                                                            <p>Attendance relationship is not loaded.</p>
                                                                        @endif --}}
                                                                        {{-- @switch($event->status)
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
                                                        @endswitch --}}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @else
                                                            <tr class="text-center">
                                                                <td colspan="4">
                                                                    {{ __('No Booking Found') }}
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
