<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Deposit;

use Yajra\DataTables\DataTables;
use Auth;

class DepositController extends Controller
{
    public function index() {
        return view('admin.deposits.index');
    }

    public function data() {
        $deposits = Deposit::orderby('created_at', 'desc')->get();

        return DataTables::of($deposits)
            ->addColumn('provider', function ($deposit) {
                $provider = 'Cashlesso';                
                return $provider;
            })
            ->make(true);
    }

}