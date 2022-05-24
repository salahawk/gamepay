<nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
  <div class="sb-sidenav-menu">
    <div class="nav">
      <a class="nav-link active deposits" href="{{ route('admin-merchant.deposits') }}">
        <div class="sb-nav-link-icon"><i class="fa-solid fa-money-bill"></i></div>
        Deposits
      </a>
      <a class="nav-link withdrawals" href="{{ route('admin-merchant.withdrawals') }}">
        <div class="sb-nav-link-icon"><i class="fa-solid fa-money-bill"></i></div>
        Withdrawals
      </a>
      <a class="nav-link swap" href="{{ route('admin-merchant.swap') }}">
        <div class="sb-nav-link-icon"><i class="fa-solid fa-money-bill-transfer"></i></div>
        Swap
      </a>
      <a class="nav-link rolling" href="{{ route('admin-merchant.rolling') }}">
        <div class="sb-nav-link-icon"><i class="fa-solid fa-hand-holding-dollar"></i></div>
        Rolling Reserve/Chargebacks
      </a>
      <a class="nav-link users" href="{{ route('admin-merchant.users') }}">
        <div class="sb-nav-link-icon"><i class="fa-solid fa-user"></i></div>
        View user Info
      </a>
      <a class="nav-link deposit-guide" href="{{ route('admin-merchant.deposits.guide') }}">
        <div class="sb-nav-link-icon"><i class="fa-solid fa-file-lines"></i></div>
        Deposit integration Guide
      </a>
      <a class="nav-link withdrawal-guide" href="{{ route('admin-merchant.withdrawals.guide') }}">
        <div class="sb-nav-link-icon"><i class="fa-solid fa-file-lines"></i></div>
        Payout integration Guide
      </a>
      <a class="nav-link fee" href="{{ route('admin-merchant.users.fee') }}">
        <div class="sb-nav-link-icon"><i class="fa-solid fa-rectangle-list"></i></div>
        Fee Structure
      </a>
      <a class="nav-link history" href="{{ route('admin-merchant.withdrawals.guide') }}">
        <div class="sb-nav-link-icon"><i class="fa-solid fa-rectangle-list"></i></div>
        GR Txn History
      </a>
    </div>
  </div>
</nav>