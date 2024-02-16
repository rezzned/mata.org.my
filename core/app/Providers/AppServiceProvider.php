<?php

namespace App\Providers;

use App\BasicExtra;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Social;
use App\Language;
use App\Menu;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        $socials = Social::orderBy('serial_number', 'ASC')->get();
        $langs = Language::all();

        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $bs = $currentLang->basic_setting;
        $be = $currentLang->basic_extended;
        $bex = $currentLang->basic_extra;
        $hbex = BasicExtra::first();

        config()->set('site.bex', $bex); // config
        config()->set('site.bs', $bs); // config

        $ulinks = $currentLang->ulinks;
        $apopups = $currentLang->popups()->where('status', 1)->orderBy('serial_number', 'ASC')->get();

        if (serviceCategory($hbex)) {
            $scats = $currentLang->scategories()->where('status', 1)->orderBy('serial_number', 'ASC')->get();
        }

        if (Menu::where('language_id', $currentLang->id)->count() > 0) {
            $menus = Menu::where('language_id', $currentLang->id)->first()->menus;
        } else {
            $menus = json_encode([]);
        }

        if ($currentLang->rtl == 1) {
            $rtl = 1;
        } else {
            $rtl = 0;
        }

        view()->composer(
            '*',
            function ($view)
            use ($bs, $be, $bex, $hbex, $scats, $apopups, $ulinks, $menus, $currentLang, $rtl) {
                $view->with('bs', $bs);
                $view->with('be', $be);
                $view->with('bex', $bex);
                $view->with('hbex', $hbex);
                if (serviceCategory($hbex)) {
                    $view->with('scats', $scats);
                }
                $view->with('apopups', $apopups);
                $view->with('ulinks', $ulinks);
                $view->with('menus', $menus);
                $view->with('currentLang', $currentLang);
                $view->with('rtl', $rtl);
            }
        );

        View::share('socials', $socials);
        View::share('langs', $langs);
    }
}
