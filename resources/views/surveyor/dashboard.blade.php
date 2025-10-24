@extends('layouts.app')

@section('title', 'Surveyor Dashboard')

@php
    use Illuminate\Support\Str;
@endphp

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="page-header">
            <h2 class="pageheader-title">Surveyor Dashboard</h2>
            <p class="pageheader-text">Welcome, {{ auth()->user()->name ?? 'Surveyor' }}</p>
        </div>
    </div>
</div>

<div class="row">
    <!-- Total Jobs -->
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-inline-block">
                    <h5 class="text-muted">Total Jobs</h5>
                    <h2 class="mb-0">{{ $totalJobs }}</h2>
                </div>
                <div class="float-right icon-circle-medium" style="background: #1a202c;">
                    <i class="fa fa-clipboard-list fa-fw fa-sm" style="color: #c1ec4a;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Pending -->
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-inline-block">
                    <h5 class="text-muted">Pending</h5>
                    <h2 class="mb-0">{{ $pendingJobs }}</h2>
                </div>
                <div class="float-right icon-circle-medium" style="background: #1a202c;">
                    <i class="fa fa-clock fa-fw fa-sm" style="color: #c1ec4a;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- In Progress -->
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-inline-block">
                    <h5 class="text-muted">In Progress</h5>
                    <h2 class="mb-0">{{ $inProgressJobs }}</h2>
                </div>
                <div class="float-right icon-circle-medium" style="background: #1a202c;">
                    <i class="fa fa-tasks fa-fw fa-sm" style="color: #c1ec4a;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Completed -->
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-inline-block">
                    <h5 class="text-muted">Completed</h5>
                    <h2 class="mb-0">{{ $completedJobs }}</h2>
                </div>
                <div class="float-right icon-circle-medium" style="background: #1a202c;">
                    <i class="fa fa-check-circle fa-fw fa-sm" style="color: #c1ec4a;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Quick Actions -->
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <a href="{{ route('surveyor.surveys.index') }}" class="btn btn-primary">
                    <i class="fas fa-list"></i> View My Survey Jobs
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Jobs -->
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Recent Survey Jobs</h5>
            </div>
            <div class="card-body">
                @if($recentJobs->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Client</th>
                                    <th>Property</th>
                                    <th>Level</th>
                                    <th>Status</th>
                                    <th>Scheduled</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentJobs as $job)
                                    <tr>
                                        <td>{{ $job->id }}</td>
                                        <td>{{ $job->client_name }}</td>
                                        <td>{{ Str::limit($job->property_address_full, 30) }}</td>
                                        <td>
                                            <span class="badge badge-info">
                                                {{ $job->level ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $job->status_badge }}">
                                                {{ ucfirst($job->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $job->scheduled_date ? $job->scheduled_date->format('M d, Y') : 'TBD' }}</td>
                                        <td>
                                            <a href="{{ route('surveyor.surveys.show', $job->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No survey jobs assigned yet.</p>
                @endif
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
