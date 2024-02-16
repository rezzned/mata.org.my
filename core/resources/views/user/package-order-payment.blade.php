@extends("front.$version.layout")

@section('pagename')
 - {{__('Order of') . ' ' . $package->title}}
@endsection

@section('meta-keywords', "$package->meta_keywords")
@section('meta-description', "$package->meta_description")

@push('styles')
    <style>
        .hand {
            cursor: pointer;
        }
        .myr_razerms {
            border-radius: 10px !important;
        }
        .myr_razerms label{
            overflow: hidden;
        }
        .myr_razerms img{
            border: 3px solid transparent;
            border-radius: 10px !important;
        }
        .myr_razerms img:hover{
            transform: scale(1.01);
            border-radius: 10px !important;
        }
        .myr_razerms input:checked+label img {
            border: 3px solid #0787ff;
        }
    </style>
@endpush

@section('content')
    @section('breadcrumb-title', __('Package Order'))
    @section('breadcrumb-subtitle')
    {{__('Place Order for')}} <p class="d-inline-block" style="color:#{{$bs->base_color}};">{{convertUtf8($package->title)}}</p>
    @endsection
    @section('breadcrumb-link', __('Package Order'))

  <!--   quote area start   -->
  <div class="quote-area pt-110 pb-115">
    <div class="container">
      <div class="row">
        <div class="col-lg-8">
          <form
            class="pay-form"
            action=""
            enctype="multipart/form-data" method="POST"
            role="molpayseamless" >

            @csrf
            <input type="hidden" name="package_id" value="{{$package->id}}">

            @if (count($gateways) + count($ogateways) > 0)
            <div class="row mb-4">
                <div class="col-lg-6">
                    <div class="form-element mb-2">
                        <label>{{__('Pay Via')}}  <span>**</span></label>
                        <select name="method" id="method" class="option input-field" required="">
                            @foreach($gateways as $paydata)
                                <option value="{{ $paydata->name }}" data-form="{{ $paydata->showCheckoutLink() }}" data-show="{{ $paydata->showForm() }}" data-href="{{ route('front.load.payment', ['slug1' => $paydata->showKeyword(),'slug2' => $paydata->id]) }}" data-val="{{ $paydata->keyword }}">
                                    {{$paydata->name}}
                                </option>
                            @endforeach

                            @if (!empty($ogateways))
                                @foreach($ogateways as $ogateway)
                                    <option value="{{ $ogateway->id }}" data-form="{{ route('front.offline.submit', $ogateway->id) }}" data-show="yes" data-href="{{ route('front.load.payment',['slug1' => "offline",'slug2' => $ogateway->id]) }}" data-val="offline">
                                        {{ $ogateway->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>

                        @if ($errors->has('receipt'))
                            <p class="text-danger">{{$errors->first('receipt')}}</p>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- show payment form here if available --}}
            <div id="payments" class="d-none"></div>

            <input type="hidden" name="cmd" value="_xclick">
            <input type="hidden" name="no_note" value="1">
            <input type="hidden" name="lc" value="MY">
            <input type="hidden" name="currency_code" id="currency_name" value="MYR">
            <input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynow_LG.gif:NonHostedGuest">
            <input type="hidden" name="sub" id="sub" value="0">
            <input type="hidden" name="subscription_id" value="{{ $subscription->id }}">
            <input type="hidden" name="is_upgrade" id="is_upgrade"
            @if (($activeSub->count() > 0 && $activeSub->first()->current_package->type == 'associate_member') && $package->upgrade_fee > 0)
            value="1"
            @else
            value="0"
            @endif
            >

            <div class="row">
                <div class="col-lg-12">
                    <button type="submit" name="button">{{__('Submit')}}</button>
                </div>
            </div>
          </form>
        </div>
        @php
            $total_price = packageTotalPrice($package);
        @endphp
        <div class="col-lg-4 mt-4 mt-lg-0">
            @if ($bex->recurring_billing == 1)
                <div class="bg-light package-order-summary py-5 px-5">
                    <h4>{{__('Order Summay')}}</h4>
                    <ul>
                        <li>
                            <strong>{{__('Membership')}}:</strong>
                            <span>{{$package->title}}</span>
                        </li>
                        <li>
                            <strong>{{__('Duration')}}:</strong>
                            <span class="text-capitalize">
                                @if (($activeSub->count() > 0 && $activeSub->first()->current_package->type == 'associate_member') && $package->upgrade_fee > 0)
                                    @lang('One time')
                                @else
                                    {{$package->duration == 'monthly' ? __('Monthly') : __('Yearly')}}
                                @endif
                            </span>
                        </li>
                        @if ($package->entrance_fee)
                            @if ($activeSub->count() == 0 || ($activeSub->count() > 0 && $activeSub->first()->current_package->type == 'associate_member'))
                                <li>
                                    <strong>{{__('Entrance fee')}}:</strong>
                                    <span>
                                        {{$bex->base_currency_text_position == 'left' ? $bex->base_currency_text : ''}}
                                        {{$package->entrance_fee}}
                                        {{$bex->base_currency_text_position == 'right' ? $bex->base_currency_text : ''}}
                                    </span>
                                </li>
                            @endif
                        @endif
                        <li>
                            <strong>
                                @if (($activeSub->count() > 0 && $activeSub->first()->current_package->type == 'associate_member') && $package->upgrade_fee > 0)
                                    {{ __('Upgrade Price') }}:
                                @else
                                    {{__('Price')}}:
                                @endif
                                </strong>
                            <span>
                                {{$bex->base_currency_text_position == 'left' ? $bex->base_currency_text : ''}}
                                {{$total_price}}
                                {{$bex->base_currency_text_position == 'right' ? $bex->base_currency_text : ''}}
                            </span>
                        </li>

                        @php
                            // if there is a current active subscription for this user
                            $activeSub = App\Subscription::where('user_id', Auth::user()->id)->where('status', 1);
                            if($package->duration == 'monthly') {
                                $days = 30;
                            } elseif ($package->duration == 'yearly') {
                                $days = 365;
                            }
                            if ($activeSub->count() > 0) {
                                $activationDay = \Carbon\Carbon::parse($activeSub->first()->expire_date);
                                $expireDay = \Carbon\Carbon::parse($activeSub->first()->expire_date)->addDays($days);
                            } else {
                                $activationDay = \Carbon\Carbon::now();
                                $expireDay = \Carbon\Carbon::now()->addDays($days);
                            }
                        @endphp
                        <li>
                            <strong>{{__('Activation Date')}}:</strong>
                            <span id="onlineActivationDate" style="display: none;">{{$activationDay->toFormattedDateString()}}</span>
                            <span class="text-right" id="offlineActivationDate" style="display: none;">{{__('Will be notified by mail after Admin accepts the subscription request')}}</span>
                        </li>
                        <li>
                            <strong>{{__('Expire Date')}}:</strong>
                            <span id="onlineExpiryDate" style="display: none;">{{$expireDay->toFormattedDateString()}}</span>
                            <span class="text-right" id="offlineExpiryDate" style="display: none;">{{__('Will be notified by mail after Admin accepts the subscription request')}}</span>
                        </li>
                    </ul>
                </div>
            @else
                @includeIf("front.$version.package-order")
            @endif
        </div>
      </div>
    </div>
  </div>
  <!--   quote area end   -->
