@extends('user.layout')

@section('pagename')
    - {{ __('My CPD Hours') }}
@endsection

@section('content')
    <style>
        button.btn-none {
            background: transparent !important;
            padding: 5px 10px !important;
        }
    </style>
    <!--   hero area start   -->
    {{-- <div class="breadcrumb-area services service-bg" style="background-image: url('{{ asset('assets/front/img/' . $bs->breadcrumb) }}');background-size:cover;">
        <div class="container">
            <div class="breadcrumb-txt">
                <div class="row">
                    <div class="col-xl-7 col-lg-8 col-sm-10">
                        <h1>{{ __('My CPD Points') }}</h1>
                        <ul class="breadcumb">
                            <li><a href="{{ route('user-dashboard') }}">{{ __('Dashboard') }}</a></li>
                            <li>{{ __('My CPD Points') }}</li>
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
                                        <h4>{{ __('My CPD Points') }}</h4>
                                    </div>
                                    <div class="main-info">
                                        <div class="main-table">
                                            <div class="table-responsive">
                                                <table id="packagesTable" class="table-bordered dataTables_wrapper dt-responsive table-striped dt-bootstrap4 table" style="width:100%">
                                                    <thead>
                                                        <tr>
                                                            <th></th>
                                                            @foreach ($years as $year)
                                                                <th class="text-center">{{ $year }}</th>
                                                            @endforeach
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <th class="">{{ __('Required CPD Points') }}</th>
                                                            @foreach ($req_cpd as $key => $reqCpd)
                                                                <td class="text-center">
                                                                    @if ($req_cpd_data[$key])
                                                                    <a class="btn btn-secondary btn-sm" href="#editModal_{{$req_cpd_data[$key]->id}}" data-toggle="modal">
                                                                        <span class="btn-label">
                                                                          <i class="fas fa-edit"></i>
                                                                        </span>
                                                                        {{ $reqCpd }}
                                                                      </a>
                                                                      <div class="modal fade" id="editModal_{{$req_cpd_data[$key]->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                                          <div class="modal-content">
                                                                            <div class="modal-header">
                                                                              <h5 class="modal-title text-black-50 mb-0">Edit this year CPD required</h5>
                                                                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                <span aria-hidden="true">&times;</span>
                                                                              </button>
                                                                            </div>
                                                                            <form id="ajaxEditForm" class="" action="{{route('user-update-required-cpdpoint')}}" method="POST">
                                                                                @csrf
                                                                                <div class="modal-body">
                                                                                
                                                                                    <div class="form-group text-left">
                                                                                    <label for="">Required Point **</label>
                                                                                    <input id="innrequiredpoints" type="required_points" class="form-control" name="required_points" value="{{$req_cpd_data[$key]->required_points}}" placeholder="Enter required points">
                                                                                    <p id="eerrrequiredpoints" class="mb-0 text-danger em"></p>
                                                                                    </div>
                                                                                    <div class="form-group text-left">
                                                                                    <label for="">Year **</label>
                                                                                    <input id="inyear" type="name" class="form-control" name="year" value="{{$req_cpd_data[$key]->year}}" placeholder="Enter year">
                                                                                    <p id="eerryear" class="mb-0 text-danger em"></p>
                                                                                    </div>
                                                                                
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <input type="hidden" name="id" value="{{$req_cpd_data[$key]->id}}">
                                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                                                                </div>
                                                                            </form>
                                                                          </div>
                                                                        </div>
                                                                      </div>
                                                                    @else 
                                                                      {{ $reqCpd }}
                                                                    @endif
                                                                </td>
                                                            @endforeach
                                                        </tr>
                                                        <tr>
                                                            <th class="">{{ __('Total MATA CPD Points') }}</th>
                                                            @foreach ($internal_cpd as $intCpd)
                                                                <td class="text-center">{{ $intCpd }}</td>
                                                            @endforeach
                                                        </tr>
                                                        <tr>
                                                            <th class="">{{ __('Total External CPD Points') }}</th>
                                                            @foreach ($external_cpd as $extCpd)
                                                                <td class="text-center">{{ $extCpd }}</td>
                                                            @endforeach
                                                        </tr>
                                                        <tr>
                                                            <th class="">{{ __('Total CPD Points') }}</th>
                                                            @foreach ($cpd_total as $totalCpd)
                                                                <td class="text-center">{{ $totalCpd }}</td>
                                                            @endforeach
                                                        </tr>
                                                        <tr>
                                                            <th class="">{{ __('CPD Status') }}</th>
                                                            @foreach ($cpd_status as $cpdStatus)
                                                                <td class="text-center">{{ $cpdStatus }}</td>
                                                            @endforeach
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
                                        <a href="javascript:void(0);" data-toggle="modal" data-target="#reqExtCpdPointModel">Request External CPD Points</a>
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
                                                                              <h5 class="modal-title" id="exampleModalLongTitle">Details</h5>
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
                                                                                        <strong style="text-transform: capitalize;">{{ __('CPD Points') }}:</strong> {{ $extCPD->status == 0 ? "Pending" : round($extCPD->amount) }}
                                                                                    </p>
                                                                                    <p class="mb-1">
                                                                                        <strong style="text-transform: capitalize;">{{ __('Organized by') }}:</strong> {{ convertUtf8($extCPD->organized_by) }}
                                                                                    </p>
                                                                                    <form action='{{ route('user-ext-cert-dw', ['cert' => $extCPD->id]) }}' method="POST">@csrf
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
                                        <input type="date" name="start_date" class="form-control text-center" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="">End Date</label>
                                        <input type="date" name="end_date" class="form-control text-center" required>
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
                    <button type="submit" class="btn btn-primary ml-3 px-5" form="cpdPointUpd">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <!--    footer section start   -->
@endsection
{{-- @section('scripts')
<script>
    $(document).ready(function() {
        $('#packagesTable').DataTable({
            responsive: true,
            ordering: false
        });
    });
</script>
@endsection --}}
