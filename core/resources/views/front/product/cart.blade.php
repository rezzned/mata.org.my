@extends("front.$version.layout")

@section('pagename')
 - {{__('Cart')}}
@endsection

@section('meta-keywords', "$be->cart_meta_keywords")
@section('meta-description', "$be->cart_meta_description")


@section('styles')
<link rel="stylesheet" href="{{asset('assets/front/css/jquery-ui.min.css')}}">
@endsection


@section('breadcrumb-title', convertUtf8($be->cart_title))
@section('breadcrumb-subtitle', convertUtf8($be->cart_subtitle))
@section('breadcrumb-link', __('Cart'))

@section('content')

<!--====== SHOPPING CART PART START ======-->

<section class="cart-area">
    <div class="container">
        <div class="row">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                @if($cart != null)
                    <ul class="total-item-info">
                        @php
                            $cartTotal = cartTotal();
                            $countitem = CartItemTotal();
                        @endphp
                        <li><strong>{{__('Total Items')}}:</strong> <strong class="cart-item-view">{{$cart ? $countitem : 0}}</strong></li>
                        <li><strong>{{__('Cart Total')}} :</strong>  <strong class="cart-total-view">{{ currency_symbol('left') }} {{$cartTotal}} {{ currency_symbol('right') }}</strong></li>
                    </ul>
                @endif
                <div class="table-outer">
                    @php
                        $cart_quantity = 0;
                    @endphp
                    @if($cart != null || $event_cart != null)
                    <table class="cart-table">
                        @if (count($cart ?? []) > 0)
                        <thead class="cart-header product_header">
                            <tr>
                                <th class="prod-column">{{__('Publications')}}</th>
                                <th class="hide-column"></th>
                                <th>{{__('Quantity')}}</th>
                                <th class="availability">{{__('Availability')}}</th>
                                <th class="price">{{__('Price')}}</th>
                                <th><small>{{__('Postal Fee')}}</small></th>
                                <th>{{__('Total')}}</th>
                                <th>{{__('Remove')}}</th>
                            </tr>
                        </thead>
                        @endif
                        <tbody>
                            @if (count($cart ?? []) > 0)
                            @foreach ($cart as $id => $item)
                                @php
                                    $product = App\Product::findOrFail($id);
                                    $cart_quantity += $item['qty'];
                                @endphp
                                <tr class="remove{{$id}}">
                                    <td colspan="2" class="prod-column">
                                        <div class="column-box">
                                            <div class="title pl-0">
                                                <a target="_blank" href="{{route('front.product.details',$product->slug)}}"><h3 class="prod-title">{{convertUtf8($item['name'])}}</h3></a>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="qty">
                                        <div class="product-quantity d-flex mb-35" id="quantity">
                                            <button type="button" class="sub">-</button>
                                            <input type="text" class="cart_qty" id="1" value="{{$item['qty']}}" />
                                            <button type="button" class="add">+</button>
                                        </div>
                                    </td>
                                    <input type="hidden" value="{{$id}}" class="product_id">
                                    <td class="unit-price">
                                        <div class="available-info">
                                            @if ($product->type == 'digital')
                                                <span class="icon fa fa-check thm-bg-clr"></span>{{__('Item(s)')}}<br>{{__('Avilable Now')}}
                                            @else
                                                @if($product->stock >= $item['qty'])
                                                    <span class="icon fa fa-check thm-bg-clr"></span>{{__('Item(s)')}}<br>{{__('Avilable Now')}}
                                                @else
                                                    <span class="icon fa fa-times thm-bg-rmv"></span>{{__('Item(s)')}}<br>{{__('Out Of Stock')}}
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                    <td class="price cart_price">{{ currency_symbol('left') }} <span>{{$item['price']}}</span> {{ currency_symbol('right') }}</td>
                                    <td class="postal_fee"><small>{{ currency_symbol('left') }} <span>{{$item['postal_fee'] * $item['qty']}}</span> {{ currency_symbol('right') }}</small></td>
                                    <td class="sub-total">{{ currency_symbol('left') }} <span>{{$item['qty'] * $item['price'] + $item['qty'] * $item['postal_fee']}}</span> {{ currency_symbol('right') }}</td>
                                    <td>
                                        <div class="remove">
                                            <div class="checkbox">
                                            <span class="fas fa-times item-remove" rel="{{$id}}" data-href="{{route('cart.item.remove',$id)}}"></span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            @endif

                            {{-- EVENTS --}}
                            @if (count($event_cart ?? []) > 0)
                            <thead class="cart-header border-bottom event_header">
                                <tr>
                                    <th class="prod-column py-3">{{__('Events')}}</th>
                                    <th class="hide-column"></th>
                                    <th class="py-3">{{__('Quantity')}}</th>
                                    <th class="availability py-3">{{__('Availability')}}</th>
                                    <th class="price py-3">{{__('Price')}}</th>
                                    <th class="py-3">{{__('Total')}}</th>
                                    <th class="py-3">{{__('Remove')}}</th>
                                </tr>
                            </thead>
                            @endif
                            @foreach ($event_cart ?? [] as $key => $item)
                            @php
                                $event = App\Event::findOrFail($item['id']);
                                $ticket = $event->eventTicket()->findOrFail($item['tkId']);
                            @endphp
                            <tr class="remove_event_{{ $event->id }}_{{$ticket->id}}">
                                <td colspan="2" class="prod-column">
                                    <div class="column-box">
                                        <div class="title pl-0">
                                            <a target="_blank" href="{{route('front.event_details',$event->slug)}}"><h3 class="prod-title">{{convertUtf8($item['title'])}}</h3></a>
                                        </div>
                                    </div>
                                </td>
                                <td class="qty">
                                    <div class="product-quantity d-flex mb-35" id="quantity-{{ $event->id . $ticket->id }}">
                                        <button type="button" class="sub">-</button>
                                        <input type="text" class="event_qty" id="1" value="{{$item['qty']}}" />
                                        <button type="button" class="add">+</button>
                                    </div>
                                </td>
                                <input type="hidden" value="{{$event->id}}" class="event_id">
                                <input type="hidden" value="{{$ticket->id}}" class="event_ticket_id">
                                <td class="unit-price">
                                    <div class="available-info">
                                        @if($ticket->available >= $item['qty'])
                                            <span class="icon fa fa-check thm-bg-clr"></span>{{__('Item(s)')}}<br>{{__('Avilable Now')}}
                                        @else
                                            <span class="icon fa fa-times thm-bg-rmv"></span>{{__('Item(s)')}}<br>{{__('Out Of Stock')}}
                                        @endif
                                    </div>
                                </td>
                                <td class="price event_price">{{ currency_symbol('left') }} <span>{{$item['cost']}}</span> {{ currency_symbol('right') }}</td>
                                <td class="event_sub_total sub-total">{{ currency_symbol('left') }} <span>{{$item['qty'] * $item['cost']}}</span> {{ currency_symbol('right') }}</td>
                                <td>
                                    <div class="remove">
                                        <div class="checkbox">
                                        <span class="fas fa-times item-remove" rel="{{ $event->id }}_{{$ticket->id}}" data-href="{{route('remove_event_from_cart', $event->id)}}"></span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                        <div class="bg-light py-5 text-center">
                            <h3 class="text-uppercase">{{__('Cart is empty!')}}</h3>
                        </div>
                    @endif
                    <input type="hidden" id="cart_quantity_total" value="{{$cart_quantity}}">
                </div>
            </div>
        </div>
        <div class="alert alert-warning mt-3" id="update_cart_message" style="display: none">
            <p>{{ __('Please click update cart button to update your cart before checkout unless you will not get the right quantity.') }}</p>
        </div>
        @if ($cart != null)
            <div class="row cart-middle">
                <div class="col-lg-6 offset-lg-6 col-sm-12">
                    <div class="update-cart float-right d-inline-block ml-4">
                        <a class="main-btn main-btn-2 proceed-checkout-btn" href="{{route('front.checkout')}}" type="button"><span>{{__('Checkout')}}</span></a>
                    </div>
                    <div class="update-cart float-right d-inline-block">
                        <button class="main-btn main-btn-2" id="cartUpdate" data-href="{{route('cart.update')}}" type="button"><span>{{__('Update Cart')}}</span></button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>

<!--====== SHOPPING CART PART ENDS ======-->

@endsection


@section('scripts')
<script>
    var symbol = "{{$bex->base_currency_symbol}}";
    var position = "{{$bex->base_currency_symbol_position}}";
</script>
<script src="{{asset('assets/front/js/jquery.ui.js')}}"></script>
<script src="{{asset('assets/front/js/product.js')}}"></script>
<script src="{{asset('assets/front/js/cart.js')}}"></script>

<script>

$(document.body).on('click','.sub, .add',function() {
    var quantity = 0;
    $('.cart_qty').each(function(i, val) {
        quantity += parseInt($(this).val());
    });


    $('#update_cart_message').hide();
    let cart_quantity = $('#cart_quantity_total').val();
    if (quantity > parseInt(cart_quantity)) {
        $('#update_cart_message').show();
    }
});

</script>
@endsection
