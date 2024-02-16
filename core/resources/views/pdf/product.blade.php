<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    @include('pdf.style.style')
</head>
<body>
    <div class="order-comfirmation">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="logo text-center" style="margin-bottom: 20px; padding-top: 30px;">
                        <img src="{{asset('assets/front/img/' . $bs->logo)}}" alt=""  height="80">
                    </div>

                    <div class="confirmation-message bg-primary" style="padding: 5px 0px;margin-bottom: 40px;">
                        <h2 class="text-center"><strong>{{__('ORDER INVOICE')}}</strong></h2>
                    </div>

                    <div class="row">
                        <div class="pull-left" style="width: 50%">
                            <div>
                                <h4><strong>Order Details</strong></h4>
                            </div>
                            <p>
                                <strong>Order Number:</strong>
                                #{{$order->order_number}}
                            </p>
                            <p>
                                <strong>Order Date:</strong>
                                {{$order->created_at->format('d-m-Y')}}
                            </p>
                            @if (!onlyDigitalItems($order))
                            <p>
                                <strong>Order Status:</strong>
                                {{$order->order_status}}
                            </p>
                            @endif
                            <p>
                                <strong>Cart Total:</strong>
                                <span>{{$bex->base_currency_text_position == 'left' ? $bex->base_currency_text : ''}}</span> {{$order->cart_total}} <span>{{$bex->base_currency_text_position == 'right' ? $bex->base_currency_text : ''}}</span>
                            </p>
                            <p>
                                <strong>Discount:</strong>
                                <span>{{$bex->base_currency_text_position == 'left' ? $bex->base_currency_text : ''}}</span> {{$order->discount}} <span>{{$bex->base_currency_text_position == 'right' ? $bex->base_currency_text : ''}}</span>
                            </p>
                            <p>
                                <strong>Subtotal:</strong>
                                <span>{{$bex->base_currency_text_position == 'left' ? $bex->base_currency_text : ''}}</span> {{$order->cart_total - $order->discount}} <span>{{$bex->base_currency_text_position == 'right' ? $bex->base_currency_text : ''}}</span>
                            </p>
                            <p>
                                <strong>Shipping Charge:</strong>
                                <span>{{$bex->base_currency_text_position == 'left' ? $bex->base_currency_text : ''}}</span> {{$order->shipping_charge}} <span>{{$bex->base_currency_text_position == 'right' ? $bex->base_currency_text : ''}}</span>
                            </p>
                            <p>
                                <strong>Tax ({{$bex->tax}}%):</strong>
                                <span>{{$bex->base_currency_text_position == 'left' ? $bex->base_currency_text : ''}}</span> {{$order->tax}} <span>{{$bex->base_currency_text_position == 'right' ? $bex->base_currency_text : ''}}</span>
                            </p>
                            <p>
                                <strong>Total:</strong>
                                <span>{{$bex->base_currency_text_position == 'left' ? $bex->base_currency_text : ''}}</span> {{$order->total}} <span>{{$bex->base_currency_text_position == 'right' ? $bex->base_currency_text : ''}}</span>
                            </p>
                            <p>
                                <strong>Payment Status:</strong>
                                {{$order->payment_status}}
                            </p>
                            <p>
                                <strong>Payment Method:</strong>
                                {{$order->method}}
                            </p>
                        </div>
                        <div class="pull-left" style="width: 50%">
                            <div>
                                <h4><strong>Shipping Details</strong></h4>
                            </div>
                            <p>
                                <strong>Name:</strong>
                                {{$order->shpping_fname }} {{$order->shpping_lname}}
                            </p>
                            <p>
                                <strong>Email:</strong>
                                {{$order->shpping_email}}
                            </p>
                            <p>
                                <strong>Number:</strong>
                                {{$order->shpping_number }}
                            </p>
                            <p>
                                <strong>Address:</strong>
                                {{$order->shpping_address }}
                            </p>
                            <p>
                                <strong>City:</strong>
                                {{$order->shpping_city }}
                            </p>
                            <p>
                                <strong>Country:</strong>
                                {{$order->shpping_country }}
                            </p>
                        </div>
                        {{-- <div class="pull-left" style="width: 33.33%">
                            <div>
                                <h4><strong>Billing Details</strong></h4>
                            </div>
                            <p>
                                <strong>Name:</strong> {{$order->billing_fname }} {{$order->billing_lname}}
                            </p>
                            <p>
                                <strong>Email:</strong> {{$order->billing_email }}
                            </p>
                            <p>
                                <strong>Number:</strong> {{$order->billing_number }}
                            </p>
                            <p>
                                <strong>Address:</strong> {{$order->billing_address }}
                            </p>
                            <p>
                                <strong>City:</strong> {{$order->billing_city }}
                            </p>
                            <p>
                                <strong>Country:</strong> {{$order->billing_country }}
                            </p>
                        </div> --}}
                    </div>

                    <div class="row">
                        <table class="table table-striped" style="margin-bottom:100px">
                            <thead>
                              <tr>
                                <th scope="col">#</th>
                                <th scope="col">Publication Name</th>
                                <th scope="col">Price</th>
                                <th scope="col">Qty</th>
                                <th scope="col">Total</th>
                              </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->orderitems as $key => $item)

                                <tr>
                                    <th>{{$key+1}}</th>
                                    <td>{{$item->title}}</td>
                                    <td><span>{{$bex->base_currency_text_position == 'left' ? $bex->base_currency_text : ''}}</span> {{$item->price}} <span>{{$bex->base_currency_text_position == 'right' ? $bex->base_currency_text : ''}}</span></td>
                                    <td>{{$item->qty}}</td>
                                    <td><span>{{$bex->base_currency_text_position == 'left' ? $bex->base_currency_text : ''}}</span> {{$item->price * $item->qty}} <span>{{$bex->base_currency_text_position == 'right' ? $bex->base_currency_text : ''}}</span></td>

                                  </tr>
                                @endforeach
                            </tbody>
                          </table>
                    </div>

                </div>
            </div>
        </div>

    </div>
</body>
</html>
