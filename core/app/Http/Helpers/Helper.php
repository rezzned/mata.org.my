<?php

use App\BasicExtra;
use App\CpdTransaction;
use App\Page;
use App\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

if (!function_exists('stateList')) {
    function stateList()
    {
        return [
            'Johor',
            'Kedah',
            'Kelantan',
            'Melaka',
            'Negeri Sembilan',
            'Pahang',
            'Perak',
            'Perlis',
            'Pulau Pinang',
            'Selangor',
            'Terengganu',
            'Sabah',
            'Sarawak',
            'Kuala Lumpur',
            'Labuan',
            'Putrajaya'
        ];
    }
}

if (!function_exists('productCreatePdf')) {
    function productCreatePdf($order)
    {
        ini_set("memory_limit", "999M");
        ini_set("max_execution_time", "999");

        if ($order->invoice_number) {
            @unlink(root_path('assets/front/invoices/product/' . $order->invoice_number));
        }

        $fileName = Str::random(4) . time() . '.pdf';
        $order->update(['invoice_number' => $fileName]);

        $path = root_path('assets/front/invoices/product/' . $fileName);
        $data['order'] = $order;

        Pdf::loadView('pdf.product', $data)->save($path);

        return $fileName;
    }
}

if (!function_exists('setEnvironmentValue')) {
    function setEnvironmentValue(array $values)
    {

        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);

        if (count($values) > 0) {
            foreach ($values as $envKey => $envValue) {

                $str .= "\n"; // In case the searched variable is in the last line without \n
                $keyPosition = strpos($str, "{$envKey}=");
                $endOfLinePosition = strpos($str, "\n", $keyPosition);
                $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);

                // If key does not exist, add it
                if (!$keyPosition || !$endOfLinePosition || !$oldLine) {
                    $str .= "{$envKey}={$envValue}\n";
                } else {
                    $str = str_replace($oldLine, "{$envKey}={$envValue}", $str);
                }
            }
        }

        $str = substr($str, 0, -1);
        if (!file_put_contents($envFile, $str)) return false;
        return true;
    }
}


if (!function_exists('convertUtf8')) {
    function convertUtf8($value)
    {
        return mb_detect_encoding($value, mb_detect_order(), true) === 'UTF-8' ? $value : mb_convert_encoding($value, 'UTF-8');
    }
}


if (!function_exists('make_slug')) {
    function make_slug($string)
    {
        $slug = preg_replace('/\s+/u', '-', trim($string));
        $slug = str_replace("/", "", $slug);
        $slug = str_replace("?", "", $slug);
        return $slug;
    }
}


if (!function_exists('make_input_name')) {
    function make_input_name($string)
    {
        return preg_replace('/\s+/u', '_', trim($string));
    }
}


