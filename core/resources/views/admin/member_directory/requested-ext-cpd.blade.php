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
    </style>
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
                            @if (count($cpd_list) == 0)
                                <h3 class="text-center">NO REQUEST FOUND</h3>
                            @else
                                <div class="table-responsive">
                                    <table class="table-striped mt-3 table">
                                        <thead>
                                            <tr>
                                                <th scope="col">Name</th>
                                                <th scope="col">Username</th>
                                                <th scope="col" class="text-center">Start Date</th>
                                                <th scope="col" class="text-center">End Date</th>
                                                <th scope="col">Training Title</th>
                                                <th scope="col" class="text-center">CPD Point</th>
                                                <th scope="col" class="text-left">Organized By</th>
                                                <th scope="col" class="text-center">Certificate</th>
                                                <th scope="col">Remarks</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($cpd_list as $key => $cp)
                                                <tr>
                                                    <td>{{ $cp->user->full_name }}</td>
                                                    <td>{{ $cp->user->username }}</td>
                                                    <td>{{ dateFormat($cp->start_date) }}</td>
                                                    <td>{{ dateFormat($cp->end_date) }}</td>
                                                    <td>{{ $cp->training_title }}</td>
                                                    <td class="text-center">{{ $cp->amount }}</td>
                                                    <td>{{ $cp->organized_by }}</td>
                                                    <td><form action='{{ route('admin.cpd.external.cert', ['cert' => $cp->id]) }}' method="POST">{{ csrf_field() }}
                                                    <button type='submit' class='btn btn-primary btn-sm'>Certificate</button>
                                                    </form></td>
                                                    <td><span title="{{ $cp->details }}">{{ Str::limit($cp->details, 25) }}</span></td>
                                                    <td>
                                                        <form action="{{ route('admin.res-req-cpd-ext') }}" method='POST' onsubmit="return confirm('Are you sure?')"> @csrf
                                                            <input type="hidden" name="id" value="{{ $cp->id }}">
                                                            <select name="action" class="form-control" onchange="this.form.submit()" style="width: 100px">
                                                                <option value="">@lang('Select')</option>
                                                                <option value="accept">@lang('Accept')</option>
                                                                <option value="reject">@lang('Reject')</option>
                                                            </select>
                                                        </form>
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
            </div>
        </div>
    </div>
@endsection
