@extends('admin.layouts.app')
@section('header_styles')
<style>
  .dataTable-selector {
    width: 70px;
  }
</style>
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
    <h5 class="mt-4 fw-bold pb-0 mb-0">Withdrawals</h5>
    <p class="text-success">Today withdraw: 10,00000</p>
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
              <option value="Success">Succeeded</option>
              <option value="Failure">Failed</option>
              <option value="Rejected">Rejected</option>
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
          <table id="datatablesSimple" class="table table-responsive">
            <thead>
              <tr>
                <th>Order ID</th>
                <th>Txn ID</th>
                <th>Email</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Req Date</th>
                <th>Status</th>
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
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
  var table = $('#datatablesSimple').DataTable({
    processing: true,
    serverSide: true,
    language: {
      search: ""
    },
    ajax: {
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      url: "{{ route('admin.withdrawals.data') }}",
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
        data: 'txn_id',
        name: 'txn_id'
      },
      {
        data: 'email',
        name: 'email'
      },
      {
        data: 'txn_amount',
        name: 'txn_amount'
      },
      {
        data: 'txn_payment_type',
        name: 'txn_payment_type'
      },
      {
        data: 'created_at',
        name: 'created_at'
      },
      {
        data: 'status',
        name: 'status'
      }
    ],
  });

  $(document).on('click', '.search', function() {
    table.ajax.reload();
  });

  $(document).find('select[name="datatablesSimple_length"]').addClass("dataTable-selector");
  $(document).find('input[type="search"]').addClass("dataTable-input");
  $(document).find('input[type="search"]').attr("placeholder", "Search...");
  $(document).find("div.dataTable-dropdown").remove();
  $(document).find("div.dataTable-search").remove();
</script>
@endsection