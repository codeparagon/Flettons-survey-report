@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="page-header">
            <h2 class="pageheader-title">User Management</h2>
            <div class="page-breadcrumb">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Users</li>
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
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">All Users</h5>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-plus"></i> Add New User
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge badge-pill" style="background: #1a202c; color: #c1ec4a;">
                                            {{ $user->role->display_name }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($user->status === 'active')
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.users.edit', $user->id) }}" 
                                               class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($user->id !== auth()->id())
                                                <form action="{{ route('admin.users.destroy', $user->id) }}" 
                                                      method="POST" 
                                                      onsubmit="return confirm('Are you sure?');"
                                                      class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No users found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    {{ $users->links() }}
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

.card-body .btn-light,
.btn-light,
a.btn-light,
button.btn-light {
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

.card-body .btn-light:hover,
.btn-light:hover,
a.btn-light:hover,
button.btn-light:hover {
    background-color: #2D3748 !important;
    border-color: #2D3748 !important;
    color: #C1EC4A !important;
    text-decoration: none !important;
    box-shadow: none !important;
}

.card-body .btn-danger,
.btn-danger,
a.btn-danger,
button.btn-danger {
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

.card-body .btn-danger:hover,
.btn-danger:hover,
a.btn-danger:hover,
button.btn-danger:hover {
    background-color: #2D3748 !important;
    border-color: #2D3748 !important;
    color: #C1EC4A !important;
    text-decoration: none !important;
    box-shadow: none !important;
}

/* Badge Styling */
.badge-success,
span.badge-success {
    background-color: #C1EC4A !important;
    color: #1A202C !important;
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
