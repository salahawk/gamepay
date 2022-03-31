<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use App\Models\Deposit;
use App\Models\User;
use Twilio\Rest\Client;
use Mail;

class MerchantController extends Controller
{
    public function index()
    {
        // echo '<script>alert("ok");</script>';
        // echo '<script>console.log("ok");</script>';
        return view('merchants.index');
    }

    public function checkUser(Request $request)
    {
        $user = User::where(
            'wallet_address',
            $request->wallet_address
        )->first();

        if (empty($user) || $user->is_verified != 1) {
            return response()->json(['user_verified' => 'no']);
        } else {
            return response()->json([
                'user_verified' => 'yes',
                'user_id' => $user->id,
            ]);
        }
    }

    public function validateVpa(Request $request)
    {
        $auth_token = $this->getAuthToken();
        if ($this->_validateVpa($auth_token, $request->payer_address)) {
            $user = User::find($request->user_id);
            $user->payer_address = $request->payer_address;
            $user->amount = $request->amount;
            $user->save();
            // return response()->json(['status' => 'success']);
            return redirect()->route('send-deposit', ['user_id' => $user->id, 'authToken' => $auth_token]);
        } else {
            return response()->json(['status' => 'fail']);
        }
    }

    public function selectOtp(Request $request)
    {
        return view('merchants.select-otp')->with('data', $request->data);
    }

    public function sendMobileOtp(Request $request)
    {
        $random_code = random_int(100000, 999999);

				$sample = User::where('wallet_address', $request->wallet_address)->first();
				if (empty($sample)) {
					$aUser = new User();
					$aUser->mobile_number = $request->mobile_number;
					$aUser->wallet_address = $request->wallet_address;
					$aUser->otp_value = $random_code;
					$saved = $aUser->save();
				} else {
					$sample->otp_value = $random_code;
					$sample->mobile_number = $request->mobile_number;
					$saved = $sample->save();
				}

        if ($saved) {
            $text = 'Sending the mobile verification code: ' . $random_code;
            $otp_data['phone'] = $request->mobile_number;
            $otp_data['text'] = $text;
            $this->sendSMS($otp_data);
            return response()->json(['success' => 'success']);
        } else {
            return view('error-500');
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
        $aUser = User::where('wallet_address', $wallet_address)->first();
        if (empty($aUser)) {
            return response()->json(['success' => 'fail']);
        }

        if ($aUser->otp_value == $request->submit_value) {
						if ($aUser->is_verified == 2) {
							$aUser->is_verified = 1;
							$aUser->cust_name = $request->cust_name;
						}
						else $aUser->is_verified = 2;
						$aUser->save();
            return response()->json(['success' => 'success']);
        } else {
            return response()->json(['success' => 'fail']);
        }
    }

    public function sendEmailOtp(Request $request)
    {
        $random_code = random_int(100000, 999999);
        $wallet_address = $request->wallet_address;
				$email = $request->email_address;
        $data = ['name' => 'Verification', 'code' => $random_code];

				$sample = User::where('wallet_address', $wallet_address)->first();

				if (empty($sample)) {
        	$aUser = new User();
					$aUser->email = $email;
					$aUser->otp_value = $random_code;
					$aUser->wallet_address = $wallet_address;
					$aUser->save();
				} else {
					$sample->otp_value = $random_code;
					$sample->email = $email;
					$sample->save();
				}

        Mail::send('merchants.email-otp', $data, function ($message) use (
            $email
        ) {
            $message
                ->to($email, 'GAMERE')
                ->subject('GAMERE email confirming request');
            $message->from('JAX@gamepay.com', 'GAMERE');
        });
    }

    public function submitEmailOtp(Request $request)
    {
        $wallet_address = $request->wallet_address;
        $aUser = User::where('wallet_address', $wallet_address)->first();
        if (empty($aUser)) {
            return response()->json(['success' => 'fail']);
        }

        if ($aUser->otp_value == $request->submit_value) {
					if ($aUser->is_verified == 2) {
						$aUser->is_verified = 1;
						$aUser->cust_name = $request->cust_name;
					}
					else $aUser->is_verified = 2;
					$aUser->save();
          return response()->json(['success' => 'success']);
        } else {
            return response()->json(['success' => 'fail']);
        }
    }

    public function sendDeposit(Request $request)
    {
        $aUser = User::find($request->user_id);

        // Validate VPA
        $url = 'https://uat.cashlesso.com/pgws/upi/validateVpa';

        $pay_id = env('PAY_ID');
        $orderAmount = $aUser->amount;
        $orderId = $aUser->cust_name . random_int(1000, 9999);
        $orderCurrencyId = '356';
        $payeAddress = $aUser->payer_address;
        $customerEmail = $aUser->email;
        $customerPhone = $aUser->mobile_number;
        $productinfo = 'GAMERE';
        $customerName = $aUser->cust_name;
        $customerId = $orderId;

        $gwUrl = 'https://uat.cashlesso.com/pgws/upi/initiateCollect';
        $returnUrl = 'http://127.0.0.1:8000/upi/response';

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
				$orderId=$responsePayment->ORDER_ID;
				
				$aDeposit = new Deposit;
				$aDeposit->created_date = $responsePayment->RESPONSE_DATE_TIME;
				if (!empty($responsePayment->TXN_ID)) {
          $aDeposit->txnid = $responsePayment->TXN_ID;
        }
				if (!empty($responsePayment->CURRENCY_CODE)) {
					$aDeposit->currency_code = $responsePayment->CURRENCY_CODE;
				}
        if (!empty($responsePayment->CURRENCY_CODE)) {
          $aDeposit->status = $responsePayment->STATUS;
        }
				$aDeposit->payment_id	 = $responsePayment->PAY_ID;
				$aDeposit->order_id = $responsePayment->ORDER_ID;
				$aDeposit->amount = $orderAmount;
				if (!empty($responsePayment->TOTAL_AMOUNT)) {
					$aDeposit->total_amount = $responsePayment->TOTAL_AMOUNT;
				}
				$aDeposit->cust_name = $customerName;
				$aDeposit->hash = $responsePayment->HASH;
				if (!empty($responsePayment->TOTAL_AMOUNT)) {
					$aDeposit->acq_id = $responsePayment->ACQ_ID;
				}
				$aDeposit->email = $customerEmail;
				$aDeposit->phone = $customerPhone;
				$aDeposit->payer_address = $payeAddress;
				$aDeposit->wallet = $aUser->wallet_address;
				$aDeposit->productinfo = $productinfo;
				$aDeposit->save();

				//echo '<pre>';
				//print_r($responsePayment);
				
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

    public function kycIndex() {
        return view('merchants.kyc');
    }
}
