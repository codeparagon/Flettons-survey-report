@extends('layouts.app')

@section('title', 'Edit Survey Section')

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="page-header">
            <h2 class="pageheader-title">Edit Survey Section</h2>
            <div class="page-breadcrumb">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.survey-sections.index') }}">Survey Sections</a></li>
                        <li class="breadcrumb-item active">Edit</li>
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
                <form action="{{ route('admin.survey-sections.update', $surveySection) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group">
                        <label for="name">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $surveySection->name) }}" 
                               placeholder="e.g., roofs, walls" required>
                        <small class="form-text text-muted">Internal name used in code (lowercase, no spaces)</small>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="display_name">Display Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('display_name') is-invalid @enderror" 
                               id="display_name" name="display_name" value="{{ old('display_name', $surveySection->display_name) }}" 
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
                                <option value="{{ $category->id }}" {{ old('survey_category_id', $surveySection->survey_category_id) == $category->id ? 'selected' : '' }}>
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
                                @if($surveySection->icon && strpos($surveySection->icon, 'storage/') !== false)
                                    <div class="mt-2">
                                        <small class="text-muted">Current icon:</small><br>
                                        <img src="{{ asset($surveySection->icon) }}" alt="Current icon" style="max-width: 32px; max-height: 32px;">
                                    </div>
                                @endif
                                @error('icon_file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Or Select FontAwesome Icon</label>
                                <select class="form-control @error('icon') is-invalid @enderror" id="icon" name="icon">
                                    <option value="">Select an icon</option>
                                    <option value="fas fa-home" {{ old('icon', $surveySection->icon) == 'fas fa-home' ? 'selected' : '' }}>🏠 Home</option>
                                    <option value="fas fa-building" {{ old('icon', $surveySection->icon) == 'fas fa-building' ? 'selected' : '' }}>🏢 Building</option>
                                    <option value="fas fa-warehouse" {{ old('icon', $surveySection->icon) == 'fas fa-warehouse' ? 'selected' : '' }}>🏭 Warehouse</option>
                                    <option value="fas fa-industry" {{ old('icon', $surveySection->icon) == 'fas fa-industry' ? 'selected' : '' }}>🏭 Industry</option>
                                    <option value="fas fa-hammer" {{ old('icon', $surveySection->icon) == 'fas fa-hammer' ? 'selected' : '' }}>🔨 Tools</option>
                                    <option value="fas fa-tools" {{ old('icon', $surveySection->icon) == 'fas fa-tools' ? 'selected' : '' }}>🔧 Tools</option>
                                    <option value="fas fa-cog" {{ old('icon', $surveySection->icon) == 'fas fa-cog' ? 'selected' : '' }}>⚙️ Settings</option>
                                    <option value="fas fa-tag" {{ old('icon', $surveySection->icon) == 'fas fa-tag' ? 'selected' : '' }}>🏷️ Tag</option>
                                    <option value="fas fa-tags" {{ old('icon', $surveySection->icon) == 'fas fa-tags' ? 'selected' : '' }}>🏷️ Tags</option>
                                    <option value="fas fa-list" {{ old('icon', $surveySection->icon) == 'fas fa-list' ? 'selected' : '' }}>📋 List</option>
                                    <option value="fas fa-clipboard-list" {{ old('icon', $surveySection->icon) == 'fas fa-clipboard-list' ? 'selected' : '' }}>📋 Clipboard</option>
                                    <option value="fas fa-check-circle" {{ old('icon', $surveySection->icon) == 'fas fa-check-circle' ? 'selected' : '' }}>✅ Check</option>
                                    <option value="fas fa-exclamation-triangle" {{ old('icon', $surveySection->icon) == 'fas fa-exclamation-triangle' ? 'selected' : '' }}>⚠️ Warning</option>
                                    <option value="fas fa-info-circle" {{ old('icon', $surveySection->icon) == 'fas fa-info-circle' ? 'selected' : '' }}>ℹ️ Info</option>
                                    <option value="fas fa-question-circle" {{ old('icon', $surveySection->icon) == 'fas fa-question-circle' ? 'selected' : '' }}>❓ Question</option>
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
                                  placeholder="Brief description of this section">{{ old('description', $surveySection->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="sort_order">Sort Order <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                               id="sort_order" name="sort_order" value="{{ old('sort_order', $surveySection->sort_order) }}" 
                               min="0" required>
                        <small class="form-text text-muted">Lower numbers appear first</small>
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
                                   value="1" {{ old('is_active', $surveySection->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                        </div>
                        <small class="form-text text-muted">Inactive sections won't be shown to users</small>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Section
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
                <h5 class="mb-0">Section Details</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th>Created:</th>
                        <td>{{ $surveySection->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Updated:</th>
                        <td>{{ $surveySection->updated_at->format('M d, Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Category:</th>
                        <td>
                            @if($surveySection->category)
                                <span class="badge badge-info">{{ $surveySection->category->display_name }}</span>
                            @else
                                <span class="text-muted">No category</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Assessments:</th>
                        <td>
                            <span class="badge badge-primary">{{ $surveySection->assessments->count() }}</span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.survey-sections.toggle-status', $surveySection) }}" method="POST" class="mb-2">
                    @csrf
                    <button type="submit" class="btn btn-block {{ $surveySection->is_active ? 'btn-warning' : 'btn-success' }}">
                        <i class="fas fa-{{ $surveySection->is_active ? 'pause' : 'play' }}"></i>
                        {{ $surveySection->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                </form>
                
                <form action="{{ route('admin.survey-sections.destroy', $surveySection) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this section? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-block btn-danger">
                        <i class="fas fa-trash"></i> Delete Section
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
