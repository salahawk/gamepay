<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Deposit;
use App\Models\Payout;
use App\Models\User;

use Yajra\DataTables\DataTables;
use Auth;

class PayoutController extends Controller
{
    public function index() {
        return view('admin.payouts.index');
    }

    public function data() {
        $payouts = Payout::orderby('created_at', 'desc')->get();

        return DataTables::of($payouts)
            ->addColumn('action', function ($payout) {
                $updateData_url = route('admin.payouts.process', ['id' => $payout->id]);
                if ($payout->status != "Processing")
                    return '<a type="button" class = "btn btn-sm btn-danger" href = "' . $updateData_url . '">Process</a>';
                else
                    return '<a class = "btn btn-sm btn-success" type="button" disabled>Already processed</a>';
            })
            ->addColumn('email', function($payout) {
                return $payout->user->email;
            })
            ->make(true);
    }

    public function missedPayout() {
        return view("admin.payouts.missed");
    }

    public function dataMissed() {
        $payouts = Payout::where('status', 'Captured')
                            ->where('minted_status', "<>", "success")
                            ->orderby('created_at', 'desc')
                            ->select('*');

        return DataTables::of($payouts)
            ->addColumn('action', function ($payout) {
                $updateData_url = route('admin.deposits.mint.manual', ['id' => $payout->id, 'wallet' => $payout->wallet]);
                return '<a type="button" class = "btn btn-sm btn-danger" href = "' . $updateData_url . '">Manual Mint</a>';
            })
            ->addColumn('psp_name', function ($payout) {
                return $payout->psp->name;
            })
            ->make(true);
    }

    public function process(Request $request) {
        
        // if present in DB, make transaction
        $payout = Payout::where('id', $request->id)->first();
        $order_id = $payout->order_id;
        $amount = $payout->txn_amount;
        $beneficiary_cd = $payout->beneficiary_cd;
        $comment = "test";

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://uat.cashlesso.com/payout/v2/initateTransaction',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'{
                                "PAY_ID": "1016601009105737",
                                "ORDER_ID": "' . $order_id . '",
                                "TXN_AMOUNT": "' . $amount . '",
                                "BENEFICIARY_CD": "' . $beneficiary_cd . '",
                                "BENE_COMMENT": "' . $comment . '",
                                "TXN_PAYMENT_TYPE": "NEFT"
                                } ',
          CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer 853E8CA793795D2067CA199ECE28222CBF5ACA699BE450ED3F76D49A01137A42',
            'Content-Type: application/json'
          ),
        ));
  
        $response = curl_exec($curl);
  
        curl_close($curl);
        $json_resp = json_decode($response);
  
        $payout->hash = $json_resp->HASH;
        $payout->status = $json_resp->STATUS;
        $payout->action = $json_resp->ACTION;
        $payout->response_message = $json_resp->RESPONSE_MESSAGE;
        $payout->txn_payment_type = $json_resp->TXN_PAYMENT_TYPE;
        $payout->total_amount = $json_resp->TOTAL_AMOUNT;
        $saved = $payout->save();
  
        if ($saved) {
            return redirect()->route('admin.payouts');
        } else {
            return response()->json(['status' => 'false', 'data' => $json_resp]);
        }
    }
}