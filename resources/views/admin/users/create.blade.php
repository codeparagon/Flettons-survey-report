@extends('layouts.app')

@section('title', 'Create User')

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="page-header">
            <h2 class="pageheader-title">Create New User</h2>
            <div class="page-breadcrumb">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}" class="breadcrumb-link">Users</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Create</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-8 offset-xl-2">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">User Details</h5>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label for="name">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="role_id">Role <span class="text-danger">*</span></label>
                        <select class="form-control" id="role_id" name="role_id" required>
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                    {{ $role->display_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <small class="form-text text-muted">Minimum 8 characters</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create User
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
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
</style>
@endsection