if (!function_exists('serviceCategory')) {
    function serviceCategory($hbex = null)
    {
        $hbex = $hbex ?? BasicExtra::first();
        if ($hbex->service_category == 1) {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('slug_create')) {
    function slug_create($val)
    {
        $slug = preg_replace('/\s+/u', '-', trim($val));
        $slug = str_replace("/", "", $slug);
        $slug = str_replace("?", "", $slug);
        return $slug;
    }
}


if (!function_exists('getHref')) {
    function getHref($link)
    {
        $href = "#";

        if ($link["type"] == 'home') {
            $href = route('front.index');
        } elseif ($link["type"] == 'services' || $link["type"] == 'services-megamenu') {
            $href = route('front.services');
        } elseif ($link["type"] == 'packages') {
            $href = route('front.packages');
        } elseif ($link["type"] == 'portfolios' || $link["type"] == 'portfolios-megamenu') {
            $href = route('front.portfolios');
        } elseif ($link["type"] == 'team') {
            $href = route('front.team');
        } elseif ($link["type"] == 'career') {
            $href = route('front.career');
        } elseif ($link["type"] == 'courses' || $link["type"] == 'courses-megamenu') {
            $href = route('courses');
        } elseif ($link["type"] == 'events' || $link["type"] == 'events-megamenu') {
            $href = route('front.events');
        } elseif ($link["type"] == 'causes' || $link["type"] == 'causes-megamenu') {
            $href = route('front.causes');
        } elseif ($link["type"] == 'knowledgebase') {
            $href = route('front.knowledgebase');
        } elseif ($link["type"] == 'calendar') {
            $href = route('front.calendar');
        } elseif ($link["type"] == 'gallery') {
            $href = route('front.gallery');
        } elseif ($link["type"] == 'faq') {
            $href = route('front.faq');
        } elseif ($link["type"] == 'products' || $link["type"] == 'products-megamenu') {
            $href = route('front.product');
        } elseif ($link["type"] == 'cart') {
            $href = route('front.cart');
        } elseif ($link["type"] == 'checkout') {
            $href = route('front.checkout');
        } elseif ($link["type"] == 'blogs' || $link["type"] == 'blogs-megamenu') {
            $href = route('front.blogs');
        } elseif ($link["type"] == 'rss') {
            $href = route('front.rss');
        } elseif ($link["type"] == 'feedback') {
            $href = route('feedback');
        } elseif ($link["type"] == 'contact') {
            $href = route('front.contact');
        } elseif ($link["type"] == 'member-directory') {
            $href = route('front.member.directory');
        } elseif ($link["type"] == 'user-dashboard') {
            $href = route('user-dashboard');
        } elseif ($link["type"] == 'custom') {
            if (empty($link["href"])) {
                $href = "#";
            } else {
                $href = $link["href"];
            }
        } else {
            $pageid = (int)$link["type"];
            $page = Page::find($pageid);
            if (!empty($page)) {
                $href = route('front.dynamicPage', [$page->slug]);
            } else {
                $href = '#';
            }
        }

        return $href;
    }
}



if (!function_exists('create_menu')) {
    function create_menu($arr)
    {
        echo '<ul style="z-index: 0;">';
        foreach ($arr["children"] as $el) {

            // determine if the class is 'submenus' or not
            $class = null;
            if (array_key_exists("children", $el)) {
                $class = 'class="submenus"';
            }


            // determine the href
            $href = getHref($el);


            echo '<li ' . $class . '>';
            echo '<a  href="' . $href . '" target="' . $el["target"] . '">' . $el["text"] . '</a>';
            if (array_key_exists("children", $el)) {
                create_menu($el);
            }
            echo '</li>';
        }
        echo '</ul>';
    }
}



if (!function_exists('hex2rgb')) {
    function hex2rgb($colour)
    {
        if ($colour[0] == '#') {
            $colour = substr($colour, 1);
        }
        if (strlen($colour) == 6) {
            list($r, $g, $b) = array($colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5]);
        } elseif (strlen($colour) == 3) {
            list($r, $g, $b) = array($colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2]);
        } else {
            return false;
        }
        $r = hexdec($r);
        $g = hexdec($g);
        $b = hexdec($b);
        return array('red' => $r, 'green' => $g, 'blue' => $b);
    }
}


if (!function_exists('onlyDigitalItemsInCart')) {
    function onlyDigitalItemsInCart()
    {
        $cart = session()->get('cart');

        if (!empty($cart)) {
            foreach ($cart as $key => $cartItem) {
                if (array_key_exists('type', $cartItem) && $cartItem['type'] != 'digital') {
                    return false;
                }
            }
        }

        return true;
    }
}


if (!function_exists('containsDigitalItemsInCart')) {
    function containsDigitalItemsInCart()
    {
        $cart = session()->get('cart');

        if (!empty($cart)) {
            foreach ($cart as $key => $cartItem) {
                if (array_key_exists('type', $cartItem) && $cartItem['type'] == 'digital') {
                    return true;
                }
            }
        }

        return false;
    }
}


if (!function_exists('onlyDigitalItems')) {
    function onlyDigitalItems($order)
    {
        $oitems = $order->orderitems;

        foreach ($oitems as $key => $oitem) {
            if ($oitem->product->type ?? '' != 'digital') {
                return false;
            }
        }

        return true;
    }
}


