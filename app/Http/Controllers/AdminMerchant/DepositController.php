<?php

namespace App\Http\Controllers\AdminMerchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Deposit;
use App\Models\User;

use Yajra\DataTables\DataTables;
use Auth;

class DepositController extends Controller
{
    public function index() {
        return view('admin_merchant.deposits.index');
    }

    public function data() {
        $deposits = Deposit::where('is_client', 0)->where('caller_id', 1)->orderby('created_at', 'desc')->select('*');

        return DataTables::of($deposits)
            ->make(true);
    }
}