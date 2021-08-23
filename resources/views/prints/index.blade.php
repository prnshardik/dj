@extends('layout.app')

@section('meta')
@endsection

@section('title')
    Prints
@endsection

@section('styles')
    <style>
        .table-responsive {
            overflow: inherit;
        }

        .btn, .navbar .navbar-nav>a.btn {
            margin: 0px 0px !important;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"></div>
                <div class="card-body">
                    <div class="toolbar"></div>
                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <select name="options" class="form-control" id="options">
                                <option value="">Select option</option>
                                <option value="items" selected>Items</option>
                                <option value="subItems">Sub Items</option>
                                <option value="itemsInventories">Items Inventories</option>
                                <option value="subItemsInventories">Sub Items Inventories</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <button id="optionSubmit" class="btn btn-primary">Submit</button>
                        </div>
                        <div class="col-sm-6"></div>
                        <div class="col-sm-6 text-right">
                            <input type="text" name="search" id="search" placeholder="search">
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th width="5%">Sr. No</th>
                                    <th width="65%">Title</th>
                                    <th width="10%">Height</th>
                                    <th width="10%">Width</th>
                                    <th width="10%">Quantity</th>
                                </tr>
                            </thead>
                            <tbody id="datatable"></tbody>
                        </table>
                        <div id="pagination"></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 pr-5 pb-2">
                            <button id="submit" class="btn btn-primary float-right">Submit</button>
                        </div>
                    </div>

                    <form action="{{ route('prints.print') }}" method="post" id="myForm">
                        @csrf
                        @method('post')

                        <input type="hidden" name="formOption" id="formOption">
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        let option = '';
        let data = {};

        $(document).on('click', '#pagination .pagination a', function(event){
            event.preventDefault(); 
            var page = $(this).attr('href').split('page=')[1];
            var search = $('#search').val();
            _data(page, search);
        });

        $(document).on('keyup', '#search', function(event){
            event.preventDefault(); 
            var search = $('#search').val();
            _data(0, search);
        });

        function _data(page, search){
            $.ajax({
                "url": "{{ route('prints') }}"+"?page="+page+"&search="+search+"&selected="+JSON.stringify(data)+"&option="+option,
                "dataType": "json",
                "type": "Get",
                success: function (response){
                    $('#datatable').html(response.data);
                    $('#pagination').html(response.pagination);
                },
                error: function(response){
                    $('#datatable').html('<td colspan="5" class="text-center"><h3>No data found</h3></td>');
                    $('#pagination').html('');
                }
            });
        }

        $(document).ready(function () {
            $('#optionSubmit').on('click', function(){
                option = $('#options').find(":selected").val();

                if(option == ''){
                    alert('please select option first');
                } else {
                    _data(0, '');
                }
            });
        });

        $(document).on('click', '.option', function(){
            var value = $(this).val();
            var id = $(this).data('id');

            let height = $('#height'+id).val();
            let width = $('#width'+id).val();
            let quantity = $('#quantity'+id).val();

            if(height == '' || width == '' || quantity == ''){
                return false;
            }

            if($(this).prop("checked") == true){
                data[value] = {'id': value, 'height': height, 'width': width, 'quantity': quantity};
            } else {
                delete data[value];
            }
        });

        $('#submit').on('click', function(){
            if(option == ''){
                alert('please select option');
                return false;
            }

            if(jQuery.isEmptyObject(data)){
                alert('please select data');
                return false;
            }

            $('#formOption').val(option);
            
            $.each(data, function(index, value){
                $('#myForm').append("<input type='hidden' name='formId[]' value="+value.id+">");
                $('#myForm').append("<input type='hidden' name='formHeight[]' value="+value.height+">");
                $('#myForm').append("<input type='hidden' name='formWidth[]' value="+value.width+">");
                $('#myForm').append("<input type='hidden' name='formQuantity[]' value="+value.quantity+">");
            });

            $("#myForm").submit();
        });

        $(document).ready(function () {
            option = 'items';
            _data(0, '');
        });
    </script>
@endsection
