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
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('newdesign/assets/vendor/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('newdesign/assets/vendor/datatables/js/data-table.js') }}"></script>
    <!-- Slimscroll -->
    <script src="{{ asset('newdesign/assets/vendor/slimscroll/jquery.slimscroll.js') }}"></script>
    <!-- Main JS -->
    <script src="{{ asset('newdesign/assets/libs/js/main-js.js') }}"></script>

    @stack('scripts')
</body>
</html>
