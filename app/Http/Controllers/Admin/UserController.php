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

    $users = User::
      // where('created_at', '>=', $from . " 00:00:00")
      // ->where('created_at', '<=', $to . " 23:59:59")
      // ->where('pan_status', $status_sign, $status)
      // ->where('email', $email_sign, $email)
      // ->where('account_no', $order_id_sign, $order_id)
      orderby('created_at', 'desc')->get();
    $externals = External::orderby('created_at', 'desc')->get();
    $result = $users->merge($externals);

    return DataTables::of($result)
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
      ->addColumn('mobile', function($user) {
        if (!empty($user->mobile)) {
          return $user->mobile;
        } else {
          return $user->phone;
        }
      })

      ->rawColumns(['kyc', 'pan_st'])
      ->make(true);
  }

  public function kyc(Request $request)
  {
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
      orderby('created_at', 'desc')->get();
    $externals = External::orderby('created_at', 'desc')->get();
    $results = $users->merge($externals);
    return DataTables::of($results)
      ->addColumn('proof1', function ($result) {
        return '<a href="' . URL::to('/uploads/kyc') . "/" . $result->front_img . '" class="text-dark" target="_blank">prooflink</a>';
      })
      ->addColumn('proof2', function ($result) {
        return '<a href="' . URL::to('/uploads/kyc') . "/" . $result->back_img . '" class="text-dark" target="_blank">prooflink</a>';
      })
      ->addColumn('proof_status', function ($result) {
        $is_client = !empty($result->mobile) ? 1 : 0;
        if ($result->kyc_status == "verified") {
          return '<span class="alert-success rounded py-1 px-2">Verified</span>';
        } else if ($result->kyc_status == "cancelled") {
          return '<span class="alert-danger rounded py-1 px-2">Cancelled</span>';
        } else {
          return '<a href="' . route("admin.users.kyc.verify") . '?id=' . $result->id . '&is_client='. $is_client .'" class="btn btn-primary">Verify</a>
          <a href="' . route("admin.users.kyc.cancel") . "?id=" . $result->id . '&is_client='. $is_client .'" class="btn btn-outline-primary">Cancel</a>';
        }
      })
      ->addColumn('remarks_proof', function ($result) {
        if (empty($result->kyc_remarks)) {
          return '<form class="d-inline" method="POST" action="' . route("admin.users.remarks") . '">
                    <input class="form-control" type="text" name="kyc_remarks" placeholder="Remarks" style="width:90px; display: inline">
                    <input type="hidden" name="_token" value="'. csrf_token().'">
                    <input type="hidden" name="type" value="kyc">
                    <input type="hidden" name="id" value="'. $result->id .'">
                    <button class="btn btn-primary d-inline" type="submit">Save</button>
                  </form>';
        } else {
          return '<p>' . $result->kyc_remarks . '</p>';
        }
      })
      ->addColumn('bank_proof', function ($result) {
        return '<a href="' . URL::to('/uploads/pan') . "/" . $result->pan_front . '" class="text-dark" target="_blank">prooflink</a>';
      })
      ->addColumn('bank_status', function ($result) {
        $is_client = !empty($result->mobile) ? 1 : 0;
        if ($result->pan_status == "verified") {
          return '<span class="alert-success rounded py-1 px-2">Verified</span>';
        } else if ($result->pan_status == "cancelled") {
          return '<span class="alert-danger rounded py-1 px-2">Cancelled</span>';
        } else {
          return '<a href="' . route("admin.users.pan.verify") . "?id=" . $result->id . '&is_client='. $is_client .'" class="btn btn-primary">Verify</a>
          <a href="' . route("admin.users.pan.cancel") . "?id=" . $result->id . '&is_client='. $is_client .'" class="btn btn-outline-primary">Cancel</a>';
        }
      })
      ->addColumn('remarks_bank', function ($result) {
        $is_client = !empty($result->mobile) ? 1 : 0;
        if (empty($result->bank_remarks)) {
          return '<form class="d-inline" method="POST" action="' . route("admin.users.remarks") . '">
                  <input class="form-control" type="text" name="bank_remarks" placeholder="Remarks" style="width:90px; display: inline">
                  <input type="hidden" name="_token" value="'. csrf_token().'">
                  <input type="hidden" name="type" value="pan">
                  <input type="hidden" name="is_client" value="'. $is_client .'">
                  <input type="hidden" name="id" value="'. $result->id .'">
                  <button class="btn btn-primary d-inline" type="submit">Save</button>
                </form>';
        } else {
          return '<p>' . $result->bank_remarks . '</p>';
        }
      })
      ->rawColumns(['proof1', 'proof2', 'proof_status', 'remarks_proof', 'bank_proof', 'remarks_bank', 'bank_status'])
      ->make(true);
  }

  public function kycVerify(Request $request)
  {
    $user = $request->is_client == 1 ? User::find($request->id) : External::find($request->id);
    $user->kyc_status = "verified";
    $user->save();

    return redirect()->route('admin.users.kyc');
  }

  public function kycCancel(Request $request)
  {
    $user = $request->is_client == 1 ? User::find($request->id) : External::find($request->id);
    $user->kyc_status = "cancelled";
    $user->save();

    return redirect()->route('admin.users.kyc');
  }

  public function panVerify(Request $request)
  {
    $user = $request->is_client == 1 ? User::find($request->id) : External::find($request->id);
    $user->pan_status = "verified";
    $user->save();

    return redirect()->route('admin.users.kyc');
  }

  public function panCancel(Request $request)
  {
    $user = $request->is_client == 1 ? User::find($request->id) : External::find($request->id);
    $user->pan_status = "cancelled";
    $user->save();

    return redirect()->route('admin.users.kyc');
  }

  public function remarks(Request $request) {
    $user = $request->is_client == 1 ? User::find($request->id) : External::find($request->id);
    $request->type == "kyc" ? $user->kyc_remarks = $request->kyc_remarks : $user->bank_remarks = $request->bank_remarks;
    $user->save();

    return redirect()->route('admin.users.kyc');
  }
}
