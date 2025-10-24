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
                                    <td>{{ $assessment->survey->client_name }}</td>
                                    <td>
                                        <span class="badge badge-info">{{ $assessment->section->display_name }}</span>
                                    </td>
                                    <td>
                                        @if($assessment->condition_rating)
                                            @switch($assessment->condition_rating)
                                                @case('excellent')
                                                    <span class="badge badge-success">Excellent</span>
                                                    @break
                                                @case('good')
                                                    <span class="badge badge-primary">Good</span>
                                                    @break
                                                @case('fair')
                                                    <span class="badge badge-warning">Fair</span>
                                                    @break
                                                @case('poor')
                                                    <span class="badge badge-danger">Poor</span>
                                                    @break
                                            @endswitch
                                        @else
                                            <span class="text-muted">Not rated</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($assessment->photos && count($assessment->photos) > 0)
                                            <span class="badge badge-info">{{ count($assessment->photos) }}</span>
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
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.survey-section-assessments.show', $assessment) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.survey-section-assessments.edit', $assessment) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.survey-section-assessments.toggle-completion', $assessment) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm {{ $assessment->is_completed ? 'btn-secondary' : 'btn-success' }}">
                                                    <i class="fas fa-{{ $assessment->is_completed ? 'undo' : 'check' }}"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.survey-section-assessments.destroy', $assessment) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this assessment?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
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
@endsection
