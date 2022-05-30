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
            ->where('status', $status_sign, $status)
            ->where('email', $email_sign, $email)
            ->where('order_id', $order_id_sign, $order_id)
            ->orderby('created_at', 'desc')->select('*');
            
        return DataTables::of($payouts)
            ->addColumn('created_at', function ($payout) {
                return $payout->created_at->format('Y-m-d H:i:s');
            })
            ->addColumn('status', function ($deposit) {
                return '<a type="button" href="' . route('admin.withdrawals.release') . '" class="btn btn-primary">Release</a>
                <a type="button" class="btn btn-outline-primary">Reject</a>';
            })
            ->rawColumns(['status'])
            ->make(true);
    }

    public function missed() {
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
}
