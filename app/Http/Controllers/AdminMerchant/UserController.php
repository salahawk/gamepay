<?php

namespace App\Http\Controllers\AdminMerchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\External;
use App\Models\Merchant;

use Yajra\DataTables\DataTables;
use Auth;
use Hash;
use Session;

class UserController extends Controller
{
  public function users()
  {
    return view('admin_merchant.users.index');
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

    $users = External::where('merchant_id', Session::get('merchant_id'))
      ->where('created_at', '>=', $from . " 00:00:00")
      ->where('created_at', '<=', $to . " 23:59:59")
      // ->where('status', $status_sign, $status)
      ->where('email', $email_sign, $email)
      // ->where('account_no', $order_id_sign, $order_id)
      ->orderby('created_at', 'desc')->select('*');
    return DataTables::of($users)
      ->make(true);
  }

  public function profile()
  {
    $merchant = Merchant::find(Session::get('merchant_id'));
    return view('admin_merchant.users.profile')->with('merchant', $merchant);
  }

  public function changePassword(Request $request)
  {
    $merchant = Merchant::find(Session::get('merchant_id'));
    if (!Hash::check($request->old, $merchant->password)) {
      return response()->json(['status' => 'fail', 'message' => 'Your old password is incorrect']);
    }

    $merchant->password = Hash::make($request->new);
    $saved = $merchant->save();
    if ($saved) {
      return response()->json(['status' => 'success', 'message' => 'Your password is successfully updated!']);
    }
  }

  public function rolling()
  {
    return view('admin_merchant.users.rolling');
  }

  public function fee()
  {
    return view('admin_merchant.users.fee');
  }

  public function index()
  {
    return view('admin_merchant.users.login');
  }

  public function login(Request $request)
  {
    $merchant = Merchant::where('email', $request->email)->first();
    if (empty($merchant)) {
      return response()->json(['status' => 'fail', 'message' => 'Unknown email.']);
    }

    if (Hash::check($request->password, $merchant->password)) {
      Session::put('merchant_id', $merchant->id);
      return redirect()->route('admin-merchant.deposits');
    }

    return redirect()->route('admin-merchant.home');
  }

  public function logout()
  {
    Session::flush();

    return redirect()->route('admin-merchant.home');
  }
}
