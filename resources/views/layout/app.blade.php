<!DOCTYPE html>
<html lang="en">
<head>
    @include('layout.meta')
  
    <title>{{ _site_title() }} | @yield('title')</title>
    
    @include('layout.styles')

    <link rel="icon" href="{{ asset('qr_logo.png') }}" type="image/gif" sizes="16x16">
</head>

<body class="">
    <div class="wrapper ">
    
        @include('layout.sidebar')
    
        <div class="main-panel">
            @include('layout.header')

            <div class="content">
                @yield('content')
            </div>

            @include('layout.footer')
        </div>
    </div>

    @include('layout.theme-config')

    @include('layout.scripts')
</body>
</html>