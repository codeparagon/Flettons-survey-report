@extends('layouts.app')

@section('title', 'Survey Levels Management')

@push('styles')
<style>
    .level-card {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        background: white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .level-card:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        transform: translateY(-2px);
    }

    .level-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }

    .level-title {
        color: #1a202c;
        font-size: 22px;
        font-weight: 700;
        margin: 0;
        line-height: 1.3;
    }

    .level-badge {
        background: #1a202c;
        color: #c1ec4a;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .level-description {
        color: #6b7280;
        margin-bottom: 20px;
        font-size: 15px;
        line-height: 1.5;
    }

    .sections-list {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 20px;
    }

    .section-tag {
        background: #f3f4f6;
        color: #374151;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        border: 1px solid #e5e7eb;
    }

    .level-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: auto;
    }

    .btn-action {
        padding: 10px 16px;
        font-size: 14px;
        font-weight: 600;
        border-radius: 8px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        border: 2px solid transparent;
    }

    .btn-view {
        background: #1a202c;
        color: #c1ec4a;
        border-color: #1a202c;
    }

    .btn-view:hover {
        background: #2d3748;
        color: #c1ec4a;
        border-color: #2d3748;
        transform: translateY(-1px);
    }

    .btn-edit {
        background: #c1ec4a;
        color: #1a202c;
        border-color: #c1ec4a;
    }

    .btn-edit:hover {
        background: #a8d83a;
        color: #1a202c;
        border-color: #a8d83a;
        transform: translateY(-1px);
    }

    .btn-delete {
        background: #1a202c;
        color: #ef4444;
        border-color: #1a202c;
    }

    .btn-delete:hover {
        background: #2d3748;
        color: #ef4444;
        border-color: #2d3748;
        transform: translateY(-1px);
    }

    .page-header {
        margin-bottom: 32px;
    }

    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: #1a202c;
        margin-bottom: 8px;
    }

    .btn-primary-large {
        background: #1a202c;
        color: #c1ec4a;
        border: 2px solid #1a202c;
        padding: 12px 24px;
        font-size: 16px;
        font-weight: 600;
        border-radius: 10px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
    }

    .btn-primary-large:hover {
        background: #2d3748;
        color: #c1ec4a;
        border-color: #2d3748;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(26, 32, 44, 0.3);
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .empty-state-icon {
        font-size: 64px;
        color: #9ca3af;
        margin-bottom: 20px;
    }

    .empty-state-title {
        font-size: 24px;
        font-weight: 600;
        color: #1a202c;
        margin-bottom: 12px;
    }

    .empty-state-text {
        font-size: 16px;
        color: #6b7280;
        margin-bottom: 24px;
    }

    .alert {
        border-radius: 10px;
        border: none;
        padding: 16px 20px;
        margin-bottom: 24px;
        font-size: 15px;
        font-weight: 500;
    }

    .alert-success {
        background: rgba(0, 212, 170, 0.1);
        color: #00d4aa;
        border-left: 4px solid #00d4aa;
    }

    .alert-danger {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
        border-left: 4px solid #ef4444;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="page-title">Survey Levels Management</h1>
                    <a href="{{ route('admin.survey-levels.create') }}" class="btn-primary-large">
                        <i class="fas fa-plus"></i> Create New Level
                    </a>
                </div>
            </div>

            <div class="row">
                @forelse($levels as $level)
                    <div class="col-md-6 col-lg-4">
                        <div class="level-card">
                            <div class="level-header">
                                <h4 class="level-title">{{ $level->display_name }}</h4>
                                <span class="level-badge">{{ $level->name }}</span>
                            </div>
                            
                            <p class="level-description">{{ $level->description }}</p>
                            
                            <div class="sections-list">
                                @forelse($level->sections as $section)
                                    <span class="section-tag">{{ $section->display_name }}</span>
                                @empty
                                    <span class="text-muted">No sections assigned</span>
                                @endforelse
                            </div>
                            
                            <div class="level-actions">
                                <a href="{{ route('admin.survey-levels.show', $level) }}" 
                                   class="btn-action btn-view">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="{{ route('admin.survey-levels.edit', $level) }}" 
                                   class="btn-action btn-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('admin.survey-levels.destroy', $level) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this survey level?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action btn-delete">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="empty-state">
                            <i class="fas fa-clipboard-list empty-state-icon"></i>
                            <h3 class="empty-state-title">No Survey Levels Found</h3>
                            <p class="empty-state-text">Create your first survey level to get started with organizing your survey sections.</p>
                            <a href="{{ route('admin.survey-levels.create') }}" class="btn-primary-large">
                                <i class="fas fa-plus"></i> Create Survey Level
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
