@extends("front.$version.layout")

@section('styles')
    <link rel="stylesheet" href="{{asset('assets/front/css/nice-select.css')}}">
    <link rel="stylesheet" href="{{asset('assets/front/css/jquery.nice-number.min.css')}}">
    <style>
        input {
            margin-bottom: 10px;
        }

        .anonymous_user {
            font-size: 14px;
            text-align: center;
            margin-top: 20px;
        }

        .anonymous_user input {
            height: 14px;
            width: 14px;
            margin-right: 5px;
        }

        #stripe-section, #razorpay-section, #payumoney-section {
            margin-top: 10px;
        }

        .gateway-desc {
            background: #f1f1f1;
            font-size: 14px;
            padding: 10px 25px;
            margin-bottom: 20px;
            color: #212529;
            margin-top: 20px;
        }
    </style>
@endsection
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

@section('pagename')
    - {{__('Training')}} - {{convertUtf8($event->title)}}
@endsection

@section('meta-keywords', "$event->meta_keywords")
@section('meta-description', "$event->meta_description")

@section('breadcrumb-title', $bs->event_details_title)
@section('breadcrumb-subtitle', strlen($event->title) > 30 ? mb_substr($event->title,0,30,'utf-8') . '...': $event->title)
@section('breadcrumb-link', __('Event Details'))

