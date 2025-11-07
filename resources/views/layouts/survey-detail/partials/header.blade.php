@php
    use Illuminate\Support\Str;
    // Survey is automatically available from parent view
    $currentTab = request()->get('tab', 'client-property');
@endphp

@if(isset($survey) && $survey)
<div class="survey-detail-header">
    <div class="survey-detail-header-content">
        <div class="survey-detail-header-left">
            <a href="{{ route('surveyor.surveys.index') }}" class="survey-detail-back-btn" title="Back to Surveys">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="survey-detail-header-info">
                <div class="survey-detail-address">
                    {{ Str::limit($survey->full_address ?? 'N/A', 40) }}
                </div>
                <div class="survey-detail-status-inline">
                    <span class="survey-detail-status-label">Status:</span>
                    <span class="survey-detail-status-badge status-{{ $survey->status ?? 'pending' }}">
                        {{ $survey->status_label ?? 'Pending' }}
                    </span>
                </div>
            </div>
        </div>
        <div class="survey-detail-header-right">
            <button type="button" class="survey-detail-menu-btn" id="survey-detail-sidebar-toggle" title="Menu">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>
    
    <!-- Tab Navigation -->
    <div class="survey-detail-tabs">
        <a href="{{ route('surveyor.surveys.detail', ['survey' => $survey->id, 'tab' => 'client-property']) }}" 
           class="survey-detail-tab {{ $currentTab === 'client-property' ? 'active' : '' }}">
            Client/Property
        </a>
        <a href="{{ route('surveyor.surveys.detail', ['survey' => $survey->id, 'tab' => 'media']) }}" 
           class="survey-detail-tab {{ $currentTab === 'media' ? 'active' : '' }}">
            Media
        </a>
        <a href="{{ route('surveyor.surveys.detail', ['survey' => $survey->id, 'tab' => 'input']) }}" 
           class="survey-detail-tab {{ $currentTab === 'input' ? 'active' : '' }}">
            Input
        </a>
        <a href="{{ route('surveyor.surveys.detail', ['survey' => $survey->id, 'tab' => 'output']) }}" 
           class="survey-detail-tab {{ $currentTab === 'output' ? 'active' : '' }}">
            Output
        </a>
        <a href="{{ route('surveyor.surveys.detail', ['survey' => $survey->id, 'tab' => 'configuration']) }}" 
           class="survey-detail-tab {{ $currentTab === 'configuration' ? 'active' : '' }}">
            Configuration
        </a>
    </div>
</div>
@endif

