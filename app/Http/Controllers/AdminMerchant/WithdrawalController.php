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
    public function index() {
        return view('admin_merchant.withdrawals.index');
    }

    public function data() {
        $deposits = Payout::where('is_external', 1)->where('caller_id', 1)->orderby('created_at', 'desc')->select('*');

        return DataTables::of($deposits)
            ->make(true);
    }
}