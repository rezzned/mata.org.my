@extends('admin.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">Payment Gateways</h4>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="{{route('admin.dashboard')}}">
          <i class="flaticon-home"></i>
        </a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">Payment Gateways</a>
      </li>
    </ul>
  </div>
  <div class="row">
    <div class="col-lg-4">
        <div class="card">
            <form class="" action="{{route('admin.razerms.update')}}" method="post">
            @csrf
            <div class="card-header">
                <div class="row">
                    <div class="col-lg-12">
                        <input type="text" name="name" value="{{ $razerms->name }}" class="form-control" />
                    </div>
                </div>
            </div>


            <div class="card-body pt-5 pb-5">
                <div class="row">
                <div class="col-lg-12">
                    @csrf
                    @php
                        $razermsInfo = json_decode($razerms->information, true);
                    @endphp
                    <div class="form-group">
                        <label>RazerMS</label>
                        <div class="selectgroup w-100">
                        <label class="selectgroup-item">
                            <input type="radio" name="status" value="1" class="selectgroup-input" {{$razerms->status == 1 ? 'checked' : ''}}>
                            <span class="selectgroup-button">Active</span>
                        </label>
                        <label class="selectgroup-item">
                            <input type="radio" name="status" value="0" class="selectgroup-input" {{$razerms->status == 0 ? 'checked' : ''}}>
                            <span class="selectgroup-button">Deactive</span>
                        </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>RazerMS Sandbox</label>
                        <div class="selectgroup w-100">
                        <label class="selectgroup-item">
                            <input type="radio" name="sandbox" value="1" class="selectgroup-input" {{$razermsInfo['sandbox'] == 1 ? 'checked' : ''}}>
                            <span class="selectgroup-button">Active</span>
                        </label>
                        <label class="selectgroup-item">
                            <input type="radio" name="sandbox" value="0" class="selectgroup-input" {{$razermsInfo['sandbox'] == 0 ? 'checked' : ''}}>
                            <span class="selectgroup-button">Deactive</span>
                        </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>RazerMS Merchant ID</label>
                        <input class="form-control" name="merchantid" value="{{$razermsInfo['merchantid']}}">
                        @if ($errors->has('merchantid'))
                            <p class="mb-0 text-danger">{{$errors->first('merchantid')}}</p>
                        @endif
                    </div>

                    <div class="form-group">
                        <label>RazerMS Verify Key</label>
                        <input class="form-control" name="key" value="{{$razermsInfo['key']}}">
                        @if ($errors->has('key'))
                            <p class="mb-0 text-danger">{{$errors->first('key')}}</p>
                        @endif
                    </div>

                    <div class="form-group">
                        <label>RazerMS Secret Key</label>
                        <input class="form-control" name="secret" value="{{$razermsInfo['secret']}}">
                        @if ($errors->has('secret'))
                            <p class="mb-0 text-danger">{{$errors->first('secret')}}</p>
                        @endif
                    </div>

                </div>
                </div>
            </div>

            <div class="card-footer">
                <div class="form">
                <div class="form-group from-show-notify row">
                    <div class="col-12 text-center">
                    <button type="submit" class="btn btn-success">Update</button>
                    </div>
                </div>
                </div>
            </div>
            </form>
        </div>
    </div>

    <div class="col-lg-4">
      <div class="card">
        <form action="{{route('admin.paypal.update')}}" method="post">
          @csrf
          <div class="card-header">
              <div class="row">
                  <div class="col-lg-12">
                    <input type="text" name="name" value="{{ $paypal->name }}" class="form-control" />
                  </div>
              </div>
          </div>
          <div class="card-body pt-5 pb-5">
            <div class="row">
              <div class="col-lg-12">
                @csrf

                <div class="form-group">
                  <label>Paypal</label>
                  <div class="selectgroup w-100">
                    <label class="selectgroup-item">
                      <input type="radio" name="status" value="1" class="selectgroup-input" {{$paypal->status == 1 ? 'checked' : ''}}>
                      <span class="selectgroup-button">Active</span>
                    </label>
                    <label class="selectgroup-item">
                      <input type="radio" name="status" value="0" class="selectgroup-input" {{$paypal->status == 0 ? 'checked' : ''}}>
                      <span class="selectgroup-button">Deactive</span>
                    </label>
                  </div>
                </div>
                @php
                    $paypalInfo = json_decode($paypal->information, true);
                @endphp
                <div class="form-group">
                  <label>Paypal Test Mode</label>
                  <div class="selectgroup w-100">
                    <label class="selectgroup-item">
                      <input type="radio" name="sandbox_check" value="1" class="selectgroup-input" {{$paypalInfo["sandbox_check"] == 1 ? 'checked' : ''}}>
                      <span class="selectgroup-button">Active</span>
                    </label>
                    <label class="selectgroup-item">
                      <input type="radio" name="sandbox_check" value="0" class="selectgroup-input" {{$paypalInfo["sandbox_check"] == 0 ? 'checked' : ''}}>
                      <span class="selectgroup-button">Deactive</span>
                    </label>
                  </div>
                </div>
                <div class="form-group">
                  <label>Paypal Client ID</label>
                  <input class="form-control" name="client_id" value="{{$paypalInfo["client_id"]}}">
                  @if ($errors->has('client_id'))
                    <p class="mb-0 text-danger">{{$errors->first('client_id')}}</p>
                  @endif
                </div>
                <div class="form-group">
                  <label>Paypal Client Secret</label>
                  <input class="form-control" name="client_secret" value="{{$paypalInfo["client_secret"]}}">
                  @if ($errors->has('client_secret'))
                    <p class="mb-0 text-danger">{{$errors->first('client_secret')}}</p>
                  @endif
                </div>

              </div>
            </div>
          </div>
          <div class="card-footer">
            <div class="form">
              <div class="form-group from-show-notify row">
                <div class="col-12 text-center">
                  <button type="submit" id="displayNotif" class="btn btn-success">Update</button>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card">
        <form class="" action="{{route('admin.stripe.update')}}" method="post">
          @csrf
          <div class="card-header">
              <div class="row">
                  <div class="col-lg-12">
                    <input type="text" name="name" value="{{ $stripe->name }}" class="form-control" />
                  </div>
              </div>
          </div>
          <div class="card-body pt-5 pb-5">
            <div class="row">
              <div class="col-lg-12">
                @csrf
                @php
                    $stripeInfo = json_decode($stripe->information, true);
                    // dd($stripeInfo);
                @endphp
                <div class="form-group">
                    <label>Stripe</label>
                    <div class="selectgroup w-100">
                      <label class="selectgroup-item">
                        <input type="radio" name="status" value="1" class="selectgroup-input" {{$stripe->status == 1 ? 'checked' : ''}}>
                        <span class="selectgroup-button">Active</span>
                      </label>
                      <label class="selectgroup-item">
                        <input type="radio" name="status" value="0" class="selectgroup-input" {{$stripe->status == 0 ? 'checked' : ''}}>
                        <span class="selectgroup-button">Deactive</span>
                      </label>
                    </div>
                </div>
                <div class="form-group">
                    <label>Stripe Key</label>
                    <input class="form-control" name="key" value="{{$stripeInfo['key']}}">
                    @if ($errors->has('key'))
                        <p class="mb-0 text-danger">{{$errors->first('key')}}</p>
                    @endif
                </div>
                <div class="form-group">
                    <label>Stripe Secret</label>
                    <input class="form-control" name="secret" value="{{$stripeInfo['secret']}}">
                    @if ($errors->has('secret'))
                        <p class="mb-0 text-danger">{{$errors->first('secret')}}</p>
                    @endif
                </div>

              </div>
            </div>
          </div>
          <div class="card-footer">
            <div class="form">
              <div class="form-group from-show-notify row">
                <div class="col-12 text-center">
                  <button type="submit" id="displayNotif" class="btn btn-success">Update</button>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <form class="" action="{{route('admin.razorpay.update')}}" method="post">
            @csrf
            <div class="card-header">
                <div class="row">
                    <div class="col-lg-12">
                      <input type="text" name="name" value="{{ $razorpay->name }}" class="form-control" />
                    </div>
                </div>
            </div>


            <div class="card-body pt-5 pb-5">
                <div class="row">
                <div class="col-lg-12">
                    @csrf
                    @php
                        $razorpayInfo = json_decode($razorpay->information, true);
                        // dd($razorpayInfo);
                    @endphp
                    <div class="form-group">
                        <label>Razorpay</label>
                        <div class="selectgroup w-100">
                        <label class="selectgroup-item">
                            <input type="radio" name="status" value="1" class="selectgroup-input" {{$razorpay->status == 1 ? 'checked' : ''}}>
                            <span class="selectgroup-button">Active</span>
                        </label>
                        <label class="selectgroup-item">
                            <input type="radio" name="status" value="0" class="selectgroup-input" {{$razorpay->status == 0 ? 'checked' : ''}}>
                            <span class="selectgroup-button">Deactive</span>
                        </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Razorpay Key</label>
                        <input class="form-control" name="key" value="{{$razorpayInfo['key']}}">
                        @if ($errors->has('key'))
                            <p class="mb-0 text-danger">{{$errors->first('key')}}</p>
                        @endif
                    </div>

                    <div class="form-group">
                        <label>Razorpay Secret</label>
                        <input class="form-control" name="secret" value="{{$razorpayInfo['secret']}}">
                        @if ($errors->has('secret'))
                            <p class="mb-0 text-danger">{{$errors->first('secret')}}</p>
                        @endif
                    </div>

                </div>
                </div>
            </div>

            <div class="card-footer">
                <div class="form">
                <div class="form-group from-show-notify row">
                    <div class="col-12 text-center">
                    <button type="submit" class="btn btn-success">Update</button>
                    </div>
                </div>
                </div>
            </div>
            </form>
        </div>
    </div>
  </div>

@endsection
