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
        return view('merchants.index');
    }

    public function checkUser(Request $request)
    {
        $user = User::where(
            'wallet_address',
            $request->wallet_address
        )->first();

        if (empty($user)) {
            return response()->json(['user_exist' => 'no']);
            // print_r("dfdefdfd"); exit();
        } else {
            // return redirect()
            //     ->route()
            //     ->with('WALLET_ADDRESS', $request->WALLET_ADDRESS)
            //     ->with('AMOUNT', $request->AMOUNT);
        }
    }

    public function selectOtp(Request $request)
    {
        return view('merchants.select-otp')->with('data', $request->data);
    }

    public function sendMobileOtp(Request $request)
    {
        $random_code = random_int(100000, 999999);

        $aUser = new User;
        $aUser->mobile_number = $request->mobile_number;
        $aUser->wallet_address = $request->wallet_address;
        $aUser->otp_value = $random_code;
        $saved = $aUser->save();

        if ($saved) {
            $text = 'Sending the mobile verification code: ' . $random_code;
            $otp_data['phone'] = $request->mobile_number;
            $otp_data['text'] = $text;
            $this->sendSMS($otp_data);
            return response()->json(['success' => "success"]);
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

    public function submitMobileOtp(Request $request) {
        $mobile_number = $request->wallet_address;
        $aUser = User::where('wallet_address', $wallet_address)->first();
        if (empty($aUser)) {
            return response()->json(['success' => 'fail']);
        }

        if ($aUser->opt_value == $request->submit_value) {
            return response()->json(['success' => 'success']);
        } else {
            return response()->json(['success' => 'fail']);
        }
    }

    public function sendEmailOtp(Request $request)
    {
        $random_code = random_int(100000, 999999);
        $email = $request->email_address;
        $data = ['name' => 'Verification', 'code' => $random_code];

        $aUser = new User;
        $aUser->email = $email;
        $aUser->otp_value = $random_code;
        $aUser->wallet_address = $request->wallet_address;
        $aUser->save();

        Mail::send('merchants.email-otp', $data, function ($message) use (
            $email
        ) {
            $message
                ->to($email, 'GAMERE')
                ->subject('GAMERE email confirming request');
            $message->from('JAX@gamepay.com', 'GAMERE');
        });
    }

    public function submitEmailOtp(Request $request) {
        $email = $request->wallet_address;
        $aUser = User::where('wallet_address', $wallet_address)->first();
        if (empty($aUser)) {
            return response()->json(['success' => 'fail']);
        }

        if ($aUser->opt_value == $request->submit_value) {
            return response()->json(['success' => 'success']);
        } else {
            return response()->json(['success' => 'fail']);
        }
    }

    public function sendCashlesso(Request $request)
    {
        $appId = '1026610611162108';
        $appSecret = '6a25d71992a04ae2';
        $orderType = 'SALE';
        $orderCurrency = 'INR';
        $orderCurrencyId = '356';
        $productinfo = $request['PRODUCT_DESC'];
        $orderId = random_int(100000000, 999999999);
        $orderAmount = $request['AMOUNT'];
        $customerName = $request['CUST_NAME'];
        $customerPhone = $request['CUST_PHONE'];
        $customerEmail = $request['CUST_EMAIL'];
        $customerAddress = $request['CUST_STREET_ADDRESS1'];
        $customerZip = $request['CUST_ZIP'];
        $gwUrl = 'https://uat.cashlesso.com/pgui/jsp/paymentrequest';
        $returnUrl = 'http://127.0.0.1:8000/cashlesso/response';

        $signValues = [
            'PAY_ID' => $appId,
            'ORDER_ID' => $orderId,
            'AMOUNT' => $orderAmount,
            'TXNTYPE' => $orderType,
            'CUST_NAME' => $customerName,
            'CUST_STREET_ADDRESS1' => $customerAddress,
            'CUST_ZIP' => $customerZip,
            'CUST_PHONE' => $customerPhone,
            'CUST_EMAIL' => $customerEmail,
            'PRODUCT_DESC' => $request['PRODUCT_DESC'],
            'CURRENCY_CODE' => $orderCurrencyId,
            'RETURN_URL' => $returnUrl,
        ];

        $post_data = $signValues;

        $requestHash = '';
        $signHashValue = '';
        $signHashArr = [];

        ksort($signValues);
        foreach ($signValues as $k => $v) {
            array_push($signHashArr, "$k=$v");
        }
        $signHashValue = implode('~', $signHashArr) . $appSecret;
        $requestHash = strtoupper(hash('sha256', $signHashValue));
        $post_data['HASH'] = $requestHash;
        $post_data['gwUrl'] = $gwUrl;
        // $data = http_build_query($post_data);

        return view('merchants.sender')->with('signValues', $post_data);
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
        // $deposit->currency_code = $request->CURRENCY_CODE;
        $deposit->save();

        $wallet = $request->PRODUCT_DESC;

        // mint tokens
        $exec_phrase =
            'node contract-interact.js ' . $wallet . ' ' . $request->AMOUNT;

        // print_r($exec_phrase); exit();
        chdir('../');
        exec($exec_phrase, $var, $result);
        return redirect()->route('home');
    }
}
