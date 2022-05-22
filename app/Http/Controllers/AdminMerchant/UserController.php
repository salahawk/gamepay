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
    public function index() {
        return view('admin_merchant.users.index');
    }

    public function data() {
        $deposits = External::where('merchant_id', 1)->orderby('created_at', 'desc')->select('*');

        return DataTables::of($deposits)
            ->make(true);
    }
}