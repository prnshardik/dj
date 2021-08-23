@extends('layout.app')

@section('meta')
@endsection

@section('title')
    Cart
@endsection

@section('styles')
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Cart
                        <a class="btn btn-primary btn-sm pull-right ml-2" style="margin-top: 8px;margin-bottom: 5px" href="{{ route('cart.create') }}">Add New</a>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="toolbar"></div>
                    <div id="datatable_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4">
                        <table id="data-table" class="table responsive table-striped table-bordered dataTable dtr-inline" cellspacing="0" width="100%" role="grid" style="width: 100%;">
                            <thead>
                                <tr role="row">
                                    <th>No</th>
                                    <th>User Name</th>
                                    <th>Party Name</th>
                                    <th>Items Count</th>
                                    <th>Sub Items Count</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">

        var datatable;

        $(document).ready(function() {
            if($('#data-table').length > 0){
                datatable = $('#data-table').DataTable({
                    processing: true,
                    serverSide: true,

                    // "pageLength": 10,
                    // "iDisplayLength": 10,
                    "responsive": true,
                    "aaSorting": [],
                    // "order": [], //Initial no order.
                    //     "aLengthMenu": [
                    //     [5, 10, 25, 50, 100, -1],
                    //     [5, 10, 25, 50, 100, "All"]
                    // ],

                    // "scrollX": true,
                    // "scrollY": '',
                    // "scrollCollapse": false,
                    // scrollCollapse: true,

                    // lengthChange: false,

                    "ajax":{
                        "url": "{{ route('cart') }}",
                        "type": "POST",
                        "dataType": "json",
                        "data":{
                            _token: "{{csrf_token()}}"
                        }
                    },
                    "columnDefs": [{
                            //"targets": [0, 5], //first column / numbering column
                            "orderable": true, //set not orderable
                        },
                    ],
                    columns: [
                        {
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex'
                        },
                        {
                            data: 'user_name',
                            name: 'user_name'
                        },
                        {
                            data: 'party_name',
                            name: 'party_name'
                        },
                        {
                            data: 'items_count',
                            name: 'items_count'
                        },
                        {
                            data: 'sub_items_count',
                            name: 'sub_items_count'
                        },
                        {
                            data: 'status',
                            name: 'status'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                        },
                    ]
                });
            }
        });

        function change_status(object){
            var id = $(object).data("id");
            var msg = "Are you sure to delete record?";

            if (confirm(msg)) {
                $.ajax({
                    "url": "{!! route('cart.change.status') !!}",
                    "dataType": "json",
                    "type": "POST",
                    "data":{
                        id: id,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response){
                        if (response.code == 200){
                            datatable.ajax.reload();
                            toastr.success('Record status changed successfully.', 'Success');
                        }else{
                            toastr.error('Failed to delete record.', 'Error');
                        }
                    }
                });
            }
        }
    </script>
@endsection
