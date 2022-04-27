<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Twilio\Rest\Client;
use Mail;
use Auth;
use Illuminate\Support\Facades\Session;

use App\Models\Deposit;
use App\Models\User;
use App\Models\Verification;
use App\Models\Payout;


class DirectUserController extends Controller
{
    public function index()
    {
        return view('direct_users.index')->with('user', Auth::user());
    }

    public function checkUser(Request $request)
    {
      $SALT = 'salt123456789';
        $user = User::where("id", Auth::user()->id)->first();
        
        if (empty($user)) {
          return redirect()->route('index');
        } else if ($user->email_status == "verified" && $user->mobile_status == "verified" && $user->kyc_status != "verified") {
          // return redirect()->route('kyc', ['user_id' => $user->id]);
          return response()->json(['user_verified' => 'kyc_need', 'user_id' => $user->id]);
        } else if ($user->email_status == "verified" && $user->mobile_status != "verified" && $user->kyc_status != "verified") {
          return response()->json(['user_verified' => 'only_email']);
        } else if ($user->email_status != "verified" || $user->mobile_status != "verified" || $user->kyc_status != "verified") {
          return response()->json(['user_verified' => 'no']);
        } else {
          $deposit = new Deposit;
          $deposit->user_id = $user->id;
          $deposit->amount = $request->amount;
          $deposit->crypto = $request->currency;
          $deposit->network = $request->network;
          $deposit->remarks = $request->remarks;
          $deposit->amount = $request->amount;
          $deposit->inr_value = $request->inr_value;
          $deposit->is_external = 0;
          $deposit->cust_name = $user->first_name;
          $deposit->wallet = $request->wallet_address;
          $deposit->order_id = $user->first_name . random_int(10000, 99999);
          $deposit->save();

            // return response()->json([
            //     'user_verified' => 'yes',
            //     'deposit_id' => $deposit->id,
            // ]);

          // add third party bank calculation
          $valuecheck = $deposit->order_id."|*".$deposit->amount."|*".urldecode($user->email)."|*".$user->phone."|*".urldecode($deposit->cust_name)."|*" . $SALT;
			    $eurl = hash('sha512', $valuecheck);
          $url = 'https://gifts.com/pgway/acquirernew/upipay.php';
          $encData=urlencode(base64_encode("firstname=$deposit->cust_name&mobile=$user->phone&amount=$deposit->amount&email=$user->email&txnid=$deposit->order_id&eurl=$eurl"));
          return response()->json(['user_verified' => 'yes', 'url' => $url."?encdata=". $encData]);
          // return redirect()->away($url."?encdata=". $encData);
        }
    }

    public function validateVpa(Request $request)
    {
        $auth_token = $this->getAuthToken();
        if ($this->_validateVpa($auth_token, $request->payer_address)) {
            $deposit = Deposit::where('id', $request->deposit_id)->first();
            $deposit->payer_address = $request->payer_address;
            $deposit->save();
            // return response()->json(['status' => 'success']);
            return redirect()->route('send-deposit', ['deposit_id' => $request->deposit_id, 'authToken' => $auth_token]);
        } else {
            return response()->json(['status' => 'fail']);
        }
    }

    public function sendMobileOtp(Request $request)
    {
        $random_code = random_int(100000, 999999);
        if (!empty($request->user_id)) {
          $sample = User::where('id', $request->user_id)->first();
        } else {
          $sample = User::where('id', Auth::user()->id)->first();
        }

				if (empty($sample)) {
					$aUser = new User();
					$aUser->mobile = $request->mobile_number;
					$aUser->wallet_address = $request->wallet_address;
					$aUser->otp_value = $random_code;
          $aUser->is_external = 0;
					$saved = $aUser->save();
				} else {
					$sample->otp_value = $random_code;
					$sample->mobile = $request->mobile_number;
					$saved = $sample->save();
				}

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
        $wallet_address = $request->wallet_address;
        if (!empty($request->user_id)) {
          $aUser = User::where('id', $request->user_id)->first();
        } else {
          // $aUser = User::where('wallet_address', $wallet_address)->where('is_external', 0)->first();
          $aUser = User::where('id', Auth::user()->id)->first();
        }

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
        if (!empty($request->user_id)) {
          $aUser = User::where('id', $request->user_id)->first();
        } else {
          $wallet_address = $request->wallet_address;
				  $aUser = User::where('wallet_address', $wallet_address)->where('is_external', 0)->first();
        }
				if (empty($aUser)) {
        	$aUser = new User();
					$aUser->email = $email;
					$aUser->otp_value = $random_code;
					$aUser->wallet_address = $wallet_address;
					$aUser->amount = $request->amount;
					$aUser->crypto = $request->currency;
					$aUser->network = $request->network;
					$aUser->remarks = $request->remarks;
					$aUser->inr_value = $request->inr_value;
          $aUser->is_external = 0;
					$aUser->save();
				} else {
					// $aUser->otp_value = $random_code;
					// $aUser->email = $email;
					// $aUser->amount = $request->amount;
					// $aUser->crypto = $request->currency;
					// $aUser->network = $request->network;
					// $aUser->remarks = $request->remarks;
					// $aUser->inr_value = $request->inr_value;
          // $aUser->is_external = 0;
					// $aUser->save();
				}

        Mail::send('merchants.email-otp', $data, function ($message) use (
            $email
        ) {
            $message
                ->to($email, 'GAMERE')
                ->subject('GAMERE email confirming request');
            $message->from('JAX@gamepay.com', 'GAMERE');
        });

				return response()->json(['status' => 'success']);
    }

