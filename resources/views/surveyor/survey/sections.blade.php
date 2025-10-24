@extends('layouts.app')

@section('title', 'Survey Sections - ' . $survey->client_name)

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="page-header">
            <h2 class="pageheader-title">Survey Sections - {{ $survey->client_name }}</h2>
            <div class="page-breadcrumb">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('surveyor.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('surveyor.surveys.index') }}">Surveys</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('surveyor.surveys.show', $survey) }}">Survey #{{ $survey->id }}</a></li>
                        <li class="breadcrumb-item active">Sections</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Survey Progress</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Survey Information</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th width="120">Client:</th>
                                <td>{{ $survey->client_name }}</td>
                            </tr>
                            <tr>
                                <th>Property:</th>
                                <td>{{ $survey->property_address_full }}</td>
                            </tr>
                            <tr>
                                <th>Level:</th>
                                <td><span class="badge badge-info">{{ $survey->level }}</span></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Progress</h6>
                        <div class="progress mb-2">
                            <div class="progress-bar" style="width: {{ $progress['percentage'] }}%">
                                {{ $progress['completed'] }}/{{ $progress['total'] }} Sections
                            </div>
                        </div>
                        <small class="text-muted">{{ $progress['percentage'] }}% Complete</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Survey Sections</h5>
            </div>
            <div class="card-body">
                @foreach($sectionsByCategory as $category => $sections)
                    <div class="category-section">
                        <div class="category-header">
                            <h4 class="category-title">
                                @if($category === 'exterior')
                                    <i class="fas fa-home mr-2"></i>
                                    Exterior
                                @else
                                    <i class="fas fa-couch mr-2"></i>
                                    Interior
                                @endif
                            </h4>
                            <div class="category-progress">
                                @php
                                    $categoryCompleted = $sections->filter(function($section) use ($assessments) {
                                        $assessment = $assessments->get($section->id);
                                        return $assessment && $assessment->is_completed;
                                    })->count();
                                    $categoryTotal = $sections->count();
                                    $categoryPercentage = $categoryTotal > 0 ? round(($categoryCompleted / $categoryTotal) * 100) : 0;
                                @endphp
                                <span class="progress-text">{{ $categoryCompleted }}/{{ $categoryTotal }} Complete</span>
                                <div class="progress-bar-container">
                                    <div class="progress-bar" style="width: {{ $categoryPercentage }}%"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="sections-container">
                            @foreach($sections as $section)
                                @php
                                    $assessment = $assessments->get($section->id);
                                    $isCompleted = $assessment && $assessment->is_completed;
                                @endphp
                                <a href="{{ route('surveyor.survey.section.form', [$survey, $section]) }}" 
                                   class="section-card {{ $isCompleted ? 'completed' : '' }}">
                                    <img src="{{ asset($section->icon) }}" 
                                         alt="{{ $section->display_name }}" 
                                         class="section-icon">
                                    <div class="section-content">
                                        <span class="section-name">{{ $section->display_name }}</span>
                                    </div>
                                    @if($isCompleted)
                                        <div class="completion-badge">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@if($progress['completed'] === $progress['total'])
<div class="row">
    <div class="col-xl-12">
        <div class="card border-success">
            <div class="card-body text-center">
                <h5 class="text-success">
                    <i class="fas fa-check-circle"></i> All Sections Completed!
                </h5>
                <p class="text-muted">You can now mark this survey as completed.</p>
                <a href="{{ route('surveyor.surveys.show', $survey) }}" class="btn btn-success">
                    <i class="fas fa-check"></i> Mark Survey Complete
                </a>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('newdesign/assets/libs/css/new-survey.css') }}">
<style>
/* Category Section Styling */
.category-section {
    margin-bottom: 40px;
}

.category-section:last-child {
    margin-bottom: 0;
}

.category-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding: 20px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 12px;
    border: 1px solid #e5e7eb;
}

.category-title {
    color: #1A202C;
    font-size: 24px;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
}

.category-title i {
    color: #C1EC4A;
    font-size: 28px;
}

.category-progress {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 8px;
}

.progress-text {
    color: #6B7280;
    font-size: 14px;
    font-weight: 600;
}

.progress-bar-container {
    width: 200px;
    height: 8px;
    background-color: #E5E7EB;
    border-radius: 4px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #C1EC4A 0%, #B0D93F 100%);
    border-radius: 4px;
    transition: width 0.3s ease;
}

/* Survey Sections - Landscape Card Layout */
.sections-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
    gap: 20px;
}

.section-card {
    position: relative;
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: flex-start;
    padding: 25px 30px;
    height: 120px;
    gap: 20px;
    border: 2px solid #d5d7d9;
    border-radius: 12px;
    background-color: #fff;
    color: #1A202C;
    font-weight: 600;
    font-size: 16px;
    text-decoration: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    cursor: pointer;
    width: 100%;
}

.section-card:hover,
.section-card:focus {
    border-color: #C1EC4A;
    box-shadow: 0 8px 25px rgba(193, 236, 74, 0.15);
    text-decoration: none;
    color: #1A202C;
    transform: translateY(-2px);
}

.section-card:active {
    transform: translateY(0);
    box-shadow: 0 4px 15px rgba(193, 236, 74, 0.2);
}

/* Section Icon Styling */
.section-icon {
    width: 70px;
    height: 70px;
    object-fit: contain;
    transition: transform 0.3s ease;
    flex-shrink: 0;
    border-radius: 8px;
    background-color: #f8f9fa;
    padding: 8px;
}

.section-card:hover .section-icon {
    transform: scale(1.05);
}

/* Section Content Styling */
.section-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    text-align: left;
}

.section-name {
    font-size: 18px;
    font-weight: 600;
    color: #1A202C;
    line-height: 1.4;
    text-align: left;
}

/* Completion Badge */
.completion-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    color: #C1EC4A;
    font-size: 24px;
    background-color: #fff;
    border-radius: 50%;
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.section-card.completed {
    border-color: #C1EC4A;
    background-color: rgba(193, 236, 74, 0.03);
}

.section-card.completed:hover {
    border-color: #C1EC4A;
    background-color: rgba(193, 236, 74, 0.08);
}

/* Responsive Design */
@media (max-width: 768px) {
    .sections-container {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .section-card {
        height: 100px;
        padding: 20px;
        gap: 15px;
    }
    
    .section-icon {
        width: 50px;
        height: 50px;
    }
    
    .section-name {
        font-size: 16px;
    }
    
    .completion-badge {
        width: 30px;
        height: 30px;
        font-size: 20px;
    }
}

@media (max-width: 480px) {
    .section-card {
        height: 90px;
        padding: 15px;
        gap: 12px;
    }
    
    .section-icon {
        width: 45px;
        height: 45px;
    }
    
    .section-name {
        font-size: 15px;
    }
}

/* Override any conflicting styles */
.sections-container .section-card {
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    justify-content: flex-start !important;
    text-align: left !important;
}

.sections-container .section-card .section-icon {
    width: 70px !important;
    height: 70px !important;
    object-fit: contain !important;
    flex-shrink: 0 !important;
}

.sections-container .section-card .section-content {
    flex: 1 !important;
    text-align: left !important;
}

.sections-container .section-card .section-name {
    text-align: left !important;
    font-size: 18px !important;
}
</style>
@endpush
