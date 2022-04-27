<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

use Twilio\Rest\Client;
use Mail;
use Auth;

use App\Models\User;
use App\Models\Deposit;
use App\Models\External;
use App\Models\Payout;
use App\Models\Merchant;

class MerchantController extends Controller
{
    public function index(Request $request)
    {
        // validate key and salt
        $salt = '';

        $rules = [
          'KEY' => 'required|numeric',
          'TXNID' => 'required',
          'AMOUNT' => 'required|numeric|min: 500|max: 50000',
          'CUSTOMER_NAME' => 'bail|required|alpha|min: 2|max: 30',
          'EMAIL' => 'required|email',
          'PHONE' => 'required|numeric',
          'CRYPTO' => 'required|alpha',
          'NETWORK' => 'required|alpha',
          'ADDRESS' => 'required',
          'REMARKS' => 'required',
          'KYC_STATUS' => 'required|alpha',
          'EMAIL_STATUS' => 'required|alpha',
          'MOBILE_STATUS' => 'required|alpha',
          'CURL' => 'required|url',
          'SURL' => 'required|url',
          'EURL' => 'required|url',
          'HASH' => 'required|alpha_num',
        ];

        $validator = Validator::make($request->input(), $rules);

        if ($validator->fails()) {
            return response()->json(['status'=> 'fail', 'error'=> $validator->errors()]);
        }
        // validate hash
        $key = $request->KEY;
        $txn_id = $request->TXNID;
        $amount = $request->AMOUNT;
        $customer_name = $request->CUSTOMER_NAME;
        $email = $request->EMAIL;
        $phone = $request->PHONE;
        $crypto = $request->CRYPTO;
        $network = $request->NETWORK;
        $address = $request->ADDRESS;
        $remarks = $request->REMARKS;
        $kyc_status = $request->KYC_STATUS;
        $email_status = $request->EMAIL_STATUS;
        $mobile_status = $request->MOBILE_STATUS;
        $surl = $request->SURL;
        $eurl = $request->EURL;
        $curl = $request->CURL;
        $hash = $request->HASH;

        // select PSP for merchant
        $ip_string = $request->header('origin');
        $pieces = explode("//", $ip_string);
        $merchant = Merchant::where('ip', $pieces[1])->first();
        $salt = $merchant->salt;

        $hash_string =
            $key .
            '|' .
            $txn_id .
            '|' .
            $amount .
            '|' .
            $customer_name .
            '|' .
            $email .
            '|' .
            $phone .
            '|' .
            $crypto .
            '|' .
            $network .
            '|' .
            $address .
            '|' .
            $remarks .
            '|' .
            $kyc_status .
            '|' .
            $email_status .
            '|' .
            $mobile_status .
            '|' .
            $surl .
            '|' .
            $eurl .
            '|' .
            $curl .
            '|' .
            $salt;

        $hash_value = hash('sha256', $hash_string);

        if ($hash != $hash_value) { var_dump("hash value error");
            return view('external_user.error');
        }
        
        // check user if existing by wallet and email
        $user = External::where('address', $address)
                            ->where('email', $email)
                            ->where('email_status', 'verified')
                            ->where('kyc_status', 'verified')
                            ->where('mobile_status', 'verified')
                            ->first();
        
        if (!empty($user)) {
          $user->key = $key;
          $user->txnid = $txn_id;
          $user->amount = $amount;
          $user->cust_name = $customer_name;
          $user->email = $email;
          $user->phone = $phone;
          $user->crypto = $crypto;
          $user->network = $network;
          $user->address = $address;
          $user->remarks = $remarks;
          $user->kyc_status = $kyc_status;
          $user->email_status = $email_status;
          $user->mobile_status = $mobile_status;
          $user->surl = $surl;
          $user->eurl = $eurl;
          $user->curl = $curl;
          $user->hash = $hash;
          $saved = $user->save();
          

          $deposit = new Deposit;
          $deposit->user_id = $user->id;
          $deposit->amount = $amount;
          $deposit->crypto = $crypto;
          $deposit->network = $network;
          $deposit->remarks = $remarks;
          $deposit->is_client = 0;
          $deposit->cust_name = $customer_name;
          $deposit->wallet = $address;
          $deposit->order_id = $customer_name . random_int(10000000, 99999999);
          $deposit->caller_id = $merchant->id;
          $deposit->save();

          // add third party bank calculation
          $valuecheck = $deposit->order_id."|*".$amount."|*".urldecode($email)."|*".$phone."|*".urldecode($customer_name)."|*" . $salt;
		  $eurl = hash('sha512', $valuecheck);
          $url = 'https://coinsplashgifts.com/pgway/acquirernew/upipay.php';
          $encData=urlencode(base64_encode("firstname=$customer_name&mobile=$phone&amount=$amount&email=$email&txnid=$deposit->order_id&eurl=$eurl"));
          return redirect()->away($url."?encdata=". $encData);
        }
        
        $garbage = External::where('email', $email)
                          ->delete();

        // insert DB
        $sample = new External();
        $sample->key = $key;
        $sample->txnid = $txn_id;
        $sample->amount = $amount;
        $sample->cust_name = $customer_name;
        $sample->email = $email;
        $sample->phone = $phone;
        $sample->crypto = $crypto;
        $sample->network = $network;
        $sample->address = $address;
        $sample->remarks = $remarks;
        $sample->kyc_status = $kyc_status;
        $sample->email_status = $email_status;
        $sample->mobile_status = $mobile_status;
        $sample->surl = $surl;
        $sample->eurl = $eurl;
        $sample->curl = $curl;
        $sample->hash = $hash;
        $saved = $sample->save();

        $user_id = $sample->id;

        
        // redirect to the verification
        if ($email_status == 'verified' && $mobile_status =="verified" && $kyc_status != 'verified') {
          return redirect()->route('securepay.kyc', ['user_id' => $user_id]);
        } else if ($email_status != "verified" || $mobile_status != "verified" || $kyc_status != "verified") {
          return view('external_users.index', compact('user_id','amount', 'crypto', 'network', 'address', 'remarks', 'email_status', 'mobile_status', 'kyc_status', 'phone', 'email', 'awayUrl'));
        } else {            
            $deposit = new Deposit;
            $deposit->user_id = $user_id;
            $deposit->amount = $amount;
            $deposit->crypto = $crypto;
            $deposit->network = $network;
            $deposit->remarks = $remarks;
            $deposit->is_client = 0;
            $deposit->cust_name = $customer_name;
            $deposit->wallet = $address;
            $deposit->order_id = $customer_name . random_int(10000000, 99999999);
            $deposit->caller_id = $merchant->id;
            $deposit->save();

            $valuecheck = $deposit->order_id."|*".$amount."|*".urldecode($email)."|*".$phone."|*".urldecode($customer_name)."|*" . $salt;
            $eurl = hash('sha512', $valuecheck);
            $encData=urlencode(base64_encode("firstname=$customer_name&mobile=$phone&amount=$amount&email=$email&txnid=$deposit->order_id&eurl=$eurl"));
            $url = 'https://coinsplashgifts.com/pgway/acquirernew/upipay.php';
            $awayUrl = $url."?encdata=". $encData;

          return redirect()->away($url."?encdata=". $encData);
        }
    }

