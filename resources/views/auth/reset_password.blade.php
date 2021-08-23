@extends('auth.layout.app')

@section('meta')
@endsection

@section('title')
    Reset Password
@endsection

@section('styles')
@endsection

@section('content')
    <div class="container">
        <div class="col-lg-4 col-md-6 ml-auto mr-auto">
            <form id="form" action="{{ route('recover.password') }}" method="post">
                @csrf
                @method('post')
                <input type="hidden" name="token" value="{{ $string }}">

                <div class="card card-login">
                    <div class="card-header ">
                        <div class="card-header ">
                            <h3 class="header text-center"><img src="{{ asset('qr_logo.png') }}" style="max-width: 45%;" ></h3>
                            <h6 class="header text-center">Reset Password</h6>
                        </div>
                    </div>
                    <div class="card-body ">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="nc-icon nc-single-02"></i>
                                </span>
                            </div>
                            <input type="email" name="email" id="email" class="form-control" placeholder="Email" autocomplete="off" value="{{ $email }}">
                            @error('email')
                                <div class="invalid-feedback" style="display: block;">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>
                        <div class="input-group" style="margin-bottom: 15px;">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="nc-icon nc-key-25"></i>
                                </span>
                            </div>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Password">
                            @error('password')
                                <div class="invalid-feedback" style="display: block;">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer ">
                        <button class="btn btn-warning btn-round btn-block mb-3" type="submit">Login</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
@endsection