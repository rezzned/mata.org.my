@extends('admin.layout')

@section('content')
    <style>
        .curp {
            cursor: pointer
        }
    </style>
    <div class="page-header justify-content-between">
        <div class="d-flex align-items-center">
            <h4 class="page-title">
                Member Directory
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
                    <a href="#">@lang('Member Directory')</a>
                </li>
            </ul>
        </div>
        <a href="{{ route('admin.register.user.add') }}" class="btn btn-primary btn-sm">@lang('Add Users')</a>
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
                                Member Directory
                            </div>
                        </div>
                        <div class="col-6 mt-lg-0 mt-2">
                            <button class="btn btn-danger btn-sm d-none bulk-delete float-right ml-2 mt-1" data-href="{{ route('register.user.bulk.delete') }}"><i class="flaticon-interface-5"></i> Delete</button>
                            <form action="{{ url()->full() }}" class="form-inline float-right">
                                <select name="order" class="form-control mr-1" onchange="this.form.submit()">
                                    <option value="">{{ __('Sort') }}</option>
                                    <option {{ request()->input('order') == 'newest' ? 'selected' : '' }} value="newest">Newest</option>
                                    <option {{ request()->input('order') == 'oldest' ? 'selected' : '' }} value="oldest">Oldest</option>
                                    <option {{ request()->input('order') == 'a_z' ? 'selected' : '' }} value="a_z">A-Z</option>
                                    <option {{ request()->input('order') == 'z_a' ? 'selected' : '' }} value="z_a">Z-A</option>
                                </select>
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
                                                {{-- <th scope="col">
                                                    <input type="checkbox" class="bulk-check" data-val="all">
                                                </th> --}}
                                                <th scope="col" nowrap>Name</th>
                                                <th scope="col" nowrap>Username</th>
                                                <th scope="col" nowrap>Date of Birth</th>
                                                <th scope="col" nowrap>Age</th>
                                                <th scope="col" nowrap>Gender</th>
                                                <th scope="col" nowrap>Nation</th>
                                                <th scope="col" nowrap>Email</th>
                                                <th scope="col" nowrap>Contact Number</th>
                                                <th scope="col" nowrap>Company Fax</th>
                                                <th scope="col" nowrap>Address</th>
                                                <th scope="col" nowrap>Membership</th>
                                                <th scope="col" nowrap>CPD Point</th>
                                                <th scope="col" nowrap>Email Status</th>
                                                <th scope="col" nowrap>Account Status</th>
                                                <td scope="col" nowrap>Action</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($users as $key => $user)
                                                <tr>
                                                    {{-- <td>
                                                        <input type="checkbox" class="bulk-check" data-val="{{ $user->id }}">
                                                    </td> --}}
                                                    <td nowrap>{{ convertUtf8($user->full_name) }}</td>
                                                    <td nowrap>{{ convertUtf8($user->username) }}</td>
                                                    <td nowrap>{{ dateFormat($user->date_of_birth, 'd-M-Y') }}</td>
                                                    <td nowrap>{{ $user->date_of_birth ? Carbon\Carbon::parse($user->date_of_birth)->age . ' years': '-' }}</td>
                                                    <td nowrap>{{ ucfirst(convertUtf8($user->gender)) }}</td>
                                                    <td nowrap>{{ ucfirst(convertUtf8($user->nation)) }}</td>
                                                    <td nowrap>{{ convertUtf8($user->email) }}</td>
                                                    <td nowrap>{{ $user->personal_phone }}</td>
                                                    <td nowrap>{{ $user->company_fax }}</td>
                                                    <td nowrap>{{ convertUtf8($user->address) }}</td>
                                                    <td nowrap>
                                                        @if ($user->subscription && today()->lt($user->subscription->expire_date))
                                                            {{ convertUtf8($user->subscription->current_package->title ?? 'No membership') }}
                                                        @else
                                                            <button class="curp rounded border-0" data-toggle="modal" data-target="#membershipModel{{ $user->id }}">
                                                                <svg height="15" width="15" viewBox="0 0 512 512">
                                                                    <path d="M234.5 5.7c13.9-5 29.1-5 43.1 0l192 68.6C495 83.4 512 107.5 512 134.6V377.4c0 27-17 51.2-42.5 60.3l-192 68.6c-13.9 5-29.1 5-43.1 0l-192-68.6C17 428.6 0 404.5 0 377.4V134.6c0-27 17-51.2 42.5-60.3l192-68.6zM256 66L82.3 128 256 190l173.7-62L256 66zm32 368.6l160-57.1v-188L288 246.6v188z" />
                                                                </svg>
                                                                Active
                                                            </button>
                                                            <div class="modal fade" tabindex="-1" id="membershipModel{{ $user->id }}">
                                                                <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header border-none">
                                                                            <h5 class="modal-title">Active Membership</h5>
                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                <span aria-hidden="true">&times;</span>
                                                                            </button>
                                                                        </div>
                                                                        <div class="modal-body px-4">
                                                                            <form action="{{ route('admin.user.subscribe') }}" method="POST" id="membershipForm{{ $user->id }}" onsubmit="return confirm('Are you sure?')"> @csrf
                                                                                <label for="package_id_{{ $user->id }}">Membership package</label>
                                                                                <select name="package_id" id="package_id_{{ $user->id }}" required class="form-control">
                                                                                    <option value="">Select membership</option>
                                                                                    @foreach ($packages as $package)
                                                                                        <option value="{{ $package->id }}">{{ $package->title }} - RM{{ $package->price }}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                                                                            </form>
                                                                        </div>
                                                                        <div class="modal-footer justify-content-center border-none">
                                                                            <button type="submit" class="btn btn-primary ml-3 px-5" form="membershipForm{{ $user->id }}">Active Membership</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td nowrap>
                                                        <span class="pr-3">{{ round($user->cpd_point) ?? '0' }}</span>
                                                        <button class="curp rounded border-0" data-toggle="modal" data-target="#cpdPointModel{{ $user->id }}">
                                                            <i class="fas fa-arrow-up"></i>
                                                        </button>
                                                        <div class="modal fade" tabindex="-1" id="cpdPointModel{{ $user->id }}">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header border-none">
                                                                        <h5 class="modal-title">Update CPD Point</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body px-5">
                                                                        <form action="{{ route('admin.update.cpd-point') }}" method="POST" id="cpdPointUpd{{ $user->id }}" onsubmit="return confirm('Are you sure?')"> @csrf
                                                                            <input type="number" name="amount" min="1" placeholder="Point Amount" class="form-control text-center" autocomplete="off">
                                                                            <input type="hidden" name="id" value="{{ $user->id }}">
                                                                        </form>
                                                                    </div>
                                                                    <div class="modal-footer justify-content-center border-none">
                                                                        <button type="submit" class="btn btn-secondary mr-3 px-5" form="cpdPointUpd{{ $user->id }}" value="-" name="type">Subtract</button>
                                                                        <button type="submit" class="btn btn-primary ml-3 px-5" form="cpdPointUpd{{ $user->id }}" value="+" name="type">Add</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td nowrap>
                                                        <form id="emailForm{{ $user->id }}" class="d-inline-block" action="{{ route('register.user.email') }}" method="post"> @csrf
                                                            <select class="form-control form-control-sm {{ strtolower($user->email_verified) == 'yes' ? 'bg-success' : 'bg-danger' }}" name="email_verified"
                                                                    onchange="document.getElementById('emailForm{{ $user->id }}').submit();">
                                                                <option value="Yes" {{ strtolower($user->email_verified) == 'yes' ? 'selected' : '' }}>Verify</option>
                                                                <option value="no" {{ strtolower($user->email_verified) == 'no' ? 'selected' : '' }}>Unverify</option>
                                                            </select>
                                                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                                                        </form>
                                                    </td>

                                                    <td nowrap>
                                                        <form id="userFrom{{ $user->id }}" class="d-inline-block" action="{{ route('register.user.ban') }}" method="post">
                                                            @csrf
                                                            <select class="form-control form-control-sm {{ $user->status == 1 ? 'bg-success' : 'bg-danger' }}" name="status"
                                                                    onchange="document.getElementById('userFrom{{ $user->id }}').submit();">
                                                                <option value="1" {{ $user->status == 1 ? 'selected' : '' }}>Active</option>
                                                                <option value="0" {{ $user->status == 0 ? 'selected' : '' }}>Deactive</option>
                                                            </select>
                                                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                                                        </form>
                                                    </td>
                                                    <td nowrap>
                                                        <div class="dropdown">
                                                            <button class="btn btn-info btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                <a class="dropdown-item" href="{{route('register.user-member.view',$user->id)}}">Details</a>
                                                                <a class="dropdown-item" href="{{ route('register.user.changePass', $user->id) }}">Change Password</a>
                                                                <form class="deleteform d-block" action="{{ route('register.user.delete') }}" method="post"> @csrf
                                                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                                                    <button type="submit" class="deletebtn">Delete</button>
                                                                </form>
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
                            {{ $users->appends(['term' => request()->input('term'), 'order' => request()->input('order')])->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
