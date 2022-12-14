<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

use Twilio\Rest\Client;
use Mail;
use Auth;
use Carbon\Carbon;

use App\Models\User;
use App\Models\Deposit;
use App\Models\External;
use App\Models\Payout;
use App\Models\Merchant;
use App\Models\Verification;
use App\Jobs\ProcessStatus;

class MerchantController extends Controller
{
  public function index(Request $request)
  {
    // validate key and salt
    $psp_key = env("PSP_KEY");
    $rules = [
      'KEY' => 'required',
      'TXNID' => 'required',
      'AMOUNT' => 'required|numeric|min: 500|max: 50000',
      'FIRST_NAME' => 'bail|required|alpha|between:2,40',
      'LAST_NAME' => 'bail|required|alpha|between:2,40',
      'EMAIL' => 'required|email',
      'PHONE' => 'required|numeric|digits_between:10,12',
      'CRYPTO' => 'required|alpha|in:USDT,BTC,GAMERE',
      'NETWORK' => 'required|alpha|in:BSC,AVAX,ETH,POLYGON',
      'ADDRESS' => 'required|alpha_num|between:30,42',
      'REMARKS' => 'required|between:0,18',
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
      return response()->json(['status' => 'fail', 'error' => $validator->errors()]);
    }

    if (!$this->isAddress($request->ADDRESS)) {
      return response()->json(['status' => 'fail', 'error' => 'Invalid wallet address']);
    }

    // validate hash
    $key = $request->KEY;
    $txn_id = $request->TXNID;
    $amount = $request->AMOUNT;
    $first_name = $request->FIRST_NAME;
    $last_name = $request->LAST_NAME;
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

    $merchant = Merchant::where('key', $key)->first();
    if (empty($merchant)) {
      return response()->json(['status' => 'fail', 'message' => 'merchant key is not registered']);
    }
    $salt = $merchant->salt;

    // select PSP according to the email and loyalty points
    // $psp_id = choose_gameway($email, $merchat->loyalty_points);

    $hash_string =
      $key .
      '|' .
      $txn_id .
      '|' .
      $amount .
      '|' .
      $first_name .
      '|' .
      $last_name .
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

    $hash_value = hash('sha512', $hash_string);

    if ($hash != $hash_value) {
      var_dump("hash value error");
      return redirect()->away($eurl . "?data=hash missmatch");
    }

    // check user if existing by wallet and email
    $user = External::where('email', $email)
      ->where('email_status', 'verified')
      ->where('kyc_status', 'verified')
      ->where('mobile_status', 'verified')
      ->where('merchant_id', $merchant->id)
      ->first();

    if (!empty($user)) {
      $user->key = $key;
      $user->txnid = $txn_id;
      $user->amount = $amount;
      $user->first_name = $first_name;
      $user->last_name = $last_name;
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

      // if kyc verified, save image from merchant
      if ($kyc_status == "verified" && !empty($merchant->kyc_url)) {
        $base_url = $merchant->kyc_url . "?email=";
        if (empty($user->front_img) || empty($user->back_img)) {
          $curl = curl_init();
          curl_setopt_array($curl, array(
            CURLOPT_URL => $base_url . $user->email,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
          ));

          $response = curl_exec($curl);

          curl_close($curl);
          $json_resp = json_decode($response);
          foreach ($json_resp as $resp) {
            $imageUrl = urldecode(stripslashes('https://www.jungleraja.com/' . $resp->DownloadLink));
            if ($resp->DocType == "Doc9") {
              $rawImage = file_get_contents($imageUrl);
              if ($rawImage) {
                $filename = "mp" . date("Y-m-d-H-i-s") . $resp->FileName;
                file_put_contents("uploads/pan/" . $filename, $rawImage);
                $user->pan_front = $filename;
                $user->save();
              }
            } else if ($resp->DocType == "Doc10") {
              $rawImage = file_get_contents($imageUrl);
              if ($rawImage) {
                $filename = "mkf" . date("Y-m-d-H-i-s") . $resp->FileName;
                file_put_contents("uploads/kyc/" . $filename, $rawImage);
                $user->front_img = $filename;
                $user->save();
              }
            } else if ($resp->DocType == "Doc11") {
              $rawImage = file_get_contents($imageUrl);
              if ($rawImage) {
                $filename = "mkb" . date("Y-m-d-H-i-s") . $resp->FileName;
                file_put_contents("uploads/kyc/" . $filename, $rawImage);
                $user->back_img = $filename;
                $user->save();
              }
            }
          }
        }
      }

      $deposit = new Deposit;
      $deposit->user_id = $user->id;
      $deposit->txnid = $txn_id;
      $deposit->amount = $amount;
      $deposit->crypto = $crypto;
      $deposit->network = $network;
      $deposit->remarks = $remarks;
      $deposit->is_client = 0;
      $deposit->first_name = $first_name;
      $deposit->last_name = $last_name;
      $deposit->wallet = $address;
      $deposit->order_id = strlen($first_name) < 3 ? $first_name . random_int(10000000, 99999999) : substr($first_name, 0, 4) . random_int(10000000, 99999999);
      $deposit->caller_id = $merchant->id;
      $deposit->email = $email;
      $deposit->psp_id = 1;  // have to modify later based on routing logic
      $deposit->save();

      if ($kyc_status == "verified" && empty($merchant->kyc_url) && empty($user->front_img) && empty($user->back_img)) {
        $user->kyc_status = "pending";
        $user->save();
        return redirect()->route('securepay.kyc', ['user_id' => $user->id, 'deposit_id' => $deposit->id]);
      }

      // redirect to PSP
      $awayUrl = $this->sendToPsp($deposit->order_id, $amount, $email, $phone, $first_name);
      $verify_url = "https://coinsplashgifts.com/api/transaction/response.php";
      ProcessStatus::dispatch($deposit->id, $verify_url)->delay(Carbon::now()->addMinutes(10));
      return redirect()->away($awayUrl);
    }

    $garbage = External::where('email', $email)
      ->delete();

    // insert DB
    $sample = new External();
    $sample->key = $key;
    $sample->txnid = $txn_id;
    $sample->amount = $amount;
    $sample->first_name = $first_name;
    $sample->last_name = $last_name;
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
    $sample->beneficiary_cd = $first_name . random_int(10000000000, 99999999999);
    $sample->merchant_id = $merchant->id;
    $saved = $sample->save();

    // if kyc verified, save image from merchant
    if ($kyc_status == "verified" && !empty($merchant->kyc_url)) {
      $base_url = $merchant->kyc_url . "?email=";
      if (empty($user->front_img) || empty($user->back_img)) {
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $base_url . $user->email,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $json_resp = json_decode($response);
        foreach ($json_resp as $resp) {
          $imageUrl = urldecode(stripslashes('https://www.jungleraja.com/' . $resp->DownloadLink));
          if ($resp->DocType == "Doc9") {
            $rawImage = file_get_contents($imageUrl);
            if ($rawImage) {
              $filename = "mp" . date("Y-m-d-H-i-s") . $resp->FileName;
              file_put_contents("uploads/pan/" . $filename, $rawImage);
              $user->pan_front = $filename;
              $user->save();
            }
          } else if ($resp->DocType == "Doc10") {
            $rawImage = file_get_contents($imageUrl);
            if ($rawImage) {
              $filename = "mkf" . date("Y-m-d-H-i-s") . $resp->FileName;
              file_put_contents("uploads/kyc/" . $filename, $rawImage);
              $user->front_img = $filename;
              $user->save();
            }
          } else if ($resp->DocType == "Doc11") {
            $rawImage = file_get_contents($imageUrl);
            if ($rawImage) {
              $filename = "mkb" . date("Y-m-d-H-i-s") . $resp->FileName;
              file_put_contents("uploads/kyc/" . $filename, $rawImage);
              $user->back_img = $filename;
              $user->save();
            }
          }
        }
      }
    }

    $user_id = $sample->id;

    $deposit = new Deposit;
    $deposit->user_id = $user_id;
    $deposit->txnid = $txn_id;
    $deposit->amount = $amount;
    $deposit->crypto = $crypto;
    $deposit->network = $network;
    $deposit->remarks = $remarks;
    $deposit->is_client = 0;
    $deposit->first_name = $first_name;
    $deposit->last_name = $last_name;
    $deposit->wallet = $address;
    $deposit->order_id = strlen($first_name) < 3 ? $first_name . random_int(10000000, 99999999) : substr($first_name, 0, 4) . random_int(10000000, 99999999);
    $deposit->caller_id = $merchant->id;
    $deposit->email = $email;
    $deposit->psp_id = 1;  // have to modify later
    $deposit->save();

    if ($kyc_status == "verified" && empty($merchant->kyc_url) && empty($user->front_img) && empty($user->back_img)) {
      $user->kyc_status = "pending";
      $user->save();
      return redirect()->route('securepay.kyc', ['user_id' => $user->id, 'deposit_id' => $deposit->id]);
    }

    // redirect to the verification
    if ($email_status == 'verified' && $mobile_status == "verified" && $kyc_status != 'verified') {
      return redirect()->route('securepay.kyc', ['user_id' => $user_id, 'deposit_id' => $deposit->id]);
    } else if ($email_status != "verified" || $mobile_status != "verified" || $kyc_status != "verified") {
      $awayUrl = $this->sendToPsp($deposit->order_id, $amount, $email, $phone, $first_name);
      return view('external_users.status_verify', compact('user_id', 'amount', 'crypto', 'network', 'address', 'remarks', 'email_status', 'mobile_status', 'kyc_status', 'phone', 'email', 'awayUrl'));
    } else {
      $awayUrl = $this->sendToPsp($deposit->order_id, $amount, $email, $phone, $first_name);
      $verify_url = "https://coinsplashgifts.com/api/transaction/response.php";
      ProcessStatus::dispatch($deposit->id, $verify_url)->delay(Carbon::now()->addMinutes(10));
      return redirect()->away($awayUrl);
    }
  }

