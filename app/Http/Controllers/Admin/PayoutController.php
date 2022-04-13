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