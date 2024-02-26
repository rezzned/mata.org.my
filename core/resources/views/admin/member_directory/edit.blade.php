@extends('admin.layout')
@section('content')
<div class="page-header">
    <h4 class="page-title">{{__('Member Directory Details')}}</h4>
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
            <a href="{{ url()->previous() }}">Member Directory</a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="#">{{__('Member Directory Details')}}</a>
        </li>
    </ul>

    <a href="{{ url()->previous() }}" class="btn-md btn btn-primary" style="margin-left: auto;">Back</a>
</div>
<div class="card">
    <div class="card-header">
        <div class="h4 card-title">
            Profile Picture
        </div>
    </div>
    <div class="card-body py-4">
        <div class="upload-img">
            @if (strpos($user->photo, 'facebook') !== false || strpos($user->photo, 'google'))
                <div class="img-box">
                    <img class="showimage" width="150" src="{{$user->photo ? $user->photo : asset('assets/front/img/user/profile-img.png')}}" alt="user-image">
                </div>
            @else
                <div class="img-box">
                    <img class="showimage" width="150" src="{{$user->photo ? asset('assets/front/img/user/'.$user->photo) : asset('assets/front/img/user/profile-img.png')}}" alt="user-image">
                </div>
            @endif
            <div class="file-upload-area">
                <div class="upload-file">
                    <input type="file" name="photo" id="image" accept="image/*" class="upload image">
                    <span>{{__('Upload')}}</span>
                </div>
                @error('photo')
                    <p class="text-danger" >{{ convertUtf8($message) }}</p>
                @enderror
            </div>
        </div>
        <form action="{{route('register.user-member.update',$user->id)}}" method="POST">
            @csrf
            <div class="row my-3">
                <div class="col-md-6">
                    <div class="input-box mb-sm-2">
                        <span>{{__('First Name')}} *</span>
                        <input class='form-control' type="text" name="fname" value="{{$user->fname}}"
                            id="first_name">
                        @if(Session::has('err'))
                        <p class="text-danger mb-2 mt-2">{{Session::get('err')}}</p>
                        @endif
                        @error('fname')
                        <p class="text-danger mb-2 mt-2">{{ convertUtf8($message) }}</p>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-box mb-sm-2">
                        <span>{{__('Last Name')}} *</span>
                        <input class='form-control' type="text" name="lname" value="{{$user->lname}}"
                            id="last_name">
                        @if(Session::has('err'))
                        <p class="text-danger mb-2 mt-2">{{Session::get('err')}}</p>
                        @endif
                        @error('lname')
                        <p class="text-danger mb-2 mt-2">{{ convertUtf8($message) }}</p>
                        @enderror
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="input-box mb-sm-2">
                        <span>{{__('Email')}} *</span>
                        <input class='form-control' type="email" name="email" value="{{$user->email}}">
                        @if ($errors->has('email'))
                        <p class="text-danger mb-0 mt-2">{{$errors->first('email')}}</p>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-box mb-sm-2">
                        <span>{{__('Date of birth')}} *</span>
                        <input class='form-control' type="date" name="date_of_birth" id="date_of_birth" value="{{$user->date_of_birth}}" pattern="yyyy-mm-dd">
                        @if ($errors->has('date_of_birth'))
                        <p class="text-danger mb-0 mt-2">{{$errors->first('date_of_birth')}}</p>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-box mb-sm-2">
                        <span>{{__('Age')}} *</span>
                        <input class='form-control' type="number" min="18" name="age" value="{{ old('age', carbon_parse($user->date_of_birth)->age) }}" id="user_age" readonly aria-readonly="true">
                        @if ($errors->has('age'))
                        <p class="text-danger mb-0 mt-2">{{$errors->first('age')}}</p>
                        @endif
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="input-box mb-sm-2">
                        <span>{{__('Address')}} *</span>
                        <input class='form-control' type="text" name="address" value="{{$user->address}}">
                        @if ($errors->has('address'))
                        <p class="text-danger mb-0 mt-2">{{$errors->first('address')}}</p>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-box mb-sm-2">
                        <span>{{__('Postcode')}} *</span>
                        <input class='form-control' type="text" name="city" value="{{$user->city}}">
                        @if ($errors->has('city'))
                        <p class="text-danger mb-0 mt-2">{{$errors->first('city')}}</p>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-box mb-sm-2">
                        <span>{{__('State')}} *</span>
                        <select name="state" id="state" class="form-control">
                            @foreach (stateList() as $item)
                                <option {{ ($user->state == $item) ? 'selected' : '' }} value="{{ $item }}">{{ $item }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('state'))
                        <p class="text-danger mb-0 mt-2">{{$errors->first('state')}}</p>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-box mb-sm-2">
                        <span>{{__('Country')}} *</span>
                        <select name="country" id="country" class="form-control">
                            <option {{ $user->country == 'Malaysia' ? 'selected' : ''}} value="Malaysia">Malaysia</option>
                            <option {{ $user->country == 'Other' ? 'selected' : ''}} value="Other">Other</option>
                        </select>
                        @if ($errors->has('country'))
                        <p class="text-danger mb-0 mt-2">{{$errors->first('country')}}</p>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-box mb-sm-2">
                        <span>{{__('Gender')}} *</span>
                        <select name="gender" id="gender" class="form-control">
                            <option value="">Select Gender</option>
                            <option {{ $user->gender == 'male' ? 'selected' : ''}} value="male">Male</option>
                            <option {{ $user->gender == 'female' ? 'selected' : ''}} value="female">Female</option>
                        </select>
                        @if ($errors->has('gender'))
                        <p class="text-danger mb-0 mt-2">{{$errors->first('gender')}}</p>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-box mb-sm-2">
                        <span>{{__('Nation (Race)')}} *</span>
                        <input class='form-control' type="text" name="nation" value="{{$user->nation}}">
                        @if ($errors->has('nation'))
                        <p class="text-danger mb-0 mt-2">{{$errors->first('nation')}}</p>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="input-box mb-sm-2">
                        <span>{{__('Contact Number')}} *</span>
                        <input class='form-control' type="text" name="personal_phone"
                            value="{{$user->personal_phone}}">
                        @if ($errors->has('personal_phone'))
                        <p class="text-danger mb-0 mt-2">{{$errors->first('personal_phone')}}</p>
                        @endif
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="input-box mb-sm-2">
                        <span>{{__('Member Directory')}} *</span>
                        <select name="member_directory" id="member_directory" class="form-control">
                            <option value="Not Active" @if($member_directory == 'Not Active') Selected @endif>Not Active</option>
                            <option value="Active" @if($member_directory == 'Active') Selected @endif>Active</option>
                        </select>
                        @if ($errors->has('member_directory'))
                        <p class="text-danger mb-0 mt-2">{{$errors->first('member_directory')}}</p>
                        @endif
                    </div>
                </div>

            </div>

            <div class="input-btn text-center">
                <button type="submit" class="btn btn-success">{{__('Update')}}</button>
            </div>
        </form>
    </div>

</div>
@endsection

@push('footer-js')
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

        $('#date_of_birth').on('change', function () {
            // dayjs.extend(dayjs_plugin_duration);
            const dateOfBirth = $(this).val();
            const dob = dayjs(dateOfBirth);
            const today = dayjs();
            const age = today.diff(dob, 'year');
            $('#user_age').val(age);
        });
    </script>
@endpush
