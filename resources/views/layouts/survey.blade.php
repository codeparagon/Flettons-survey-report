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
    <!-- Survey Theme CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom/survey-theme.css') }}">

    @stack('styles')
</head>
<body class="survey-page">
    <div class="survey-layout">
        <!-- Survey Header -->
        @include('layouts.survey.partials.header')

        <!-- Sidebar Backdrop -->
        <div class="survey-sidebar-backdrop" id="survey-sidebar-backdrop"></div>

        <!-- Survey Sidebar -->
        @include('layouts.survey.partials.sidebar')

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
    // Aggressively remove all overlays and enable interactions
    $(document).ready(function() {
        function removeAllOverlays() {
            // Remove all possible overlay elements (but NOT our modals)
            $('.modal-backdrop, .overlay, .backdrop, .dataTables_processing, div.dt-button-background')
                .not('.survey-data-mock-lightbox-backdrop')
                .remove();
            
            // Remove modal-open class only if our lightbox is not active
            if (!$('#survey-data-mock-lightbox').hasClass('active') && 
                !$('#surveyDataMockRatingModal').is(':visible') &&
                !$('#surveyDataMockCostModal').is(':visible')) {
                $('body').removeClass('modal-open');
            }
            
            // Find and remove any invisible full-page overlays by checking z-index
            $('body > *').each(function() {
                var $el = $(this);
                
                // Skip our legitimate modals
                if ($el.hasClass('survey-data-mock-lightbox') || 
                    $el.attr('id') === 'survey-data-mock-lightbox' ||
                    $el.attr('id') === 'surveyDataMockRatingModal' ||
                    $el.attr('id') === 'surveyDataMockCostModal') {
                    return;
                }
                
                var zIndex = parseInt($el.css('z-index')) || 0;
                var position = $el.css('position');
                
                // If element has high z-index and covers the page, remove it
                if ((position === 'fixed' || position === 'absolute') && zIndex > 100) {
                    var width = $el.outerWidth();
                    var height = $el.outerHeight();
                    var windowWidth = $(window).width();
                    var windowHeight = $(window).height();
                    
                    // If it covers most of the screen and is invisible/transparent, remove it
                    if (width > windowWidth * 0.8 && height > windowHeight * 0.8) {
                        var opacity = parseFloat($el.css('opacity')) || 1;
                        var bg = $el.css('background-color');
                        
                        if (opacity < 0.1 || bg === 'rgba(0, 0, 0, 0)' || bg === 'transparent') {
                            $el.remove();
                            console.log('Removed suspicious overlay:', $el[0]);
                        }
                    }
                }
            });
            
            // Force enable pointer events on body and main containers (only if no modal is open)
            if (!$('#survey-data-mock-lightbox').hasClass('active')) {
                $('body, .survey-layout, .survey-main-content, .survey-content-wrapper').css({
                    'pointer-events': 'auto',
                    'overflow': 'auto',
                    'padding-right': '0'
                });
            }
            
            // Remove any blur filters
            $('body, .survey-layout, .survey-main-content').css({
                'filter': 'none',
                '-webkit-filter': 'none',
                'backdrop-filter': 'none'
            });
        }
        
        // Remove overlays immediately
        removeAllOverlays();
        
        // Remove overlays after a short delay (in case they're added dynamically)
        setTimeout(removeAllOverlays, 100);
        setTimeout(removeAllOverlays, 500);
        setTimeout(removeAllOverlays, 1000);
        
        // Continuously monitor and remove overlays
        setInterval(function() {
            removeAllOverlays();
            
            // Also hide (don't remove) any processing overlays that appear
            $('.dataTables_processing, div.dt-button-background').css({
                'display': 'none !important',
                'visibility': 'hidden !important',
                'opacity': '0 !important',
                'pointer-events': 'none !important',
                'z-index': '-9999 !important'
            });
        }, 200);
    });
    
    // Re-enable context menu (right-click)
    $(document).on('contextmenu', function(e) {
        // Allow right-click everywhere
        return true;
    });
    
    // Prevent any scripts from disabling context menu
    $(document).off('contextmenu').on('contextmenu', function(e) {
        return true;
    });
    
    // Also remove on any dynamic content load
    $(document).ajaxComplete(function() {
        $('.modal-backdrop, .overlay, .backdrop, .dataTables_processing, div.dt-button-background').remove();
        $('body').removeClass('modal-open');
        $('body, .survey-layout').css('pointer-events', 'auto');
    });
    
    // Watch for DataTables initialization
    $(document).on('datatable:initialized', function() {
        setTimeout(function() {
            $('.dataTables_processing, div.dt-button-background').remove();
            $('body, .survey-layout').css('pointer-events', 'auto');
        }, 100);
    });
    
    // Allow inspect element (F12)
    $(document).on('keydown', function(e) {
        // Allow F12 and Ctrl+Shift+I
        if (e.key === 'F12' || (e.ctrlKey && e.shiftKey && e.key === 'I')) {
            return true;
        }
    });
    </script>

    @stack('body-end')
</body>
</html>

