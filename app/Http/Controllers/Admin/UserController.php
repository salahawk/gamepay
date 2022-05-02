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
                $updateData_url = route('mint-manual', ['id' => $deposit->id, 'wallet' => $deposit->wallet]);
                return '<a type="button" class = "btn btn-sm btn-danger" href = "' . $updateData_url . '">Manual Mint</a>';
            })
            ->addColumn('psp_name', function ($deposit) {
                return $deposit->psp->name;
            })
            ->make(true);
    }


    public function activationIndex() {
        $merchantKey = config('app.merchantKey');
        $merchantId = config('app.merchantId');
        $companyId = config('app.companyId');
        $merchantUser = config('app.merchantUser');
        $paymentUser = config('app.paymentUser');
        $merchantHead = config('app.merchantHead');
        $userHead = config('app.userHead');
        $con = config('app.con');
        $condition='';

        if(isset($_POST['fromdate']) && $_POST['fromdate']!='' && isset($_POST['todate']) && $_POST['todate']!=''){
            $fromdate=$_POST['fromdate'];
            $todate=$_POST['todate'];
            $condition.="AND DATE(created_date) between '$fromdate' and '$todate'";
        }
        else if(isset($_POST['fromdate']) && $_POST['fromdate']!=''){
            $fromdate=$_POST['fromdate'];
            $condition.="AND DATE(created_date)='$fromdate'";
        }
        else if(isset($_POST['todate']) && $_POST['todate']!=''){
            $todate=$_POST['todate'];
            $condition.="AND DATE(created_date)='$todate'";
        }

        if(isset($_POST['emailid']) && $_POST['emailid']!=''){
            $emailid=trim($_POST['emailid']);
            $condition.="AND email like '%$emailid%'";
        }

        if(isset($_POST['transid']) && $_POST['transid']!=''){
            $transid=trim($_POST['transid']);
            $condition.="AND txnid='$transid'";
        }

        $date=date('Y-m-d');
        $mindate=date('Y-m-d', strtotime('-3 days'));

        if($condition!=''){
            $condition=$condition;
        }else{
            //$condition="AND DATE(created_date) between '$mindate' and '$date'";
            $condition="AND DATE(created_date)='$date'";
        }

        $filterdate="SELECT * FROM `deposits` where `key` like '%$merchantKey%' $condition and status like '%success%' order by created_date DESC";
        $result = mysqli_query($con, $filterdate);

        $todate=date('Y-m-d');
        if(isset($_SESSION['usertype']) && $_SESSION['usertype']!='admin'){
            $flowchart="SELECT sum(amount) as amt FROM `deposits` WHERE DATE(created_date)= '$date' and `key` like '%$merchantKey%' and status like '%success%'";
        }else{
            $flowchart="SELECT sum(amount) as amt,DATE(created_date) as date FROM `deposits` where DATE(created_date)= '$date' and `key` like '%$merchantKey%' and status like '%success%' group by DATE(created_date)";
        }
        $result1 = mysqli_query($con, $flowchart);
        $rowData = mysqli_fetch_array($result1,MYSQLI_ASSOC);
        $totDeposit=($rowData['amt']);


        return view('admin.deposits.activation', compact('totDeposit', 'result'));
    }
 
    public function activationUpdateData($id) {
        $user = User::where('id', '=', $id)->first();
        $user->is_verified == 1 ? $user->is_verified = 0 : $user->is_verified = 1;
        $user->save();
        return redirect()->route('admin.activation');
    }
    
    public function activationData() {
        return DataTables::of(User::select())
            ->addIndexColumn()
            ->addColumn('action', function ($user) {
                $updateData_url = route('admin.activation.updateData', ['id' => $user->id]);
                if ( $user->is_verified == 0)
                    $activate = '<a class = "btn btn-sm btn-danger" href = "' . $updateData_url . '">NotActivated</a>';
                else 
                    $activate = '<a class = "btn btn-sm btn-success" href = "' . $updateData_url . '">Activated</a>';
                return $activate;
                return "kkk";
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function pendingIndex() {
        $merchantKey = config('app.merchantKey');
        $merchantId = config('app.merchantId');
        $companyId = config('app.companyId');
        $merchantUser = config('app.merchantUser');
        $paymentUser = config('app.paymentUser');
        $merchantHead = config('app.merchantHead');
        $userHead = config('app.userHead');
        
        // Fileuploadpath
        $filepath='showlion';
        $con = config('app.con');
        $condition='';

        if(isset($_REQUEST['fromdate']) && $_REQUEST['fromdate']!='' && isset($_REQUEST['todate']) && $_REQUEST['todate']!=''){
            $fromdate=$_REQUEST['fromdate'];
            $todate=$_REQUEST['todate'];
            $condition.="AND DATE(created_date) between '$fromdate' and '$todate'";
        }
        else if(isset($_REQUEST['fromdate']) && $_REQUEST['fromdate']!=''){
            $fromdate=$_REQUEST['fromdate'];
            $condition.="AND DATE(created_date)='$fromdate'";
        }
        else if(isset($_REQUEST['todate']) && $_REQUEST['todate']!=''){
            $todate=$_REQUEST['todate'];
            $condition.="AND DATE(created_date)='$todate'";
        }

        if(isset($_REQUEST['emailid']) && $_REQUEST['emailid']!=''){
            $emailid=$_REQUEST['emailid'];
            $condition.="AND email like '%$emailid%'";
        }

        if(isset($_REQUEST['transid']) && $_REQUEST['transid']!=''){
            $transid=$_REQUEST['transid'];
            $condition.="AND txnid='$transid'";
        }

        $minamount = '';
        if (!empty($_REQUEST['minamount'])) {
            $minamount = intval($_REQUEST['minamount']);
            $condition .= " AND amount >= $minamount ";
        }

        $service_provider = '';
        if (!empty($_REQUEST['provider'])) {
            $service_provider = addslashes($_REQUEST['provider']);
            $condition .= " AND service_provider LIKE '%$service_provider%' ";
        }

        $date=date('Y-m-d');
        $mindate=date('Y-m-d', strtotime('-3 days'));

        if($condition!=''){
            $condition=$condition;
        }else{
            $condition="AND DATE(created_date) between '$mindate' and '$date'";
        }

        $filterdate="SELECT * FROM `deposits` where `key`='$merchantKey' and status NOT like '%success%' $condition";
        $result = mysqli_query($con, $filterdate);
        
        $todate=date('Y-m-d');
        if(isset($_SESSION['usertype']) && $_SESSION['usertype']!='admin'){
            $flowchart="SELECT sum(amount) as amt FROM `deposits` WHERE DATE(created_date)= '$date' and `key` like '%$merchantKey%' and status like '%success%'";
        }else{
            $flowchart="SELECT sum(amount) as amt,DATE(created_date) as date FROM `deposits` where DATE(created_date)= '$date' and `key` like '%$merchantKey%' and status like '%success%' group by DATE(created_date)";
        }
        $result1 = mysqli_query($con, $flowchart);
        $rowData = mysqli_fetch_array($result1,MYSQLI_ASSOC);
        $totDeposit=($rowData['amt']);

        return view('admin.deposits.pending', compact('result', 'totDeposit', 'minamount', 'service_provider'));
    }

    public function pendingData() {
        return DataTables::of(Deposit::select())
            ->addColumn('provider', function ($deposit) {
                $provider = 'Cashlesso';                
                return $provider;
            })
            ->make(true);
    }

    public function successIndex() {
        $merchantKey = config('app.merchantKey');
        $merchantId = config('app.merchantId');
        $companyId = config('app.companyId');
        $merchantUser = config('app.merchantUser');
        $paymentUser = config('app.paymentUser');
        $merchantHead = config('app.merchantHead');
        $userHead = config('app.userHead');
        $con = config('app.con');
        $condition='';

        if(isset($_POST['fromdate']) && $_POST['fromdate']!='' && isset($_POST['todate']) && $_POST['todate']!=''){
            $fromdate=$_POST['fromdate'];
            $todate=$_POST['todate'];
            $condition.="AND DATE(created_date) between '$fromdate' and '$todate'";
        }
        else if(isset($_POST['fromdate']) && $_POST['fromdate']!=''){
            $fromdate=$_POST['fromdate'];
            $condition.="AND DATE(created_date)='$fromdate'";
        }
        else if(isset($_POST['todate']) && $_POST['todate']!=''){
            $todate=$_POST['todate'];
            $condition.="AND DATE(created_date)='$todate'";
        }

        if(isset($_POST['emailid']) && $_POST['emailid']!=''){
            $emailid=trim($_POST['emailid']);
            $condition.="AND email like '%$emailid%'";
        }

        if(isset($_POST['transid']) && $_POST['transid']!=''){
            $transid=trim($_POST['transid']);
            $condition.="AND txnid='$transid'";
        }

        $date=date('Y-m-d');
        $mindate=date('Y-m-d', strtotime('-3 days'));

        if($condition!=''){
            $condition=$condition;
        }else{
            //$condition="AND DATE(created_date) between '$mindate' and '$date'";
            $condition="AND DATE(created_date)='$date'";
        }

        $filterdate="SELECT * FROM `deposits` where `key` like '%$merchantKey%' $condition and status like '%success%' order by created_date DESC";
        $result = mysqli_query($con, $filterdate);

        $todate=date('Y-m-d');
        if(isset($_SESSION['usertype']) && $_SESSION['usertype']!='admin'){
            $flowchart="SELECT sum(amount) as amt FROM `deposits` WHERE DATE(created_date)= '$date' and `key` like '%$merchantKey%' and status like '%success%'";
        }else{
            $flowchart="SELECT sum(amount) as amt,DATE(created_date) as date FROM `deposits` where DATE(created_date)= '$date' and `key` like '%$merchantKey%' and status like '%success%' group by DATE(created_date)";
        }
        $result1 = mysqli_query($con, $flowchart);
        $rowData = mysqli_fetch_array($result1,MYSQLI_ASSOC);
        $totDeposit=($rowData['amt']);


        return view('admin.deposits.success', compact('totDeposit', 'result'));
    }
    public function successData() {
        return DataTables::of(Deposit::select())
            ->addColumn('provider', function ($deposit) {
                $provider = 'Cashlesso';                
                return $provider;
            })
            ->make(true);
    }
}