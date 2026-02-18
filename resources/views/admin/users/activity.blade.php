@extends('layouts.app')

@section('title', 'User Activity Log')

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="users-form-hero">
            <h1 class="users-form-hero-title">Activity Log</h1>
            <p class="users-form-hero-subtitle">View activity history for {{ $targetUser->name }}</p>
            <nav aria-label="breadcrumb" class="users-form-breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.show', $targetUser->id) }}">{{ $targetUser->name }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Activity</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-10 offset-xl-1">
        <div class="users-form-card">
            <div class="users-form-card-header">
                <div class="users-form-card-icon">
                    <i class="fas fa-history"></i>
                </div>
                <h2 class="users-form-card-title">User Activity History</h2>
            </div>
            <div class="users-form-card-body">
                <div class="users-activity-info">
                    <div class="users-activity-user">
                        <div class="users-activity-avatar">
                            @php 
                                $avatarVariant = ['a','b','c','d','e','f'][$targetUser->id % 6];
                                $initials = strtoupper(substr($targetUser->name, 0, 2));
                            @endphp
                            <div class="users-avatar users-avatar-{{ $avatarVariant }}">
                                {{ $initials }}
                            </div>
                        </div>
                        <div class="users-activity-user-info">
                            <h3>{{ $targetUser->name }}</h3>
                            <p>{{ $targetUser->email }}</p>
                            <span class="users-activity-role">
                                <i class="{{ $targetUser->role->icon ?? 'fas fa-user' }}"></i>
                                {{ $targetUser->role->display_name ?? 'No Role' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="users-activity-section">
                    <h4 class="users-activity-section-title">
                        <i class="fas fa-info-circle"></i>
                        Account Information
                    </h4>
                    <div class="users-activity-list">
                        <div class="users-activity-item">
                            <div class="users-activity-item-icon">
                                <i class="fas fa-calendar-plus"></i>
                            </div>
                            <div class="users-activity-item-content">
                                <div class="users-activity-item-title">Account Created</div>
                                <div class="users-activity-item-meta">{{ $targetUser->created_at->format('F d, Y \a\t h:i A') }}</div>
                                <div class="users-activity-item-time">{{ $targetUser->created_at->diffForHumans() }}</div>
                            </div>
                        </div>

                        @if($targetUser->email_verified_at)
                        <div class="users-activity-item">
                            <div class="users-activity-item-icon verified">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="users-activity-item-content">
                                <div class="users-activity-item-title">Email Verified</div>
                                <div class="users-activity-item-meta">{{ $targetUser->email_verified_at->format('F d, Y \a\t h:i A') }}</div>
                                <div class="users-activity-item-time">{{ $targetUser->email_verified_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        @endif

                        @if($targetUser->last_login_at)
                        <div class="users-activity-item">
                            <div class="users-activity-item-icon">
                                <i class="fas fa-sign-in-alt"></i>
                            </div>
                            <div class="users-activity-item-content">
                                <div class="users-activity-item-title">Last Login</div>
                                <div class="users-activity-item-meta">{{ $targetUser->last_login_at->format('F d, Y \a\t h:i A') }}</div>
                                <div class="users-activity-item-time">{{ $targetUser->last_login_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        @endif

                        <div class="users-activity-item">
                            <div class="users-activity-item-icon {{ $targetUser->status === 'active' ? 'active' : 'inactive' }}">
                                <i class="fas fa-{{ $targetUser->status === 'active' ? 'check' : 'times' }}-circle"></i>
                            </div>
                            <div class="users-activity-item-content">
                                <div class="users-activity-item-title">Account Status</div>
                                <div class="users-activity-item-meta">
                                    <span class="users-activity-status {{ $targetUser->status === 'active' ? 'active' : 'inactive' }}">
                                        {{ ucfirst($targetUser->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="users-activity-section">
                    <h4 class="users-activity-section-title">
                        <i class="fas fa-list"></i>
                        Activity Log
                    </h4>
                    <div class="users-activity-empty">
                        <i class="fas fa-inbox"></i>
                        <p>No activity logs available at this time.</p>
                        <small>Activity logging will be implemented in a future update.</small>
                    </div>
                </div>

                <div class="users-form-actions">
                    <a href="{{ route('admin.users.show', $targetUser->id) }}" class="users-form-btn users-form-btn-primary">
                        <i class="fas fa-user"></i> View User Details
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="users-form-btn users-form-btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Users
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* —— Users Form: Aesthetic Design —— */
.users-form-hero { margin-bottom: 2rem; padding: 1.75rem 0; }
.users-form-hero-title { font-size: 1.75rem; font-weight: 600; color: #1A202C; margin: 0 0 0.25rem 0; }
.users-form-hero-subtitle { font-size: 0.9375rem; color: #64748B; margin: 0 0 0.75rem 0; }
.users-form-breadcrumb .breadcrumb-item a { color: #64748B; }
.users-form-breadcrumb .breadcrumb-item.active { color: #1A202C; }

.users-form-card {
    background: #FFF;
    border-radius: 16px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 4px 12px rgba(0,0,0,0.03);
    border: 1px solid #E2E8F0;
    overflow: hidden;
}
.users-form-card-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem 1.75rem;
    background: linear-gradient(180deg, #FAFBFC 0%, #FFF 100%);
    border-bottom: 1px solid #E2E8F0;
}
.users-form-card-icon {
    width: 48px; height: 48px;
    border-radius: 12px;
    background: linear-gradient(135deg, #1A202C 0%, #2D3748 100%);
    color: #C1EC4A;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.25rem;
}
.users-form-card-title { font-size: 1.125rem; font-weight: 600; color: #1A202C; margin: 0; }
.users-form-card-body { padding: 1.75rem; }

.users-activity-info {
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #E2E8F0;
}

.users-activity-user {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.users-activity-avatar {
    flex-shrink: 0;
}

.users-avatar {
    width: 64px;
    height: 64px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: 700;
    color: #1A202C;
    border: 2px solid #FFF;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.users-avatar-a { background: linear-gradient(135deg, #DBEAFE 0%, #93C5FD 100%); }
.users-avatar-b { background: linear-gradient(135deg, #D1FAE5 0%, #6EE7B7 100%); }
.users-avatar-c { background: linear-gradient(135deg, #FEF3C7 0%, #FCD34D 100%); }
.users-avatar-d { background: linear-gradient(135deg, #FCE7F3 0%, #F9A8D4 100%); }
.users-avatar-e { background: linear-gradient(135deg, #E0E7FF 0%, #A5B4FC 100%); }
.users-avatar-f { background: linear-gradient(135deg, #FED7AA 0%, #FDBA74 100%); }

.users-activity-user-info h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1A202C;
    margin: 0 0 0.25rem 0;
}

.users-activity-user-info p {
    font-size: 0.9375rem;
    color: #64748B;
    margin: 0 0 0.75rem 0;
}

.users-activity-role {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.375rem 0.875rem;
    background: #F8FAFC;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 600;
    color: #1A202C;
}

.users-activity-section {
    margin-bottom: 2rem;
}

.users-activity-section-title {
    font-size: 1rem;
    font-weight: 600;
    color: #1A202C;
    margin: 0 0 1rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.users-activity-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.users-activity-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1rem;
    background: #F8FAFC;
    border-radius: 12px;
    border: 1px solid #E2E8F0;
    transition: all 0.2s ease;
}

.users-activity-item:hover {
    background: #FFF;
    border-color: #C1EC4A;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.users-activity-item-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: #E2E8F0;
    color: #64748B;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 1rem;
}

.users-activity-item-icon.verified {
    background: rgba(5, 150, 105, 0.1);
    color: #059669;
}

.users-activity-item-icon.active {
    background: rgba(5, 150, 105, 0.1);
    color: #059669;
}

.users-activity-item-icon.inactive {
    background: rgba(100, 116, 139, 0.1);
    color: #64748B;
}

.users-activity-item-content {
    flex: 1;
}

.users-activity-item-title {
    font-size: 0.9375rem;
    font-weight: 600;
    color: #1A202C;
    margin-bottom: 0.25rem;
}

.users-activity-item-meta {
    font-size: 0.875rem;
    color: #64748B;
    margin-bottom: 0.25rem;
}

.users-activity-item-time {
    font-size: 0.75rem;
    color: #94A3B8;
    font-style: italic;
}

.users-activity-status {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-size: 0.8125rem;
    font-weight: 600;
}

.users-activity-status.active {
    background: rgba(5, 150, 105, 0.1);
    color: #059669;
}

.users-activity-status.inactive {
    background: rgba(100, 116, 139, 0.1);
    color: #64748B;
}

.users-activity-empty {
    text-align: center;
    padding: 3rem 2rem;
    color: #94A3B8;
}

.users-activity-empty i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.users-activity-empty p {
    font-size: 1rem;
    font-weight: 500;
    color: #64748B;
    margin: 0 0 0.5rem 0;
}

.users-activity-empty small {
    font-size: 0.875rem;
    color: #94A3B8;
}

.users-form-actions {
    display: flex;
    gap: 0.75rem;
    margin-top: 1.75rem;
    padding-top: 1.5rem;
    border-top: 1px solid #E2E8F0;
}
.users-form-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    font-size: 0.9375rem;
    font-weight: 600;
    border-radius: 10px;
    border: 2px solid transparent;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s ease;
}
.users-form-btn-primary {
    background: #C1EC4A;
    color: #1A202C;
    border-color: #C1EC4A;
}
.users-form-btn-primary:hover {
    background: #A8D83A;
    border-color: #A8D83A;
    transform: translateY(-1px);
}
.users-form-btn-secondary {
    background: #1A202C;
    color: #C1EC4A;
    border-color: #1A202C;
}
.users-form-btn-secondary:hover {
    background: #2D3748;
    border-color: #2D3748;
    color: #C1EC4A;
    transform: translateY(-1px);
}
</style>
@endpush
