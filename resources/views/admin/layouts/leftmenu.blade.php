<script>
    $(document).ready(function($) {
        var url = window.location.href;
        $('ul.categories li').each(function() {
            $(this).removeClass('opend');
        });
        $('ul li a[href="' + url + '"]').parents('li').addClass('opend');
        $('ul li a[href="' + url + '"]').parents('ul').css('display', "block");
        $('li.opend').children('a').attr('style', 'color: #ff5722 !important');
    });
</script>
<?php
//session_start(); 



// Teeanpathi Panel
if(isset($_SESSION['usertype']) && $_SESSION['usertype']=='admin'){?>
<ul class="categories">
    <li class="opend"><i class="fa fa-home fa-fw" aria-hidden="true"></i><a href="dashboard.php">Dashboard</a>
    </li>
    <li class="arr"><i class="fa fa-file-text fa-fw"></i><a href="#">Settlements</a>
        <ul class="side-nav-dropdown">
            <li><a href="view_gw_settlement.php">View Settlement</a></li>
        </ul>
    </li>
    <li class="arr"><i class="fa fa-file-text fa-fw"></i><a href="all_deposit_transaction.php">Today Deposit by
            Email</a>

    </li>
    <li class="arr"><i class="fa fa-envelope fa-fw"></i><a href="#">Deposits</a>
        <ul class="side-nav-dropdown">
            <li><a href="view_all_deposit.php">View All Deposit</a></li>
            <li><a href="view_deposit.php">View Success</a></li>
            <li><a href="view_pending_deposit.php">View Pending / failure</a></li>

            <li><a href="check_missed.php">Today Missed Deposit</a></li>

            <li><a href="view_missed_deposit.php">View Missed Deposit</a></li>
        </ul>
    </li>
    <li class="arr" class="opend"> <i class="fa fa-envelope fa-fw"></i><a
            href="gateway_status.php">Gateway Status</a></li>
    <li class="arr"><i class="fa fa-users fa-fw"></i><a href="#">Transactions</a>
        <ul class="side-nav-dropdown">
            <li><a href="transaction.php">View Transaction</a></li>
            <li><a href="summary_transaction.php">View Txn Summary</a></li>
        </ul>
    </li>
    <li class="arr"><i class="fa fa-arrow-up fa-fw"></i><a href="#">Cash Outs</a>
        <ul class="side-nav-dropdown">
            <li><a href="cashout.php">Import Cashout</a></li>
            <li><a href="view_cash_detail.php">View Cashout</a></li>
            <li><a href="approve_redeem.php">View Redeem Return</a></li>
            <li><a href="view_uploaded_files.php">View Uploaded Files</a></li>
        </ul>
    </li>
</ul>
<?php }else if(isset($_SESSION['usertype']) && $_SESSION['support']==1){?>
<ul class="categories">
    <li class="arr" class="opend"> <i class="fa fa-envelope fa-fw"></i><a href="#">Deposits</a>
        <ul class="side-nav-dropdown">

            <li><a href="check_missed.php">Last Two Days Missed</a></li>
            <li><a href="view_missed_deposit.php">View All Missed</a></li>
            <li><a href="depositinfo.php">View Direct Deposit</a></li>
            <li><a href="view_all_deposit.php">View All Deposit</a></li>
            <li><a href="view_deposit.php">View Success</a></li>
            <li><a href="view_pending_deposit.php">View Failure / Initiated</a></li>
        </ul>
    </li>
    <li class="arr" class="opend"> <i class="fa fa-envelope fa-fw"></i><a
            href="gateway_status.php">Gateway Status</a></li>
    <li class="arr"><i class="fa fa-arrow-up fa-fw"></i><a href="#">Cash Outs</a>
        <ul class="side-nav-dropdown">
            <li><a href="view_cash_detail.php">View Cashout</a></li>
        </ul>
    </li>
</ul>
<?php }else if(isset($_SESSION['usertype']) && $_SESSION['support']==2){?>
<ul class="categories">
    <li class="arr" class="opend"> <i class="fa fa-envelope fa-fw"></i><a href="#">Deposits</a>
        <ul class="side-nav-dropdown">
            <li><a href="check_missed.php">Last Two Days Missed</a></li>
            <li><a href="view_missed_deposit.php">View All Missed</a></li>
            <li><a href="depositinfo.php">View Direct Deposit</a></li>
        </ul>
    </li>
    <li class="arr" class="opend"> <i class="fa fa-envelope fa-fw"></i><a
            href="gateway_status.php">Gateway Status</a></li>

    <li class="arr"><i class="fa fa-arrow-up fa-fw"></i><a href="#">Cash Outs</a>
        <ul class="side-nav-dropdown">
            <li><a href="view_cash_detail.php">View Cashout</a></li>
        </ul>
    </li>
</ul>


<?php }else{ ?>

<ul class="categories">
    <li class="opend"><i class="fa fa-home fa-fw" aria-hidden="true"></i><a href="{{ route('admin.dashboard') }}"> Dashboard</a>
    </li>

    <li class="arr"><i class="fa fa-file-text fa-fw"></i><a href="all_email_deposit.php">Today Deposits by
            Email</a>

    </li>
    <li class="arr"><i class="fa fa-envelope fa-fw"></i><a href="#">Deposits</a>
        <ul class="side-nav-dropdown">
            <li><a href="{{ route('admin.deposits') }}">View All Deposit</a></li>
            <li><a href="view_deposit.php">View Success</a></li>
            <li><a href="view_pending_deposit.php">View Failure / Initiated</a></li>
            <li><a href="check-status.php">Check Status</a></li>
            <li><a href="check_missed.php">Two Days Missed Deposit</a></li>
            <li><a href="view_missed_deposit.php">View Missed Deposit</a></li>
        </ul>
    </li>
    <li class="arr"><i class="fa fa-users fa-fw"></i><a href="#">Transactions</a>
        <ul class="side-nav-dropdown">
            <li><a href="transaction.php">View Transaction</a></li>
            <li><a href="summary_transaction.php">View Txn Summary</a></li>

        </ul>
    </li>
</ul>
<?php } ?>
@section('footer_script')
@endsection