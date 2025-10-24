@extends('layouts.app')

@section('title', 'Survey Category Details')

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="page-header">
            <h2 class="pageheader-title">{{ $surveyCategory->display_name }}</h2>
            <div class="page-breadcrumb">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.survey-categories.index') }}">Survey Categories</a></li>
                        <li class="breadcrumb-item active">{{ $surveyCategory->display_name }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Category Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="200">Name:</th>
                        <td>{{ $surveyCategory->name }}</td>
                    </tr>
                    <tr>
                        <th>Display Name:</th>
                        <td>{{ $surveyCategory->display_name }}</td>
                    </tr>
                    <tr>
                        <th>Icon:</th>
                        <td>
                            @if($surveyCategory->icon)
                                <i class="{{ $surveyCategory->icon }}"></i> {{ $surveyCategory->icon }}
                            @else
                                <span class="text-muted">No icon</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Description:</th>
                        <td>{{ $surveyCategory->description ?: 'No description' }}</td>
                    </tr>
                    <tr>
                        <th>Sort Order:</th>
                        <td>{{ $surveyCategory->sort_order }}</td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td>
                            @if($surveyCategory->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-secondary">Inactive</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Created:</th>
                        <td>{{ $surveyCategory->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Updated:</th>
                        <td>{{ $surveyCategory->updated_at->format('M d, Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        @if($surveyCategory->sections->count() > 0)
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Sections in this Category</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Display Name</th>
                                <th>Icon</th>
                                <th>Sort Order</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($surveyCategory->sections as $section)
                                <tr>
                                    <td>{{ $section->name }}</td>
                                    <td>{{ $section->display_name }}</td>
                                    <td>
                                        @if($section->icon)
                                            <i class="{{ $section->icon }}"></i>
                                        @else
                                            <span class="text-muted">No icon</span>
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
                                        <a href="{{ route('admin.survey-sections.show', $section) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.survey-sections.edit', $section) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <a href="{{ route('admin.survey-categories.edit', $surveyCategory) }}" class="btn btn-primary btn-block mb-2">
                    <i class="fas fa-edit"></i> Edit Category
                </a>
                
                <form action="{{ route('admin.survey-categories.toggle-status', $surveyCategory) }}" method="POST" class="mb-2">
                    @csrf
                    <button type="submit" class="btn btn-block {{ $surveyCategory->is_active ? 'btn-warning' : 'btn-success' }}">
                        <i class="fas fa-{{ $surveyCategory->is_active ? 'pause' : 'play' }}"></i>
                        {{ $surveyCategory->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                </form>
                
                <a href="{{ route('admin.survey-sections.create') }}?category={{ $surveyCategory->id }}" class="btn btn-success btn-block mb-2">
                    <i class="fas fa-plus"></i> Add Section
                </a>
                
                <a href="{{ route('admin.survey-categories.index') }}" class="btn btn-secondary btn-block">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <h3 class="text-primary">{{ $surveyCategory->sections->count() }}</h3>
                        <p class="text-muted mb-0">Sections</p>
                    </div>
                    <div class="col-6">
                        <h3 class="text-success">{{ $surveyCategory->sections->where('is_active', true)->count() }}</h3>
                        <p class="text-muted mb-0">Active</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
