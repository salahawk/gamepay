<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Deposit;

use Yajra\DataTables\DataTables;
use Auth;

class SuccessController extends Controller
{
    public function index() {
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
        print_r($filterdate);
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
    public function data() {
        return DataTables::of(Deposit::select())
            ->addColumn('provider', function ($deposit) {
                $provider = 'Cashlesso';                
                return $provider;
            })
            ->make(true);
    }
}