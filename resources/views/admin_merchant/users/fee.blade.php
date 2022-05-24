@extends('admin_merchant.layouts.app')
@section('header_styles')

@endsection
@section('contents')
<main>
  <div class="container-fluid px-4">
    <div class="mt-5">
      <div class="row mb-3">
        <div class="col-12 col-md-4 text-left pb-2 pb-md-0">
          <div class="alert-primary boxshadow p-3 rounded text-center">
            <p>Deposit Wallet Balance:</p>
            <h3 class="pb-0 mb-3"><strong>12,000,000 GR</strong></h3>
            <a href="#" class="btn btn-primary">Swap to BUSD</a>
          </div>
        </div>
        <div class="col-12 col-md-4 text-right pb-2 pb-md-0">
          <div class="alert-primary boxshadow p-3 rounded text-center">
            <p>Withdraw Wallet Balance:</p>
            <h3 class="pb-0 mb-3"><strong>12,000,000 GR</strong></h3>
            <a href="#" class="btn btn-primary">Add more</a> <a href="#" class="btn btn-outline-primary">Withdraw</a>
          </div>
        </div>
        <div class="col-12 col-md-4 text-right pb-2 pb-md-0">
          <div class="alert-primary boxshadow p-3 rounded text-center">
            <p>Rolling reserve Balance:</p>
            <h3 class="pb-0 mb-3"><strong>12,000,000 GR</strong></h3>
            <a href="#" class="btn btn-primary">More Details</a>
          </div>
        </div>
      </div>
    </div>
    <div class="admincard">
      <div class="card mb-4 boxshadow">
        <div class="card-header py-3 fw-bold"> <i class="fas fa-table me-1"></i> Fee Structure </div>
        <div class="card-body">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Items</th>
                <th>Fee (%)</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Deposits Netbanking</td>
                <td>6%</td>
              </tr>
              <tr>
                <td>Deposits UPI</td>
                <td>5%</td>
              </tr>
              <tr>
                <td>Withdraw Bank Transfer</td>
                <td>2.5%</td>
              </tr>
              <tr>
                <td>Withdraw Crypto</td>
                <td>1%</td>
              </tr>
              <tr>
                <td>Rolling Reserve</td>
                <td>5% (for 6 month)</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</main>
@endsection
@section('footer_scipts')

@endsection