    public function sendMobileOtp(Request $request)
    {
        $random_code = random_int(100000, 999999);
        
        $sample = External::where('id', $user_id)->first();				
        $sample->otp_value = $random_code;
        $sample->phone = $request->mobile_number;
        $saved = $sample->save();

        if ($saved) {
            $text = 'Sending the mobile verification code: ' . $random_code;
            $otp_data['phone'] = $request->mobile_number;
            $otp_data['text'] = $text;
            $this->sendSMS($otp_data);
            return response()->json(['status' => 'success']);
        } else {
            return response()->json(['status' => 'fail']);
        }
    }

    protected function sendSMS($data)
    {
        $sid = 'ACff03be6cdd84b244ef95ec58ec7b4689';
        $token = '3ee55f03cf4da970e837d189232f5fcf';

        $client = new Client($sid, $token);
        return $client->messages->create($data['phone'], [
            'from' => '+14302041158',
            'body' => $data['text'],
        ]);
    }

    public function submitMobileOtp(Request $request)
    {
        $aUser = External::where('id', $request->user_id)->first();

        if (empty($aUser)) {
            return response()->json(['status' => 'fail']);
        }

        if ($aUser->otp_value == $request->submit_value) {
            $aUser->mobile_status = "verified";
            $aUser->save();
            return response()->json(['status' => 'success', 'user_id' => $aUser->id]);
        } else {
            return response()->json(['status' => 'fail']);
        }
    }

