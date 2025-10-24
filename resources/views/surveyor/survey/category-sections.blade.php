@extends('layouts.app')

@section('title', $category->display_name . ' Sections - Survey #' . $survey->id)

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="page-header">
            <h2 class="pageheader-title">{{ $category->display_name }} Sections - {{ $survey->client_name }}</h2>
            <div class="page-breadcrumb">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('surveyor.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('surveyor.surveys.index') }}">Surveys</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('surveyor.surveys.show', $survey) }}">Survey #{{ $survey->id }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('surveyor.survey.categories', $survey) }}">Categories</a></li>
                        <li class="breadcrumb-item active">{{ $category->display_name }}</li>
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
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="{{ $category->icon }} mr-2"></i>
                        {{ $category->display_name }} Sections
                    </h5>
                    <a href="{{ route('surveyor.survey.categories', $survey) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back to Categories
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="category-info mb-4">
                    <p class="text-muted mb-3">{{ $category->description }}</p>
                    <div class="category-progress-summary">
                        @php
                            $categoryCompleted = $sections->filter(function($section) use ($assessments) {
                                $assessment = $assessments->get($section->id);
                                return $assessment && $assessment->is_completed;
                            })->count();
                            $categoryTotal = $sections->count();
                            $categoryPercentage = $categoryTotal > 0 ? round(($categoryCompleted / $categoryTotal) * 100) : 0;
                        @endphp
                        <div class="progress-info">
                            <span class="progress-text">{{ $categoryCompleted }}/{{ $categoryTotal }} Sections Complete</span>
                            <span class="progress-percentage">{{ $categoryPercentage }}%</span>
                        </div>
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
                                <span class="section-description">{{ $section->description }}</span>
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
        </div>
    </div>
</div>

@if($categoryCompleted === $categoryTotal)
<div class="row">
    <div class="col-xl-12">
        <div class="card border-success">
            <div class="card-body text-center">
                <h5 class="text-success">
                    <i class="fas fa-check-circle"></i> {{ $category->display_name }} Category Completed!
                </h5>
                <p class="text-muted">All sections in this category have been completed.</p>
                <a href="{{ route('surveyor.survey.categories', $survey) }}" class="btn btn-success">
                    <i class="fas fa-arrow-left"></i> Back to Categories
                </a>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('styles')
<style>
/* Category Info Styling */
.category-info {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 25px;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
}

.category-progress-summary {
    margin-top: 15px;
}

.progress-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.progress-text {
    color: #6B7280;
    font-size: 16px;
    font-weight: 600;
}

.progress-percentage {
    color: #C1EC4A;
    font-size: 18px;
    font-weight: 700;
}

.progress-bar-container {
    width: 100%;
    height: 10px;
    background-color: #E5E7EB;
    border-radius: 5px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #C1EC4A 0%, #B0D93F 100%);
    border-radius: 5px;
    transition: width 0.3s ease;
}

/* Survey Sections - Landscape Card Layout */
.sections-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
    gap: 20px;
    margin-top: 20px;
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
    margin-bottom: 4px;
}

.section-description {
    font-size: 14px;
    color: #6B7280;
    line-height: 1.3;
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
    
    .section-description {
        font-size: 13px;
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
    
    .section-description {
        font-size: 12px;
    }
}
</style>
@endpush
