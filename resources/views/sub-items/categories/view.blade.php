@extends('layout.app')

@section('meta')
@endsection

@section('title')
    View sub item category
@endsection

@section('styles')
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header ">
                    <h4 class="card-title">View sub item category</h4>
                </div>
                <div class="card-body ">
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <label for="title">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control" placeholder="Plese enter title" value="{{ @old('title', $data->title) }}" disabled/>
                            <span class="kt-form__help error title"></span>
                        </div>
                        <div class="form-group col-sm-12">
                            <label for="description">Description <span class="text-danger"></span></label>
                            <textarea name="description" id="description" cols="30" rows="10" class="form-control" placeholder="Plese enter description" disabled>{{ @old('description', $data->description) }}</textarea>
                            <span class="kt-form__help error description"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <a href="{{ route('sub.items.categories') }}" class="btn btn-default">Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@endsection

