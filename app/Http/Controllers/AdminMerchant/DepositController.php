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
  public function index()
  {
    return view('admin_merchant.deposits.index');
  }

  public function data(Request $request)
  {print_r($request->from); exit();
    $from = $request->from == "" ? "1990-12-31" : $request->from;
    $to = $request->to == "" ? "2999-12-31" : $request->to;

    // $deposits = Deposit::where('created_at', '>', $from)
    //   ->where('created_at', '<', $to)
    //   ->where('status', $request->status)
    //   ->where('email', $request->email)
    //   ->where('order_id', $request->order_id)
    //   ->orderby('created_at', 'desc')->select('*');
    
    $deposits = Deposit::where('created_at', '>', $from)
      ->where('created_at', '<', $to)
      ->orderby('created_at', 'desc')->select('*');
    return DataTables::of($deposits)
      ->make(true);
  }

  public function search(Request $request)
  {

  }
}
