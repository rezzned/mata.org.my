<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::user()->status != 1) {
            if(Auth::user()->status == 2) {
                Session::flash('error', 'Your account has been expired please contact support to activate again.');
            } else {
                Session::flash('error', 'Your account will be activate in 24 hours.');
            }
            Auth::guard('web')->logout();
            return redirect(route('front.index'));
        } elseif (strtolower(Auth::user()->email_verified) == 'no') {
            Auth::guard('web')->logout();
            Session::flash('error', 'Your email is not verified!');
            return redirect(route('front.index'));
        }
        return $next($request);
    }
}
