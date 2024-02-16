<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('password', function() {
    return Hash::make('password');
});

Route::fallback(function () {
    return view('errors.404');
});

Route::get('optimize', function () {
    Artisan::call('optimize');
    return redirect()->route('front.index');
})->middleware('auth:admin');

/*=======================================================
******************** Front Routes **********************
=======================================================*/

Route::post('/push', 'Front\PushController@store');
Route::get('checkexpireduser', 'Front\FrontendController@checkExpiredUser');

Route::group(['middleware' => 'setlang'], function () {
    // Route::get('/', 'Front\FrontendController@index')->name('front.index');
    Route::get('/', 'User\LoginController@showLoginForm')->name('front.index');

    //causes donation payment
    Route::post('/cause/payment', 'Front\CausesController@makePayment')->name('front.causes.payment');
    //event tickets payment
    Route::post('/event/payment', 'Front\EventController@makePayment')->name('front.event.payment');
    //causes donation payment via Paypal
    Route::post('/cause/razerms/payment/success', 'Payment\causes\RazerMsController@successPayment')->name('donation.razerms.success');
    Route::get('/cause/razerms/payment/cancel', 'Payment\causes\RazerMsController@cancelPayment')->name('donation.razerms.cancel');

    //causes donation payment via Razorpay
    Route::post('/cause/razorpay/payment/success', 'Payment\causes\RazorpayController@successPayment')->name('donation.razorpay.success');
    Route::post('/cause/razorpay/payment/cancel', 'Payment\causes\RazorpayController@cancelPayment')->name('donation.razorpay.cancel');

    Route::post('/payment/instructions', 'Front\FrontendController@paymentInstruction')->name('front.payment.instructions');

    Route::post('/sendmail', 'Front\FrontendController@sendmail')->name('front.sendmail');
    Route::post('/subscribe', 'Front\FrontendController@subscribe')->name('front.subscribe');
    Route::get('/quote', 'Front\FrontendController@quote')->name('front.quote');
    Route::post('/sendquote', 'Front\FrontendController@sendquote')->name('front.sendquote');

    Route::get('/checkout/payment/{slug1}/{slug2}', 'Front\FrontendController@loadpayment')->name('front.load.payment');

    // Package Order Routes
    Route::post('/package-order', 'Front\FrontendController@submitorder')->name('front.packageorder.submit');
    Route::get('/order-confirmation/{packageid}/{packageOrderId}', 'Front\FrontendController@orderConfirmation')->name('front.packageorder.confirmation');
    Route::get('/payment/{packageid}/cancle', 'Payment\PaymentController@paycancle')->name('front.payment.cancle');
    //Paypal Routes
    Route::post('/paypal/submit', 'Payment\PaypalController@store')->name('front.paypal.submit');
    Route::get('/paypal/{packageid}/notify', 'Payment\PaypalController@notify')->name('front.paypal.notify');
    //Stripe Routes
    Route::post('/stripe/submit', 'Payment\StripeController@store')->name('front.stripe.submit');

    // RazorPay
    Route::post('razorpay/submit', 'Payment\RazorpayController@store')->name('front.razorpay.submit');
    Route::post('razorpay/notify', 'Payment\RazorpayController@notify')->name('front.razorpay.notify');

    // RazerMS
    Route::post('razerms/submit', 'Payment\RazerMsController@store')->name('front.razerms.submit');
    Route::post('razerms/notify', 'Payment\RazerMsController@notify')->name('front.razerms.notify');

    //Offline Routes
    Route::post('/offline/{oid}/submit', 'Payment\OfflineController@store')->name('front.offline.submit');


    Route::get('/team', 'Front\FrontendController@team')->name('front.team');
    Route::get('/gallery', 'Front\FrontendController@gallery')->name('front.gallery');
    Route::get('/faq', 'Front\FrontendController@faq')->name('front.faq');

    //
    Route::get('/member-directory', 'Front\MemberDirectory@index')->name('front.member.directory');

    // change language routes
    Route::get('/changelanguage/{lang}', 'Front\FrontendController@changeLanguage')->name('changeLanguage');

    // event cart
    Route::post('/event/add_to_cart', 'Front\EventController@addToCart')->name('add_event_to_cart');
    Route::get('/cart/remove_event/{id}', 'Front\EventController@cartEventRemove')->name('remove_event_from_cart');

    // Product
    Route::get('/cart', 'Front\ProductController@cart')->name('front.cart');
    Route::get('/add-to-cart/{id}', 'Front\ProductController@addToCart')->name('add.cart');
    Route::post('/cart/update', 'Front\ProductController@updatecart')->name('cart.update');
    Route::get('/cart/item/remove/{id}', 'Front\ProductController@cartitemremove')->name('cart.item.remove');
    Route::get('/checkout', 'Front\ProductController@checkout')->name('front.checkout');
    Route::get('/checkout/{slug}', 'Front\ProductController@Prdouctcheckout')->name('front.product.checkout');
    Route::post('/coupon', 'Front\ProductController@coupon')->name('front.coupon');

    // review
    Route::post('product/review/submit', 'Front\ReviewController@reviewsubmit')->name('product.review.submit');

    // CHECKOUT SECTION
    Route::get('/product/payment/return', 'Payment\product\PaymentController@payreturn')->name('product.payment.return');
    Route::get('/product/payment/cancle', 'Payment\product\PaymentController@paycancle')->name('product.payment.cancel');
    Route::get('/product/paypal/notify', 'Payment\product\PaypalController@notify')->name('product.paypal.notify');
    // paypal routes
    Route::post('/product/paypal/submit', 'Payment\product\PaypalController@store')->name('product.paypal.submit');
    // stripe routes
    Route::post('/product/stripe/submit', 'Payment\product\StripeController@store')->name('product.stripe.submit');
    Route::post('/product/offline/{gatewayid}/submit', 'Payment\product\OfflineController@store')->name('product.offline.submit');

    // RazorPay
    Route::post('/product/razorpay/submit', 'Payment\product\RazorpayController@store')->name('product.razorpay.submit');
    Route::post('/product/razorpay/notify', 'Payment\product\RazorpayController@notify')->name('product.razorpay.notify');
    Route::post('/product/razorpay/cancel', 'Payment\product\RazorpayController@cancel')->name('product.razorpay.cancel');
    // RazerMS
    Route::post('/product/razerms/submit', 'Payment\product\RazermsController@store')->name('product.razerms.submit');
    Route::post('/product/razerms/notify', 'Payment\product\RazermsController@notify')->name('product.razerms.notify');
    Route::get('/product/razerms/cancel', 'Payment\product\RazermsController@cancel')->name('product.razerms.cancel');
    // CHECKOUT SECTION ENDS

    // client feedback route
    Route::get('/feedback', 'Front\FeedbackController@feedback')->name('feedback');
    Route::post('/store_feedback', 'Front\FeedbackController@storeFeedback')->name('store_feedback');
});

