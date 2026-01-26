@extends('layouts.app')

@section('title', 'Super Admin Dashboard')

@section('content')
<style>
.dashboard-hero {
    background: linear-gradient(135deg, #1A202C 0%, #2D3748 100%);
    border-radius: 20px;
    padding: 2.5rem;
    margin-bottom: 2rem;
    color: white;
    position: relative;
    /* overflow: hidden; */
}

.dashboard-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(193, 236, 74, 0.15) 0%, transparent 70%);
    border-radius: 50%;
}

.dashboard-hero h1 {
    font-size: 2.25rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    position: relative;
    z-index: 1;
    color: white !important;
}

.dashboard-hero p {
    font-size: 1.125rem;
    opacity: 0.9;
    position: relative;
    z-index: 1;
}

.stat-card {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    height: 100%;
    transition: all 0.3s ease;
    border: 1px solid #E5E7EB;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
}

.stat-card.stat-primary::before {
    background: linear-gradient(90deg, #C1EC4A 0%, #A8D83A 100%);
}

.stat-card.stat-warning::before {
    background: linear-gradient(90deg, #F59E0B 0%, #D97706 100%);
}

.stat-card.stat-info::before {
    background: linear-gradient(90deg, #3B82F6 0%, #2563EB 100%);
}

.stat-card.stat-success::before {
    background: linear-gradient(90deg, #10B981 0%, #059669 100%);
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
}

.stat-card-value {
    font-size: 3rem;
    font-weight: 700;
    color: #1A202C;
    margin-bottom: 0.5rem;
    line-height: 1;
    letter-spacing: -0.02em;
}

.stat-card-label {
    font-size: 0.875rem;
    color: #6B7280;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-bottom: 0.75rem;
}

.stat-card-meta {
    font-size: 0.875rem;
    color: #6B7280;
    padding-top: 0.75rem;
    border-top: 1px solid #F3F4F6;
    line-height: 1.5;
}

.stat-card-meta.highlight {
    color: #10B981;
    font-weight: 600;
}

.stat-card-meta.warning {
    color: #F59E0B;
    font-weight: 600;
}

.dashboard-section {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 1.5rem;
    border: 1px solid #E5E7EB;
}

.dashboard-section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #F3F4F6;
}

.dashboard-section-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1A202C;
    margin: 0;
}

.quick-action-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.quick-action-btn {
    display: block;
    padding: 1rem 1.5rem;
    border-radius: 12px;
    background: white;
    border: 2px solid #E5E7EB;
    color: #1A202C;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9375rem;
    transition: all 0.3s ease;
    text-align: center;
}

.quick-action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    border-color: #C1EC4A;
    background: linear-gradient(135deg, #1A202C 0%, #2D3748 100%);
    color: white;
    text-decoration: none;
}

.recent-survey-item {
    padding: 1.25rem;
    border-radius: 12px;
    border: 1px solid #E5E7EB;
    margin-bottom: 0.75rem;
    transition: all 0.2s ease;
    background: #FAFBFC;
}

.recent-survey-item:hover {
    border-color: #C1EC4A;
    background: white;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    transform: translateX(4px);
}

.recent-survey-item:last-child {
    margin-bottom: 0;
}

.survey-status-badge {
    display: inline-block;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-size: 0.8125rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.survey-status-badge.pending {
    background: #FEF3C7;
    color: #92400E;
}

.survey-status-badge.completed {
    background: #D1FAE5;
    color: #065F46;
}

.survey-status-badge.in_progress {
    background: #DBEAFE;
    color: #1E40AF;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: #6B7280;
}

.empty-state h4 {
    color: #1A202C;
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.survey-meta {
    font-size: 0.875rem;
    color: #6B7280;
    margin-top: 0.5rem;
}

.survey-id {
    font-weight: 700;
    color: #1A202C;
    font-size: 1rem;
    margin-bottom: 0.5rem;
}

.survey-client {
    font-size: 0.875rem;
    color: #6B7280;
    margin-bottom: 0.5rem;
}

.survey-address {
    font-size: 0.875rem;
    color: #6B7280;
    line-height: 1.5;
}

.action-btn {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    border: none;
    font-size: 0.8125rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s ease;
    display: inline-block;
    background: #F3F4F6;
    color: #1A202C;
}

.action-btn:hover {
    background: #E5E7EB;
    color: #1A202C;
    text-decoration: none;
}

.field-label {
    font-size: 0.75rem;
    color: #6B7280;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    font-weight: 600;
}
</style>

<div class="dashboard-hero">
    <h1>Admin Dashboard</h1>
    <p>{{ config('app.name') }} - System Overview</p>
</div>

<div class="row mb-4">
    <!-- Total Surveys -->
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-4">
        <div class="stat-card stat-primary">
            <div class="stat-card-value">{{ $totalSurveys }}</div>
            <div class="stat-card-label">Total Surveys</div>
            @if($completionRate > 0)
            <div class="stat-card-meta highlight">{{ $completionRate }}% completion rate</div>
            @else
            <div class="stat-card-meta">No surveys yet</div>
            @endif
        </div>
    </div>
    
    <!-- Pending Surveys -->
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-4">
        <div class="stat-card stat-warning">
            <div class="stat-card-value">{{ $pendingSurveys }}</div>
            <div class="stat-card-label">Pending</div>
            @if($unassignedSurveys > 0)
            <div class="stat-card-meta warning">{{ $unassignedSurveys }} unassigned</div>
            @else
            <div class="stat-card-meta">All assigned</div>
            @endif
        </div>
    </div>
    
    <!-- In Progress -->
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-4">
        <div class="stat-card stat-info">
            <div class="stat-card-value">{{ $inProgressSurveys }}</div>
            <div class="stat-card-label">In Progress</div>
            @if($activeSurveyors > 0)
            <div class="stat-card-meta">{{ $activeSurveyors }} active surveyors</div>
            @else
            <div class="stat-card-meta">No active surveyors</div>
            @endif
        </div>
    </div>
    
    <!-- Completed -->
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-4">
        <div class="stat-card stat-success">
            <div class="stat-card-value">{{ $completedSurveys }}</div>
            <div class="stat-card-label">Completed</div>
            @if($completedSurveys > 0)
            <div class="stat-card-meta highlight">Ready for delivery</div>
            @else
            <div class="stat-card-meta">No completed surveys</div>
            @endif
        </div>
    </div>
</div>

<div class="row mb-4">
    <!-- Total Users -->
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-4">
        <div class="stat-card stat-primary">
            <div class="stat-card-value">{{ $totalUsers }}</div>
            <div class="stat-card-label">Total Users</div>
            <div class="stat-card-meta">{{ $activeUsers }} active users</div>
        </div>
    </div>
    
    <!-- Survey Levels -->
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-4">
        <div class="stat-card stat-info">
            <div class="stat-card-value">{{ $totalLevels }}</div>
            <div class="stat-card-label">Survey Levels</div>
            <div class="stat-card-meta">{{ $activeLevels }} active levels</div>
        </div>
    </div>
    
    <!-- Completion Rate -->
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-4">
        <div class="stat-card stat-success">
            <div class="stat-card-value">{{ $completionRate }}%</div>
            <div class="stat-card-label">Completion Rate</div>
            <div class="stat-card-meta">Overall performance</div>
        </div>
    </div>
    
    <!-- Unassigned Surveys -->
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-4">
        <div class="stat-card stat-warning">
            <div class="stat-card-value">{{ $unassignedSurveys }}</div>
            <div class="stat-card-label">Unassigned</div>
            @if($unassignedSurveys > 0)
            <div class="stat-card-meta warning">Needs assignment</div>
            @else
            <div class="stat-card-meta highlight">All assigned</div>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <!-- Quick Actions -->
    <div class="col-xl-12 mb-4">
        <div class="dashboard-section">
            <div class="dashboard-section-header">
                <h3 class="dashboard-section-title">Quick Actions</h3>
            </div>
            <div class="quick-action-grid">
                <a href="{{ route('admin.surveys.index') }}" class="quick-action-btn">Manage Surveys</a>
                <a href="{{ route('admin.users.index') }}" class="quick-action-btn">Manage Users</a>
                <a href="{{ route('admin.users.create') }}" class="quick-action-btn">Add New User</a>
                <a href="{{ route('admin.survey-levels.index') }}" class="quick-action-btn">Survey Levels</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Survey Jobs -->
    <div class="col-xl-12">
        <div class="dashboard-section">
            <div class="dashboard-section-header">
                <h3 class="dashboard-section-title">Recent Survey Jobs</h3>
            </div>
            <div>
                @if($recentSurveys->count() > 0)
                    @foreach($recentSurveys as $survey)
                        <div class="recent-survey-item">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <div class="survey-id">Survey #{{ $survey->id }}</div>
                                    <div class="survey-client">{{ $survey->client_name ?? 'N/A' }}</div>
                                    <div class="survey-address">{{ Str::limit($survey->property_address_full ?? 'N/A', 40) }}</div>
                                </div>
                                <div class="col-md-2">
                                    <div class="field-label">Level</div>
                                    <span class="badge badge-info" style="background: #DBEAFE; color: #1E40AF; padding: 0.5rem 1rem; border-radius: 8px; font-weight: 600;">
                                        {{ str_replace('_', ' ', ucfirst($survey->level ?? 'N/A')) }}
                                    </span>
                                </div>
                                <div class="col-md-2">
                                    <div class="field-label">Status</div>
                                    <span class="survey-status-badge {{ $survey->status }}">
                                        {{ ucfirst($survey->status) }}
                                    </span>
                                </div>
                                <div class="col-md-2">
                                    <div class="field-label">Surveyor</div>
                                    <div style="font-size: 0.875rem; color: #1A202C; font-weight: 500;">
                                        {{ $survey->surveyor ? $survey->surveyor->name : 'Unassigned' }}
                                    </div>
                                </div>
                                <div class="col-md-3 text-right">
                                    <div style="display: flex; gap: 0.5rem; justify-content: flex-end; margin-bottom: 0.5rem;">
                                        <a href="{{ route('admin.surveys.show', $survey->id) }}" class="action-btn">View Details</a>
                                    </div>
                                    <div class="survey-meta">
                                        {{ $survey->created_at->format('M d, Y') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="empty-state">
                        <h4>No survey applications yet</h4>
                        <p>Surveys from external platform will appear here</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
