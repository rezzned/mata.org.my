{{-- Start: Paypal Area --}}
@if ($paypal->status == 1)
<div class="option-block">
    <div class="radio-block">
        <div class="checkbox">
            <label>
                <input name="method" type="radio" class="input-check" value="paypal" data-tabid="paypal" data-action="{{route('product.paypal.submit')}}">
                <span>{{$paypal->name}}</span>
            </label>
        </div>
    </div>
</div>
@endif
{{-- End: Paypal Area --}}


{{-- Start: Stripe Area --}}
@if ($stripe->status == 1)
<div class="option-block">
    <div class="checkbox">
        <label>
            <input name="method" class="input-check" type="radio" value="stripe" data-tabid="stripe" data-action="{{route('product.stripe.submit')}}">
            <span>{{$stripe->name}}</span>
        </label>
    </div>
</div>


<div class="row gateway-details" id="tab-stripe">

    <div class="col-md-6 mb-4">
        <div class="field-label">{{__('Card Number')}} *</div>
        <div class="field-input">
            <input type="text" class="card-elements" name="cardNumber" placeholder="{{ __('Card Number')}}" autocomplete="off" oninput="validateCard(this.value);" />
        </div>
        @error('cardNumber')
        <p class="text-danger">{{convertUtf8($message)}}</p>
        @enderror
        <span id="errCard" class="text-danger"></span>
    </div>
    <div class="col-md-6 mb-4">
        <div class="field-label">{{__('CVC')}} *</div>
        <div class="field-input">
            <input type="text" class="card-elements" placeholder="{{ __('CVC') }}" name="cardCVC" oninput="validateCVC(this.value);">
        </div>
        @error('cardCVC')
        <p class="text-danger">{{convertUtf8($message)}}</p>
        @enderror
        <span id="errCVC text-danger"></span>
    </div>
    <div class="col-md-6 mb-4">
        <div class="field-label">{{__('Month')}} *</div>
        <div class="field-input">
            <input type="text" class="card-elements" placeholder="{{__('Month')}}" name="month">
        </div>
        @error('month')
        <p class="text-danger">{{convertUtf8($message)}}</p>
        @enderror
    </div>
    <div class="col-md-6 mb-4">
        <div class="field-label">{{__('Year')}} *</div>
        <div class="field-input">
            <input type="text" class="card-elements" placeholder="{{__('Year')}}" name="year">
        </div>
        @error('year')
        <p class="text-danger">{{convertUtf8($message)}}</p>
        @enderror
    </div>
</div>
@endif
{{-- End: Stripe Area --}}



{{-- Start: Paystack Area --}}
@if ($paystackData->status == 1)
<div class="option-block">
    <div class="radio-block">
        <div class="checkbox">
            <label>
                <input name="method" type="radio" class="input-check" value="paystack" data-tabid="paystack" data-action="{{route('product.paystack.submit')}}">
                <span>{{$paystackData->name}}</span>
            </label>
        </div>
    </div>
</div>

<div class="row gateway-details" id="tab-paystack">
    <input type="hidden" name="txnid" id="ref_id" value="">
    <input type="hidden" name="sub" id="sub" value="0">
    <input type="hidden" name="method" value="Paystack">
</div>
@endif
{{-- End: Paystack Area --}}




{{-- Start: Flutterwave Area --}}
@if ($flutterwave->status == 1)
<div class="option-block">
    <div class="radio-block">
        <div class="checkbox">
            <label>
                <input name="method" type="radio" class="input-check" value="flutterwave" data-tabid="flutterwave" data-action="{{route('product.flutterwave.submit')}}">
                <span>{{$flutterwave->name}}</span>
            </label>
        </div>
    </div>
</div>

<div class="row gateway-details" id="tab-flutterwave">
    <input type="hidden" name="method" value="Flutterwave">
</div>
@endif
{{-- End: Flutterwave Area --}}


