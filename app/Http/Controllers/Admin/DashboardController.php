<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Deposit;

class DashboardController extends Controller
{
    public function index() {
        $merchantKey="SHOWLION-PAYHUBZ";
        $merchantId=26;
        $companyId=11;

        // User logo
        $merchantUser="Rapidpay User";
        $paymentUser="Payment User";

        // Panel Header
        $merchantHead='SHOWLION';
        $userHead='PAYMENT';

        // Fileuploadpath
        $filepath='showlion';
        $con = mysqli_connect('localhost', 'root', '','laravel');
        $date=date('Y-m-d');
        $days_ago = date('Y-m-d', strtotime('-10 days', strtotime($date)));

        $gateway1="SELECT sum(amount) as amt, service_provider FROM `deposits` WHERE DATE(`created_date`)='$date' and  `key`='$merchantKey' and status like 'success%' group by `service_provider`";
        $result = mysqli_query($con, $gateway1); 
        $depositAmt=0;
        while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)){						
                $servideprovider=ucfirst($row['service_provider']);
                $depositAmt+=$row['amt'];
        }
        $commsion=($depositAmt*13/100);
        $GST=($depositAmt*18/100);
        
        $total_avilable_balance=($depositAmt-0-$commsion-$GST);
        
        session('total_avilable_balance', $total_avilable_balance);
        
        
        // Daywaise Report
        $todate=date('Y-m-d');
        
        if(session('usertype') != null && session('usertype') != 'admin'){
            $flowchart="SELECT (sum(amount)+ sum(fees)) as amt,service_provider as date FROM `deposits` WHERE DATE(created_date)= '$date' and `key` like '%$merchantKey%' and status like '%success%' group by service_provider";
        }else{
            $flowchart="SELECT sum(amount) as amt,DATE(created_date) as date FROM `deposits` where `key` like '%$merchantKey%' and status like '%success%' and DATE(created_date) BETWEEN '$days_ago' AND '$date' group by DATE(created_date)";
        }
        
        $flowresult = mysqli_query($con, $flowchart);
        $depositAmt=0;
        
        $lastfive="SELECT * FROM `deposits` WHERE DATE(`created_date`)='$date' and `key`='$merchantKey' and status like '%success%' order by id DESC limit 0,5";
        $lastrecord = mysqli_query($con, $lastfive);

        $settelemeAmt = 2345;
        $expenseAmt = $GST == 0 ? 20000 : 0;
        $cashoutamount = $commsion;
        $total_avilable_balance = $total_avilable_balance;

        return view('admin.dashboard', compact('lastrecord', 'flowresult', 'settelemeAmt', 'expenseAmt', 'cashoutamount', 'total_avilable_balance'));
    }
}