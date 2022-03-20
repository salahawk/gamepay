<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Deposit;

class MerchantController extends Controller
{
    public function index()
    {
        return view('merchants.index');
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
