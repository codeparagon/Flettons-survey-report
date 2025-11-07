{{-- Input Tab Sidebar Navigation --}}
@php
    $sections = [
        'Introductory Details' => [
            'Company Information' => true,
            'Date of Survey' => true,
            'Client Details' => true,
        ],
        'Scope and Details of Instruction' => [
            'Scope of Work' => true,
            'Instruction Details' => true,
        ],
        'Limitations of Building Survey' => [
            'Limitations Overview' => true,
            'Exclusions' => true,
        ],
        'Survey Details' => [
            'Company Information' => true,
            'Date of Survey' => true,
            'Tenure' => true,
            'Property Type' => true,
        ],
        'Surveyor\'s Overall Assessment' => [
            'Surveyor\'s Opinion' => true,
            'Areas of Concern' => true,
            'Recommendations' => true,
        ],
        'The Main Building - Exterior' => [
            'Roofs' => true,
            'Walls' => true,
            'Windows' => true,
            'Doors' => true,
            'Other Roofs Front' => ($currentSection === 'other-roofs-front'),
        ],
        'The Main Building - Interior' => [
            'Ground Floor' => true,
            'First Floor' => true,
            'Second Floor' => true,
            'Basement' => true,
        ],
        'Services' => [
            'Electrical' => true,
            'Plumbing' => true,
            'Heating' => true,
            'Drainage' => true,
        ],
        'Grounds' => [
            'Front Garden' => true,
            'Rear Garden' => true,
            'Boundaries' => true,
        ],
    ];
@endphp

@foreach($sections as $mainSection => $subSections)
    <div class="survey-detail-sidebar-subsection">
        <div class="survey-detail-sidebar-subsection-title">{{ $mainSection }}</div>
        @foreach($subSections as $subSection => $isCompleted)
            @php
                $sectionKey = strtolower(str_replace([' ', '\''], ['-', ''], $subSection));
            @endphp
            <a href="{{ route('surveyor.surveys.detail', ['survey' => $survey->id, 'tab' => 'input', 'section' => $sectionKey]) }}" 
               class="survey-detail-sidebar-item {{ $sectionKey === $currentSection ? 'active' : '' }}">
                <i class="fas fa-check-circle {{ $isCompleted ? 'completed' : '' }}"></i>
                <span>{{ $subSection }}</span>
            </a>
        @endforeach
    </div>
@endforeach