  protected function sendToPsp($order_id, $amount, $email, $phone, $first_name)
  {
    $psp_key = env('PSP_KEY');
    $valuecheck = env("PSP_KEY") . "|*" . $order_id . "|*" . $amount . "|*" . urldecode($email) . "|*" . $phone . "|*" . urldecode($first_name) . "|*" . env('PSP_SALT');
    $eurl = hash('sha512', $valuecheck);
    $encData = urlencode(base64_encode("key=$psp_key&firstname=$first_name&mobile=$phone&amount=$amount&email=$email&txnid=$order_id&eurl=$eurl"));
    $url = 'https://coinsplashgifts.com/pgway/acquirernew/upipay.php';
    return $url . "?encdata=" . $encData;
    // return $valuecheck;
  }

  public function sendMobileOtp(Request $request)
  {
    $random_code = random_int(100000, 999999);
    $sample = External::where('id', $request->user_id)->first();
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

  public function kycManual(Request $request)
  {
    $front_path = "uploads/kyc";
    $back_path = "uploads/kyc";
    $allowedfileExtension = ['png', 'jpg', 'jpeg'];

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

      if ($request->deposit_id == 0) {
        return redirect()->route('securepay.pan', [
          'user_id' => $user->id,
          'status' => 'Manual KYC images are under approval',
          'pan_front' => $front_name,
          'pan_back' => $back_name
        ]);
      }

      $psp_key = env("PSP_KEY");
      $deposit = Deposit::where('id', $request->deposit_id)->first();
      $valuecheck = env("PSP_KEY") . "|*" . $deposit->order_id . "|*" . $deposit->amount . "|*" . urldecode($deposit->email) . "|*" . $user->phone . "|*" . urldecode($deposit->first_name) . "|*" . env('PSP_SALT');
      $eurl = hash('sha512', $valuecheck);
      $encData = urlencode(base64_encode("key=$psp_key&firstname=$deposit->first_name&mobile=$user->phone&amount=$deposit->amount&email=$deposit->email&txnid=$deposit->order_id&eurl=$eurl"));
      $url = 'https://coinsplashgifts.com/pgway/acquirernew/upipay.php';
      $awayUrl = $url . "?encdata=" . $encData;
      $verify_url = "https://coinsplashgifts.com/api/transaction/response.php";
      ProcessStatus::dispatch($deposit->id, $verify_url)->delay(Carbon::now()->addMinutes(10));
      return redirect()->away($url . "?encdata=" . $encData);
    } else {
      return response()->json(['status' => 'fail']);
    }
  }

