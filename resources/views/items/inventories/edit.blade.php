@extends('layout.app')

@section('meta')
@endsection

@section('title')
    Edit Item
@endsection

@section('styles')
    <link href="{{ asset('assets/css/dropify.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/sweetalert2.bundle.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header ">
                    <h4 class="card-title">Edit Item</h4>
                </div>
                <div class="card-body ">
                    <form name="form" action="{{ route('items.inventories.update') }}" id="form" method="post"  autocomplete="off" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        
                        <input type="hidden" name="id" value="{{ $data->id }}">
                        @php $items_array = []; @endphp
                        @if(isset($data) && $data->items->isNotEmpty())
                            @foreach($data->items as $item)
                                @php array_push($items_array, $item->item_id); @endphp
                                <input type="hidden" class="items_id" name="items_id[]" value="{{ $item->item_id }}" />
                            @endforeach
                        @endif

                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label for="title">Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="title" class="form-control" placeholder="Plese enter title" value="{{ @old('title', $data->title) }}" />
                                <span class="kt-form__help error title"></span>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="description">Description <span class="text-danger"></span></label>
                                <input type="text" name="description" id="Description" class="form-control" placeholder="Plese enter description" value="{{ @old('description', $data->description) }}" />
                                <span class="kt-form__help error Description"></span>
                            </div>
                            <div class="form-group col-sm-12">
                                <label for="image">Image</label>
                                <input type="file" class=" dropify" id="image" name="image" data-default-file="{{ $data->image }}" data-allowed-file-extensions="jpg png jpeg" data-max-file-size-preview="5M" data-show-remove="false">
                                <span class="kt-form__help error image"></span>
                            </div>
                            <div class="col-sm-6">
                                <span class="kt-form__help error items_id"></span>
                            </div>
                            <div class="col-sm-6 text-right">
                                <input type="text" name="items_search" id="items_search" placeholder="Items search">
                            </div>
                            <div class="col-sm-12">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th width="5%">ID</th>
                                                <th width="38%">Title</th>
                                                <th width="57%">Description</th>
                                            </tr>
                                        </thead>
                                        <tbody id="items_datatable"></tbody>
                                    </table>
                                    <div id="items_pagination"></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ route('items.inventories') }}" class="btn btn-default">Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/dropify.min.js') }}"></script>
    <script src="{{ asset('assets/js/promise.min.js') }}"></script>
    <script src="{{ asset('assets/js/sweetalert2.bundle.js') }}"></script>

    <script>
        $(document).ready(function(){
            $('.dropify').dropify({
                messages: {
                    'default': 'Drag and drop profile image here or click',
                    'remove':  'Remove',
                    'error':   'Ooops, something wrong happended.'
                }
            });

            var drEvent = $('.dropify').dropify();

            var dropifyElements = {};
            $('.dropify').each(function () {
                dropifyElements[this.id] = false;
            });

            drEvent.on('dropify.beforeClear', function(event, element){
                id = event.target.id;
                if(!dropifyElements[id]){
                    var url = "{!! route('users.remove.image') !!}";
                    <?php if(isset($data) && isset($data->id)){ ?>
                        var id_encoded = "{{ base64_encode($data->id) }}";

                        Swal.fire({
                            title: 'Are you sure want delete this image?',
                            text: "",
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes'
                        }).then(function (result){
                            if (result.value){
                                $.ajax({
                                    url: url,
                                    type: "POST",
                                    data:{
                                        id: id_encoded,
                                        _token: "{{ csrf_token() }}"
                                    },
                                    dataType: "JSON",
                                    success: function (data){
                                        if(data.code == 200){
                                            Swal.fire('Deleted!', 'Deleted successfully.', 'success');
                                            dropifyElements[id] = true;
                                            element.clearElement();
                                        }else{
                                            Swal.fire('', 'Failed to delete', 'error');
                                        }
                                    },
                                    error: function (jqXHR, textStatus, errorThrown){
                                        Swal.fire('', 'Failed to delete', 'error');
                                    }
                                });
                            }
                        });

                        return false;
                    <?php } else { ?>
                        Swal.fire({
                            title: 'Are you sure want delete this image?',
                            text: "",
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes'
                        }).then(function (result){
                            if (result.value){
                                Swal.fire('Deleted!', 'Deleted successfully.', 'success');
                                dropifyElements[id] = true;
                                element.clearElement();
                            }else{
                                Swal.fire('Cancelled', 'Discard last operation.', 'error');
                            }
                        });
                        return false;
                    <?php } ?>
                } else {
                    dropifyElements[id] = false;
                    return true;
                }
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            var form = $('#form');
            $('.kt-form__help').html('');
            form.submit(function(e) {
                $('.help-block').html('');
                $('.m-form__help').html('');
                $.ajax({
                    url : form.attr('action'),
                    type : form.attr('method'),
                    data : form.serialize(),
                    dataType: 'json',
                    async:false,
                    success : function(json){
                        return true;
                    },
                    error: function(json){
                        if(json.status === 422) {
                            e.preventDefault();
                            var errors_ = json.responseJSON;
                            $('.kt-form__help').html('');
                            $.each(errors_.errors, function (key, value) {
                                $('.'+key).html(value);
                            });
                        }
                    }
                });
            });
        });
    </script>

    <script>
        var config = {
            routes: {
                inventories_items: "{{ route('items.inventories.items') }}",
                delete_inventories: "{{ route('items.inventories.items.delete') }}"
            },
            inventory_id: "{{ $data->id }}",
            items: "{{ json_encode($items_array) }}"
        };
    </script>
    <script src="{{ asset('assets/js/itemsInventory.js') }}"></script>
@endsection

