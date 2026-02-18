@extends('layouts.app')

@section('title', 'Survey Section Assessment #' . $assessment->id)

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="page-header">
            <h2 class="pageheader-title">Assessment #{{ $assessment->id }}</h2>
            <div class="page-breadcrumb">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.survey-section-assessments.index') }}">Survey Section Assessments</a></li>
                        <li class="breadcrumb-item active">Assessment #{{ $assessment->id }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Assessment Details</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="200">Survey:</th>
                        <td>
                            <a href="{{ route('admin.surveys.show', $assessment->survey) }}" class="text-primary">
                                Survey #{{ $assessment->survey->id }}
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th>Client:</th>
                        <td>{{ $assessment->survey->client_name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Section:</th>
                        <td><span class="badge badge-info">{{ $assessment->sectionDefinition->display_name ?? 'N/A' }}</span></td>
                    </tr>
                    <tr>
                        <th>Condition Rating:</th>
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
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td>
                            @if($assessment->is_completed)
                                <span class="badge badge-success">Completed</span>
                            @else
                                <span class="badge badge-secondary">Pending</span>
                            @endif
                        </td>
                    </tr>
                    @if($assessment->completed_at)
                    <tr>
                        <th>Completed:</th>
                        <td>{{ $assessment->completed_at->format('M d, Y H:i') }}
                            @if($assessment->completedBy)
                                <br><small class="text-muted">by {{ $assessment->completedBy->name }}</small>
                            @endif
                        </td>
                    </tr>
                    @endif
                    @if($assessment->notes)
                    <tr>
                        <th>Notes:</th>
                        <td>{{ $assessment->notes }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Created:</th>
                        <td>{{ $assessment->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Updated:</th>
                        <td>{{ $assessment->updated_at->format('M d, Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        @if($assessment->photos && $assessment->photos->count() > 0)
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Photos ({{ $assessment->photos->count() }})</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($assessment->photos as $photo)
                        <div class="col-md-4 col-sm-6 mb-3">
                            @if($photo->file_path && \Storage::disk('public')->exists($photo->file_path))
                                <img src="{{ asset('storage/' . $photo->file_path) }}" alt="Photo" class="img-fluid img-thumbnail" style="max-height: 200px; object-fit: cover;">
                            @else
                                <div class="bg-light p-3 text-center text-muted">Image not found</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        @if($assessment->costs && $assessment->costs->count() > 0)
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Costs</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assessment->costs as $cost)
                            <tr>
                                <td>{{ $cost->description ?? 'N/A' }}</td>
                                <td>£{{ number_format($cost->amount ?? 0, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if($assessment->defects && $assessment->defects->count() > 0)
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Defects</h5>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-1">
                    @foreach($assessment->defects as $defect)
                        <span class="badge badge-warning">{{ $defect->value ?? 'N/A' }}</span>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <a href="{{ route('admin.survey-section-assessments.edit', $assessment) }}" class="btn btn-primary btn-block mb-2">
                    <i class="fas fa-edit"></i> Edit Assessment
                </a>
                <form action="{{ route('admin.survey-section-assessments.toggle-completion', $assessment) }}" method="POST" class="mb-2">
                    @csrf
                    <button type="submit" class="btn btn-block {{ $assessment->is_completed ? 'btn-secondary' : 'btn-success' }}">
                        <i class="fas fa-{{ $assessment->is_completed ? 'undo' : 'check' }}"></i>
                        {{ $assessment->is_completed ? 'Mark as Incomplete' : 'Mark as Complete' }}
                    </button>
                </form>
                <form action="{{ route('admin.survey-section-assessments.destroy', $assessment) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this assessment? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-block btn-danger">
                        <i class="fas fa-trash"></i> Delete Assessment
                    </button>
                </form>
                <a href="{{ route('admin.survey-section-assessments.index') }}" class="btn btn-secondary btn-block mt-2">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
