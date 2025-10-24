@extends('layouts.app')

@section('title', 'Edit Survey Level')

@push('styles')
<style>
    .form-section {
        background: white;
        border-radius: 12px;
        padding: 32px;
        margin-bottom: 32px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
    }

    .section-title {
        color: #1a202c;
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 24px;
        padding-bottom: 12px;
        border-bottom: 3px solid #1a202c;
        position: relative;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: -3px;
        left: 0;
        width: 60px;
        height: 3px;
        background: #c1ec4a;
    }

    .sections-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    .section-item {
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 20px;
        cursor: pointer;
        transition: all 0.3s ease;
        background: white;
        position: relative;
    }

    .section-item:hover {
        border-color: #1a202c;
        background: #f9fafb;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .section-item.selected {
        border-color: #1a202c;
        background: #f0f9ff;
        box-shadow: 0 4px 12px rgba(26, 32, 44, 0.15);
    }

    .section-item.selected::before {
        content: '✓';
        position: absolute;
        top: 12px;
        right: 12px;
        background: #1a202c;
        color: #c1ec4a;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: bold;
    }

    .section-item input[type="checkbox"] {
        display: none;
    }

    .section-name {
        font-weight: 700;
        color: #1a202c;
        margin-bottom: 8px;
        font-size: 16px;
    }

    .section-description {
        font-size: 14px;
        color: #6b7280;
        line-height: 1.5;
    }

    .form-group {
        margin-bottom: 24px;
    }

    .form-label {
        font-weight: 600;
        color: #1a202c;
        margin-bottom: 8px;
        font-size: 15px;
    }

    .form-control {
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 15px;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        border-color: #1a202c;
        box-shadow: 0 0 0 3px rgba(26, 32, 44, 0.1);
    }

    .form-control.is-invalid {
        border-color: #ef4444;
    }

    .invalid-feedback {
        font-size: 14px;
        font-weight: 500;
        margin-top: 6px;
    }

    .page-header {
        margin-bottom: 32px;
    }

    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: #1a202c;
        margin-bottom: 8px;
    }

    .btn-secondary {
        background: #6b7280;
        color: white;
        border: 2px solid #6b7280;
        padding: 12px 24px;
        font-size: 15px;
        font-weight: 600;
        border-radius: 10px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
    }

    .btn-secondary:hover {
        background: #4b5563;
        color: white;
        border-color: #4b5563;
        transform: translateY(-1px);
    }

    .btn-primary {
        background: #1a202c;
        color: #c1ec4a;
        border: 2px solid #1a202c;
        padding: 12px 24px;
        font-size: 15px;
        font-weight: 600;
        border-radius: 10px;
        transition: all 0.2s ease;
    }

    .btn-primary:hover {
        background: #2d3748;
        color: #c1ec4a;
        border-color: #2d3748;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(26, 32, 44, 0.3);
    }

    .form-actions {
        background: #f9fafb;
        border-radius: 12px;
        padding: 24px;
        margin-top: 32px;
        border: 1px solid #e5e7eb;
    }

    .text-muted {
        color: #6b7280 !important;
        font-size: 15px;
        line-height: 1.5;
    }

    .text-danger {
        color: #ef4444 !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="page-title">Edit Survey Level</h1>
                    <a href="{{ route('admin.survey-levels.index') }}" class="btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Levels
                    </a>
                </div>
            </div>

            <form action="{{ route('admin.survey-levels.update', $surveyLevel) }}" 
                  method="POST">
                @csrf
                @method('PUT')

                <!-- Basic Information -->
                <div class="form-section">
                    <h4 class="section-title">Basic Information</h4>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="form-label">Level Name <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $surveyLevel->name ?? '') }}" 
                                       placeholder="e.g., Level 1, Level 2">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="display_name" class="form-label">Display Name <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('display_name') is-invalid @enderror" 
                                       id="display_name" 
                                       name="display_name" 
                                       value="{{ old('display_name', $surveyLevel->display_name ?? '') }}" 
                                       placeholder="e.g., Basic Survey, Standard Survey">
                                @error('display_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sort_order" class="form-label">Sort Order <span class="text-danger">*</span></label>
                                <input type="number" 
                                       class="form-control @error('sort_order') is-invalid @enderror" 
                                       id="sort_order" 
                                       name="sort_order" 
                                       value="{{ old('sort_order', $surveyLevel->sort_order ?? 0) }}" 
                                       min="0">
                                @error('sort_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="is_active" class="form-label">Status</label>
                                <select class="form-control @error('is_active') is-invalid @enderror" 
                                        id="is_active" 
                                        name="is_active">
                                    <option value="1" {{ old('is_active', $surveyLevel->is_active ?? true) ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ !old('is_active', $surveyLevel->is_active ?? true) ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('is_active')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="3" 
                                  placeholder="Describe what this survey level covers...">{{ old('description', $surveyLevel->description ?? '') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Section Assignment -->
                <div class="form-section">
                    <h4 class="section-title">Assign Sections</h4>
                    <p class="text-muted">Select the sections that should be included in this survey level. You can drag to reorder them.</p>
                    
                    <div class="sections-grid">
                        @foreach($sections as $section)
                            <div class="section-item" onclick="toggleSection({{ $section->id }})">
                                <input type="checkbox" 
                                       name="sections[]" 
                                       value="{{ $section->id }}" 
                                       id="section_{{ $section->id }}"
                                       {{ in_array($section->id, old('sections', $surveyLevel->sections->pluck('id')->toArray() ?? [])) ? 'checked' : '' }}>
                                <div class="section-name">{{ $section->display_name }}</div>
                                <div class="section-description">{{ $section->description }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <div class="d-flex justify-content-end gap-3">
                        <a href="{{ route('admin.survey-levels.index') }}" class="btn-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> Update Level
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleSection(sectionId) {
    const checkbox = document.getElementById('section_' + sectionId);
    const item = checkbox.closest('.section-item');
    
    checkbox.checked = !checkbox.checked;
    
    if (checkbox.checked) {
        item.classList.add('selected');
    } else {
        item.classList.remove('selected');
    }
}

// Initialize selected state on page load
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('input[name="sections[]"]:checked').forEach(function(checkbox) {
        checkbox.closest('.section-item').classList.add('selected');
    });
});
</script>
@endsection
