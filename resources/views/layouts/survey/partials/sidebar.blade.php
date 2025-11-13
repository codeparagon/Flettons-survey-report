<div class="survey-sidebar" id="survey-sidebar">
    <div class="survey-sidebar-content">
        <!-- Navigation -->
        <div class="survey-sidebar-section">
            <div class="survey-sidebar-title">MENU</div>
            <nav class="survey-nav">
                <a href="{{ route('surveyor.surveys.index') }}" class="survey-nav-item {{ request()->is('surveyor/surveys*') ? 'active' : '' }}">
                    <span class="survey-nav-label">My Reports</span>
                </a>
                <a href="{{ route('surveyor.dashboard') }}" class="survey-nav-item {{ request()->is('surveyor/dashboard') ? 'active' : '' }}">
                    <span class="survey-nav-label">My Performance</span>
                </a>
                <a href="javascript:void(0)" class="survey-nav-item survey-nav-disabled">
                    <span class="survey-nav-label">Account</span>
                </a>
                <a href="javascript:void(0)" class="survey-nav-item survey-nav-disabled">
                    <span class="survey-nav-label">Settings</span>
                </a>
                <a href="{{ route('logout') }}" class="survey-nav-item" onclick="event.preventDefault(); document.getElementById('survey-sidebar-logout-form').submit();">
                    <span class="survey-nav-label">Logout</span>
                </a>
                <form id="survey-sidebar-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </nav>
        </div>

        <!-- Filters Section -->
        <div class="survey-sidebar-section survey-filters-section">
            <button class="survey-filters-toggle" id="survey-filters-toggle" type="button" aria-expanded="false">
                <span class="survey-filters-toggle-icon">
                    <i class="fas fa-filter"></i>
                </span>
                <span class="survey-filters-toggle-label">Filters</span>
                <i class="fas fa-chevron-down survey-filters-toggle-caret"></i>
            </button>
            <div class="survey-filters-container" id="survey-filters-container">
                <div class="survey-filters">
                    @yield('filters')
                </div>
                <div class="survey-filters-actions">
                    <button type="button" class="survey-filter-reset" id="filter-reset">
                        <i class="fas fa-undo"></i>
                        <span>Reset</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="survey-sidebar-footer">
        <button type="button" class="survey-sidebar-collapse" id="survey-sidebar-collapse">
            <i class="fas fa-chevron-left"></i>
            <span>Hide Sidebar</span>
        </button>
    </div>
</div>


