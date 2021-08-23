@extends('auth.layout.app')

@section('meta')
@endsection

@section('title')
    Forget Password
@endsection

@section('styles')
@endsection

@section('content')
    <div class="container">
        <div class="col-lg-4 col-md-6 ml-auto mr-auto">
            <form id="form" action="{{ route('password.forget') }}" method="post">
                @csrf
                @method('post')

                <div class="card card-login">
                    <div class="card-header ">
                        <div class="card-header ">
                            <h3 class="header text-center"><img src="{{ asset('qr_logo.png') }}" style="max-width: 45%;" ></h3>
                            <h6 class="header text-center">Forget Password</h6>
                        </div>
                    </div>
                    <div class="card-body ">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="nc-icon nc-single-02"></i>
                                </span>
                            </div>
                            <input class="form-control" type="email" name="email" placeholder="Email" autocomplete="off">
                            @error('email')
                                <div class="invalid-feedback" style="display: block;">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer ">
                        <button class="btn btn-warning btn-round btn-block mb-3" type="submit">Reset Password</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
@endsection