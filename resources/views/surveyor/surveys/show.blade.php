@extends('layouts.app')

@section('title', 'Survey Details')

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="page-header">
            <h2 class="pageheader-title">Survey #{{ $survey->id }}</h2>
            <div class="page-breadcrumb">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('surveyor.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('surveyor.surveys.index') }}">Surveys</a></li>
                        <li class="breadcrumb-item active">Survey #{{ $survey->id }}</li>
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
                <h5 class="mb-0">Survey Details</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="200">Client:</th>
                        <td>{{ $survey->client_name }}</td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td>{{ $survey->client_email }}</td>
                    </tr>
                    <tr>
                        <th>Phone:</th>
                        <td>{{ $survey->inf_field_Phone1 ?? $survey->telephone_number }}</td>
                    </tr>
                    <tr>
                        <th>Property:</th>
                        <td>{{ $survey->full_address }}</td>
                    </tr>
                    <tr>
                        <th>Survey Level:</th>
                        <td><span class="badge badge-info">{{ $survey->level ?? 'N/A' }}</span></td>
                    </tr>
                    <tr>
                        <th>Scheduled:</th>
                        <td>{{ $survey->scheduled_date ? $survey->scheduled_date->format('M d, Y') : 'Not Scheduled' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Update Status</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('surveyor.surveys.updateStatus', $survey->id) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Current Status:</label>
                        <div>
                            <span class="badge {{ $survey->status_badge }} p-2">
                                {{ $survey->status_label }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Update Status</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="assigned" {{ $survey->status == 'assigned' ? 'selected' : '' }}>Assigned</option>
                            <option value="in_progress" {{ $survey->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ $survey->status == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-check"></i> Update Status
                    </button>
                </form>
                
                <hr>
                
                @if($survey->surveyor_id === auth()->id())
                    
                    <!-- Add Media Button - Available for assigned, in_progress, and completed surveys -->
                    <a href="{{ route('surveyor.survey.media', $survey) }}" class="btn btn-outline-primary btn-block mb-2">
                        <i class="fas fa-camera"></i> Add Media
                    </a>
                
                    @if($survey->status === 'assigned')
                        <form action="{{ route('surveyor.surveys.start', $survey) }}" method="POST" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="fas fa-play"></i> Start Survey
                            </button>
                        </form>
                    @elseif($survey->status === 'in_progress')
                        <a href="{{ route('surveyor.survey.categories', $survey) }}" class="btn btn-primary btn-block mb-2">
                            <i class="fas fa-clipboard-list"></i> Continue Survey Categories
                        </a>
                    @elseif($survey->status === 'completed')
                        <a href="{{ route('surveyor.survey.categories', $survey) }}" class="btn btn-info btn-block mb-2">
                            <i class="fas fa-eye"></i> View Completed Survey
                        </a>
                    @endif
                    
                @elseif(!$survey->surveyor_id)
                    <form action="{{ route('surveyor.surveys.claim', $survey) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-warning btn-block">
                            <i class="fas fa-hand-paper"></i> Claim This Survey
                        </button>
                    </form>
                @endif
                
                <a href="{{ route('surveyor.surveys.index') }}" class="btn btn-secondary btn-block">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
/* SurvAI Branding for Buttons and Badges - High Specificity */
.card-body .btn-primary,
.btn-primary,
a.btn-primary,
button.btn-primary {
    background-color: #C1EC4A !important;
    border-color: #C1EC4A !important;
    color: #1A202C !important;
    font-weight: 600 !important;
    padding: 12px 20px !important;
    border-radius: 6px !important;
    display: inline-block !important;
    text-decoration: none !important;
    box-shadow: none !important;
}

.card-body .btn-primary:hover,
.btn-primary:hover,
a.btn-primary:hover,
button.btn-primary:hover {
    background-color: #B0D93F !important;
    border-color: #B0D93F !important;
    color: #1A202C !important;
    text-decoration: none !important;
    box-shadow: none !important;
}

.card-body .btn-success,
.btn-success,
a.btn-success,
button.btn-success {
    background-color: #1A202C !important;
    border-color: #1A202C !important;
    color: #C1EC4A !important;
    font-weight: 600 !important;
    padding: 12px 20px !important;
    border-radius: 6px !important;
    display: inline-block !important;
    text-decoration: none !important;
    box-shadow: none !important;
}

.card-body .btn-success:hover,
.btn-success:hover,
a.btn-success:hover,
button.btn-success:hover {
    background-color: #2D3748 !important;
    border-color: #2D3748 !important;
    color: #C1EC4A !important;
    text-decoration: none !important;
    box-shadow: none !important;
}

.card-body .btn-warning,
.btn-warning,
a.btn-warning,
button.btn-warning {
    background-color: #1A202C !important;
    border-color: #1A202C !important;
    color: #C1EC4A !important;
    font-weight: 600 !important;
    padding: 12px 20px !important;
    border-radius: 6px !important;
    display: inline-block !important;
    text-decoration: none !important;
    box-shadow: none !important;
}

.card-body .btn-warning:hover,
.btn-warning:hover,
a.btn-warning:hover,
button.btn-warning:hover {
    background-color: #2D3748 !important;
    border-color: #2D3748 !important;
    color: #C1EC4A !important;
    text-decoration: none !important;
    box-shadow: none !important;
}

.card-body .btn-info,
.btn-info,
a.btn-info,
button.btn-info {
    background-color: #C1EC4A !important;
    border-color: #C1EC4A !important;
    color: #1A202C !important;
    font-weight: 600 !important;
    padding: 12px 20px !important;
    border-radius: 6px !important;
    display: inline-block !important;
    text-decoration: none !important;
    box-shadow: none !important;
}

.card-body .btn-info:hover,
.btn-info:hover,
a.btn-info:hover,
button.btn-info:hover {
    background-color: #B0D93F !important;
    border-color: #B0D93F !important;
    color: #1A202C !important;
    text-decoration: none !important;
    box-shadow: none !important;
}

.card-body .btn-secondary,
.btn-secondary,
a.btn-secondary,
button.btn-secondary {
    background-color: #1A202C !important;
    border-color: #1A202C !important;
    color: #C1EC4A !important;
    font-weight: 600 !important;
    padding: 12px 20px !important;
    border-radius: 6px !important;
    display: inline-block !important;
    text-decoration: none !important;
    box-shadow: none !important;
}

.card-body .btn-secondary:hover,
.btn-secondary:hover,
a.btn-secondary:hover,
button.btn-secondary:hover {
    background-color: #2D3748 !important;
    border-color: #2D3748 !important;
    color: #C1EC4A !important;
    text-decoration: none !important;
    box-shadow: none !important;
}

/* Badge Styling */
.badge-info,
span.badge-info {
    background-color: #C1EC4A !important;
    color: #1A202C !important;
    font-weight: 600 !important;
    padding: 6px 12px !important;
    border-radius: 4px !important;
}

.badge-success,
span.badge-success {
    background-color: #C1EC4A !important;
    color: #1A202C !important;
    font-weight: 600 !important;
    padding: 6px 12px !important;
    border-radius: 4px !important;
}

.badge-warning,
span.badge-warning {
    background-color: #1A202C !important;
    color: #C1EC4A !important;
    font-weight: 600 !important;
    padding: 6px 12px !important;
    border-radius: 4px !important;
}

.badge-danger,
span.badge-danger {
    background-color: #1A202C !important;
    color: #C1EC4A !important;
    font-weight: 600 !important;
    padding: 6px 12px !important;
    border-radius: 4px !important;
}

.badge-secondary,
span.badge-secondary {
    background-color: #1A202C !important;
    color: #C1EC4A !important;
    font-weight: 600 !important;
    padding: 6px 12px !important;
    border-radius: 4px !important;
}
</style>
@endsection
