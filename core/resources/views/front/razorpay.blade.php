<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<form action="https://pay.merchant.razer.com/RMS/pay/{{$merchantid}}/" method="post" />
		 <input type=hidden name=instID value="{{$merchantid}}">
		 <input type=hidden name=orderid value="{{$invoiceid}}">
		 <input type=hidden name=amount value="{{$amount}}">
		 <input type=hidden name=cur value="{{$currency}}">
		 <input type=hidden name=bill_desc value="{{$bill_desc}}">
		 <input type=hidden name=bill_email value="{{$email}}">
		 <input type=hidden name=bill_name value="{{$bill_name}}">
		 <input type=hidden name=country value="{{$country}}">
		 <input type=hidden name=bill_mobile value="{{$phone}}">
		 <input type=hidden name=returnurl value="{{$returnurl}}">
		 <input type=hidden name=callbackurl value="{{$returnurl}}">
		 <input type=hidden name=vcode value="$vkey">
		 <br>
		 <input src="./images/logo_molpay.gif" name="submit" type="image">
</form>

<script>
  // Checkout details as a json
  var options = {!! $json !!};

  /**
  * The entire list of Checkout fields is available at
  * https://docs.razorpay.com/docs/checkout-form#checkout-fields
  */
  options.handler = function (response) {
    document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
    document.getElementById('razorpay_signature').value = response.razorpay_signature;
    document.razorpayform.submit();
  };

  // Boolean whether to show image inside a white frame. (default: true)
  options.theme.image_padding = false;

  options.modal = {
    ondismiss: function() {
      window.location.assign("{{ url()->previous() }}");
    },

    // Boolean indicating whether pressing escape key
    // should close the checkout form. (default: true)
    escape: true,

    // Boolean indicating whether clicking translucent blank
    // space outside checkout form should close the form. (default: false)
    backdropclose: false
  };

  var rzp = new Razorpay(options);
  rzp.open();

  // document.getElementById('rzp-button1').onclick = function(e) {
  //   rzp.open();
  //   e.preventDefault();
  // }
</script>
