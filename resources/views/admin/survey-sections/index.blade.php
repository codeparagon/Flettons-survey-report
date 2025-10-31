@extends('layouts.app')

@section('title', 'Survey Sections')

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="page-header">
            <h2 class="pageheader-title">Survey Sections</h2>
            <div class="page-breadcrumb">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Survey Sections</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Survey Sections</h5>
                <a href="{{ route('admin.survey-sections.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add New Section
                </a>
            </div>
            <div class="card-body p-0">
                <x-datatable id="sectionsTable" :columns="['Section', 'Category', 'Type', 'Fields', 'Status', 'Actions']">
                    @foreach($sections as $section)
                        @php
                            $section->load('fields');
                            $fieldCount = $section->fields()->active()->count();
                            $generationMethod = $section->generation_method ?? 'database';
                        @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($section->icon)
                                        <span class="mr-2">
                                            @if(strpos($section->icon, 'storage/') !== false)
                                                <img src="{{ asset($section->icon) }}" alt="Icon" style="max-width: 24px; max-height: 24px; object-fit: contain;" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                                                <i class="fas fa-image text-primary" style="display:none;"></i>
                                            @else
                                                <i class="{{ $section->icon }}"></i>
                                            @endif
                                        </span>
                                    @endif
                                    <div>
                                        <strong>{{ $section->display_name }}</strong>
                                        <br><small class="text-muted">{{ $section->name }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($section->category)
                                    <span class="badge badge-info">{{ $section->category->display_name }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($generationMethod === 'database')
                                    <span class="badge badge-secondary">
                                        <i class="fas fa-database"></i> Database
                                    </span>
                                @elseif($generationMethod === 'ai')
                                    <span class="badge badge-success">
                                        <i class="fas fa-robot"></i> AI
                                    </span>
                                @else
                                    <span class="badge badge-secondary">—</span>
                                @endif
                            </td>
                            <td>
                                @if($fieldCount > 0)
                                    <span class="badge badge-primary">{{ $fieldCount }}</span>
                                @else
                                    <span class="badge badge-secondary">Default</span>
                                @endif
                            </td>
                            <td>
                                @if($section->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('admin.survey-sections.edit', $section) }}" class="btn btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('admin.survey-sections.show', $section) }}" class="btn btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.survey-sections.toggle-status', $section) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn {{ $section->is_active ? 'btn-secondary' : 'btn-success' }}" title="{{ $section->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="fas fa-{{ $section->is_active ? 'pause' : 'play' }}"></i>
                                        </button>
                                    </form>
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
