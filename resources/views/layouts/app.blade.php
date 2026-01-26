<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ config('app.name') }}</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('newdesign/assets/vendor/bootstrap/css/bootstrap.min.css') }}">
    <!-- FontAwesome CSS -->
    <link rel="stylesheet" href="{{ asset('newdesign/assets/vendor/fonts/fontawesome/css/fontawesome-all.css') }}">
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
        @include('layouts.partials.header')

        <!-- Sidebar Backdrop -->
        <div class="survey-sidebar-backdrop" id="survey-sidebar-backdrop"></div>

        <!-- Survey Sidebar -->
        @include('layouts.partials.sidebar')

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
    // Sidebar toggle functionality
    $(document).ready(function() {
        const sidebar = $('#survey-sidebar');
        const sidebarBackdrop = $('#survey-sidebar-backdrop');
        const sidebarOpenBtn = $('#survey-sidebar-open');
        const sidebarCollapseBtn = $('#survey-sidebar-collapse');
        const mainContent = $('#survey-main-content');

        // Function to update sidebar state
        function updateSidebarState(isCollapsed) {
            if (isCollapsed) {
                sidebar.addClass('collapsed');
                mainContent.addClass('sidebar-collapsed');
                sidebarOpenBtn.addClass('show');
                if (window.innerWidth < 769) {
                    sidebarBackdrop.removeClass('show');
                }
            } else {
                sidebar.removeClass('collapsed');
                mainContent.removeClass('sidebar-collapsed');
                sidebarOpenBtn.removeClass('show');
                if (window.innerWidth < 769) {
                    sidebarBackdrop.addClass('show');
                }
            }
        }

        // Toggle sidebar collapse
        if (sidebarCollapseBtn.length) {
            sidebarCollapseBtn.on('click', function() {
                const isCollapsed = !sidebar.hasClass('collapsed');
                updateSidebarState(isCollapsed);
            });
        }

        // Open sidebar button
        if (sidebarOpenBtn.length) {
            sidebarOpenBtn.on('click', function() {
                updateSidebarState(false);
            });
        }

        // Close sidebar on backdrop click
        if (sidebarBackdrop.length) {
            sidebarBackdrop.on('click', function() {
                updateSidebarState(true);
            });
        }

        // Handle window resize
        $(window).on('resize', function() {
            if (window.innerWidth >= 769) {
                sidebarBackdrop.removeClass('show');
            } else if (!sidebar.hasClass('collapsed')) {
                sidebarBackdrop.addClass('show');
            }
        });

        // Profile dropdown toggle
        const profileBtn = $('#survey-profile-btn');
        const profileMenu = $('#survey-profile-menu');
        
        if (profileBtn.length && profileMenu.length) {
            profileBtn.on('click', function(e) {
                e.stopPropagation();
                profileMenu.toggleClass('show');
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('.survey-profile-dropdown').length) {
                    profileMenu.removeClass('show');
                }
            });
        }

        // Search functionality
        const searchInput = $('#survey-header-search');
        const searchClear = $('#survey-search-clear');
        
        if (searchInput.length && searchClear.length) {
            searchInput.on('input', function() {
                if ($(this).val().length > 0) {
                    searchClear.show();
                } else {
                    searchClear.hide();
                }
            });

            searchClear.on('click', function() {
                searchInput.val('');
                $(this).hide();
            });
        }

        // Filters toggle (for surveyor)
        const filtersToggle = $('#survey-filters-toggle');
        const filtersContainer = $('#survey-filters-container');
        
        if (filtersToggle.length && filtersContainer.length) {
            filtersToggle.on('click', function() {
                const isExpanded = filtersContainer.hasClass('open');
                filtersContainer.toggleClass('open');
                filtersToggle.attr('aria-expanded', !isExpanded);
            });
        }
    });
    </script>

    @stack('body-end')
</body>
</html>
