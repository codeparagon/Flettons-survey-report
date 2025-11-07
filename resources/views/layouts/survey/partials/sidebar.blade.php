<div class="survey-sidebar">
    <div class="survey-sidebar-content">
        <!-- Navigation -->
        <div class="survey-sidebar-section">
            <div class="survey-sidebar-title">MENU</div>
            <nav class="survey-nav">
                <a href="{{ route('surveyor.dashboard') }}" class="survey-nav-item {{ request()->is('surveyor/dashboard') ? 'active' : '' }}">
                    Dashboard
                </a>
                <a href="{{ route('surveyor.surveys.index') }}" class="survey-nav-item {{ request()->is('surveyor/surveys*') ? 'active' : '' }}">
                    My Survey Jobs
                </a>
                <a href="{{ route('logout') }}" class="survey-nav-item" onclick="event.preventDefault(); document.getElementById('survey-sidebar-logout-form').submit();">
                    Logout
                </a>
                <form id="survey-sidebar-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </nav>
        </div>

        <!-- Filters Section -->
        <div class="survey-sidebar-section survey-filters-section" id="survey-filters-section">
            <div class="survey-sidebar-title">FILTERS</div>
            <div class="survey-filters">
                @yield('filters')
            </div>
        </div>
    </div>
</div>


