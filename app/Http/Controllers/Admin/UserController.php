<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Deposit;
use App\Models\User;

use Yajra\DataTables\DataTables;
use Auth;

class UserController extends Controller
{
    public function index() {
        return view('admin.users.index');
    }

    public function data() {
        $users = User::orderby('created_at', 'desc')->select('*');

        return DataTables::of($users)
            ->addColumn('kyc_manual', function ($user) {
                $kyc_approve_url = route('admin.users.approve', ['id' => $user->id, 'type'=>'kyc', 'approve' => 1]);
                $kyc_reject_url = route('admin.users.approve', ['id' => $user->id, 'type'=>'kyc', 'approve' => 0]);
                if ($user->kyc_type == "veriff") 
                    return "Tried by veriff.com";
                if ($user->kyc_status == "verified" || $user->kyc_status == "rejected")
                    return $user->kyc_status;
                else
                    return '<a type="button" class = "btn btn-sm btn-success" href = "' . $kyc_approve_url . '">KYC Approve</a>' . 
                    '<a type="button" class = "btn btn-sm btn-danger" href = "' . $kyc_reject_url . '">KYC Reject</a>';
            })
            ->addColumn('pan_approve', function ($user) {
                $pan_approve_url = route('admin.users.approve', ['id'=> $user->id, 'approve'=>1, 'type'=>'pan']);
                $pan_reject_url = route('admin.users.approve', ['id'=> $user->id, 'approve'=>0, 'type'=>'pan']);
                if ($user->pan_status == "verified" || $user->pan_status == "rejected")
                    return $user->pan_status;
                else
                    return '<a type="button" class = "btn btn-sm btn-success" href = "' . $pan_approve_url . '">Pan Approve</a>' . 
                    '<a type="button" class = "btn btn-sm btn-danger" href = "' . $pan_reject_url . '">Pan Reject</a>';
            })
            ->rawColumns(['kyc_manual', 'pan_approve'])
            ->make(true);
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
}