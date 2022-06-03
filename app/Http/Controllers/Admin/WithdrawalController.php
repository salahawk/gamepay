<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Payout;
use App\Models\User;

use Yajra\DataTables\DataTables;
use Auth;

class WithdrawalController extends Controller
{
  public function index()
  {
    return view('admin.withdrawals.index');
  }

  public function data(Request $request)
  {
    $from = $request->from == "" ? "1990-12-31" : $request->from;
    $to = $request->to == "" ? "2999-12-31" : $request->to;
    $status = $request->status == "" ?  "%" : $request->status;
    $status_sign = $request->status == "" ?  "like" : "=";
    $email = $request->email == "" ?  "%" : $request->email;
    $email_sign = $request->email == "" ?  "like" : "=";
    $order_id = $request->order_id == "" ?  "%" : $request->order_id;
    $order_id_sign = $request->order_id == "" ?  "like" : "=";

    $payouts = Payout::where('created_at', '>=', $from . " 00:00:00")
      ->where('created_at', '<=', $to . " 23:59:59")
      // ->where('status', $status_sign, $status)
      // ->where('email', $email_sign, $email)
      // ->where('order_id', $order_id_sign, $order_id)
      ->orderby('created_at', 'desc')->select('*');

    return DataTables::of($payouts)
      ->addColumn('created_at', function ($payout) {
        return $payout->created_at->format('Y-m-d H:i:s');
      })
      ->addColumn('status', function ($payout) {
        if (!empty($payout->status)) {
          return $payout->status;
        } else {
          return '<a type="button" href="' . route('admin.withdrawals.release') . '?id=' . $payout->id . '" class="btn btn-primary">Release</a>
                  <a type="button" class="btn btn-outline-primary">Reject</a>';
        }
      })
      ->rawColumns(['status'])
      ->make(true);
  }

  public function missed()
  {
    return view('admin.withdrawals.missed');
  }

  public function missedData(Request $request)
  {
    $from = $request->from == "" ? "1990-12-31" : $request->from;
    $to = $request->to == "" ? "2999-12-31" : $request->to;
    $status = $request->status == "" ?  "%" : $request->status;
    $status_sign = $request->status == "" ?  "like" : "=";
    $email = $request->email == "" ?  "%" : $request->email;
    $email_sign = $request->email == "" ?  "like" : "=";
    $order_id = $request->order_id == "" ?  "%" : $request->order_id;
    $order_id_sign = $request->order_id == "" ?  "like" : "=";

    $payouts = Payout::where('created_at', '>=', $from . " 00:00:00")
      ->where('created_at', '<=', $to . " 23:59:59")
      ->where('status', $status_sign, $status)
      ->where('email', $email_sign, $email)
      ->where('order_id', $order_id_sign, $order_id)
      ->orderby('created_at', 'desc')->select('*');
    return DataTables::of($payouts)
      ->addColumn('created_at', function ($deposit) {
        return $deposit->created_at->format('Y-m-d H:i:s');
      })
      ->make(true);
  }

  public function release(Request $request)
  {
    $payout = Payout::find($request->id);
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
        "BENEFICIARY_CD" => $payout->user->beneficiary_cd,
        "TXN_AMOUNT" => $payout->txn_amount,
        "BENE_COMMENT" => $comment,
        "TXN_PAYMENT_TYPE" => $payout->txn_payment_type,
        "ORDER_ID" => $payout->order_id,
      ),
      CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer 5CFB73B65096F2C11F6BA309C0D13C3BA2E8D7D1D1B14FE3224BB0E94008EA15',
      ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    $json_resp = json_decode($response);

