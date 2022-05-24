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
    <h4 class="mt-4 fw-bold text-center">Swap</h4>
    <p class="text-center pb-4 mb-0 text-muted">Note: You are allowed to swap only 50% of the total available balance</p>
    <div class="row forms justify-content-center">
      <div class="col-12 col-md-5">
        <div class="bg-white boxshadow swap rounded p-3 p-md-4 mb-4">

          <form>
            <div class="bg-light rounded p-2 mb-3">
              <div class="row pb-2">
                <div class="col-5 my-auto"><img src="{{asset('assets/img/admin-merchant/gamerupee.svg')}}" width="27px" /> <strong>GAMERE</strong></div>
                <div class="col-7"><input type="text" class="form-control forninput mb-2 mr-sm-2" id="inlineFormInputName2" placeholder="00.00"></div>
              </div>
              <div class="row">
                <div class="col-12">
                  <p class="pb-0 mb-0 text-muted">Available Balance: 00.00</p>
                </div>
              </div>
            </div>
            <div class="bg-light rounded p-2 mb-3">
              <div class="row">
                <div class="col-12">
                  <p class="pb-4 mb-0 text-muted">You will get</p>
                </div>
              </div>
              <div class="row">
                <div class="col-5 my-auto"><img src="{{asset('assets/img/admin-merchant/busd.svg')}}" width="27px" /> <strong>BUSD</strong></div>
                <div class="col-7 swapget text-muted"><span>00.00</span></div>
              </div>

            </div>
          </form>

          <a href="#" class="btn btn-primary w-100">Approve</a>
        </div>
      </div>
    </div>
  </div>
</main>
@endsection

@section('footer_scipts')

@endsection