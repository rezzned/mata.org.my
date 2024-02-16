@extends('user.layout')

@section('pagename')
- {{__('Dashboard')}}
@endsection

@section('content')

<!--   hero area start   -->
<div class="breadcrumb-area services service-bg"
    style="background-image: url('{{asset  ('assets/front/img/' . $bs->breadcrumb)}}');background-size:cover;">
    <div class="container">
        <div class="breadcrumb-txt" style="padding:50px 0 40px 0">
            @if (count($banners))

            <div id="bannerCarousel" class="carousel slide" data-ride="carousel">
                <div class="carousel-inner">
                    @foreach ($banners as $key => $item)
                    <div class="carousel-item {{$key == 0 ? 'active' : ''}}">
                        <img src="{{ url('assets/front/img/banner/'.$item->image) }}" width="100%"  class="d-block w-100" alt="" />
                    </div>
                    @endforeach
                </div>
                <button class="carousel-control-prev" type="button" data-target="#bannerCarousel" data-slide="prev" style="background:none;border:none">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-target="#bannerCarousel" data-slide="next" style="background:none;border:none">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
                </button>
            </div>

        @endif
        </div>
    </div>
    <div class="breadcrumb-area-overlay"></div>
</div>


<!--   hero area end    -->
<!--====== CHECKOUT PART START ======-->
<section class="user-dashbord">
    <div class="container">
        <div class="row">
            @include('user.inc.site_bar')
            <div class="col-lg-9">
                <div class="row mb-5">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <a class="card card-box box-6 invoice">
                                    <div class="card-info">
                                        <h4>{{__('Overdue Payment')}}</h4>
                                        <p>{{ $activeSub && $activeSub->status != 3 && $activeSub->expire_date ? (carbon_parse($activeSub->expire_date) >= today() ? '0' : $activeSub->current_package->price) : '0' }}</p>
                                    </div>
                                </a>
                            </div>


                            <div class="col-md-6 mb-4">
                                <a href="{{route('user-profile').'#license_expire_date'}}" class="card card-box box-8 bell">
                                    <div class="card-info">
                                        <h4>{{__('Licence Expire Date')}}</h4>
                                        @if ($user->license_expire_date)
                                            <p>
                                                @if (carbon_parse($user->license_expire_date) <= today())
                                                {{ __("Expired") }}
                                                @else
                                                {{dateFormat($user->license_expire_date, 'd-m-Y')}}
                                                @endif
                                                @if (carbon_parse($user->license_expire_notify_date) <= today() && $user->license_expire_notify == 'yes')
                                                    <span data-toggle="tooltip" data-placement="top" title="{{ (carbon_parse($user->license_expire_date) <= today() ? __('Your license is expired') : __('Your license is about to expire')) }}">
                                                        <svg width="16" height="16" fill="currentColor" class="bi bi-exclamation-circle-fill" viewBox="0 0 16 16">
                                                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
                                                        </svg>
                                                    </span>
                                                @endif
                                            </p>
                                        @else
                                            <p>{{ __('No Update') }}
                                                <span data-toggle="tooltip" data-placement="top" title="{{ __('Please update your license expire date') }}">
                                                    <svg width="16" height="16" fill="currentColor" class="bi bi-question-circle-fill" viewBox="0 0 16 16">
                                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.496 6.033h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286a.237.237 0 0 0 .241.247zm2.325 6.443c.61 0 1.029-.394 1.029-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94 0 .533.425.927 1.01.927z"/>
                                                    </svg>
                                                </span>
                                            </p>
                                        @endif
                                    </div>
                                </a>
                            </div>

                            <div class="col-md-6">
                                @php
                                $cpd_required = auth()->user()->cpd_required->sortBy('year')->first();
                                @endphp
                                <a class="card card-box box-1 mb-4 cpd_point" href="{{ route('user-cpdhours') }}">
                                    <div class="card-info">
                                        <h4>{{__('Total CPD Points')}}</h4>
                                        <p>
                                            {{ round(Auth::user()->cpd_point ?? 0) }}
                                            {{-- {{ $cpd_required->required_points }} --}}
                                            <span data-toggle="tooltip" data-placement="top" title="{{ __ ('Required ').(isset($cpd_required) ? $cpd_required->required_points. ' ('.$cpd_required->year.')' : 'none')}}">
                                                <svg width="16" height="16" fill="currentColor" class="bi bi-question-circle-fill" viewBox="0 0 16 16">
                                                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.496 6.033h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286a.237.237 0 0 0 .241.247zm2.325 6.443c.61 0 1.029-.394 1.029-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94 0 .533.425.927 1.01.927z"/>
                                                </svg>
                                            </span>
                                        </p>
                                    </div>
                                </a>
                            </div>

                            <div class="col-md-6 mb-4">
                                <a class="card card-box box-6 cpd_point2">
                                    <div class="card-info">
                                        <h4>{{__('CPD Points Balance')}}</h4>
                                        <p>{{isset($cpd_point_reqired) ? ($cpd_point_reqired->required_points ?? 0) - round(Auth::user()->cpd_point ?? 0) : 0 }}</p>
                                    </div>
                                </a>
                            </div>

                            <div class="col-md-6 mb-4">
                                <a class="card card-box box-4 event" href="{{route('user-events')}}">
                                    <div class="card-info">
                                        <h4>{{__('Training Joined')}}</h4>
                                        <p>{{App\EventDetail::where('user_id',Auth::user()->id)->count()}}</p>
                                    </div>
                                </a>
                            </div>

                            <div class="col-md-6">
                                <a class="card card-box box-2 mb-4 product" href="{{route('user-orders')}}">
                                    <div class="card-info">
                                        <h4>{{__('Publication Orders')}}</h4>
                                        <p>{{App\ProductOrder::where('user_id',Auth::user()->id)->count()}}</p>
                                    </div>
                                </a>
                            </div>

                            <div class="col-md-6">
                                <a class="card card-box box-3 course" href="{{route('user.course_orders')}}">
                                    <div class="card-info">
                                        <h4>{{__('Enrolled Courses')}}</h4>
                                        <p>{{App\CoursePurchase::where('user_id',Auth::user()->id)->where('payment_status',
                                            'Completed')->count()}}</p>
                                    </div>
                                </a>
                            </div>

                            <div class="col-md-6 mb-4">
                                <a class="card card-box box-5 member" href="{{ route('user-packages') }}">
                                    <div class="card-info">
                                        <h4>{{__('Membership')}}</h4>
                                        <p>{{ ($activeSub && $activeSub->status==1) ? $activeSub->current_package->title ?? "No membership" : "No membership" }}</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="user-profile-details">
                            <div class="account-info">
                                <div class="title">
                                    <h4>{{__('MATA Member Information')}}</h4>
                                </div>
                                <div class="main-info">
                                    <div class="d-flex justify-content-between">
                                        <h5>{{ convertUtf8($user->full_name) }} ({{convertUtf8($user->username)}})</h5>
                                        <p>ID: {{ strtoupper($user->membership_id) }}</p>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table">
                                                <tr class="">
                                                    <td class="py-1 pl-0 border-0">{{__('First Name')}}:</td>
                                                    <td class="py-1 pl-0 border-0">{{ convertUtf8($user->fname) }}</td>
                                                </tr>
                                                <tr class="">
                                                    <td class="py-1 pl-0 border-0">{{__('Email')}}:</td>
                                                    <td class="py-1 pl-0 border-0">{{ convertUtf8($user->email) }}</td>
                                                </tr>
                                                <tr class="">
                                                    <td class="py-1 pl-0 border-0">{{__('Phone')}}:</td>
                                                    <td class="py-1 pl-0 border-0">{{ $user->personal_phone ?? 'N/A' }}</td>
                                                </tr>
                                                <tr class="">
                                                    <td class="py-1 pl-0 border-0">{{__('Address')}}:</td>
                                                    <td class="py-1 pl-0 border-0">{{convertUtf8($user->address)}}</td>
                                                </tr>
                                                <tr class="">
                                                    <td class="py-1 pl-0 border-0">{{__('Postcode')}}:</td>
                                                    <td class="py-1 pl-0 border-0">{{convertUtf8($user->city)}}</td>
                                                </tr>
                                                <tr class="">
                                                    <td class="py-1 pl-0 border-0">{{__('State')}}:</td>
                                                    <td class="py-1 pl-0 border-0">{{convertUtf8($user->state)}}</td>
                                                </tr>
                                                <tr class="">
                                                    <td class="py-1 pl-0 border-0">{{__('Country')}}:</td>
                                                    <td class="py-1 pl-0 border-0">{{convertUtf8($user->country)}}</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table">
                                                <tr class="">
                                                    <td class="py-1 pl-0 border-0">{{__('Last Name')}}:</td>
                                                    <td class="py-1 pl-0 border-0">{{ convertUtf8($user->lname) }}</td>
                                                </tr>
                                                <tr class="">
                                                    <td class="py-1 pl-0 border-0">{{__('Date of Birth')}}:</td>
                                                    <td class="py-1 pl-0 border-0">{{ dateFormat($user->date_of_birth, 'd/m/Y') }}</td>
                                                </tr>
                                                <tr class="">
                                                    <td class="py-1 pl-0 border-0">{{__('Age')}}:</td>
                                                    <td class="py-1 pl-0 border-0">
                                                        {{ $user->date_of_birth ? Carbon\Carbon::parse($user->date_of_birth)->age . ' years' : '-' }}
                                                    </td>
                                                </tr>
                                                <tr class="">
                                                    <td class="py-1 pl-0 border-0">{{__('Gender')}}:</td>
                                                    <td class="py-1 pl-0 border-0">{{convertUtf8(Str::ucfirst($user->gender)) ?? 'Other'}}</td>
                                                </tr>
                                                <tr class="">
                                                    <td class="py-1 pl-0 border-0">{{__('Nation')}}:</td>
                                                    <td class="py-1 pl-0 border-0">{{convertUtf8($user->nation ?? "Unknown")}}</td>
                                                </tr>
                                                <tr class="">
                                                    <td class="py-1 pl-0 border-0">{{ trans('Company Fax') }}:</td>
                                                    <td class="py-1 pl-0 border-0">{{ $user->company_fax }}</td>
                                                </tr>
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
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#ordersTable').DataTable({
            responsive: true,
            ordering: false
        });
    });
</script>
@endsection
