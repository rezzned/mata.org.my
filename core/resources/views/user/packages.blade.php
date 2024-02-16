@extends('user.layout')

@section('pagename')
 - {{__('Membership')}}
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
                    <div class="col-12">
                            @if (!$active_sub || ($active_sub && ((!$active_sub->current_package && !$active_sub->pending_package))))
                            <div class="alert alert-warning" style="border-left: 5px solid #000;">
                                {{__("Currently, you are a Non-member")}}.
                            </div>
                            @endif
                            @if ($active_sub && $active_sub->next_package_id)
                            <div class="alert alert-warning" style="border-left: 5px solid #000;">
                                <p class="mb-0">{{__('You already have another package in stock to activate along side the current package.')}}</p>
                                <p class="mb-0">{{__('You cannot purchase / extend / change to any package, until the next package is activated.')}}</p>
                            </div>
                            @endif
                            @if ($active_sub && $active_sub->current_package_id && $active_sub->status==1)
                            <div class="alert alert-warning" style="border-left: 5px solid #000;">
                                <p class="mb-0"><strong>{{__('Current Package')}}:</strong> {{$active_sub->current_package->title}} ({{__('Expire Date')}}: {{\Carbon\Carbon::parse($active_sub->expire_date)->toFormattedDateString() }})</p>
                            </div>
                            @endif
                            @if ($active_sub && !empty($active_sub->next_package_id))
                            <div class="alert alert-warning" style="border-left: 5px solid #000;">
                                <p class="mb-0"><strong>{{__('Next Package to Activate')}}:</strong> {{$active_sub->next_package->title}}</p>
                            </div>
                            @endif
                            @if ($active_sub && $active_sub->status == 3 && $active_sub->pending_package)
                            <div class="alert alert-warning" style="border-left: 5px solid #000;">
                                <p class="mb-0">{{ __("You have a active membership registration request please wait for approval") }}</p>
                            </div>
                            @endif
                    </div>
                    <div class="col-lg-12">
                        <div class="user-profile-details">
                            <div class="account-info">
                                <div class="title">
                                    <h4>{{__('Membership')}}</h4>
                                </div>
                                <div class="main-info">
                                    <div class="main-table">
                                    <div class="table-responsiv">
                                        <table id="packagesTable" class="dataTables_wrapper dt-responsive table-striped dt-bootstrap4" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>{{__('Member type')}}</th>
                                                    <th>{{__('Membership fee')}}</th>
                                                    <th>{{__('Duration')}}</th>
                                                    @if ($active_sub || ($active_sub && empty($active_sub->next_package_id)))
                                                    <th class="text-center">{{__('Member registration')}}</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if($packages)
                                                @foreach ($packages as $package)
                                                <tr>
                                                    <td>{{ $package->title }}</td>
                                                    <td>
                                                        {{ $bex->base_currency_symbol }}
                                                        @if ($active_sub)
                                                            {{$package->extend_fee}}
                                                            @if ($package->type == 'associate_to_standard_member')
                                                                {{$package->price}}
                                                            @endif
                                                            <br>
                                                        @else
                                                            {{$package->price}}
                                                            @if ($package->entrance_fee)
                                                                <br>
                                                                {{ ('Entrance fee') }}: {{$bex->base_currency_symbol}} {{$package->entrance_fee}}
                                                            @endif
                                                            <br>
                                                        @endif
                                                        @if ((isset($active_sub) && (isset($active_sub->current_package) && $active_sub->current_package->type == 'associate_member')) && $package->upgrade_fee > 0)
                                                            @lang('Upgrade Fee'):  {{currency_format($package->upgrade_fee)}}
                                                        @endif
                                                    </td>
                                                    <td>{{$package->duration == 'monthly' ? __('Monthly') : __('Yearly')}}</td>
                                                    <td class="text-center">
                                                        @if ($active_sub && $active_sub->status != 3)
                                                            @if ($active_sub->current_package_id == $package->id && $active_sub->payment_status == 0)
                                                                <a href="{{route('user-packages.payment',$active_sub->id)}}" class="btn btn-sm text-uppercase ">{{ __('Pay') }}</a>
                                                            @endif
                                                            @if ($active_sub->current_package_id == $package->id && $active_sub->status == 1 && in_array($package->type, ['associate_member', 'standard_member']))
                                                                <a href="{{route('front.packageorder.index',$package->id)}}" class="btn btn-sm text-uppercase ">{{ __('Extend') }}</a>
                                                            @elseif($package->type == 'standard_member' && $active_sub->current_package && $active_sub->current_package->type != 'standard_member' 
                                                                && carbon_parse($user->associate_member_start_date)->age >= 3 && $user->cpd_point >= 20)
                                                                <a href="{{route('front.packageorder.index',$package->id)}}" class="btn btn-sm text-uppercase ">{{ __('Upgrade') }}</a>
                                                            @elseif((!$active_sub->current_package || !$active_sub->pending_package) && $active_sub->current_package_id == $package->id)
                                                                <a href="{{route('front.packageorder.index', $package->id)}}" class="btn btn-sm text-uppercase ">{{ __('Register') }}</a>
                                                            @else
                                                                N/A
                                                            @endif
                                                        @elseif (!$active_sub->current_package_id && $active_sub->status == 3)
                                                            <a href="{{route('front.packageorder.index', $package->id)}}" class="btn btn-sm text-uppercase ">{{ __('Register') }}</a>
                                                        @elseif (!$active_sub && $package->type != 'associate_to_standard_member')
                                                            <a href="{{route('front.packageorder.index', $package->id)}}" class="btn btn-sm text-uppercase ">{{ __('Register') }}</a>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                                @else
                                                <tr class="text-center">
                                                    <td colspan="4">
                                                        {{__('No Packages')}}
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
        $('#packagesTable').DataTable({
            responsive: true,
            ordering: false
        });
    });
</script>
@endsection

