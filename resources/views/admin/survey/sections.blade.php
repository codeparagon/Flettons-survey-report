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
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.surveys.index') }}">Surveys</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.surveys.show', $survey) }}">Survey #{{ $survey->id }}</a></li>
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
                            <tr>
                                <th>Surveyor:</th>
                                <td>{{ $survey->surveyor ? $survey->surveyor->name : 'Not Assigned' }}</td>
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
                <div class="row sections-row">
                    @foreach($requiredSections as $section)
                        @php
                            $assessment = $assessments->get($section->id);
                            $isCompleted = $assessment && $assessment->is_completed;
                        @endphp
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 col-12">
                            <a href="{{ route('admin.survey.section.assessment', [$survey, $section]) }}" 
                               class="section-card {{ $isCompleted ? 'completed' : '' }}">
                                <img src="{{ asset($section->icon) }}" 
                                     alt="{{ $section->display_name }}" 
                                     class="icon">
                                <span>{{ $section->display_name }}</span>
                                @if($isCompleted)
                                    <div class="completion-badge">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                @endif
                            </a>
                        </div>
                    @endforeach
                </div>
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
                <p class="text-muted">This survey is ready for final review and report generation.</p>
                <a href="{{ route('admin.surveys.show', $survey) }}" class="btn btn-success">
                    <i class="fas fa-file-alt"></i> Generate Report
                </a>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('newdesign/assets/libs/css/new-survey.css') }}">
<style>
/* Additional styles for completion badges */
.section-card {
    position: relative;
}

.completion-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    color: #28a745;
    font-size: 20px;
}

.section-card.completed {
    border-color: #28a745 !important;
    background-color: rgba(40, 167, 69, 0.05);
}

.section-card.completed:hover {
    border-color: #28a745 !important;
    background-color: rgba(40, 167, 69, 0.1);
}

.sections-row {
    margin-top: 20px;
}

.sections-heading {
    font-size: 20px;
    font-weight: 700;
    color: #1A202C;
    margin-bottom: 20px;
}
</style>
@endsection


