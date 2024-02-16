@extends("front.$version.layout")

@section('pagename')
 -
 {{__('Login')}}
@endsection


@section('meta-keywords', "$be->login_meta_keywords")
@section('meta-description', "$be->login_meta_description")
{{-- 
@section('breadcrumb-subtitle', __('Sign In'))
@section('breadcrumb-link', __('Sign In')) --}}


@section('content')


<!--   hero area start    -->
<div class="login-area">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                @if($bex->product_guest_checkout == 1 && !empty(request()->input('redirected')) && request()->input('redirected') == 'checkout' && !containsDigitalItemsInCart())
                    <a href="{{route('front.checkout', ['type' => 'guest'])}}" class="btn btn-block btn-primary mb-4 base-bg py-3 border-0">{{__('Checkout as Guest')}}</a>

                    <div class="mt-4 mb-3 text-center">
                        <h3 class="mb-0"><strong>{{__('OR')}},</strong></h3>
                    </div>
                @elseif($bex->package_guest_checkout == 1 && !empty(request()->input('redirected')) && request()->input('redirected') == 'package-checkout')
                    <a href="{{session()->get('link') . '?type=guest'}}" class="btn btn-block btn-primary mb-4 base-bg py-3 border-0">{{__('Checkout as Guest')}}</a>

                    <div class="mt-4 mb-3 text-center">
                        <h3 class="mb-0"><strong>{{__('OR')}},</strong></h3>
                    </div>
                @endif
                <div class="login-content">
                    <div class="login-title">
                        <h3 class="title">{{__('Login')}}</h3>
                    </div>
                    {{-- @if ($bex->is_facebook_login == 1 || $bex->is_google_login == 1)
                    <div class="social-logins mt-4 mb-4">
                        <div class="btn-group btn-group-toggle d-flex">
                            @if ($bex->is_facebook_login == 1)
                                <a class="btn btn-primary text-white py-2 facebook-login-btn" href="{{route('front.facebook.login')}}"><i class="fab fa-facebook-f mr-2"></i> {{__('Login via Facebook')}}</a>
                            @endif
                            @if ($bex->is_google_login == 1)
                                <a class="btn btn-danger text-white py-2 google-login-btn" href="{{route('front.google.login')}}"><i class="fab fa-google mr-2"></i> {{__('Login via Google')}}</a>
                            @endif
                        </div>
                    </div>
                    @endif --}}
                    <form id="loginForm" action="{{route('user.login')}}" method="POST">
                        @csrf
                        <div class="input-box">
                            <span>{{__('Email')}} *</span>
                            <input type="email" name="email" value="{{Request::old('email')}}">
                            @if(Session::has('err'))
                                <p class="text-danger mb-2 mt-2">{{Session::get('err')}}</p>
                            @endif
                            @error('email')
                            <p class="text-danger mb-2 mt-2">{{ convertUtf8($message) }}</p>
                            @enderror
                        </div>
                        <div class="input-box mb-4">
                            <span>{{__('Password')}} *</span>
                            <div class="position-relative password">
                                <input type="password" name="password" value="{{Request::old('password')}}" id="password" />
                                <button type="button" class="password_view" 
                                onclick="passwordView('')">
                                    <svg id="password_hide" style="display: none;" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                    </svg>
                                    <svg id="password_view" width="16" height="16" fill="currentColor" class="bi bi-eye-slash" viewBox="0 0 16 16">
                                        <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l.708.709z"/>
                                        <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z"/>
                                        <path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12-.708.708z"/>
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                            <p class="text-danger mb-2 mt-2">{{ convertUtf8($message) }}</p>
                            @enderror
                        </div>

                        @if ($bs->is_recaptcha == 1)
                        <div class="d-block mb-4">
                            {!! NoCaptcha::renderJs() !!}
                            {!! NoCaptcha::display() !!}
                            @if ($errors->has('g-recaptcha-response'))
                            @php
                                $errmsg = $errors->first('g-recaptcha-response');
                            @endphp
                            <p class="text-danger mb-0 mt-2">{{__("$errmsg")}}</p>
                            @endif
                        </div>
                    @endif

                        <div class="input-btn">
                            <button type="submit" class="submit_btn_load">{{__('LOG IN')}}</button><br>
                            <p class="float-lg-right float-left">{{__("Don't have an account ?")}} <a href="{{route('user-register')}}">{{__('Click Here')}}</a> {{__('to create one.')}}</p>
                            <a class="" href="{{route('user-forgot')}}">{{__('Lost your password?')}}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!--   hero area end    -->
@endsection

@section('scripts')
    <script>
        function passwordView($id) {
            var x = document.getElementById($id+"password");
            if (x.type === "password") {
                x.type = "text";
                document.getElementById($id+'password_view').style.display = 'block';
                document.getElementById($id+'password_hide').style.display = 'none';
            } else {
                x.type = "password";
                document.getElementById($id+'password_view').style.display = 'none';
                document.getElementById($id+'password_hide').style.display = 'block';
            }
        }
    </script>
@endsection

