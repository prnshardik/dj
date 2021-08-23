@extends('layout.app')

@section('meta')
    <meta name="_token" content="{{ csrf_token() }}">
@endsection

@section('title')
    Cart @if(isset($cart_id)) Edit @else Add @endif
@endsection

@section('styles')
    <link href="{{ asset('assets/css/smart_wizard_all.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/select2.css') }}" rel="stylesheet" />

    <style>
        .select2-container{
            width: 100% !important;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Cart @if(isset($cart_id)) Edit @else Add @endif</h5>
                </div>
                <div class="card-body">
                    <div id="smartwizard" style="min-height:400px;">
                        <ul class="nav">
                            <li>
                                <a class="nav-link" href="#step-1" data-repo="0">
                                    Detail
                                </a>
                            </li>
                            <li>
                                <a class="nav-link" href="#step-2" data-repo="1">
                                    Item Inventories
                                </a>
                            </li>
                            <li>
                                <a class="nav-link" href="#step-3" data-repo="2">
                                    Sub Item Inventories
                                </a>
                            </li>
                            <li>
                                <a class="nav-link" href="#step-4" data-repo="3">
                                    Preview
                                </a>
                            </li>
                        </ul>
                    
                        <div class="tab-content">
                            <div id="step-1" class="tab-pane" role="tabpanel">
                                <div class="row">
                                    <div class="form-group col-sm-6">
                                        <label for="user">User Name <span class="text-danger">*</span></label>
                                        <select name="user" id="user" class="form-control"></select>
                                        <span class="kt-form__help error user"></span>
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label for="sub_users">Sub Users <span class="text-danger">*</span></label>
                                        <select name="sub_users[]" id="sub_users" class="form-control select2" multiple></select>
                                        <span class="kt-form__help error sub_users"></span>
                                    </div>
                                    <div class="form-group col-sm-12">
                                        <label for="party_name">Party Name <span class="text-danger">*</span></label>
                                        <input type="text" name="party_name" id="party_name" class="form-control" placeholder="Plese enter party name">
                                        <span class="kt-form__help error party_name"></span>
                                    </div>
                                    <div class="form-group col-sm-12">
                                        <label for="party_address">Party Address <span class="text-danger">*</span></label>
                                        <textarea name="party_address" id="party_address" cols="30" rows="3" class="form-control" placeholder="Plese enter party address"></textarea>
                                        <span class="kt-form__help error party_address"></span>
                                    </div>
                                </div>
                            </div>
                            <div id="step-2" class="tab-pane" role="tabpanel">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <span class="kt-form__help error inventory_error"></span>
                                    </div>
                                    <div class="col-sm-6 text-right">
                                        <input type="text" name="inventories_search" id="inventories_search" placeholder="inventories search">
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th width="5%">ID</th>
                                                        <th width="38%">Title</th>
                                                        <th width="57%">Items</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="inventories_datatable"></tbody>
                                            </table>
                                            <div id="inventories_pagination"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="step-3" class="tab-pane" role="tabpanel">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <span class="kt-form__help error sub_inventories_search"></span>
                                    </div>
                                    <div class="col-sm-6 text-right">
                                        <input type="text" name="sub_inventories_search" id="sub_inventories_search" placeholder="Sub inventories search">
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th width="5%">ID</th>
                                                        <th width="38%">Title</th>
                                                        <th width="57%">Items</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="sub_inventories_datatable"></tbody>
                                            </table>
                                            <div id="sub_inventories_pagination"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="step-4" class="tab-pane" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        <div class="card ">
                                            <div class="card-header ">
                                                <h5 class="card-title">User Name</h5>
                                            </div>
                                            <div class="card-body " id="preview_user"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12">
                                        <div class="card ">
                                            <div class="card-header ">
                                                <h5 class="card-title">Party Name</h5>
                                            </div>
                                            <div class="card-body " id="preview_party_name"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12">
                                        <div class="card ">
                                            <div class="card-header ">
                                                <h5 class="card-title">Party Address</h5>
                                            </div>
                                            <div class="card-body " id="preview_party_address"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12">
                                        <div class="card ">
                                            <div class="card-header ">
                                                <h5 class="card-title">Sub Users</h5>
                                            </div>
                                            <div class="card-body" id="preview_sub_users"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12">
                                        <div class="card ">
                                            <div class="card-header ">
                                                <h5 class="card-title">Items Invenotories</h5>
                                            </div>
                                            <div class="card-body" id="preview_inventories"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-sm-12">
                                        <div class="card ">
                                            <div class="card-header ">
                                                <h5 class="card-title">Sub Items Invenotories</h5>
                                            </div>
                                            <div class="card-body" id="preview_sub_inventories"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/jquery.smartWizard.js') }}"></script>
    <script src="{{ asset('assets/js/select2.js') }}"></script>
    <script>
        var config = {
            routes: {
                insert: "{{ route('cart.insert') }}",
                users: "{{ route('cart.users') }}",
                sub_users: "{{ route('cart.sub.users') }}",
                inventories: "{{ route('cart.inventories') }}",
                delete_inventories: "{{ route('cart.delete.inventories') }}",
                sub_inventories: "{{ route('cart.sub_inventories') }}",
                delete_sub_inventories: "{{ route('cart.delete.sub_inventories') }}",
                cart: "{{ route('cart') }}",
            }
        };
        var cart_id = "{{ $cart_id ?? '' }}";
        
        if(cart_id != ''){
            config.routes.insert = "{{ route('cart.update') }}",
            config.routes.detail = "{{ route('cart.detail') }}"
        }
    </script>
    <script src="{{ asset('assets/js/customSteps.js') }}"></script>
@endsection