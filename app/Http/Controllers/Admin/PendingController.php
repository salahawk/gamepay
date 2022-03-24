<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Deposit;

use Yajra\DataTables\DataTables;
use Auth;

class PendingController extends Controller
{
    public function index() {
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

        // $sql = "select * from deposit where 1 $condition";
        // $result_tbl=$db->get_rsltset($sql);
        // $transId=array();
        // foreach($result_tbl as $res){
        //     array_push($transId,$res['transaction_id']);
        // }

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

    public function data() {
        return DataTables::of(Deposit::select())
            ->addColumn('provider', function ($deposit) {
                $provider = 'Cashlesso';                
                return $provider;
            })
            ->make(true);
    }
}