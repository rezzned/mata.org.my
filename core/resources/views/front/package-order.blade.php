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
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
          <form
            class="pay-form"
            action="{{route('front.packageorder.submit')}}"

            enctype="multipart/form-data" method="POST"
            role="molpayseamless"
            >

            @csrf
            <input type="hidden" name="package_id" value="{{$package->id}}">
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-element mb-4">
                        @php
                            $name = '';
                            if(empty(old())) {
                                if (Auth::check()) {
                                    $name = Auth::user()->full_name;
                                }
                            } else {
                                $name = old('name');
                            }
                        @endphp
                        <label>{{__('Name')}} <span>**</span></label>
                        <input name="name" type="text" value="{{$name}}" placeholder="{{__('Enter Name')}}" {{ !$name ?: 'readonly' }}>

                        @if ($errors->has("name"))
                        <p class="text-danger mb-0">{{$errors->first("name")}}</p>
                        @endif
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-element mb-4">
                        @php
                            $email = '';
                            if(empty(old('email'))) {
                                if (Auth::check()) {
                                    $email = Auth::user()->email;
                                }
                            } else {
                                $email = old('email');
                            }
                        @endphp
                        <label>{{__('Email')}} <span>**</span></label>
                        <input name="email" type="text" value="{{$email}}" placeholder="{{__('Enter Email Address')}}" readonly>

                        @if ($errors->has("email"))
                        <p class="text-danger mb-0">{{$errors->first("email")}}</p>
                        @endif
                    </div>
                </div>
                @foreach ($inputs as $input)
                    <div class="{{$input->type == 4 || $input->type == 3 ? 'col-lg-12' : 'col-lg-6'}}">
                        <div class="form-element mb-4">
                            @if ($input->type == 1)
                                <label>{{convertUtf8($input->label)}} @if($input->required == 1) <span>**</span> @endif</label>
                                <input name="{{$input->name}}" type="text" value="{{old("$input->name")}}" placeholder="{{convertUtf8($input->placeholder)}}">
                            @endif

                            @if ($input->type == 2)
                                <label>{{convertUtf8($input->label)}} @if($input->required == 1) <span>**</span> @endif</label>
                                <select name="{{$input->name}}">
                                    <option value="" selected disabled>{{convertUtf8($input->placeholder)}}</option>
                                    @foreach ($input->package_input_options as $option)
                                        <option value="{{convertUtf8($option->name)}}" {{old("$input->name") == convertUtf8($option->name) ? 'selected' : ''}}>{{convertUtf8($option->name)}}</option>
                                    @endforeach
                                </select>
                            @endif

                            @if ($input->type == 3)
                                <label>{{convertUtf8($input->label)}} @if($input->required == 1) <span>**</span> @endif</label>
                                @foreach ($input->package_input_options as $option)
                                    <div class="custom-control custom-checkbox custom-control-inline">
                                        <input type="checkbox" id="customCheckboxInline{{$option->id}}" name="{{$input->name}}[]" class="custom-control-input" value="{{convertUtf8($option->name)}}" {{is_array(old("$input->name")) && in_array(convertUtf8($option->name), old("$input->name")) ? 'checked' : ''}}>
                                        <label class="custom-control-label" for="customCheckboxInline{{$option->id}}">{{convertUtf8($option->name)}}</label>
                                    </div>
                                @endforeach
                            @endif

                            @if ($input->type == 4)
                                <label>{{convertUtf8($input->label)}} @if($input->required == 1) <span>**</span> @endif</label>
                                <textarea name="{{$input->name}}" id="" cols="30" rows="10" placeholder="{{convertUtf8($input->placeholder)}}">{{old("$input->name")}}</textarea>
                            @endif

                            @if ($input->type == 6)
                                <label>{{convertUtf8($input->label)}} @if($input->required == 1) <span>**</span> @endif</label>
                                <input class="datepicker" name="{{$input->name}}" type="text" value="{{old("$input->name")}}" placeholder="{{convertUtf8($input->placeholder)}}" autocomplete="off">
                            @endif

                            @if ($input->type == 7)
                                <label>{{convertUtf8($input->label)}} @if($input->required == 1) <span>**</span> @endif</label>
                                <input class="timepicker" name="{{$input->name}}" type="text" value="{{old("$input->name")}}" placeholder="{{convertUtf8($input->placeholder)}}" autocomplete="off">
                            @endif

                            @if ($input->type == 5)
                            <div class="row">
                              <div class="col-lg-12">
                                <div class="form-element mb-2">
                                  <label>{{$input->label}} @if($input->required == 1) <span>**</span> @endif</label>
                                  <input type="file" name="{{$input->name}}" value="">
                                </div>
                                <p class="text-warning mb-0">** {{__('Only zip file is allowed')}}</p>
                              </div>
                            </div>
                            @endif

                            @if ($errors->has("$input->name"))
                            <p class="text-danger mb-0">{{$errors->first("$input->name")}}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            @if (!auth()->user()->subscription || (auth()->user()->subscription && auth()->user()->subscription->current_package_id != request()->id))
                @if ($package->document_copy)
                    <div class="row">
                        <div class="col-sm-12 form-element">
                            <label>{{__('Document Copy (Soft)')}}  <span>**</span></label>
                        </div>
                        @php
                            $document_copy = explode(',',$package->document_copy);
                            $document_copy_field = json_decode($package->document_copy_field, true);
                        @endphp
                        @foreach ($document_copy_field as $key => $item)
                            <div class="col-sm-6">
                                <div class="form-element mb-2">
                                    <label>{{$document_copy[$key]}}</label>
                                    <input type="file" name="{{$item}}" class="form-control" accept="image/*" required>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif


            {{-- @if (count($gateways) + count($ogateways) > 0)
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

            <!-- show payment form here if available -->
            <div id="payments" class="d-none"></div>

            <input type="hidden" name="cmd" value="_xclick">
            <input type="hidden" name="no_note" value="1">
            <input type="hidden" name="lc" value="MY">
            <input type="hidden" name="currency_code" id="currency_name" value="MYR">
            <input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynow_LG.gif:NonHostedGuest">
             --}}
             <input type="hidden" name="sub" id="sub" value="0">
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
