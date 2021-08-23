@extends('layout.app')

@section('meta')
@endsection

@section('title')
    Print QR Codes
@endsection

@section('styles')
    <style>
        @media print{
            #printDivStyle{
                height: 90vh;
                margin-bottom: 50px;
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
                                @if($data->isNotEmpty())
                                    <div class="printableArea" style="display:flex; flex-wrap: wrap;">
                                        @foreach($data as $row)
                                            @for($i=0; $i<$row->quantity; $i++)
                                                @php 
                                                    $height = $row->height * 96; 
                                                    $width = $row->width * 96;
                                                    $font = $width / 160 ."em";
                                                    // dd($font);
                                                    $path = '';

                                                    if($option == 'items'){
                                                        $path = url('uploads/qrcodes/items');
                                                    }elseif($option == 'subItems'){
                                                        $path = url('uploads/qrcodes/sub_items');
                                                    }elseif($option == 'itemsInventories'){
                                                        $path = url('uploads/qrcodes/items_inventory');
                                                    }elseif($option == 'subItemsInventories'){
                                                        $path = url('uploads/qrcodes/sub_items_inventory');
                                                    }
                                                @endphp
                                                <div class="print-break">
                                                    <div style="margin-left: 10px; margin-right: 10px; margin-bottom: 30px;">
                                                        <img id="image" src="{{ $path.'/'.$row->qrcode }}" alt="{{ $row->qrcode }}" style="width: {{ $width }}px; height: {{ $height }}px; padding: 5px;">
                                                        <div id="name" class="text-center" style="width: {{ $width }}px;">
                                                            <span style="font-size: {{$font}}"><b>{{ $row->name }}</b></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endfor
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 text-center">
                @if($data->isNotEmpty())
                    <input type="button" class="btn btn-primary" style="cursor:pointer" onclick="printDiv('printableArea')" value="Print" />
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function printDiv(divName) {
            var printContents = document.getElementById(divName).innerHTML;
            var originalContents = document.body.innerHTML;
           
            document.body.innerHTML = printContents;

            window.print();

            document.body.innerHTML = originalContents;
        }
    </script>
@endsection