Route::group(['middleware' => ['web', 'setlang']], function () {
    Route::post('/login', 'User\LoginController@login')->name('user.login.submit');

    Route::get('/login/google/callback', 'User\LoginController@handleGoogleCallback')->name('front.google.callback');

    Route::get('/register', 'User\RegisterController@registerPage')->name('user-register');
    Route::post('/register/submit', 'User\RegisterController@register')->name('user-register-submit');
    Route::get('/register/verify/{token}', 'User\RegisterController@token')->name('user-register-token');
    Route::get('/forgot', 'User\ForgotController@showforgotform')->name('user-forgot');
    Route::post('/forgot', 'User\ForgotController@forgot')->name('user-forgot-submit');

    // Course Route For Front-End
    Route::post('/course/review', 'Front\CourseController@giveReview')->name('course.review');
});

/** Route For Enroll In Free Courses **/
Route::post('/free_course/enroll', 'Front\FreeCourseEnrollController@enroll')->name('free_course.enroll');

Route::get('/free_course/enroll/complete', 'Front\FreeCourseEnrollController@complete')->name('course.enroll.complete');
/** End Of Route For Enroll In Free Courses **/

/** Route For PayPal Payment To Sell The Courses **/
Route::post('/course/payment/paypal', 'Payment\Course\PayPalGatewayController@redirectToPayPal')->name('course.payment.paypal');
Route::get('/course/payment/paypal/notify', 'Payment\Course\PayPalGatewayController@notify')->name('course.paypal.notify');
Route::get('/course/payment/paypal/complete', 'Payment\Course\PayPalGatewayController@complete')->name('course.paypal.complete');
Route::get('/course/payment/paypal/cancel', 'Payment\Course\PayPalGatewayController@cancel')->name('course.paypal.cancel');
/** End Of Route For PayPal Payment To Sell The Courses **/

