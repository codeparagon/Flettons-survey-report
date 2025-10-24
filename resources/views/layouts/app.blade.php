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

    <style>
        /* Ensure footer sticks to bottom */
        .dashboard-main-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .dashboard-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .dashboard-content {
            flex: 1;
        }
        
        .footer {
            margin-top: auto;
        }

        /* Revert sidebar to original colors */
        .sidebar-dark {
            background-color: #1a202c !important;
        }

        /* Table styling */
        .table {
            background-color: #ffffff !important;
            color: #1a202c !important;
        }

        .table thead th {
            background-color: #1a202c !important;
            color: #c1ec4a !important;
            border-color: #1a202c !important;
        }

        .table tbody tr {
            border-color: #e5e7eb !important;
        }

        .table tbody tr:hover {
            background-color: #f9fafb !important;
        }

        /* SurvAI Button Branding - Global Styles */
        .btn-primary,
        a.btn-primary,
        button.btn-primary {
            background-color: #C1EC4A !important;
            border-color: #C1EC4A !important;
            color: #1A202C !important;
            font-weight: 600 !important;
            padding: 12px 20px !important;
            border-radius: 6px !important;
            display: inline-block !important;
            text-decoration: none !important;
            box-shadow: none !important;
        }

        .btn-primary:hover,
        a.btn-primary:hover,
        button.btn-primary:hover {
            background-color: #B0D93F !important;
            border-color: #B0D93F !important;
            color: #1A202C !important;
            text-decoration: none !important;
            box-shadow: none !important;
        }

        .btn-outline-primary,
        a.btn-outline-primary,
        button.btn-outline-primary {
            background-color: #1A202C !important;
            border-color: #1A202C !important;
            color: #C1EC4A !important;
            font-weight: 600 !important;
            padding: 12px 20px !important;
            border-radius: 6px !important;
            display: inline-block !important;
            text-decoration: none !important;
            box-shadow: none !important;
        }

        .btn-outline-primary:hover,
        a.btn-outline-primary:hover,
        button.btn-outline-primary:hover {
            background-color: #2D3748 !important;
            border-color: #2D3748 !important;
            color: #C1EC4A !important;
            text-decoration: none !important;
            box-shadow: none !important;
        }

        .btn-outline-secondary,
        a.btn-outline-secondary,
        button.btn-outline-secondary {
            background-color: #1A202C !important;
            border-color: #1A202C !important;
            color: #C1EC4A !important;
            font-weight: 600 !important;
            padding: 12px 20px !important;
            border-radius: 6px !important;
            display: inline-block !important;
            text-decoration: none !important;
            box-shadow: none !important;
        }

        .btn-outline-secondary:hover,
        a.btn-outline-secondary:hover,
        button.btn-outline-secondary:hover {
            background-color: #2D3748 !important;
            border-color: #2D3748 !important;
            color: #C1EC4A !important;
            text-decoration: none !important;
            box-shadow: none !important;
        }

        .btn-success,
        a.btn-success,
        button.btn-success {
            background-color: #1A202C !important;
            border-color: #1A202C !important;
            color: #C1EC4A !important;
            font-weight: 600 !important;
            padding: 12px 20px !important;
            border-radius: 6px !important;
            display: inline-block !important;
            text-decoration: none !important;
            box-shadow: none !important;
        }

        .btn-success:hover,
        a.btn-success:hover,
        button.btn-success:hover {
            background-color: #2D3748 !important;
            border-color: #2D3748 !important;
            color: #C1EC4A !important;
            text-decoration: none !important;
            box-shadow: none !important;
        }

        .btn-warning,
        a.btn-warning,
        button.btn-warning {
            background-color: #1A202C !important;
            border-color: #1A202C !important;
            color: #C1EC4A !important;
            font-weight: 600 !important;
            padding: 12px 20px !important;
            border-radius: 6px !important;
            display: inline-block !important;
            text-decoration: none !important;
            box-shadow: none !important;
        }

        .btn-warning:hover,
        a.btn-warning:hover,
        button.btn-warning:hover {
            background-color: #2D3748 !important;
            border-color: #2D3748 !important;
            color: #C1EC4A !important;
            text-decoration: none !important;
            box-shadow: none !important;
        }

        .btn-info,
        a.btn-info,
        button.btn-info {
            background-color: #C1EC4A !important;
            border-color: #C1EC4A !important;
            color: #1A202C !important;
            font-weight: 600 !important;
            padding: 12px 20px !important;
            border-radius: 6px !important;
            display: inline-block !important;
            text-decoration: none !important;
            box-shadow: none !important;
        }

        .btn-info:hover,
        a.btn-info:hover,
        button.btn-info:hover {
            background-color: #B0D93F !important;
            border-color: #B0D93F !important;
            color: #1A202C !important;
            text-decoration: none !important;
            box-shadow: none !important;
        }

        .btn-secondary,
        a.btn-secondary,
        button.btn-secondary {
            background-color: #1A202C !important;
            border-color: #1A202C !important;
            color: #C1EC4A !important;
            font-weight: 600 !important;
            padding: 12px 20px !important;
            border-radius: 6px !important;
            display: inline-block !important;
            text-decoration: none !important;
            box-shadow: none !important;
        }

        .btn-secondary:hover,
        a.btn-secondary:hover,
        button.btn-secondary:hover {
            background-color: #2D3748 !important;
            border-color: #2D3748 !important;
            color: #C1EC4A !important;
            text-decoration: none !important;
            box-shadow: none !important;
        }

        .btn-danger,
        a.btn-danger,
        button.btn-danger {
            background-color: #1A202C !important;
            border-color: #1A202C !important;
            color: #C1EC4A !important;
            font-weight: 600 !important;
            padding: 12px 20px !important;
            border-radius: 6px !important;
            display: inline-block !important;
            text-decoration: none !important;
            box-shadow: none !important;
        }

        .btn-danger:hover,
        a.btn-danger:hover,
        button.btn-danger:hover {
            background-color: #2D3748 !important;
            border-color: #2D3748 !important;
            color: #C1EC4A !important;
            text-decoration: none !important;
            box-shadow: none !important;
        }

        .btn-light,
        a.btn-light,
        button.btn-light {
            background-color: #1A202C !important;
            border-color: #1A202C !important;
            color: #C1EC4A !important;
            font-weight: 600 !important;
            padding: 12px 20px !important;
            border-radius: 6px !important;
            display: inline-block !important;
            text-decoration: none !important;
            box-shadow: none !important;
        }

        .btn-light:hover,
        a.btn-light:hover,
        button.btn-light:hover {
            background-color: #2D3748 !important;
            border-color: #2D3748 !important;
            color: #C1EC4A !important;
            text-decoration: none !important;
            box-shadow: none !important;
        }

        /* Small button styling */
        .btn-sm {
            padding: 8px 16px !important;
            font-size: 14px !important;
        }

        /* SurvAI Badge Branding - Global Styles */
        .badge-info,
        span.badge-info {
            background-color: #C1EC4A !important;
            color: #1A202C !important;
            font-weight: 600 !important;
            padding: 6px 12px !important;
            border-radius: 4px !important;
        }

        .badge-success,
        span.badge-success {
            background-color: #C1EC4A !important;
            color: #1A202C !important;
            font-weight: 600 !important;
            padding: 6px 12px !important;
            border-radius: 4px !important;
        }

        .badge-warning,
        span.badge-warning {
            background-color: #1A202C !important;
            color: #C1EC4A !important;
            font-weight: 600 !important;
            padding: 6px 12px !important;
            border-radius: 4px !important;
        }

        .badge-danger,
        span.badge-danger {
            background-color: #1A202C !important;
            color: #C1EC4A !important;
            font-weight: 600 !important;
            padding: 6px 12px !important;
            border-radius: 4px !important;
        }

        .badge-secondary,
        span.badge-secondary {
            background-color: #1A202C !important;
            color: #C1EC4A !important;
            font-weight: 600 !important;
            padding: 6px 12px !important;
            border-radius: 4px !important;
        }

        /* Card styling */
        .card {
            background-color: #ffffff !important;
            border-color: #e2e8f0 !important;
        }

        .card-header {
            background-color: #1a202c !important;
            border-color: #1a202c !important;
            color: #c1ec4a !important;
        }

        .card-body {
            color: #1a202c !important;
        }

        /* Form styling */
        .form-control {
            background-color: #ffffff !important;
            border-color: #d1d5db !important;
            color: #1a202c !important;
        }

        .form-control:focus {
            background-color: #ffffff !important;
            border-color: #00d4aa !important;
            color: #1a202c !important;
            box-shadow: 0 0 0 0.2rem rgba(0, 212, 170, 0.25) !important;
        }

        .form-label {
            color: #1a202c !important;
        }

        /* Alert styling */
        .alert-success {
            background-color: rgba(0, 212, 170, 0.1) !important;
            border-color: #00d4aa !important;
            color: #00d4aa !important;
        }

        .alert-danger {
            background-color: rgba(239, 68, 68, 0.1) !important;
            border-color: #ef4444 !important;
            color: #ef4444 !important;
        }

        /* Footer styling */
        .footer {
            background-color: #1a202c !important;
            color: #a0aec0 !important;
        }

        .footer a {
            color: #e2e8f0 !important;
        }

        .footer a:hover {
            color: #00d4aa !important;
        }

        /* Main content background - white */
        .dashboard-content {
            background-color: #ffffff !important;
        }

        /* Headings */
        h1, h2, h3, h4, h5, h6 {
            color: #1a202c !important;
        }

        /* Special heading colors for dark backgrounds */
        .card-header h1, .card-header h2, .card-header h3, 
        .card-header h4, .card-header h5, .card-header h6 {
            color: #c1ec4a !important;
        }

        .table thead th {
            color: #c1ec4a !important;
        }

        /* Text colors */
        body {
            background-color: #f8fafc !important;
            color: #1a202c !important;
        }
    </style>

    @stack('styles')
</head>

<body>
    <div class="dashboard-main-wrapper">
        <!-- Navbar -->
        @include('layouts.partials.navbar')

        <!-- Sidebar -->
        @include('layouts.partials.sidebar')

        <!-- Main Content -->
        <div class="dashboard-wrapper">
            <div class="container-fluid dashboard-content">
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

            <!-- Footer -->
            @include('layouts.partials.footer')
        </div>
    </div>

    <!-- jQuery -->
    <script src="{{ asset('newdesign/assets/vendor/jquery/jquery-3.3.1.min.js') }}"></script>
    <!-- Bootstrap Bundle -->
    <script src="{{ asset('newdesign/assets/vendor/bootstrap/js/bootstrap.bundle.js') }}"></script>
    <!-- Slimscroll -->
    <script src="{{ asset('newdesign/assets/vendor/slimscroll/jquery.slimscroll.js') }}"></script>
    <!-- Main JS -->
    <script src="{{ asset('newdesign/assets/libs/js/main-js.js') }}"></script>

    @stack('scripts')
</body>
</html>

