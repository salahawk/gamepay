<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use Illuminate\Support\Facades\Validator;

use App\Models\User;
use App\Models\Merchant;

class GamepayController extends Controller
{
  public function index() {
    return view('gamepay.index');
  }

  public function addMerchant(Request $request) {
    $rules = [
        'name' => 'required|alpha',
        'mobile' => 'required|unique:merchants',
        'email' => 'required|email|unique:merchants'
    ];

    $validator = Validator::make($request->input(), $rules);

    if ($validator->fails()) {
      return response()->json(['status' => 'fail', 'error' => $validator->errors()]);
    }

    $merchant = new Merchant;
    $merchant->name = $request->name;
    $merchant->mobile = $request->mobile;
    $merchant->email = $request->email;

    // key and salt generation
    $key = random_int(10000000, 99999999);
    $sample = Merchant::where('key', $key)->first();
    if (!empty($sample)) {
      $key = random_int(10000000, 99999999);
    }

    $salt = $request->name . random_int(100000,999999);
    $sample = Merchant::where('salt', $salt)->first();
    if (!empty($sample)) {
      $salt = $request->name . random_int(100000,999999);
    }

    $merchant->key = $key;
    $merchant->salt = $salt;

    $merchant->save();

    return redirect()->route('home');
  }

  public function terms() {
    return view('gamepay.terms');
  }
}