    public function submitEmailOtp(Request $request)
    {
        if (!empty($request->user_id)) {
          $aUser = User::where('id', $request->user_id)->first();
        } else {
          $wallet_address = $request->wallet_address;
          $aUser = User::where('wallet_address', $wallet_address)->where('is_external', 0)->first();
        }
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

    public function sendDeposit(Request $request)
    {
        $deposit = Deposit::where('id', $request->deposit_id)->first();

        $pay_id = env('PAY_ID');
        $orderAmount = $deposit->amount;
        $orderId = $deposit->order_id;
        $orderCurrencyId = '356';
        $payeAddress = $deposit->payer_address;
        $customerEmail = $deposit->user->email;
        $customerPhone = $deposit->user->mobile;
        $productinfo = 'GAMERE';
        $customerName = $deposit->cust_name;
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

				curl_setopt_array($curlCollet, array(
					CURLOPT_URL => 'https://uat.cashlesso.com/pgws/upi/initiateCollect',
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'POST',
					CURLOPT_POSTFIELDS =>'{
                                "PAY_ID":  "'.env('PAY_ID').'",
                                "AMOUNT":  "'.$orderAmount.'",
                                "ORDER_ID":  "'.$orderId.'",
                                "CURRENCY_CODE":  "'.$orderCurrencyId.'",
                                "PAYER_ADDRESS":  "'.$payeAddress.'",
                                "CUST_EMAIL":  "'.$customerEmail.'",
                                "CUST_PHONE":  "'.$customerPhone.'",
                                "HASH":  "'.$requestHash.'",
                                "PRODUCT_DESC":  "'.$productinfo.'",
                                "CUST_NAME":  "'.$customerName.'",
                                "CUST_ID":  "'.$customerId.'"
                              }',
					CURLOPT_HTTPHEADER => array(
					'Content-Type: application/json',
					"Authorization: Bearer $request->authToken") 
				));

				$responseCollect = curl_exec($curlCollet);
        curl_close($curlCollet);

				$responsePayment=json_decode($responseCollect);
			
        // save deposit transaction data
				$deposit->created_date = $responsePayment->RESPONSE_DATE_TIME;
        $deposit->currency_code = $orderCurrencyId;
				$deposit->pay_id = $pay_id;
        $deposit->order_id = $orderId;
        $deposit->email = $customerEmail;
				$deposit->phone = $customerPhone;
        $deposit->product_desc = $productinfo;

        if (!empty($responsePayment->TXN_ID)) {
          $deposit->txnid = $responsePayment->TXN_ID;
        }
        if (!empty($responsePayment->STATUS)) {
          $deposit->status = $responsePayment->STATUS;
        }
				if (!empty($responsePayment->TOTAL_AMOUNT)) {
					$deposit->total_amount = $responsePayment->TOTAL_AMOUNT;
				}
        if (!empty($responsePayment->HASH)) {
          $deposit->hash = $responsePayment->HASH;
        }
				if (!empty($responsePayment->ACQ_ID)) {
					$deposit->acq_id = $responsePayment->ACQ_ID;
				}
				
				$deposit->save();
				
				if($responsePayment->RESPONSE_CODE==000 && $responsePayment->STATUS=='Sent to Bank'){
					// $data['status']=$responsePayment->STATUS;
					// $data['responseCode']=$responsePayment->RESPONSE_CODE;
					$orderId=$responsePayment->ORDER_ID;
					
					//$this->load->view('upipay',$data);
					// $orderId=base64_encode($responsePayment->ORDER_ID);
					// redirect("../responsehandle.php?id=$orderId");
					return view('merchants.upi-response')->with('orderId', $orderId);
				}else{
					// if(isset($responsePayment->STATUS)){
					// 	$data['status']=$responsePayment->STATUS;
					// }else{
					// 	$data['status']='';
					// }
					// $data['responseCode']=$responsePayment->RESPONSE_CODE;
					// $orderId=base64_encode($responsePayment->ORDER_ID);
					$orderId=$responsePayment->ORDER_ID;
					//$this->load->view('upipay',$data);
					return view('merchants.upi-response')->with('orderId', $orderId);
					// redirect("../responsehandle.php?id=$orderId");	
				}
        
    }

    public function upiResponse(Request $request) {
        $aDeposit = Deposit::where('order_id', $reqeust->ORDER_ID)->first();
        $aDeposit->status = $request->STATUS;
        $aDeposit->auto_refund_eligible = $request->AUTO_REFUND_ELIGIBLE;
        $aDeposit->status = $request->STATUS;
        $aDeposit->save();

        $sample = Deposit::where('wallet', $aDeposit->wallet)->get();
        $num = $sample->count();
        // mint tokens
        $exec_phrase = 'node contract-interact.js ' . $aDeposit->wallet . ' ' . $aDeposit->amount . ' ' . $num;

        chdir('../');
        exec($exec_phrase, $var, $result);
    }

