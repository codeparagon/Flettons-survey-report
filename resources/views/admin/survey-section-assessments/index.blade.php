@extends('layouts.app')

@section('title', 'Survey Section Assessments')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="assessments-page-header">
            <div class="assessments-header-inner">
                <h2 class="assessments-page-title">Survey Section Assessments</h2>
                <div class="page-breadcrumb">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Survey Section Assessments</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card assessments-card">
            <div class="card-header assessments-card-header">
                <h5 class="mb-0">Manage Survey Section Assessments</h5>
            </div>
            <div class="card-body assessments-card-body">
                <!-- Desktop table -->
                <div class="assessments-table-wrap">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered assessments-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Survey</th>
                                    <th>Client</th>
                                    <th>Section</th>
                                    <th>Rating</th>
                                    <th>Photos</th>
                                    <th>Status</th>
                                    <th>Completed</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assessments as $assessment)
                                    <tr>
                                        <td>{{ $assessment->id }}</td>
                                        <td>
                                            <a href="{{ route('admin.surveys.show', $assessment->survey) }}" class="text-primary">
                                                Survey #{{ $assessment->survey->id }}
                                            </a>
                                        </td>
                                        <td>{{ $assessment->survey->client_name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge badge-info">{{ $assessment->sectionDefinition->display_name ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            @if($assessment->condition_rating)
                                                @switch($assessment->condition_rating)
                                                    @case(1)
                                                        <span class="badge badge-success">Excellent</span>
                                                        @break
                                                    @case(2)
                                                        <span class="badge badge-primary">Good</span>
                                                        @break
                                                    @case(3)
                                                        <span class="badge badge-warning">Fair</span>
                                                        @break
                                                    @case(4)
                                                        <span class="badge badge-danger">Poor</span>
                                                        @break
                                                    @default
                                                        <span class="badge badge-secondary">Rated ({{ $assessment->condition_rating }})</span>
                                                @endswitch
                                            @else
                                                <span class="text-muted">Not rated</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($assessment->photos && $assessment->photos->count() > 0)
                                                <span class="badge badge-info">{{ $assessment->photos->count() }}</span>
                                            @else
                                                <span class="text-muted">No photos</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($assessment->is_completed)
                                                <span class="badge badge-success">Completed</span>
                                            @else
                                                <span class="badge badge-secondary">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($assessment->completed_at)
                                                {{ $assessment->completed_at->format('M d, Y') }}
                                                @if($assessment->completedBy)
                                                    <br><small class="text-muted">by {{ $assessment->completedBy->name }}</small>
                                                @endif
                                            @else
                                                <span class="text-muted">Not completed</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2 assessments-actions">
                                                <a href="{{ route('admin.survey-section-assessments.show', $assessment) }}" class="btn btn-sm btn-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.survey-section-assessments.edit', $assessment) }}" class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.survey-section-assessments.toggle-completion', $assessment) }}" method="POST" class="d-inline mb-0">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm {{ $assessment->is_completed ? 'btn-secondary' : 'btn-success' }}" title="{{ $assessment->is_completed ? 'Mark as Incomplete' : 'Mark as Complete' }}">
                                                        <i class="fas fa-{{ $assessment->is_completed ? 'undo' : 'check' }}"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.survey-section-assessments.destroy', $assessment) }}" method="POST" class="d-inline mb-0" onsubmit="return confirm('Are you sure you want to delete this assessment? This action cannot be undone.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            No survey section assessments found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile card list -->
                <div class="assessments-mobile-list">
                    @forelse($assessments as $assessment)
                        <div class="assessment-mobile-card">
                            <div class="assessment-mobile-card-header">
                                <div>
                                    <span class="badge badge-info assessment-mobile-section">{{ $assessment->sectionDefinition->display_name ?? 'N/A' }}</span>
                                    <span class="assessment-mobile-id">#{{ $assessment->id }}</span>
                                </div>
                                <div class="assessment-mobile-badges">
                                    @if($assessment->is_completed)
                                        <span class="badge badge-success">Completed</span>
                                    @else
                                        <span class="badge badge-secondary">Pending</span>
                                    @endif
                                    @if($assessment->condition_rating)
                                        @switch($assessment->condition_rating)
                                            @case(1)
                                                <span class="badge badge-success">Excellent</span>
                                                @break
                                            @case(2)
                                                <span class="badge badge-primary">Good</span>
                                                @break
                                            @case(3)
                                                <span class="badge badge-warning">Fair</span>
                                                @break
                                            @case(4)
                                                <span class="badge badge-danger">Poor</span>
                                                @break
                                            @default
                                                <span class="badge badge-secondary">Rated</span>
                                        @endswitch
                                    @endif
                                </div>
                            </div>
                            <div class="assessment-mobile-card-meta">
                                <a href="{{ route('admin.surveys.show', $assessment->survey) }}" class="text-primary">Survey #{{ $assessment->survey->id }}</a>
                                <span class="text-muted"> · </span>
                                <span>{{ $assessment->survey->client_name ?? 'N/A' }}</span>
                                @if($assessment->photos && $assessment->photos->count() > 0)
                                    <span class="badge badge-info ml-1">{{ $assessment->photos->count() }} photo(s)</span>
                                @endif
                            </div>
                            <div class="assessment-mobile-card-actions">
                                <a href="{{ route('admin.survey-section-assessments.show', $assessment) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="{{ route('admin.survey-section-assessments.edit', $assessment) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('admin.survey-section-assessments.toggle-completion', $assessment) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $assessment->is_completed ? 'btn-secondary' : 'btn-success' }}">
                                        <i class="fas fa-{{ $assessment->is_completed ? 'undo' : 'check' }}"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.survey-section-assessments.destroy', $assessment) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this assessment? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="assessment-mobile-empty text-center py-4 text-muted">
                            No survey section assessments found.
                        </div>
                    @endforelse
                </div>

                <div class="assessments-pagination">
                    {{ $assessments->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
@endsection

@push('styles')
<style>
/* Page header */
.assessments-page-header {
    margin-bottom: 1.25rem;
}

.assessments-header-inner .breadcrumb {
    padding: 0;
    background: transparent;
}

.assessments-page-title {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

/* Card */
.assessments-card-body {
    padding: 1rem 1.25rem;
}

.assessments-table-wrap {
    width: 100%;
}

.assessments-table {
    min-width: 800px;
}

/* Action buttons */
.assessments-actions .btn,
.assessments-actions form {
    margin: 0;
}

.assessments-actions .btn {
    min-width: 34px;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
}

.assessments-actions form button[type="submit"] {
    cursor: pointer;
}

/* Mobile list: hidden by default */
.assessments-mobile-list {
    display: none;
}

.assessment-mobile-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 14px 16px;
    margin-bottom: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
}

.assessment-mobile-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 8px;
    margin-bottom: 8px;
}

.assessment-mobile-section {
    font-size: 0.8rem;
}

.assessment-mobile-id {
    font-size: 0.75rem;
    color: #6b7280;
    margin-left: 6px;
}

.assessment-mobile-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    justify-content: flex-end;
}

.assessment-mobile-card-meta {
    font-size: 0.85rem;
    color: #374151;
    margin-bottom: 12px;
}

.assessment-mobile-card-meta a {
    text-decoration: none;
}

.assessment-mobile-card-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    align-items: center;
}

.assessment-mobile-card-actions .btn {
    padding: 0.35rem 0.6rem;
    font-size: 0.8rem;
}

.assessment-mobile-card-actions form {
    margin: 0;
}

.assessments-pagination {
    margin-top: 1rem;
}

.assessments-pagination nav {
    flex-wrap: wrap;
}

/* Responsive: show mobile cards, hide table */
@media (max-width: 768px) {
    .assessments-page-header {
        margin-bottom: 1rem;
    }

    .assessments-page-title {
        font-size: 1.25rem;
    }

    .assessments-card-header {
        padding: 0.875rem 1rem;
    }

    .assessments-card-body {
        padding: 0.875rem 1rem;
    }

    .assessments-table-wrap {
        display: none;
    }

    .assessments-mobile-list {
        display: block;
    }

    .assessments-pagination .pagination {
        justify-content: center;
    }

    .assessments-pagination .page-link {
        padding: 0.4rem 0.65rem;
        font-size: 0.875rem;
    }
}

@media (max-width: 576px) {
    .assessments-page-title {
        font-size: 1.1rem;
    }

    .assessment-mobile-card {
        padding: 12px 14px;
    }

    .assessment-mobile-card-actions {
        gap: 6px;
    }

    .assessment-mobile-card-actions .btn {
        font-size: 0.75rem;
        padding: 0.3rem 0.5rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const actionButtons = document.querySelectorAll('.assessments-actions .btn, .assessment-mobile-card-actions .btn');
    actionButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
        });
        if (button.type === 'submit') {
            button.addEventListener('click', function(e) {
                const form = button.closest('form');
                if (form && !form.checkValidity()) {
                    e.preventDefault();
                    form.reportValidity();
                }
            });
        }
    });

    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            if (alert && alert.parentNode) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(function() {
                    if (alert.parentNode) alert.remove();
                }, 500);
            }
        }, 5000);
    });
});
</script>
@endpush
