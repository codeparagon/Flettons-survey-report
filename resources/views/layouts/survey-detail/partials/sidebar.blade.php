@php
    // Survey is automatically available from parent view
    $currentTab = request()->get('tab', 'client-property');
    $currentSection = request()->get('section', '');
@endphp

@if(isset($survey) && $survey)

<!-- Sidebar Backdrop -->
<div class="survey-detail-sidebar-backdrop" id="survey-detail-sidebar-backdrop"></div>

<div class="survey-detail-sidebar">
    <div class="survey-detail-sidebar-header">
        <div class="survey-detail-sidebar-title">Navigation</div>
        <button type="button" class="survey-detail-sidebar-close" id="survey-detail-sidebar-close" title="Close">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <div class="survey-detail-sidebar-content">
        <!-- Sub-sections for Input/Output -->
        @if(in_array($currentTab, ['input', 'output']))
        <div class="survey-detail-sidebar-section">
            <div class="survey-detail-sidebar-section-title">
                {{ $currentTab === 'input' ? 'Input Sections' : 'Output Sections' }}
            </div>
            <nav class="survey-detail-sidebar-nav">
                @if($currentTab === 'input')
                    @include('surveyor.surveys.partials.input-sidebar', ['survey' => $survey, 'currentSection' => $currentSection])
                @elseif($currentTab === 'output')
                    @include('surveyor.surveys.partials.output-sidebar', ['survey' => $survey, 'currentSection' => $currentSection])
                @endif
            </nav>
        </div>
        @endif

        <!-- Quick Actions -->
        <div class="survey-detail-sidebar-section">
            <div class="survey-detail-sidebar-section-title">Quick Actions</div>
            <nav class="survey-detail-sidebar-nav">
                <a href="{{ route('surveyor.surveys.index') }}" class="survey-detail-sidebar-item">
                    <span>Back to Surveys</span>
                </a>
                <a href="{{ route('logout') }}" class="survey-detail-sidebar-item" onclick="event.preventDefault(); document.getElementById('survey-detail-logout-form').submit();">
                    <span>Logout</span>
                </a>
                <form id="survey-detail-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </nav>
        </div>
    </div>
</div>
@endif

