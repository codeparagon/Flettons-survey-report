{{-- Output Tab Sidebar Navigation - Report Sections --}}
@php
    $sections = [
        'Report Summary' => [
            'Executive Summary' => 'executive-summary',
            'Key Findings' => 'key-findings',
            'Recommendations' => 'recommendations',
        ],
        'Costs Analysis' => [
            'Estimated Repair Costs' => 'estimated-repair-costs',
            'Maintenance Costs' => 'maintenance-costs',
            'Total Costs' => 'total-costs',
        ],
        'Documents' => [
            'Survey Report' => 'survey-report',
            'Photographs' => 'photographs',
            'Supporting Documents' => 'supporting-documents',
        ],
        'Geo Documents' => [
            'Location Maps' => 'location-maps',
            'Satellite Images' => 'satellite-images',
            'Planning Maps' => 'planning-maps',
        ],
        'Final Output' => [
            'Complete Report' => 'complete-report',
            'Report Summary' => 'report-summary',
            'Appendices' => 'appendices',
        ],
    ];
@endphp

@foreach($sections as $mainSection => $subSections)
    <div class="survey-detail-sidebar-subsection">
        <div class="survey-detail-sidebar-subsection-title">{{ $mainSection }}</div>
        @foreach($subSections as $subSection => $sectionKey)
            <a href="{{ route('surveyor.surveys.detail', ['survey' => $survey->id, 'tab' => 'output', 'section' => $sectionKey]) }}" 
               class="survey-detail-sidebar-item {{ $sectionKey === $currentSection ? 'active' : '' }}">
                <i class="fas fa-check-circle completed"></i>
                <span>{{ $subSection }}</span>
            </a>
        @endforeach
    </div>
@endforeach
