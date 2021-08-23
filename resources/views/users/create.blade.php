@extends('layout.app')

@section('meta')
@endsection

@section('title')
    Create User
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
                    <h4 class="card-title">Create User</h4>
                </div>
                <div class="card-body ">
                    <form name="form" action="{{ route('users.insert') }}" id="form" method="post" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label for="name">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Plese enter name" value="{{ @old('name') }}" />
                                <span class="kt-form__help error name"></span>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="email">Email <span class="text-danger">*</span></label>
                                <input type="text" name="email" id="email" class="form-control" placeholder="Plese enter email address" value="{{ @old('email') }}" autocomplete="off" />
                                <span class="kt-form__help error email"></span>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="phone">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" name="phone" id="phone" class="form-control" placeholder="Plese enter phone number" value="{{ @old('phone') }}" autocomplete="off" />
                                <span class="kt-form__help error phone"></span>
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="password">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" id="password" class="form-control" placeholder="Plese enter password" autocomplete="off" />
                                <span class="kt-form__help error password"></span>
                            </div>
                            <div class="form-group col-sm-12">
                                <label for="image">Image</label>
                                <input type="file" class=" dropify" id="image" name="image"  data-allowed-file-extensions="jpg png jpeg" data-max-file-size-preview="5M" data-show-remove="false">
                                <span class="kt-form__help error image"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ route('users') }}" class="btn btn-default">Back</a>
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
            var drEvent = $('.dropify').dropify({
                messages: {
                    'default': 'Drag and drop profile image here or click',
                    'remove':  'Remove',
                    'error':   'Ooops, something wrong happended.'
                }
            });

            var dropifyElements = {};
            $('.dropify').each(function () {
                dropifyElements[this.id] = false;
            });

            drEvent.on('dropify.beforeClear', function(event, element){
                console.log('asd');
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
        $(document).ready(function() {
            $("#email").prop("autocomplete", "off");
            $("#email").val('');

            $("#password").prop("autocomplete", "off");
            $("#password").val('');

            $('#phone').keyup(function(e){
                if (/\D/g.test(this.value)){
                    this.value = this.value.replace(/\D/g, '');
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
@endsection

