@extends('layouts.app')

@section('title', 'View Content Section')

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
    }

    .section-container {
        max-width: 1000px;
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

    .section-card {
        background: white !important;
        border-radius: 12px !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1) !important;
        overflow: hidden !important;
        margin-bottom: 24px !important;
    }

    .section-header {
        padding: 20px 24px !important;
        background: var(--builder-bg) !important;
        border-bottom: 1px solid var(--builder-border) !important;
    }

    .section-header h3 {
        color: var(--builder-primary) !important;
        margin: 0 !important;
    }

    .section-body {
        padding: 24px;
    }

    .section-meta {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
        padding: 16px;
        background: var(--builder-bg);
        border-radius: 8px;
    }

    .meta-item {
        display: flex;
        flex-direction: column;
    }

    .meta-label {
        font-size: 12px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        margin-bottom: 4px;
    }

    .meta-value {
        font-size: 14px;
        color: #374151;
        font-weight: 500;
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

    .content-display {
        padding: 20px;
        background: var(--builder-bg);
        border-radius: 8px;
        border: 1px solid var(--builder-border);
        white-space: pre-wrap;
        word-wrap: break-word;
        line-height: 1.6;
        color: #374151;
    }

    .tags-list {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
</style>
@endpush

@section('content')
<div class="section-container">
    <div class="page-header">
        <div>
            <h1 class="page-title">
                <i class="fas fa-file-alt mr-2"></i>
                Content Section Details
            </h1>
        </div>
        <div>
            <a href="{{ route('admin.content-sections.index') }}" class="btn-builder btn-builder-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
            <a href="{{ route('admin.content-sections.edit', $contentSection) }}" class="btn-builder btn-builder-primary">
                <i class="fas fa-edit"></i> Edit
            </a>
        </div>
    </div>

    <div class="section-card">
        <div class="section-header">
            <h3 style="margin: 0; color: var(--builder-primary);">{{ $contentSection->title }}</h3>
        </div>
        <div class="section-body">
            <div class="section-meta">
                <div class="meta-item">
                    <span class="meta-label">ID</span>
                    <span class="meta-value">#{{ $contentSection->id }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Status</span>
                    <span class="meta-value">
                        @if($contentSection->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-secondary">Inactive</span>
                        @endif
                    </span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Link Type</span>
                    <span class="meta-value">
                        @if($contentSection->subcategory_id)
                            <span class="badge badge-info">Subcategory</span>
                        @elseif($contentSection->category_id)
                            <span class="badge badge-warning">Category</span>
                        @else
                            <span class="badge badge-secondary">Standalone</span>
                        @endif
                    </span>
                </div>
                @if($contentSection->category_id || $contentSection->subcategory_id)
                    <div class="meta-item">
                        <span class="meta-label">Linked To</span>
                        <span class="meta-value">
                            @if($contentSection->subcategory_id)
                                {{ $contentSection->subcategory->category->display_name ?? '' }} > 
                                {{ $contentSection->subcategory->display_name ?? '' }}
                            @elseif($contentSection->category_id)
                                {{ $contentSection->category->display_name ?? '' }}
                            @endif
                        </span>
                    </div>
                @endif
                <div class="meta-item">
                    <span class="meta-label">Sort Order</span>
                    <span class="meta-value">{{ $contentSection->sort_order }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Created</span>
                    <span class="meta-value">{{ $contentSection->created_at->format('M d, Y H:i') }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Updated</span>
                    <span class="meta-value">{{ $contentSection->updated_at->format('M d, Y H:i') }}</span>
                </div>
            </div>

            @if($contentSection->tags && count($contentSection->tags) > 0)
                <div style="margin-bottom: 24px;">
                    <span class="meta-label" style="display: block; margin-bottom: 8px;">Tags</span>
                    <div class="tags-list">
                        @foreach($contentSection->tags as $tag)
                            <span class="badge badge-info">{{ $tag }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            <div>
                <span class="meta-label" style="display: block; margin-bottom: 8px;">Content</span>
                <div class="content-display">
                    {{ $contentSection->content }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

