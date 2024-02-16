@extends('admin.layout')
@section('content')
@php
$admin = Auth::guard('admin')->user();
if (!empty($admin->role)) {
    $permissions = $admin->role->permissions;
    $permissions = json_decode($permissions, true);
}
@endphp
<div class="mt-2 mb-4">
    <h2 class="text-white pb-2">Welcome back, {{Auth::guard('admin')->user()->first_name}} {{Auth::guard('admin')->user()->last_name}}!</h2>
</div>
<div class="row">
    @if (empty($admin->role) || (!empty($permissions) && in_array('Package Management', $permissions)))
    <div class="col-sm-6 col-md-3">
        <a href="{{route('admin.register.user') . '?language=' . $default->code}}" class="d-block">
            <div class="card card-stats card-primary card-round">
                <div class="card-body">
                    <div class="row">
                        <div class="col-3">
                            <div class="icon-big text-center">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                        </div>
                        <div class="col-9 col-stats">
                            <div class="numbers">
                                <p class="card-category">@lang('Registered Members')</p>
                                <h4 class="card-title">{{$default->packages()->count()}}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    @if ($bex->recurring_billing == 1)
    <div class="col-sm-6 col-md-3">
        <a href="{{route('admin.subscriptions', ['type' => 'active'])}}" class="d-block">
            <div class="card card-stats card-secondary card-round">
                <div class="card-body">
                    <div class="row">
                        <div class="col-3">
                            <div class="icon-big text-center">
                                <i class="far fa-handshake"></i>
                            </div>
                        </div>
                        <div class="col-9 col-stats">
                            <div class="numbers">
                                <p class="card-category">Active Subscriptions</p>
                                <h4 class="card-title">{{\App\Subscription::where('status', 1)->count()}}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    @endif
    @if ($bex->recurring_billing == 0)
    <div class="col-sm-6 col-md-3">
        <a href="{{route('admin.all.orders')}}" class="d-block">
            <div class="card card-stats card-secondary card-round">
                <div class="card-body">
                    <div class="row">
                        <div class="col-3">
                            <div class="icon-big text-center">
                                <i class="fas fa-box-open"></i>
                            </div>
                        </div>
                        <div class="col-9 col-stats">
                            <div class="numbers">
                                <p class="card-category">Membership Orders</p>
                                <h4 class="card-title">{{\App\PackageOrder::count()}}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    @endif

    @endif


    @if (empty($admin->role) || (!empty($permissions) && in_array('Shop Management', $permissions)))
    @if ($bex->is_shop == 1)
    <div class="col-sm-6 col-md-3">
        <a href="{{route('admin.product.index', ['language' => $default->code])}}" class="d-block">
            <div class="card card-stats card-danger card-round">
                <div class="card-body">
                    <div class="row">
                        <div class="col-3">
                            <div class="icon-big text-center">
                                <i class="fas fa-boxes"></i>
                            </div>
                        </div>
                        <div class="col-9 col-stats">
                            <div class="numbers">
                                <p class="card-category">Publication</p>
                                <h4 class="card-title">{{$default->products()->count()}}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    @endif
    @if ($bex->is_shop == 1)
    <div class="col-sm-6 col-md-3">
        <a href="{{route('admin.all.product.orders')}}" class="d-block">
            <div class="card card-stats card-warning card-round">
                <div class="card-body">
                    <div class="row">
                        <div class="col-3">
                            <div class="icon-big text-center">
                                <i class="fas fa-truck"></i>
                            </div>
                        </div>
                        <div class="col-9 col-stats">
                            <div class="numbers">
                                <p class="card-category">Publication Orders</p>
                                <h4 class="card-title">{{\App\ProductOrder::count()}}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    @endif
    @endif


    @if (empty($admin->role) || (!empty($permissions) && in_array('Course Management', $permissions)))
    @if ($bex->is_course == 1)
    <div class="col-sm-6 col-md-3">
        <a href="{{route('admin.course.index', ['language' => $default->code])}}" class="d-block">
            <div class="card card-stats card-success card-round">
                <div class="card-body">
                    <div class="row">
                        <div class="col-3">
                            <div class="icon-big text-center">
                                <i class="fas fa-video"></i>
                            </div>
                        </div>
                        <div class="col-9 col-stats">
                            <div class="numbers">
                                <p class="card-category">Courses</p>
                                <h4 class="card-title">{{$default->courses()->count()}}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-md-3">
        <a href="{{route('admin.course.purchaseLog')}}" class="d-block">
            <div class="card card-stats card-dark card-round">
                <div class="card-body">
                    <div class="row">
                        <div class="col-3">
                            <div class="icon-big text-center">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                        </div>
                        <div class="col-9 col-stats">
                            <div class="numbers">
                                <p class="card-category">Course Enrolls</p>
                                <h4 class="card-title">{{\App\CoursePurchase::count()}}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    @endif
    @endif


    @if (empty($admin->role) || (!empty($permissions) && in_array('Events Management', $permissions)))
    @if ($bex->is_event == 1)
    <div class="col-sm-6 col-md-3">
        <a href="{{route('admin.event.index', ['language' => $default->code])}}" class="d-block">
            <div class="card card-stats card-info card-round">
                <div class="card-body ">
                    <div class="row">
                        <div class="col-3">
                            <div class="icon-big text-center">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </div>
                        <div class="col-9 col-stats">
                            <div class="numbers">
                                <p class="card-category">Trainings</p>
                                <h4 class="card-title">{{$default->events()->count()}}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-md-3">
        <a href="{{route('admin.event.payment.log')}}" class="d-block">
            <div class="card card-stats card-primary card-round">
                <div class="card-body ">
                    <div class="row">
                        <div class="col-3">
                            <div class="icon-big text-center">
                                <i class="far fa-calendar-check"></i>
                            </div>
                        </div>
                        <div class="col-9 col-stats">
                            <div class="numbers">
                                <p class="card-category">Trainings Bookings</p>
                                <h4 class="card-title">{{\App\EventDetail::count()}}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    @endif
    @endif

    @if (empty($admin->role) || (!empty($permissions) && in_array('Donation Management', $permissions)))
    @if ($bex->is_donation == 1)
    <div class="col-sm-6 col-md-3">
        <a href="{{route('admin.donation.index', ['language' => $default->code])}}" class="d-block">
            <div class="card card-stats card-danger card-round">
                <div class="card-body ">
                    <div class="row">
                        <div class="col-3">
                            <div class="icon-big text-center">
                                <i class="fas fa-hand-holding-heart"></i>
                            </div>
                        </div>
                        <div class="col-9 col-stats">
                            <div class="numbers">
                                <p class="card-category">Causes</p>
                                <h4 class="card-title">{{$default->causes()->count()}}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-md-3">
        <a href="{{route('admin.donation.payment.log')}}" class="d-block">
            <div class="card card-stats card-warning card-round">
                <div class="card-body ">
                    <div class="row">
                        <div class="col-3">
                            <div class="icon-big text-center">
                                <i class="fas fa-donate"></i>
                            </div>
                        </div>
                        <div class="col-9 col-stats">
                            <div class="numbers">
                                <p class="card-category">Donations</p>
                                <h4 class="card-title">{{\App\DonationDetail::count()}}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    @endif
    @endif

    @if (empty($admin->role) || (!empty($permissions) && in_array('Users Management', $permissions)))
    <div class="col-sm-6 col-md-3">
        <a href="{{route('admin.subscriber.index')}}" class="d-block">
            <div class="card card-stats card-info card-round">
                <div class="card-body ">
                    <div class="row">
                        <div class="col-3">
                            <div class="icon-big text-center">
                                <i class="fas fa-bell"></i>
                            </div>
                        </div>
                        <div class="col-9 col-stats pl-1">
                            <div class="numbers">
                                <p class="card-category">Subscribers</p>
                                <h4 class="card-title">{{\App\Subscriber::count()}}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    @endif
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="row row-card-no-pd">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-head-row">
                            <h4 class="card-title">Pending Payment Orders</h4>
                        </div>
                        <p class="card-category">
                            All payment pending orders
                        </p>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Pending Type</th>
                                                <th>Total</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($pending_items as $key => $pending_item)
                                            <tr>
                                                <td>{{$pending_item['name']}}</td>
                                                <td>{{$bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : ''}} {{round($pending_item['amount'],2)}} {{$bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : ''}}</td>

                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-info btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            Actions
                                                        </button>
                                                        @if ($pending_item['type'] == 'publication_pending')
                                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                <a class="dropdown-item" href="{{route('admin.product.details', $pending_item['id'])}}" target="_blank">Details</a>
                                                                @if ($pending_item['invoice_number'])
                                                                    <a class="dropdown-item" href="{{asset('assets/front/invoices/product/'.$pending_item['invoice_number'])}}" target="_blank">Invoice</a>
                                                                @endif
                                                                <form class="deleteform d-block" action="{{route('admin.product.order.delete')}}" method="post">
                                                                    @csrf
                                                                    <input type="hidden" name="order_id" value="{{$pending_item['id']}}">
                                                                    <button type="submit" class="deletebtn">
                                                                        Delete
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        @elseif($pending_item['type'] == 'event_pending')
                                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                            @if ($pending_item['invoice_number'])
                                                                <a class="dropdown-item" href="{{asset('assets/front/img/events/receipt/'.$pending_item['invoice_number'])}}" target="_blank">Invoice</a>
                                                            @endif
                                                            <form class="deleteform d-block" action="{{route('admin.event.payment.delete')}}" method="post">
                                                                @csrf
                                                                <input type="hidden" name="order_id" value="{{$pending_item['id']}}">
                                                                <button type="submit" class="deletebtn">
                                                                    Delete
                                                                </button>
                                                            </form>
                                                        </div>
                                                        @elseif($pending_item['type'] == 'subscription_pending')
                                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                            @if ($pending_item['invoice_number'])
                                                                <a class="dropdown-item" href="{{asset('assets/front/receipt/'.$pending_item['invoice_number'])}}" target="_blank">Invoice</a>
                                                            @endif
                                                            <form class="deleteform d-block" action="{{route('admin.package.subDelete')}}" method="post">
                                                                @csrf
                                                                <input type="hidden" name="order_id" value="{{$pending_item['id']}}">
                                                                <button type="submit" class="deletebtn">
                                                                    Delete
                                                                </button>
                                                            </form>
                                                        </div>
                                                        @endif
                                                    </div>
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
<!-- Send Mail Modal -->
<div class="modal fade" id="mailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Send Mail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="ajaxEditForm" class="" action="{{route('admin.quotes.mail')}}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="">Client Mail **</label>
                        <input id="inemail" type="text" class="form-control" name="email" value="" placeholder="Enter email">
                        <p id="eerremail" class="mb-0 text-danger em"></p>
                    </div>
                    <div class="form-group">
                        <label for="">Subject **</label>
                        <input id="insubject" type="text" class="form-control" name="subject" value="" placeholder="Enter subject">
                        <p id="eerrsubject" class="mb-0 text-danger em"></p>
                    </div>
                    <div class="form-group">
                        <label for="">Message **</label>
                        <textarea id="inmessage" class="form-control nic-edit" name="message" rows="5" cols="80" placeholder="Enter message"></textarea>
                        <p id="eerrmessage" class="mb-0 text-danger em"></p>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button id="updateBtn" type="button" class="btn btn-primary">Send Mail</button>
            </div>
        </div>
    </div>
</div>
@endsection
