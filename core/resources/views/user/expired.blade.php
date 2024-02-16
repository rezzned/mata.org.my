@extends('user.layout')

@section('pagename')
 - {{__('Change Password')}}
@endsection

@section('content')

<!--   hero area end    -->
     <!--====== CHECKOUT PART START ======-->
     <section class="user-dashbord">
        <div class="container">
            <div class="row">
                @include('user.inc.site_bar')
                <div class="col-lg-9">
                    <div class="row mb-5">
                        <div class="col-lg-12">
                            <div class="user-reset">
                                <div class="account-info">
                                    <div class="title">
                                        <h4>{{__('Notice')}}</h4>
                                    </div>
                                    <div class="edit-info-area">
                                       @if(session()->has('err'))
                                       <p class="text-danger mb-4">{{ session()->get('err') }}</p>
                                       <p class="text-center">
                                            We are extremely that your account has been expired due to inactivity. Please contact our support to activate your account again.
                                       </p>
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
