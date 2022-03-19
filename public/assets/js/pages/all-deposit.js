jQuery(document).ready(function() {
    var table = $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.deposits.data') }}",
            type: "GET"
        },
        columns: [
            {data: 'txnid', name: 'txnid'},
            {data: 'email', name: 'email'},
            {data: 'bank', name: 'bank'},
            {data: 'amount', name: 'amount'},
            {data: 'status', name: 'status'},
            {data: 'created_date', name: 'created_date'},
            {data: 'provider', name: 'provider'},
        ],
    });

});