@section('content')
    <!--====== Start Event details Section ======-->
    <section class="event-details-section pt-130 pb-140">
        <div class="container">
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <div class="alert alert-danger alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>{{ $error }}</strong>
                    </div>
                @endforeach
            @endif
            <div class="row">
                <div class="col-lg-6">
                    @if ($event->image != 'null')
                        <div class="event-big-slide mb-30">
                            @foreach(json_decode($event->image) as $event_image)
                                <div class="product-img">
                                    <a href="{{asset('/assets/front/img/events/sliders/'.$event_image)}}" class="image-popup">
                                        <img src="{{asset('/assets/front/img/events/sliders/'.$event_image)}}" class="img-fluid" alt="" width="700" height="500">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                        <div class="event-thumb-slide">
                            @foreach(json_decode($event->image) as $event_image)
                                <div class="product-img">
                                    <img src="{{asset('/assets/front/img/events/sliders/'.$event_image)}}" class="img-fluid" alt="">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="col-lg-6">
                    <div class="event-details-wrapper">
                        <div class="event-content">
                            <h3>{{convertUtf8($event->title)}}</h3>
                            <div class="event-meta mb-2">
                                <span class="date"><i class="far fa-calendar-alt"></i>{{dateFormat($event->date, 'd-m-Y')}}</span>
                                @if (!empty($event->venue_location))
                                <span class="location"><i class="fas fa-map-marker-alt"></i>{{$event->venue_location}}</span>
                                @endif
                            </div>
                            @if ($ticket_data)
                                <p class="price base-color">{{currency_format($ticket_data->cost ?? '0')}} / {{__('per ticket')}}</p>
                            @else

                            @endif
                            <div id="purchase-section" style="display: block">
                                <div class="time-count">
                                    <div id="simple_timer"></div>
                                </div>
                                <p>{!! convertUtf8($event->short_desc) !!}</p>
                                <p>{!! replaceBaseUrl(convertUtf8($event->content)) !!}</p>
                                <div class="info-box mb-15">
                                    <a class="base-color" href="{{route('front.events', ['category' => $event->cat_id])}}">{{$event->eventCategories->name}}</a>
                                    <p>{{$event->venue_location}}</p>
                                </div>
                                <p class="base-color">{{ round($event->cpd_points) }} {{ __('CPD points will be given after the event') }}</p>

                                @if($event->eventTicket->sum('available') > 0 && $ticket_data)
                                    <ul class="mb-20" style="display:flex">
                                        <li hidden><input type="number" id="tickets" min="1" max="{{$ticket_data->available}}" hidden></li>
                                        <li><input type="hidden" id="cost" value="{{$ticket_data->cost}}"></li>
                                        @if(!is_null($event->video))
                                            <li><a href="{{asset('/assets/front/img/events/videos/'.$event->video)}}" class="play_btn">
                                                <i class="fas fa-play"></i></a></li>
                                            <li>
                                        @endif
                                    </ul>
                                    <div class="mt-3">
                                        <a href="javascript:void(0)" class="main-btn" id="addToCart">{{__('Book Ticket')}}</a>
                                    </div>
                                    {{-- <a href="javascript:void(0)" class="main-btn" id="addToCart">{{__('Add To Cart')}}</a> --}}
                                    {{-- <a href="javascript:void(0)" class="main-btn" id="addToCart" data-guest>{{__('Add To Cart')}}</a> --}}

                                @else
                                    <div>{{__('No tickets are available')}}</div>
                                @endif
                            </div>
                            @php
                                if(Auth::check()) {
                                    $name = Auth::user()->full_name;
                                    $email = Auth::user()->email;
                                    $phone = Auth::user()->personal_phone;
                                    $ic_number = Auth::user()->idcard_no;
                                } else {
                                    $name = '';
                                    $email = '';
                                    $phone = '';
                                    $ic_number = '';
                                }
                            @endphp

                            <div id="invoice-section" style="display: none; justify-content: center; text-align: center">
                                @if ($bex->event_guest_checkout == 1 && !Auth::check())
                                    <div class="alert alert-warning">
                                        {{__('You may proceed to register as a guest, but please state your affiliation (Professional Organization\'s name). If you want to login before purchasing, then please Click Here')}}
                                        <a href="{{route('user.login', ['redirected' => 'event'])}}">{{__('Click Here')}}</a>
                                    </div>
                                @endif
                                <form action="{{route("front.event.payment")}}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <hr>
                                    <h4>{{__('Invoice')}}</h4>
                                    <hr>
                                    <input type="hidden" name="event_id" value="{{$event->id}}">
                                    <input type="hidden" name="event_ticket_id" value="{{$ticket_data ? $ticket_data->id : ''}}">
                                    <input type="hidden" name="event_slug" value="{{$event->slug}}">
                                    <div>{{__('No. Of Tickets')}}: <span id="quantity">5</span></div>
                                    <input type="hidden" name="ticket_quantity" id="ticket-quantity" value="">
                                    <div>{{__('Per Ticket Cost')}}: <span id="per_ticket_cost">{{ currency_format($ticket_data ? $ticket_data->cost : '') }}</span></div>
                                    <input type="hidden" name="ticket_cost" id="ticket_cost" value="{{$ticket_data ? $ticket_data->cost : ''}}">
                                    <div>{{__('Total Cost')}}: <span>{{ currency_symbol('left') }}<span id="total">{{$ticket_data ? $ticket_data->cost : ''}}</span>{{ currency_symbol('right') }}</span></div>
                                    <input type="hidden" name="total_cost" id="total-cost" value="">
                                    <br>
                                    <div id="donation-info-section">
                                        <input type="text" class="form_control" name="name" placeholder="{{__('Enter your name')}}" value="{{$name}}">
                                        <input type="text" class="form_control" name="ic_number" placeholder="{{__('Enter your IC number')}}" value="{{ $ic_number }}">
                                        <input type="email" class="form_control" name="email" placeholder="{{__('Enter your email address')}}" value="{{$email}}">
                                        <input type="text" class="form_control" name="company_name" placeholder="{{__("Enter your company's name")}}">
                                        @guest
                                        <input type="text" class="form_control" name="address" placeholder="{{__("Enter your address")}}">
                                        @endguest
                                        <input type="text" class="form_control" name="phone" placeholder="{{__('Enter your phone')}}" value="{{$phone}}">
                                        @guest
                                        <input type="text" class="form_control" name="professional_member" placeholder="{{__('Enter your professional member')}}">
                                        @endguest
                                        {{--@guest--}}
                                        <div class="d-block pb-3">
                                            <input type="hidden" name="price_type" value="{{ $ticket_data->type }}" id="price_type">
                                            <label for="price_variation" class="text-left">Ticket Price</label>
                                            <select name="price_variation" id="price_variation" class="form-control w-100">
                                                @foreach ($event_ticket as $_ticket)
                                                <option value="{{ $_ticket->cost }}" data-type="{{ $_ticket->type }}">{{ undash_str($_ticket->type) }} - {{ currency_format($_ticket->cost) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        {{--@endguest--}}
                                    </div>
                                    <br>
                                    <br>
                                    <br>
                                    <div>
                                        <ul class="cart-total-table">
                                            <li class="clearfix">
                                                <span class="col col-title text-left">{{__('Cart Total')}}</span>
                                                <span class="col text-right">{{ currency_symbol('left') }}<span id="cart_total" class="subtotal">{{$ticket_data ? $ticket_data->cost : ''}}</span>{{ currency_symbol('right') }}
                                                </span>
                                            </li>
                                            <li class="clearfix">
                                                <span class="col col-title text-left">{{ __('Discount') }}
                                                    <span class="text-success">(<i class="fas fa-minus"></i>)</span>
                                                </span>
                                                <span class="col text-right">
                                                    {{ currency_symbol('left') }}<span id="discount">{{ 0 }}</span>{{ currency_symbol('right') }}
                                                </span>
                                            </li>
                                            <li class="clearfix">
                                                <span class="col col-title text-left">{{ __('Subtotal') }}</span>
                                                <span class="col text-right">
                                                    {{ currency_symbol('left') }}<span id="sub_total" class="subtotal">{{$ticket_data ? $ticket_data->cost : ''}}</span>{{ currency_symbol('right') }}
                                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="checkout-area pt-1">
                                        <div class="coupon mt-4">
                                            <h4 class="mb-3">{{__('Coupon')}}</h4>
                                            <div class="form-group d-flex">
                                                <input type="text" class="form-control" name="coupon" value="">
                                                <button class="btn btn-primary base-bg border-0" type="button" onclick="applyCoupon();">{{__('Apply')}}</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form_group" style="display:flex;flex-direction:column;margin-top:20px">
                                        <select name="payment_method" id="payment-method">
                                            <option value="0">{{__('Choose an option')}}</option>
                                            @foreach($payment_gateways as $payment_gateway)
                                                <option value="{{$payment_gateway->id}}" data-name="{{$payment_gateway->name}}">{{$payment_gateway->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div id="stripe-section" style="display:none">
                                        <input type="text" class="form_control" name="card_number" placeholder="{{__('Card Number')}}">
                                        <input type="text" class="form_control" name="card_cvv" placeholder="{{__('CVV')}}">
                                        <div style="display:flex">
                                            <input style="margin-right:5px" type="text" class="form_control" name="card_month" placeholder="{{__('Month')}}">
                                            <input type="text" class="form_control" name="card_year" placeholder="{{__('Year')}}">
                                        </div>
                                    </div>
                                    <div id="razorpay-section" style="display:none">
                                        <input type="text" class="form_control" name="razorpay_phone" placeholder="{{__('Enter your phone')}}">
                                        <input type="text" class="form_control" name="razorpay_address" placeholder="{{__('Enter your address')}}">
                                    </div>
                                    <div id="razerms-section" style="display:none">
                                        <div class="my-3">
                                            <p class="mb-3">
                                                <strong class="d-block" style="color:#45d82d">Secure Online Payment by Razer</strong>
                                                Please select a payment type from below to proceed for payment.
                                            </p>
                                            <div class="d-flex flex-wrap">
                                                <div class="myr_razerms m-1 rounded">
                                                    <input type="radio" name="rms_payment_options" id="paymentcredit_alb-paymex" value="credit" hidden/>
                                                    <label class="hand" for="paymentcredit_alb-paymex">
                                                        <img src="{{ asset('assets/razerms/payment-credit.jpg') }}" title="ALB Paymex" alt="ALB Paymex"/>
                                                    </label>
                                                </div>
                                                <div class="myr_razerms m-1 rounded">
                                                    <input type="radio" name="rms_payment_options" id="paymentm2u" value="maybank2u" hidden/>
                                                    <label class="hand" for="paymentm2u">
                                                        <img src="{{ asset('assets/razerms/payment-m2u.jpg') }}" title="Maybank 2u" alt="Maybank 2u"/>
                                                    </label>
                                                </div>
                                            </div>
                                            <input type="hidden" name="rms_currency" id="rms_currency" value="MYR"/>
                                        </div>
                                    </div>
                                    <div id="instructions"></div>
                                    <input type="hidden" name="is_receipt" value="0" id="is_receipt">
                                    <div class="form_group" style="display:flex;flex-direction:row;justify-content:space-between;text-align:center;margin-top:20px;">
                                        <a href="javascript:void(0)" class="main-btn" id="cancel" style="height:45px;justify-content:center;text-align:center;">{{__('Cancel')}}</a>
                                        <button class="main-btn submit_btn_load" type="submit" style="height:45px;justify-content:center;text-align:center;padding-top:0;padding-bottom:0;">
                                            {{__('Confirm')}}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="discription-area mt-80 mb-50">
                        <div class="discription-tabs">
                            <ul class="nav nav-tabs">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#description">{{__('Details')}}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#organizer">{{__('Organizer')}}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#vanue">{{__('Venue')}}</a>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-content">
                            <div id="description" class="tab-pane active">
                                <div class="event-content-box">
                                    <div class="info">
                                        <span>{{__('Start')}}:</span>
                                        <p>{{date_format(date_create($event->date),"M d,Y")}}
                                            @ {{date_format(date_create($event->time),"h:i:sa")}}</p>
                                    </div>
                                    <div class="info">
                                        <span>{{__('Cost')}}:</span>
                                        <p>{{$bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : ''}}{{$ticket_data ? $ticket_data->cost : ''}}{{$bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : ''}}</p>
                                    </div>
                                    <div class="info">
                                        <span>{{__('Event Categories')}}:</span>
                                        <p>{{convertUtf8($event->eventCategories->name)}}, {{__('Training')}}</p>
                                    </div>
                                </div>
                            </div>
                            <div id="organizer" class="tab-pane fade">
                                <div class="event-content-box">
                                    <h4>{{convertUtf8($event->organizer)}}</h4>
                                    @if (!empty($event->organizer_email))
                                        <div class="info">
                                            <span>{{__('Email')}}:</span>
                                            <p><a href="{{$event->organizer_email}}">{{convertUtf8($event->organizer_email)}}</a></p>
                                        </div>
                                    @endif
                                    @if (!empty($event->organizer_website))
                                    <div class="info">
                                        <span>{{__('Website')}}:</span>
                                        <p><a href="{{$event->organizer_website}}">{{convertUtf8($event->organizer_website)}}</a></p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div id="vanue" class="tab-pane fade">
                                <div class="event-content-box">
                                    <h4>{{convertUtf8($event->title)}}</h4>
                                    <p>{{convertUtf8($event->venue)}} <br>{{convertUtf8($event->venue_location)}}</p>
                                    @if (!empty($event->venue_location))
                                        <div class="map-box">
                                            <iframe id="gmap_canvas" src="https://maps.google.com/maps?q={{$event->venue_location}}&t=&z=13&ie=UTF8&iwloc=&output=embed"></iframe>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if(count($moreEvents)>0)
                <div class="row recent-event mb-30">
                    <div class="col-lg-12">
                        <h4 class="title">{{__('MAYBE YOU LIKE')}}</h4>
                        @foreach($moreEvents as $moreEvent)
                            <div class="event-item">
                                <div class="event-img">
                                    <img data-src="{{asset('/assets/front/img/events/sliders/'.json_decode($moreEvent->image)[0])}}"
                                        class="img-fluid lazy" alt="">
                                </div>
                                <div class="event-content">
                                    <h4><a href="{{route('front.event_details',[$moreEvent->slug])}}">{{convertUtf8($moreEvent->title)}}</a></h4>
                                    <div class="post-meta mb-2">
                                        @if (!empty($moreEvent->venue_location))
                                        <span><a href="#">{{convertUtf8($moreEvent->venue_location)}}</a></span>
                                        @endif
                                        <span>
                                            <a href="#">{{date_format(date_create($moreEvent->date),"d M Y")}}</a>
                                        </span>
                                    </div>
                                    <p class="price base-color">{{ currency_format($moreEvent->cost) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
    <!--====== End Event details Section ======-->
@endsection
@section('scripts')
<script src="{{asset('/assets/front/js/jquery.nice-select.min.js')}}"></script>
<script src="{{asset('/assets/front/js/jquery.nice-number.min.js')}}"></script>
<script src="{{asset('/assets/front/js/jquery.easypiechart.min.js')}}"></script>
{{-- <script src="{{asset('/assets/front/js/jquery.syotimer.min.js')}}"></script> --}}
<script src="{{asset('/assets/front/js/event.js')}}"></script>
<script type="text/javascript">
    const d = new Date('{!! $event->date !!}');
    const ye = parseInt(new Intl.DateTimeFormat('en', {year: 'numeric'}).format(d));
    const mo = parseInt(new Intl.DateTimeFormat('en', {month: 'numeric'}).format(d));
    const da = parseInt(new Intl.DateTimeFormat('en', {day: '2-digit'}).format(d));
    const t = ' {!! $event->time !!}';
    const time = t.split(":");
    const hr = parseInt(time[0]);
    const min = parseInt(time[1]);
    $('#simple_timer').syotimer({
        year: ye,
        month: mo,
        day: da,
        hour: hr,
        minute: min,
    });
</script>
<script>
    function applyCoupon() {
        $.post(
            "{{route('front.event.coupon')}}",
            {
                coupon: $("input[name='coupon']").val(),
                cart_total:  $("#total-cost").val(),
                _token: document.querySelector('meta[name=csrf-token]').getAttribute('content')
            },
            function(data) {
                if (data.status == 'success') {
                    toastr["success"](data.message);
                    $("input[name='coupon']").val('');
                    $("#discount").html(data.coupon);
                    const total = $('#total-cost').val() - data.coupon;
                    $("#sub_total").html(total);
                    $("#total-cost").val(total);
                } else {
                    toastr["error"](data.message);
                }
            }
        );
    }
    $("input[name='coupon']").on('keypress', function(e) {
        let code = e.which;
        if (code == 13) {
            e.preventDefault();
            applyCoupon();
        }
    });
    // apply coupon functionality ends
    $(document).ready(function () {
        $('#price_variation').on('change', function (e) {
            const quantity = $("#tickets").val();
            const cost = $("#price_variation").val();
            const total = quantity * cost;
            $("#quantity").html(`<span>${quantity}<span/>`);
            $("#ticket-quantity").val(quantity);
            $("#total").html(`<span>${total}<span/>`);
            $("#cart_total").html(total);
            $("#sub_total").html(total);
            $("#total-cost").val(total);
            $("#purchase-section").css('display', 'none');
            $("#invoice-section").css('display', 'block');
            $('#per_ticket_cost').text(cost);
            $('#ticket_cost').val(cost);
            const type = e.target.dataset.type;
            $('#price_type').val(type);
        });

        $("#addToCart:not([data-guest])").on('click', function () {
            const quantity = $("#tickets").val();
{{--            @guest--}}
            const cost = $("#price_variation").val();
{{--            @else--}}
{{--            var cost = $("#cost").val();--}}
{{--            @endguest--}}
            const total = quantity * cost;
            $("#quantity").html(`<span>${quantity}<span/>`);
            $("#ticket-quantity").val(quantity);
            $("#total").html(`<span>${total}<span/>`);
            $("#cart_total").html(total);
            $("#sub_total").html(total);
            $("#total-cost").val(total);
            $("#purchase-section").css('display', 'none');
            $("#invoice-section").css('display', 'block');
            $('#price_variation').prop('selectedIndex',0);
            $('#price_type').val($('#price_variation option')[0].dataset.type);
        });

        $("#addToCart[data-guest]").on('click', function () {
            const quantity = $("#tickets").val();
{{--            @guest--}}
            const cost = $("#price_variation").val();
{{--            @else--}}
{{--            var cost = $("#cost").val();--}}
{{--            @endguest--}}
            const total = quantity * cost;
            const newForm = new FormData();
            $('#price_variation option')[0].setAttribute('selected', 'selected');
            $(`<form action="{{ route('add_event_to_cart') }}" method="post">@csrf
                <input type="hidden" name="quantity" value="${quantity}"/>
                <input type="hidden" name="cost" value="${cost}"/>
                <input type="hidden" name="total" value="${total}"/>
                <input type="hidden" name="event_id" value="{{ $event->id }}"/>
                <input type="hidden" name="event_ticket_id" value="{{ $ticket_data->id ?? '' }}"/>
            </form>`).appendTo('body').submit();
        });

        $("#cancel").on('click', function () {
            $("#purchase-section").css('display', 'block');
            $("#invoice-section").css('display', 'none');
        });
        $("#payment-method").change(function () {
            var selectedPaymentMethodId = $(this).children("option:selected").val();
            var selectedPaymentMethod = $(this).children("option:selected").data('name');
            let offline = {!! $offline !!};
            let data = [];
            offline.map(({id, name}) => {
                data.push(name);
            });
            $("#instructions").html('');
            if (selectedPaymentMethodId == "14") {
                $('#razorpay-section').fadeOut();
                $('#razerms-section').fadeOut();
                $('#stripe-section').fadeIn(5);
            } else if (selectedPaymentMethodId == "9") {
                $('#razerms-section').fadeOut();
                $('#stripe-section').fadeOut();
                $('#razorpay-section').fadeIn(5);
            } else if (selectedPaymentMethodId == "20") {
                $('#stripe-section').fadeOut();
                $('#razorpay-section').fadeOut();
                $('#razerms-section').fadeIn(5);
            } else if (data.indexOf(selectedPaymentMethod) !== -1) {
                $('#razerms-section').fadeOut();
                $('#stripe-section').fadeOut();
                $('#razorpay-section').fadeOut();
                //ajax call for instructions
                let name = selectedPaymentMethod;
                let formData = new FormData();
                formData.append('name', name);
                $('button[type="submit"]').prop('disabled', true);
                $("#instructions").html('{{ trans('Loading...')}}');
                $.ajax({
                    url: '{{route('front.payment.instructions')}}',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    type: 'POST',
                    contentType: false,
                    processData: false,
                    cache: false,
                    data: formData,
                    success: function (data) {
                        $('button[type="submit"]').prop('disabled', false);
                        console.log(data);
                        let instruction = $("#instructions");
                        let instructions = `<div class="gateway-desc">${data.instructions}</div>`;
                        let description = `<div class="gateway-desc"><p>${data.description}</p></div>`;
                        let receipt = `<div class="form-element mb-2">
                                        <label>Receipt  <span>**</span> </label>
                                        <input type="file" name="receipt" value="" class="file-input" accept='image/jpg,image/jpeg,image/png' required />
                                        <p class="mb-0 text-warning">** Receipt image must be .jpg / .jpeg / .png</p>
                                    </div>`;
                        if (data.is_receipt == "1") {
                            $("#is_receipt").val(1);
                            let finalInstruction = instructions + description + receipt;
                            instruction.html(finalInstruction);
                        } else {
                            $("#is_receipt").val(0);
                            let finalInstruction = instructions + description;
                            instruction.html(finalInstruction);
                        }
                        $('#instructions').fadeIn();
                    },
                    error: function (data) {
                        console.log(data);
                    }
                })
            } else {
                $('#razerms-section').fadeOut();
                $('#stripe-section').fadeOut();
                $('#razorpay-section').fadeOut();
                $('#payumoney-section').fadeOut();
                $('#instructions').fadeOut();
            }
        });

    });
    document.addEventListener('DOMContentLoaded', function(){
        setTimeout(function() {
            console.clear();
        }, 1000);
    });
</script>
@endsection
