<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Twilio\Rest\Client as Twiliio;
use Mail;
use Auth;
use Illuminate\Support\Facades\Session;
use DB;

use App\Models\Deposit;
use App\Models\User;
use App\Models\Verification;
use App\Models\Payout;
use App\Models\Psp;
use App\Models\Client;
use App\Models\Merchant;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
  // @params
  // amount - to be purchased
  // currency - usdt, gamerer
  // network - bsc, polygon
  // crypto - usdt, gamerer
  // remarks - bluh bluh
  // inr_value - inr value
  public function guest(Request $request)
  {
    $test = Deposit::where('wallet', $request->wallet_address)->where('status', "Captured")->first();
    if (empty($test)) {
      return response()->json(['status' => 'fail', 'message' => 'No successful transaction happened with this wallet address. Please register and verify.']);
    }

    $rules = [
      'amount' => 'required|numeric',
      'currency' => 'required',
      'network' => 'required|alpha',
      'remarks' => 'required',
      'inr_value' => 'required|numeric',
    ];

    $validator = Validator::make($request->input(), $rules);

    if ($validator->fails()) {
      return response()->json(['status' => 'fail', 'message' => $validator->errors()]);
    }

    // find predefined PSP provider based on IP address for client
    $ip_string = $request->header('origin');
    $pieces = explode("//", $ip_string);
    $client = Client::where('ip', $pieces[1])->first();
    $psp = Psp::where('client_id', $client->id)->first();

    if (empty($client) || empty($psp)) {
      return response()->json(['status' => 'fail', 'message' => 'Unknown ip address']);
    }

    $psp_key = env("PSP_KEY");

    $deposit = new Deposit;
    $deposit->user_id = $test->user_id;
    $deposit->amount = $request->amount;
    $deposit->crypto = $request->currency;
    $deposit->network = $request->network;
    $deposit->remarks = $request->remarks;
    $deposit->inr_value = $request->inr_value;
    $deposit->is_client = 1;
    $deposit->cust_name = $test->first_name;
    $deposit->wallet = $request->wallet_address;
    $deposit->order_id = strlen($test->first_name) < 3 ? $test->first_name . random_int(10000000, 99999999) : substr($test->first_name, 0, 4) . random_int(10000000, 99999999);
    $deposit->caller_id = $psp->client_id;
    $deposit->email = $test->email;
    $deposit->psp_id = $psp->id;  // have to modify later
    $deposit->save();


    // add third party bank calculation
    $valuecheck = $psp_key . "|*" . $deposit->order_id . "|*" . $deposit->amount . "|*" . urldecode($test->email) . "|*" . $test->mobile . "|*" . urldecode($deposit->cust_name) . "|*" . env('PSP_SALT');
    $hash = hash('sha512', $valuecheck);
    $url = $psp->deposit_url;
    $encData = urlencode(base64_encode("key=$psp_key&firstname=$deposit->cust_name&mobile=$test->mobile&amount=$deposit->amount&email=$test->email&txnid=$deposit->order_id&eurl=$hash"));
    return response()->json(['status' => 'success', 'url' => $url . "?encdata=" . $encData]);
  }

  // @params
  // amount - to be purchased
  // currency - usdt, gamerer
  // network - bsc, polygon
  // crypto - usdt, gamerer
  // remarks - bluh bluh
  // inr_value - inr value
  public function deposit(Request $request)
  {
    $psp_key = env("PSP_KEY");
    $user = User::find(auth()->user()->id);

    // find predefined PSP provider based on IP address for client
    $ip_string = $request->header('origin');
    $pieces = explode("//", $ip_string);
    $client = Client::where('ip', $pieces[1])->first();
    $psp = Psp::where('client_id', $client->id)->first();

    if (empty($client) || empty($psp)) {
      return response()->json(['status' => 'fail', 'message' => 'Unknown ip address']);
    }

    
    $deposit = new Deposit;
    $deposit->user_id = $user->id;
    $deposit->amount = $request->amount;
    $deposit->crypto = $request->currency;
    $deposit->network = $request->network;
    $deposit->remarks = $request->remarks;
    $deposit->inr_value = $request->inr_value;
    $deposit->is_client = 1;
    $deposit->cust_name = $user->first_name;
    $deposit->wallet = $request->wallet_address;
    $deposit->order_id = strlen($user->first_name) < 3 ? $user->first_name . random_int(10000000, 99999999) : substr($user->first_name, 0, 4) . random_int(10000000, 99999999);
    $deposit->caller_id = $psp->client_id;
    $deposit->email = $user->email;
    $deposit->psp_id = $psp->id;  // have to modify later
    $deposit->save();

    $user->current_deposit_id = $deposit->id;
    $user->save();
    
    if ($user->email_status == "verified" && $user->mobile_status == "verified" && $user->kyc_status != "verified") {
      return response()->json(['status' => 'fail', 'kyc' => 'no', 'mobile' => 'yes']);
    } else if ($user->email_status == "verified" && $user->mobile_status != "verified" && $user->kyc_status != "verified") {
      return response()->json(['status' => 'fail', 'kyc' => 'no', 'mobile' => 'no']);
    } else {
      $rules = [
        'amount' => 'required|numeric',
        'currency' => 'required',
        'network' => 'required|alpha',
        'remarks' => 'required',
        'inr_value' => 'required|numeric',
      ];

      $validator = Validator::make($request->input(), $rules);

      if ($validator->fails()) {
        return response()->json(['status' => 'fail', 'error' => $validator->errors()]);
      }

      // find predefined PSP provider based on IP address for client
      $ip_string = $request->header('origin');
      $pieces = explode("//", $ip_string);
      $client = Client::where('ip', $pieces[1])->first();
      $psp = Psp::where('client_id', $client->id)->first();

      if (empty($client) || empty($psp)) {
        return response()->json(['status' => 'fail', 'message' => 'Unknown ip address']);
      }

      // add third party bank calculation
      $valuecheck = $psp_key . "|*" . $deposit->order_id . "|*" . $deposit->amount . "|*" . urldecode($user->email) . "|*" . $user->mobile . "|*" . urldecode($deposit->cust_name) . "|*" . env('PSP_SALT');
      $hash = hash('sha512', $valuecheck);
      $url = $psp->deposit_url;
      $encData = urlencode(base64_encode("key=$psp_key&firstname=$deposit->cust_name&mobile=$user->mobile&amount=$deposit->amount&email=$user->email&txnid=$deposit->order_id&eurl=$hash"));
      return response()->json(['status' => 'success', 'url' => $url . "?encdata=" . $encData]);
    }
  }

  // @params
  // front - front image file
  // back - back image file
  public function kycManual(Request $request)
  {
    // $rules = [
    //   'front' => 'required',
    //   'back' => 'required'
    // ];

    // $validator = Validator::make($request, $rules);

    // if ($validator->fails()) {
    //   return response()->json(['status' => 'fail', 'error' => $validator->errors()]);
    // }

    $front_path = "uploads/kyc";
    $back_path = "uploads/kyc";
    $allowedfileExtension = ['png', 'jpg', 'jpeg'];

    $front = $request->file('front');
    $back = $request->file('back');

    $front_check = in_array(strtolower($front->getClientOriginalExtension()), $allowedfileExtension);
    $back_check = in_array(strtolower($back->getClientOriginalExtension()), $allowedfileExtension);

    if ($front_check && $back_check) {
      $front_name = "f" . date("Y-m-d-H-i-s") . "." . $front->getClientOriginalExtension();
      $front->move($front_path, $front_name);
      $back_name = "b" . date("Y-m-d-H-i-s") . "." . $back->getClientOriginalExtension();
      $back->move($back_path, $back_name);

      $user = User::find(auth()->user()->id);
      $user->kyc_type = "manual";
      $user->front_img = $front_name;
      $user->back_img = $back_name;
      $user->save();

      $psp_key = env("PSP_KEY");
      $ip_string = $request->header('origin');
      $pieces = explode("//", $ip_string);
      $client = Client::where('ip', $pieces[1])->first();
      $psp = Psp::where('client_id', $client->id)->first();

      if (empty($client) || empty($psp)) {
        return response()->json(['status' => 'fail', 'message' => 'Unknown ip address']);
      }

      $deposit = Deposit::where('id', $user->current_deposit_id)->first();

      $valuecheck = $psp_key . "|*" . $deposit->order_id . "|*" . $deposit->amount . "|*" . urldecode($user->email) . "|*" . $user->mobile . "|*" . urldecode($deposit->cust_name) . "|*" . env('PSP_SALT');
      $hash = hash('sha512', $valuecheck);
      $url = $psp->deposit_url;
      $encData = urlencode(base64_encode("key=$psp_key&firstname=$deposit->cust_name&mobile=$user->mobile&amount=$deposit->amount&email=$user->email&txnid=$deposit->order_id&eurl=$hash"));
      return response()->json(['status' => 'success', 'data' => $url . "?encdata=" . $encData]);
    } else {
      return response()->json(['status' => 'fail', 'message' => 'File types are not allowed image files']);
    }
  }

  // @params
  // @RETURN
  // user's all data
  public function showUser()
  {
    return response()->json(['status' => 'success', 'data' => auth()->user()]);
  }

  // @params
  // @RETURN
  // user's all data
  public function portfolio(Request $request)
  {
    $ip_string = $request->header('origin');
    $pieces = explode("//", $ip_string);
    $client = Client::where('ip', trim($pieces[1], "/"))->first();

    if (empty($client)) {
      return response()->json(['status' => 'fail', 'message' => 'Unknown ip address']);
    }

    $gamere = $this->calculatePortfolio(auth()->user()->id, $client->id, "G RUPEE", "amount");
    $gamere_inr = $this->calculatePortfolio(auth()->user()->id, $client->id, "G RUPEE", "inr_value");
    $btc = $this->calculatePortfolio(auth()->user()->id, $client->id, "BTC", "amount");
    $btc_inr = $this->calculatePortfolio(auth()->user()->id, $client->id, "BTC", "inr_value");
    $usdt = $this->calculatePortfolio(auth()->user()->id, $client->id, "USDT", "amount");
    $usdt_inr = $this->calculatePortfolio(auth()->user()->id, $client->id, "USDT", "inr_value");

    $total = $gamere_inr + $btc_inr + $usdt_inr;

    $data = array(
      [
        "asset" => "G RUPEE",
        "available" => $gamere,
        "gamere" => $gamere_inr
      ],
      [
        "asset" => "BTC",
        "available" => $btc,
        "gamere" => $btc_inr
      ],
      [
        "asset" => "USDT",
        "available" => $usdt,
        "gamere" => $usdt_inr
      ]
    );
    return response()->json(['status' => 'success', 'data' => $data, 'total' => $total]);
  }

  protected function calculatePortfolio($user_id, $client_id, $currency, $type)
  {
    if ($type == "amount") {
      $deposit = "amount";
      $payout = "txn_amount";
    } else {
      $deposit = "inr_value";
      $payout = "inr_value";
    }

    $query = "SELECT
              (
                (
                  SELECT
                    SUM(" . $deposit . ")
                  FROM
                    deposits
                  WHERE
                    deposits.user_id = '" . $user_id . "'
                  AND deposits.caller_id = '" . $client_id . "'
                  AND deposits.is_client = '1'
                  AND deposits.crypto = '" . $currency . "'
                  AND (
                    deposits.status = 'Captured'
                    OR deposits.status = 'Success'
                  )
                ) - (
                  SELECT
                  SUM(" . $payout . ")
                  FROM
                    payouts
                  WHERE
                    payouts.user_id = '" . $user_id . "'
                  AND payouts.caller_id = '" . $client_id . "'
                  AND payouts.is_external = '0'
                  AND payouts.currency = '" . $currency . "'
                  AND (
                    payouts. STATUS = 'Captured'
                    OR payouts. STATUS = 'Success'
                  )
                )
              ) AS total_sum  WHERE 1";

    $total = DB::select($query);
    return empty($total[0]->total_sum) ? 0 : $total[0]->total_sum;
  }
  // @params
  // pan - file
  // account_no - account number
  // ifsc - ifsc number
  // @RETURN
  // user's all data
  public function savePan(Request $request)
  {
    // $rules = [
    //   'ifsc' => 'required',
    //   'account_no' => 'required'
    // ];

    // $validator = Validator::make($request->input(), $rules);

    // if ($validator->fails()) {
    //   return response()->json(['status' => 'fail', 'error' => $validator->errors()]);
    // }

    $user = auth()->user();
    $user->account_no = $request->account_no;
    $user->ifsc = $request->ifsc;
    $user->payer_address = $request->payer_address;

    $pan_path = "uploads/pan";
    $allowedfileExtension = ['png', 'jpg', 'jpeg'];
    $pan = $request->file('pan');
    $pan_check = in_array(strtolower($pan->getClientOriginalExtension()), $allowedfileExtension);
    if ($pan_check) {
      $pan_name = "p" . date("Y-m-d-H-i-s") . "." . $pan->getClientOriginalExtension();
      $pan->move($pan_path, $pan_name);

      $user->pan = $pan_name;
      $user->save();
      return response()->json(['status' => 'success', 'user' => $user]);
    } else {
      return response()->json(['status' => 'fail', 'message' => 'File types are not allowed image files']);
    }
  }

  // @params
  // pan - file
  // account_no - account number
  // ifsc - ifsc number
  // @RETURN
  // user's all data
  public function processPayout(Request $request)
  {
    $user = User::find(auth()->user()->id);
    $payer_address = $user->payer_address; // $payer_address = "9213116078@yesb";
    $ifsc = $user->ifsc; // $ifsc = "ICIC0003168";
    $account_no = $user->account_no; // $account_no = '316805000799';
    // $addahar = $user->addahar?; //$addahar = '640723564873';

    // choose caller and PSP
    $ip_string = $request->header('origin');
    $pieces = explode("//", $ip_string);
    $client = Client::where('ip', $pieces[1])->first();
    $psp = Psp::where('client_id', $client->id)->first();

    if (empty($client) || empty($psp)) {
      return response()->json(['status' => 'fail', 'message' => 'Unknown ip address']);
    }

    if (!$this->verifyPayout($user->beneficiary_cd, $psp->payout_verify_url)) { // if not present in DB, then add
      $add_url = $psp->payout_add_url;

      $curl = curl_init();

      curl_setopt_array($curl, array(
        CURLOPT_URL => $add_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array(
          "BENEFICIARY_CD" => $user->beneficiary_cd,
          "BENE_NAME" => $user->first_name,
          "MOBILE_NUMBER" => $user->mobile,
          "EMAIL_ID" => $user->email,
          "PAYER_ADDRESS" => $payer_address,
          "BANK_NAME" => "YESB",
          "IFSC_CODE" => $ifsc,
          "BENE_ACCOUNT_NO" => $account_no,
          "ACTION" => "ADD",
        ),
        CURLOPT_HTTPHEADER => array(
          'Authorization: Bearer 5CFB73B65096F2C11F6BA309C0D13C3BA2E8D7D1D1B14FE3224BB0E94008EA15'
        ),
      ));

      $response0 = curl_exec($curl);
      curl_close($curl);
      $json_resp0 = json_decode($response0);

      if ($json_resp0->STATUS != "Success") {
        return response()->json(['status' => 'fail', 'message' => $json_resp0]);
      }
    }

    // if present in DB, make transaction
    $order_id = $user->first_name . random_int(10000000, 99999999);
    $amount = $request->amount;
    $comment = "client payout test";
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => $psp->payout_release_url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => array(
        "BENEFICIARY_CD" => $user->beneficiary_cd,
        "TXN_AMOUNT" => $amount,
        "BENE_COMMENT" => $comment,
        "TXN_PAYMENT_TYPE" => "UPI",
        "ORDER_ID" => $order_id,
      ),
      CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer 5CFB73B65096F2C11F6BA309C0D13C3BA2E8D7D1D1B14FE3224BB0E94008EA15',
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    $json_resp = json_decode($response);

    $payout = new Payout;
    $payout->user_id = $user->id;
    $payout->email = $user->email;
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
    $payout->txn_hash = $request->txn_hash;
    $payout->remarks = $request->remarks;
    $payout->sender = $request->wallet_address;
    $payout->receiver = $request->receiver;
    $payout->network = $request->network;
    $payout->currency = $request->currecy;
    $payout->inr_value = $request->inr_value;
    $payout->is_external = 0;
    $payout->pg_txn_message = $request->PG_TXN_MESSAGE;
    $payout->caller_id = $client->id;
    $payout->psp_id = $psp->id;
    $saved = $payout->save();

    if ($saved) {
      return response()->json(['status' => 'success']);
    } else {
      return response()->json(['status' => 'false', 'message' => $json_resp]);
    }
  }

  // @params
  // kyc data to store in DB
  // @RETURN
  // user's all data
  public function processKyc(Request $request)
  {
    $user = User::find(auth()->user()->id);
    $user->kyc_type = "veriff";
    $user->save();

    $veriff = new Verification;
    $veriff->user_id = $user->id;
    $veriff->veriff_id = $request->veriff_id;
    $veriff->veriff_url = $request->veriff_url;
    $veriff->sessionToken = $request->sessionToken;
    $veriff->is_verified = 0;
    $veriff->is_external = 0;

    $veriff->save();

    return response()->json(['status' => 'success']);
  }

  // @params
  // response from PSP for deposit
  // @return
  // status
  public function responseDeposit(Request $request)
  {
    $deposit = Deposit::where('order_id', $request->ORDER_ID)->first();

    if (empty($deposit)) {
      return response()->json(['status' => 'fail', "message" => 'Order id does not exist in DB.']);
    }
    $caller = '';
    if ($deposit->is_client) {
      $caller = Client::where('id', $deposit->caller_id)->first();
    } else {
      $caller = Merchant::where('id', $deposit->caller_id)->first();
    }

    // hash generation check
    $hash_string = "|" . env("PSP_KEY") . "|" . $request->ORDER_ID . "|" . $request->AMOUNT . "|" . $request->FIRST_NAME . "|" . $request->CUST_EMAIL . "|" . $request->STATUS . "|";

    $hash_string .= env('PSP_SALT');
    $hash = hash("sha512", $hash_string);

    // if ($hash != $request->generateHash) {
    //   return response()->json(['status' => 'fail', "message" => 'hash is wrong', "data"=>$hash_string]);
    // }

    // amount and email double check
    if ($request->AMOUNT != $deposit->amount || $request->CUST_EMAIL != $deposit->email || $request->ORDER_ID != $deposit->order_id) {
      return response()->json(['status' => 'fail', 'message' => "amount or email is incorrect in response"]);
    }

    $deposit->created_date = $request->RESPONSE_DATE_TIME;
    $deposit->phone = $request->CUST_PHONE;
    $deposit->payer_address = $request->CARD_MASK;
    $deposit->currency_code = $request->CURRENCY_CODE;
    $deposit->status = $request->STATUS;
    $deposit->amount = $request->AMOUNT;
    $deposit->email = $request->CUST_EMAIL;
    $deposit->txn_type = $request->PAYMENT_TYPE;
    $deposit->order_id = $request->ORDER_ID;
    $deposit->total_amount = $request->TOTAL_AMOUNT;
    $deposit->pg_txn_message = $request->PG_TXN_MESSAGE;
    $deposit->response_message = $request->RESPONSE_MESSAGE;
    $deposit->hash = $request->generateHash;
    $saved = $deposit->save();

    if (!$saved) {
      return response()->json(['status' => 'fail']);
    }
    $c_url = "";
    if ($deposit->is_client == 0) { // if merchant
      // send response back to merchant
      if ($request->STATUS == "Captured" || $request->STATUS == "Success") {
        $response_url = $deposit->user->surl;
        $c_url = $deposit->user->curl;
      } else {
        $response_url = $deposit->user->eurl;
      }

      $hash_string = $caller->key . "|" . $deposit->txnid . "|" . $deposit->amount . "|" . $deposit->email . "|" . $deposit->status . "|" . $caller->salt;
      $hash = hash('sha512', $hash_string);

      // curl
      if ($c_url) {
        // hash calculation
        // $hash_sequence = "key|orderid|mobile|amount|email|pgtxnmessage|responsemessage|paymenttype|status";
        $status = "Success";
        $hash_sequence = $caller->key . "|" . $deposit->txnid . "|" . $deposit->amount . "|" . $deposit->email . "|" . $status . "|" . $caller->salt;
        $hash_c = hash('sha512', $hash_sequence);
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $c_url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => array(
            "KEY" => $caller->key,
            "TXNID" => $deposit->txnid,
            "AMOUNT" => $deposit->amount,
            "EMAIL" => $deposit->email,
            "MOBILE" => $deposit->phone,
            "STATUS" => $deposit->status,
            "HASH" => $hash_c,
          ),
        ));

        $response = curl_exec($curl);
        curl_close($curl); 

        print_r("here we check curl");
        print_r($response);
      }

      // surl or eurl
      // return redirect()->away($response_url)->with('KEY',$caller->key)
      //                           ->with('TXNID',$deposit->txnid)
      //                           ->with('AMOUNT',$deposit->amount)
      //                           ->with('EMAIL',$deposit->email)
      //                           ->with('MOBILE',$deposit->phone)
      //                           ->with('STATUS',$deposit->status)
      //                           ->with('HASH',$hash);
      // $curl = curl_init();
      // curl_setopt_array($curl, array(
      //   CURLOPT_URL => $response_url,
      //   CURLOPT_RETURNTRANSFER => true,
      //   CURLOPT_ENCODING => '',
      //   CURLOPT_MAXREDIRS => 10,
      //   CURLOPT_TIMEOUT => 0,
      //   CURLOPT_FOLLOWLOCATION => true,
      //   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      //   CURLOPT_CUSTOMREQUEST => 'POST',
      //   CURLOPT_POSTFIELDS => array(
      //     "KEY" => $caller->key,
      //     "TXNID" => $deposit->txnid,
      //     "AMOUNT" => $deposit->amount,
      //     "EMAIL" => $deposit->email,
      //     "MOBILE" => $deposit->phone,
      //     "STATUS" => $deposit->status,
      //     "HASH" => $hash,
      //   ),
      // ));

      // $response = curl_exec($curl);
      // curl_close($curl); 
      // print_r($response);
      // callback url
      

      return response()->json(['status' => 'success']);
    } else { // if client
      if ($request->STATUS == "Captured" || $request->STATUS == "Success") {
        // mint tokens
        $exec_phrase =
          'node contract-interact.js ' . $deposit->wallet . ' ' . $request->AMOUNT;

        chdir('../');
        exec($exec_phrase, $var, $result);
        if ($result) {
          return response()->json(['status' => 'fail', 'message' => "Deposit succeeded but mint failed"]);
        }

        $mint_status = '';
        $mint_comment = '';
        $crypto_txn_hash = '';
        $mint_error = "";
        foreach ($var as $item) {
          if (str_contains($item, "mintHash")) {
            $mint_status = "Success";
            $crypto_txn_hash = substr($item, -66);
          }

          if (str_contains($item, "error")) {
            $mint_error = $item;
            $mint_status = "Fail";
          }
        }

        $deposit->mint_status = $mint_status;
        $deposit->mint_comment = $mint_comment;
        $deposit->crypto_txn_hash = $crypto_txn_hash;
        $deposit->mint_error = $mint_error;
        $deposit->save();

        return response()->json(['status' => 'success', 'message' => "successfully minted"]);
      }
    }



    

    // if ($request->STATUS == "Captured" || $request->STATUS == "Success") {
    //   // mint tokens
    //   $exec_phrase =
    //     'node contract-interact.js ' . $deposit->wallet . ' ' . $request->AMOUNT;

    //   chdir('../');
    //   exec($exec_phrase, $var, $result);
    //   if ($result) {
    //     return response()->json(['status' => 'fail', 'message' => "Deposit succeeded but mint failed"]);
    //   }

    //   $mint_status = '';
    //   $mint_comment = '';
    //   $crypto_txn_hash = '';
    //   $mint_error = "";
    //   foreach ($var as $item) {
    //     if (str_contains($item, "mintHash")) {
    //       $mint_status = "Success";
    //       $crypto_txn_hash = substr($item, -66);
    //     }

    //     if (str_contains($item, "error")) {
    //       $mint_error = $item;
    //       $mint_status = "Fail";
    //     }
    //   }

    //   $deposit->mint_status = $mint_status;
    //   $deposit->mint_comment = $mint_comment;
    //   $deposit->crypto_txn_hash = $crypto_txn_hash;
    //   $deposit->mint_error = $mint_error;
    //   $deposit->save();

    //   return response()->json(['status' => 'success', 'message' => "successfully minted"]);
    // } else if ($request->STATUS != "Captured" && $request->STATUS != "Declined" && $request->STATUS != "Pending") {
    //   //this code runs every second 
    //   $curl = curl_init();

    //   curl_setopt_array($curl, array(
    //     CURLOPT_URL => 'https://coinsplashgifts.com/api/transaction/response.php',
    //     CURLOPT_RETURNTRANSFER => true,
    //     CURLOPT_ENCODING => '',
    //     CURLOPT_MAXREDIRS => 10,
    //     CURLOPT_TIMEOUT => 0,
    //     CURLOPT_FOLLOWLOCATION => true,
    //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //     CURLOPT_CUSTOMREQUEST => 'POST',
    //     CURLOPT_POSTFIELDS => array(
    //       "ORDER_ID" => $request->ORDER_ID,
    //     ),
    //     CURLOPT_HTTPHEADER => array(
    //       'Authorization: Bearer 5CFB73B65096F2C11F6BA309C0D13C3BA2E8D7D1D1B14FE3224BB0E94008EA15'
    //     ),
    //   ));

    //   $response = curl_exec($curl);
    //   curl_close($curl);
    //   $json_resp = json_decode($response);

    //   if ($json_resp->STATUS != "") {
    //     $deposit->STATUS = $json_resp->STATUS;
    //     return response()->json(['status' => 'success', 'message' => $json_resp->STATUS]);
    //   } else {
    //     return response()->json(['status' => 'success', 'message' => "No response from PSP"]);
    //   }
    // } else {
    //   return response()->json(['status' => 'fail', 'message' => "Deposit was not successful"]);
    // }
  }

  protected function verifyPayout($beneficiary_cd, $verify_url)
  {
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => $verify_url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => array(
        'BENEFICIARY_CD' => $beneficiary_cd,
        'ACTION' => 'VERIFY'
      ),
      CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer 5CFB73B65096F2C11F6BA309C0D13C3BA2E8D7D1D1B14FE3224BB0E94008EA15',
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

  public function history()
  {
    $deposits = Deposit::where('user_id', auth()->user()->id)->where('is_client', 1)->get();
    $payouts = Payout::where('user_id', auth()->user()->id)->where('is_external', 0)->get();
    return response()->json(['status' => 'success', 'deposits' => $deposits, 'payouts' => $payouts]);
  }










  // @params
  // mobile - mobile number
  // @response
  // status
  // data
  public function sendMobileOtp(Request $request)
  {
    $rules = [
      'mobile' => 'required|numeric',
    ];

    $validator = Validator::make($request->input(), $rules);

    if ($validator->fails()) {
      return response()->json(['status' => 'fail', 'error' => $validator->errors()]);
    }

    $random_code = random_int(100000, 999999);
    $user = User::find(auth()->user()->id);
    $user->mobile = $request->mobile;
    $user->otp_value = $random_code;
    $user->save();

    $text = 'Sending the mobile verification code: ' . $random_code;
    $otp_data['phone'] = $request->mobile;
    $otp_data['text'] = $text;
    $this->sendSMS($otp_data);

    return response()->json(['status' => 'success', 'data' => $request->mobile]);
  }

  protected function sendSMS($data)
  {
    $sid = env('TWILIO_SID');
    $token = env('TWILIO_TOKEN');

    $client = new Twiliio($sid, $token);
    return $client->messages->create($data['phone'], [
      'from' => env('TWILIO_NUMBER'),
      'body' => $data['text'],
    ]);
  }

  // @params
  // mobile - mobile number
  // otp - otp value
  public function submitMobileOtp(Request $request)
  {
    $rules = [
      'mobile' => 'required|numeric',
      'email' => 'required|email'
    ];

    $validator = Validator::make($request->input(), $rules);

    if ($validator->fails()) {
      return response()->json(['status' => 'fail', 'message' => $validator->errors()]);
    }

    $user = User::where('email', $request->email)->first();
    if (empty($user)) {
      return response()->json(['status' => 'fail', 'message' => 'You are not registered yet']);
    }

    // if ($user->mobile != $request->mobile) {
    //   return response()->json(['status' => 'fail', 'message' => 'mobile number is incorrect']);
    // }
    $user->mobile = $request->mobile;
    $user->mobile_status = "verified";
    $user->save();
    return response()->json(['status' => 'success']);
  }






































  public function responseKyc(Request $request)
  {
    if ($request['status'] == 'success') {
      $veriff = Veriffication::where('veriff_id', $request['verification']['id'])->first();
      if (!empty($veriff)) {
        $veriff->is_verified = 1;
        $veriff->save();
      } else {
        print_r("veriffication contains wrong data");
      }
    } else {
      print_r("veriffication contains wrong response");
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

  public function sendEmailOtp(Request $request)
  {
    $random_code = random_int(100000, 999999);
    $email = $request->email_address;
    $data = ['name' => 'Verification', 'code' => $random_code];
    if (!empty($request->user_id)) {
      $user = User::where('id', $request->user_id)->first();
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
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        "Authorization: Bearer $request->authToken"
      )
    ));

    $responseCollect = curl_exec($curlCollet);
    curl_close($curlCollet);

    $responsePayment = json_decode($responseCollect);

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

    if ($responsePayment->RESPONSE_CODE == 000 && $responsePayment->STATUS == 'Sent to Bank') {
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
    $exec_phrase = 'node contract-interact.js ' . $aDeposit->wallet . ' ' . $aDeposit->amount . ' ' . $num;

    chdir('../');
    exec($exec_phrase, $var, $result);
  }

  public function mintManual(Request $request)
  {
    $aDeposit = Deposit::where('id', $request->id)->first();

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
      CURLOPT_POSTFIELDS => '{
                                    "PAY_ID":  "' . env('PAY_ID') . '",
                                    "PAYER_ADDRESS":  "' . $payeAddress .  '",
                                    "HASH":"' .  $hash . '"
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

  public function kycIndex(Request $request)
  {
    if (empty($request->status))
      return view('direct_users.kyc')->with('user_id', $request->user_id);
    else
      return view('direct_users.kyc')->with('user_id', $request->user_id)
        ->with('status', $request->status)
        ->with('pan_front', $request->pan_front)
        ->with('pan_back', $request->pan_back);
  }

  public function profileEdit(Request $reqeust)
  {
    return view('direct_users.profile-edit')->with('user', Auth::user());
  }


  public function sell()
  {
    return view('direct_users.sell')->with('user', Auth::user());
  }
}
