<div class="col-lg-3">
    <div class="user-sidebar">
        <div class="mb-3">
            @if (auth()->user() && strpos(auth()->user()->photo, 'facebook') !== false || strpos(auth()->user()->photo, 'google'))
            <div class="text-center">
                <img class="showimage rounded-circle" src="{{ auth()->user()->photo ? auth()->user()->photo : asset('assets/front/img/user/profile-img.png') }}" alt="user-image">
            </div>
            @else
            <div class="text-center">
                <img class="showimage rounded-circle" src="{{ auth()->user()->photo ? asset('assets/front/img/user/' . auth()->user()->photo) : asset('assets/front/img/user/profile-img.png') }}" alt="user-image">
            </div>
            @endif
        </div>
        <ul class="links">
            <li>
                <a class="@if (request()->path() == 'user/dashboard') active @endif" href="{{ route('user-dashboard') }}">{{ __('Home') }}</a>
            </li>
            <li>
                <a class="@if (request()->path() == 'user/notification') active @endif" href="{{ route('user-notification') }}">
                    {{ __('My Notifications') }}
                    @if (request()->user()->unreadNotifications()->count())
                        <span class="badge badge-info float-right">{{request()->user()->unreadNotifications()->count()}}</span>
                    @endif
                </a>
            </li>
            <li>
                <a class="@if (Route::is('user-upcoming-events')) active @endif" href="{{ route('user-upcoming-events') }}">
                    {{ __('Upcoming Event') }}
                </a>
            </li>
            <li>
                <a class="@if (request()->path() == 'user/events') active @elseif(request()->is('user/event/*')) active @endif" href="{{ route('user-events') }}">
                    {{ __('Event History') }}</a>
            </li>
            <li>
                <a class="@if (request()->path() == 'user/attendance') active @elseif(request()->is('user/attendance/*')) active @endif" href="{{ route('user-attendance') }}">
                    {{ __('Attendance History') }}</a>
            </li>
            <li>
                <a class="@if (request()->path() == 'user/payments') active @endif" href="{{ route('user-payments') }}">
                    {{ __('My Payments') }}
                </a>
            </li>
            <li>
                <a class="@if (request()->path() == 'user/cpd-points') active @endif" href="{{ route('user-cpdhours') }}">
                    {{ __('My CPD Points') }}
                </a>
            </li>
            <li>
                <a class="@if (request()->path() == 'member-directory') active @endif" href="{{route('front.member.directory')}}">{{__('Member Directory')}}</a>
            </li>
            <li>
                <a class="@if (request()->path() == 'user/profile') active @endif" href="{{ route('user-profile') }}">{{ __('Update Profile') }}</a>
            </li>
            <li>
                <a class="@if (request()->path() == 'user/orders') active @elseif(request()->is('user/order/*')) active @endif" href="{{ route('user-orders') }}">
                    {{ __('Publication Orders') }}
                </a>
            </li>
            <li>
                <a class="@if (request()->path() == 'user/course_orders') active @endif" href="{{ route('user.course_orders') }}">
                    {{ __('Courses') }}</a>
            </li>

            <li><a href="{{ route('user-logout') }}">{{ __('Logout') }}</a></li>
        </ul>
    </div>
</div>
