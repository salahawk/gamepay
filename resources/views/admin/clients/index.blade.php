@extends('admin.layouts.app')

@section('header_style')
    <link href="{{ asset('assets/js/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/js/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet"
        type="text/css" />
@endsection

@section('content')
    <div class="welcome">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="content">
                        <h2>View All Clients</h2>
                        <p>We are working to enhance your experience!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form action="{{ route('admin.clients.store') }}" method="POST">
        <section class="frm-sec">
            <div class="row">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                {{-- <div class="col-md-6">
                    <div class="form-group">
                        <label for="usr">Deposits Date From</label>
                        <input type="date" class="ipt-usr in-cs effect-15 form-control" name="fromdate" id='fromdate'
                            class="in-cs" value="">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="usr">Deposits Date To</label>
                        <input type="date" class="ipt-usr in-cs effect-15 form-control" name="todate" id='todate'
                            class="in-cs" value="">
                    </div>
                </div> --}}
                {{-- <div class="col-md-6">
                    <div class="form-group">
                        <label for="usr">Email</label>
                        <input type="text" class="ipt-usr in-cs effect-15 form-control" name="emailid" id='emailid'
                            class="in-cs" value="">
                    </div>
                </div>

                <div class="col-md-6">

                    <div class="form-group">
                        <label for="usr">Transaction Id</label>
                        <input type="text" class="ipt-usr in-cs effect-15 form-control" name="transid" id='transid'
                            class="in-cs" value="">
                    </div>

                </div> --}}

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="usr">IP</label>
                        <input type="text" class="ipt-usr in-cs effect-15 form-control" name="ip" id='ip'
                            class="in-cs" value="" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="usr">Name</label>
                        <input type="text" class="ipt-usr in-cs effect-15 form-control" name="name" id='name'
                            class="in-cs" value="" required>
                    </div>
                </div>

                <center>
                    <button type="submit" class="btn btn-outline frm-btn">Create</button><br>
                    {{-- <a onclick="exportExcel()" class="btn btn-outline frm-btn" name="submit"
                        value="settlementAdd">Download</a> --}}
                </center>
            </div>

        </section>
    </form>
    <section class="h-tb-sec">
        <div class="h-tb">
            <div class="home-tb">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>IP</th>
                                <th>Created Date</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
    <br>
    <br>

    <!-- section -->
@endsection
@section('footer_script')
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.js"></script> --}}
    {{-- <script src="{{ asset('assets/js/main.js') }}"></script> --}}
    {{-- <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script> --}}
    {{-- <script src="vendor/jquery-easing/jquery.easing.min.js"></script> --}}
    {{-- <script src="{{ asset('assets/js/datatables/dataTable.js') }}"></script> --}}
    {{-- <script src="{{ asset('assets/js/datatables/dataTables.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('assets/js/datatables/plugins/bootstrap/datatables.bootstrap.js' )}}" type="text/javascript"></script> --}}
    {{-- <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script> --}}
    {{-- <script src="{{ asset('js/pages/datatables-demo.js') }}"></script> --}}
    <script>
        var table = $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            url: "{{ route('admin.clients.data') }}",
            type: "POST"
        },
        columns: [
            {data: 'name', name: 'name'},
            {data: 'ip', name: 'ip'},
            {data: 'created_at', name: 'created_at'},
        ],
        });
    </script>
@endsection
