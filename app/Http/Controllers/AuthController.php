<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Auth;
use Hash;
use Illuminate\Support\Str;
use Mail;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function index() {
        return view('auth.index');
    }

    public function signup(Request $request) {
      $request->validate([
          'firstname' => 'required',
          'lastname' => 'required',
          'email' => 'required|email|unique:users',
          'password' => 'required|min:6',
      ]);

      $token = Str::random(64);

      $user = new User;
      $user->first_name = $request->firstname;
      $user->last_name = $request->lastname;
      $user->email = $request->email;
      $user->mobile = $request->mobile;
      $user->password = Hash::make($request->password);
      $user->token = $token;
      $saved = $user->save();

      if ($saved) {
        $activate_email = $request->email;
        Mail::send('auth.activate', ['token'=> $token], function ($message) use (
            $activate_email
        ) {
            $message
                ->to($activate_email, 'GAMERE')
                ->subject('GAMERE email confirming request');
            $message->from('JAX@gamepay.com', 'GAMERE');
        });

        return redirect()->route('index')->with('message','Registration successful! Please verify email to continue.');
      }
      return redirect()->view('404');
    }

    public function verifyEmail($token)
    {
        $user = User::where('token', $token)->first();
  
        $message = 'Sorry your email cannot be identified.';
  
        if(!is_null($user) ) {
            if($user->email_status != 'verified') {
                $user->email_status = 'verified';
                $user->save();
                $message = "Your e-mail is verified. You can now login.";
            } else {
                $message = "Your e-mail is already verified. You can now login.";
            }
        }
  
      return redirect()->route('index')->with('message', $message);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
   
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            Session::put('user_id', Auth::user()->id);

            return redirect()->intended('exchange')->with('message', 'You have Successfully loggedin');
        }
  
        return redirect()->route('index')->with('warning', 'Oppes! You have entered invalid credentials');
    }

    public function logout(Request $request) {
      Session::flush();
      Auth::logout();

      return redirect()->route('index');
    }

    public function privacy() {
        return view('infos.privacy');
    }

    public function terms() {
        return view('infos.terms');
    }

    public function contact() {
        return view('infos.contact');
    }

    public function refundPolicy() {
        return view('infos.refund-policy');
    }
}
