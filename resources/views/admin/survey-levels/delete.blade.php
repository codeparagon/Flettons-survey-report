@extends('layouts.app')

@section('title', 'Confirm Delete - Survey Level')

@push('styles')
<style>
    .delete-confirm-card {
        background: white;
        border-radius: 12px;
        padding: 32px;
        margin-bottom: 24px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border: 2px solid #dc3545;
    }

    .delete-warning {
        background: #fff5f5;
        border-left: 4px solid #dc3545;
        padding: 16px 20px;
        margin: 20px 0;
        border-radius: 4px;
    }

    .level-summary {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin: 20px 0;
    }

    .level-summary-title {
        font-size: 20px;
        font-weight: 700;
        color: #1a202c;
        margin-bottom: 8px;
    }

    .level-summary-meta {
        color: #6b7280;
        font-size: 14px;
    }

    .btn-delete-confirm {
        background: #dc3545;
        border-color: #dc3545;
        color: white;
        padding: 10px 24px;
    }

    .btn-delete-confirm:hover {
        background: #c82333;
        border-color: #bd2130;
        color: white;
    }

    .step-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #dc3545;
        color: white;
        font-weight: 700;
        font-size: 14px;
        margin-right: 10px;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="page-header mb-4">
            <h2 class="pageheader-title">Delete Survey Level</h2>
            <div class="page-breadcrumb">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.survey-levels.index') }}">Survey Levels</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.survey-levels.show', $surveyLevel) }}">{{ $surveyLevel->display_name }}</a></li>
                        <li class="breadcrumb-item active">Confirm Delete</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-8">
        <div class="delete-confirm-card">
            <div class="d-flex align-items-center mb-4">
                <span class="step-badge">2</span>
                <h4 class="mb-0">Step 2: Confirm deletion</h4>
            </div>
            <p class="text-muted">You are about to permanently delete this survey level. This action cannot be undone.</p>

            <div class="level-summary">
                <div class="level-summary-title">{{ $surveyLevel->display_name }}</div>
                <div class="level-summary-meta">{{ $surveyLevel->name }}</div>
                <div class="level-summary-meta mt-2">
                    {{ $surveyLevel->sectionDefinitions->count() }} sections &nbsp;•&nbsp;
                    {{ $surveyLevel->accommodationTypes->count() }} accommodation types &nbsp;•&nbsp;
                    {{ $surveyLevel->contentSections->count() }} content sections
                </div>
            </div>

            @if($surveyLevel->surveys->count() > 0)
                <div class="delete-warning">
                    <strong><i class="fas fa-exclamation-triangle"></i> Cannot delete</strong>
                    <p class="mb-0 mt-2">This survey level has <strong>{{ $surveyLevel->surveys->count() }}</strong> survey(s) assigned to it. Please reassign those surveys to another level before deleting.</p>
                    <a href="{{ route('admin.survey-levels.index') }}" class="btn btn-secondary mt-3">
                        <i class="fas fa-arrow-left"></i> Back to Survey Levels
                    </a>
                </div>
            @else
                <div class="delete-warning">
                    <strong><i class="fas fa-exclamation-triangle"></i> Warning</strong>
                    <p class="mb-0 mt-2">This will remove the survey level and all its section, accommodation type, and content section assignments.</p>
                </div>

                <form action="{{ route('admin.survey-levels.destroy', $surveyLevel) }}" method="POST" class="d-inline" onsubmit="return confirm('Final confirmation: Are you absolutely sure you want to delete this survey level? This cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-delete-confirm">
                        <i class="fas fa-trash"></i> Yes, Delete Survey Level
                    </button>
                </form>
                <a href="{{ route('admin.survey-levels.show', $surveyLevel) }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            @endif
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Two-Step Delete Process</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong><span class="badge badge-secondary">Step 1</span></strong>
                    <p class="mb-0 mt-1 text-muted small">You clicked "Delete" on the survey level list or details page.</p>
                </div>
                <div>
                    <strong><span class="badge badge-danger">Step 2</span></strong>
                    <p class="mb-0 mt-1 text-muted small">Confirm the deletion on this page. An extra confirmation dialog will appear when you proceed.</p>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">Safe Alternatives</h5>
            </div>
            <div class="card-body">
                <p class="text-muted small">If you're not sure about deleting, consider:</p>
                <ul class="small text-muted mb-0">
                    <li>Deactivating the level instead (Edit → set to Inactive)</li>
                    <li>Reviewing which surveys use this level first</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
