<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Survey') - {{ config('app.name') }}</title>

    <!-- Google Fonts - Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('newdesign/assets/vendor/bootstrap/css/bootstrap.min.css') }}">
    <!-- FontAwesome CSS -->
    <link rel="stylesheet" href="{{ asset('newdesign/assets/vendor/fonts/fontawesome/css/fontawesome-all.css') }}">
    <!-- FontAwesome CDN Fallback -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="{{ asset('newdesign/assets/vendor/datatables/css/dataTables.bootstrap4.css') }}">
    <!-- Theme CSS -->
    <link rel="stylesheet" href="{{ asset('newdesign/assets/libs/css/style.css') }}">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom/main.css') }}">
    <!-- Survey Theme CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom/survey-theme.css') }}">

    <link href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css" rel="stylesheet">

    @stack('styles')
</head>

<body class="survey-page">
    <div class="survey-layout">
        <!-- Survey Header -->
        @include('layouts.survey-mock.partials.header')

        <!-- Sidebar Backdrop -->
        <div class="survey-sidebar-backdrop" id="survey-sidebar-backdrop"></div>

        <!-- Survey Sidebar -->
        @include('layouts.survey-mock.partials.sidebar')

        <!-- Main Content -->
        <div class="survey-main-content" id="survey-main-content">
            <div class="survey-content-wrapper">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>

        <!-- Sidebar Open Button -->
        <button type="button" class="survey-sidebar-open-btn" id="survey-sidebar-open" aria-label="Show sidebar">
            <i class="fa fa-chevron-right"></i>
        </button>
    </div>

    <!-- jQuery -->
    <script src="{{ asset('newdesign/assets/vendor/jquery/jquery-3.3.1.min.js') }}"></script>
    <!-- Bootstrap Bundle -->
    <script src="{{ asset('newdesign/assets/vendor/bootstrap/js/bootstrap.bundle.js') }}"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('newdesign/assets/vendor/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('newdesign/assets/vendor/datatables/js/data-table.js') }}"></script>
    <!-- Slimscroll -->
    <script src="{{ asset('newdesign/assets/vendor/slimscroll/jquery.slimscroll.js') }}"></script>
    <!-- Main JS -->
    <script src="{{ asset('newdesign/assets/libs/js/main-js.js') }}"></script>
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>

    @stack('scripts')

    <script>
        $(document).ready(function() {
            // Sidebar collapse toggle - using jQuery for consistency
            var $sidebar = $('#survey-sidebar');
            var $mainContent = $('#survey-main-content');
            var $sidebarOpenBtn = $('#survey-sidebar-open');

            if ($sidebar.hasClass('collapsed') && $sidebarOpenBtn.length) {
                $sidebarOpenBtn.addClass('show');
            }

            var updateSidebarCollapseUI = function(isCollapsed) {
                var $btn = $('#survey-sidebar-collapse');
                var $icon = $btn.find('i');
                if (isCollapsed) {
                    $icon.removeClass('fa-chevron-left').addClass('fa-chevron-right');
                } else {
                    $icon.removeClass('fa-chevron-right').addClass('fa-chevron-left');
                }
            };

            // Use jQuery delegated event for sidebar collapse button
            $(document).on('click', '#survey-sidebar-collapse', function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                var isCollapsed = $sidebar.toggleClass('collapsed').hasClass('collapsed');
                $mainContent.toggleClass('sidebar-collapsed', isCollapsed);
                $sidebarOpenBtn.toggleClass('show', isCollapsed);
                updateSidebarCollapseUI(isCollapsed);
                return false;
            });

            // Sidebar open button
            $(document).on('click', '#survey-sidebar-open', function(e) {
                e.stopPropagation();
                if ($sidebar.hasClass('collapsed')) {
                    $sidebar.removeClass('collapsed');
                    $mainContent.removeClass('sidebar-collapsed');
                    $sidebarOpenBtn.removeClass('show');
                    updateSidebarCollapseUI(false);
                }
            });

            // Profile dropdown
            const profileBtn = document.getElementById('survey-profile-btn');
            const profileMenu = document.getElementById('survey-profile-menu');

            if (profileBtn && profileMenu) {
                profileBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    profileMenu.classList.toggle('show');
                });

                document.addEventListener('click', function(e) {
                    if (!profileBtn.contains(e.target) && !profileMenu.contains(e.target)) {
                        profileMenu.classList.remove('show');
                    }
                });
            }

            // Remove overlays
            function removeAllOverlays() {
                $('.modal-backdrop, .overlay, .backdrop, .dataTables_processing, div.dt-button-background')
            .remove();
                $('body').removeClass('modal-open');
                $('body, .survey-layout, .survey-main-content, .survey-content-wrapper').css({
                    'pointer-events': 'auto',
                    'overflow': 'auto'
                });
            }

            removeAllOverlays();
            setTimeout(removeAllOverlays, 100);
            setTimeout(removeAllOverlays, 500);
        });
    </script>
</body>

</html>
