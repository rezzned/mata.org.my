@extends('admin.layout')

@section('content')
    <div class="page-header">
        <h4 class="page-title">
            Certificate
        </h4>
        <ul class="breadcrumbs">
            <li class="nav-home">
                <a href="{{ route('admin.dashboard') }}">
                    <i class="flaticon-home"></i>
                </a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">Events Management</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">Certificate</a>
            </li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header p-1">
                    <div class="row">
                        <div class="col-lg-10">
                            <form action="{{ url()->full() }}" class="form-inline">
                                <div class="form-group">
                                    <label for="">From</label>
                                    <input class="form-control datepicker" type="text" name="from_date"
                                        placeholder="From"
                                        value="{{ request()->input('from_date') ? request()->input('from_date') : '' }}"
                                        autocomplete="off" />
                                </div>

                                <div class="form-group">
                                    <label for="">To</label>
                                    <input class="form-control datepicker ml-1" type="text" name="to_date"
                                        placeholder="To"
                                        value="{{ request()->input('to_date') ? request()->input('to_date') : '' }}"
                                        autocomplete="off" />
                                </div>

                                <div class="form-group">
                                    <label for="training">Training</label>
                                    <select name="event" class="form-control ml-1">
                                        <option value="" selected>All</option>
                                        @foreach ($events as $event)
                                            <option value="{{ $event->id }}"
                                                {{ $event->id == request('event') ? 'selected' : '' }}>{{ $event->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-sm ml-1">Search</button>
                                </div>

                                @if (request()->query('event'))
                                    <div class="form-group">
                                        <a href="{{ route('admin.event.attendance.send-all', ['id' => request()->query('event')]) }}"
                                            class="btn btn-primary btn-sm ml-1">Send All</a>
                                    </div>
                                @endif
                            </form>
                            {{-- <a href="javascript:void(0)" class="btn btn-secondary btn-sm" onclick="location.href='{{url()->current()}}';">Reset</a> --}}

                        </div>
                        <div class="col-lg-2">
                            <form action="{{ route('admin.event.export') }}" class="form-inline justify-content-end">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-success btn-sm ml-1"
                                        title="CSV Format">Export</button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            @if (count($bookings) > 0)
                                <div class="table-responsive">
                                    <table class="table-striped mt-3 table">
                                        <thead>
                                            <tr>
                                                <th scope="col" nowrap>Ticket ID</th>
                                                <th scope="col" nowrap>Name</th>
                                                <th scope="col" nowrap>Email</th>
                                                <th scope="col" nowrap>Phone</th>
                                                <th scope="col" nowrap>Event</th>
                                                <th scope="col" nowrap>Amount</th>
                                                <th scope="col" nowrap>Quantity</th>
                                                <th scope="col" nowrap>Attendance</th>
                                                <th scope="col" nowrap>Payment Status</th>
                                                <th scope="col" nowrap>Date</th>
                                                <th scope="col" nowrap>Action</th>
                                                <th scope="col" nowrap></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($bookings as $key => $booking)
                                                <tr>
                                                    <td nowrap>#{{ $booking->transaction_id }}</td>
                                                    <td nowrap>{{ $booking->name }}</td>
                                                    <td nowrap>{{ $booking->email }}</td>
                                                    <td nowrap>{{ $booking->phone }}</td>
                                                    <td nowrap>
                                                        {{ convertUtf8(Str::limit($booking->event->title ?? '', 25)) }}
                                                    </td>
                                                    <td>{{ currency_format($booking->amount) }}</td>
                                                    <td>{{ $booking->quantity }}</td>
                                                    <td>
                                                        @if (!$booking->attendance)
                                                            <form action="{{ route('admin.event.attendance') }}"
                                                                method="post" id="statusForm{{ $booking->id }}">
                                                                @csrf
                                                                <select name="attendance" class="form-control attend_status"
                                                                    data-id="{{ $booking->id }}">
                                                                    <option value="">{{ __('Select') }}</option>
                                                                    <option
                                                                        {{ $booking->attendance && $booking->attendance == 'attend' ? 'selected' : '' }}
                                                                        value="attend">{{ __('Attend') }}</option>
                                                                    <option
                                                                        {{ $booking->attendance && $booking->attendance == 'not_attend' ? 'selected' : '' }}
                                                                        value="not_attend">{{ __('Not Attend') }}</option>
                                                                </select>
                                                                <input type="hidden" name="id"
                                                                    value="{{ $booking->id }}">
                                                                <!-- Modal -->
                                                                <div class="modal fade"
                                                                    id="rejected_modal_{{ $booking->id }}" tabindex="-1"
                                                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                                    <div class="modal-dialog">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title"
                                                                                    id="exampleModalLabel">Booking rejected
                                                                                    and refund note</h5>
                                                                                <button type="button" class="close"
                                                                                    data-dismiss="modal" aria-label="Close">
                                                                                    <span aria-hidden="true">&times;</span>
                                                                                </button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <textarea name="refund_note" class="form-control" required rows="5"></textarea>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button type="button"
                                                                                    class="btn btn-secondary"
                                                                                    data-dismiss="modal">Close</button>
                                                                                <button type="submit"
                                                                                    class="btn btn-primary">Submit</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        @else
                                                            <div class="btn-group">
                                                                <button type="submit" class="btn btn-sm btn-primary"
                                                                    form="cert_download{{ $booking->id }}">Download
                                                                    Certificate</button>
                                                                <button type="submit" class="btn btn-sm btn-danger"
                                                                    onclick="return confirm('Are you sure?')"
                                                                    form="cert_regenerate{{ $booking->id }}">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        class="icon icon-tabler icon-tabler-rotate-clockwise"
                                                                        width="16" height="16" viewBox="0 0 24 24"
                                                                        stroke-width="2" stroke="currentColor"
                                                                        fill="none" stroke-linecap="round"
                                                                        stroke-linejoin="round">
                                                                        <path stroke="none" d="M0 0h24v24H0z"
                                                                            fill="none"></path>
                                                                        <path d="M4.05 11a8 8 0 1 1 .5 4m-.5 5v-5h5"></path>
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                            <form
                                                                action="{{ route('admin.event.certificate.download', $booking->id) }}"
                                                                method="post" id="cert_download{{ $booking->id }}">
                                                                @csrf </form>
                                                            <form
                                                                action="{{ route('admin.event.certificate.regenerate', $booking->id) }}"
                                                                method="post" id="cert_regenerate{{ $booking->id }}">
                                                                @csrf </form>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (strtolower($booking->status) == 'pending')
                                                            <span class="badge badge-warning">Pending</span>
                                                        @elseif (strtolower($booking->status) == 'success')
                                                            <span class="badge badge-success">Success</span>
                                                        @elseif (strtolower($booking->status) == 'rejected')
                                                            <span class="badge badge-danger">Rejected</span>
                                                        @endif
                                                    </td>
                                                    @php($gatewayType = Str::of($booking->transaction_details)->replace(['\"', '"', "\'", "'"], ''))
                                                    <td nowrap>
                                                        {{ dateFormat($booking->created_at, 'd-M-Y') }}
                                                    </td>
                                                    <td nowrap>
                                                        @if ($booking->user_id)
                                                            <div class="dropdown">
                                                                <button class="btn btn-info btn-sm dropdown-toggle"
                                                                    type="button" id="dropdownMenuButton"
                                                                    data-toggle="dropdown" aria-haspopup="true"
                                                                    aria-expanded="false">Actions</button>
                                                                <div class="dropdown-menu"
                                                                    aria-labelledby="dropdownMenuButton">
                                                                    <a class="dropdown-item"
                                                                        href="{{ route('register.user-member.edit', $booking->user_id) }}">Update</a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td nowrap>
                                                        <form id="attendanceForm"
                                                            action="{{ route('admin.event.attendance') }}"
                                                            method="post">
                                                            @csrf
                                                            <input type="hidden" name="id"
                                                                value="{{ $booking->id }}">
                                                            <input type="hidden" name="attendance" value="attend">
                                                            <input type="hidden" name="refund_note" value="">
                                                            <div class="form-group">
                                                                <button type="submit"
                                                                    class="btn btn-primary btn-sm ml-1">Send</button>
                                                            </div>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="d-flex justify-content-center align-items-center" style="min-height:300px">
                                    <h3>Please select dates to see the report</h3>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                @if (!empty($bookings))
                    <div class="card-footer">
                        <div class="row">
                            <div class="d-inline-block mx-auto">
                                {{ $bookings->withQueryString()->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

@endsection

@push('footer-js')
    <script>
        $(document.body).on('change', '.attend_status', function() {
            const id = $(this).data('id');
            const status = $(this).val();

            if (status == 'not_attend') {
                $('#rejected_modal_' + id).modal('show');
            } else {
                $('#statusForm' + id).submit();
            }
        })

        document.getElementById('attendanceForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent the default form submission

            // Perform any additional logic or validation here

            // Optionally, you can use AJAX to submit the form asynchronously
            // Example using jQuery AJAX:
            $.ajax({
                type: 'POST',
                url: this.action,
                data: $(this).serialize(),
                success: function(response) {
                    // Handle the success response
                    location.reload(); // Refresh the page
                },
                error: function(xhr, status, error) {
                    // Handle the error response
                }
            });
        });

        $(function() {
            $(`[name="payment_method"] option`).on('click', function() {
                $(`[name="pmt"]`).val($(this).data('pmtype'));
            });
        });
    </script>
@endpush