    public function mintManual(Request $request) {
        $aDeposit = Deposit::where('id',$request->id)->first();
        
        $deposits = Deposit::where('wallet', $request->wallet)->get();
        $num = $deposits->count();

        
        $exec_phrase = 'node contract-interact.js ' . $aDeposit->wallet . ' ' . $aDeposit->amount . ' ' . $num;
        
        chdir('../');
        exec($exec_phrase, $var, $result);
        
        $aDeposit->status = "Success";
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
                                    "PAY_ID":  "' . env('PAY_ID') .'",
                                    "PAYER_ADDRESS":  "' . $payeAddress .	'",
                                    "HASH":"' .	$hash . '"
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
        return view('direct_users.kyc')->with('user_id', $request->user_id);
			else
				return view('direct_users.kyc')->with('user_id', $request->user_id)
                                      ->with('status', $request->status)
                                      ->with('pan_front', $request->pan_front)
                                      ->with('pan_back', $request->pan_back);
    }

		public function kycProcess(Request $request) {
			$user = User::where('id', $request->user_id)->first();
			$user->kyc_type = "veriff";
			$user->save();

			$veriff = new Verification;
			$veriff->user_id = $user->id;
			$veriff->veriff_id = $request->veriff_id;
			$veriff->veriff_url = $request->veriff_url;
			$veriff->sessionToken = $request->sessionToken;
			$veriff->is_verified = $request->is_verified;
			$veriff->is_external = 0;

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
        $front_name = "f" . date("Y-m-d-H-i-s") . "." . $front->getClientOriginalExtension();  
        $front->move($front_path, $front_name);
        $back_name = "b" . date("Y-m-d-H-i-s") . "." . $back->getClientOriginalExtension();  
        $back->move($back_path, $back_name);

        $user = User::where('id', $request->user_id)->first();
        $user->kyc_type = "manual";
        $user->front_img = $front_name;
        $user->back_img = $back_name;
        $user->save();

        return redirect()->route('kyc', ['user_id' => $user->id, 
                                        'status' => 'Manual KYC images are under approval',
                                        'pan_front' => $front_name,
                                        'pan_back' => $back_name]);
      } else {
        return response()->json(['status' => 'fail']);
      }
		}

    public function portfolio(Request $request) {
      $total = 7000.34;
      $deposits = Deposit::where('user_id', Auth::user()->id)->where('is_external', 0)->get();
      $payouts = Payout::where('user_id', Auth::user()->id)->where('is_external', 0)->get();
      return view('portfolios.index')->with('deposits', $deposits)->with('payouts', $payouts)->with('total', $total);
    }

    public function profile(Request $reqeust) {
      return view('direct_users.profile')->with('user', Auth::user());
    }

    public function profileEdit(Request $reqeust) {
      return view('direct_users.profile-edit')->with('user', Auth::user());
    }

    public function processPayout(Request $request) {
      // $user = Auth::user();
      $user = User::where('id', $request->user_id)->first();
      $payer_address = "9213116078@yesb";

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
                                "PAY_ID":"1016601009105737",
                                "BENEFICIARY_CD":"'. $user->beneficiary_cd .'",
                                "BENE_NAME": "' . $user->first_name . '",
                                "CURRENCY_CD": "356",
                                "MOBILE_NUMBER": "'. $user->mobile .'",
                                "EMAIL_ID": "' . $user->email . '",
                                "ADDRESS_1": "' . $user->address1 . '",
                                "ADDRESS_2": "' . $user->address2 . '",
                                "AADHAR_NO": "'. $user->aadhar_no .'",
                                "PAYER_ADDRESS": "'. $payer_address .'",
                                "BANK_NAME": "YESB",
                                "IFSC_CODE": "'. $user->ifsc .'",
                                "BENE_ACCOUNT_NO": "'. $user->account_no .'",
                                "ACTION":"ADD"
                                }',
          CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer 853E8CA793795D2067CA199ECE28222CBF5ACA699BE450ED3F76D49A01137A42',
            'Content-Type: application/json'
          ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $json_resp1 = json_decode($response);

        if ($json_resp1->STATUS != "Success") {
          return response()->json(['status'=>'adding beneficiary fail']);
        }
      }

      // if present in DB, make transaction
      $order_id = $user->first_name . random_int(100000, 999999);
      $amount = $request->amount;
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
                              "PAY_ID": "1016601009105737",
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
      $payout->is_external = 0;
      $payout->txn_hash = $request->txn_hash;
      $payout->network = $request->network;
      $payout->currency = $request->currency;
      $payout->sender = $request->wallet_address;
      $payout->receiver = $request->receiver;
      $payout->inr_value = $request->inr_value;
      $payout->remarks = $request->remarks;
      $saved = $payout->save();

      return redirect()->route('portfolio');
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

    public function sell() {
      return view('direct_users.sell')->with('user', Auth::user());
    }
}
