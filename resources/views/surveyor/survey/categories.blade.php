@extends('layouts.app')

@section('title', 'Select Category - Survey #' . $survey->id)

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="page-header">
            <h2 class="pageheader-title">Select Category - {{ $survey->client_name }}</h2>
            <div class="page-breadcrumb">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('surveyor.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('surveyor.surveys.index') }}">Surveys</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('surveyor.surveys.show', $survey) }}">Survey #{{ $survey->id }}</a></li>
                        <li class="breadcrumb-item active">Select Category</li>
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
                        <h6>Overall Progress</h6>
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
                <h5 class="mb-0">Select Survey Category</h5>
            </div>
            <div class="card-body">
                <div class="categories-container">
                    @foreach($categories as $category)
                        @php
                            $categoryCompleted = $category->sections->filter(function($section) use ($assessments) {
                                $assessment = $assessments->get($section->id);
                                return $assessment && $assessment->is_completed;
                            })->count();
                            $categoryTotal = $category->sections->count();
                            $categoryPercentage = $categoryTotal > 0 ? round(($categoryCompleted / $categoryTotal) * 100) : 0;
                        @endphp
                        @if($categoryTotal > 0)
                        <a href="{{ route('surveyor.survey.category.sections', [$survey, $category]) }}" 
                           class="category-card">
                            <div class="category-icon">
                                <i class="{{ $category->icon }}"></i>
                            </div>
                            <div class="category-content">
                                <h4 class="category-title">{{ $category->display_name }}</h4>
                                <p class="category-description">{{ $category->description }}</p>
                                <div class="category-progress">
                                    <span class="progress-text">{{ $categoryCompleted }}/{{ $categoryTotal }} Sections Complete</span>
                                    <div class="progress-bar-container">
                                        <div class="progress-bar" style="width: {{ $categoryPercentage }}%"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="category-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* Category Selection Styling */
.categories-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 30px;
    margin-top: 20px;
}

.category-card {
    display: flex;
    align-items: center;
    padding: 30px;
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border: 2px solid #e5e7eb;
    border-radius: 16px;
    text-decoration: none;
    color: #1A202C;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    gap: 25px;
}

.category-card:hover {
    border-color: #C1EC4A;
    background: linear-gradient(135deg, #ffffff 0%, rgba(193, 236, 74, 0.05) 100%);
    transform: translateY(-4px);
    box-shadow: 0 12px 28px rgba(193, 236, 74, 0.15);
    text-decoration: none;
    color: #1A202C;
}

.category-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #C1EC4A 0%, #B0D93F 100%);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 8px 20px rgba(193, 236, 74, 0.3);
}

.category-icon i {
    font-size: 36px;
    color: #1A202C;
}

.category-content {
    flex: 1;
}

.category-title {
    font-size: 24px;
    font-weight: 700;
    color: #1A202C;
    margin: 0 0 8px 0;
}

.category-description {
    color: #6B7280;
    font-size: 16px;
    margin: 0 0 15px 0;
    line-height: 1.5;
}

.category-progress {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.progress-text {
    color: #6B7280;
    font-size: 14px;
    font-weight: 600;
}

.progress-bar-container {
    width: 100%;
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

.category-arrow {
    width: 40px;
    height: 40px;
    background-color: #F3F4F6;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: all 0.3s ease;
}

.category-card:hover .category-arrow {
    background-color: #C1EC4A;
    transform: translateX(4px);
}

.category-card:hover .category-arrow i {
    color: #1A202C;
}

.category-arrow i {
    font-size: 16px;
    color: #6B7280;
    transition: color 0.3s ease;
}

/* Responsive Design */
@media (max-width: 768px) {
    .categories-container {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .category-card {
        padding: 20px;
        gap: 20px;
    }
    
    .category-icon {
        width: 60px;
        height: 60px;
    }
    
    .category-icon i {
        font-size: 28px;
    }
    
    .category-title {
        font-size: 20px;
    }
    
    .category-description {
        font-size: 14px;
    }
}

@media (max-width: 480px) {
    .category-card {
        flex-direction: column;
        text-align: center;
        padding: 25px;
    }
    
    .category-content {
        order: 2;
    }
    
    .category-arrow {
        order: 3;
    }
}
</style>
@endpush
