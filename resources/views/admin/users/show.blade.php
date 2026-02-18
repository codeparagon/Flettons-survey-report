@extends('layouts.app')

@section('title', 'View User')

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="users-form-hero">
            <h1 class="users-form-hero-title">User Details</h1>
            <p class="users-form-hero-subtitle">View {{ $user->name }}'s account information</p>
            <nav aria-label="breadcrumb" class="users-form-breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
                    <li class="breadcrumb-item active" aria-current="page">View</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-8 offset-xl-2">
        <div class="users-form-card">
            <div class="users-form-card-header">
                <div class="users-form-card-icon">
                    <i class="fas fa-user"></i>
                </div>
                <h2 class="users-form-card-title">User Information</h2>
            </div>
            <div class="users-form-card-body">
                <div class="users-show-section">
                    <div class="users-show-group">
                        <label class="users-show-label">Full Name</label>
                        <div class="users-show-value">{{ $user->name }}</div>
                    </div>
                    
                    <div class="users-show-group">
                        <label class="users-show-label">Email Address</label>
                        <div class="users-show-value">
                            <a href="mailto:{{ $user->email }}" class="users-show-link">{{ $user->email }}</a>
                            @if($user->email_verified_at)
                                <span class="users-show-badge verified">
                                    <i class="fas fa-check-circle"></i> Verified
                                </span>
                            @else
                                <span class="users-show-badge unverified">
                                    <i class="fas fa-times-circle"></i> Unverified
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="users-show-group">
                        <label class="users-show-label">Role</label>
                        <div class="users-show-value">
                            <span class="users-show-role">
                                <i class="{{ $user->role->icon ?? 'fas fa-user' }}"></i>
                                {{ $user->role->display_name ?? 'No Role' }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="users-show-group">
                        <label class="users-show-label">Status</label>
                        <div class="users-show-value">
                            @if($user->status === 'active')
                                <span class="users-show-status active">
                                    <span class="users-show-status-dot"></span>
                                    Active
                                </span>
                            @else
                                <span class="users-show-status inactive">
                                    <span class="users-show-status-dot"></span>
                                    Inactive
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="users-show-group">
                        <label class="users-show-label">User ID</label>
                        <div class="users-show-value users-show-id">{{ str_pad($user->id, 6, '0', STR_PAD_LEFT) }}</div>
                    </div>
                    
                    <div class="users-show-group">
                        <label class="users-show-label">Account Created</label>
                        <div class="users-show-value">
                            <div>{{ $user->created_at->format('F d, Y') }}</div>
                            <div class="users-show-meta">{{ $user->created_at->format('h:i A') }} • {{ $user->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                    
                    @if($user->last_login_at)
                    <div class="users-show-group">
                        <label class="users-show-label">Last Login</label>
                        <div class="users-show-value">
                            <div>{{ $user->last_login_at->format('F d, Y') }}</div>
                            <div class="users-show-meta">{{ $user->last_login_at->format('h:i A') }} • {{ $user->last_login_at->diffForHumans() }}</div>
                        </div>
                    </div>
                    @endif
                    
                    @if($user->phone)
                    <div class="users-show-group">
                        <label class="users-show-label">Phone</label>
                        <div class="users-show-value">
                            <a href="tel:{{ $user->phone }}" class="users-show-link">{{ $user->phone }}</a>
                        </div>
                    </div>
                    @endif
                    
                    @if($user->login_attempts > 0)
                    <div class="users-show-group">
                        <label class="users-show-label">Login Attempts</label>
                        <div class="users-show-value">
                            <span class="users-show-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                {{ $user->login_attempts }} failed attempts
                            </span>
                        </div>
                    </div>
                    @endif
                </div>
                
                <div class="users-form-actions">
                    <a href="{{ route('admin.users.edit', $user->id) }}" class="users-form-btn users-form-btn-primary">
                        <i class="fas fa-edit"></i> Edit User
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
.users-form-hero { margin-bottom: 2rem; padding: 1.5rem 0 0.5rem; }
.users-form-hero-title { font-size: 1.75rem; font-weight: 600; color: #1A202C; margin: 0 0 0.5rem 0; }
.users-form-hero-subtitle { font-size: 0.9375rem; color: #64748B; margin: 0 0 1rem 0; }
.users-form-breadcrumb { margin-top: 0.75rem; padding-top: 0.75rem; }
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
.users-form-card-body { padding: 2rem 1.75rem; }

.users-show-section {
    display: flex;
    flex-direction: column;
    gap: 1.75rem;
}

.users-show-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.users-show-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #64748B;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.users-show-value {
    font-size: 1rem;
    color: #1A202C;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.users-show-link {
    color: #1A202C;
    text-decoration: none;
    transition: color 0.2s;
}

.users-show-link:hover {
    color: #C1EC4A;
}

.users-show-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 600;
}

.users-show-badge.verified {
    background: rgba(5, 150, 105, 0.1);
    color: #059669;
}

.users-show-badge.unverified {
    background: rgba(220, 38, 38, 0.1);
    color: #DC2626;
}

.users-show-role {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: #F8FAFC;
    border-radius: 8px;
    font-weight: 600;
    color: #1A202C;
}

.users-show-status {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 50px;
    font-weight: 600;
    font-size: 0.875rem;
}

.users-show-status.active {
    background: rgba(5, 150, 105, 0.1);
    color: #059669;
}

.users-show-status.inactive {
    background: rgba(100, 116, 139, 0.1);
    color: #64748B;
}

.users-show-status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: currentColor;
}

.users-show-status.active .users-show-status-dot {
    background: #059669;
}

.users-show-status.inactive .users-show-status-dot {
    background: #64748B;
}

.users-show-id {
    font-family: 'SF Mono', 'Monaco', 'Inconsolata', 'Fira Code', monospace;
    background: #F8FAFC;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    display: inline-block;
}

.users-show-meta {
    font-size: 0.875rem;
    color: #94A3B8;
    margin-top: 0.25rem;
}

.users-show-warning {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: rgba(220, 38, 38, 0.1);
    color: #DC2626;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.875rem;
}

.users-form-actions {
    display: flex;
    gap: 0.75rem;
    margin-top: 2rem;
    padding-top: 1.75rem;
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
