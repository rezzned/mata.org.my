@extends("front.$version.layout")

@section('pagename')
-
{{__('Register')}}
@endsection

@section('meta-keywords', "$be->register_meta_keywords")
@section('meta-description', "$be->register_meta_description")

@section('breadcrumb-subtitle', __('Sign Up'))
@section('breadcrumb-link', __('Sign Up'))

@section('styles')
<link rel="stylesheet" href="{{asset('/assets/flatpickr/flatpickr.min.css')}}" />
@endsection
@section('content')

<!--   hero area start    -->
<div class="login-area">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="login-content">
                    @if(Session::has('sendmail'))
                    <div class="alert alert-success mb-4">
                        <p style="line-height: 24px;">{{Session::get('sendmail')}}</p>
                    </div>
                    @endif
                    <div class="login-title">
                        <h3 class="title">{{__('Register')}}</h3>
                    </div>

                    <form action="{{route('user-register-submit')}}" method="POST">@csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-box">
                                    <span>{{__('First Name')}} *</span>
                                    <input type="text" name="fname" value="{{old('fname')}}" id="first_name">
                                    @if(Session::has('err'))
                                    <p class="text-danger mb-2 mt-2">{{Session::get('err')}}</p>
                                    @endif
                                    @error('fname')
                                    <p class="text-danger mb-2 mt-2">{{ convertUtf8($message) }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-box">
                                    <span>{{__('Last Name')}} *</span>
                                    <input type="text" name="lname" value="{{old('lname')}}" id="last_name">
                                    @if(Session::has('err'))
                                    <p class="text-danger mb-2 mt-2">{{Session::get('err')}}</p>
                                    @endif
                                    @error('lname')
                                    <p class="text-danger mb-2 mt-2">{{ convertUtf8($message) }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="input-box">
                            <span>{{__('Email')}} *</span>
                            <input type="email" name="email" value="{{old('email')}}">
                            @if ($errors->has('email'))
                            <p class="text-danger mb-0 mt-2">{{$errors->first('email')}}</p>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-box">
                                    <span>{{__('Password')}} *</span>
                                    <div class="position-relative password">
                                        <input type="password" name="password" value="{{old('password')}}" id="password" />
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

                                    @if ($errors->has('password'))
                                    <p class="text-danger mb-0 mt-2">{{$errors->first('password')}}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-box">
                                    <span>{{__('Confirmation Password')}} *</span>
                                        <div class="position-relative password">
                                            <input type="password" name="password_confirmation"
                                            value="{{old('password_confirmation')}}" id="confirmpassword" />

                                            <button type="button" class="password_view"
                                            onclick="passwordView('confirm')">
                                                <svg id="confirmpassword_hide" style="display: none;" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                                </svg>
                                                <svg id="confirmpassword_view" width="16" height="16" fill="currentColor" class="bi bi-eye-slash" viewBox="0 0 16 16">
                                                    <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l.708.709z"/>
                                                    <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z"/>
                                                    <path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12-.708.708z"/>
                                                </svg>
                                            </button>
                                        </div>

                                    @if ($errors->has('password_confirmation'))
                                    <p class="text-danger mb-0 mt-2">{{$errors->first('password_confirmation')}}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="input-box">
                                    <span>{{__('Date of birth')}} *</span>
                                    <input type="text" name="date_of_birth" id="date_of_birth" class="form-control datepicker_class" value="{{old('date_of_birth')}}" placeholder="dd-mm-yyyy"/>
                                    @if ($errors->has('date_of_birth'))
                                    <p class="text-danger mb-0 mt-2">{{$errors->first('date_of_birth')}}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-box">
                                    <span>{{__('Age')}} *</span>
                                    <input type="number" min="18" name="age" value="{{old('age')}}" id="user_age" readonly aria-readonly="true">
                                    @if ($errors->has('age'))
                                    <p class="text-danger mb-0 mt-2">{{$errors->first('age')}}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="input-box">
                                    <span>{{__('Address')}} *</span>
                                    <span data-toggle="tooltip" data-placement="top" title="{{ __ ("Insert full address including Company's Name")}}">
                                        <svg width="16" height="16" fill="currentColor" class="bi bi-question-circle-fill" viewBox="0 0 16 16">
                                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.496 6.033h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286a.237.237 0 0 0 .241.247zm2.325 6.443c.61 0 1.029-.394 1.029-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94 0 .533.425.927 1.01.927z"/>
                                        </svg>
                                    </span>
                                    <input type="text" name="address" value="{{old('address')}}">
                                    @if ($errors->has('address'))
                                    <p class="text-danger mb-0 mt-2">{{$errors->first('address')}}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-box">
                                    <span>{{__('Postcode')}} *</span>
                                    <input type="text" name="city" value="{{old('city')}}">
                                    @if ($errors->has('city'))
                                    <p class="text-danger mb-0 mt-2">{{$errors->first('city')}}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-box">
                                    <span>{{__('State')}} *</span>
                                    <select name="state" id="state">
                                        @foreach (stateList() as $item)
                                            <option {{ (old('state') && old('state') == $item) ? 'selected' : '' }} value="{{ $item }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('state'))
                                    <p class="text-danger mb-0 mt-2">{{$errors->first('state')}}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-box">
                                    <span>{{__('Country')}} *</span>
                                    <select name="country" id="country">
                                        <option value="Malaysia">Malaysia</option>
                                        <option value="Other">Other</option>
                                    </select>
                                    @if ($errors->has('state'))
                                    <p class="text-danger mb-0 mt-2">{{$errors->first('state')}}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-box">
                                    <span>{{__('Gender')}} *</span>
                                    <select name="gender" id="gender">
                                        <option value="">Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                    @if ($errors->has('gender'))
                                    <p class="text-danger mb-0 mt-2">{{$errors->first('gender')}}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-box">
                                    <span>{{__('Nation (Race)')}} *</span>
                                    <input type="text" name="nation" value="{{old('nation')}}">
                                    @if ($errors->has('nation'))
                                    <p class="text-danger mb-0 mt-2">{{$errors->first('nation')}}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-box">
                                    <span>{{__('Contact Number')}} *</span>
                                    <input type="text" name="personal_phone" value="{{old('personal_phone')}}">
                                    @if ($errors->has('personal_phone'))
                                    <p class="text-danger mb-0 mt-2">{{$errors->first('personal_phone')}}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-box">
                                    <span>{{__('Company Fax')}} *</span>
                                    <input type="text" name="company_fax" value="{{old('company_fax')}}">
                                    @if ($errors->has('company_fax'))
                                    <p class="text-danger mb-0 mt-2">{{$errors->first('company_fax')}}</p>
                                    @endif
                                </div>
                            </div>
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
                            <button type="submit" class='submit_btn_load'>{{__('Register')}}</button>
                            <p>{{__('Already have an account ?')}} <a href="{{route('user.login')}}">{{__('Click Here')}}</a> {{__('to login')}}.</p>
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
    <script src="{{asset('/assets/flatpickr/flatpickr.min.js')}}"></script>
    <script>
        const pickerDateFormat = 'd-m-Y';
        flatpickr('.datepicker_class', {
            dateFormat: pickerDateFormat,
            maxDate: new Date()
        });

        $('#date_of_birth').on('change', function () {
            const dateOfBirth = $(this).val();
            const dateFromFlatpicker = flatpickr.parseDate(dateOfBirth, pickerDateFormat);
            const formatedDate = flatpickr.formatDate(dateFromFlatpicker, "Y-m-d");
            const dob = dayjs(formatedDate, "YYYY-MM-DD");
            const today = dayjs();
            const age = today.diff(dob, 'year');
            $('#user_age').val(age);
            // $(this).val(formatedDate);
        });
    </script>
@endsection
