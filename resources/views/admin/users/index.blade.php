@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="users-hero">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 users-hero-content">
                <div>
                    <h1 class="users-hero-title">User Management</h1>
                    <p class="users-hero-subtitle">Manage system users, their roles and access levels.</p>
                </div>
                <div class="users-hero-actions">
                    <a href="{{ route('admin.users.create') }}" class="users-btn-add">
                        <i class="fas fa-user-plus"></i>
                        <span>Create New User</span>
                    </a>
                </div>
            </div>
            <nav aria-label="breadcrumb" class="users-breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Users</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="users-card">
            <div class="users-card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div class="users-card-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <h2 class="users-card-title">All Users</h2>
                        <p class="users-card-subtitle mb-0">Overview of every account in the system.</p>
                    </div>
                </div>
                <div class="users-card-header-actions">
                    <button type="button" class="users-search-btn" id="header-search-toggle-usersTable" title="Search Users">
                        <i class="fas fa-search"></i>
                        <span>Search</span>
                    </button>
                </div>
            </div>
            <div class="users-card-body">
                <x-datatable 
                    id="usersTable" 
                    :columns="['Name', 'Email', 'Role', 'Status', 'Created', 'Actions']" 
                    :search="true" 
                    :filter="false"
                    :clickableRows="false"
                    pageLength="15">
                    @forelse($users as $user)
                        <tr class="users-row">
                            <td>
                                <div>
                                    <div class="users-name">{{ $user->name }}</div>
                                    <div class="users-meta">ID #{{ $user->id }}</div>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="users-role">{{ $user->role->display_name ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <span class="users-status users-status-{{ $user->status }}">
                                    <span class="users-status-dot"></span>
                                    <span class="users-status-label">{{ ucfirst($user->status) }}</span>
                                </span>
                            </td>
                            <td>{{ $user->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div class="users-actions">
                                    <a href="{{ route('admin.users.show', $user->id) }}" class="users-action" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="users-action" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($user->status === 'active')
                                        <form action="{{ route('admin.users.deactivate', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to deactivate this user?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="users-action" title="Deactivate">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.users.activate', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="users-action" title="Activate">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="users-action users-action-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 users-index-no-data">No users found.</td>
                        </tr>
                    @endforelse
                </x-datatable>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* —— Users: Simple, Intelligent Index —— */
:root {
    --users-dark: #111827;
    --users-muted: #6B7280;
    --users-border: #E5E7EB;
    --users-surface: #FFFFFF;
    --users-surface-soft: #F9FAFB;
    --users-accent: #C1EC4A;
    --users-accent-soft: #F3FEE6;
}

/* Hero */
.users-hero {
    margin-bottom: 1.25rem;
    padding: 0.75rem 0 0.25rem;
}

.users-hero-content {
    margin-bottom: 0.75rem;
}

.users-hero-title {
    font-size: 1.6rem;
    font-weight: 600;
    letter-spacing: -0.02em;
    color: var(--users-dark);
    margin-bottom: 0.25rem;
}

.users-hero-subtitle {
    font-size: 0.9rem;
    color: var(--users-muted);
    margin-bottom: 0;
}

.users-hero-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.users-btn-add {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.6rem 1.25rem;
    border-radius: 999px;
    border: 1px solid var(--users-accent);
    background: var(--users-surface);
    color: var(--users-dark);
    font-size: 0.9rem;
    font-weight: 600;
    text-decoration: none;
    transition: background 0.16s ease, color 0.16s ease, border-color 0.16s ease;
}

.users-btn-add:hover {
    background: var(--users-accent-soft);
    color: var(--users-dark);
    text-decoration: none;
}

.users-breadcrumb {
    margin-top: 0.5rem;
    padding-top: 0.5rem;
}

.users-breadcrumb .breadcrumb-item a {
    color: var(--users-muted);
}

.users-breadcrumb .breadcrumb-item.active {
    color: var(--users-dark);
}

/* Card */
.users-card {
    background: var(--users-surface);
    border-radius: 12px;
    border: 1px solid var(--users-border);
    overflow: hidden;
}

.users-card-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--users-border);
    background: var(--users-surface-soft);
}

.users-card-header-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.users-search-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    border: 1px solid var(--users-border);
    background: var(--users-surface);
    color: var(--users-dark);
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.15s ease;
}

.users-search-btn:hover {
    background: var(--users-surface-soft);
    border-color: var(--users-accent);
    color: var(--users-dark);
}

.users-search-btn.active {
    background: var(--users-accent-soft);
    border-color: var(--users-accent);
    color: var(--users-dark);
}

.users-search-btn i {
    font-size: 0.875rem;
}

