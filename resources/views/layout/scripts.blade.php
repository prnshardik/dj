<script src="{{ asset('assets/js/core/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/perfect-scrollbar.jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bootstrap-switch.js') }}"></script>
<script src="{{ asset('assets/js/plugins/jquery.validate.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bootstrap-tagsinput.js') }}"></script>
<script src="{{ asset('assets/js/plugins/jasny-bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/nouislider.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bootstrap-notify.js') }}"></script>
<script src="{{ asset('assets/js/paper-dashboard.min1036.js?v=2.1.1') }}" type="text/javascript"></script>
<script src="{{ asset('assets/demo/demo.js') }}"></script>
<script src="{{ asset('assets/demo/jquery.sharrre.js') }}"></script>
<script src="{{ asset('assets/vendors/toastr/toastr.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/vendors/DataTables/datatables.min.js') }}"></script>

<script> 
    $(document).ready(function() {
        $sidebar = $('.sidebar');
        $sidebar_img_container = $sidebar.find('.sidebar-background');

        $full_page = $('.full-page');

        $sidebar_responsive = $('body > .navbar-collapse');
        sidebar_mini_active = false;

        window_width = $(window).width();

        fixed_plugin_open = $('.sidebar .sidebar-wrapper .nav li.active a p').html();

        $('.fixed-plugin a').click(function(event) {
            if ($(this).hasClass('switch-trigger')) {
                if (event.stopPropagation) {
                    event.stopPropagation();
                } else if (window.event) {
                    window.event.cancelBubble = true;
                }
            }
        });

        $('.fixed-plugin .active-color span').click(function() {
            $full_page_background = $('.full-page-background');

            $(this).siblings().removeClass('active');
            $(this).addClass('active');

            var new_color = $(this).data('color');

            if ($sidebar.length != 0) {
                $sidebar.attr('data-active-color', new_color);
            }

            if ($full_page.length != 0) {
                $full_page.attr('data-active-color', new_color);
            }

            if ($sidebar_responsive.length != 0) {
                $sidebar_responsive.attr('data-active-color', new_color);
            }
        });

        $('.fixed-plugin .background-color span').click(function() {
            $(this).siblings().removeClass('active');
            $(this).addClass('active');

            var new_color = $(this).data('color');

            if ($sidebar.length != 0) {
                $sidebar.attr('data-color', new_color);
            }

            if ($full_page.length != 0) {
                $full_page.attr('filter-color', new_color);
            }

            if ($sidebar_responsive.length != 0) {
                $sidebar_responsive.attr('data-color', new_color);
            }
        });

        $('.fixed-plugin .img-holder').click(function() {
            $full_page_background = $('.full-page-background');

            $(this).parent('li').siblings().removeClass('active');
            $(this).parent('li').addClass('active');

            var new_image = $(this).find("img").attr('src');

            if ($sidebar_img_container.length != 0 && $('.switch-sidebar-image input:checked').length != 0) {
                $sidebar_img_container.fadeOut('fast', function() {
                    $sidebar_img_container.css('background-image', 'url("' + new_image + '")');
                    $sidebar_img_container.fadeIn('fast');
                });
            }

            if ($full_page_background.length != 0 && $('.switch-sidebar-image input:checked').length != 0) {
                var new_image_full_page = $('.fixed-plugin li.active .img-holder').find('img').data('src');

                $full_page_background.fadeOut('fast', function() {
                    $full_page_background.css('background-image', 'url("' + new_image_full_page + '")');
                    $full_page_background.fadeIn('fast');
                });
            }

            if ($('.switch-sidebar-image input:checked').length == 0) {
                var new_image = $('.fixed-plugin li.active .img-holder').find("img").attr('src');
                var new_image_full_page = $('.fixed-plugin li.active .img-holder').find('img').data('src');

                $sidebar_img_container.css('background-image', 'url("' + new_image + '")');
                $full_page_background.css('background-image', 'url("' + new_image_full_page + '")');
            }

            if ($sidebar_responsive.length != 0) {
                $sidebar_responsive.css('background-image', 'url("' + new_image + '")');
            }
        });

        $('.switch-sidebar-image input').on("switchChange.bootstrapSwitch", function() {
            $full_page_background = $('.full-page-background');

            $input = $(this);

            if ($input.is(':checked')) {
                if ($sidebar_img_container.length != 0) {
                    $sidebar_img_container.fadeIn('fast');
                    $sidebar.attr('data-image', '#');
                }

                if ($full_page_background.length != 0) {
                    $full_page_background.fadeIn('fast');
                    $full_page.attr('data-image', '#');
                }

                background_image = true;
            } else {
                if ($sidebar_img_container.length != 0) {
                    $sidebar.removeAttr('data-image');
                    $sidebar_img_container.fadeOut('fast');
                }

                if ($full_page_background.length != 0) {
                    $full_page.removeAttr('data-image', '#');
                    $full_page_background.fadeOut('fast');
                }

                background_image = false;
            }
        });

        $('.switch-mini input').on("switchChange.bootstrapSwitch", function() {
            $body = $('body');

            $input = $(this);

            if (paperDashboard.misc.sidebar_mini_active == true) {
                $('body').removeClass('sidebar-mini');
                paperDashboard.misc.sidebar_mini_active = false;
            } else {
                $('body').addClass('sidebar-mini');
                paperDashboard.misc.sidebar_mini_active = true;
            }

            var simulateWindowResize = setInterval(function() {
                window.dispatchEvent(new Event('resize'));
            }, 180);

            setTimeout(function() {
                clearInterval(simulateWindowResize);
            }, 1000);
        });
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
@yield('scripts')