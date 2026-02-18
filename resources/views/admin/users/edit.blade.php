@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="users-form-hero">
            <h1 class="users-form-hero-title">Edit User</h1>
            <p class="users-form-hero-subtitle">Update {{ $user->name }}'s account</p>
            <nav aria-label="breadcrumb" class="users-form-breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
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
                    <i class="fas fa-user-edit"></i>
                </div>
                <h2 class="users-form-card-title">User Details</h2>
            </div>
            <div class="users-form-card-body">
                @if($errors->any())
                    <div class="users-form-alert">
                        <i class="fas fa-exclamation-circle"></i>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="users-form">
                    @csrf
                    @method('PUT')
                    
                    <div class="users-form-group">
                        <label for="name">Full Name <span class="required">*</span></label>
                        <input type="text" class="users-form-input" id="name" name="name" value="{{ old('name', $user->name) }}" placeholder="Enter full name" required>
                    </div>
                    
                    <div class="users-form-group">
                        <label for="email">Email Address <span class="required">*</span></label>
                        <input type="email" class="users-form-input" id="email" name="email" value="{{ old('email', $user->email) }}" placeholder="user@example.com" required>
                    </div>
                    
                    <div class="users-form-group">
                        <label for="role_id">Role <span class="required">*</span></label>
                        <select class="users-form-input" id="role_id" name="role_id" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ (old('role_id', $user->role_id) == $role->id) ? 'selected' : '' }}>
                                    {{ $role->display_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="users-form-section">
                        <p class="users-form-section-label">
                            <i class="fas fa-key"></i>
                            Change password <span class="text-muted">(leave blank to keep current)</span>
                        </p>
                        <div class="users-form-row">
                            <div class="users-form-group">
                                <label for="password">New Password</label>
                                <input type="password" class="users-form-input" id="password" name="password" placeholder="Min 8 characters">
                            </div>
                            <div class="users-form-group">
                                <label for="password_confirmation">Confirm New Password</label>
                                <input type="password" class="users-form-input" id="password_confirmation" name="password_confirmation" placeholder="Re-enter password">
                            </div>
                        </div>
                    </div>
                    
                    <div class="users-form-actions">
                        <button type="submit" class="users-form-btn users-form-btn-primary">
                            <i class="fas fa-save"></i> Update User
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="users-form-btn users-form-btn-secondary">
                            <i class="fas fa-arrow-left"></i> Cancel
                        </a>
                    </div>
                </form>
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

.users-form-alert {
    display: flex;
    gap: 1rem;
    padding: 1rem 1.25rem;
    margin-bottom: 1.5rem;
    background: #FEF2F2;
    border: 1px solid #FECACA;
    border-radius: 12px;
    color: #DC2626;
}
.users-form-alert i { flex-shrink: 0; margin-top: 2px; }
.users-form-alert ul { margin: 0; padding-left: 1.25rem; }

.users-form-section {
    margin: 2rem 0;
    padding: 1.5rem;
    background: #F8FAFC;
    border-radius: 12px;
    border: 1px solid #E2E8F0;
}
.users-form-section-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
    margin: 0 0 1rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.users-form-section-label .text-muted { font-weight: 400; color: #94A3B8; }

.users-form-group { margin-bottom: 1.5rem; }
.users-form-group:last-child { margin-bottom: 0; }
.users-form-group label {
    display: block;
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
}
.users-form-group .required { color: #DC2626; }
.users-form-input {
    width: 100%;
    padding: 0.75rem 1rem;
    font-size: 0.9375rem;
    border: 1px solid #E2E8F0;
    border-radius: 10px;
    background: #FFF;
    color: #1A202C;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.users-form-input:focus {
    outline: none;
    border-color: #C1EC4A;
    box-shadow: 0 0 0 3px rgba(193, 236, 74, 0.15);
}
.users-form-input::placeholder { color: #94A3B8; }

.users-form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
@media (max-width: 576px) { .users-form-row { grid-template-columns: 1fr; } }

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