    $payout->hash = $json_resp->HASH;
    $payout->status = $json_resp->STATUS;
    $payout->pay_id = $json_resp->PAY_ID;
    $payout->action = $json_resp->ACTION;
    $payout->txn_amount = $json_resp->TXN_AMOUNT;
    $payout->response_message = $json_resp->RESPONSE_MESSAGE;
    $payout->txn_payment_type = $json_resp->TXN_PAYMENT_TYPE;
    if (!empty($json_resp->TOTAL_AMOUNT)) {
      $payout->total_amount = $json_resp->TOTAL_AMOUNT;
    }
    $saved = $payout->save();

    return redirect()->route('admin.withdrawals');
  }

  // public function addPayout(Request $request)
  // {
  //   $user = External::where('id', $request->user_id)->first();

  //   // choose caller and PSP
  //   $used_deposit = Deposit::where('email', $user->email)->where('wallet', $user->wallet)->first();

  //   // if present in DB, make transaction
  //   $order_id = strlen($user->first_name) < 3 ? $user->first_name . random_int(10000000, 99999999) : substr($user->first_name, 0, 4) . random_int(10000000, 99999999);
  //   $amount = $user->amount;
  //   $comment = "payout test";

  //   $curl = curl_init();
  //   curl_setopt_array($curl, array(
  //     CURLOPT_URL => 'https://coinsplashgifts.com/payout/release.php',
  //     CURLOPT_RETURNTRANSFER => true,
  //     CURLOPT_ENCODING => '',
  //     CURLOPT_MAXREDIRS => 10,
  //     CURLOPT_TIMEOUT => 0,
  //     CURLOPT_FOLLOWLOCATION => true,
  //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  //     CURLOPT_CUSTOMREQUEST => 'POST',
  //     CURLOPT_POSTFIELDS => array(
  //       "BENEFICIARY_CD" => $user->beneficiary_cd,
  //       "TXN_AMOUNT" => $amount,
  //       "BENE_COMMENT" => $comment,
  //       "TXN_PAYMENT_TYPE" => $request->payment_type,
  //       "ORDER_ID" => $order_id,
  //     ),
  //     CURLOPT_HTTPHEADER => array(
  //       'Authorization: Bearer 5CFB73B65096F2C11F6BA309C0D13C3BA2E8D7D1D1B14FE3224BB0E94008EA15',
  //     ),
  //   ));
  //   $response = curl_exec($curl);
  //   curl_close($curl);

  //   $json_resp = json_decode($response);

  //   $payout = new Payout;
  //   $payout->user_id = $user->id;
  //   $payout->email = $user->email;
  //   $payout->hash = $json_resp->HASH;
  //   $payout->status = $json_resp->STATUS;
  //   $payout->beneficiary_cd = $json_resp->BENEFICIARY_CD;
  //   $payout->pay_id = $json_resp->PAY_ID;
  //   $payout->order_id = $json_resp->ORDER_ID;
  //   $payout->action = $json_resp->ACTION;
  //   $payout->txn_amount = $json_resp->TXN_AMOUNT;
  //   $payout->response_message = $json_resp->RESPONSE_MESSAGE;
  //   $payout->txn_payment_type = $json_resp->TXN_PAYMENT_TYPE;
  //   if (!empty($json_resp->TOTAL_AMOUNT)) {
  //     $payout->total_amount = $json_resp->TOTAL_AMOUNT;
  //   }
  //   $payout->txn_hash = $user->txn_hash;
  //   $payout->remarks = $user->remarks;
  //   $payout->sender = $user->address;
  //   $payout->receiver = $user->receiver;
  //   $payout->network = $user->network;
  //   $payout->currency = $user->crypto;
  //   $payout->inr_value = $user->inr_value;
  //   $payout->is_external = 1;
  //   $payout->caller_id = 1; //$used_deposit->caller_id;
  //   $payout->psp_id = 1; //$used_deposit->psp_id;
  //   $saved = $payout->save();

  //   if ($saved) {
  //     return response()->json(['status' => 'success']);
  //   } else {
  //     return response()->json(['status' => 'false', 'data' => $json_resp]);
  //   }
  // }
}
