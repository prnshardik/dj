@extends('layout.app')

@section('meta')
@endsection

@section('title')
    Print QR Code
@endsection

@section('styles')
    <style>
        @media print{
            #printDivStyle{
                display: flex;
                justify-content: center;
                align-items: center;
                height: 90vh;
            }

            #heightDiv{
                display: none;
            }

            #widthDiv{
                display: none;
            }
        }
    </style>
@endsection

@section('content')
    <div class="row" id="printableArea">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-body ">
                        <div class="row">
                            <div class="form-group col-sm-12" id="printDivStyle">
                                @if(isset($data) && !empty($data->qrcode))
                                    <div class="text-center" id="printableArea">
                                        <img id="image" src="{{ url('uploads/qrcodes/sub_items').'/'.$data->qrcode }}" alt="{{ $data->qrcode }}" class="ml-2" style="width: 250px; height: 250px" >
                                        <div id="name"class="text-center">
                                            <span id="font" style="font-size:20px"><b>{{ $data->name }}</b></span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="form-group col-md-3 col-sm-3"></div>
                            <div class="form-group col-md-3 col-sm-3" id="heightDiv">
                                <label for="height">Height:</label>
                                <input type="text" name="height" id="height" class="form-control height">
                            </div>
                            <div class="form-group col-md-3 col-sm-3" id="widthDiv">
                                <label for="width">Width:</label>
                                <input type="text" name="width" id="width" class="form-control width">
                            </div>
                            <div class="form-group col-md-3 col-sm-3"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 text-center">
                @if(isset($data) && !empty($data->qrcode))
                    <input type="button" class="btn btn-primary" style="cursor:pointer" onclick="printDiv('printableArea')" value="Print" />
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function printDiv(divName) {
            let height = $('#height').val();
            let width = $('#width').val();

            if(height == ''){ height = 5; }
            if(width == ''){ width = 5; }

            height = height * 96;
            width = width * 96;
            font_size = width / 160;

            $('#image').css("height", height+"px");
            $('#image').css("width", width+"px");
            $('#name').css("width", width+"px");
            $('#font').css("font-size", font_size+"em");

            var printContents = document.getElementById(divName).innerHTML;
            var originalContents = document.body.innerHTML;
           
            document.body.innerHTML = printContents;

            window.print();

            document.body.innerHTML = originalContents;

            $('#image').css("height", "250px");
            $('#image').css("width", "250px");
            $('#name').removeAttr("style");
            $('#font').css("font-size", "20px");
        }
    </script>
@endsection