@endsection


@section('scripts')

{{-- <script src="https://js.paystack.co/v1/inline.js"></script> --}}
{{-- <script src="https://js.stripe.com/v3/" async></script> --}}

@if (count($gateways) + count($ogateways) > 0)
<script>
$(document).ready(function() {
    changeGateway();
    toggleActivationExpiry();
})
</script>
@endif


<script>

function toggleActivationExpiry() {
    let type = $("#method").find('option:selected').data('val');

    if(type == 'offline') {
        $("#onlineActivationDate").hide();
        $("#onlineExpiryDate").hide();

        $("#offlineActivationDate").show();
        $("#offlineExpiryDate").show();
    } else {
        $("#offlineActivationDate").hide();
        $("#offlineExpiryDate").hide();

        $("#onlineActivationDate").show();
        $("#onlineExpiryDate").show();
    }
}

function changeGateway() {
    var val  = $('#method').find(':selected').attr('data-val');
    var form = $('#method').find(':selected').attr('data-form');
    var show = $('#method').find(':selected').attr('data-show');
    var href = $('#method').find(':selected').attr('data-href');


    if(show == "yes") {
        $('#payments').removeClass('d-none');
    } else {
        $('#payments').addClass('d-none');
    }

    if(val == 'paystack'){
        $('.pay-form').prop('id','paystack');
    }

    $('#payments').load(href);
    $('.pay-form').attr('action',form);
}

$('#method').on('change',function() {
    changeGateway();
    toggleActivationExpiry();
});

</script>
@endsection
