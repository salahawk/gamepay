<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use App\Models\User;
use App\Models\Deposit;
use App\Models\External;

class ExternalUserController extends Controller
{
    public function index(Request $request)
    {
        // validate key and salt
        $salt = 'salt123456789';
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

        // check if compulsary values are empty
        if (
            empty($key) ||
            empty($txn_id) ||
            empty($amount) ||
            empty($customer_name) ||
            empty($email) ||
            empty($phone) ||
            empty($crypto) ||
            empty($network) ||
            empty($address) ||
            empty($remarks) ||
            empty($kyc_status) ||
            empty($email_status) ||
            empty($mobile_status) ||
            empty($surl) ||
            empty($eurl) ||
            empty($curl) ||
            empty($hash)
        ) {var_dump("one of the values is empty");
            return view('external_user.error');
        }

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
        // $user = User::where('wallet_address', $address)
        //                     ->where('email', $email)
        //                     ->where('email_status', 'verified')
        //                     ->where('kyc_status', 'verified')
        //                     ->where('mobile_status', 'verified')
        //                     ->where('is_external', 1)
        //                     ->first();
        $user = External::where('address', $address)
                            ->where('email', $email)
                            ->where('email_status', 'verified')
                            ->where('kyc_status', 'verified')
                            ->where('mobile_status', 'verified')
                            ->first();
        
        if (!empty($user)) { // redirect to the verification
          return redirect()->route('securepay.upi', [
              'external_user_id' => $user->id,
          ]);
        }
        
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
          return view('external_users.index', compact('user_id','amount', 'crypto', 'network', 'address', 'remarks', 'email_status', 'mobile_status', 'kyc_status', 'phone', 'email'));
        } else {
            return redirect()->route('securepay.upi', [
                'external_user_id' => $user_id,
            ]);
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
        $ext = External::where('id',$request->external_user_id)->first();
        // save external txn info
        $ext->key = $ext->key;
        $ext->txnid = $ext->txn_id;
        $ext->amount = $ext->amount;
        $ext->cust_name = $ext->cust_name;
        $ext->email = $ext->email;
        $ext->phone = $ext->mobile;
        $ext->crypto = $ext->crypto;
        $ext->network = $ext->network;
        $ext->surl = $ext->surl;
        $ext->eurl = $ext->eurl;
        $ext->curl = $ext->curl;
        $ext->remarks = $ext->remarks;
        $ext->kyc_status = $ext->kyc_status;
        $ext->email_status = $ext->email_status;
        $ext->mobile_status = $ext->mobile_status;
        $ext->hash = $ext->hash;
        $ext->save();        

        // Validate VPA
        $url = 'https://uat.cashlesso.com/pgws/upi/validateVpa';

        $pay_id = env('PAY_ID');
        $orderAmount = $ext->amount;
        $orderId = $ext->cust_name . random_int(1000, 9999);
        $orderCurrencyId = '356';
        $payeAddress = $ext->payer_address;
        $customerEmail = $ext->email;
        $customerPhone = $ext->mobile_number;
        $productinfo = 'GAMERE';
        $customerName = $ext->cust_name;
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
        $aDeposit->user_id = $ext->id;
        $aDeposit->is_external = 1;
        $aDeposit->created_date = $responsePayment->RESPONSE_DATE_TIME;
        if (!empty($responsePayment->TXN_ID)) {
            $aDeposit->txnid = $responsePayment->TXN_ID;
        }
        if (!empty($responsePayment->CURRENCY_CODE)) {
            $aDeposit->currency_code = $responsePayment->CURRENCY_CODE;
        }
        if (!empty($responsePayment->STATUS)) {
            $aDeposit->status = $responsePayment->STATUS;
        }
        $aDeposit->pay_id = $responsePayment->PAY_ID;
        $aDeposit->order_id = $responsePayment->ORDER_ID;
        $aDeposit->amount = $orderAmount;
        if (!empty($responsePayment->TOTAL_AMOUNT)) {
            $aDeposit->total_amount = $responsePayment->TOTAL_AMOUNT;
        }
        $aDeposit->cust_name = $customerName;
        $aDeposit->hash = $responsePayment->HASH;
        if (!empty($responsePayment->ACQ_ID)) {
            $aDeposit->acq_id = $responsePayment->ACQ_ID;
        }
        $aDeposit->email = $customerEmail;
        $aDeposit->phone = $customerPhone;
        $aDeposit->payer_address = $payeAddress;
        $aDeposit->wallet = $ext->wallet_address;
        $aDeposit->product_desc = $productinfo;
        $aDeposit->save();

        //echo '<pre>';
        //print_r($responsePayment);

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

        $exec_phrase =
            'node contract-interact.js ' .
            $aDeposit->wallet .
            ' ' .
            $aDeposit->amount .
            ' ' .
            $num;

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
            return view('external_users.kyc')->with('user_id', $request->user_id)->with('status', $request->status);
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

          return redirect()->route('securepay.kyc', ['user_id' => $user->id, 'status' => 'Manual KYC images are under approval']);
      } else {
          return response()->json(['status' => 'fail']);
      }
    }

    public function payout(Request $request) {
        $user = External::where('email', $request->EMAIL)->first();
        if (empty($user)) {
            return response()->json(['status'=>'Email not found']);
        } else 
            return view('external_users.payout')->with('user', $user);
    }

    public function processPayout(Request $request) {
        // $user = Auth::user();
        $user = External::where('id', $request->user_id)->first();
        $payer_address = "9213116078@yesb";
        $ifsc = "abcde123456";
        $account_no = '316805000799';
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
                                  "BENE_NAME": "' . $user->cust_name . '",
                                  "CURRENCY_CD": "356",
                                  "MOBILE_NUMBER": "'. $user->phone .'",
                                  "EMAIL_ID": "' . $user->email . '",
                                  
                                  
                                  "AADHAR_NO": "'. $user->beneficiary_cd .'",
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
          $json_resp = json_decode($response);
  
          if ($json_resp->STATUS != "Success") {
            return response()->json(['status'=>'fail', 'data' => $json_resp]);
          }
        }
  
        // if present in DB, make transaction
        $order_id = $user->first_name . random_int(100000, 99999);
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
        $payout->hash = $json_resp->hash;
        $payout->status = $json_resp->status;
        $payout->beneficiary_cd = $json_resp->beneficiary_cd;
        $payout->pay_id = $json_resp->pay_id;
        $payout->order_id = $json_resp->order_id;
        $payout->action = $json_resp->action;
        $payout->txn_amount = $json_resp->txn_amount;
        $payout->response_message = $json_resp->response_message;
        $payout->txn_payment_type = $json_resp->txn_payment_type;
        $payout->total_amount = $json_resp->total_amount;
        $payout->save();
  
  
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
}
