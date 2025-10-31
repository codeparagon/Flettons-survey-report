@extends('layouts.app')

@section('title', 'Survey Section Details')

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="page-header">
            <h2 class="pageheader-title">{{ $surveySection->display_name }}</h2>
            <div class="page-breadcrumb">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.survey-sections.index') }}">Survey Sections</a></li>
                        <li class="breadcrumb-item active">{{ $surveySection->display_name }}</li>
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
                <h5 class="mb-0">Section Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="200">Name:</th>
                        <td>{{ $surveySection->name }}</td>
                    </tr>
                    <tr>
                        <th>Display Name:</th>
                        <td>{{ $surveySection->display_name }}</td>
                    </tr>
                    <tr>
                        <th>Category:</th>
                        <td>
                            @if($surveySection->category)
                                <span class="badge badge-info">{{ $surveySection->category->display_name }}</span>
                            @else
                                <span class="text-muted">No category</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Icon:</th>
                        <td>
                            @if($surveySection->icon)
                                @if(strpos($surveySection->icon, 'storage/') !== false)
                                    <img src="{{ asset($surveySection->icon) }}" alt="Section icon" style="max-width: 48px; max-height: 48px;">
                                    <br><small class="text-muted">Uploaded image</small>
                                @else
                                    <i class="{{ $surveySection->icon }}"></i> {{ $surveySection->icon }}
                                @endif
                            @else
                                <span class="text-muted">No icon</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Description:</th>
                        <td>{{ $surveySection->description ?: 'No description' }}</td>
                    </tr>
                    <tr>
                        <th>Sort Order:</th>
                        <td>{{ $surveySection->sort_order }}</td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td>
                            @if($surveySection->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-secondary">Inactive</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Created:</th>
                        <td>{{ $surveySection->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Updated:</th>
                        <td>{{ $surveySection->updated_at->format('M d, Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        @if($surveySection->assessments->count() > 0)
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Recent Assessments</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Survey ID</th>
                                <th>Client</th>
                                <th>Rating</th>
                                <th>Status</th>
                                <th>Completed</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($surveySection->assessments->take(10) as $assessment)
                                <tr>
                                    <td>{{ $assessment->survey->id }}</td>
                                    <td>{{ $assessment->survey->client_name }}</td>
                                    <td>
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
                                    </td>
                                    <td>
                                        @if($assessment->is_completed)
                                            <span class="badge badge-success">Completed</span>
                                        @else
                                            <span class="badge badge-secondary">Pending</span>
                                        @endif
                                    </td>
                                    <td>{{ $assessment->completed_at ? $assessment->completed_at->format('M d, Y') : 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('admin.survey-section-assessments.show', $assessment) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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
                <a href="{{ route('admin.survey-sections.edit', $surveySection) }}" class="btn btn-primary btn-block mb-2">
                    <i class="fas fa-edit"></i> Edit Section
                </a>
                
                <form action="{{ route('admin.survey-sections.toggle-status', $surveySection) }}" method="POST" class="mb-2">
                    @csrf
                    <button type="submit" class="btn btn-block {{ $surveySection->is_active ? 'btn-warning' : 'btn-success' }}">
                        <i class="fas fa-{{ $surveySection->is_active ? 'pause' : 'play' }}"></i>
                        {{ $surveySection->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                </form>
                
                <a href="{{ route('admin.survey-section-assessments.index') }}?section={{ $surveySection->id }}" class="btn btn-info btn-block mb-2">
                    <i class="fas fa-clipboard-check"></i> View All Assessments
                </a>
                
                <a href="{{ route('admin.survey-sections.index') }}" class="btn btn-secondary btn-block">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <h3 class="text-primary">{{ $surveySection->assessments->count() }}</h3>
                        <p class="text-muted mb-0">Total Assessments</p>
                    </div>
                    <div class="col-6">
                        <h3 class="text-success">{{ $surveySection->assessments->where('is_completed', true)->count() }}</h3>
                        <p class="text-muted mb-0">Completed</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
