@if (Request::is('user*'))
@php
    $subscription = auth()->user()->subscription;
@endphp
@if ($subscription && carbon_parse($subscription->expire_date) <= today() && $subscription->status != 3)
<div class="modal" tabindex="-1" role="dialog" id="membership_notify_model" data-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="background: #27334f">
            <div class="modal-header border-0"></div>
            <div class="modal-body" style="min-height: 200px">
                <div class="d-flex">
                    <div class="px-4">
                        <i class="fa fa-exclamation-triangle fa-5x text-warning"></i>
                    </div>
                    <div>
                        <p class="h3 text-white mb-3">Your membership licence has been expired</p>
                        <p class="h4">Your expired membership: {{ $subscription->current_package->title }}</p>
                        <p class="h5">You have {{ currency_format($subscription->current_package->price) }} overdue payment.</p>
                        <p class="h6">Please pay the amount to continue your membership.</p>
                        @if ($subscription->pending_package_id)
                        <span class="bg-info rounded text-white px-3 py-2 mt-4 d-inline-block">{{ __('Your payment request is under review') }}</span>
                        @else
                        <a class="btn btn-primary mt-4" href="{{ route('front.packageorder.index', [$subscription->current_package_id]) }}">{{ __('Pay') }}</a>
                        <form action="{{ route('user-cancel-membership') }}" class="d-inline">
                            @csrf
                            <input type="hidden" name="subscription_id" value="{{ $subscription->id }}" />
                            <button type="submit" class="btn btn-info mt-4 submit_btn_load">{{ __('Cancel') }}</button>
                        </form>
                        @endif
                        <div class="mt-4">
                            <a class="text-decoration-none" href="{{ route('user-logout') }}">
                                <i class="fa fa-sign-out"></i> {{ __('Logout') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0"></div>
        </div>
    </div>
</div>
<script>
    $(function () {
        $('#membership_notify_model').modal({keyboard: false});
        $('html, body').css({overflow: 'hidden', height: '100%'});
    });
</script>
@endif
@endif
