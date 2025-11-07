<div class="survey-header">
    <div class="survey-header-content">
        <div class="survey-header-left">
            <a href="{{ route('surveyor.dashboard') }}" class="survey-logo">
                <span class="survey-logo-icon">★</span>
                <span class="survey-logo-text">SURVAI™</span>
            </a>
        </div>
        <div class="survey-header-right">
            <div class="survey-search-wrapper">
                <div class="survey-search-box">
                    <i class="fas fa-search survey-search-icon"></i>
                    <input type="text" id="survey-header-search" class="survey-search-input" placeholder="Search surveys...">
                    <button type="button" class="survey-search-clear" id="survey-search-clear" style="display: none;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="survey-header-actions">
                <button type="button" class="survey-filter-toggle" id="survey-filter-toggle" title="Filters">
                    <i class="fas fa-filter"></i>
                </button>
                <div class="survey-profile-dropdown">
                    <button type="button" class="survey-profile-btn" id="survey-profile-btn">
                        <img src="{{ asset('newdesign/assets/images/avatar-1.jpg') }}" alt="{{ auth()->user()->name }}" class="survey-profile-img">
                    </button>
                    <div class="survey-profile-menu" id="survey-profile-menu">
                        <div class="survey-profile-info">
                            <div class="survey-profile-name">{{ auth()->user()->name }}</div>
                            <div class="survey-profile-role">{{ auth()->user()->role->display_name ?? 'Surveyor' }}</div>
                        </div>
                        <div class="survey-profile-divider"></div>
                        <a href="{{ route('logout') }}" class="survey-profile-menu-item" onclick="event.preventDefault(); document.getElementById('survey-logout-form').submit();">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                        <form id="survey-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

