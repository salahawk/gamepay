<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use Yajra\DataTables\DataTables;
use Auth;
use Session;
use Hash;
use URL;

use App\Models\Merchant;
use App\Models\User;
use App\Models\External;


class UserController extends Controller
{
  public function index()
  {
    return view('admin.users.login');
  }

  public function login(Request $request)
  {
    $merchant = Merchant::where('email', $request->email)->first();
    if (empty($merchant)) {
      return response()->json(['status' => 'fail', 'message' => 'Unknown email.']);
    }

    if (Hash::check($request->password, $merchant->password)) {
      Session::put('merchant_id', $merchant->id);
      return redirect()->route('admin.deposits');
    }

    return redirect()->route('home');
  }

  public function users()
  {
    return view('admin.users.index');
  }

  public function data(Request $request) {
    $from = $request->from == "" ? "1990-12-31" : $request->from;
    $to = $request->to == "" ? "2999-12-31" : $request->to;
    $status = $request->status == "" ?  "%" : $request->status;
    $status_sign = $request->status == "" ?  "like" : "=";
    $email = $request->email == "" ?  "%" : $request->email;
    $email_sign = $request->email == "" ?  "like" : "=";
    $order_id = $request->order_id == "" ?  "%" : $request->order_id;
    $order_id_sign = $request->order_id == "" ?  "like" : "=";

    $users = User::
      // where('created_at', '>=', $from . " 00:00:00")
      // ->where('created_at', '<=', $to . " 23:59:59")
      // ->where('pan_status', $status_sign, $status)
      // ->where('email', $email_sign, $email)
      // ->where('account_no', $order_id_sign, $order_id)
      orderby('created_at', 'desc')->select('*');
    return DataTables::of($users)
      ->addColumn('kyc', function ($user) {
        if ($user->kyc_status == "verified") {
          return '<span class="alert-success rounded py-1 px-2">Verified</span>';
        } else {
          return '<span class="alert-warning py-1 px-2 rounded">Pending</span>';
        }
      })
      ->addColumn('pan_st', function ($user) {
        if ($user->pan_status == "verified") {
          return '<span class="alert-success rounded py-1 px-2">Verified</span>';
        } else {
          return '<span class="alert-warning py-1 px-2 rounded">Pending</span>';
        }
      })
      ->rawColumns(['kyc', 'pan_st'])
      ->make(true);
  }

  public function kyc(Request $request) {
    return view('admin.users.kyc');
  }

  public function kycData(Request $request)
  {
    $from = $request->from == "" ? "1990-12-31" : $request->from;
    $to = $request->to == "" ? "2999-12-31" : $request->to;
    $status = $request->status == "" ?  "%" : $request->status;
    $status_sign = $request->status == "" ?  "like" : "=";
    $email = $request->email == "" ?  "%" : $request->email;
    $email_sign = $request->email == "" ?  "like" : "=";
    $order_id = $request->order_id == "" ?  "%" : $request->order_id;
    $order_id_sign = $request->order_id == "" ?  "like" : "=";

    $users = User::
      // where('created_at', '>=', $from . " 00:00:00")
      // ->where('created_at', '<=', $to . " 23:59:59")
      // ->where('pan_status', $status_sign, $status)
      // ->where('email', $email_sign, $email)
      // ->where('account_no', $order_id_sign, $order_id)
      orderby('created_at', 'desc')->select('*');
    return DataTables::of($users)
      ->addColumn('proof1', function ($user) {
        return '<a href="' . URL::to('/uploads/kyc') . "/" . $user->front_img . '" class="text-dark" target="_blank">prooflink</a>';
      })
      ->addColumn('proof2', function ($user) {
        return '<a href="' . URL::to('/uploads/kyc') . "/" . $user->back_img . '" class="text-dark" target="_blank">prooflink</a>';
      })
      ->addColumn('status', function ($user) {
        if ($user->kyc_status == "verified") {
          return '<span class="alert-success rounded py-1 px-2">Verified</span>';
        } else {
          return '<a href="#" class="btn btn-primary">Verify</a><a href="#" class="btn btn-outline-primary">Cancel</a>';
        }
      })
      ->addColumn('remarks_proof', function ($user) {
        if (empty($user->kyc_remarks)) {
          return '<form class="d-inline">
                    <input class="form-control" type="email" placeholder="Remarks" style="width:90px; display: inline">
                    <a href="#" class="btn btn-primary d-inline">Save</a>
                  </form>';
        } else {
          return '<p>'. $user->kyc_status.'</p>';
        }
      })
      ->addColumn('bank_proof', function ($user) {
        return '<a href="' . URL::to('/uploads/pan') . "/" . $user->pan . '" class="text-dark" target="_blank">prooflink</a>';
      })
      ->addColumn('remarks_bank', function ($user) {
        if (empty($user->pan_remarks)) {
          return '<form class="d-inline">
                    <input class="form-control" type="email" placeholder="Remarks" style="width:90px; display: inline">
                    <a href="#" class="btn btn-primary d-inline">Save</a>
                  </form>';
        } else {
          return '<p>'. $user->pan_status.'</p>';
        }
      })
      ->rawColumns(['proof1', 'proof2', 'status', 'remarks_proof', 'bank_proof', 'remarks_bank'])
      ->make(true);
  }
}
