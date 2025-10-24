@extends('layouts.app')

@section('title', 'Create Survey Section')

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="page-header">
            <h2 class="pageheader-title">Create Survey Section</h2>
            <div class="page-breadcrumb">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.survey-sections.index') }}">Survey Sections</a></li>
                        <li class="breadcrumb-item active">Create</li>
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
                <h5 class="mb-0">Section Information</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.survey-sections.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="form-group">
                        <label for="name">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" 
                               placeholder="e.g., roofs, walls" required>
                        <small class="form-text text-muted">Internal name used in code (lowercase, no spaces)</small>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="display_name">Display Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('display_name') is-invalid @enderror" 
                               id="display_name" name="display_name" value="{{ old('display_name') }}" 
                               placeholder="e.g., Roofs, Walls" required>
                        <small class="form-text text-muted">Name shown to users</small>
                        @error('display_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="survey_category_id">Category <span class="text-danger">*</span></label>
                        <select class="form-control @error('survey_category_id') is-invalid @enderror" 
                                id="survey_category_id" name="survey_category_id" required>
                            <option value="">Select a category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('survey_category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->display_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('survey_category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="icon">Icon</label>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Upload Icon Image</label>
                                <input type="file" class="form-control-file @error('icon_file') is-invalid @enderror" 
                                       id="icon_file" name="icon_file" accept="image/*">
                                <small class="form-text text-muted">Upload a custom icon image (PNG, JPG, SVG)</small>
                                @error('icon_file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Or Select FontAwesome Icon</label>
                                <select class="form-control @error('icon') is-invalid @enderror" id="icon" name="icon">
                                    <option value="">Select an icon</option>
                                    <option value="fas fa-home" {{ old('icon') == 'fas fa-home' ? 'selected' : '' }}>🏠 Home</option>
                                    <option value="fas fa-building" {{ old('icon') == 'fas fa-building' ? 'selected' : '' }}>🏢 Building</option>
                                    <option value="fas fa-warehouse" {{ old('icon') == 'fas fa-warehouse' ? 'selected' : '' }}>🏭 Warehouse</option>
                                    <option value="fas fa-industry" {{ old('icon') == 'fas fa-industry' ? 'selected' : '' }}>🏭 Industry</option>
                                    <option value="fas fa-hammer" {{ old('icon') == 'fas fa-hammer' ? 'selected' : '' }}>🔨 Tools</option>
                                    <option value="fas fa-tools" {{ old('icon') == 'fas fa-tools' ? 'selected' : '' }}>🔧 Tools</option>
                                    <option value="fas fa-cog" {{ old('icon') == 'fas fa-cog' ? 'selected' : '' }}>⚙️ Settings</option>
                                    <option value="fas fa-tag" {{ old('icon') == 'fas fa-tag' ? 'selected' : '' }}>🏷️ Tag</option>
                                    <option value="fas fa-tags" {{ old('icon') == 'fas fa-tags' ? 'selected' : '' }}>🏷️ Tags</option>
                                    <option value="fas fa-list" {{ old('icon') == 'fas fa-list' ? 'selected' : '' }}>📋 List</option>
                                    <option value="fas fa-clipboard-list" {{ old('icon') == 'fas fa-clipboard-list' ? 'selected' : '' }}>📋 Clipboard</option>
                                    <option value="fas fa-check-circle" {{ old('icon') == 'fas fa-check-circle' ? 'selected' : '' }}>✅ Check</option>
                                    <option value="fas fa-exclamation-triangle" {{ old('icon') == 'fas fa-exclamation-triangle' ? 'selected' : '' }}>⚠️ Warning</option>
                                    <option value="fas fa-info-circle" {{ old('icon') == 'fas fa-info-circle' ? 'selected' : '' }}>ℹ️ Info</option>
                                    <option value="fas fa-question-circle" {{ old('icon') == 'fas fa-question-circle' ? 'selected' : '' }}>❓ Question</option>
                                </select>
                                @error('icon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mt-2">
                            <label for="custom_icon">Custom Icon Class</label>
                            <input type="text" class="form-control @error('custom_icon') is-invalid @enderror" 
                                   id="custom_icon" name="custom_icon" value="{{ old('custom_icon') }}" 
                                   placeholder="e.g., fas fa-custom-icon">
                            <small class="form-text text-muted">Or enter a custom FontAwesome icon class</small>
                            @error('custom_icon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3" 
                                  placeholder="Brief description of this section">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="sort_order">Sort Order <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                               id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" 
                               min="0" required>
                        <small class="form-text text-muted">Lower numbers appear first</small>
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
                                   value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                        </div>
                        <small class="form-text text-muted">Inactive sections won't be shown to users</small>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Section
                        </button>
                        <a href="{{ route('admin.survey-sections.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Help</h5>
            </div>
            <div class="card-body">
                <h6>Section Name</h6>
                <p class="text-muted">Use lowercase letters, numbers, and underscores only. This is used internally in the system.</p>
                
                <h6>Display Name</h6>
                <p class="text-muted">This is what users will see. Use proper capitalization and spaces.</p>
                
                <h6>Category</h6>
                <p class="text-muted">Select which category this section belongs to. Categories help organize sections.</p>
                
                <h6>Icon</h6>
                <p class="text-muted">Use FontAwesome icon classes. Visit <a href="https://fontawesome.com/icons" target="_blank">FontAwesome</a> for available icons.</p>
                
                <h6>Sort Order</h6>
                <p class="text-muted">Sections with lower numbers appear first in lists. Use increments of 10 for easy reordering.</p>
            </div>
        </div>

        @if($categories->count() == 0)
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">No Categories</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">You need to create categories first before creating sections.</p>
                <a href="{{ route('admin.survey-categories.create') }}" class="btn btn-primary btn-block">
                    <i class="fas fa-plus"></i> Create Category
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
