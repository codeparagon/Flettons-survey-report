@extends('layouts.app')

@section('title', $section->display_name . ' Assessment - ' . $survey->client_name)

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="page-header">
            <h2 class="pageheader-title">{{ $section->display_name }} Assessment</h2>
            <div class="page-breadcrumb">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.surveys.index') }}">Surveys</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.surveys.show', $survey) }}">Survey #{{ $survey->id }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.survey.sections', $survey) }}">Sections</a></li>
                        <li class="breadcrumb-item active">{{ $section->display_name }}</li>
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
                <h5 class="mb-0">{{ $section->display_name }} Assessment Details</h5>
            </div>
            <div class="card-body">
                @if($assessment && $assessment->is_completed)
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Assessment Information</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="150">Condition Rating:</th>
                                    <td>
                                        <span class="badge {{ $assessment->condition_badge }}">
                                            {{ ucfirst($assessment->condition_rating) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Completed By:</th>
                                    <td>{{ $assessment->completedBy ? $assessment->completedBy->name : 'Unknown' }}</td>
                                </tr>
                                <tr>
                                    <th>Completed At:</th>
                                    <td>{{ $assessment->formatted_completed_at }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Section Description</h6>
                            <p class="text-muted">{{ $section->description }}</p>
                        </div>
                    </div>

                    @if($assessment->defects_noted)
                    <div class="form-group">
                        <label>Defects Noted</label>
                        <div class="card">
                            <div class="card-body">
                                <p class="mb-0">{{ $assessment->defects_noted }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($assessment->recommendations)
                    <div class="form-group">
                        <label>Recommendations</label>
                        <div class="card">
                            <div class="card-body">
                                <p class="mb-0">{{ $assessment->recommendations }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($assessment->notes)
                    <div class="form-group">
                        <label>Additional Notes</label>
                        <div class="card">
                            <div class="card-body">
                                <p class="mb-0">{{ $assessment->notes }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($assessment->photos && count($assessment->photos) > 0)
                    <div class="form-group">
                        <label>Photos</label>
                        <div class="row">
                            @foreach($assessment->photos as $photo)
                            <div class="col-md-3 mb-3">
                                <div class="card">
                                    <img src="{{ Storage::url($photo) }}" 
                                         class="card-img-top" 
                                         style="height: 200px; object-fit: cover;"
                                         alt="Assessment Photo">
                                    <div class="card-body p-2">
                                        <small class="text-muted">Photo {{ $loop->iteration }}</small>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                @else
                    <div class="text-center py-5">
                        <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Assessment Not Started</h5>
                        <p class="text-muted">This section has not been assessed yet by the surveyor.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Survey Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <th width="100">Client:</th>
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
                    <tr>
                        <th>Section:</th>
                        <td>{{ $section->display_name }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Actions</h5>
            </div>
            <div class="card-body">
                <a href="{{ route('admin.survey.sections', $survey) }}" class="btn btn-secondary btn-block mb-2">
                    <i class="fas fa-arrow-left"></i> Back to Sections
                </a>
                <a href="{{ route('admin.surveys.show', $survey) }}" class="btn btn-primary btn-block">
                    <i class="fas fa-eye"></i> View Survey Details
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

