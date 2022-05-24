<?php

namespace App\Http\Middleware;

use Closure;
use Session;

class AuthGamepay
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
        if (Session::get('merchant_id')) {
            return $next($request);
        }
        return redirect()->route('home');

    }
}
