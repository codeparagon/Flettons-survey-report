@extends('layouts.app')

@section('title', 'Super Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="page-header">
            <h2 class="pageheader-title">Super Admin Dashboard</h2>
            <p class="pageheader-text">Welcome to {{ config('app.name') }}</p>
            <div class="page-breadcrumb">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">Dashboard</a></li>
                        <li class="breadcrumb-item active">Overview</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Total Surveys -->
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-inline-block">
                    <h5 class="text-muted">Total Surveys</h5>
                    <h2 class="mb-0">{{ $totalSurveys }}</h2>
                </div>
                <div class="float-right icon-circle-medium" style="background: #1a202c;">
                    <i class="fa fa-clipboard-list fa-fw fa-sm" style="color: #c1ec4a;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Pending Surveys -->
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-inline-block">
                    <h5 class="text-muted">Pending</h5>
                    <h2 class="mb-0">{{ $pendingSurveys }}</h2>
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
                    <h2 class="mb-0">{{ $inProgressSurveys }}</h2>
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
                    <h2 class="mb-0">{{ $completedSurveys }}</h2>
                </div>
                <div class="float-right icon-circle-medium" style="background: #1a202c;">
                    <i class="fa fa-check-circle fa-fw fa-sm" style="color: #c1ec4a;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Survey Levels Stats -->
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-inline-block">
                    <h5 class="text-muted">Total Levels</h5>
                    <h2 class="mb-0">{{ $totalLevels }}</h2>
                </div>
                <div class="float-right icon-circle-medium" style="background: #1a202c;">
                    <i class="fa fa-layer-group fa-fw fa-sm" style="color: #c1ec4a;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Active Levels -->
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-inline-block">
                    <h5 class="text-muted">Active Levels</h5>
                    <h2 class="mb-0">{{ $activeLevels }}</h2>
                </div>
                <div class="float-right icon-circle-medium" style="background: #1a202c;">
                    <i class="fa fa-check-circle fa-fw fa-sm" style="color: #c1ec4a;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Empty columns for layout -->
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-inline-block">
                    <h5 class="text-muted">Quick Access</h5>
                    <a href="{{ route('admin.survey-levels.index') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-layer-group"></i> Manage Levels
                    </a>
                </div>
                <div class="float-right icon-circle-medium" style="background: #1a202c;">
                    <i class="fa fa-cog fa-fw fa-sm" style="color: #c1ec4a;"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-inline-block">
                    <h5 class="text-muted">System Status</h5>
                    <span class="badge badge-success">Dynamic Levels Active</span>
                </div>
                <div class="float-right icon-circle-medium" style="background: #1a202c;">
                    <i class="fa fa-server fa-fw fa-sm" style="color: #c1ec4a;"></i>
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
                <div class="row">
                    <div class="col-md-3">
                        <a href="{{ route('admin.surveys.index') }}" class="btn btn-primary btn-block">
                            <i class="fas fa-clipboard-list"></i> Manage Surveys
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary btn-block">
                            <i class="fas fa-users"></i> Manage Users
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.users.create') }}" class="btn btn-outline-secondary btn-block">
                            <i class="fas fa-user-plus"></i> Add New User
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.survey-levels.index') }}" class="btn btn-outline-secondary btn-block">
                            <i class="fas fa-layer-group"></i> Survey Levels
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="#" class="btn btn-outline-secondary btn-block">
                            <i class="fas fa-cog"></i> Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Survey Jobs -->
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Recent Survey Jobs</h5>
            </div>
            <div class="card-body">
                @if($recentSurveys->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Client</th>
                                    <th>Property</th>
                                    <th>Level</th>
                                    <th>Status</th>
                                    <th>Surveyor</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentSurveys as $survey)
                                    <tr>
                                        <td>{{ $survey->id }}</td>
                                        <td>{{ $survey->client_name }}</td>
                                        <td>{{ Str::limit($survey->property_address_full, 30) }}</td>
                                        <td>
                                            <span class="badge badge-info">
                                                {{ str_replace('_', ' ', ucfirst($survey->level)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $survey->status_badge }}">
                                                {{ ucfirst($survey->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $survey->surveyor ? $survey->surveyor->name : 'Unassigned' }}</td>
                                        <td>{{ $survey->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <a href="{{ route('admin.surveys.show', $survey->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No survey applications yet. Surveys from external platform will appear here.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

