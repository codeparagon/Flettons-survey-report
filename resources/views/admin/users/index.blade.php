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
            <div class="card-body p-0">
                <x-datatable id="usersTable" :columns="['ID', 'Name', 'Email', 'Role', 'Status', 'Created', 'Actions']">
                    @foreach($users as $user)
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
                    @endforeach
                </x-datatable>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
/* SurvAI Branding for Buttons and Badges */
.btn-primary {
    background-color: #C1EC4A !important;
    border-color: #C1EC4A !important;
    color: #1A202C !important;
}

.btn-primary:hover {
    background-color: #B0D93F !important;
}

.btn-danger {
    background-color: #1A202C !important;
    border-color: #1A202C !important;
    color: #C1EC4A !important;
}

.btn-danger:hover {
    background-color: #2D3748 !important;
}

.btn-light {
    background-color: #1A202C !important;
    border-color: #1A202C !important;
    color: #C1EC4A !important;
}

.btn-light:hover {
    background-color: #2D3748 !important;
}

.badge-success {
    background-color: #C1EC4A !important;
    color: #1A202C !important;
}

.badge-secondary {
    background-color: #1A202C !important;
    color: #C1EC4A !important;
}
</style>
@endsection
