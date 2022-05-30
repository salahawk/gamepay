<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use App\Models\Deposit;
use App\Models\Merchant;
use App\Models\User;

use Yajra\DataTables\DataTables;
use Auth;

class DepositController extends Controller
{
  public function index()
  {
    return view('admin.deposits.index');
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

    $deposits = Deposit::where('created_at', '>=', $from . " 00:00:00")
      ->where('created_at', '<=', $to . " 23:59:59")
      ->where('status', $status_sign, $status)
      ->where('email', $email_sign, $email)
      ->where('order_id', $order_id_sign, $order_id)
      ->orderby('created_at', 'desc')->select('*');
    return DataTables::of($deposits)
      ->addColumn('created_at', function ($deposit) {
        return $deposit->created_at->format('Y-m-d H:i:s');
      })
      ->addColumn('status', function ($deposit) {
        if ($deposit->status == "Success") {
          return '<span class="text-success">' . $deposit->status . '</span>';
        } else if ($deposit->status == "Pending") {
          return '<span class="text-warning">' . $deposit->status . '</span>';
        } else {
          return '<span class="text-danger">' . $deposit->status . '</span>';
        }
      })
      ->rawColumns(['status'])
      ->make(true);
  }

  public function missed()
  {
    return view('admin.deposits.missed');
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

    $deposits = Deposit::where('created_at', '>=', $from . " 00:00:00")
      ->where('created_at', '<=', $to . " 23:59:59")
      ->where('status', "Success")
      ->where('email', $email_sign, $email)
      ->where('order_id', $order_id_sign, $order_id)
      ->where('is_missed', 1)
      ->orderby('created_at', 'desc')->select('*');
    return DataTables::of($deposits)
      ->addColumn('created_at', function ($deposit) {
        return $deposit->created_at->format('Y-m-d H:i:s');
      })
      ->addColumn('status', function ($deposit) {
        return '<a type="button" href="' . route('admin.deposits.release') . '?id=' . $deposit->id . '" class="btn btn-primary">Release</a>';
      })
      ->rawColumns(['status'])
      ->make(true);
  }

  public function release(Request $request)
  {
    $deposit = Deposit::find($request->id);
    $deposit->is_missed = 0;
    $deposit->save();

    if ($deposit->is_client == 1) {
      $this->mint_token($deposit->id, $deposit->amount);
      return redirect()->route('admin.deposits');
    } else if ($deposit->is_client == 0) { // if merchant
        $caller = Merchant::where('id', $deposit->caller_id)->first();
        // send response back to merchant
        $response_url = $deposit->user->surl;
        $c_url = $deposit->user->curl;

        $hash_string = $caller->key . "|" . $deposit->txnid . "|" . $deposit->amount . "|" . $deposit->email . "|" . $deposit->status . "|" . $caller->salt;
        $hash = hash('sha512', $hash_string);

        // curl
        if ($c_url) {
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

          if ($response == "SUCCESS") {
            // mint tokens
            $this->mint_token($deposit->id, $request->AMOUNT);
          }
        }

        // surl or eurl
        return redirect()->away($response_url)->with('KEY', $caller->key)
          ->with('TXNID', $deposit->txnid)
          ->with('AMOUNT', $deposit->amount)
          ->with('EMAIL', $deposit->email)
          ->with('MOBILE', $deposit->phone)
          ->with('STATUS', $deposit->status)
          ->with('HASH', $hash);
    }
  }

  public function reject(Request $request)
  {
    $deposit = Deposit::find($request->id);
    $deposit->missed = 0;
  }
}
