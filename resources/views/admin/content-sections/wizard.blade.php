@extends('layouts.app')

@section('title', $section ? 'Edit Content Section' : 'Create Content Section')

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

    .wizard-container {
        max-width: 900px;
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

    .wizard-card {
        background: white !important;
        border-radius: 12px !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1) !important;
        overflow: hidden !important;
    }

    .wizard-steps {
        display: flex;
        background: var(--builder-bg);
        border-bottom: 1px solid var(--builder-border);
        padding: 0;
        margin: 0;
    }

    .wizard-step {
        flex: 1;
        padding: 16px 20px;
        text-align: center;
        cursor: pointer;
        border-right: 1px solid var(--builder-border);
        position: relative;
        transition: all 0.2s;
    }

    .wizard-step:last-child {
        border-right: none;
    }

    .wizard-step.active {
        background: white;
        color: var(--builder-primary);
        font-weight: 600;
    }

    .wizard-step.completed {
        background: var(--builder-bg);
        color: var(--builder-success);
    }

    .wizard-step-number {
        display: inline-block;
        width: 28px;
        height: 28px;
        line-height: 28px;
        border-radius: 50%;
        background: var(--builder-border);
        color: #6b7280;
        font-weight: 600;
        margin-right: 8px;
    }

    .wizard-step.active .wizard-step-number {
        background: var(--builder-accent);
        color: var(--builder-primary);
    }

    .wizard-step.completed .wizard-step-number {
        background: var(--builder-success);
        color: white;
    }

    .wizard-content {
        padding: 32px;
    }

    .wizard-step-panel {
        display: none;
    }

    .wizard-step-panel.active {
        display: block;
    }

    .form-label {
        display: block;
        font-weight: 600;
        font-size: 14px;
        color: #374151;
        margin-bottom: 6px;
    }

    .form-label .required {
        color: var(--builder-danger);
    }

    .form-input {
        width: 100% !important;
        padding: 10px 14px !important;
        border: 2px solid var(--builder-border) !important;
        border-radius: 8px !important;
        font-size: 14px !important;
        transition: all 0.2s !important;
        background: white !important;
        color: #374151 !important;
    }

    .form-input:focus {
        outline: none !important;
        border-color: var(--builder-accent) !important;
        box-shadow: 0 0 0 3px rgba(193, 236, 74, 0.2) !important;
    }

    .form-input.is-invalid {
        border-color: var(--builder-danger) !important;
    }

    .form-textarea {
        width: 100% !important;
        min-height: 200px !important;
        padding: 10px 14px !important;
        border: 2px solid var(--builder-border) !important;
        border-radius: 8px !important;
        font-size: 14px !important;
        transition: all 0.2s !important;
        background: white !important;
        color: #374151 !important;
        font-family: inherit !important;
        resize: vertical !important;
    }

    .form-textarea:focus {
        outline: none !important;
        border-color: var(--builder-accent) !important;
        box-shadow: 0 0 0 3px rgba(193, 236, 74, 0.2) !important;
    }

    .form-textarea.is-invalid {
        border-color: var(--builder-danger) !important;
    }

    .form-textarea-large {
        min-height: 400px;
    }

    .custom-select-input {
        display: block !important;
        width: 100% !important;
        height: 48px !important;
        padding: 12px 16px !important;
        font-size: 14px !important;
        font-weight: 400 !important;
        line-height: 1.5 !important;
        color: var(--builder-primary) !important;
        background-color: #fff !important;
        border: 2px solid var(--builder-border) !important;
        border-radius: 8px !important;
        cursor: pointer !important;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out !important;
    }
    
    .custom-select-input:hover {
        border-color: var(--builder-primary) !important;
    }

    .custom-select-input:focus {
        border-color: var(--builder-accent) !important;
        outline: 0 !important;
        box-shadow: 0 0 0 3px rgba(193, 236, 74, 0.25) !important;
    }

    .custom-select-input.is-invalid {
        border-color: var(--builder-danger) !important;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-checkbox {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        padding: 10px 14px;
        border: 2px solid var(--builder-border);
        border-radius: 8px;
        transition: all 0.2s;
    }
    
    .form-checkbox:hover {
        border-color: #1a202c;
        background: #f9fafb;
    }
    
    .form-checkbox input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: #1a202c;
    }

    .radio-group {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
    }

    .radio-option {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 16px;
        border: 2px solid var(--builder-border);
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
        flex: 1;
        min-width: 150px;
    }

    .radio-option:hover {
        border-color: #1a202c;
        background: #f9fafb;
    }

    .radio-option input[type="radio"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: #1a202c;
    }

    .radio-option.active {
        border-color: var(--builder-accent);
        background: #fefce8;
    }

    .conditional-field {
        display: none;
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid var(--builder-border);
    }

    .conditional-field.show {
        display: block;
    }

    .wizard-footer {
        display: flex;
        justify-content: space-between;
        padding: 20px 32px;
        border-top: 1px solid var(--builder-border);
        background: var(--builder-bg);
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

    .btn-builder-next {
        background: var(--builder-primary) !important;
        color: white !important;
        border: 2px solid var(--builder-primary) !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
    
    .btn-builder-next:hover {
        background: #2d3748 !important;
        color: white !important;
        border-color: #2d3748 !important;
    }

    #nextBtn {
        display: inline-flex !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
    
    .btn-builder-cancel {
        background: var(--builder-hover) !important;
        color: #374151 !important;
        border: 2px solid var(--builder-border) !important;
    }
    
    .btn-builder-cancel:hover {
        background: var(--builder-border) !important;
        color: #374151 !important;
    }

    .form-help-text {
        font-size: 12px;
        color: #6b7280;
        margin-top: 4px;
    }

    .error-message {
        color: var(--builder-danger) !important;
        font-size: 12px !important;
        margin-top: 4px !important;
    }

    .is-invalid {
        border-color: var(--builder-danger) !important;
    }
</style>
@endpush

@section('content')
<div class="wizard-container">
    <div class="page-header">
        <div>
            <h1 class="page-title">
                <i class="fas fa-file-alt mr-2"></i>
                {{ $section ? 'Edit Content Section' : 'Create Content Section' }}
            </h1>
            <p class="page-subtitle">Manage content sections that can be linked to survey categories or standalone</p>
        </div>
        <a href="{{ route('admin.content-sections.index') }}" class="btn-builder btn-builder-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <form id="contentSectionForm" action="{{ $section ? route('admin.content-sections.update', $section) : route('admin.content-sections.store') }}" method="POST">
        @csrf
        @if($section)
            @method('PUT')
        @endif

        <div class="wizard-card">
            <div class="wizard-steps">
                <div class="wizard-step active" data-step="1">
                    <span class="wizard-step-number">1</span>
                    <span>Basic Information</span>
                </div>
                <div class="wizard-step" data-step="2">
                    <span class="wizard-step-number">2</span>
                    <span>Content</span>
                </div>
            </div>

            <div class="wizard-content">
                <!-- Step 1: Basic Information -->
                <div class="wizard-step-panel active" id="step1">
                    <div class="form-group">
                        <label class="form-label">
                            Title <span class="required">*</span>
                        </label>
                        <input type="text" 
                               class="form-input @error('title') is-invalid @enderror" 
                               name="title" 
                               value="{{ old('title', $section->title ?? '') }}" 
                               required 
                               placeholder="Enter section title">
                        @error('title')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Link Type <span class="required">*</span>
                        </label>
                        <div class="radio-group">
                            <label class="radio-option {{ (old('link_type', $linkType ?? 'standalone') === 'standalone') ? 'active' : '' }}">
                                <input type="radio" name="link_type" value="standalone" 
                                       {{ (old('link_type', $linkType ?? 'standalone') === 'standalone') ? 'checked' : '' }} 
                                       onchange="updateLinkType()">
                                <span>Standalone</span>
                            </label>
                            <label class="radio-option {{ (old('link_type', $linkType ?? 'standalone') === 'category') ? 'active' : '' }}">
                                <input type="radio" name="link_type" value="category" 
                                       {{ (old('link_type', $linkType ?? 'standalone') === 'category') ? 'checked' : '' }} 
                                       onchange="updateLinkType()">
                                <span>Category</span>
                            </label>
                            <label class="radio-option {{ (old('link_type', $linkType ?? 'standalone') === 'subcategory') ? 'active' : '' }}">
                                <input type="radio" name="link_type" value="subcategory" 
                                       {{ (old('link_type', $linkType ?? 'standalone') === 'subcategory') ? 'checked' : '' }} 
                                       onchange="updateLinkType()">
                                <span>Subcategory</span>
                            </label>
                        </div>
                        @error('link_type')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="conditional-field" id="categoryField">
                        <div class="form-group">
                            <label class="form-label">
                                Category <span class="required">*</span>
                            </label>
                            <select class="custom-select-input @error('category_id') is-invalid @enderror" 
                                    name="category_id" 
                                    id="categorySelect"
                                    onchange="updateSubcategoryOptions()">
                                <option value="">Select Category...</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ old('category_id', $section->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                        {{ $category->display_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="conditional-field" id="subcategoryField">
                        <div class="form-group">
                            <label class="form-label">
                                Subcategory <span class="required">*</span>
                            </label>
                            <select class="custom-select-input @error('subcategory_id') is-invalid @enderror" 
                                    name="subcategory_id" 
                                    id="subcategorySelect">
                                <option value="">Select Subcategory...</option>
                                @if($section && $section->subcategory_id)
                                    @foreach($subcategories->where('category_id', $section->category_id) as $subcategory)
                                        <option value="{{ $subcategory->id }}" 
                                                {{ old('subcategory_id', $section->subcategory_id ?? '') == $subcategory->id ? 'selected' : '' }}>
                                            {{ $subcategory->display_name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('subcategory_id')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tags</label>
                        <input type="text" 
                               class="form-input" 
                               name="tags" 
                               value="{{ old('tags', $tagsString ?? '') }}" 
                               placeholder="Enter tags separated by commas (e.g., tag1, tag2, tag3)">
                        <div class="form-help-text">Separate multiple tags with commas</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Sort Order</label>
                        <input type="number" 
                               class="form-input" 
                               name="sort_order" 
                               value="{{ old('sort_order', $section->sort_order ?? '') }}" 
                               min="0" 
                               placeholder="0">
                        <div class="form-help-text">Lower numbers appear first. Leave empty for auto-assignment.</div>
                    </div>

                    <div class="form-group">
                        <label class="form-checkbox">
                            <input type="checkbox" 
                                   name="is_active" 
                                   value="1" 
                                   {{ old('is_active', $section->is_active ?? true) ? 'checked' : '' }}>
                            <span>Active (section will be visible)</span>
                        </label>
                    </div>
                </div>

                <!-- Step 2: Content -->
                <div class="wizard-step-panel" id="step2">
                    <div class="form-group">
                        <label class="form-label">
                            Content <span class="required">*</span>
                        </label>
                        <textarea class="form-textarea form-textarea-large @error('content') is-invalid @enderror" 
                                  name="content" 
                                  required 
                                  placeholder="Enter section content...">{{ old('content', $section->content ?? '') }}</textarea>
                        @error('content')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                        <div class="form-help-text">This content will be editable by surveyors in the future</div>
                    </div>
                </div>
            </div>

            <div class="wizard-footer">
                <div>
                    <button type="button" 
                            class="btn-builder btn-builder-cancel" 
                            id="prevBtn" 
                            onclick="previousStep()" 
                            style="display: none;">
                        <i class="fas fa-arrow-left"></i> Previous
                    </button>
                </div>
                <div>
                    <a href="{{ route('admin.content-sections.index') }}" class="btn-builder btn-builder-cancel">
                        Cancel
                    </a>
                    <button type="button" 
                            class="btn-builder btn-builder-next" 
                            id="nextBtn" 
                            onclick="nextStep()">
                        Next <i class="fas fa-arrow-right"></i>
                    </button>
                    <button type="submit" 
                            class="btn-builder btn-builder-primary" 
                            id="submitBtn" 
                            style="display: none;">
                        <i class="fas fa-save"></i> {{ $section ? 'Update' : 'Create' }} Section
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
let currentStep = 1;
const totalSteps = 2;

// Categories and subcategories data
const categoriesData = @json($categories);
const subcategoriesData = @json($subcategories);

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateLinkType();
    updateStepDisplay();
});

function updateLinkType() {
    const linkType = document.querySelector('input[name="link_type"]:checked').value;
    const categoryField = document.getElementById('categoryField');
    const subcategoryField = document.getElementById('subcategoryField');
    const categorySelect = document.getElementById('categorySelect');
    const subcategorySelect = document.getElementById('subcategorySelect');

    // Update radio button active states
    document.querySelectorAll('.radio-option').forEach(option => {
        option.classList.remove('active');
    });
    document.querySelector(`input[value="${linkType}"]`).closest('.radio-option').classList.add('active');

    if (linkType === 'standalone') {
        categoryField.classList.remove('show');
        subcategoryField.classList.remove('show');
        categorySelect.removeAttribute('required');
        subcategorySelect.removeAttribute('required');
    } else if (linkType === 'category') {
        categoryField.classList.add('show');
        subcategoryField.classList.remove('show');
        categorySelect.setAttribute('required', 'required');
        subcategorySelect.removeAttribute('required');
        updateSubcategoryOptions();
    } else if (linkType === 'subcategory') {
        categoryField.classList.add('show');
        subcategoryField.classList.add('show');
        categorySelect.setAttribute('required', 'required');
        subcategorySelect.setAttribute('required', 'required');
        updateSubcategoryOptions();
    }
}

function updateSubcategoryOptions() {
    const categoryId = document.getElementById('categorySelect').value;
    const subcategorySelect = document.getElementById('subcategorySelect');
    
    subcategorySelect.innerHTML = '<option value="">Select Subcategory...</option>';
    
    if (categoryId) {
        const subcategories = subcategoriesData.filter(sub => sub.category_id == categoryId);
        subcategories.forEach(sub => {
            const option = document.createElement('option');
            option.value = sub.id;
            option.textContent = sub.display_name;
            subcategorySelect.appendChild(option);
        });
    }
}

function updateStepDisplay() {
    // Update step indicators
    document.querySelectorAll('.wizard-step').forEach((step, index) => {
        const stepNum = index + 1;
        step.classList.remove('active', 'completed');
        if (stepNum < currentStep) {
            step.classList.add('completed');
        } else if (stepNum === currentStep) {
            step.classList.add('active');
        }
    });

    // Update step panels
    document.querySelectorAll('.wizard-step-panel').forEach((panel, index) => {
        const stepNum = index + 1;
        panel.classList.remove('active');
        if (stepNum === currentStep) {
            panel.classList.add('active');
        }
    });

    // Update navigation buttons
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');

    if (currentStep === 1) {
        if (prevBtn) prevBtn.style.display = 'none';
        if (nextBtn) {
            nextBtn.style.display = 'inline-flex';
            nextBtn.style.visibility = 'visible';
            nextBtn.style.opacity = '1';
        }
        if (submitBtn) submitBtn.style.display = 'none';
    } else if (currentStep === totalSteps) {
        if (prevBtn) prevBtn.style.display = 'inline-flex';
        if (nextBtn) nextBtn.style.display = 'none';
        if (submitBtn) submitBtn.style.display = 'inline-flex';
    } else {
        if (prevBtn) prevBtn.style.display = 'inline-flex';
        if (nextBtn) {
            nextBtn.style.display = 'inline-flex';
            nextBtn.style.visibility = 'visible';
            nextBtn.style.opacity = '1';
        }
        if (submitBtn) submitBtn.style.display = 'none';
    }
}

function nextStep() {
    if (validateStep(currentStep)) {
        if (currentStep < totalSteps) {
            currentStep++;
            updateStepDisplay();
        }
    }
}

function previousStep() {
    if (currentStep > 1) {
        currentStep--;
        updateStepDisplay();
    }
}

function validateStep(step) {
    let isValid = true;
    const stepPanel = document.getElementById(`step${step}`);
    const requiredFields = stepPanel.querySelectorAll('[required]');

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            isValid = false;
            field.classList.add('is-invalid');
            if (!field.nextElementSibling || !field.nextElementSibling.classList.contains('error-message')) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'error-message';
                errorDiv.textContent = 'This field is required';
                field.parentNode.appendChild(errorDiv);
            }
        } else {
            field.classList.remove('is-invalid');
            const errorMsg = field.parentNode.querySelector('.error-message');
            if (errorMsg) {
                errorMsg.remove();
            }
        }
    });

    // Special validation for step 1
    if (step === 1) {
        const linkType = document.querySelector('input[name="link_type"]:checked').value;
        if (linkType === 'category' || linkType === 'subcategory') {
            const categoryId = document.getElementById('categorySelect').value;
            if (!categoryId) {
                isValid = false;
                document.getElementById('categorySelect').classList.add('is-invalid');
            }
        }
        if (linkType === 'subcategory') {
            const subcategoryId = document.getElementById('subcategorySelect').value;
            if (!subcategoryId) {
                isValid = false;
                document.getElementById('subcategorySelect').classList.add('is-invalid');
            }
        }
    }

    return isValid;
}

// Allow clicking on step indicators to navigate
document.querySelectorAll('.wizard-step').forEach((step, index) => {
    step.addEventListener('click', function() {
        const targetStep = index + 1;
        if (targetStep <= currentStep) {
            currentStep = targetStep;
            updateStepDisplay();
        }
    });
});
</script>
@endpush

