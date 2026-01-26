@extends('layouts.app')

@section('title', 'Client Dashboard')

@section('content')
<style>
.dashboard-hero {
    background: linear-gradient(135deg, #1A202C 0%, #2D3748 100%);
    border-radius: 20px;
    padding: 2.5rem;
    margin-bottom: 2rem;
    color: white;
    position: relative;
    overflow: hidden;
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
    background: linear-gradient(90deg, #C1EC4A 0%, #A8D83A 100%);
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

.quick-action-btn {
    display: inline-block;
    padding: 1rem 2rem;
    border-radius: 12px;
    background: linear-gradient(135deg, #1A202C 0%, #2D3748 100%);
    color: white;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9375rem;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.quick-action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(26, 32, 44, 0.2);
    border-color: #C1EC4A;
    color: white;
    text-decoration: none;
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
}

.action-btn-view {
    background: #F3F4F6;
    color: #1A202C;
}

.action-btn-view:hover {
    background: #E5E7EB;
    color: #1A202C;
    text-decoration: none;
}

.action-btn-report {
    background: linear-gradient(135deg, #1A202C 0%, #2D3748 100%);
    color: white;
}

.action-btn-report:hover {
    background: linear-gradient(135deg, #2D3748 0%, #1A202C 100%);
    color: white;
    text-decoration: none;
}
</style>

<div class="dashboard-hero">
    <h1>Welcome back, {{ auth()->user()->name ?? explode('@', auth()->user()->email)[0] }}</h1>
    <p>Survey activity overview</p>
</div>

<div class="row mb-4">
    <!-- Total Surveys -->
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-4">
        <div class="stat-card">
            <div class="stat-card-value">{{ $totalSurveys }}</div>
            <div class="stat-card-label">Total Surveys</div>
            @if($completionRate > 0)
            <div class="stat-card-meta highlight">{{ $completionRate }}% completion rate</div>
            @else
            <div class="stat-card-meta">No surveys yet</div>
            @endif
        </div>
    </div>
    
    <!-- Pending -->
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-4">
        <div class="stat-card">
            <div class="stat-card-value">{{ $pendingSurveys }}</div>
            <div class="stat-card-label">Pending</div>
            @if($pendingSurveys > 0)
            <div class="stat-card-meta">Awaiting surveyor assignment</div>
            @else
            <div class="stat-card-meta">All surveys assigned</div>
            @endif
        </div>
    </div>
    
    <!-- Completed -->
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-4">
        <div class="stat-card">
            <div class="stat-card-value">{{ $completedSurveys }}</div>
            <div class="stat-card-label">Completed</div>
            @if($completedSurveys > 0)
            <div class="stat-card-meta highlight">Ready for review</div>
            @else
            <div class="stat-card-meta">No completed surveys</div>
            @endif
        </div>
    </div>
    
    <!-- Reports Available -->
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-4">
        <div class="stat-card">
            <div class="stat-card-value">{{ $reportsAvailable }}</div>
            <div class="stat-card-label">Reports Available</div>
            @if($reportsAvailable > 0)
            <div class="stat-card-meta highlight">Ready to download</div>
            @else
            <div class="stat-card-meta">No reports available</div>
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
            <div>
                <a href="{{ route('client.surveys.index') }}" class="quick-action-btn">View All Surveys</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Surveys -->
    <div class="col-xl-12">
        <div class="dashboard-section">
            <div class="dashboard-section-header">
                <h3 class="dashboard-section-title">Recent Surveys</h3>
            </div>
            <div>
                @if($recentSurveys->count() > 0)
                    @foreach($recentSurveys as $survey)
                        <div class="recent-survey-item">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <div class="survey-id">Survey #{{ $survey->id }}</div>
                                    <div class="survey-address">{{ Str::limit($survey->property_address_full ?? 'N/A', 50) }}</div>
                                </div>
                                <div class="col-md-2">
                                    <div style="font-size: 0.75rem; color: #6B7280; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Level</div>
                                    <span class="badge badge-info" style="background: #DBEAFE; color: #1E40AF; padding: 0.5rem 1rem; border-radius: 8px; font-weight: 600;">
                                        {{ str_replace('_', ' ', ucfirst($survey->level ?? 'N/A')) }}
                                    </span>
                                </div>
                                <div class="col-md-2">
                                    <div style="font-size: 0.75rem; color: #6B7280; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Status</div>
                                    <span class="survey-status-badge {{ $survey->status }}">
                                        {{ ucfirst($survey->status) }}
                                    </span>
                                </div>
                                <div class="col-md-2">
                                    <div style="font-size: 0.75rem; color: #6B7280; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Surveyor</div>
                                    <div style="font-size: 0.875rem; color: #1A202C; font-weight: 500;">
                                        {{ $survey->surveyor ? $survey->surveyor->name : 'Unassigned' }}
                                    </div>
                                </div>
                                <div class="col-md-2 text-right">
                                    <div style="display: flex; gap: 0.5rem; justify-content: flex-end; flex-wrap: wrap;">
                                        <a href="{{ route('client.surveys.show', $survey->id) }}" class="action-btn action-btn-view">View</a>
                                        @if($survey->status === 'completed')
                                        <a href="{{ route('client.surveys.report', $survey->id) }}" class="action-btn action-btn-report">Report</a>
                                        @endif
                                    </div>
                                    <div class="survey-meta" style="margin-top: 0.5rem;">
                                        {{ $survey->created_at->format('M d, Y') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="empty-state">
                        <h4>No surveys yet</h4>
                        <p>Contact admin to request a survey</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
