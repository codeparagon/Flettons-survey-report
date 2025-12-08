@extends('layouts.app')

@section('title', 'Survey Level Details')

@push('styles')
<style>
    .level-header {
        background: linear-gradient(135deg, #C1EC4A 0%, #B0D93F 100%);
        color: #1A202C;
        padding: 30px;
        border-radius: 12px;
        margin-bottom: 30px;
    }

    .level-title {
        font-size: 28px;
        font-weight: 700;
        margin: 0 0 10px 0;
    }

    .level-subtitle {
        font-size: 16px;
        opacity: 0.8;
        margin: 0 0 15px 0;
    }

    .level-description {
        font-size: 14px;
        opacity: 0.9;
        margin: 0;
    }

    .info-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .info-title {
        color: #1A202C;
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #C1EC4A;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }

    .info-item {
        text-align: center;
    }

    .info-value {
        font-size: 24px;
        font-weight: 700;
        color: #1A202C;
        margin-bottom: 5px;
    }

    .info-label {
        font-size: 12px;
        color: #6B7280;
        text-transform: uppercase;
        font-weight: 600;
    }

    .sections-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 15px;
    }

    .section-card {
        background: #F9FAFB;
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        transition: all 0.3s ease;
    }

    .section-card:hover {
        border-color: #C1EC4A;
        background: #F0F9FF;
    }

    .section-icon {
        width: 48px;
        height: 48px;
        margin: 0 auto 15px;
        border-radius: 50%;
        background: #C1EC4A;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #1A202C;
        font-size: 20px;
    }

    .section-name {
        font-weight: 600;
        color: #1A202C;
        margin-bottom: 8px;
    }

    .section-description {
        font-size: 12px;
        color: #6B7280;
        margin-bottom: 10px;
    }

    .section-category {
        background: #E5E7EB;
        color: #374151;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Survey Level Details</h2>
                <div>
                    <a href="{{ route('admin.survey-levels.edit', $surveyLevel) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit Level
                    </a>
                    <a href="{{ route('admin.survey-levels.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Levels
                    </a>
                </div>
            </div>

            <!-- Level Header -->
            <div class="level-header">
                <h1 class="level-title">{{ $surveyLevel->display_name }}</h1>
                <p class="level-subtitle">{{ $surveyLevel->name }}</p>
                @if($surveyLevel->description)
                    <p class="level-description">{{ $surveyLevel->description }}</p>
                @endif
            </div>

            <!-- Level Information -->
            <div class="info-card">
                <h4 class="info-title">Level Information</h4>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-value">{{ $surveyLevel->sectionDefinitions->count() }}</div>
                        <div class="info-label">Total Sections</div>
                    </div>
                    <div class="info-item">
                        <div class="info-value">{{ $surveyLevel->sort_order }}</div>
                        <div class="info-label">Sort Order</div>
                    </div>
                    <div class="info-item">
                        <div class="info-value">
                            <span class="badge {{ $surveyLevel->is_active ? 'badge-success' : 'badge-secondary' }}">
                                {{ $surveyLevel->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <div class="info-label">Status</div>
                    </div>
                    <div class="info-item">
                        <div class="info-value">{{ $surveyLevel->surveys->count() }}</div>
                        <div class="info-label">Surveys Using This Level</div>
                    </div>
                </div>
            </div>

            <!-- Assigned Sections -->
            <div class="info-card">
                <h4 class="info-title">Assigned Sections</h4>
                @if($surveyLevel->sectionDefinitions->count() > 0)
                    <div class="sections-grid">
                        @foreach($surveyLevel->sectionDefinitions as $section)
                            <div class="section-card">
                                <div class="section-icon">
                                    <i class="{{ $section->subcategory->category->icon ?? 'fas fa-clipboard-list' }}"></i>
                                </div>
                                <div class="section-name">{{ $section->display_name }}</div>
                                <div class="section-description">{{ $section->description }}</div>
                                <div class="section-category">{{ $section->subcategory->category->display_name ?? 'No Category' }}</div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Sections Assigned</h5>
                        <p class="text-muted">This survey level doesn't have any sections assigned to it.</p>
                        <a href="{{ route('admin.survey-levels.edit', $surveyLevel) }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Sections
                        </a>
                    </div>
                @endif
            </div>

            <!-- Related Surveys -->
            @if($surveyLevel->surveys->count() > 0)
                <div class="info-card">
                    <h4 class="info-title">Surveys Using This Level</h4>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Survey ID</th>
                                    <th>Property Address</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($surveyLevel->surveys as $survey)
                                    <tr>
                                        <td>#{{ $survey->id }}</td>
                                        <td>{{ $survey->property_address }}</td>
                                        <td>
                                            <span class="badge badge-{{ $survey->status === 'completed' ? 'success' : ($survey->status === 'in_progress' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($survey->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $survey->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <a href="{{ route('admin.surveys.show', $survey) }}" class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

