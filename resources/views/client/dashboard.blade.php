@extends('layouts.app')

@section('title', 'Client Dashboard')

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="page-header">
            <h2 class="pageheader-title">Client Dashboard</h2>
            <p class="pageheader-text">Welcome, {{ auth()->user()->name ?? auth()->user()->email }}</p>
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
    
    <!-- Pending -->
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
    
    <!-- Reports Available -->
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-inline-block">
                    <h5 class="text-muted">Reports</h5>
                    <h2 class="mb-0">{{ $reportsAvailable }}</h2>
                </div>
                <div class="float-right icon-circle-medium" style="background: #1a202c;">
                    <i class="fa fa-file-pdf fa-fw fa-sm" style="color: #c1ec4a;"></i>
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
                <a href="{{ route('client.surveys.index') }}" class="btn btn-primary">
                    <i class="fas fa-list"></i> View My Surveys
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Surveys -->
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">My Recent Surveys</h5>
            </div>
            <div class="card-body">
                @if($recentSurveys->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
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
                                        <td>{{ $survey->surveyor ? $survey->surveyor->name : 'Pending' }}</td>
                                        <td>{{ $survey->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <a href="{{ route('client.surveys.show', $survey->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No surveys yet. Contact admin to request a survey.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
