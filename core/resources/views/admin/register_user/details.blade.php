@extends('admin.layout')
@section('content')
    <div class="page-header">
        <h4 class="page-title">{{__('Registered Users Details')}}</h4>
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
                <a href="{{ url()->previous() }}">Registered Users</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{__('Registered Users Details')}}</a>
            </li>
        </ul>

        <div style="margin-left: auto">
            <a href="{{ route('register.user.edit',$user->id) }}" class="btn-md btn btn-primary mr-1" >Edit</a>
            <a href="{{ url()->previous() }}" class="btn-md btn btn-primary">Back</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">
                            <div class="h4 card-title">
                                Profile Picture
                            </div>
                        </div>
                        <div class="card-body py-4 text-center">
                            @if (strpos($user->photo, 'facebook') !== false || strpos($user->photo, 'google'))
                                <img class="rounded-circle" src="{{ $user->photo ? $user->photo : asset('assets/front/img/user/profile-img.png') }}" alt="" width="150">
                            @else
                                <img src="{{ !empty($user->photo) ? asset('assets/front/img/user/' . $user->photo) : '' }}" alt="" width="150">
                            @endif
                            <p class="mb-0">
                                @lang('Membership ID'): {{$user->membership_id}}
                                <button type='button' data-toggle='modal' data-target="#membershipIdModal" class="badge mb-0 mt-1 ml-1">@lang('Edit')</button>
                            </p>

                        </div>
                        <div class="modal fade" id="membershipIdModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="exampleModalLongTitle">Add Event Category</h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                  </button>
                                </div>
                                <div class="modal-body">
                                  <form id="ajaxForm" class="modal-form create" action="{{route('register.user.membership-id.update',$user->id)}}" method="POST">
                                    @csrf

                                    <div class="form-group">
                                      <label for="">Name **</label>
                                      <input type="text" class="form-control" name="membershipid" value="{{$user->membership_id}}" placeholder="Enter Membership ID">
                                      <p id="errmembershipid" class="mb-0 text-danger em"></p>
                                    </div>

                                    <input type="hidden" name="id" value="{{ $user->id }}">

                                  </form>
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                  <button id="submitBtn" type="button" class="btn btn-primary">Submit</button>
                                </div>
                              </div>
                            </div>
                          </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">{{ __('Account Details') }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-lg-6">
                                    <strong>{{ __('First Name:') }}</strong>
                                </div>
                                <div class="col-lg-6">
                                    {{ $user->fname }}
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-6">
                                    <strong>{{ __('Last Name:') }}</strong>
                                </div>
                                <div class="col-lg-6">
                                    {{ $user->lname }}
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-6">
                                    <strong>{{ __('Username:') }}</strong>
                                </div>
                                <div class="col-lg-6">
                                    {{ $user->username }}
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-6">
                                    <strong>{{ __('Email:') }}</strong>
                                </div>
                                <div class="col-lg-6">
                                    {{ $user->email }}
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-6">
                                    <strong>{{ __('Contact Number:') }}</strong>
                                </div>
                                <div class="col-lg-6">
                                    {{ $user->personal_phone }}
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-6">
                                    <strong>{{ __('Country:') }}</strong>
                                </div>
                                <div class="col-lg-6">
                                    {{ $user->country }}
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-6">
                                    <strong>{{ __('Postcode:') }}</strong>
                                </div>
                                <div class="col-lg-6">
                                    {{ $user->city }}
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-6">
                                    <strong>{{ __('Address:') }}</strong>
                                </div>
                                <div class="col-lg-6">
                                    {{ $user->address }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-md-9">
                    <div class="row">

                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">{{ __('Shipping Details') }}</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-lg-6">
                                            <strong>{{ __('Email:') }}</strong>
                                        </div>
                                        <div class="col-lg-6">
                                            {{ $user->billing_email }}
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-lg-6">
                                            <strong>{{ __('Phone:') }}</strong>
                                        </div>
                                        <div class="col-lg-6">
                                            {{ $user->billing_number }}
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-lg-6">
                                            <strong>{{ __('City:') }}</strong>
                                        </div>
                                        <div class="col-lg-6">
                                            {{ $user->billing_city }}
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-lg-6">
                                            <strong>{{ __('Address:') }}</strong>
                                        </div>
                                        <div class="col-lg-6">
                                            {{ $user->billing_address }}
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-lg-6">
                                            <strong>{{ __('Country:') }}</strong>
                                        </div>
                                        <div class="col-lg-6">
                                            {{ $user->billing_country }}
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>

                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">{{ __('MATA Member Information') }}</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row mb-2">
                                                <div class="col-lg-6">
                                                    <strong>{{ __('Date of Birth:') }}</strong>
                                                </div>
                                                <div class="col-lg-6">
                                                    {{ Carbon\Carbon::parse($user->date_of_birth)->format('d M, Y') }}
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-lg-6">
                                                    <strong>{{ __('Age:') }}</strong>
                                                </div>
                                                <div class="col-lg-6">
                                                    {{ $user->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth)->age . ' years' : '-' }}
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-lg-6">
                                                    <strong>{{ __('Gender:') }}</strong>
                                                </div>
                                                <div class="col-lg-6">
                                                    {{ Str::ucfirst($user->gender) }}
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-lg-6">
                                                    <strong>{{ __('Nation:') }}</strong>
                                                </div>
                                                <div class="col-lg-6">
                                                    {{ $user->nation }}
                                                </div>
                                            </div>
                                        </div>
                                        {{-- <div class="col-md-6">
                                            <div class="row mb-2">
                                                <div class="col-lg-6">
                                                    <strong>{{ __('IC Nnumber:') }}</strong>
                                                </div>
                                                <div class="col-lg-6">
                                                    {{ $user->idcard_no }}
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-lg-6">
                                                    <strong>{{ __('Current Income:') }}</strong>
                                                </div>
                                                <div class="col-lg-6">
                                                    {{$bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : ''}} {{ $user->current_income ?? 0 }} {{$bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : ''}}
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-lg-6">
                                                    <strong>{{ __('Company Telephone:') }}</strong>
                                                </div>
                                                <div class="col-lg-6">
                                                    {{ $user->company_phone }}
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-lg-6">
                                                    <strong>{{ __('Company Email:') }}</strong>
                                                </div>
                                                <div class="col-lg-6">
                                                    {{ $user->company_email }}
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-lg-6">
                                                    <strong>{{ __('Company Address:') }}</strong>
                                                </div>
                                                <div class="col-lg-6">
                                                    {{ $user->company_address }}
                                                </div>
                                            </div>
                                        </div> --}}
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Orders of [{{ $user->username }}]</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive product-list">
                                <h5></h5>
                                <table class="table-striped mt-3 table">
                                    <thead>
                                        <tr>
                                            <th scope="col">Date</th>
                                            <th scope="col">Total</th>
                                            <th scope="col">Payment Status</th>
                                            <th scope="col">Order Status</th>
                                            <th scope="col">Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($orders as $key => $order)
                                            <tr>
                                                <td>{{ convertUtf8($order->created_at->format('d-m-Y')) }}</td>
                                                <td>$ {{ round($order->total, 2) }}</td>
                                                <td>
                                                    @if ($order->payment_status == 'Pending' || $order->payment_status == 'pending')
                                                        <p class="badge badge-danger">{{ $order->payment_status }}</p>
                                                    @else
                                                        <p class="badge badge-success">{{ $order->payment_status }}</p>
                                                    @endif
                                                </td>
                                                <td>
                                                    <form id="statusForm{{ $order->id }}" class="d-inline-block" action="{{ route('admin.product.orders.status') }}" method="post">
                                                        @csrf
                                                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                        <select class="form-control @if ($order->order_status == 'pending') bg-warning
                                                                @elseif ($order->order_status == 'processing')
                                                                bg-primary
                                                                @elseif ($order->order_status == 'completed')
                                                                bg-success
                                                                @elseif ($order->order_status == 'reject')
                                                                bg-danger @endif"
                                                                name="order_status" onchange="document.getElementById('statusForm{{ $order->id }}').submit();">
                                                            <option value="pending" {{ $order->order_status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                            <option value="processing" {{ $order->order_status == 'processing' ? 'selected' : '' }}>Processing</option>
                                                            <option value="completed" {{ $order->order_status == 'completed' ? 'selected' : '' }}>Completed</option>
                                                            <option value="reject" {{ $order->order_status == 'reject' ? 'selected' : '' }}>Rejected</option>
                                                        </select>
                                                    </form>
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.product.details', $order->id) }}" class="btn btn-primary btn-sm editbtn"><i class="fas fa-eye"></i> View</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-block text-center">
                                {{ $orders->links() }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>


        </div>
    </div>
@endsection
