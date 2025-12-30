<div class="nav-left-sidebar sidebar-dark">
    <div class="menu-list">
        <nav class="navbar navbar-expand-lg navbar-light">
            <a class="d-xl-none d-lg-none" href="#">Dashboard</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav flex-column">
                    <li class="nav-divider">Menu</li>

                    @if(auth()->user()->isSuperAdmin())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                <i class="fa fa-fw fa-home"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/surveys*') ? 'active' : '' }}" href="{{ route('admin.surveys.index') }}">
                                <i class="fa fa-fw fa-clipboard-list"></i>Survey Jobs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/users*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                                <i class="fa fa-fw fa-users"></i>User Management
                            </a>
                        </li>
                        
                        <li class="nav-divider">Survey Configuration</li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/survey-builder*') ? 'active' : '' }}" href="{{ route('admin.survey-builder.index') }}">
                                <i class="fa fa-fw fa-magic"></i>Sections
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/accommodation-builder*') ? 'active' : '' }}" href="{{ route('admin.accommodation-builder.index') }}">
                                <i class="fa fa-fw fa-bed"></i>Accommodations
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/survey-options*') ? 'active' : '' }}" href="{{ route('admin.survey-options.index') }}">
                                <i class="fa fa-fw fa-sliders-h"></i>Global Options
                            </a>
                        </li> 
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/survey-levels*') ? 'active' : '' }}" href="{{ route('admin.survey-levels.index') }}">
                                <i class="fa fa-fw fa-sitemap""></i>Survey Levels
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/content-sections*') ? 'active' : '' }}" href="{{ route('admin.content-sections.index') }}">
                                <i class="fa fa-fw fa-file-alt"></i>Content Sections
                            </a>
                        </li>
                        
                        <li class="nav-divider">Data Management</li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/survey-section-assessments*') ? 'active' : '' }}" href="{{ route('admin.survey-section-assessments.index') }}">
                                <i class="fa fa-fw fa-clipboard-check"></i>Assessments
                            </a>
                        </li>
                        
                        <!-- <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fa fa-fw fa-cog"></i>Settings
                            </a>
                        </li> -->
                    @elseif(auth()->user()->isSurveyor())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('surveyor/dashboard') ? 'active' : '' }}" href="{{ route('surveyor.dashboard') }}">
                                <i class="fa fa-fw fa-home"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('surveyor/surveys*') ? 'active' : '' }}" href="{{ route('surveyor.surveys.index') }}">
                                <i class="fa fa-fw fa-clipboard-list"></i>My Survey Jobs
                            </a>
                        </li>
                        <!-- <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fa fa-fw fa-chart-bar"></i>Reports
                            </a>
                        </li> -->
                    @elseif(auth()->user()->isClient())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('client/dashboard') ? 'active' : '' }}" href="{{ route('client.dashboard') }}">
                                <i class="fa fa-fw fa-home"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('client/surveys*') ? 'active' : '' }}" href="{{ route('client.surveys.index') }}">
                                <i class="fa fa-fw fa-clipboard-list"></i>My Surveys
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fa fa-fw fa-download"></i>Downloads
                            </a>
                        </li>
                    @endif

                    <!-- <li class="nav-divider">Account</li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fa fa-fw fa-user-circle"></i>Profile</a>
                    </li> -->
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fa fa-fw fa-sign-out-alt"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</div>
