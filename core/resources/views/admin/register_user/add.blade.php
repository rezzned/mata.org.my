@extends('admin.layout')

@section('content')
<div class="page-header">
    <h4 class="page-title">Password</h4>
    <ul class="breadcrumbs">
        <li class="nav-home">
            <a href="#">
                <i class="flaticon-home"></i>
            </a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="#">Registered Users</a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="#">Password</a>
        </li>
    </ul>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            {{ $errors }}
            <form action="{{route('admin.register.user.save')}}" method="post" role="form">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6">
                            <div class="card-title">@lang('Add New User')</div>
                        </div>
                        <div class="col-6 text-right">
                            <a href="{{route('admin.register.user')}}" class="btn btn-sm btn-primary">Back</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6 offset-lg-3">
                            @csrf
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{__('First Name')}} *</label>
                                            <input type="text" name="fname" class="form-control" value="{{old('fname')}}" id="first_name">
                                            @if(Session::has('err'))
                                            <p class="text-danger mb-2 mt-2">{{Session::get('err')}}</p>
                                            @endif
                                            @error('fname')
                                            <p class="text-danger mb-2 mt-2">{{ convertUtf8($message) }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{__('Last Name')}} *</label>
                                            <input type="text" name="lname" class="form-control" value="{{old('lname')}}" id="last_name">
                                            @if(Session::has('err'))
                                            <p class="text-danger mb-2 mt-2">{{Session::get('err')}}</p>
                                            @endif
                                            @error('lname')
                                            <p class="text-danger mb-2 mt-2">{{ convertUtf8($message) }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>{{__('Email')}} *</label>
                                    <input type="email" name="email" class="form-control" value="{{old('email')}}">
                                    @if ($errors->has('email'))
                                    <p class="text-danger mb-0 mt-2">{{$errors->first('email')}}</p>
                                    @endif
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{__('Password')}} *</label>
                                            <div class="position-relative password">
                                                <input type="password" class="form-control" name="password" value="{{old('password')}}" id="password" />
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
                                        <div class="form-group">
                                            <label>{{__('Confirmation Password')}} *</label>
                                                <div class="position-relative password">
                                                    <input type="password" class="form-control" name="password_confirmation"
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
                                        <div class="form-group">
                                            <label>{{__('Date of birth')}} *</label>
                                            <input type="date" name="date_of_birth" id="date_of_birth" class="form-control" class="form-control datepicker_class" value="{{old('date_of_birth')}}" placeholder="dd-mm-yyyy" />
                                            @if ($errors->has('date_of_birth'))
                                            <p class="text-danger mb-0 mt-2">{{$errors->first('date_of_birth')}}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{__('Age')}} *</label>
                                            <input type="number" class="form-control" min="18" name="age" value="{{ old('age', carbon_parse(request('date_of_birth'))->age) }}" id="user_age" readonly aria-readonly="true">
                                            @if ($errors->has('age'))
                                            <p class="text-danger mb-0 mt-2">{{$errors->first('age')}}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>{{__('Address')}} *</label>
                                            <input type="text" class="form-control" name="address" value="{{old('address')}}">
                                            @if ($errors->has('address'))
                                            <p class="text-danger mb-0 mt-2">{{$errors->first('address')}}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{__('Postcode')}} *</label>
                                            <input type="text" class="form-control" name="city" value="{{old('city')}}">
                                            @if ($errors->has('city'))
                                            <p class="text-danger mb-0 mt-2">{{$errors->first('city')}}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{__('State')}} *</label>
                                            <select name="state" class="form-control" id="state">
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
                                        <div class="form-group">
                                            <label>{{__('Country')}} *</label>
                                            <select name="country" class="form-control" id="country">
                                                <option value="Malaysia">Malaysia</option>
                                                <option value="Other">Other</option>
                                            </select>
                                            @if ($errors->has('state'))
                                            <p class="text-danger mb-0 mt-2">{{$errors->first('state')}}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{__('Gender')}} *</label>
                                            <select name="gender" class="form-control" id="gender">
                                                <option value="">Select Gender</option>
                                                <option value="male" {{ old('gender') == 'male' }}>Male</option>
                                                <option value="female" {{ old('gender') == 'female' }}>Female</option>
                                            </select>
                                            @if ($errors->has('gender'))
                                            <p class="text-danger mb-0 mt-2">{{$errors->first('gender')}}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{__('Nation (Race)')}} *</label>
                                            <input type="text" class="form-control" name="nation" value="{{old('nation')}}">
                                            @if ($errors->has('nation'))
                                            <p class="text-danger mb-0 mt-2">{{$errors->first('nation')}}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{__('Contact Number')}} *</label>
                                            <input type="text" class="form-control" name="personal_phone" value="{{old('personal_phone')}}">
                                            @if ($errors->has('personal_phone'))
                                            <p class="text-danger mb-0 mt-2">{{$errors->first('personal_phone')}}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{__('Company Fax')}} *</label>
                                            <input type="text" class="form-control" name="company_fax" value="{{old('company_fax')}}">
                                            @if ($errors->has('company_fax'))
                                            <p class="text-danger mb-0 mt-2">{{$errors->first('company_fax')}}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-success">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

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
    {{-- <script src="{{asset('assets/front/js/dayjs_plugings/duration.min.js')}}"></script> --}}
    <script>
        flatpickr('.datepicker_class', {
            dateFormat: 'd-m-Y',
            maxDate: new Date()
        });
        $('#date_of_birth').on('change', function () {
            // dayjs.extend(dayjs_plugin_duration);
            const dateOfBirth = $(this).val();
            const dob = dayjs(dateOfBirth);
            const today = dayjs();
            const age = today.diff(dob, 'year');
            $('#user_age').val(age);
        });
    </script>
@endsection
