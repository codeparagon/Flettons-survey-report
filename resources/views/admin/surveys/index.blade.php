@extends('layouts.app')

@section('title', 'Survey Management')

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="page-header">
            <h2 class="pageheader-title">Survey Management</h2>
            <p class="pageheader-text">Manage survey applications and assign surveyors</p>
            <div class="page-breadcrumb">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Surveys</li>
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
                <h5 class="mb-0">All Survey Applications</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Client Name</th>
                                <th>Email</th>
                                <th>Property Address</th>
                                <th>Level</th>
                                <th>Status</th>
                                <th>Surveyor</th>
                                <th>Scheduled</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($surveys as $survey)
                                <tr>
                                    <td>{{ $survey->id }}</td>
                                    <td>{{ $survey->client_name }}</td>
                                    <td>{{ $survey->client_email }}</td>
                                    <td>{{ Str::limit($survey->property_address_full, 40) }}</td>
                                    <td>
                                        <span class="badge badge-info">
                                            {{ $survey->level ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $survey->status_badge }}">
                                            {{ ucfirst($survey->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $survey->surveyor ? $survey->surveyor->name : 'Unassigned' }}</td>
                                    <td>{{ $survey->scheduled_date ? $survey->scheduled_date->format('M d, Y') : 'TBD' }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.surveys.show', $survey->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.surveys.edit', $survey->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        No survey applications yet. Surveys from external platform will appear here.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    {{ $surveys->links() }}
                </div>
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

.card-body .btn-info,
.btn-info,
a.btn-info,
button.btn-info {
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

.card-body .btn-info:hover,
.btn-info:hover,
a.btn-info:hover,
button.btn-info:hover {
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

/* Small button styling */
.btn-sm {
    padding: 8px 16px !important;
    font-size: 14px !important;
}
</style>
@endsection
