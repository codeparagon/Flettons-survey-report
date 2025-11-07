<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Survey') - {{ config('app.name') }}</title>

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
    <!-- Survey Detail Theme CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom/survey-detail-theme.css') }}">

    @stack('styles')
</head>
<body class="survey-detail-page">
    @php
        // Get survey from view data if available
        $survey = isset($survey) ? $survey : (get_defined_vars()['survey'] ?? null);
    @endphp
    <div class="survey-detail-layout">
        <!-- Main Top Header with Branding -->
        <div class="survey-detail-top-header">
            <div class="survey-detail-top-header-content">
                <div class="survey-detail-brand">
                    <span class="survey-detail-brand-icon">★</span>
                    <span class="survey-detail-brand-text">SurvAI™</span>
                </div>
                <div class="survey-detail-top-header-actions">
                    <button type="button" class="survey-detail-action-btn" title="New Document">
                        <i class="fas fa-file-plus"></i>
                    </button>
                    <button type="button" class="survey-detail-action-btn" title="Add">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Survey Detail Header -->
        @include('layouts.survey-detail.partials.header', ['survey' => $survey ?? null])

        <!-- Survey Detail Sidebar -->
        @include('layouts.survey-detail.partials.sidebar', ['survey' => $survey ?? null])

        <!-- Main Content -->
        <div class="survey-detail-main-content">
            <div class="survey-detail-content-wrapper">
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

                <div class="survey-detail-content-inner">
                    @yield('content')
                    @stack('footer')
                </div>
            </div>
        </div>
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
    // Mobile app-like interactions
    $(document).ready(function() {
        // Sidebar toggle
        const sidebarToggle = document.getElementById('survey-detail-sidebar-toggle');
        const sidebarClose = document.getElementById('survey-detail-sidebar-close');
        const sidebar = document.querySelector('.survey-detail-sidebar');
        const sidebarBackdrop = document.getElementById('survey-detail-sidebar-backdrop');
        
        function openSidebar() {
            if (sidebar) sidebar.classList.add('show');
            if (sidebarBackdrop) sidebarBackdrop.classList.add('show');
            if (sidebarToggle) sidebarToggle.classList.add('active');
        }
        
        function closeSidebar() {
            if (sidebar) sidebar.classList.remove('show');
            if (sidebarBackdrop) sidebarBackdrop.classList.remove('show');
            if (sidebarToggle) sidebarToggle.classList.remove('active');
        }
        
        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                if (sidebar.classList.contains('show')) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            });
        }
        
        if (sidebarClose) {
            sidebarClose.addEventListener('click', function(e) {
                e.stopPropagation();
                closeSidebar();
            });
        }
        
        if (sidebarBackdrop) {
            sidebarBackdrop.addEventListener('click', function() {
                closeSidebar();
            });
        }
        
        // Close sidebar on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && sidebar && sidebar.classList.contains('show')) {
                closeSidebar();
            }
        });
        
        // Remove any overlays
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open');
    });
    </script>
</body>
</html>

