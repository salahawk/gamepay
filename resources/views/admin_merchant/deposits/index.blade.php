@extends('admin_merchant.layouts.app')
@section('header_styles')
<style>
.dataTable-selector {
  width: 70px;
}
</style>
@endsection
@section('contents')
<main>
  <div class="container-fluid px-4 pt-5">
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
          <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#addmoney">Add more</a> <a href="#" class="btn btn-outline-primary" data-toggle="modal" data-target="#exampleModal">Withdraw</a>
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
    <h5 class="mt-4 fw-bold pb-0 mb-0">Deposits</h5>
    <p class="text-success">Today deposits: 10,00000</p>
    <div class="bg-white boxshadow rounded p-3 p-md-4 mb-4">
      <div class="row forms">
        <div class="col-6 col-md-4 mb-3">
          <div class="form-group mb-4 mb-md-0">
            <div class="datepicker date input-group p-0">
              <input type="text" placeholder="From Date" class="form-control" id="from">
              <div class="input-group-append"><span class="input-group-text px-4"><i class="fa-solid fa-calendar-days"></i></span></div>
            </div>
          </div>
        </div>
        <div class="col-6 col-md-4 mb-3">
          <div class="form-group mb-4 mb-md-0">
            <div class="datepicker date input-group p-0">
              <input type="text" placeholder="To Date" class="form-control" id="to">
              <div class="input-group-append"><span class="input-group-text px-4"><i class="fa-solid fa-calendar-days"></i></span></div>
            </div>
          </div>
          <!-- DEnd ate Picker Input -->
        </div>
        <div class="col-6 col-md-4 mb-3">
          <div class="form-group mb-4 mb-md-0">
            <select class="form-control" id="status">
              <option value="0">Status</option>
              <option value="Captured">Succeeded</option>
              <option value="Failed">Failed</option>
              <option value="">Pending</option>
            </select>
          </div>
        </div>
        <div class="col-6 col-md-4 mb-3 mb-md-0">
          <div class="form-group mb-4 mb-md-0">
            <select class="form-control" id="category">
              <option value="0">Search By</option>
              <option value="1">Email ID</option>
              <option value="2">Order Id</option>
            </select>
          </div>
        </div>
        <div class="col-6 col-md-4 mb-3 mb-md-0">
          <div class="form-group mb-4 mb-md-0">
            <input class="form-control field" type="text" placeholder="Enter Email">
          </div>
        </div>
        <div class="col-6 col-md-4 mb-3 mb-md-0">
          <button type="button" class="btn btn-primary search">Submit</button>
          <button type="button" class="btn btn-primary">Download</button>
        </div>
      </div>
    </div>
    <div class="admincard">
      <div class="card mb-4 boxshadow">
        <div class="card-header py-3 fw-bold"> <i class="fas fa-table me-1"></i> Transaction </div>
        <div class="card-body">
          <table id="datatablesSimple" class="table table-hover table-bordered table-responsive">
            <thead>
              <tr>
                <th>Order ID</th>
                <th>Email ID</th>
                <th>Txn. Request time</th>
                <!-- <th>Txn. status time</th> -->
                <th>Amount</th>
                <th>Settled GR</th>
                <th>Status</th>
                <th>Remarks</th>
              </tr>
            </thead>
            <tbody>

            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</main>
@endsection

@section('footer_scipts')
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> -->
<!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"> -->
<!-- </script> -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
  var table = $('#datatablesSimple').DataTable({
    processing: true,
    serverSide: true,
    language: { search: "" },
    ajax: {
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      url: "{{ route('admin-merchant.deposits.data') }}",
      type: "POST",
      data: function(data) {
        data["from"] = $('#from').val(),
        data['to'] = $("#to").val();
        data['email'] = $("#category").val() == 1 ? $(".field").val() : "";
        data['status'] = $("#status").val() == 0 ? "" : $("#status").val();
        data['order_id'] = $("#category").val() == 2 ? $(".field").val() : "";
      }
    },
    columns: [{
        data: 'order_id',
        name: 'order_id'
      },
      {
        data: 'email',
        name: 'email'
      },
      {
        data: 'created_at',
        name: 'created_at'
      },
      {
        data: 'amount',
        name: 'amount'
      },
      {
        data: 'inr_value',
        name: 'inr_value'
      },
      {
        data: 'status',
        name: 'status'
      },
      {
        data: 'remarks',
        name: 'remarks'
      },
    ],
  });

  $(document).on('click', '.search', function() {
    table.ajax.reload();
    // $('#from').val("");
    // $("#to").val("");
    // $("#category").val(0);
    // $("#status").val(0);
    // $(".field").val("");
  });

  $(document).find('select[name="datatablesSimple_length"]').addClass("dataTable-selector");
  $(document).find('input[type="search"]').addClass("dataTable-input");
  $(document).find('input[type="search"]').attr("placeholder", "Search...");
  $(document).find("div.dataTable-dropdown").remove();
  $(document).find("div.dataTable-search").remove();
});
</script>
@endsection