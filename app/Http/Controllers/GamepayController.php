<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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
    $key = Str::random(32);
    $sample = Merchant::where('key', $key)->first();
    if (!empty($sample)) {
      $key = Str::random(32);
    }

    $salt = Str::random(48);
    $sample = Merchant::where('salt', $salt)->first();
    if (!empty($sample)) {
      $salt = Str::random(48);
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