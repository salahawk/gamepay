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
            <li><a href="{{ route('admin.deposits.missed') }}">View Missed Deposits</a></li>
        </ul>
    </li>
    <li class="arr">
        <i class="fa fa-envelope fa-fw"></i>
        <a href="{{ route('admin.payouts') }}">Payouts</a>
        <ul class="side-nav-dropdown">
            <li><a href="{{ route('admin.payouts') }}">View All Payout</a></li>
            <li><a href="{{ route('admin.payouts.missed') }}">View Missed Payouts</a></li>
        </ul>
    </li>
    {{-- <li class="arr"><i class="fa fa-users fa-fw"></i><a href="#">Transactions</a>
        <ul class="side-nav-dropdown">
            <li><a href="#">View Transaction</a></li>
            <li><a href="#">View Txn Summary</a></li>
        </ul>
    </li> --}}
    <li class="arr"><i class="fa fa-arrow-up fa-fw"></i><a href="{{ route('admin.users') }}">Users</a>
        <ul class="side-nav-dropdown">
            <li><a href="{{ route('admin.users') }}">Users Verification</a></li>
        </ul>
    </li>
    <li class="arr"><i class="fa fa-arrow-up fa-fw"></i><a href="{{ route('admin.activation') }}">PSP</a>
        <ul class="side-nav-dropdown">
            <li><a href="{{ route('admin.activation') }}">Activation</a></li>
        </ul>
    </li>
    <li class="arr"><i class="fa fa-arrow-up fa-fw"></i><a href="{{ route('admin.clients') }}">Clients</a>
        <ul class="side-nav-dropdown">
            <li><a href="{{ route('admin.clients') }}">Users</a></li>
        </ul>
    </li>

</ul>


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