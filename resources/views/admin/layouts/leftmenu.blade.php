<?php

if(isset($_SESSION['usertype']) && $_SESSION['usertype']=='admin'){?>
<ul class="categories">
    <li class="opend">
        <i class="fa fa-home fa-fw" aria-hidden="true"></i>
        <a href="#">Dashboard</a>
    </li>
    <li class="arr"><i class="fa fa-file-text fa-fw"></i><a href="#">Settlements</a>
        <ul class="side-nav-dropdown">
            <li><a href="#">View Settlement</a></li>
        </ul>
    </li>
    <li class="arr"><i class="fa fa-file-text fa-fw"></i><a href="#">Today Deposit by
            Email</a>

    </li>
    <li class="arr"><i class="fa fa-envelope fa-fw"></i><a href="#">Deposits</a>
        <ul class="side-nav-dropdown">
            <li><a href="#">View All Deposit</a></li>
            <li><a href="#">View Success</a></li>
            <li><a href="#">View Pending / failure</a></li>

            <li><a href="#">Today Missed Deposit</a></li>

            <li><a href="#">View Missed Deposit</a></li>
        </ul>
    </li>
    <li class="arr" class="opend"> <i class="fa fa-envelope fa-fw"></i><a
            href="#">Gateway Status</a></li>
    <li class="arr"><i class="fa fa-users fa-fw"></i><a href="#">Transactions</a>
        <ul class="side-nav-dropdown">
            <li><a href="#">View Transaction</a></li>
            <li><a href="#">View Txn Summary</a></li>
        </ul>
    </li>
    <li class="arr"><i class="fa fa-arrow-up fa-fw"></i><a href="#">Cash Outs</a>
        <ul class="side-nav-dropdown">
            <li><a href="#">Import Cashout</a></li>
            <li><a href="#">View Cashout</a></li>
            <li><a href="#">View Redeem Return</a></li>
            <li><a href="#">View Uploaded Files</a></li>
        </ul>
    </li>
</ul>
<?php }else if(isset($_SESSION['usertype']) && $_SESSION['support']==1){?>
<ul class="categories">
    <li class="arr" class="opend"> <i class="fa fa-envelope fa-fw"></i><a href="#">Deposits</a>
        <ul class="side-nav-dropdown">

            <li><a href="#">Last Two Days Missed</a></li>
            <li><a href="#">View All Missed</a></li>
            <li><a href="#">View Direct Deposit</a></li>
            <li><a href="#">View All Deposit</a></li>
            <li><a href="#">View Success</a></li>
            <li><a href="#">View Failure / Initiated</a></li>
        </ul>
    </li>
    <li class="arr" class="opend"> <i class="fa fa-envelope fa-fw"></i><a
            href="#">Gateway Status</a></li>
    <li class="arr"><i class="fa fa-arrow-up fa-fw"></i><a href="#">Cash Outs</a>
        <ul class="side-nav-dropdown">
            <li><a href="#">View Cashout</a></li>
        </ul>
    </li>
</ul>
<?php }else if(isset($_SESSION['usertype']) && $_SESSION['support']==2){?>
<ul class="categories">
    <li class="arr" class="opend"> <i class="fa fa-envelope fa-fw"></i><a href="#">Deposits</a>
        <ul class="side-nav-dropdown">
            <li><a href="#">Last Two Days Missed</a></li>
            <li><a href="#">View All Missed</a></li>
            <li><a href="#">View Direct Deposit</a></li>
        </ul>
    </li>
    <li class="arr" class="opend"> <i class="fa fa-envelope fa-fw"></i><a
            href="#">Gateway Status</a></li>

    <li class="arr"><i class="fa fa-arrow-up fa-fw"></i><a href="#">Cash Outs</a>
        <ul class="side-nav-dropdown">
            <li><a href="#">View Cashout</a></li>
        </ul>
    </li>
</ul>


<?php }else{ ?>

<ul class="categories">
    <li class="opend" id = "dash_liID">
        <i class="fa fa-home fa-fw" aria-hidden="true"></i>
        <a href="{{ route('admin.dashboard') }}"> Dashboard</a>
    </li>
    {{-- <li class="arr">
        <i class="fa fa-file-text fa-fw"></i>
        <a href="#">Today Deposits by Email</a>
    </li> --}}
    <li class="arr">
        <i class="fa fa-envelope fa-fw"></i>
        <a href="{{ route('admin.deposits') }}">Deposits</a>
        <ul class="side-nav-dropdown">
            <li><a href="{{ route('admin.deposits') }}">View All Deposit</a></li>
            <li><a href="{{ route('admin.success') }}">View Success</a></li>
            <li><a href="{{ route('admin.pending') }}">View Failure / Initiated</a></li>
            <li><a href="#">Check Status</a></li>
            <li><a href="#">Two Days Missed Deposit</a></li>
            <li><a href="#">View Missed Deposit</a></li>
        </ul>
    </li>
    {{-- <li class="arr"><i class="fa fa-users fa-fw"></i><a href="#">Transactions</a>
        <ul class="side-nav-dropdown">
            <li><a href="#">View Transaction</a></li>
            <li><a href="#">View Txn Summary</a></li>
        </ul>
    </li> --}}
    <li class="arr"><i class="fa fa-arrow-up fa-fw"></i><a href="{{ route('admin.activation') }}">Users</a>
        <ul class="side-nav-dropdown">
            <li><a href="{{ route('admin.activation') }}">Activation</a></li>
        </ul>
    </li>
</ul>
<?php } ?>

<script>
    $(document).ready(function($) {
        var url = window.location.href;
        $('ul.categories li').each(function() {
            $(this).removeClass('opend');
        });
        $('ul li a[href="' + url + '"]').parents('li').addClass('opend');
        $('ul li a[href="' + url + '"]').parents('ul').css('display', "block");
        $('li.opend').children('a').attr('style', 'color: #ff5722 !important');

        $("#dash_liID").click(function() {
            window.location.replace($(this).children('a').attr('href'));
        });
    });
</script>