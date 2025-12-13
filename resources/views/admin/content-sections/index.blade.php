@extends('layouts.app')

@section('title', 'Content Sections')

@push('styles')
<style>
    :root {
        --builder-primary: #1a202c;
        --builder-accent: #c1ec4a;
        --builder-success: #10b981;
        --builder-danger: #ef4444;
        --builder-warning: #f59e0b;
        --builder-border: #e5e7eb;
        --builder-bg: #f9fafb;
        --builder-hover: #f3f4f6;
    }

    .sections-container {
        max-width: 1400px;
        margin: 0 auto;
    }

    .page-header {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        margin-bottom: 24px !important;
        padding: 20px 24px !important;
        background: var(--builder-primary) !important;
        color: white !important;
        border-radius: 12px !important;
    }

    .page-title {
        color: var(--builder-accent)!important;
        font-size: 22px;
        font-weight: 700;
        margin: 0;
    }

    .page-subtitle {
        font-size: 14px;
        opacity: 0.8;
        margin-top: 4px;
    }

    .btn-builder {
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        padding: 10px 16px !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        font-size: 14px !important;
        cursor: pointer !important;
        transition: all 0.2s !important;
        border: none !important;
        text-decoration: none !important;
    }

    .btn-builder-primary {
        background: var(--builder-accent) !important;
        color: var(--builder-primary) !important;
    }

    .btn-builder-primary:hover {
        background: #a8d83a !important;
        color: var(--builder-primary) !important;
    }

    .btn-builder-secondary {
        background: rgba(255,255,255,0.1) !important;
        color: white !important;
        border: 1px solid rgba(255,255,255,0.2) !important;
    }
    
    .btn-builder-secondary:hover {
        background: rgba(255,255,255,0.2) !important;
        color: white !important;
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 12px;
    }

    .sections-table {
        background: white !important;
        border-radius: 12px !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1) !important;
        overflow: hidden !important;
    }

    .table-header {
        padding: 20px 24px;
        background: var(--builder-bg);
        border-bottom: 1px solid var(--builder-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .table-header h5 {
        margin: 0 !important;
        font-size: 16px !important;
        font-weight: 600 !important;
        color: var(--builder-primary) !important;
    }

    .table-responsive {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead {
        background: var(--builder-bg);
    }

    th {
        padding: 12px 16px !important;
        text-align: left !important;
        font-weight: 600 !important;
        font-size: 12px !important;
        text-transform: uppercase !important;
        color: #6b7280 !important;
        border-bottom: 2px solid var(--builder-border) !important;
    }

    td {
        padding: 16px !important;
        border-bottom: 1px solid var(--builder-border) !important;
        font-size: 14px !important;
        color: #374151 !important;
    }

    tbody tr:hover {
        background: var(--builder-hover) !important;
    }

    .badge {
        display: inline-block !important;
        padding: 4px 10px !important;
        border-radius: 6px !important;
        font-size: 11px !important;
        font-weight: 600 !important;
        text-transform: uppercase !important;
    }

    .badge-success {
        background: var(--builder-success) !important;
        color: white !important;
    }

    .badge-secondary {
        background: #9ca3af !important;
        color: white !important;
    }

    .badge-info {
        background: #3b82f6 !important;
        color: white !important;
    }

    .badge-warning {
        background: var(--builder-warning) !important;
        color: white !important;
    }

    .btn-group {
        display: flex;
        gap: 6px;
    }

    .btn {
        padding: 6px 12px !important;
        border-radius: 6px !important;
        border: none !important;
        cursor: pointer !important;
        font-size: 12px !important;
        transition: all 0.2s !important;
        text-decoration: none !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 4px !important;
    }

    .btn-info {
        background: #3b82f6 !important;
        color: white !important;
    }

    .btn-info:hover {
        background: #2563eb !important;
        color: white !important;
    }

    .btn-warning {
        background: var(--builder-warning) !important;
        color: white !important;
    }

    .btn-warning:hover {
        background: #d97706 !important;
        color: white !important;
    }

    .btn-danger {
        background: var(--builder-danger) !important;
        color: white !important;
    }

    .btn-danger:hover {
        background: #dc2626 !important;
        color: white !important;
    }

    .btn-success {
        background: var(--builder-success) !important;
        color: white !important;
    }

    .btn-success:hover {
        background: #059669 !important;
        color: white !important;
    }

    .btn-secondary {
        background: #6b7280 !important;
        color: white !important;
    }

    .btn-secondary:hover {
        background: #4b5563 !important;
        color: white !important;
    }

    .content-preview {
        max-width: 300px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .link-info {
        font-size: 12px !important;
        color: #6b7280 !important;
    }

    .text-muted {
        color: #6b7280 !important;
    }

    .empty-state {
        text-align: center !important;
        padding: 60px 20px !important;
        color: #9ca3af !important;
    }

    .empty-state-icon {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.5;
    }
</style>
@endpush

@section('content')
<div class="sections-container">
    <div class="page-header">
        <div>
            <h1 class="page-title">
                <i class="fas fa-file-alt mr-2"></i>
                Content Sections
            </h1>
            <p class="page-subtitle">Manage content sections that can be linked to survey categories or standalone</p>
        </div>
        <a href="{{ route('admin.content-sections.create') }}" class="btn-builder btn-builder-primary">
            <i class="fas fa-plus"></i> Create New Section
        </a>
    </div>

    <div class="sections-table">
        <div class="table-header">
            <h5>All Content Sections</h5>
        </div>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Link Type</th>
                        <th>Content Preview</th>
                        <th>Tags</th>
                        <th>Sort Order</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sections as $section)
                        <tr>
                            <td>{{ $section->id }}</td>
                            <td>
                                <strong>{{ $section->title }}</strong>
                            </td>
                            <td>
                                @if($section->subcategory_id)
                                    <span class="badge badge-info">Subcategory</span>
                                    <div class="link-info">
                                        {{ $section->subcategory->category->display_name ?? '' }} > 
                                        {{ $section->subcategory->display_name ?? '' }}
                                    </div>
                                @elseif($section->category_id)
                                    <span class="badge badge-warning">Category</span>
                                    <div class="link-info">
                                        {{ $section->category->display_name ?? '' }}
                                    </div>
                                @else
                                    <span class="badge badge-secondary">Standalone</span>
                                @endif
                            </td>
                            <td>
                                <div class="content-preview" title="{{ strip_tags($section->content) }}">
                                    {{ Str::limit(strip_tags($section->content), 100) }}
                                </div>
                            </td>
                            <td>
                                @if($section->tags && count($section->tags) > 0)
                                    @foreach(array_slice($section->tags, 0, 3) as $tag)
                                        <span class="badge badge-info">{{ $tag }}</span>
                                    @endforeach
                                    @if(count($section->tags) > 3)
                                        <span class="badge badge-secondary">+{{ count($section->tags) - 3 }}</span>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $section->sort_order }}</td>
                            <td>
                                @if($section->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.content-sections.show', $section) }}" 
                                       class="btn btn-info btn-sm" 
                                       title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.content-sections.edit', $section) }}" 
                                       class="btn btn-warning btn-sm" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.content-sections.toggle-status', $section) }}" 
                                          method="POST" 
                                          class="d-inline">
                                        @csrf
                                        <button type="submit" 
                                                class="btn btn-sm {{ $section->is_active ? 'btn-secondary' : 'btn-success' }}" 
                                                title="{{ $section->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="fas fa-{{ $section->is_active ? 'pause' : 'play' }}"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.content-sections.destroy', $section) }}" 
                                          method="POST" 
                                          class="d-inline" 
                                          onsubmit="return confirm('Are you sure you want to delete this content section? This action cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-danger btn-sm" 
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                    <h3>No Content Sections Found</h3>
                                    <p>Get started by creating your first content section.</p>
                                    <a href="{{ route('admin.content-sections.create') }}" class="btn-builder btn-builder-primary">
                                        <i class="fas fa-plus"></i> Create First Section
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($sections->hasPages())
            <div style="padding: 20px 24px; border-top: 1px solid var(--builder-border);">
                {{ $sections->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

