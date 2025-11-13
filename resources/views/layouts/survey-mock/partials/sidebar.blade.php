@php
    // Get survey from route parameter if available
    $currentSurvey = request()->route('survey') ?? (isset($survey) ? $survey : null);
@endphp

<div class="survey-sidebar" id="survey-sidebar">
    <div class="survey-sidebar-content">
        <!-- Navigation -->
        <div class="survey-sidebar-section">
            <div class="survey-sidebar-title">MENU</div>
            <nav class="survey-nav">
                @if($currentSurvey)
                    <a href="{{ route('surveyor.surveys.detail.mock', $currentSurvey) }}" class="survey-nav-item {{ request()->is('surveyor/surveys/*/detail-mock*') ? 'active' : '' }}">
                        <span class="survey-nav-label">Survey Details</span>
                    </a>
                    <a href="{{ route('surveyor.surveys.desk-study.mock', $currentSurvey) }}" class="survey-nav-item {{ request()->is('surveyor/surveys/*/desk-study-mock*') ? 'active' : '' }}">
                        <span class="survey-nav-label">Desk Study</span>
                    </a>
                    <a href="{{ route('surveyor.surveys.data.mock', $currentSurvey) }}" class="survey-nav-item {{ request()->is('surveyor/surveys/*/data-mock*') ? 'active' : '' }}">
                        <span class="survey-nav-label">Survey Data</span>
                    </a>
                    <a href="{{ route('surveyor.surveys.media.mock', $currentSurvey) }}" class="survey-nav-item {{ request()->is('surveyor/surveys/*/media-mock*') ? 'active' : '' }}">
                        <span class="survey-nav-label">Media Files</span>
                    </a>
                    <a href="{{ route('surveyor.surveys.transcript.mock', $currentSurvey) }}" class="survey-nav-item {{ request()->is('surveyor/surveys/*/transcript-mock*') ? 'active' : '' }}">
                        <span class="survey-nav-label">Transcript</span>
                    </a>
                    <a href="{{ route('surveyor.surveys.documents.mock', $currentSurvey) }}" class="survey-nav-item {{ request()->is('surveyor/surveys/*/documents-mock*') ? 'active' : '' }}">
                        <span class="survey-nav-label">Documents</span>
                    </a>
                @else
                    <a href="javascript:void(0)" class="survey-nav-item survey-nav-disabled">
                        <span class="survey-nav-label">Survey Details</span>
                    </a>
                    <a href="javascript:void(0)" class="survey-nav-item survey-nav-disabled">
                        <span class="survey-nav-label">Desk Study</span>
                    </a>
                    <a href="javascript:void(0)" class="survey-nav-item survey-nav-disabled">
                        <span class="survey-nav-label">Survey Data</span>
                    </a>
                    <a href="javascript:void(0)" class="survey-nav-item survey-nav-disabled">
                        <span class="survey-nav-label">Media Files</span>
                    </a>
                    <a href="javascript:void(0)" class="survey-nav-item survey-nav-disabled">
                        <span class="survey-nav-label">Transcript</span>
                    </a>
                    <a href="javascript:void(0)" class="survey-nav-item survey-nav-disabled">
                        <span class="survey-nav-label">Documents</span>
                    </a>
                @endif
            </nav>
        </div>
    </div>

    <div class="survey-sidebar-footer">
        <button type="button" class="survey-sidebar-collapse" id="survey-sidebar-collapse">
            <i class="fas fa-chevron-left"></i>
            <span>Hide Sidebar</span>
        </button>
    </div>
</div>