{{-- Start: Razorpay Area --}}
@if ($razorpay->status == 1)
<div class="option-block">
    <div class="radio-block">
        <div class="checkbox">
            <label>
                <input name="method" type="radio" class="input-check" value="razorpay" data-tabid="razorpay" data-action="{{route('product.razorpay.submit')}}">
                <span>{{$razorpay->name}}</span>
            </label>
        </div>
    </div>
</div>

<div class="row gateway-details" id="tab-razorpay">
    <input type="hidden" name="method" value="Razorpay">
</div>
@endif
{{-- End: Razorpay Area --}}

{{-- Start: razerms Area --}}
@if ($razerms->status == 1)
<div class="option-block">
    <div class="radio-block">
        <div class="checkbox">
            <label>
                <input name="method" type="radio" class="input-check" value="razerms" data-tabid="razerms" data-action="{{route('product.razerms.submit')}}">
                <span>{{$razerms->name}}</span>
            </label>
        </div>
    </div>
</div>

@push('styles')
    <style>
        .hand { cursor: pointer; }
        .myr_razerms { border-radius: 10px !important; }
        .myr_razerms label{ overflow: hidden; }
        .myr_razerms img{ border: 3px solid transparent; border-radius: 10px !important; }
        .myr_razerms img:hover{ transform: scale(1.01); border-radius: 10px !important; }
        .myr_razerms input:checked+label img { border: 3px solid #0787ff; }
    </style>
@endpush

<div class="row gateway-details" id="tab-razerms">
    <input type="hidden" name="method" value="razerms">
    <div class="ml-3 mb-2">
        <p class="mb-3">
            <strong class="d-block" style="color:#45d82d;">Secure Online Payment by Razer</strong>
            Please select a payment type from below to proceed for payment.
        </p>
        <div class="d-flex flex-wrap">
            <div class="myr_razerms m-1 rounded">
                <input type="radio" name="payment_options" id="paymentcredit_alb-paymex" value="credit" hidden required/>
                <label class="hand" for="paymentcredit_alb-paymex">
                    <img src="{{ asset('assets/razerms/payment-credit.jpg') }}" title="ALB Paymex"/>
                </label>
            </div>
            <div class="myr_razerms m-1 rounded">
                <input type="radio" name="payment_options" id="paymentm2u" value="maybank2u" hidden required/>
                <label class="hand" for="paymentm2u">
                    <img src="{{ asset('assets/razerms/payment-m2u.jpg') }}" title=""/>
                </label>
            </div>
        </div>
        <input type="hidden" name="currency" id="currency" value="MYR"/>
    </div>
</div>
@endif
{{-- End: razerms Area --}}



{{-- Start: Offline Gateways Area --}}
@foreach ($ogateways as $ogateway)
    <div class="option-block">
        <div class="checkbox">
            <label>
            <input name="method" class="input-check" type="radio" value="{{$ogateway->id}}" data-tabid="{{$ogateway->id}}" data-action="{{route('product.offline.submit', $ogateway->id)}}">
                <span>{{$ogateway->name}}</span>
            </label>
        </div>
    </div>

    <p class="gateway-desc">{{$ogateway->short_description}}</p>

    <div class="gateway-details row" id="tab-{{$ogateway->id}}">
        <div class="col-12">
            <div class="gateway-instruction">
                {!! replaceBaseUrl($ogateway->instructions) !!}
            </div>
        </div>

        @if ($ogateway->is_receipt == 1)
            <div class="col-12 mb-4">
                <label for="" class="d-block">{{__('Receipt')}} **</label>
                <input type="file" name="receipt">
                <p class="mb-0 text-warning">** {{__('Receipt image must be .jpg / .jpeg / .png')}}</p>
            </div>
        @endif
    </div>
@endforeach


@if ($errors->has('receipt'))
    <p class="text-danger mb-4">{{$errors->first('receipt')}}</p>
@endif
{{-- End: Offline Gateways Area --}}



<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="no_note" value="1">
<input type="hidden" name="lc" value="UK">
<input type="hidden" name="currency_code" value="USD">
<input type="hidden" name="ref_id" id="ref_id" value="">
<input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynow_LG.gif:NonHostedGuest">
<input type="hidden" name="currency_sign" value="$">

