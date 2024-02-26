@extends('admin.layout')

@section('content')
    <style>
        .curp {
            cursor: pointer
        }
        textarea.form-control {
            height: 110px !important;
            resize: vertical;
        }

         #requiredCpdPointModal input::-webkit-outer-spin-button,
         #requiredCpdPointModal input::-webkit-inner-spin-button {
             display: none;
         }
    </style>
    <button class="btn btn-sm btn-secondary float-right" data-toggle="modal" data-target="#requiredCpdPointModal">Default Required CPD Point</button>
    <div class="page-header">
        <h4 class="page-title">Membership Tracker</h4>
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
                <a href="#">Membership Tracker</a>
            </li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li class="text-dark">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card-title">
                                Registered Users
                            </div>
                        </div>
                        <div class="col-6 mt-lg-0 mt-2">
                            <button class="btn btn-danger btn-sm d-none bulk-delete float-right ml-2 mt-1" data-href="{{ route('register.user.bulk.delete') }}"><i class="flaticon-interface-5"></i> Delete</button>
                            <form action="{{ url()->full() }}" class="float-right">
                                <input type="text" name="term" class="form-control" value="{{ request()->input('term') }}" placeholder="Search by Username / Email" style="min-width: 250px;">
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            @if (count($users) == 0)
                                <h3 class="text-center">NO USER FOUND</h3>
                            @else
                                <div class="table-responsive">
                                    <table class="table-striped mt-3 table">
                                        <thead>
                                            <tr>
                                                <th scope="col">
                                                    <input type="checkbox" class="bulk-check" data-val="all">
                                                </th>
                                                <th scope="col">Name</th>
                                                <th scope="col">Username</th>
                                                <th scope="col">Personal Number</th>
                                                <th scope="col" class="text-center">CPD Required</th>
                                                <th scope="col" class="text-center">CPD Point</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($users as $key => $user)
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" class="bulk-check" data-val="{{ $user->id }}">
                                                    </td>
                                                    <td>{{ convertUtf8($user->fname) }} {{ convertUtf8($user->lname) }}</td>
                                                    <td>{{ convertUtf8($user->username) }}</td>
                                                    <td>{{ $user->personal_phone }}</td>
                                                    <td class="text-center">
                                                        <a href="javascript:void(0)" class="" data-userid="{{ $user->id }}" onclick="xrenderCpdReqired({{ $user->id }})"
                                                            data-toggle="modal" data-target="#cpdRequiredPointModel">{{ $user->cpd_required->sortByDesc('year')->first()->required_points ?? '0' }}</a>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="px-2">{{ round($user->cpd_point) ?? '0' }}</span>
                                                    </td>
                                                    <td>
                                                        <div class="text-center">
                                                            <button class="btn btn-sm my-1 py-1" data-toggle="modal" data-target="#cpdPointModel{{ $user->id }}">
                                                                <i class="fas fa-arrow-up"></i> Update Point
                                                            </button>
                                                            <button class="btn btn-sm my-1 py-1" data-toggle="modal" data-target="#cpdExternalPointModal"
                                                                data-open="#btnCpdExternalPointModal" data-userid="{{ $user->id }}">
                                                                <i class="fas fa-list"></i> External Points
                                                            </button>
                                                        </div>
                                                        <div class="modal fade" tabindex="-1" id="cpdPointModel{{ $user->id }}">
                                                            <div class="modal-dialog modal-dialog-scrollable">
                                                                <div class="modal-content" x-data="{ptype: 'internal'}">
                                                                    <div class="modal-header border-none">
                                                                        <h5 class="modal-title">Update CPD Point</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body px-5">
                                                                        <form action="{{ route('admin.update.cpd-point') }}" method="POST" id="cpdPointUpd{{ $user->id }}" onsubmit="return confirm('Are you sure?')" :enctype="ptype=='external' ? 'multipart/form-data' : false"> @csrf
                                                                            <input type="hidden" name="id" value="{{ $user->id }}">
                                                                            <div class="mx-4 mb-2 px-3">
                                                                                <label for="">Internal/External Point</label>
                                                                                <select name="cpdtype" id="cpdType{{ $user->id }}" class="form-control text-center" x-model="ptype">
                                                                                    <option value="internal">MATA Internal</option>
                                                                                    <option value="external">External</option>
                                                                                </select>
                                                                            </div>
                                                                            <div class="mx-4 mb-2 px-3">
                                                                                <label for="">Point Amount</label>
                                                                                <input type="number" name="amount" min="1" placeholder="Point Amount" class="form-control text-center" autocomplete="off" required>
                                                                            </div>
                                                                            <div x-show="ptype=='external'">
                                                                                <div class="mx-4 mb-2 px-3">
                                                                                    <div class="row">
                                                                                        <div class="col-md-6">
                                                                                            <label for="">Start Date</label>
                                                                                            <input type="date" name="start_date" class="form-control text-center" :disabled="ptype=='internal'" :required="ptype=='external'">
                                                                                        </div>
                                                                                        <div class="col-md-6">
                                                                                            <label for="">End Date</label>
                                                                                            <input type="date" name="end_date" class="form-control text-center" :disabled="ptype=='internal'" :required="ptype=='external'">
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="mx-4 mb-2 px-3">
                                                                                    <label for="">Training Title</label>
                                                                                    <input type="text" name="training_title" placeholder="Training Title" class="form-control text-center" :disabled="ptype=='internal'" :required="ptype=='external'">
                                                                                </div>
                                                                                <div class="mx-4 mb-2 px-3">
                                                                                    <label for="">Organized By</label>
                                                                                    <input type="text" name="organized_by" placeholder="Organized By" class="form-control text-center" :disabled="ptype=='internal'" :required="ptype=='external'">
                                                                                </div>
                                                                                <div class="mx-4 mb-2 px-3 form-group">
                                                                                    <label for="">Attendance Certificate</label>
                                                                                    <input type="file" name="certificate" class="form-control-file text-center" :disabled="ptype=='internal'" :required="ptype=='external'">
                                                                                </div>
                                                                                <div class="mx-4 mb-2 px-3">
                                                                                    <label for="">Remarks/Note</label>
                                                                                    <textarea name="details" placeholder="Details" class="form-control" :disabled="ptype=='internal'"></textarea>
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                    <div class="modal-footer justify-content-center border-none">
                                                                        <button type="submit" class="btn btn-secondary mr-3 px-5" form="cpdPointUpd{{ $user->id }}" value="-" name="type" x-show="ptype=='internal'">Subtract</button>
                                                                        <button type="submit" class="btn btn-primary ml-3 px-5" form="cpdPointUpd{{ $user->id }}" value="+" name="type">Add</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="d-inline-block mx-auto">
                            {{ $users->appends(['term' => request()->input('term')])->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="cpdRequiredPointModel">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Required CPD History</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body px-3">
                    <div class="text-center">
                        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#cpdReqEditModel" data-dismiss="modal" id="btnShowCpdAdd">
                            Add New
                        </button>
                    </div>
                    <table class="table-sm table">
                        <thead>
                            <tr>
                                <th>Year</th>
                                <th>Required</th>
                                <th>Added At</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="cpdHistoryTable">
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer justify-content-center border-none">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal" aria-label="Close">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="cpdReqEditModel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-none">
                    <h5 class="modal-title">Add/Edit Required CPD</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body px-3">
                    <form action="{{ route('admin.add.req-cpd-point') }}" id="addReqForm" class="mx-5 px-5" method="POST">@csrf
                        <input type="hidden" value="" name="id" id="id">
                        <input type="hidden" value="" name="user_id" id="userid">
                        <div class="mb-2">
                            <label for="">Year</label>
                            <input type="number" min="1000" value="" name="year" id="year" class="form-control text-center">
                        </div>
                        <div class="mb-2">
                            <label for="">Required Points</label>
                            <input type="number" min="0" step="any" value="" name="required_points" id="required_points" class="form-control text-center">
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-center border-none">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Close" data-toggle="modal" data-target="#cpdRequiredPointModel">
                        Close
                    </button>
                    <button type="submit" class="btn btn-primary" data-dismiss="modal" aria-label="Close" onclick="addReqFormSubmit()">
                        Submit
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="cpdExternalPointModal">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">External CPD Points</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body px-3">
                    <div class="table-responsive">
                        <table class="table-sm table">
                            <thead>
                            <tr>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Training Title</th>
                                <th>CPD Points</th>
                                <th>Organized by</th>
                                <th>Attendance Certificate</th>
                                <th>Remarks/Note</th>
                            </tr>
                            </thead>
                            <tbody id="cpdExternalHistory">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer justify-content-center border-none">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal" aria-label="Close">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="requiredCpdPointModal">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header" style="border: none !important">
                    <h5 class="modal-title">Default Required CPD Point</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body px-5 pb-0">
                    <form action="{{route('admin.update.default-required-cpd-point')}}" method="POST">
                        @csrf
                        <input type="number"
                               class="form-control text-center"
                               id="requiredCpdPoint"
                               name="def_required_cpd_point"
                               placeholder="Required CPD Point"
                               aria-label="Required CPD Point"
                               value="{{$bs->def_required_cpd_point}}"
                               required/>
                        <div class="text-center mt-3">
                            <button type="submit" class="btn btn-primary btn-block">Save</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-center border-none">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal" aria-label="Close">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="cpdReqEditModel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-none">
                    <h5 class="modal-title">Add/Edit Required CPD</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body px-3">
                    <form action="{{ route('admin.add.req-cpd-point') }}" id="addReqForm" class="mx-5 px-5" method="POST">@csrf
                        <input type="hidden" value="" name="id" id="id">
                        <input type="hidden" value="" name="user_id" id="userid">
                        <div class="mb-2">
                            <label for="">Year</label>
                            <input type="number" min="1000" value="" name="year" id="year" class="form-control text-center">
                        </div>
                        <div class="mb-2">
                            <label for="">Required Points</label>
                            <input type="number" min="0" step="any" value="" name="required_points" id="required_points" class="form-control text-center">
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-center border-none">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Close" data-toggle="modal" data-target="#cpdRequiredPointModel">
                        Close
                    </button>
                    <button type="submit" class="btn btn-primary" data-dismiss="modal" aria-label="Close" onclick="addReqFormSubmit()">
                        Submit
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('footer-js')
        <script src="https://unpkg.com/alpinejs@3.10.2/dist/cdn.min.js" defer></script>
        <script>
            $(function () {
                $('[data-open="#btnCpdExternalPointModal"]').on('click', function () {
                    $('#cpdExternalPointModal').modal('show');
                    $('#cpdExternalHistory').html('<tr><td colspan="7" class="text-center"><i class="fas fa-spinner fa-spin"></i></td></tr>');
                    $.ajax({
                        url: "{{ route('admin.cpd.external.point') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            user_id: $(this).data('userid')
                        },
                        success: function (data) {
                            $('#cpdExternalHistory').html(data);
                        }
                    });
                });
            });
            function addReqFormSubmit() {
                if (confirm('Are you sure?')) {
                    $('#addReqForm').submit();
                    return;
                }
                return false;
            }

            function showReqCpdForm() {
                let uid = $(this).data('userid');
                $('#addReqForm input#id').val('');
                $('#addReqForm input#userid').val(uid);
                $('#addReqForm div input#year').val('');
                $('#addReqForm div input#required_points').val('');
            }

            function xrenderCpdReqired(xid) {
                $("#btnShowCpdAdd").attr('data-userid', xid);
                $('#btnShowCpdAdd').on('click', showReqCpdForm);
                // const xobject = $(`[data-userid=${xid}]`).data('cpddata');
                $('#cpdHistoryTable').html('Loading...');
                // console.log(xobject)
                $.ajax({
                    type: 'get',
                    url: '{{ route('admin.membership-tracker') }}',
                    data: { user_id: xid },
                    success: function(data) {
                        $('#cpdHistoryTable').html('');
                        if (data.length > 0) {
                            data.map(function(obj) {
                                console.log(obj)
                                let date = new Date(obj.created_at);
                                let dd = date.getDate();
                                let mm = date.getMonth();
                                let yy = date.getFullYear();
                                $('#cpdHistoryTable').append(`<tr>
                            <td>${obj.year}</td>
                            <td>${obj.required_points}</td>
                            <td>${dd}/${mm}/${yy}</td>
                            <td><a class="text-light" href="javascript:void(0)"
                                data-toggle="modal" data-target="#cpdReqEditModel" data-dismiss="modal"
                                data-userid="${xid}" data-id="${obj.id}" data-year="${obj.year}"
                                data-required_points="${obj.required_points}"
                                data-fillform="btnCpdReqEdit">
                            <i class="fa fa-edit"></a></td>
                        </tr>`);
                            });
                            $('[data-fillform="btnCpdReqEdit"]').on('click', function() {
                                $('#addReqForm input#id').val($(this).data('id'));
                                $('#addReqForm input#userid').val($(this).data('userid'));
                                $('#addReqForm div input#year').val($(this).data('year'));
                                $('#addReqForm div input#required_points').val($(this).data('required_points'));
                            });
                        }
                    }
                })

            }
        </script>
    @endpush
@endsection
