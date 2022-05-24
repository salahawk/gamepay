<?php

namespace App\Http\Controllers\AdminMerchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\External;
use App\Models\User;

use Yajra\DataTables\DataTables;
use Auth;

class UserController extends Controller
{
    public function index()
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

        $users = External::where('merchant_id', 1)
            ->where('created_at', '>', $from)
            ->where('created_at', '<', $to)
            ->where('pan_status', $status_sign, $status)
            ->where('email', $email_sign, $email)
            ->where('account_no', $order_id_sign, $order_id)
            ->orderby('created_at', 'desc')->select('*');
        return DataTables::of($users)
            ->make(true);
    }

    public function rolling() {
        return view('admin_merchant.users.rolling');
    }

    public function fee() {
        return view('admin_merchant.users.fee');
    }
}
