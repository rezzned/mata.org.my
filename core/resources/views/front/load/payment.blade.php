@php
if ($payment != 'offline') {
    $pay_data = $gateway->convertAutoData();
}
@endphp

@if ($payment == 'paypal')
    <input type="hidden" name="method" value="{{ $gateway->name }}">
@endif

@if ($payment == 'stripe')
    <input type="hidden" name="method" value="{{ $gateway->name }}">
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="form-element">
                <input class="input-field card-elements" name="cardNumber" type="text" placeholder="{{ __('Card Number') }}" autocomplete="off" autofocus id="_cardNumber"/>
                <span id="errCard"></span>
            </div>
            @if ($errors->has('cardNumber'))
                <p class="text-danger mb-0">{{ $errors->first('cardNumber') }}</p>
            @endif
        </div>

        <div class="col-lg-6 mb-4">
            <div class="form-element">
                <input class="input-field card-elements" name="cardCVC" type="text" placeholder="{{ __('CVV') }}" autocomplete="off" id="_cardCvc" />
                <span id="errCVC"></span>
            </div>
            @if ($errors->has('cardCVC'))
                <p class="text-danger mb-0">{{ $errors->first('cardCVC') }}</p>
            @endif
        </div>

        <div class="col-lg-6 mb-4">
            <div class="form-element">
                <input class="input-field card-elements" name="month" type="text" placeholder="MM ({{ __('Month') }})"/>
            </div>
            @if ($errors->has('month'))
                <p class="text-danger mb-0">{{ $errors->first('month') }}</p>
            @endif
        </div>

        <div class="col-lg-6 mb-4">
            <div class="form-element">
                <input class="input-field card-elements" name="year" type="text" placeholder="YY ({{ __('Year') }})"/>
            </div>
            @if ($errors->has('year'))
                <p class="text-danger mb-0">{{ $errors->first('year') }}</p>
            @endif
        </div>
    </div>
    <script type="text/javascript">
        var cnstatus = false;
        var dateStatus = false;
        var cvcStatus = false;

        // function validateCard(cn) {
        //     cnstatus = Stripe.card.validateCardNumber(cn);
        //     if (!cnstatus) {
        //         $("#errCard").html('{{ __('Card number not valid') }}');
        //     } else {
        //         $("#errCard").html('');
        //     }
        // }

        // function validateCVC(cvc) {
        //     cvcStatus = Stripe.card.validateCVC(cvc);
        //     if (!cvcStatus) {
        //         $("#errCVC").html('{{ __('CVC number not valid') }}');
        //     } else {
        //         $("#errCVC").html('');
        //     }
        // }

        // $("#_cardNumber").on('keyup', function(){
        //     const card_number = $(this).val();
        //     validateCard(card_number);
        // });
        // $("#_cardCvc").on('keyup', function(){
        //     const card_cvc = $(this).val();
        //     validateCVC(card_cvc);
        // });
    </script>
@endif

@if ($payment == 'razorpay')
    <input type="hidden" name="method" value="{{ $gateway->name }}">
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="form-element">
                <input class="input-field card-elements" name="razorpay_phone" type="text" placeholder="{{ __('Phone') }}" />
            </div>
            @if ($errors->has('razorpay_phone'))
                <p class="text-danger mb-0">{{ $errors->first('razorpay_phone') }}</p>
            @endif
        </div>
        <div class="col-lg-6 mb-4">
            <div class="form-element">
                <input class="input-field card-elements" name="razorpay_address" type="text" placeholder="{{ __('Address') }}" />
            </div>
            @if ($errors->has('razorpay_address'))
                <p class="text-danger mb-0">{{ $errors->first('razorpay_address') }}</p>
            @endif
        </div>
    </div>
@endif

@if ($payment == 'offline')
    <div>
        <p class="gateway-desc">{{ $gateway->short_description }}</p>
    </div>

    <div class="gateway-instruction">
        <p>{!! replaceBaseUrl($gateway->instructions) !!}</p>
    </div>

    @if ($gateway->is_receipt == 1)
        <div class="form-element mb-4">
            <label for="" class="d-block mb-2">{{ __('Receipt') }} **</label>
            <input type="file" name="receipt">
            <p class="text-warning mb-0">** {{ __('Receipt image must be .jpg / .jpeg / .png') }}</p>
        </div>
    @endif
@endif

@if ($payment == 'razerms')
<div class="mb-2">
	<div class="">
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
{{-- <script src="https://pay.merchant.razer.com/RMS/API/seamless/3.28/js/MOLPay_seamless.deco.js"></script> --}}
{{-- <script src="https://sandbox.merchant.razer.com/RMS/API/seamless/3.28/js/MOLPay_seamless.deco.js"></script> --}}
@endif
