<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use Auth;
use Hash;
use Illuminate\Support\Str;
use Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

use App\Models\Psp;
use App\Models\User;
use App\Models\Client;

class AuthController extends Controller
{
    public function signup(Request $request) {
      $rules = [
        'firstname' => 'required',
        'lastname' => 'required',
        'email' => 'required|email',
        'mobile' => 'required|numeric',
        'password' => 'required',
        'client' => 'required'
      ];

      $validator = Validator::make($request->input(), $rules);

      if ($validator->fails()) {
        $message = '';
        $errors = json_decode($validator->errors());
        foreach($errors as $key => $value) {
            $message .= $value[0] . '\n';
        }
        return response()->json(['status' => 'fail', 'error' => $message]);
      }

      $ip_string = $request->client;
      $pieces = explode("//", $ip_string);
      $client = Client::where('ip', trim($pieces[1], "/"))->first();

      if (empty($client)) {
        return response()->json(['status'=>'fail', 'message'=>'Unknown ip address']);
      }

      $test = User::where('email', $request->email)
                  ->where('client_id', $client->id)->first();

      if (!empty($test)) {
        return response()->json(['status'=>'fail', 'message'=>'Your email has already registered!']);
      }
      
      $user = new User;
      $user->first_name = $request->firstname;
      $user->last_name = $request->lastname;
      $user->email = $request->email;
      $user->mobile = $request->mobile;
      $user->password = $request->password;
      $user->email_status = "verified";
      $user->beneficiary_cd = $request->firstname . random_int(10000000, 99999999);
      $user->client_id = $client->id;
      $saved = $user->save();

      // $ip_string = $request->header('origin');
      // $pieces = explode("//", $ip_string);
      // $client = Client::where('ip', $pieces[1])->first();

      // if (empty($client)) {
      //   return response()->json(['status'=>'fail', 'message'=>'Unknown ip address']);
      // }


      // if ($saved) {
      //   $activate_email = $request->email;
      //   Mail::send('auth.activate', ['token'=> $email_token, 'username'=>$user->first_name, 'client_id'=> $client->id], function ($message) use (
      //       $activate_email
      //   ) {
      //       $message
      //           ->to($activate_email, 'Coinpaise')
      //           ->subject('Coinpaise email confirming request');
      //       $message->from('admin@coinpaise.com', 'Coinpaise');
      //   });

      return response()->json(['status'=>'success', 'message'=>'Registration to Gamepay is successful!']);      
    }    

    public function login(Request $request)
    {
        $rules = [
          'email' => 'required|email',
          'password' => 'required',
        ];
  
        $validator = Validator::make($request->input(), $rules);
  
        if ($validator->fails()) {
          $message = '';
          $errors = json_decode($validator->errors());
          foreach($errors as $key => $value) {
              $message .= $value[0] . '\n';
          }
          return response()->json(['status' => 'fail', 'message' => $message]);
        }

        $ip_string = $request->header('origin'); print_r($ip_string);
        $pieces = explode("//", $ip_string);
        $client = Client::where('ip', $pieces[1])->first();

        if (empty($client)) {
          return response()->json(['status'=>'fail', 'message'=>'Unknown ip address']);
        }
        
        $user = User::where('email', $request->email)->where('client_id', $client->id)->first();
        if (Hash::check($request->password, $user->password)) {
              // $user = User::where('email', $request->email)
              //             ->where('client_id', $client->id)->first();
              // if (empty($user)) {
              //   return response()->json(['status'=>'fail', 'message'=>'You are using the credential of our other payment site.']);
              // }

              if ($user->email_status == "verified") {
                  $authToken = $user->createToken('auth-token')->plainTextToken;
                  return response()->json([
                    'status' => 'success',
                    'access_token' => $authToken,
                    'user' => $user
                  ]);
              } else {
                  return response()->json(['status'=>'fail', 'message'=>'Your email is not verified yet']);
              }
        }
        return response()->json(['status'=>'fail', 'message'=>'The given data was invalid.']);
    }

    public function logout(Request $request) {
      auth()->user()->tokens()->delete();
      return response()->json(['status'=>'success']);
    }










    public function verifyEmail($client_id, $token)
    {
        $user = User::where('email_token', $token)->first();
  
        $message = 'Sorry your email cannot be identified.';
        $client = Client::find($client_id);

        if(!is_null($user) ) {
            if($user->email_status != 'verified') {
                $user->email_status = 'verified';
                $user->save();
                $message = "Your e-mail is verified. You can now login.";
                return redirect()->away($client->url);
            } else {
                $message = "Your e-mail is already verified. You can now login.";
            }
        }
  
        return response()->json(['message'=> $message]);
    }
}
 