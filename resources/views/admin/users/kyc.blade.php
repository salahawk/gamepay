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
    <h5 class="mt-4 fw-bold pb-0 mb-4">User Info</h5>

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
        <div class="card-header py-3 fw-bold"> <i class="fas fa-table me-1"></i> View User Info </div>
        <div class="card-body">
          <table id="datatablesSimple" class="table table-responsive">
            <thead>
              <tr>
                <th>Email</th>
                <th>Proof1</th>
                <th>Proof2</th>
                <th>Status</th>
                <th>Remarks Proof</th>
                <th>Bank Proof</th>
                <th>Remarks Bank</th>
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
      url: "{{ route('admin.users.kycData') }}",
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
        data: 'email',
        name: 'email'
      },
      {
        data: 'proof1',
        name: 'proof1'
      },
      {
        data: 'proof2',
        name: 'proof2'
      },
      {
        data: 'status',
        name: 'status'
      },
      {
        data: 'remarks_proof',
        name: 'remarks_proof'
      },
      {
        data: 'bank_proof',
        name: 'bank_proof'
      },
      {
        data: 'remarks_bank',
        name: 'remarks_bank'
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