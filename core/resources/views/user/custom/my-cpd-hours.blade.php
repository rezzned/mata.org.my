@extends('user.layout')

@section('pagename')
    - {{ __('My CPD Hours') }}
@endsection
@section('styles')
<link rel="stylesheet" href="{{asset('/assets/flatpickr/flatpickr.min.css')}}" />
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
                                        <h4>{{ __('My CPD Points') }}</h4>
                                    </div>
                                    <div class="main-info">
                                        <div class="main-table">
                                            <div class="table-responsive">
                                                <table id="packagesTable" class="table-bordered dataTables_wrapper dt-responsive table-striped dt-bootstrap4 table" style="width:100%">
                                                    <thead>
                                                        <tr>
                                                            <th></th>
                                                            @if (count($required_cpds_data_years))
                                                                @foreach ($required_cpds_data_years as $year)
                                                                    <th class="text-center">{{ $year }}</th>
                                                                @endforeach
                                                            @else
                                                                <th class="text-center">{{ date('Y') }}</th>
                                                            @endif
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <th class="">{{ __('Required CPD Points') }}</th>
                                                            @if (count($required_cpds_data))
                                                                @foreach ($required_cpds_data as $key => $reqCpd)
                                                                    <td class="text-center">
                                                                        @if ($reqCpd['required_cpds']->year >= date('Y'))
                                                                            <a class="btn btn-secondary btn-sm" href="#editModal_{{$reqCpd['required_cpds']->id}}" data-toggle="modal">
                                                                                <span class="btn-label">
                                                                                <i class="fas fa-edit"></i>
                                                                                </span>
                                                                                {{ $reqCpd['required_cpds']->required_points }}
                                                                            </a>
                                                                            <div class="modal fade" id="editModal_{{$reqCpd['required_cpds']->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                                                <div class="modal-content" style="background:#202940;">
                                                                                    <div class="modal-header border-0">
                                                                                        <h5 class="modal-title -50 mb-0">Edit year {{ $reqCpd['required_cpds']->year }} CPD required</h5>
                                                                                        <button type="button" class="close " data-dismiss="modal" aria-label="Close">
                                                                                            <span aria-hidden="true">&times;</span>
                                                                                        </button>
                                                                                    </div>
                                                                                    <form id="ajaxEditForm" class="" action="{{route('user-update-required-cpdpoint')}}" method="POST">
                                                                                        @csrf
                                                                                        <div class="modal-body">

                                                                                            <div class="form-group text-left">
                                                                                            <label for="">Required Point **</label>
                                                                                            <input id="innrequiredpoints" type="required_points" class="form-control" name="required_points" value="{{$reqCpd['required_cpds']->required_points}}" placeholder="Enter required points" style="background-color:#1a2035;color:#fff">
                                                                                            <p id="eerrrequiredpoints" class="mb-0 text-danger em"></p>
                                                                                            </div>

                                                                                        </div>
                                                                                        <div class="modal-footer border-0">
                                                                                        <input type="hidden" name="id" value="{{$reqCpd['required_cpds']->id}}">
                                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                                                                        </div>
                                                                                    </form>
                                                                                </div>
                                                                                </div>
                                                                            </div>
                                                                        @else
                                                                            {{ $reqCpd['required_cpds']->required_points }}
                                                                        @endif
                                                                    </td>
                                                                @endforeach
                                                            @else
                                                                <td class="text-center">
                                                                    <a class="btn btn-secondary btn-sm" href="#editModal_1" data-toggle="modal">
                                                                        <span class="btn-label">
                                                                        <i class="fas fa-edit"></i>
                                                                        </span>
                                                                        {{ 0 }}
                                                                    </a>
                                                                    <div class="modal fade" id="editModal_1" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                            <h5 class="modal-title text-black-50 mb-0">Edit year {{ date('Y') }} CPD required</h5>
                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                <span aria-hidden="true">&times;</span>
                                                                            </button>
                                                                            </div>
                                                                            <form id="ajaxEditForm" class="" action="{{route('user-save-required-cpdpoint')}}" method="POST">
                                                                                @csrf
                                                                                <div class="modal-body">
                                                                                    <p>{{ __('Required CPD point for '. date('Y')) }}</p>
                                                                                    <div class="form-group text-left">
                                                                                    <label for="">Required Point **</label>
                                                                                    <input id="innrequiredpoints" type="required_points" class="form-control" name="required_points" value="" required placeholder="Enter required points">
                                                                                    <p id="eerrrequiredpoints" class="mb-0 text-danger em"></p>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                                    <button type="submit" class="btn btn-primary">Save</button>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            @endif
                                                        </tr>
                                                        <tr>
                                                            <th class="">{{ __('Total MATA CPD Points') }}</th>
                                                            @if (count($required_cpds_data))
                                                                @foreach ($required_cpds_data as $key => $reqCpd)
                                                                    <td class="text-center">{{$reqCpd['internal_cpd']}}</td>
                                                                @endforeach
                                                            @else
                                                                <td class="text-center"></td>
                                                            @endif
                                                        </tr>
                                                        <tr>
                                                            <th class="">{{ __('Total External CPD Points') }}</th>
                                                            @if (count($required_cpds_data))
                                                                @foreach ($required_cpds_data as $key => $reqCpd)
                                                                    <td class="text-center">{{$reqCpd['external_cpd']}}</td>
                                                                @endforeach
                                                            @else
                                                                <td class="text-center"></td>
                                                            @endif
                                                        </tr>
                                                        <tr>
                                                            <th class="">{{ __('Total CPD Points') }}</th>
                                                            @if (count($required_cpds_data))
                                                                @foreach ($required_cpds_data as $key => $reqCpd)
                                                                    <td class="text-center">{{$reqCpd['cpd_total']}}</td>
                                                                @endforeach
                                                            @else
                                                                <td class="text-center"></td>
                                                            @endif
                                                        </tr>
                                                        <tr>
                                                            <th class="">{{ __('CPD Status') }}</th>
                                                            @if (count($required_cpds_data))
                                                                @foreach ($required_cpds_data as $key => $reqCpd)
                                                                    <td class="text-center">{{$reqCpd['cpd_status']}}</td>
                                                                @endforeach
                                                            @else
                                                                <td class="text-center"></td>
                                                            @endif
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="account-info">
                                    <div class="title">
                                        <h4>{{ __('External CPD Points') }}</h4>
                                        <a href="javascript:void(0);" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#reqExtCpdPointModel">Request External CPD Points</a>
                                    </div>
                                    <div class="main-info">
                                        <div class="main-table">
                                            <div class="table-responsive">
                                                <table class="table-bordered table" style="width:100%">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">{{ __('View Details') }}</th>
                                                            <th class="text-center">{{ __('Start Date') }}</th>
                                                            <th class="text-center">{{ __('End Date') }}</th>
                                                            <th class="text-left">{{ __('Event Company') }}</th>
                                                            <th class="text-left">{{ __('Event Title') }}</th>
                                                            <th class="text-center">{{ __('CPD Points') }}</th>
                                                            <th class="text-center">{{ __('Attendence Certificate') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($externalCpdPoints as $extCPD)
                                                            <tr>
                                                                <td>
                                                                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#detailsModal{{$extCPD->id}}">
                                                                        Details
                                                                    </button>
                                                                    <div class="modal fade" id="detailsModal{{$extCPD->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                                                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                                                          <div class="modal-content">
                                                                            <div class="modal-header">
                                                                              <h5 class="modal-title text-dark mb-0" id="exampleModalLongTitle">Details</h5>
                                                                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                <span aria-hidden="true">&times;</span>
                                                                              </button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                              <div class="container">
                                                                                    <p class="mb-1">
                                                                                        <strong style="text-transform: capitalize;">{{ __('Start Date') }}:</strong> {{ dateFormat($extCPD->start_date) }}
                                                                                    </p>
                                                                                    <p class="mb-1">
                                                                                        <strong style="text-transform: capitalize;">{{ __('End Date') }}:</strong> {{ dateFormat($extCPD->end_date) }}
                                                                                    </p>
                                                                                    <p class="mb-1">
                                                                                        <strong style="text-transform: capitalize;">{{ __('Training  Title') }}:</strong> {{ convertUtf8($extCPD->training_title) }}
                                                                                    </p>
                                                                                    <p class="mb-1">
                                                                                        <strong style="text-transform: capitalize;">{{ __('CPD Points') }}:</strong> {{ $extCPD->status == 0 ? "Pending": round($extCPD->amount) }}
                                                                                    </p>
                                                                                    <p class="mb-1">
                                                                                        <strong style="text-transform: capitalize;">{{ __('Organized by') }}:</strong> {{ convertUtf8($extCPD->organized_by) }}
                                                                                    </p>
                                                                                    <form action='{{ route('user-ext-cert-dw', ['cert' => $extCPD->id]) }}' method="POST">
                                                                                        @csrf
                                                                                        <button type='submit' class='btn btn-primary btn-sm'>Certificate</button>
                                                                                    </form>
                                                                              </div>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                            </div>
                                                                          </div>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td class="text-center">{{ dateFormat($extCPD->start_date) }}</td>
                                                                <td class="text-center">{{ dateFormat($extCPD->end_date) }}</td>
                                                                <td class="text-left">{{ convertUtf8($extCPD->organized_by) }}</td>
                                                                <td class="text-left">{{ convertUtf8($extCPD->training_title) }}</td>
                                                                <td class="text-center">{{ $extCPD->status == 0 ? "Pending" : round($extCPD->amount) }}</td>
                                                                <td class="text-center">
                                                                    <form action='{{ route('user-ext-cert-dw', ['cert' => $extCPD->id]) }}' method="POST">@csrf
                                                                        <button type='submit' class='btn btn-primary btn-sm'>Certificate</button>
                                                                    </form>
                                                                </td>
                                                            </tr>
                                                        @endforeach
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
    <!-- Details Modal -->




    <div class="modal fade" tabindex="-1" id="reqExtCpdPointModel">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header" style="border:none">
                    <h5 class="modal-title">Request External CPD Point</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body px-5">
                    <form action="{{ route('user-request-ext-cpd') }}" method="POST" id="cpdPointUpd" onsubmit="return confirm('Are you sure?')" enctype="multipart/form-data">@csrf
                        <div class="mx-4 mb-2 px-3">
                            <label for="">Point Amount</label>
                            <input type="number" name="amount" min="1" placeholder="Point Amount" class="form-control text-center" autocomplete="off" required>
                        </div>
                        <div>
                            <div class="mx-4 mb-2 px-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="">Start Date</label>
                                        <input type="date" name="start_date" class="form-control text-center datepicker_class" placeholder="dd-mm-yyyy" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="">End Date</label>
                                        <input type="date" name="end_date" class="form-control text-center datepicker_class" placeholder="dd-mm-yyyy" required>
                                    </div>
                                </div>
                            </div>
                            <div class="mx-4 mb-2 px-3">
                                <label for="">Training Title</label>
                                <input type="text" name="training_title" placeholder="Training Title" class="form-control text-center" required>
                            </div>
                            <div class="mx-4 mb-2 px-3">
                                <label for="">Organized By</label>
                                <input type="text" name="organized_by" placeholder="Organized By" class="form-control text-center" required>
                            </div>
                            <div class="mx-4 mb-2 px-3 form-group">
                                <label for="">Attendance Certificate</label>
                                <input type="file" name="certificate" class="form-control-file text-center" required>
                            </div>
                            <div class="mx-4 mb-2 px-3">
                                <label for="">Remarks/Note</label>
                                <textarea name="details" placeholder="Details" class="form-control"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-center" style="border:none">
                    <button type="submit" class="btn btn-primary ml-3 px-5 submit_btn_load" form="cpdPointUpd">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <!--    footer section start   -->
@endsection
@section('scripts')
<script>
    // $(document).ready(function() {
    //     $('#packagesTable').DataTable({
    //         responsive: true,
    //         ordering: false
    //     });
    // });
</script>
<script src="{{asset('/assets/flatpickr/flatpickr.min.js')}}"></script>
<script>
    flatpickr('.datepicker_class', {
        dateFormat: 'd-m-Y'
    })
</script>
@endsection
