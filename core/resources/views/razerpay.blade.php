<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    hello

    <!-- Button trigger MOLPay Seamless -->
    <button type="button" id="myPay" class="btn btn-primary btn-lg" data-toggle="molpayseamless"
        data-mpsmerchantid="molpaymerchant" data-mpschannel="maybank2u" data-mpsamount="1.20"
        data-mpsorderid="TEST1139669863" data-mpsbill_name="MOLPay Technical">Pay by Maybank2u</button>

    <!-- jQuery (necessary for MOLPay Seamless JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="https://www.onlinepayment.com.my/MOLPay/API/seamless/js/MOLPay_seamless.deco.js"></script>

    <script>
        // $( document ).ready(function() {
        //     var options = { 
        //                     mpsmerchantid:"molpaymerchant",
        //                     mpschannel:"maybank2u", 
        //                     mpsamount:"1.20", 
        //                     mpsorderid:"TEST728638391", 
        //                     mpsbill_name:"MOLPay Technical", 
        //                     ...
        //                 }; 
                            
        //     $('#myPay').MOLPaySeamless(options)
        // });
    </script>
</body>

</html>