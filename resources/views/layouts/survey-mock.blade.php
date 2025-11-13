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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('newdesign/assets/vendor/bootstrap/css/bootstrap.min.css') }}">
    <!-- FontAwesome CSS -->
    <link rel="stylesheet" href="{{ asset('newdesign/assets/vendor/fonts/fontawesome/css/fontawesome-all.css') }}">
    <!-- FontAwesome CDN Fallback -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="{{ asset('newdesign/assets/vendor/datatables/css/dataTables.bootstrap4.css') }}">
    <!-- Theme CSS -->
    <link rel="stylesheet" href="{{ asset('newdesign/assets/libs/css/style.css') }}">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom/main.css') }}">
    <!-- Survey Theme CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom/survey-theme.css') }}">

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
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if(session('error'))
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

    @stack('scripts')
    
    <script>
    $(document).ready(function() {
        // Sidebar collapse toggle
        const sidebarCollapseBtn = document.getElementById('survey-sidebar-collapse');
        const sidebar = document.getElementById('survey-sidebar');
        const mainContent = document.getElementById('survey-main-content');
        const sidebarOpenBtn = document.getElementById('survey-sidebar-open');

        if (sidebar && sidebar.classList.contains('collapsed') && sidebarOpenBtn) {
            sidebarOpenBtn.classList.add('show');
        }

        const updateSidebarCollapseUI = (isCollapsed) => {
            if (!sidebarCollapseBtn) return;
            const icon = sidebarCollapseBtn.querySelector('i');
            const label = sidebarCollapseBtn.querySelector('span');
            if (isCollapsed) {
                if (icon) {
                    icon.classList.remove('fa-chevron-left');
                    icon.classList.add('fa-chevron-right');
                }
                if (label) label.textContent = 'Show Sidebar';
            } else {
                if (icon) {
                    icon.classList.remove('fa-chevron-right');
                    icon.classList.add('fa-chevron-left');
                }
                if (label) label.textContent = 'Hide';
            }
        };

        if (sidebarCollapseBtn && sidebar && mainContent) {
            sidebarCollapseBtn.addEventListener('click', function() {
                const isCollapsed = sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('sidebar-collapsed', isCollapsed);
                if (sidebarOpenBtn) {
                    sidebarOpenBtn.classList.toggle('show', isCollapsed);
                }
                updateSidebarCollapseUI(isCollapsed);
            });
        }

        if (sidebarOpenBtn && sidebar && mainContent) {
            sidebarOpenBtn.addEventListener('click', function() {
                if (sidebar.classList.contains('collapsed')) {
                    sidebar.classList.remove('collapsed');
                    mainContent.classList.remove('sidebar-collapsed');
                    sidebarOpenBtn.classList.remove('show');
                    updateSidebarCollapseUI(false);
                }
            });
        }

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
            $('.modal-backdrop, .overlay, .backdrop, .dataTables_processing, div.dt-button-background').remove();
            $('body').removeClass('modal-open');
            $('body, .survey-layout, .survey-main-content, .survey-content-wrapper').css({
                'pointer-events': 'auto',
                'overflow': 'auto',
                'padding-right': '0'
            });
        }

        removeAllOverlays();
        setTimeout(removeAllOverlays, 100);
        setTimeout(removeAllOverlays, 500);
    });
    </script>
</body>
</html>

