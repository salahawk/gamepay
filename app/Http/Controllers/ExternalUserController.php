<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use App\Models\External_user;
use App\Models\External_deposit;

class ExternalUserController extends Controller
{
    public function index(Request $request)
    {
        // validate key and salt
        $salt = 'salt123456789';
        // validate hash
        $key = $request->KEY; var_dump($key);
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
        ) {
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
// var_dump($hash_value);
        if ($hash != $hash_value) { 
            return view('external_user.error');
        }
        
        // insert DB
        $sample = new External_user();
        $sample->key = $key;
        $sample->txn_id = $txn_id;
        $sample->amount = $amount;
        $sample->customer_name = $customer_name;
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
        // return response success->upi, fail->error
        
        if ($saved) {
            return redirect()->route('securepay.upi', [
                'external_user_id' => $sample->id,
            ]);
        } else {
            return view('external_user.error');
        }
    }

    public function getUpi(Request $request)
    {
        return view('external_user.get-payer-address')
                ->with('external_user_id', $request->external_user_id);
    }

    public function validateVpa(Request $request)
    {
        $auth_token = $this->getAuthToken(); 
        if ($this->_validateVpa($auth_token, $request->payer_address)) {
            $ext = External_user::where('id', $request->external_user_id)->first();
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
        $ext = External_user::find($request->external_user_id);

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

        $aDeposit = new External_deposit();
        $aDeposit->created_date = $responsePayment->RESPONSE_DATE_TIME;
        $aDeposit->txnid = $responsePayment->TXN_ID;
        if (!empty($responsePayment->CURRENCY_CODE)) {
            $aDeposit->currency_code = $responsePayment->CURRENCY_CODE;
        }
        $aDeposit->status = $responsePayment->STATUS;
        $aDeposit->payment_id = $responsePayment->PAY_ID;
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
        $aDeposit->wallet = $ext->wallet_address;
        $aDeposit->productinfo = $productinfo;
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

    public function kycIndex()
    {
        return view('merchants.kyc');
    }

    public function test()
    {
        return view('external_user.test');
    }
}