.users-card-icon {
    width: 36px;
    height: 36px;
    border-radius: 999px;
    border: 1px solid var(--users-border);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.95rem;
    color: var(--users-dark);
    background: var(--users-surface);
}

.users-card-title {
    font-size: 1rem;
    font-weight: 600;
    color: var(--users-dark);
    margin-bottom: 0.15rem;
}

.users-card-subtitle {
    font-size: 0.8rem;
    color: var(--users-muted);
    margin-bottom: 0;
}

.users-card-body {
    padding: 1rem 1.25rem;
}

.users-card-body .datatable-container {
    padding: 0 !important;
    margin: 0;
}

/* Hide datatable header with old search button */
.users-card-body .datatable-header {
    display: none !important;
}

/* Table rows */
.users-row td {
    vertical-align: middle;
    font-size: 0.9rem;
    padding: 0.75rem 0.75rem;
}

.users-row:hover {
    background: var(--users-surface-soft) !important;
}

/* Name */
.users-name {
    font-size: 0.93rem;
    font-weight: 600;
    color: var(--users-dark);
    margin-bottom: 0.15rem;
}

.users-meta {
    font-size: 0.75rem;
    color: var(--users-muted);
}

/* Role pill */
.users-role {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.6rem;
    border-radius: 999px;
    background: var(--users-surface-soft);
    color: var(--users-dark);
    font-size: 0.78rem;
    font-weight: 500;
    border: 1px solid rgba(148, 163, 184, 0.4);
}

/* Status chip */
.users-status {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.2rem 0.55rem;
    border-radius: 999px;
    font-size: 0.78rem;
    font-weight: 500;
}

.users-status-dot {
    width: 6px;
    height: 6px;
    border-radius: 999px;
}

.users-status-active {
    background: var(--users-accent-soft);
    color: #166534;
}

.users-status-active .users-status-dot {
    background: #16A34A;
}

.users-status-inactive {
    background: #FEF2F2;
    color: #B91C1C;
}

.users-status-inactive .users-status-dot {
    background: #EF4444;
}

/* Actions */
.users-actions {
    display: flex;
    gap: 0.35rem;
    align-items: center;
}

.users-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: 6px;
    border: 1px solid var(--users-border);
    background: var(--users-surface);
    color: var(--users-muted);
    font-size: 0.8rem;
    cursor: pointer;
    padding: 0;
    transition: background 0.15s ease, color 0.15s ease, border-color 0.15s ease;
}

.users-action:hover {
    background: var(--users-surface-soft);
    border-color: var(--users-accent);
    color: var(--users-dark);
}

.users-action-danger:hover {
    background: #FEF2F2;
    border-color: #FCA5A5;
    color: #B91C1C;
}

.users-action i {
    pointer-events: none;
}

/* Empty state */
.users-index-no-data {
    font-size: 0.9rem;
    color: var(--users-muted);
}

/* Responsive */
@media (max-width: 768px) {
    .users-hero {
        padding: 0.5rem 0 0.25rem;
        margin-bottom: 1rem;
    }

    .users-card-header {
        padding: 0.875rem 1rem;
    }

    .users-card-body {
        padding: 0.875rem 1rem;
    }

    .users-row td {
        padding: 0.625rem 0.5rem;
    }

    .users-btn-add {
        width: 100%;
        justify-content: center;
    }

    .users-actions {
        flex-wrap: wrap;
    }

    .users-card-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .users-card-header-actions {
        width: 100%;
        justify-content: flex-end;
    }

    .users-search-btn {
        width: 100%;
        justify-content: center;
    }
}

</style>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Connect header search button directly to search panel toggle
    const headerSearchBtn = document.getElementById('header-search-toggle-usersTable');
    const searchPanel = document.getElementById('search-panel-usersTable');
    const searchInput = document.getElementById('table-search-input-usersTable');
    const searchClear = document.getElementById('search-clear-usersTable');
    
    if (headerSearchBtn && searchPanel) {
        // Toggle search panel when header button is clicked
        headerSearchBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const isActive = searchPanel.classList.toggle('active');
            headerSearchBtn.classList.toggle('active', isActive);
            
            // Focus search input when panel opens
            if (isActive && searchInput) {
                setTimeout(() => searchInput.focus(), 100);
            }
        });
        
        // Sync header button state with search panel (for external toggles)
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    const isActive = searchPanel.classList.contains('active');
                    headerSearchBtn.classList.toggle('active', isActive);
                }
            });
        });
        
        observer.observe(searchPanel, {
            attributes: true,
            attributeFilter: ['class']
        });
    }
});
</script>
@endpush