    public function sendEmailOtp(Request $request)
    {
        $random_code = random_int(100000, 999999);
        $email = $request->email_address;
        $data = ['name' => 'Verification', 'code' => $random_code];
        $aUser = External::where('id', $request->user_id)->first();

        if (empty($aUser)) {
        	return response()->json(['status' => 'fail']);
				}

        Mail::send('merchants.email-otp', $data, function ($message) use (
            $email
        ) {
            $message
                ->to($email, 'GAMERE')
                ->subject('GAMERE email confirming request');
            $message->from('JAX@gamepay.com', 'GAMERE');
        });

        $aUser->email = $email;
        $aUser->otp_value = $random_code;
        $aUser->save();

				return response()->json(['status' => 'success']);
    }

    public function submitEmailOtp(Request $request)
    {
        $aUser = External::where('id', $request->user_id)->first();

        if (empty($aUser)) {
            return response()->json(['status' => 'fail']);
        }

        if ($aUser->otp_value == $request->submit_value) {
					$aUser->email_status = 'verified';
					$aUser->save();
          return response()->json(['status' => 'success']);
        } else {
            return response()->json(['status' => 'fail']);
        }
    }








    public function getUpi(Request $request)
    {
        return view('external_users.get-payer-address')
                ->with('external_user_id', $request->external_user_id);
    }

    public function validateVpa(Request $request)
    {
        $auth_token = $this->getAuthToken(); 
        if ($this->_validateVpa($auth_token, $request->payer_address)) {
            $ext = External::where('id', $request->external_user_id)->first();
            $ext->payer_address = $request->payer_address;
            $ext->save();
            
            return redirect()->route('securepay.deposit', [
                'external_user_id' => $ext->id,
                'authToken' => $auth_token,
            ]);
        } else {
            return response()->json(['status' => 'fail']);
        }
    }

