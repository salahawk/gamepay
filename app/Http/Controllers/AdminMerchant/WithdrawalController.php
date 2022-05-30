<?php

namespace App\Http\Controllers\AdminMerchant;

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
        return view('admin_merchant.withdrawals.index');
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

        $payouts = Payout::where('is_external', 1)
            ->where('caller_id', Session::get('merchant_id'))
            ->where('created_at', '>=', $from . " 00:00:00")
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

    public function guide()
    {
        return view('admin_merchant.withdrawals.guide');
    }
}
