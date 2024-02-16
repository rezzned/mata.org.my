<?php

use App\Permalink;
use Illuminate\Support\Facades\Route;

// Dynamic Routes
Route::group(['middleware' => ['setlang'], 'namespace' => 'Front'], function () {

    $wdPermalinks = Permalink::where('details', 1)->get();
    foreach ($wdPermalinks as $pl) {
        $type = $pl->type;
        $permalink = $pl->permalink;

        if ($type == 'package_order') {
            Route::get("$permalink/{id}", 'FrontendController@packageorder')->name('front.packageorder.index');
        } elseif ($type == 'service_details') {
            Route::get("$permalink/{slug}", 'FrontendController@servicedetails')->name('front.servicedetails');
        } elseif ($type == 'portfolio_details') {
            Route::get("$permalink/{slug}", 'FrontendController@portfoliodetails')->name('front.portfoliodetails');
        } elseif ($type == 'product_details') {
            Route::get("$permalink/{slug}", 'ProductController@productDetails')->name('front.product.details');
        } elseif ($type == 'course_details') {
            Route::get("$permalink/{slug}", 'CourseController@courseDetails')->name('course_details');
        } elseif ($type == 'cause_details') {
            Route::get("$permalink/{slug}", 'FrontendController@causeDetails')->name('front.cause_details');
        } elseif ($type == 'event_details') {
            Route::get("$permalink/{slug}", 'FrontendController@eventDetails')->name('front.event_details');
        } elseif ($type == 'career_details') {
            Route::get("$permalink/{slug}", 'FrontendController@careerdetails')->name('front.careerdetails');
        } elseif ($type == 'knowledgebase_details') {
            Route::get("$permalink/{slug}", 'FrontendController@knowledgebase_details')->name('front.knowledgebase_details');
        } elseif ($type == 'blog_details') {
            Route::get("$permalink/{slug}", 'FrontendController@blogdetails')->name('front.blogdetails');
        } elseif ($type == 'rss_details') {
            Route::get("$permalink/{slug}/{id}", 'FrontendController@rssdetails')->name('front.rssdetails');
        }
    }
});

// Dynamic Routes
Route::group(['middleware' => ['setlang']], function () {

    $wdPermalinks = Permalink::where('details', 0)->get();
    foreach ($wdPermalinks as $pl) {
        $type = $pl->type;
        $permalink = $pl->permalink;

        if ($type == 'packages') {
            $action = 'Front\FrontendController@packages';
            $routeName = 'front.packages';
        } elseif ($type == 'services') {
            $action = 'Front\FrontendController@services';
            $routeName = 'front.services';
        } elseif ($type == 'portfolios') {
            $action = 'Front\FrontendController@portfolios';
            $routeName = 'front.portfolios';
        } elseif ($type == 'products') {
            $action = 'Front\ProductController@product';
            $routeName = 'front.product';
        } elseif ($type == 'cart') {
            $action = 'Front\ProductController@cart';
            $routeName = 'front.cart';
        } elseif ($type == 'product_checkout') {
            $action = 'Front\ProductController@checkout';
            $routeName = 'front.checkout';
        } elseif ($type == 'team') {
            $action = 'Front\FrontendController@team';
            $routeName = 'front.team';
        } elseif ($type == 'courses') {
            $action = 'Front\CourseController@courses';
            $routeName = 'courses';
        } elseif ($type == 'causes') {
            $action = 'Front\FrontendController@causes';
            $routeName = 'front.causes';
        } elseif ($type == 'events') {
            $action = 'Front\FrontendController@events';
            $routeName = 'front.events';
        } elseif ($type == 'career') {
            $action = 'Front\FrontendController@career';
            $routeName = 'front.career';
        } elseif ($type == 'event_calendar') {
            $action = 'Front\FrontendController@calendar';
            $routeName = 'front.calendar';
        } elseif ($type == 'knowledgebase') {
            $action = 'Front\FrontendController@knowledgebase';
            $routeName = 'front.knowledgebase';
        } elseif ($type == 'gallery') {
            $action = 'Front\FrontendController@gallery';
            $routeName = 'front.gallery';
        } elseif ($type == 'faq') {
            $action = 'Front\FrontendController@faq';
            $routeName = 'front.faq';
        } elseif ($type == 'blogs') {
            $action = 'Front\FrontendController@blogs';
            $routeName = 'front.blogs';
        } elseif ($type == 'rss') {
            $action = 'Front\FrontendController@rss';
            $routeName = 'front.rss';
        } elseif ($type == 'contact') {
            $action = 'Front\FrontendController@contact';
            $routeName = 'front.contact';
        } elseif ($type == 'quote') {
            $action = 'Front\FrontendController@quote';
            $routeName = 'front.quote';
        } elseif ($type == 'login') {
            $action = 'User\LoginController@showLoginForm';
            $routeName = 'user.login';
        } elseif ($type == 'register') {
            $action = 'User\RegisterController@registerPage';
            $routeName = 'user-register';
        } elseif ($type == 'forget_password') {
            $action = 'User\ForgotController@showforgotform';
            $routeName = 'user-forgot';
        } elseif ($type == 'admin_login') {
            $action = 'Admin\LoginController@login';
            $routeName = 'admin.login';
            Route::get("$permalink", "$action")->name("$routeName")->middleware('guest:admin');
            continue;
        }

        Route::get("$permalink", "$action")->name("$routeName");
    }
});


// Dynamic Page Routes
Route::group(['middleware' => 'setlang'], function () {
    Route::get('/{slug}', 'Front\FrontendController@dynamicPage')->name('front.dynamicPage');
});
