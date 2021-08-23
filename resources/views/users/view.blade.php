@extends('layout.app')

@section('meta')
@endsection

@section('title')
    View User
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
                    <h4 class="card-title">View User</h4>
                </div>
                <div class="card-body ">
                    <div class="row">
                        <div class="form-group col-sm-6">
                            <label for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Plese enter name" value="{{ $data->firstname ?? '' }}" disabled="disabled" />
                            <span class="kt-form__help error name"></span>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="email">Email <span class="text-danger">*</span></label>
                            <input type="text" name="email" id="email" class="form-control" placeholder="Plese enter email address" value="{{ $data->email ?? '' }}" disabled="disabled" />
                            <span class="kt-form__help error email"></span>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="phone">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" name="phone" id="phone" class="form-control" placeholder="Plese enter phone number" value="{{ $data->phone ?? '' }}" disabled="disabled" />
                            <span class="kt-form__help error phone"></span>
                        </div>
                        <div class="form-group col-sm-12">
                            <label for="image">Image</label>
                            <input type="file" class="dropify" id="image" name="image" data-default-file="{{ $data->image }}" data-allowed-file-extensions="jpg png jpeg" data-max-file-size-preview="5M" data-show-remove="false"  disabled="disabled">
                            <span class="kt-form__help error image"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <a href="{{ route('users') }}" class="btn btn-default">Back</a>
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

