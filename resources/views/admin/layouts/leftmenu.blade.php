<nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
  <div class="sb-sidenav-menu">
    <div class="nav">
      <a class="nav-link collapsed" data-bs-toggle="collapse" data-bs-target="#deposits" aria-expanded="true" aria-controls="collapseLayouts">
        <div class="sb-nav-link-icon"><i class="fa-solid fa-money-bill-transfer"></i></div>
        Deposit
        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
      </a>
      <div class="collapse" id="deposits" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
        <nav class="sb-sidenav-menu-nested nav">
          <a class="nav-link" href="{{ route('admin.deposits')}}"> Deposit</a>
          <a class="nav-link" href="{{ route('admin.deposits.missed')}}"> Missed Deposit</a>
        </nav>
      </div>
      <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#withdraw" aria-expanded="false" aria-controls="collapsePages">
        <div class="sb-nav-link-icon"><i class="fa-solid fa-money-bill-transfer"></i></div>
        Withdraw
        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
      </a>
      <div class="collapse" id="withdraw" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
        <nav class="sb-sidenav-menu-nested nav">
          <a class="nav-link" href="{{ route('admin.withdrawals')}}"> Withdraw</a>
          <a class="nav-link" href="{{ route('admin.withdrawals')}}"> Missed Withdraw</a>
        </nav>
      </div>
      <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#userinfo" aria-expanded="false" aria-controls="userinfo">
        <div class="sb-nav-link-icon"><i class="fa-solid fa-user"></i></div>
        User info
        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
      </a>
      <div class="collapse" id="userinfo" aria-labelledby="headingThree" data-bs-parent="#sidenavAccordion">
        <nav class="sb-sidenav-menu-nested nav">
          <a class="nav-link" href="{{ route('admin.users')}}"> User info</a>
          <a class="nav-link" href="{{ route('admin.users.kyc')}}"> KYC</a>
        </nav>
      </div>
      <a class="nav-link" href="chargeback.html">
        <div class="sb-nav-link-icon"><i class="fa-solid fa-money-bill-wave"></i></div>
        Chargebacks
      </a>
    </div>
  </div>
</nav>