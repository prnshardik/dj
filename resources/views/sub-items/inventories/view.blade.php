@extends('layout.app')

@section('meta')
@endsection

@section('title')
    View Sub Item
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
                    <h4 class="card-title">View Sub Item</h4>
                </div>
                <div class="card-body ">
                    <div class="row">
                        <div class="form-group col-sm-6">
                            <label for="title">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control" placeholder="Plese enter title" value="{{ @old('title', $data->title) }}" disabled/>
                            <span class="kt-form__help error title"></span>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="description">Description <span class="text-danger"></span></label>
                            <input type="text" name="description" id="Description" class="form-control" placeholder="Plese enter description" value="{{ @old('description', $data->description) }}" disabled/>
                            <span class="kt-form__help error Description"></span>
                        </div>
                        <div class="form-group col-sm-12">
                            <label for="image">Image</label>
                            <input type="file" class=" dropify" id="image" name="image" data-default-file="{{ $data->image }}" data-allowed-file-extensions="jpg png jpeg" data-max-file-size-preview="5M" data-show-remove="false" disabled="disabled">
                            <span class="kt-form__help error image"></span>
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
                                    <tbody id="items_datatable">
                                        @if(isset($data) && $data->items->isNotEmpty())
                                            @php $i = 1; @endphp
                                            @foreach($data->items as $row)
                                                <tr>
                                                    <td>{{ $i }}</td>
                                                    <td>{{ $row->name }}</td>
                                                    <td>{{ $row->description }}</td>
                                                </tr>
                                                @php $i++; @endphp
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <a href="{{ route('sub.items.inventories') }}" class="btn btn-default">Back</a>
                    </div>
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
        });
    </script>
@endsection

