@extends('layout.app')

@section('meta')
@endsection

@section('title')
    Edit Items Categories
@endsection

@section('styles')
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header ">
                    <h4 class="card-title">Edit Items Categories</h4>
                </div>
                <div class="card-body ">
                    <form name="form" action="{{ route('items.categories.update') }}" id="form" method="post"  autocomplete="off" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        
                        <input type="hidden" name="id" value="{{ $data->id }}">

                        <div class="row">
                            <div class="form-group col-sm-12">
                                <label for="title">Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="title" class="form-control" placeholder="Plese enter title" value="{{ @old('title', $data->title) }}" />
                                <span class="kt-form__help error title"></span>
                            </div>
                            <div class="form-group col-sm-12">
                                <label for="description">Description <span class="text-danger"></span></label>
                                <textarea name="description" id="description" cols="30" rows="10" class="form-control" placeholder="Plese enter description">{{ @old('description', $data->description) }}</textarea>
                                <span class="kt-form__help error description"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ route('items.categories') }}" class="btn btn-default">Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
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
@endsection

