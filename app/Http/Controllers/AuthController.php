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

        return redirect()->route('index')->withSuccess('Great! You have Successfully registered');
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
  
      return redirect()->route('index');//->with('message', $message);
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

            return redirect()->intended('exchange');
                        // ->withSuccess('You have Successfully loggedin');
        }
  
        return redirect()->route('index');//->withSuccess('Oppes! You have entered invalid credentials');
    }

    public function logout(Request $request) {
      Session::flush();
      Auth::logout();

      return redirect()->route('login');
    }

















    public function registerEmailInput() {
        return view('auth.register.verify');
    }

    public function emailGetcode(Request $request) {
        $email = $request->email;
        $sample = User::where('email', $email)->first();

        $random_code = $this->email_otp($email);
        if ($request->type == "register") {
            if (!empty($sample)) {
                if ($sample->is_active == 1)
                    return response()->json(['success' => 'email repeated and user is already active']);
                else 
                    $sample->delete();
            }
            $user = new User;
            $user->email = $email;
            $user->email_code = $random_code;
            $user->save();
        } else if ($request->type == 'login') {
            $sample->email_code = $random_code;
            $sample->save();
        }

        return response()->json(['success' => 'success']);
    }

    public function emailVerify(Request $request) {
        $user = User::where('email', $request->email)->first();
        $code = $user->email_code;

        if ($request->code == $code) {
            $user->email_verified = 1;
            $user->save();

            Session::put('user_email', $user->email);
            Session::put('user_id', $user->id);

            return response()->json(['success' => 'success']);
        } else 
            return response()->json(['success' => 'fail']);
    }

    public function phoneGetcode(Request $request) {
        $phone_number = $request->phone_number;

        $test = User::where('phone_number', $request->phone_number)->first();

        if ($request->type == "register") {
            if (!empty($test)) {
                return response()->json(['success' => 'mobile-repeated']);
                
                if ($request->email != $test->email) 
                    return response()->json(['success' => 'invalid-try']);
            }

            $random_code = $this->phone_otp($phone_number);
            $user = User::where('email', $request->email)->first();
            $user->phone_number = $phone_number;
            $user->phone_code = $random_code;
            $user->save();
        } else if ($request->type == 'login') {
            if (empty($test)) return response()->json(['success' => 'mobile-empty']);
            $random_code = $this->phone_otp($phone_number);
            $test->phone_code = $random_code;
            $test->save();
        }

        return response()->json(['success'=>'success']);
    }

    public function loginEmailInput() {
        return view('auth.login.index');
    }

    public function phoneVerify(Request $request) {
        $user = User::where('phone_number', $request->phone_number)->first();
                    
        $code = $user->phone_code;

        if ($request->code == $code) {
            $user->phone_verified = 1;
            if ($user->email_verified == 1) {
                if ($user->username == '') $user->username = "username";
                $user->is_active = 1;
                Session::put('user_id', $user->id);
            }
            $user->save();

            return response()->json(['success' => 'success']);
        }
        else 
            return response()->json(['success' => 'fail']);
    }

    public function kycIndex() {
      $user = User::where('id', Session::get('user_id'))->first();
      $veriff_test = Veriffication::where('user_id', $user->id)->first();

      if (!empty($veriff_test)) {
        return redirect()->away($veriff_test->veriff_url);
      } else {
          $now = new DateTime();
          $timestamp = $now->format('Y-m-d\TH:i:s\Z');

          $veri_object = array("timestamp" => $timestamp, "vendorData"=> $user->id);
          $object = array("verification"=>$veri_object);
          $object_str = '{"verification":{"timestamp":"'. $timestamp .'", "vendorData": "' . $user->id . '"}}';
          // print_r($object_str); exit();
          $signature = strtolower(hash_hmac('sha256', $object_str, env('VERIFF_PRIVATE')));

          $response = Http::withHeaders([
            'X-AUTH-CLIENT' => env('VERIFF_PUBLIC'),
            'X-HMAC-SIGNATURE' => $signature,
            'CONTENT-TYPE' => 'application/json'
          ])->post('https://stationapi.veriff.com/v1/sessions',$object);

          if ($response['status'] == 'success') {
            $veriff_id = $response['verification']['id'];
            $veriff = new Veriffication;
            $veriff->user_id = $user->id;
            $veriff->veriff_id = $response['verification']['id'];
            $veriff->veriff_url = $response['verification']['url'];
            $veriff->sessionToken = $response['verification']['sessionToken'];
            $veriff->save();

            return redirect()->away($veriff->veriff_url);
          } else 
            print_r("Error occured");
        }
    }

    public function kycResult(Request $request) {
        if ($request['status'] == 'success') {
            $veriff = Veriffication::where('veriff_id', $request['verification']['id'])->first();
            if (!empty($veriff)) {
                $veriff->is_verified = 1;
                Session::put('is_kyc', 1);
                $veriff->save();
            } else {
                print_r("veriffication contains wrong data");
            }
        } else {
            print_r("veriffication contains wrong response");
        }   
    }

    // fbkyc implementation
    public function fbkyc(Request $request) {
        return view('fbveriffication.index');
    }

    public function fbkycIndex(Request $request) {
        $veriff_test = Fbveriffication::where('wallet_address', $request->wallet_address)->first();
        
        $status = '';
        if (!empty($veriff_test)) { 
            $status = $veriff_test->status;
            $username = $veriff_test->username;
        } else {
            return view('fbveriffication.unverified')->with('wallet_address', $request->wallet_address);
        }

        if ($status == '' && $veriff_test->event == "submitted") {
            return view('fbveriffication.pending')->with('wallet_address', $request->wallet_address)->with('username', $username);
        } else if ($status == 'approved'){
            return view('fbveriffication.verified')->with('wallet_address', $request->wallet_address)->with('username', $username);
        } else if ($status == 'approved' || $status == 'resubmission_requested' || $status == 'declined' || $status == 'expired' || $status == 'abandoned') {
            return view('fbveriffication.declined')->with('wallet_address', $request->wallet_address)->with('username', $username);
        } else {
            return redirect()->away($veriff_test->veriff_url);
        }
    }

    public function fbkycCheck(Request $request) {
        $sample = Fbveriffication::where('username', $request->username)->first();

        if (!empty($sample)) {
            return response()->json(['status' => 'exist', 'url' => $sample->veriff_url]);
        } else {
            return response()->json(['status' => 'no']);
        }
    }

    public function fbkycProcess(Request $request) {
        $sample = Fbveriffication::where('wallet_address', $request->wallet_address)->first();

        if (!empty($sample)) {
            return response()->json(['status' => 'exist', 'url' => $sample->veriff_url]);
        } else {
            $new_veriff = new Fbveriffication;
            $new_veriff->username = $request->username;
            $new_veriff->veriff_id = $request->veriff_id;
            $new_veriff->veriff_url = $request->veriff_url;
            $new_veriff->sessionToken = $request->sessionToken;
            $new_veriff->wallet_address = $request->wallet_address;
            $new_veriff->status = '';
            $new_veriff->save();

            return response()->json(['status' => 'success']);
        }
    }

    public function fbkycResult(Request $request) {
        if ($request['status'] == 'success') {
            $veriff = Fbveriffication::where('veriff_id', $request['verification']['id'])->first();
            if (!empty($veriff)) {
                $veriff->status = $request['verification']['status'];
                $veriff->save();
            } else {
                $resp_text = "veriffication contains wrong data";
                return response()->json(['status' => $resp_text]);
            }
        } else {
            $resp_text = "veriffication contains wrong response";
            return response()->json(['status' => $resp_text]);
        } 

        return response()->json(['status' => 'success']);
    }

    public function fbkycEvent(Request $request) {
        $veriff = Fbveriffication::where('veriff_id', $request->id)->first();
        if (!empty($veriff)) {
            $veriff->event = $request->action;
            $veriff->save();
        } else {
            $resp_text = "veriffication contains wrong event";
            return response()->json(['status' => $resp_text]);
        }
        return response()->json(['status' => 'success']);
    }
}
