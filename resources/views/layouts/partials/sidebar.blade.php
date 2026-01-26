<div class="survey-sidebar" id="survey-sidebar">
    <div class="survey-sidebar-content">
        <!-- Navigation -->
        <div class="survey-sidebar-section">
            <div class="survey-sidebar-title">MENU</div>
            <nav class="survey-nav">
                @if(auth()->user()->isSuperAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="survey-nav-item {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                        <span class="survey-nav-label">Dashboard</span>
                    </a>
                    <a href="{{ route('admin.surveys.index') }}" class="survey-nav-item {{ request()->is('admin/surveys*') ? 'active' : '' }}">
                        <span class="survey-nav-label">Survey Jobs</span>
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="survey-nav-item {{ request()->is('admin/users*') ? 'active' : '' }}">
                        <span class="survey-nav-label">User Management</span>
                    </a>
                    <a href="{{ route('admin.survey-builder.index') }}" class="survey-nav-item {{ request()->is('admin/survey-builder*') ? 'active' : '' }}">
                        <span class="survey-nav-label">Sections</span>
                    </a>
                    <a href="{{ route('admin.accommodation-builder.index') }}" class="survey-nav-item {{ request()->is('admin/accommodation-builder*') ? 'active' : '' }}">
                        <span class="survey-nav-label">Accommodations</span>
                    </a>
                    <a href="{{ route('admin.survey-options.index') }}" class="survey-nav-item {{ request()->is('admin/survey-options*') ? 'active' : '' }}">
                        <span class="survey-nav-label">Global Options</span>
                    </a>
                    <a href="{{ route('admin.survey-levels.index') }}" class="survey-nav-item {{ request()->is('admin/survey-levels*') ? 'active' : '' }}">
                        <span class="survey-nav-label">Survey Levels</span>
                    </a>
                    <a href="{{ route('admin.content-sections.index') }}" class="survey-nav-item {{ request()->is('admin/content-sections*') ? 'active' : '' }}">
                        <span class="survey-nav-label">Content Sections</span>
                    </a>
                    <a href="{{ route('admin.survey-section-assessments.index') }}" class="survey-nav-item {{ request()->is('admin/survey-section-assessments*') ? 'active' : '' }}">
                        <span class="survey-nav-label">Assessments</span>
                    </a>
                @elseif(auth()->user()->isSurveyor())
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
                @elseif(auth()->user()->isClient())
                    <a href="{{ route('client.dashboard') }}" class="survey-nav-item {{ request()->is('client/dashboard') ? 'active' : '' }}">
                        <span class="survey-nav-label">Dashboard</span>
                    </a>
                    <a href="{{ route('client.surveys.index') }}" class="survey-nav-item {{ request()->is('client/surveys*') ? 'active' : '' }}">
                        <span class="survey-nav-label">My Surveys</span>
                    </a>
                    <a href="javascript:void(0)" class="survey-nav-item survey-nav-disabled">
                        <span class="survey-nav-label">Downloads</span>
                    </a>
                @endif
                
                <a href="{{ route('logout') }}" class="survey-nav-item" onclick="event.preventDefault(); document.getElementById('survey-sidebar-logout-form').submit();">
                    <span class="survey-nav-label">Logout</span>
                </a>
                <form id="survey-sidebar-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </nav>
        </div>

        <!-- Filters Section (only show for surveyor) -->
        @if(auth()->user()->isSurveyor())
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
        @endif
    </div>

    <div class="survey-sidebar-footer">
        <button type="button" class="survey-sidebar-collapse" id="survey-sidebar-collapse">
            <i class="fas fa-chevron-left"></i>
            <span>Hide Sidebar</span>
        </button>
    </div>
</div>
