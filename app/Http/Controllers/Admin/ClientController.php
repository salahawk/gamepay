<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Deposit;
use App\Models\User;
use App\Models\Client;

use Yajra\DataTables\DataTables;
use Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ClientController extends Controller
{
    public function index() {
        return view('admin.clients.index');
    }

    public function data() {
        $users = Client::orderby('created_at', 'desc')->select('*');

        return DataTables::of($users)
            ->make(true);
    }
    
    public function store(Request $request) {
      $rules = [
          'name' => 'required|alpha_num',
          'ip' => 'required|unique:merchants',
      ];

      $validator = Validator::make($request->input(), $rules);

      if ($validator->fails()) {
        return response()->json(['status' => 'fail', 'error' => $validator->errors()]);
      }

      $client = new Client;
      $client->name = $request->name;
      $client->ip = $request->ip;

      $key = Str::random(32);
      $sample = Client::where('key', $key)->first();
      if (!empty($sample)) {
        $key = Str::random(32);
      }

      $salt = Str::random(48);
      $sample = Client::where('salt', $salt)->first();
      if (!empty($sample)) {
        $salt = Str::random(48);
      }
      
      $client->key = $key;
      $client->salt = $salt;
      $client->save();

      return redirect()->route('admin.clients');
    }


    public function approve(Request $request) {
        $user = User::find($request->id);
        if ($request->type == "kyc") {
            $user->kyc_status = $request->approve == 1 ? "verified" : "rejected";
        } else if ($request->type == "pan") {
            $user->pan_status = $request->approve == 1 ? "verified" : "rejected";
        }
        $user->save();

        return redirect()->route("admin.users");
    }












    public function missedDeposit() {
        return view("admin.deposits.missed");
    }

    public function dataMissed() {
        $deposits = Deposit::where('status', 'Captured')
                            ->where('minted_status', "<>", "success")
                            ->orderby('created_at', 'desc')
                            ->select('*');

        return DataTables::of($deposits)
            ->addColumn('action', function ($deposit) {
                $updateData_url = route('admin.deposits.mint.manual', ['id' => $deposit->id, 'wallet' => $deposit->wallet]);
                return '<a type="button" class = "btn btn-sm btn-danger" href = "' . $updateData_url . '">Manual Mint</a>';
            })
            ->addColumn('psp_name', function ($deposit) {
                return $deposit->psp->name;
            })
            ->make(true);
    }
}