/** Route For Stripe Payment To Sell The Courses **/
Route::post('/course/payment/stripe', 'Payment\Course\StripeGatewayController@redirectToStripe')->name('course.payment.stripe');

Route::get('/course/payment/stripe/complete', 'Payment\Course\StripeGatewayController@complete')->name('course.stripe.complete');
/** End Of Route For Stripe Payment To Sell The Courses **/

/** Route For Stripe Payment To Sell The Courses **/
Route::post('/course/payment/razerms', 'Payment\Course\RazerMsGatewayController@redirectToRazerMs')->name('course.payment.razerms');
Route::post('/course/payment/razerms/return', 'Payment\Course\RazerMsGatewayController@razermsReturn')->name('course.razerms.return');
Route::get('/course/payment/razerms/cancel', 'Payment\Course\RazerMsGatewayController@cancel')->name('course.razerms.cancel');
Route::get('/course/payment/razerms/complete', 'Payment\Course\RazerMsGatewayController@complete')->name('course.razerms.complete');
/** End Of Route For Stripe Payment To Sell The Courses **/

/** Route For Razorpay Payment To Sell The Courses **/
Route::post('/course/payment/razorpay', 'Payment\Course\RazorpayGatewayController@redirectToRazorpay')->name('course.payment.razorpay');
Route::post('/course/payment/razorpay/notify', 'Payment\Course\RazorpayGatewayController@notify')->name('course.razorpay.notify');
Route::get('/course/payment/razorpay/complete', 'Payment\Course\RazorpayGatewayController@complete')->name('course.razorpay.complete');
Route::get('/course/payment/razorpay/cancel', 'Payment\Course\RazorpayGatewayController@cancel')->name('course.razorpay.cancel');
/** End Of Route For Razorpay Payment To Sell The Courses **/


/** Route For Offline Payment To Sell The Courses **/
Route::post('/course/offline/{gatewayid}/submit', 'Payment\Course\OfflineController@store')->name('course.offline.submit');
/** End Of Route For Offline Payment To Sell The Courses **/

Route::any('payment/common/razerms-callback', [\App\Http\Controllers\Payment\Common\RazerMsCallbackController::class, 'returnCallback']);
Route::any('payment/common/razerms-notification', [\App\Http\Controllers\Payment\Common\RazerMsCallbackController::class, 'returnCallback']);


Route::group(['middleware' => ['web', 'setlang']], function () {
    Route::get('/login', 'User\LoginController@showLoginForm')->name('user.login');
    Route::post('/login', 'User\LoginController@login')->name('user.login.submit');
    Route::get('/register', 'User\RegisterController@registerPage')->name('user-register');
    Route::post('/register/submit', 'User\RegisterController@register')->name('user-register-submit');
    Route::get('/register/verify/{token}', 'User\RegisterController@token')->name('user-register-token');
    Route::get('/forgot', 'User\ForgotController@showforgotform')->name('user-forgot');
    Route::post('/forgot', 'User\ForgotController@forgot')->name('user-forgot-submit');
});

