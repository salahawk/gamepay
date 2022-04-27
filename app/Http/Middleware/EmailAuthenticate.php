<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EmailAuthenticate
{
   /**
    * Handle an incoming request.
    *
    * @param  \Illuminate\Http\Request $request
    * @param  \Closure $next
    * @return mixed
    */
   public function handle($request, Closure $next)
   {

       $user = auth()->user();

       if ($user->email_status != "verified") {
          //  return redirect('/verifyemail')->withErrors(['Account is not yet verified']);
          return redirect()->away($request->header('origin'), ['message' => "Email is not verified"]);
       }

       return $next($request);
   }