    public function deposit(Request $request)
    { 
        $user = External::where('id', $request->external_user_id)->first();      
      
        $pay_id = env('PAY_ID');
        $orderAmount = $user->amount;
        $orderId = random_int(1000000, 9999999);
        $orderCurrencyId = '356';
        $payeAddress = $user->payer_address;
        $customerEmail = $user->email;
        $customerPhone = $user->phone;
        $productinfo = 'GAMERER';
        $customerName = $user->cust_name;
        $customerId = $orderId;

        $gwUrl = 'https://uat.cashlesso.com/pgws/upi/initiateCollect';
        $returnUrl = 'http://gamepay.online/upi/response';

        $signValues = [
            'PAY_ID' => $pay_id,
            'AMOUNT' => $orderAmount,
            'ORDER_ID' => $orderId,
            'CURRENCY_CODE' => $orderCurrencyId,
            'PAYER_ADDRESS' => $payeAddress,
            'CUST_EMAIL' => $customerEmail,
            'CUST_PHONE' => $customerPhone,
            'PRODUCT_DESC' => $productinfo,
            'CUST_NAME' => $customerName,
            'CUST_ID' => $customerId,
        ];

        $post_data = $signValues;

        $requestHash = '';
        $signHashValue = '';
        $signHashArr = [];

        ksort($signValues);
        foreach ($signValues as $k => $v) {
            array_push($signHashArr, "$k=$v");
        }
        $signHashValue = implode('~', $signHashArr) . env('SALT');
        $requestHash = strtoupper(hash('sha256', $signHashValue));
        $post_data['HASH'] = $requestHash;

        $curlCollet = curl_init();

        curl_setopt_array($curlCollet, [
            CURLOPT_URL => 'https://uat.cashlesso.com/pgws/upi/initiateCollect',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
                                    "PAY_ID":  "' . env('PAY_ID') . '",
                                    "AMOUNT":  "' . $orderAmount . '",
                                    "ORDER_ID":  "' . $orderId . '",
                                    "CURRENCY_CODE":  "' . $orderCurrencyId . '",
                                    "PAYER_ADDRESS":  "' . $payeAddress . '",
                                    "CUST_EMAIL":  "' . $customerEmail . '",
                                    "CUST_PHONE":  "' . $customerPhone . '",
                                    "HASH":  "' . $requestHash . '",
                                    "PRODUCT_DESC":  "' . $productinfo . '",
                                    "CUST_NAME":  "' . $customerName . '",
                                    "CUST_ID":  "' . $customerId . '"
                                }',
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                "Authorization: Bearer $request->authToken",
            ],
        ]);

        $responseCollect = curl_exec($curlCollet);

        curl_close($curlCollet);

        $responsePayment = json_decode($responseCollect);

        $orderId = $responsePayment->ORDER_ID;
        $aDeposit = new Deposit();
        $aDeposit->user_id = $user->id;
        $aDeposit->is_client = 0;
        $aDeposit->created_date = $responsePayment->RESPONSE_DATE_TIME;
        $aDeposit->currency_code = $orderCurrencyId;
        $aDeposit->pay_id = $pay_id;
        $aDeposit->order_id = $orderId;
        $aDeposit->amount = $orderAmount;
        $aDeposit->cust_name = $customerName;
        $aDeposit->hash = $responsePayment->HASH;
        $aDeposit->email = $customerEmail;
        $aDeposit->phone = $customerPhone;
        $aDeposit->payer_address = $payeAddress;
        $aDeposit->wallet = $user->address;
        $aDeposit->product_desc = $productinfo;
        $aDeposit->network = $user->network;
        $aDeposit->crypto = $user->crypto;
        $aDeposit->key = $user->key;
        $aDeposit->remarks = $user->remarks;

        if (!empty($responsePayment->TXN_ID)) {
            $aDeposit->txnid = $responsePayment->TXN_ID;
        }

        if (!empty($responsePayment->STATUS)) {
            $aDeposit->status = $responsePayment->STATUS;
        }
        
        if (!empty($responsePayment->TOTAL_AMOUNT)) {
            $aDeposit->total_amount = $responsePayment->TOTAL_AMOUNT;
        }
        
        if (!empty($responsePayment->ACQ_ID)) {
            $aDeposit->acq_id = $responsePayment->ACQ_ID;
        }

        $aDeposit->save();

        if (
            $responsePayment->RESPONSE_CODE == 000 &&
            $responsePayment->STATUS == 'Sent to Bank'
        ) {
            // $data['status']=$responsePayment->STATUS;
            // $data['responseCode']=$responsePayment->RESPONSE_CODE;
            $orderId = $responsePayment->ORDER_ID;

            //$this->load->view('upipay',$data);
            // $orderId=base64_encode($responsePayment->ORDER_ID);
            // redirect("../responsehandle.php?id=$orderId");
            return view('merchants.upi-response')->with('orderId', $orderId);
        } else {
            // if(isset($responsePayment->STATUS)){
            // 	$data['status']=$responsePayment->STATUS;
            // }else{
            // 	$data['status']='';
            // }
            // $data['responseCode']=$responsePayment->RESPONSE_CODE;
            // $orderId=base64_encode($responsePayment->ORDER_ID);
            $orderId = $responsePayment->ORDER_ID;
            //$this->load->view('upipay',$data);
            return view('merchants.upi-response')->with('orderId', $orderId);
            // redirect("../responsehandle.php?id=$orderId");
        }
    }

    public function upiResponse(Request $request)
    {
        $aDeposit = Deposit::where('order_id', $reqeust->ORDER_ID)->first();
        $aDeposit->status = $request->STATUS;
        $aDeposit->auto_refund_eligible = $request->AUTO_REFUND_ELIGIBLE;
        $aDeposit->status = $request->STATUS;
        $aDeposit->save();

        $sample = Deposit::where('wallet', $aDeposit->wallet)->get();
        $num = $sample->count();
        // mint tokens
        $exec_phrase =
            'node contract-interact.js ' .
            $aDeposit->wallet .
            ' ' .
            $aDeposit->amount .
            ' ' .
            $num;

        chdir('../');
        exec($exec_phrase, $var, $result);
    }

    public function mintManual(Request $request)
    {
        $aDeposit = Deposit::where('id', $request->id)->first();

        $deposits = Deposit::where('wallet', $request->wallet)->get();
        $num = $deposits->count();

        $exec_phrase = 'node contract-interact.js ' . $aDeposit->wallet . ' ' . $aDeposit->amount;

        chdir('../');
        exec($exec_phrase, $var, $result);

        $aDeposit->status = 'Success';
        $aDeposit->save();
        return redirect()->route('admin.deposits');
    }

    public function responseCashlesso(Request $request)
    {
        $test = Deposit::where('txnid', $request->TXN_ID)->first();
        if (!empty($test)) {
            print_r('Already existed!');
            return redirect()->route('home');
        }

        $deposit = new Deposit();
        $deposit->currency_code = $request->CURRENCY_CODE;
        $deposit->wallet = $request->PRODUCT_DESC;
        $deposit->amount = $request->AMOUNT;
        $deposit->country = $request->CARD_ISSUER_COUNTRY;
        $deposit->txnid = $request->TXN_ID;
        $deposit->bank = $request->CARD_ISSUER_BANK;
        $deposit->hash = $request->HASH;
        $deposit->payment_id = $request->PAY_ID;
        $deposit->order_id = $request->ORDER_ID;
        $deposit->firstname = $request->CUST_NAME;
        $deposit->status = $request->STATUS;
        // $deposit->currency_code = $request->CURRENCY_CODE;
        // $deposit->currency_code = $request->CURRENCY_CODE;
        // $deposit->currency_code = $request->CURRENCY_CODE;
        // $deposit->currency_code = $request->CURRENCY_CODE;
        // $deposit->currency_code = $request->as;
        $deposit->save();

        $wallet = $request->PRODUCT_DESC;

        // mint tokens
        $exec_phrase =
            'node contract-interact.js ' . $wallet . ' ' . $request->AMOUNT;

        // print_r($exec_phrase); exit();
        chdir('../');
        exec($exec_phrase, $var, $result);
        // return redirect()->route('home');
    }

    protected function getAuthToken()
    {
        $appSecret = 'ad53f6ca708448b1';

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://uat.cashlesso.com/pgws/oauth/generateToken',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
                                    "CLIENT_ID": "OWJvbzR6ODk2ZE1UVXB1NFRaVnl3MHZRL2ozWmVRRlQ4OWFwMkt3UzJvbz0=",
                                    "CLIENT_SECRET": "D5D3B6FBD9C94D1ACFDED14D6FDD0DAE84C5552078C59976DCCE406E6C780000"
                                    }',
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);
        $json = json_decode($response);
        
        $authToken = $json->AUTHENTICATION_TOKEN;
        return $authToken;
    }

    protected function _validateVpa($authToken, $payeAddress)
    {
        $signValues = [
            'PAY_ID' => env('PAY_ID'),
            'PAYER_ADDRESS' => $payeAddress,
        ];

        $requestHash = '';
        $signHashValue = '';
        $signHashArr = [];

        ksort($signValues);
        foreach ($signValues as $k => $v) {
            array_push($signHashArr, "$k=$v");
        }
        $signHashValue = implode('~', $signHashArr) . env('SALT');
        $hash = strtoupper(hash('sha256', $signHashValue));

        $curlupi = curl_init();

        curl_setopt_array($curlupi, [
            CURLOPT_URL => 'https://uat.cashlesso.com/pgws/upi/validateVpa',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
                                    "PAY_ID":  "' .  env('PAY_ID') . '",
                                    "PAYER_ADDRESS":  "' . $payeAddress . '",
                                    "HASH":"' . $hash . '"
                                }',
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                "Authorization: Bearer $authToken",
            ],
        ]);

        // Get UPI transaction inforamtion
        $responseUPI = curl_exec($curlupi);
        $responsecode = json_decode($responseUPI);

        curl_close($curlupi);

        if ($responsecode->STATUS == 'Success') {
            return true;
        } else {
            return false;
        }
    }

    public function kycIndex(Request $request) {
        if (empty($request->status))
            return view('external_users.kyc')->with('user_id', $request->user_id);
        else
            return view('external_users.pan')->with('user_id', $request->user_id)
                                        ->with('status', $request->status)
                                        ->with('pan_front', $request->pan_front)
                                        ->with('pan_back', $request->pan_back);
    }

    public function kycProcess(Request $request) {
        $user = External::where('id', $request->user_id)->first();
        $user->cust_name = $request->cust_name;
        $user->kyc_type = "veriff";
        $user->save();

        $veriff = new Verification;
        $veriff->is_external = 1;
        $veriff->user_id = $user->id;
        $veriff->veriff_id = $request->veriff_id;
        $veriff->veriff_url = $request->veriff_url;
        $veriff->sessionToken = $request->sessionToken;
        $veriff->is_verified = $request->is_verified;
        $veriff->save();
    }

    public function kycResponse(Request $request) {
        print_r("expression"); exit();
    }

    public function kycManual(Request $request) {
      $front_path = "uploads/kyc";
      $back_path = "uploads/kyc";
      $allowedfileExtension=['png','jpg','jpeg'];

      $front = $request->file('front');
      $back = $request->file('back');

      $front_check = in_array(strtolower($front->getClientOriginalExtension()), $allowedfileExtension);
      $back_check = in_array(strtolower($back->getClientOriginalExtension()), $allowedfileExtension);

      if ($front_check && $back_check) {
          $front_name = "xf" . date("Y-m-d-H-i-s") . "." . $front->getClientOriginalExtension();  
          $front->move($front_path, $front_name);
          $back_name = "xb" . date("Y-m-d-H-i-s") . "." . $back->getClientOriginalExtension();  
          $back->move($back_path, $back_name);

          $user = External::where('id', $request->user_id)->first();
          $user->kyc_type = "manual";
          $user->front_img = $front_name;
          $user->back_img = $back_name;
          $user->save();

          return redirect()->route('securepay.pan', ['user_id' => $user->id, 
                                                    'status' => 'Manual KYC images are under approval',
                                                    'pan_front' => $front_name,
                                                    'pan_back' => $back_name]);
      } else {
          return response()->json(['status' => 'fail']);
      }
    }

    public function payout(Request $request) {
      $rules = [
                'CUSTOMER_NAME' => 'bail|required|alpha',
                'EMAIL' => 'required|email',
                'PHONE' => 'required|numeric',
                'KYC_STATUS' => 'required|alpha',
                'EMAIL_STATUS' => 'required|alpha',
                'MOBILE_STATUS' => 'required|alpha',
                'PAN_STATUS' => 'required|alpha',
                'IFSC' => 'required|alpha_num|between: 4, 11',
                'ACCOUNT_NO' => 'required|numeric|digits_between: 9, 18',
                'PAYER_ADDRESS' => 'required',
                'AMOUNT' => 'required|numeric|min: 500|max: 50000',
                'CURRENCY' => 'required|alpha',
                'NETWORK' => 'required|alpha',
                'INR_VALUE' => 'required|numeric',
                'RECEIVER' => 'required|alpha_num',
                'SENDER' => 'required|alpha_num',
                'TXN_HASH' => 'required',
                'REMARKS' => 'required',
      ];

      $validator = Validator::make($request->input(), $rules);

      if ($validator->fails()) {
          return response()->json(['status'=> 'fail', 'error'=> $validator->errors()]);
      }
        

        $user = External::where('email', $request->EMAIL)->first(); 
        if (empty($user)) {
            return response()->json(['status'=>'fail', 'error' => 'Email not found']);
        } else {
            $user->pan_status = $request->PAN_STATUS;
            $user->ifsc = $request->IFSC;
            $user->account_no = $request->ACCOUNT_NO;
            $user->payer_address = $request->PAYER_ADDRESS;
            $user->crypto = $request->CURRENCY;
            $user->network = $request->NETWORK;
            $user->inr_value = $request->INR_VALUE;
            $user->remarks = $request->REMARKS;
            $user->amount = $request->AMOUNT;
            $user->address = $request->SENDER;
            $user->receiver = $request->RECEIVER;
            $user->txn_hash = $request->TXN_HASH;
            $user->save();

            // if ($request->PAN_STATUS == 'verified') {
            //   return view('external_users.payout')->with('user', $user);
            // } else {
            //   return redirect()->route('securepay.pan', ['user_id' => $user->id]);
            // }
            if ($request->PAN_STATUS != 'verified') {
              return redirect()->route('securepay.pan', ['user_id' => $user->id]);
            } else {
              return redirect()->route('securepay.payout.add', ['user_id' => $user->id]);
            }

        }
    }

    public function addPayout(Request $request) {
      $user = External::where('id', $request->user_id)->first();
      $payer_address = $user->payer_address; // $payer_address = "9213116078@yesb";
      $ifsc = $user->ifsc; // $ifsc = "ICIC0003168";
      $account_no = $user->account_no; // $account_no = '316805000799';
      // $addahar = $user->addahar?; //$addahar = '640723564873';
      $pay_id = '1016601009105737';

      //   
      if (!$this->verifyPayout($user->beneficiary_cd)) { // if not present in DB, then add
        $url = "https://uat.cashlesso.com/payout/beneficiaryMaintenance";

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'{
                                "PAY_ID":"'. $pay_id .'",
                                "BENEFICIARY_CD":"'. $user->beneficiary_cd .'",
                                "BENE_NAME": "' . $user->cust_name . '",
                                "CURRENCY_CD": "356",
                                "MOBILE_NUMBER": "'. $user->phone .'",
                                "EMAIL_ID": "' . $user->email . '",
                                
                                
                                "PAYER_ADDRESS": "'. $payer_address .'",
                                "BANK_NAME": "YESB",
                                "IFSC_CODE": "'. $ifsc .'",
                                "BENE_ACCOUNT_NO": "'. $account_no .'",
                                "ACTION":"ADD"
                                }',
          CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer 853E8CA793795D2067CA199ECE28222CBF5ACA699BE450ED3F76D49A01137A42',
            'Content-Type: application/json'
          ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $json_resp0 = json_decode($response);

        if ($json_resp0->STATUS != "Success") {
          return response()->json(['status'=>'fail', 'data' => $json_resp0]);
        }
      }

      // if present in DB, make transaction
      $order_id = $user->cust_name . random_int(10000000, 99999999);
      $amount = $user->amount;
      $comment = "test";
      $curl = curl_init();
      curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://uat.cashlesso.com/payout/v2/initateTransaction',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
                              "PAY_ID": "'. $pay_id .'",
                              "ORDER_ID": "' . $order_id . '",
                              "TXN_AMOUNT": "' . $amount . '",
                              "BENEFICIARY_CD": "' . $user->beneficiary_cd . '",
                              "BENE_COMMENT": "' . $comment . '",
                              "TXN_PAYMENT_TYPE": "NEFT"
                              } ',
        CURLOPT_HTTPHEADER => array(
          'Authorization: Bearer 853E8CA793795D2067CA199ECE28222CBF5ACA699BE450ED3F76D49A01137A42',
          'Content-Type: application/json'
        ),
      ));

      $response = curl_exec($curl);

      curl_close($curl);
      $json_resp = json_decode($response);

      $payout = new Payout;
      $payout->user_id = $user->id;
      $payout->hash = $json_resp->HASH;
      $payout->status = $json_resp->STATUS;
      $payout->beneficiary_cd = $json_resp->BENEFICIARY_CD;
      $payout->pay_id = $json_resp->PAY_ID;
      $payout->order_id = $json_resp->ORDER_ID;
      $payout->action = $json_resp->ACTION;
      $payout->txn_amount = $json_resp->TXN_AMOUNT;
      $payout->response_message = $json_resp->RESPONSE_MESSAGE;
      $payout->txn_payment_type = $json_resp->TXN_PAYMENT_TYPE;
      $payout->total_amount = $json_resp->TOTAL_AMOUNT;
      $payout->txn_hash = $user->txn_hash;
      $payout->remarks = $user->remarks;
      $payout->sender = $user->address;
      $payout->receiver = $user->receiver;
      $payout->network = $user->network;
      $payout->currency = $user->crypto;
      $payout->inr_value = $user->inr_value;
      $payout->is_external = 1;
      $saved = $payout->save();

      if ($saved) {
          return response()->json(['status' => 'success']);
      } else {
          return response()->json(['status' => 'false', 'data' => $json_resp]);
      }

    }
  
    protected function verifyPayout($beneficiary_cd) {
      $url = "https://uat.cashlesso.com/payout/beneficiaryMaintenance";

      $curl = curl_init();
      curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://uat.cashlesso.com/payout/beneficiaryMaintenance',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
                              "PAY_ID":"1016601009105737",
                              "BENEFICIARY_CD":"'. $beneficiary_cd .'",
                              "ACTION":"VERIFY"
                              } ',
        CURLOPT_HTTPHEADER => array(
          'Authorization: Bearer 853E8CA793795D2067CA199ECE28222CBF5ACA699BE450ED3F76D49A01137A42',
          'Content-Type: application/json'
        ),
      ));

      $response = curl_exec($curl);

      curl_close($curl);
      $json_resp = json_decode($response);

      if (empty($json_resp) || $json_resp->STATUS != "Success") {
        return false;
      } else {
        return true;
      }
    }

    public function pan(Request $request) {
      if (empty($request->status))
          return view('external_users.pan')->with('user_id', $request->user_id);
      else
          return view('external_users.pan')->with('user_id', $request->user_id)
                                          ->with('status', $request->status)
                                          ->with('pan_front', $request->pan_front)
                                          ->with('pan_back', $request->pan_back);
    }

    public function panManual(Request $request) {
      $front_path = "uploads/pan";
      $back_path = "uploads/pan";
      $allowedfileExtension=['png','jpg','jpeg'];

      $front = $request->file('front');
      $back = $request->file('back');

      $front_check = in_array(strtolower($front->getClientOriginalExtension()), $allowedfileExtension);
      $back_check = in_array(strtolower($back->getClientOriginalExtension()), $allowedfileExtension);

      if ($front_check && $back_check) {
          $front_name = "pf" . date("Y-m-d-H-i-s") . "." . $front->getClientOriginalExtension();  
          $front->move($front_path, $front_name);
          $back_name = "pb" . date("Y-m-d-H-i-s") . "." . $back->getClientOriginalExtension();  
          $back->move($back_path, $back_name);

          $user = External::where('id', $request->user_id)->first();
          $user->pan_front = $front_name;
          $user->pan_back = $back_name;
          $user->save();

          return redirect()->route('securepay.pan', ['user_id' => $user->id, 
                                                    'status' => 'Manual KYC images are under approval',
                                                    'pan_front' => $front_name,
                                                    'pan_back' => $back_name]);
      } else {
          return response()->json(['status' => 'fail']);
      }
    }

    protected function selectPsp($data) {
        $psp = Psp::where('merchant_id', $data['merchant_id'])->first();
        $user = External::where('user_id', $data['user_id'])->first();

        if($psp->status == "") {
            return "psp status is not available.";
        }

        $txn_id = $user->txn_id;
        $amount = $user->amount;
        $email = $user->email;
        $phone = $user->phone;
        $customer_name = $user->cust_name;

        $valuecheck = $txn_id."|*".$amount."|*".urldecode($email)."|*".$phone."|*".urldecode($customer_name)."|*" . $SALT;
        $eurl = hash('sha512', $valuecheck);
        $encData=urlencode(base64_encode("firstname=$customer_name&mobile=$phone&amount=$amount&email=$email&txnid=$txn_id&eurl=$eurl"));
        $url = $psp->name;
        $awayUrl = $url."?encdata=". $encData;

        return $awayUrl;
    }
}
