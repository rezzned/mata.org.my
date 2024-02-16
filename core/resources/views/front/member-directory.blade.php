@extends("front.$version.layout")

@section('pagename')
- {{__('Member Directory')}}
@endsection

@section('meta-keywords', __('Member Directory'))
@section('meta-description', __('Member Directory'))

@push('styles')
<style>
    .show_dir_image{
        border-radius: 50% !important;
    }
</style>
@endpush

@section('content')
<!--   hero area start   -->
<!--   hero area end    -->


<!--====== CHECKOUT PART START ======-->
<section class="" style="padding: 120px 0 100px">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="card bg-transparent">
                    <div class="card-body">
                        <div class="title d-flex justify-content-between">
                            <h4>{{__('Member Directory')}}
                            </h4>
                            <form action="{{route('front.member.directory')}}" method="get">
                                <div class="form-inline">
                                    <select name="sort" id="sort" class="form-control mr-2" onchange="this.form.submit()">
                                        <option value="a-z">A-Z</option>
                                        <option value="z-a">Z-A</option>
                                    </select>
                                    <select name="state" onchange="this.form.submit()" class="form-control">
                                        <option value="all">{{ __('All State') }}</option>
                                        @foreach (stateList() as $item)
                                            <option {{ ($state == $item) ? 'selected' : '' }} value="{{ $item }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </form>
                        </div>
                        <div class="main-info">
                            <div class="main-table">
                                <div class="table-responsiv">
                                    <table class="dt-responsive table table-striped table-bordered dt-bootstrap4"
                                        style="width:100%">
                                        <thead>
                                            <tr>
                                                <th width="100">{{__('Picture')}}</th>
                                                <th>{{__('Full name')}}</th>
                                                <th>{{__('Address')}}</th>
                                                <th>{{__('Contact Number')}}</th>
                                                <th>{{__('Company Fax')}}</th>
                                                <th>{{__('Email')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(count($users))
                                            @foreach ($users as $user)
                                            <tr>
                                                <td>
                                                    @if (strpos($user->photo, 'facebook') !== false || strpos($user->photo, 'google'))
                                                    <div class="text-center">
                                                        <img class="show_dir_image w-100" src="{{ $user->photo ? $user->photo : asset('assets/front/img/user/profile-img.png') }}" alt="user-image">
                                                    </div>
                                                    @else
                                                    <div class="text-center">
                                                        <img class="show_dir_image rounded w-100" src="{{ $user->photo ? asset('assets/front/img/user/' . $user->photo) : asset('assets/front/img/user/profile-img.png') }}" alt="user-image">
                                                    </div>
                                                    @endif
                                                </td>
                                                <td>{{$user->fname.' '.$user->lname}}</td>
                                                <td>{{$user->address}}</td>
                                                <td>{{$user->personal_phone}}</td>
                                                <td>{{$user->company_fax}}</td>
                                                <td>{{$user->email}}</td>
                                            </tr>
                                            @endforeach
                                            @else
                                            <tr class="text-center">
                                                <td colspan="6">
                                                    {{__('No Member found')}}
                                                </td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                    {{ $users->withQueryString()->appends(['state' => $state])->links() }}
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

@endsection