Route::group(['prefix' => 'user', 'middleware' => ['auth', 'userstatus', 'setlang']], function () {
    // Summernote image upload
    Route::post('/summernote/upload', 'User\SummernoteController@upload')->name('user.summernote.upload');

    Route::get('/dashboard', 'User\UserController@index')->name('user-dashboard');
    Route::get('/notification', 'User\UserController@notification')->name('user-notification');
    Route::get('/reset', 'User\UserController@resetform')->name('user-reset');
    Route::post('/reset', 'User\UserController@reset')->name('user-reset-submit');
    Route::get('/profile', 'User\UserController@profile')->name('user-profile');
    Route::post('/profile', 'User\UserController@profileupdate')->name('user-profile-update');
    Route::get('/logout', 'User\LoginController@logout')->name('user-logout');
    Route::get('/shipping/details', 'User\UserController@shippingdetails')->name('shpping-details');
    Route::post('/shipping/details/update', 'User\UserController@shippingupdate')->name('user-shipping-update');
    Route::get('/billing/details', 'User\UserController@billingdetails')->name('billing-details');
    Route::post('/billing/details/update', 'User\UserController@billingupdate')->name('billing-update');
    Route::get('/orders', 'User\OrderController@index')->name('user-orders');
    Route::get('/order/{id}', 'User\OrderController@orderdetails')->name('user-orders-details');
    Route::get('/events', 'User\EventController@index')->name('user-events');
    Route::get('/event/{id}', 'User\EventController@eventdetails')->name('user-s');
    Route::post('/event/coupon', 'Front\EventController@coupon')->name('front.event.coupon');

    Route::get('/donations', 'User\DonationController@index')->name('user-donations');
    Route::get('/course_orders', 'User\CourseOrderController@index')->name('user.course_orders');
    Route::get('/course/{id}/lessons', 'User\CourseOrderController@courseLessons')->name('user.course.lessons');
    Route::get('/tickets', 'User\TicketController@index')->name('user-tickets');
    Route::get('/ticket/create', 'User\TicketController@create')->name('user-ticket-create');
    Route::get('/ticket/messages/{id}', 'User\TicketController@messages')->name('user-ticket-messages');
    Route::post('/ticket/store/', 'User\TicketController@ticketstore')->name('user.ticket.store');
    Route::post('/ticket/reply/{id}', 'User\TicketController@ticketreply')->name('user.ticket.reply');
    Route::post('/zip-file/upload', 'User\TicketController@zip_upload')->name('zip.upload');
    Route::get('/packages', 'User\UserController@packages')->name('user-packages');
    Route::get('/packages/payment/{id}', 'User\UserController@packagesPayment')->name('user-packages.payment');
    Route::post('/digital/download', 'User\OrderController@digitalDownload')->name('user-digital-download');
    Route::get('/package/orders', 'User\PackageController@index')->name('user-package-orders');
    Route::get('/package/order/{id}', 'User\PackageController@orderdetails')->name('user-package-order-details');
    Route::get('/payments', 'User\UserController@payments')->name('user-payments');
    Route::get('/cancel/membership', 'User\UserController@cancelMembership')->name('user-cancel-membership');
    Route::match(['GET', 'POST'], '/invoice', 'User\UserController@payinvoice')->name('user-invoice');
    Route::get('/cpd-points', 'User\UserController@cpdhours')->name('user-cpdhours');
    Route::post('/required-cpd-points', 'User\UserController@updateRequiredCpdhours')->name('user-update-required-cpdpoint');
    Route::post('/new-required-cpd-points', 'User\UserController@saveRequiredCpdhours')->name('user-save-required-cpdpoint');
    Route::get('/upcoming-events', 'User\UserController@upcomingEvents')->name('user-upcoming-events');
    Route::post('/ext-cert-down', 'User\UserController@extCertDown')->name('user-ext-cert-dw');
    Route::post('/request-ext-cpd-point', 'User\UserController@reqExtCPD')->name('user-request-ext-cpd');


    Route::get('/notification', 'User\UserController@notification')->name('user-notification');
    Route::get('/notification/all/read', 'User\UserController@notificationAllRead')->name('user-notification-all-read');
    Route::get('/notification/{id}/read', 'User\UserController@notificationRead')->name('user-notification-read');
    Route::get('/notification/{id}/delete', 'User\UserController@notificationDelete')->name('user-notification-delete');
    Route::get('/notification/trashed', 'User\UserController@notificationTrashed')->name('user-notification-trashed');
});

Route::get('test', function () {
    if (file_exists(base_path('test.php'))) {
        return include base_path('test.php');
    }
});

require __DIR__ . DIRECTORY_SEPARATOR . "admin.php";
require __DIR__ . DIRECTORY_SEPARATOR . "dynamic.php";
