@extends('layouts.app')

@section('title', 'Survey Section Assessments')

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="page-header">
            <h2 class="pageheader-title">Survey Section Assessments</h2>
            <div class="page-breadcrumb">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Survey Section Assessments</li>
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
                <h5 class="mb-0">Manage Survey Section Assessments</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
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
                                        <div class="d-flex align-items-center gap-2" style="flex-wrap: wrap;">
                                            <a href="{{ route('admin.survey-section-assessments.show', $assessment) }}" class="btn btn-sm btn-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.survey-section-assessments.edit', $assessment) }}" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.survey-section-assessments.toggle-completion', $assessment) }}" method="POST" style="display: inline-block; margin: 0;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm {{ $assessment->is_completed ? 'btn-secondary' : 'btn-success' }}" title="{{ $assessment->is_completed ? 'Mark as Incomplete' : 'Mark as Complete' }}">
                                                    <i class="fas fa-{{ $assessment->is_completed ? 'undo' : 'check' }}"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.survey-section-assessments.destroy', $assessment) }}" method="POST" style="display: inline-block; margin: 0;" onsubmit="return confirm('Are you sure you want to delete this assessment? This action cannot be undone.');">
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

                {{ $assessments->links() }}
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
/* Ensure action buttons display properly */
.btn-group .btn,
.d-flex.gap-2 .btn {
    margin: 0;
    border-radius: 4px;
}

.d-flex.gap-2 form {
    margin: 0;
    display: inline-block;
}

.d-flex.gap-2 .btn {
    min-width: 36px;
    padding: 0.25rem 0.5rem;
    cursor: pointer;
}

.d-flex.gap-2 .btn:hover {
    opacity: 0.9;
}

/* Ensure buttons are clickable */
.d-flex.gap-2 form button[type="submit"] {
    pointer-events: auto;
    cursor: pointer;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .d-flex.gap-2 {
        flex-direction: column;
        gap: 0.25rem !important;
    }
    
    .d-flex.gap-2 .btn {
        width: 100%;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Ensure all action buttons work properly
    const actionButtons = document.querySelectorAll('.d-flex.gap-2 .btn');
    
    actionButtons.forEach(function(button) {
        // Prevent double-click issues
        button.addEventListener('click', function(e) {
            // Allow the click to propagate normally
            e.stopPropagation();
        });
        
        // Ensure form buttons submit properly
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
    
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            if (alert && alert.parentNode) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(function() {
                    if (alert.parentNode) {
                        alert.remove();
                    }
                }, 500);
            }
        }, 5000);
    });
});
</script>
@endpush