if (!function_exists('containsDigitalItem')) {
    function containsDigitalItem($order)
    {
        $oitems = $order->orderitems;

        foreach ($oitems as $key => $oitem) {
            if ($oitem->product->type == 'digital') {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('cartLength')) {
    function cartLength()
    {
        $length = 0;
        if (session()->has('cart') && !empty(session()->get('cart'))) {
            $cart = session()->get('cart');
            foreach ($cart as $key => $cartItem) {
                $length += (float)$cartItem['qty'];
            }
        }

        return round($length, 2);
    }
}

if (!function_exists('currency_symbol')) {
    function currency_symbol($position)
    {
        $symbol = config('site.bex.base_currency_symbol');
        $symbol_position = config('site.bex.base_currency_symbol_position');
        return $symbol_position == $position ? $symbol : '';
    }
}

if (!function_exists('currency_format')) {
    function currency_format($amount)
    {
        return currency_symbol('left') . $amount . currency_symbol('right');
    }
}

if (!function_exists('cartTotal')) {
    function cartTotal()
    {
        $total = 0;
        if (session()->has('cart') && !empty(session()->get('cart'))) {
            $cart = session()->get('cart');
            foreach ($cart as $key => $cartItem) {
                $total += (float)$cartItem['price'] * (float)$cartItem['qty'];
            }
        }
        return round($total, 2) + eventCartTotal();
    }
}

if (!function_exists('cartSubTotal')) {
    function cartSubTotal()
    {
        $coupon = session()->has('coupon') && !empty(session()->get('coupon')) ? session()->get('coupon') : 0;
        $cartTotal = cartTotal();
        $subTotal = $cartTotal - $coupon;
        return round($subTotal, 2);
    }
}

if (!function_exists('eventCartTotal')) {
    function eventCartTotal()
    {
        $total = 0;
        if (Session::has("event_cart") && !empty(Session::get('event_cart'))) {
            $cart = Session::get('event_cart');
            foreach ($cart as $key => $item) {
                $total += (float)$item['cost'] * (float)$item['qty'];
            }
        }
        return round($total, 2);
    }
}

if (!function_exists('CartEventCount')) {
    function CartEventCount()
    {
        $total = 0;
        if (Session::has("event_cart") && !empty(Session::get('event_cart'))) {
            $cart = Session::get('event_cart');
            foreach ($cart as $key => $item) {
                $total += intval($item['qty']);
            }
        }
        return intval($total);
    }
}

if (!function_exists('CartProductCount')) {
    function CartProductCount()
    {
        $total = 0;
        if (Session::has("cart") && !empty(Session::get('cart'))) {
            $cart = Session::get('cart');
            foreach ($cart as $key => $item) {
                $total += intval($item['qty']);
            }
        }
        return intval($total);
    }
}

if (!function_exists('CartItemTotal')) {
    function CartItemTotal()
    {
        return (int) (CartProductCount() + CartEventCount());
    }
}


if (!function_exists('tax')) {
    function tax()
    {
        $bex = BasicExtra::first();
        $tax = $bex->tax;

        if (session()->has('cart') && !empty(session()->get('cart'))) {
            $tax = (cartSubTotal() * $tax) / 100;
        }

        return round($tax, 2);
    }
}

if (!function_exists('coupon')) {
    function coupon()
    {
        return session()->has('coupon') && !empty(session()->get('coupon')) ? round(session()->get('coupon'), 2) : 0.00;
    }
}

if (!function_exists('updateCpdPoint')) {
    function updateCpdPoint($user_id, $amount, $trxType = '+', $cpdType = null, $remarks = null)
    {
        $cpdType = $cpdType ?? 'internal';
        if (!$user_id) {
            return 0;
        }
        $user = User::findOrFail($user_id);
        switch ($trxType) {
            case '+':
                $remarks = $remarks ??  "Add Point";
                $user->cpd_point += $amount;
                break;
            case '-':
                $remarks = $remarks ??  "Deduct Point";
                $user->cpd_point -= $amount;
        }
        $user->save();
        if ($cpdType == 'internal') {
            CpdTransaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'trx_type' => $trxType,
                'remarks' => $remarks
            ]);
        }
        return $user->cpd_point ?? 0;
    }
}

if (!function_exists('dateFormat')) {
    function dateFormat($strDate, $format = 'd M, Y', $time = false)
    {
        $format = $format ?? 'd M, Y';
        if ($time) {
            $format = $format == 'd M, Y' ? $format . ' h:i A' : $format;
        }
        return \Carbon\Carbon::parse($strDate)->format($format);
    }
}
if (!function_exists('packageTotalPrice')) {
    function packageTotalPrice($package)
    {
        $activeSub = App\Subscription::where('user_id', auth()->user()->id)->where('status', 1);
        $total_price = $package->price;
        if ($activeSub->count() > 0 && $activeSub->first()->current_package_id == $package->id && in_array($package->type, ['associate_member', 'standard_member'])) {
            $total_price = $package->extend_fee;
        }

        if (($activeSub->count() > 0 && $activeSub->first()->current_package->type == 'associate_member') && $package->upgrade_fee > 0) {
            $total_price = $package->upgrade_fee;
        }

        // if ($activeSub->count() == 0 || ($activeSub->count() > 0 && $activeSub->first()->current_package->type == 'associate_member')) {
        //     $total_price += $package->entrance_fee;
        // }

        return $total_price;
    }
}

if (!function_exists('userSubscriptionType')) {
    function userSubscriptionType()
    {
        if (!auth()->check() || !isset(auth()->user()->subscription)) {
            return 'none_member';
        }

        return auth()->user()->subscription->current_package->type;
    }
}

if (!function_exists('eventTicket')) {
    function eventTicket($eventTicket)
    {
        $none_member = $associate_member = $standard_member = '';
        foreach ($eventTicket as $value) {
            if ($value->type == App\EventTicket::NONE_MEMBER) $none_member = $value;
            if ($value->type == App\EventTicket::ASSOCIATE_MEMBER) $associate_member = $value;
            if ($value->type == App\EventTicket::STANDARD_MEMBER) $standard_member = $value;
        }
        $auth_subscrition = auth()->check() ? auth()->user()->subscription : null;
        $none_member_check = isset($none_member) && (!auth()->user() || !isset($auth_subscrition));
        $associate_member_check = isset($associate_member) && (auth()->user()
            && ($auth_subscrition && (isset($auth_subscrition->current_package) && $auth_subscrition->current_package->type == 'associate_member')));
        $standard_member_check = isset($standard_member) && (auth()->user()
            && ($auth_subscrition && (isset($auth_subscrition->current_package) && $auth_subscrition->current_package->type == 'standard_member')));

        $ticket_data = null;
        if ($none_member_check) {
            $ticket_data = $none_member;
        } elseif ($associate_member_check) {
            $ticket_data = $associate_member;
        } elseif ($standard_member_check) {
            $ticket_data = $standard_member;
        }

        return $ticket_data;
    }
}

if (!function_exists('root_path')) {
    function root_path($path = '')
    {
        return dirname(base_path()) . '/' . trim($path, "/");
    }
}

if (!function_exists('carbon_parse')) {
    function carbon_parse($time, $tz = null)
    {
        return Carbon::parse($time, $tz);
    }
}

if (!function_exists('unslug_str')) {
    function unslug_str($str, $jointer = '-')
    {
        $unslugged = Str::replace($jointer, ' ', $str);
        return Str::title($unslugged);
    }
}

if (!function_exists('undash_str')) {
    function undash_str($str, $jointer = '_')
    {
        $undashed = Str::replace($jointer, ' ', $str);
        return Str::title($undashed);
    }
}
if (!function_exists('checkhexcolor')) {
    function checkhexcolor($color) {
        return preg_match('/^#[a-f0-9]{6}$/i', $color);
    }
}