  public function payout(Request $request)
  {
    $rules = [
      'CUSTOMER_NAME' => 'bail|required|alpha|between:2,40',
      'EMAIL' => 'required|email',
      'PHONE' => 'required|numeric|digits_between:9,12',
      'KYC_STATUS' => 'required|alpha',
      'EMAIL_STATUS' => 'required|alpha',
      'MOBILE_STATUS' => 'required|alpha',
      'PAN_STATUS' => 'required|alpha',
      'AMOUNT' => 'required|numeric|min:500|max:50000',
      'CURRENCY' => 'required|alpha|in:USDT,BTC,GAMERE',
      'NETWORK' => 'required|alpha|in:BSC,AVAX,ETH,POLYGON',
      'INR_VALUE' => 'required|numeric',
      'RECEIVER' => 'required|alpha_num|between:30,42',
      'SENDER' => 'required|alpha_num|between:30,42',
      'TXN_HASH' => 'required|between:0,60',
      'REMARKS' => 'required|between:0,18',
      'HASH' => 'required'
    ];

    $validator = Validator::make($request->input(), $rules);

    if ($validator->fails()) {
      return response()->json(['status' => 'fail', 'error' => $validator->errors()]);
    }

    if (!$request->PAYER_ADDRESS && !$request->IFSC && !$request->ACCOUNT_NO) {
      return response()->json(['status' => 'fail', 'error' => 'Request should have payer_address,  or ifsc and account number']);
    } else if (!$request->PAYER_ADDRESS && (!$request->IFSC || !$request->ACCOUNT_NO)) {
      return response()->json(['status' => 'fail', 'error' => 'Request should have payer_address,  or ifsc and account number']);
    }

    if (!$this->isAddress($request->RECEIVER)) {
      return response()->json(['status' => 'fail', 'error' => 'Invalid receiver wallet address']);
    }

    if (!$this->isAddress($request->SENDER)) {
      return response()->json(['status' => 'fail', 'error' => 'Invalid sender wallet address']);
    }

    if (!empty($request->IFSC) && !$this->ifscCheck($request->IFSC)) {
      return response()->json(['status' => 'fail', 'error' => 'Invalid IFSC format']);
    }

    if (!empty($request->PAYER_ADDRESS) && !$this->upiCheck($request->PAYER_ADDRESS)) {
      return response()->json(['status' => 'fail', 'error' => 'Invalid PAYER ADDRESS format']);
    }

    // hash check
    $key = $request->KEY;
    $txn_id = $request->TXNID;
    $amount = $request->AMOUNT;
    $first_name = $request->CUSTOMER_NAME;
    $email = $request->EMAIL;
    $phone = $request->PHONE;
    $crypto = $request->CURRENCY;
    $inr_value = $request->INR_VALUE;
    $network = $request->NETWORK;
    $remarks = $request->REMARKS;
    $receiver = $request->RECEIVER;
    $sender = $request->SENDER;
    $txn_hash = $request->TXN_HASH;
    $payer_address = $request->PAYER_ADDRESS;
    $account_no = $request->ACCOUNT_NO;
    $ifsc = $request->IFSC;
    $kyc_status = $request->KYC_STATUS;
    $email_status = $request->EMAIL_STATUS;
    $mobile_status = $request->MOBILE_STATUS;
    $pan_status = $request->PAN_STATUS;
    $hash = $request->HASH;

    $merchant = Merchant::where('key', $key)->first();
    if (empty($merchant)) {
      return response()->json(['status' => 'fail', 'message' => 'merchant key is not registered']);
    }
    $salt = $merchant->salt;

    $hash_string =
      $key .
      '|' .
      $txn_id .
      '|' .
      $amount .
      '|' .
      $first_name .
      '|' .
      $email .
      '|' .
      $phone .
      '|' .
      $crypto .
      '|' .
      $network .
      '|' .
      $inr_value .
      '|' .
      $receiver .
      '|' .
      $sender .
      '|' .
      $remarks .
      '|' .
      $kyc_status .
      '|' .
      $email_status .
      '|' .
      $mobile_status .
      '|' .
      $pan_status .
      '|' .
      $ifsc .
      '|' .
      $account_no .
      '|' .
      $payer_address .
      '|' .
      $txn_hash .
      '|' .
      $salt;
    // print_r($hash_string);
    $hash_value = hash('sha256', $hash_string);
    // print_r($hash_value); 
    if ($hash != $hash_value) {
      return response()->json(['status' => 'fail', 'error' => 'Hash error', 'hash_string' => $hash_string, 'hash_value' => $hash_value, "str" => $request->STR]);
    }


    $user = External::where('email', $request->EMAIL)->first();
    if (empty($user)) {
      return response()->json(['status' => 'fail', 'error' => 'Email not found']);
    }

    if ($kyc_status != "verified" || $user->kyc_status != "verified") {
      return redirect()->route('securepay.kyc', ['user_id' => $user->id, 'deposit_id' => 0]);
    }

    $payment_type = 'IMPS';
    $user->pan_status = $request->PAN_STATUS;

    $user->crypto = $request->CURRENCY;
    $user->network = $request->NETWORK;
    $user->inr_value = $request->INR_VALUE;
    $user->remarks = $request->REMARKS;
    $user->amount = $request->AMOUNT;
    $user->address = $request->SENDER;
    $user->receiver = $request->RECEIVER;
    $user->txn_hash = $request->TXN_HASH;
    $user->first_name = $first_name;
    $user->phone = $phone;

    if ($request->PAN_STATUS != 'verified') {
      if (!empty($request->IFSC)) {
        $user->ifsc = $request->IFSC;
      }

      if (!empty($request->ACCOUNT_NO)) {
        $user->account_no = $request->ACCOUNT_NO;
      }

      if (!empty($request->PAYER_ADDRESS)) {
        $user->payer_address = $request->PAYER_ADDRESS;
      }
      $user->save();
      return redirect()->route('securepay.pan', ['user_id' => $user->id]);
    } else {
      // check original bank detail and update the beneficiary code
      $redo = false;
      $redo_ifsc = false;
      $redo_account_no = false;
      $redo_payer_address = false;
      $added_bank_detail = "";
      $add_fields = array(
        "BENEFICIARY_CD" => $user->beneficiary_cd,
        "BENE_NAME" => $user->first_name,
        "MOBILE_NUMBER" => $user->phone,
        "EMAIL_ID" => $user->email,
        "BANK_NAME" => "YESB",
        "ACTION" => "ADD",
      );
      // "PAYER_ADDRESS" => $user->payer_address,

      if (!empty($request->IFSC)) {
        $redo_ifsc = true;
        if (!empty($user->ifsc) && $user->ifsc != $request->IFSC) {
          $redo = true;
        }
        $user->ifsc = $request->IFSC;
      }

      if (!empty($request->ACCOUNT_NO)) {
        $redo_account_no = true;
        if (!empty($user->account_no) && $user->account_no != $request->ACCOUNT_NO) {
          $redo = true;
        }
        $user->account_no = $request->ACCOUNT_NO;
      }

      if (!empty($request->PAYER_ADDRESS)) {
        $payment_type = 'UPI';
        $redo_payer_address = true;
        if (!empty($user->payer_address) && $user->payer_address != $request->PAYER_ADDRESS) {
          $redo = true;
        }
        $user->payer_address = $request->PAYER_ADDRESS;
      }

      $user->save();

      // add preparation
      $add_url = "https://coinsplashgifts.com/payout/addben.php"; // have to select url based on routing logic
      if ($redo_payer_address) {
        $added_bank_detail == "payer";
        $add_fields['PAYER_ADDRESS'] = $user->payer_address;
      }

      if ($redo_account_no) {
        $add_fields['BENE_ACCOUNT_NO'] = $user->account_no;
      }

      if ($redo_ifsc) {
        $added_bank_detail = "ifsc";
        $add_fields['IFSC_CODE'] = $user->ifsc;
      }

      if ($this->verifyPayout($user->beneficiary_cd)) { // first check if it exists

        if ($redo) {    // bank detail changed?
          // terminate & add new
          if (!($this->terminatePayout('https://coinsplashgifts.com/payout/operations.php', $user->beneficiary_cd))) {
            return response()->json(['status' => 'fail', "message" => 'can not terminate the existing bene code in redo']);
          }

          // second add
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
            CURLOPT_POSTFIELDS => $add_fields,
            CURLOPT_HTTPHEADER => array(
              'Authorization: Bearer 5CFB73B65096F2C11F6BA309C0D13C3BA2E8D7D1D1B14FE3224BB0E94008EA15',
            ),
          ));
          $response0 = curl_exec($curl);
          curl_close($curl);
          $json_resp0 = json_decode($response0);
          if (empty($json_resp0->STATUS)) {
            return response()->json(['status' => 'fail', 'data' => $json_resp0, "message" => 'second add empty']);
          }
          if ($json_resp0->STATUS != "Success") {
            return response()->json(['status' => 'fail', 'data' => $json_resp0, "message" => 'second add empty']);
          }
        }
      } else {
        // add new beneficiary code
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
          CURLOPT_POSTFIELDS => $add_fields,
          CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer 5CFB73B65096F2C11F6BA309C0D13C3BA2E8D7D1D1B14FE3224BB0E94008EA15',
          ),
        ));

        $response0 = curl_exec($curl);
        curl_close($curl);

        $json_resp0 = json_decode($response0);

        if (substr($json_resp0->PG_TXN_MESSAGE, 0, 30) == "Beneficiary already exists for" && $json_resp0->STATUS == "Failed") {
          // find matching bene code and terminate and add again.
          if ($added_bank_detail = "account") {
            $check = External::where('ifsc', $user->ifsc)->where('account_no', $user->account_no)->where('email', "<>", $user->email)->first();
            if (empty($check)) {
              return response()->json(['status' => 'fail', "message" => 'no bene code for current IFSC and ACCOUNT NO in DB']);
            }
            $check->ifsc = '';
            $check->account_no = '';
            $check->save();
          } else {
            $check = External::where('payer_address', $user->payer_address)->where('email', "<>", $user->email)->first();
            if (empty($check)) {
              return response()->json(['status' => 'fail', "message" => 'no bene code for current IFSC and ACCOUNT NO in DB']);
            }
            $check->payer_address = '';
            $check->save();
          }

          // terminate
          if (!($this->terminatePayout('https://coinsplashgifts.com/payout/operations.php', $check->beneficiary_cd))) {
            return response()->json(['status' => 'fail', "message" => 'can not terminate the existing bene code']);
          }
          // and add new
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
            CURLOPT_POSTFIELDS => $add_fields,
            CURLOPT_HTTPHEADER => array(
              'Authorization: Bearer 5CFB73B65096F2C11F6BA309C0D13C3BA2E8D7D1D1B14FE3224BB0E94008EA15',
            ),
          ));
          $response0 = curl_exec($curl);
          curl_close($curl);
          $json_resp0 = json_decode($response0);
          if (empty($json_resp0->STATUS)) {
            return response()->json(['status' => 'fail', 'data' => $json_resp0, "message" => 'third add empty']);
          }
          if ($json_resp0->STATUS != "Success") {
            return response()->json(['status' => 'fail', 'data' => $json_resp0, "message" => 'third add empty']);
          }
        } else {
          if (empty($json_resp0->STATUS)) {
            return response()->json(['status' => 'fail', 'data' => $json_resp0, "message" => 'adding new empty']);
          }

          if ($json_resp0->STATUS != "Success") {
            return response()->json(['status' => 'fail', 'data' => $json_resp0, "message" => 'adding new fail']);
          }
        }
      }

      // create new payout entry, ready to go for payout
      $payout = new Payout;
      $payout->user_id = $user->id;
      $payout->txn_id = $txn_id;
      $payout->email = $user->email;
      $payout->txn_hash = $user->txn_hash;
      $payout->remarks = $user->remarks;
      $payout->sender = $user->address;
      $payout->receiver = $user->receiver;
      $payout->network = $user->network;
      $payout->currency = $user->crypto;
      $payout->inr_value = $user->inr_value;
      $payout->txn_amount = $user->amount;
      $payout->is_external = 1;
      $payout->caller_id = 1; //$used_deposit->caller_id;
      $payout->psp_id = 1; //$used_deposit->psp_id;
      $payout->txn_payment_type = $payment_type;
      $payout->beneficiary_cd = $user->beneficiary_cd;
      $payout->order_id = strlen($user->first_name) < 3 ? $user->first_name . random_int(10000000, 99999999) : substr($user->first_name, 0, 4) . random_int(10000000, 99999999);
      $saved = $payout->save();
      // return redirect()->route('securepay.payout.add', ['user_id' => $user->id, 'payment_type' => $payment_type]); // send with payment type flag
      return response()->json(['status' => 'success', 'message' => 'Please wait until the admin does the payout']);
    }
  }

  protected function verifyPayout($beneficiary_cd)
  {
    $url = "https://coinsplashgifts.com/payout/operations.php"; // have to select URL based on routing

    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://coinsplashgifts.com/payout/operations.php',
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
        'Authorization: Bearer 5CFB73B65096F2C11F6BA309C0D13C3BA2E8D7D1D1B14FE3224BB0E94008EA15'
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

  protected function terminatePayout($url, $bene_code)
  {
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
      CURLOPT_POSTFIELDS => array('ACTION' => 'TERMINATE', 'BENEFICIARY_CD' => $bene_code),
      CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer 5CFB73B65096F2C11F6BA309C0D13C3BA2E8D7D1D1B14FE3224BB0E94008EA15'
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

  public function pan(Request $request)
  {
    if (empty($request->status))
      return view('external_users.pan')->with('user_id', $request->user_id);
    else
      return view('external_users.pan')->with('user_id', $request->user_id)
        ->with('status', $request->status)
        ->with('pan_front', $request->pan_front)
        ->with('pan_back', $request->pan_back);
  }

  public function panManual(Request $request)
  {
    $front_path = "uploads/pan";
    $back_path = "uploads/pan";
    $allowedfileExtension = ['png', 'jpg', 'jpeg'];

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

      return redirect()->route('securepay.pan', [
        'user_id' => $user->id,
        'status' => 'Manual KYC images are under approval',
        'pan_front' => $front_name,
        'pan_back' => $back_name
      ]);
    } else {
      return response()->json(['status' => 'fail']);
    }
  }


  protected function selectPsp($data)
  {
    $psp = Psp::where('merchant_id', $data['merchant_id'])->first();
    $user = External::where('user_id', $data['user_id'])->first();

    if ($psp->status == "") {
      return "psp status is not available.";
    }

    $txn_id = $user->txn_id;
    $amount = $user->amount;
    $email = $user->email;
    $phone = $user->phone;
    $first_name = $user->first_name;

    $valuecheck = $txn_id . "|*" . $amount . "|*" . urldecode($email) . "|*" . $phone . "|*" . urldecode($first_name) . "|*" . $SALT;
    $eurl = hash('sha512', $valuecheck);
    $encData = urlencode(base64_encode("firstname=$first_name&mobile=$phone&amount=$amount&email=$email&txnid=$txn_id&eurl=$eurl"));
    $url = $psp->name;
    $awayUrl = $url . "?encdata=" . $encData;

    return $awayUrl;
  }

  public function kycIndex(Request $request)
  {
    if (empty($request->status))
      return view('external_users.kyc')->with('user_id', $request->user_id)->with('deposit_id', $request->deposit_id);
    else
      return view('external_users.pan')->with('user_id', $request->user_id)
        ->with('status', $request->status)
        ->with('pan_front', $request->pan_front)
        ->with('pan_back', $request->pan_back);
  }

  public function addPayout(Request $request)
  {
    $user = External::where('id', $request->user_id)->first();

    // choose caller and PSP
    $used_deposit = Deposit::where('email', $user->email)->where('wallet', $user->wallet)->first();

    // if present in DB, make transaction
    $order_id = strlen($user->first_name) < 3 ? $user->first_name . random_int(10000000, 99999999) : substr($user->first_name, 0, 4) . random_int(10000000, 99999999);
    $amount = $user->amount;
    $comment = "payout test";

    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://coinsplashgifts.com/payout/release.php',
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
        "TXN_PAYMENT_TYPE" => $request->payment_type,
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
    if (!empty($json_resp->TOTAL_AMOUNT)) {
      $payout->total_amount = $json_resp->TOTAL_AMOUNT;
    }
    $payout->txn_hash = $user->txn_hash;
    $payout->remarks = $user->remarks;
    $payout->sender = $user->address;
    $payout->receiver = $user->receiver;
    $payout->network = $user->network;
    $payout->currency = $user->crypto;
    $payout->inr_value = $user->inr_value;
    $payout->is_external = 1;
    $payout->caller_id = 1; //$used_deposit->caller_id;
    $payout->psp_id = 1; //$used_deposit->psp_id;
    $saved = $payout->save();

    if ($saved) {
      return response()->json(['status' => 'success']);
    } else {
      return response()->json(['status' => 'false', 'data' => $json_resp]);
    }
  }















  /////////////////////////// LESS JUNK ///////////////////////////

  public function sendEmailOtp(Request $request)
  {
    $random_code = random_int(100000, 999999);
    $email = $request->email_address;
    $data = ['name' => 'Verification', 'code' => $random_code];
    $aUser = External::where('id', $request->user_id)->first();

    if (empty($aUser)) {
      return response()->json(['status' => 'fail']);
    }

    // Mail::send('merchants.email-otp', $data, function ($message) use (
    //     $email
    // ) {
    //     $message
    //         ->to($email, 'GAMERE')
    //         ->subject('GAMERE email confirming request');
    //     $message->from('JAX@gamepay.com', 'GAMERE');
    // });

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

  public function kycProcess(Request $request)
  {
    $user = External::where('id', $request->user_id)->first();
    $user->first_name = $request->first_name;
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

  public function kycResponse(Request $request)
  {

    exit();
  }
  ////////////////////////////// JUNK ////////////////////////////
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
    $customerName = $user->first_name;
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
    $aDeposit->first_name = $customerName;
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
    $aDeposit = Deposit::where('order_id', $request->ORDER_ID)->first();
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
}
