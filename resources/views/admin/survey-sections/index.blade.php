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
            <div class="card-header">
                <h5 class="mb-0">Manage Survey Sections</h5>
                <div class="card-header-right">
                    <a href="{{ route('admin.survey-sections.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Section
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Display Name</th>
                                <th>Category</th>
                                <th>Icon</th>
                                <th>Assessments</th>
                                <th>Sort Order</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sections as $section)
                                <tr>
                                    <td>{{ $section->id }}</td>
                                    <td>{{ $section->name }}</td>
                                    <td>{{ $section->display_name }}</td>
                                    <td>
                                        @if($section->category)
                                            <span class="badge badge-info">{{ $section->category->display_name }}</span>
                                        @else
                                            <span class="text-muted">No category</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($section->icon)
                                            @if(strpos($section->icon, 'storage/') !== false)
                                                <img src="{{ asset($section->icon) }}" alt="Section icon" style="max-width: 24px; max-height: 24px;">
                                                <br><small class="text-muted">Uploaded image</small>
                                            @else
                                                <i class="{{ $section->icon }}"></i> {{ $section->icon }}
                                            @endif
                                        @else
                                            <span class="text-muted">No icon</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-primary">{{ $section->assessments->count() }}</span>
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
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.survey-sections.show', $section) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.survey-sections.edit', $section) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.survey-sections.toggle-status', $section) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm {{ $section->is_active ? 'btn-secondary' : 'btn-success' }}">
                                                    <i class="fas fa-{{ $section->is_active ? 'pause' : 'play' }}"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.survey-sections.destroy', $section) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this section?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        No survey sections found.
                                        <a href="{{ route('admin.survey-sections.create') }}" class="btn btn-primary btn-sm ml-2">
                                            Create First Section
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $sections->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
