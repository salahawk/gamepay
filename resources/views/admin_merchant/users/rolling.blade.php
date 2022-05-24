@extends('admin_merchant.layouts.app')
@section('header_styles')

@endsection
@section('contents')
<main>
      <div class="container-fluid px-4 pt-5">
       <div class="row mb-3">
            <div class="col-12 col-md-4 text-left pb-2 pb-md-0">
              <div class="alert-primary boxshadow p-3 rounded text-center">
                <p>Deposit Wallet Balance:</p>
                <h3 class="pb-0 mb-3"><strong>12,000,000 GR</strong></h3>
                <a href="#" class="btn btn-primary">Swap to BUSD</a> </div>
            </div>
            <div class="col-12 col-md-4 text-right pb-2 pb-md-0">
              <div class="alert-primary boxshadow p-3 rounded text-center">
                <p>Withdraw Wallet Balance:</p>
                <h3 class="pb-0 mb-3"><strong>12,000,000 GR</strong></h3>
                <a href="#" class="btn btn-primary">Add more</a> <a href="#" class="btn btn-outline-primary"  data-toggle="modal" data-target="#exampleModal">Withdraw</a> </div>
            </div>
            <div class="col-12 col-md-4 text-right pb-2 pb-md-0">
              <div class="alert-primary boxshadow p-3 rounded text-center">
                <p>Rolling reserve Balance:</p>
                <h3 class="pb-0 mb-3"><strong>12,000,000 GR</strong></h3>
                <a href="#" class="btn btn-primary">More Details</a> </div>
            </div>
          </div>
        <h5 class="mt-4 fw-bold pb-0 mb-4">Rolling reserve / Chargebacks</h5>       
       
        <div class="admincard">
          <div class="card mb-4 boxshadow">
            <div class="card-header py-3 fw-bold"> <i class="fas fa-table me-1"></i> Rolling reserve </div>
            <div class="card-body">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Total Rolling Reserve</th>
                    <th>Ready to collect</th>
                    <th>Action</th>                    
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>10,000 GR</td>
                    <td>5000 GR</td>
                    <td><a href="#" class="btn btn-outline-info">Collect</a></td>                    
                  </tr>                  
                </tbody>
              </table>
            </div>
          </div>
        </div>
		  
        <div class="admincard">
          <div class="card mb-4 boxshadow">
            <div class="card-header py-3 fw-bold"> <i class="fas fa-table me-1"></i> Chargebacks </div>
            <div class="card-body">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Email id</th>
                    <th>Amount</th> 
					  <th>Debited GR</th> 
					  <th>info</th>   
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>20/05/2022</td>
                    <td>abc@gmail.com</td>
                    <td>10,000 INR</td> 
					  <td class="text-danger">-10,005 GR</td> 
					  <td>enna panrinsjhd lskj</td> 					  
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