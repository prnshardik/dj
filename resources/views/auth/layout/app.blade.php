<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width initial-scale=1.0">
    @yield('meta')

    <title>{{ _site_title() }} | @yield('title')</title>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@200;400;700&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/css/fontawesome.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/paper-dashboard.min1036.css?v=2.1.1') }}" rel="stylesheet" />
    <link href="{{ asset('assets/demo/demo.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendors/DataTables/datatables.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendors/toastr/toastr.min.css') }}" rel="stylesheet" />

    <style>
        .full-page>.content{
            padding-top: 4vh;
        }
    </style>

    <link rel="icon" href="{{ asset('qr_logo.png') }}" type="image/gif" sizes="16x16">
</head>
<body class="login-page">
    <div class="wrapper wrapper-full-page ">
        <div class="full-page section-image" filter-color="black" data-image="{{ asset('bg_image.jpeg') }}">
            <div class="content">
                @yield('content')
            </div>
        </div>
    </div>
    
    <script src="{{ asset('assets/js/core/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/perfect-scrollbar.jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/bootstrap-switch.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/nouislider.min.js') }}"></script>
    <script src="{{ asset('assets/demo/demo.js') }}"></script>
    <script src="{{ asset('assets/vendors/toastr/toastr.min.js') }}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            demo.checkFullPageBackgroundImage();
        });
    </script>

    <script>
        @php
            $success = '';
            if(\Session::has('success'))
                $success = \Session::get('success');

            $error = '';
            if(\Session::has('error'))
                $error = \Session::get('error');
        @endphp

        var success = "{{ $success }}";
        var error = "{{ $error }}";

        if(success != ''){
            toastr.success(success, 'Success');
        }

        if(error != ''){
            toastr.error(error, 'error');
        }
    </script>
</body>
</html>