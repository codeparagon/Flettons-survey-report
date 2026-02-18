@extends('layouts.app')

@section('title', 'Edit Survey Section Assessment')

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="page-header">
            <h2 class="pageheader-title">Edit Assessment #{{ $assessment->id }}</h2>
            <div class="page-breadcrumb">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.survey-section-assessments.index') }}">Survey Section Assessments</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.survey-section-assessments.show', $assessment) }}">Assessment #{{ $assessment->id }}</a></li>
                        <li class="breadcrumb-item active">Edit</li>
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
                <h5 class="mb-0">Assessment Information</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.survey-section-assessments.update', $assessment) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label>Survey</label>
                        <p class="form-control-plaintext">
                            <a href="{{ route('admin.surveys.show', $assessment->survey) }}" class="text-primary">
                                Survey #{{ $assessment->survey->id }} - {{ $assessment->survey->client_name ?? 'N/A' }}
                            </a>
                        </p>
                    </div>

                    <div class="form-group">
                        <label>Section</label>
                        <p class="form-control-plaintext">
                            <span class="badge badge-info">{{ $assessment->sectionDefinition->display_name ?? 'N/A' }}</span>
                        </p>
                    </div>

                    <div class="form-group">
                        <label for="condition_rating">Condition Rating</label>
                        <select class="form-control @error('condition_rating') is-invalid @enderror" id="condition_rating" name="condition_rating">
                            <option value="">Not rated</option>
                            <option value="1" {{ old('condition_rating', $assessment->condition_rating) == 1 ? 'selected' : '' }}>1 - Excellent</option>
                            <option value="2" {{ old('condition_rating', $assessment->condition_rating) == 2 ? 'selected' : '' }}>2 - Good</option>
                            <option value="3" {{ old('condition_rating', $assessment->condition_rating) == 3 ? 'selected' : '' }}>3 - Fair</option>
                            <option value="4" {{ old('condition_rating', $assessment->condition_rating) == 4 ? 'selected' : '' }}>4 - Poor</option>
                        </select>
                        @error('condition_rating')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="4" maxlength="2000" placeholder="Additional notes for this assessment">{{ old('notes', $assessment->notes) }}</textarea>
                        <small class="form-text text-muted">Maximum 2000 characters</small>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input type="hidden" name="is_completed" value="0">
                            <input type="checkbox" class="form-check-input" id="is_completed" name="is_completed" value="1" {{ old('is_completed', $assessment->is_completed) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_completed">
                                Mark as Completed
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Assessment
                        </button>
                        <a href="{{ route('admin.survey-section-assessments.show', $assessment) }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Assessment Info</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th>ID:</th>
                        <td>{{ $assessment->id }}</td>
                    </tr>
                    <tr>
                        <th>Photos:</th>
                        <td>{{ $assessment->photos ? $assessment->photos->count() : 0 }}</td>
                    </tr>
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

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <a href="{{ route('admin.survey-section-assessments.show', $assessment) }}" class="btn btn-info btn-block mb-2">
                    <i class="fas fa-eye"></i> View Assessment
                </a>
                <a href="{{ route('admin.survey-section-assessments.index') }}" class="btn btn-secondary btn-block">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
