@extends('user.layout')
@section('styles')
    <link rel="stylesheet" href="{{asset('/assets/flatpickr/flatpickr.min.css')}}" />
    <style>
        input.form_control {
            color: #000 !important;
        }
    </style>
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
                    <div class="row mb-5">
                        <div class="col-lg-12">
                            <div class="user-profile-details">
                                <div class="account-info">
                                    <div class="title">
                                        <h4>{{__('Edit Profile')}}</h4>
                                    </div>
                                    <div class="edit-info-area">
                                        @if ($errors->any())
                                            <div class="alert alert-danger">
                                                <ul>
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                        <form action="{{route('user-profile-update')}}" method="POST" enctype="multipart/form-data" >
                                            @csrf

                                            <div class="upload-img">
                                                @if (strpos($user->photo, 'facebook') !== false || strpos($user->photo, 'google'))
                                                    <div class="img-box">
                                                        <img class="showimage" src="{{$user->photo ? $user->photo : asset('assets/front/img/user/profile-img.png')}}" alt="user-image">
                                                    </div>
                                                @else
                                                    <div class="img-box">
                                                        <img class="showimage" src="{{$user->photo ? asset('assets/front/img/user/'.$user->photo) : asset('assets/front/img/user/profile-img.png')}}" alt="user-image">
                                                    </div>
                                                @endif
                                                <div class="file-upload-area w-100">
                                                    <div class="upload-file">
                                                        <input type="file" name="photo" id="image" accept="image/*" class="upload image">
                                                        <span>{{__('Upload')}}</span>
                                                    </div>
                                                    @error('photo')
                                                        <p class="text-danger" >{{ convertUtf8($message) }}</p>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <span>{{__('First Name')}} *</span>
                                                    <input type="text" class="form_control" placeholder="{{__('First Name')}}" name="fname" value="{{convertUtf8($user->fname)}}" value="{{Request::old('fname')}}">
                                                    @error('fname')
                                                        <p class="text-danger mb-4">{{ convertUtf8($message) }}</p>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-6">
                                                    <span>{{__('Last Name')}} *</span>
                                                    <input type="text" class="form_control" placeholder="{{__('Last Name')}}" name="lname" value="{{convertUtf8($user->lname)}}" value="{{Request::old('lname')}}">
                                                    @error('lname')
                                                        <p class="text-danger mb-4">{{ convertUtf8($message) }}</p>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-6">
                                                    <span>{{__('Username')}} *</span>
                                                    <input type="text" class="form_control" placeholder="{{__('Username')}}" name="username" value="{{convertUtf8($user->username)}}" value="{{Request::old('username')}}">
                                                    @error('username')
                                                        <p class="text-danger mb-4">{{ convertUtf8($message) }}</p>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-6">
                                                    <span>{{__('Email')}} *</span>
                                                    <input type="email" class="form_control" placeholder="{{__('Email')}}" name="email" disabled value="{{convertUtf8($user->email)}}" value="{{Request::old('email')}}">
                                                </div>
                                                <div class="col-lg-6">
                                                    <span>{{__('Contact Number')}} *</span>
                                                    <input type="text" class="form_control" placeholder="{{__('Phone')}}" name="personal_phone" value="{{$user->personal_phone}}" value="{{Request::old('personal_phone')}}">
                                                    @error('number')
                                                    <p class="text-danger mb-4">{{ convertUtf8($message) }}</p>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-6">
                                                    <span>{{__('Postcode')}} *</span>
                                                    <input type="text" class="form_control" placeholder="{{__('City')}}" name="city" value="{{convertUtf8($user->city)}}" value="{{Request::old('city')}}">
                                                    @error('city')
                                                    <p class="text-danger mb-4">{{ convertUtf8($message) }}</p>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-6">
                                                    <span>{{__('State')}} *</span>
                                                    <select name="state" id="state">
                                                        @foreach (stateList() as $item)
                                                            <option {{ ($user->state == $item) ? 'selected' : '' }} value="{{ $item }}">{{ $item }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('state')
                                                    <p class="text-danger mb-4">{{ convertUtf8($message) }}</p>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-6">
                                                    <span>{{__('Country')}} *</span>
                                                    <input type="text" class="form_control" placeholder="{{__('Country')}}" name="country" value="{{convertUtf8($user->country)}}" value="{{Request::old('country')}}">
                                                    @error('country')
                                                    <p class="text-danger mb-4">{{ convertUtf8($message) }}</p>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-12">
                                                    <span>{{__('Address')}} *</span>
                                                    <input type="text" name="address" class="form_control" placeholder="{{__('Address')}}" value="{{old('address', convertUtf8($user->address))}}">
                                                    @error('address')
                                                    <p class="text-danger">{{ convertUtf8($message) }}</p>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-6">
                                                    <span>{{__('Date of birth')}} *</span>
                                                    <input type="date" name="date_of_birth" class="form-control datepicker_dob_class" placeholder="dd-mm-yyyy" value="{{ dateFormat(old('date_of_birth', $user->date_of_birth), 'Y-m-d') }}" id="date_of_birth">
                                                    @if ($errors->has('date_of_birth'))
                                                    <p class="text-danger mb-0 mt-2">{{$errors->first('date_of_birth')}}</p>
                                                    @endif
                                                </div>
                                                <div class="col-lg-6">
                                                    <span>{{__('Age')}} *</span>
                                                    <input type="number" min="18" name="age" value="{{ old('age', carbon_parse($user->date_of_birth)->age) }}" readonly aria-readonly="true" id="user_age">
                                                    @if ($errors->has('age'))
                                                    <p class="text-danger mb-0 mt-2">{{$errors->first('age')}}</p>
                                                    @endif
                                                </div>
                                                <div class="col-lg-6">
                                                    <span>{{__('Gender')}} *</span>
                                                    <select name="gender" id="gender">
                                                        <option value="">Select Gender</option>
                                                        <option value="male" {{ !($user->gender == "male") ?: 'selected' }}>Male</option>
                                                        <option value="female" {{ !($user->gender == "female") ?: 'selected' }}>Female</option>
                                                    </select>
                                                    @if ($errors->has('gender'))
                                                    <p class="text-danger mb-0 mt-2">{{$errors->first('gender')}}</p>
                                                    @endif
                                                </div>
                                                <div class="col-lg-6">
                                                    <span>{{__('Nation (Race)')}} *</span>
                                                    <input type="text" name="nation" value="{{Request::old('nation', convertUtf8($user->nation))}}">
                                                    @if ($errors->has('nation'))
                                                    <p class="text-danger mb-0 mt-2">{{$errors->first('nation')}}</p>
                                                    @endif
                                                </div>
                                                <div class="col-lg-6">
                                                    <span>{{__('Company Fax')}} *</span>
                                                    <input type="text" name="company_fax" value="{{Request::old('company_email', convertUtf8($user->company_fax))}}">
                                                    @if ($errors->has('company_fax'))
                                                    <p class="text-danger mb-0 mt-2">{{$errors->first('company_fax')}}</p>
                                                    @endif
                                                </div>
                                                <div id="license_expire_date" class="col-sm-12 mb-3"></div>
                                                <div class="col-lg-6">
                                                    <span>{{__('License ID')}} *</span>
                                                    <input type="text" name="license_id" value="{{Request::old('license_id', convertUtf8($user->license_id))}}">
                                                    @if ($errors->has('license_id'))
                                                    <p class="text-danger mb-0 mt-2">{{$errors->first('license_id')}}</p>
                                                    @endif
                                                </div>
                                                <div class="col-lg-6">
                                                    <span>{{__('License Expire Date')}} *</span>
                                                    @if ($user->license_expire_date)
                                                    <input type="text" name="license_expire_date" class='form-control datepicker_class' placeholder="dd-mm-yyyy" style="background:transparent"
                                                    value="{{ carbon_parse(old('license_expire_date', $user->license_expire_date))->format('d-m-Y') }}"/>
                                                    @else
                                                    <input type="text" name="license_expire_date" class='form-control datepicker_class' placeholder="dd-mm-yyyy" readonly style="background:transparent"/>
                                                    @endif
                                                    @if ($errors->has('license_expire_date'))
                                                    <p class="text-danger mb-0 mt-2">{{$errors->first('license_expire_date')}}</p>
                                                    @endif
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="form-button mt-4">
                                                        <button type="submit" class="btn form-btn">{{__('Submit')}}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
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
        $(document.body).on('click','.upload-file span',function(){
            $('#image').trigger('click');
        });
        $(document.body).on('change','#image',function(){
            var file = event.target.files[0];
            var reader = new FileReader();
            reader.onload = function(e) {
                $('.showimage').attr('src',e.target.result)
            };

        reader.readAsDataURL(file);
        })
    </script>
    <script src="{{asset('/assets/flatpickr/flatpickr.min.js')}}"></script>
    <script>
        $(function($) {
            const pickerDateFormat = 'd-m-Y';

            function setPickerDate(elementId, date_from_db, pickerDateFormat = 'd-m-Y') {
                const date_input = document.getElementById('date_of_birth');
                const date_obj = dayjs(date_from_db, "YYYY-MM-DD");
                date_input.value = date_obj.format("DD-MM-YYYY");
                date_input.dispatchEvent(new Event('change'));
                flatpickr('#' + elementId, {
                    dateFormat: pickerDateFormat,
                    maxDate: new Date(),
                    defaultDate: date_obj.format("DD-MM-YYYY")
                });
            }

            function calculateAge(dateOfBirth, pickerDateFormat = 'd-m-Y'){
                if(dateOfBirth == "" || dateOfBirth == null) return $('#user_age').val(0);

                const dateFromFlatpicker = flatpickr.parseDate(dateOfBirth, pickerDateFormat);
                const formatedDate = flatpickr.formatDate(dateFromFlatpicker, "Y-m-d");
                const dob = dayjs(formatedDate, "YYYY-MM-DD");
                const today = dayjs();
                const age = today.diff(dob, 'year');

                $('#user_age').val(age);
            }

            $('#date_of_birth').on('change input', function () {
                const dateOfBirth = $(this).val();
                calculateAge(dateOfBirth, pickerDateFormat);
            });

            flatpickr('.datepicker_class', {
                dateFormat: pickerDateFormat,
            });

            flatpickr('.datepicker_dob_class', {
                dateFormat: pickerDateFormat,
                maxDate: new Date(),
            });

            setPickerDate("date_of_birth", "{{ dateFormat($user->date_of_birth, 'Y-m-d') }}");
        });
    </script>
@endsection
