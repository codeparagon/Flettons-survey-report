@extends('layouts.app')

@section('title', 'Global Options Manager')

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

    .options-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        padding: 20px 24px;
        background: var(--builder-primary);
        color: white;
        border-radius: 12px;
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
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
    }

    .btn-builder-primary {
        background: var(--builder-accent);
        color: var(--builder-primary);
    }

    .btn-builder-primary:hover {
        background: #a8d83a;
    }

    .btn-builder-secondary {
        background: rgba(255,255,255,0.1);
        color: white;
        border: 1px solid rgba(255,255,255,0.2);
    }
    
    .btn-builder-secondary:hover {
        background: rgba(255,255,255,0.2);
    }
    
    .btn-builder-cancel {
        background: #f3f4f6;
        color: #374151;
        border: 2px solid #e5e7eb;
    }
    
    .btn-builder-cancel:hover {
        background: #e5e7eb;
    }

    /* Option Type Cards */
    .option-type-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 24px;
    }

    .option-type-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .option-type-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 20px;
        background: var(--builder-bg);
        border-bottom: 1px solid var(--builder-border);
    }

    .option-type-title {
        font-weight: 700;
        font-size: 16px;
        color: var(--builder-primary);
        margin: 0;
    }

    .option-type-meta {
        font-size: 12px;
        color: #6b7280;
        margin-top: 2px;
    }

    .option-type-badge {
        padding: 4px 10px;
        background: var(--builder-primary);
        color: var(--builder-accent);
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .option-type-content {
        padding: 16px 20px;
    }

    /* Options List */
    .options-section {
        margin-bottom: 16px;
    }

    .options-section:last-child {
        margin-bottom: 0;
    }

    .options-section-title {
        font-size: 12px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        margin-bottom: 8px;
        padding-bottom: 6px;
        border-bottom: 1px solid var(--builder-border);
    }

    .options-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        min-height: 40px;
        padding: 8px;
        background: var(--builder-bg);
        border-radius: 8px;
        border: 1px dashed var(--builder-border);
    }

    .option-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        background: white;
        border: 1px solid var(--builder-border);
        border-radius: 6px;
        font-size: 13px;
        color: #374151;
        transition: all 0.2s;
    }

    .option-tag:hover {
        border-color: var(--builder-accent);
    }

    .option-tag-remove {
        cursor: pointer;
        color: #9ca3af;
        font-size: 14px;
    }

    .option-tag-remove:hover {
        color: var(--builder-danger);
    }

    .option-input {
        flex: 1;
        min-width: 100px;
        border: none;
        outline: none;
        font-size: 13px;
        padding: 6px;
        background: transparent;
    }

    /* Scoped Options */
    .scoped-section {
        margin-top: 12px;
        padding: 12px;
        background: #fefce8;
        border-radius: 8px;
        border: 1px solid #fef08a;
    }

    .scoped-title {
        font-size: 12px;
        font-weight: 600;
        color: #854d0e;
        margin-bottom: 8px;
    }

    /* Add Option Type */
    .add-option-type-card {
        border: 2px dashed var(--builder-border);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 200px;
        cursor: pointer;
        transition: all 0.2s;
        background: transparent;
    }

    .add-option-type-card:hover {
        border-color: var(--builder-accent);
        background: #fefce8;
    }

    .add-option-type-content {
        text-align: center;
        color: #6b7280;
    }

    .add-option-type-icon {
        font-size: 32px;
        margin-bottom: 12px;
        color: #9ca3af;
    }

    /* Modal */
    .builder-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.6);
        z-index: 99999;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .builder-modal.active {
        display: flex;
    }

    .modal-content {
        background: white;
        border-radius: 16px;
        width: 100%;
        max-width: 500px;
        max-height: 90vh;
        overflow: auto;
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.4);
    }
    
    .modal-body {
        padding: 24px;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px;
        border-bottom: 1px solid var(--builder-border);
        background: var(--builder-primary);
        color: white;
    }

    .modal-title {
        font-size: 18px;
        font-weight: 600;
        margin: 0;
    }

    .modal-close {
        background: none;
        border: none;
        color: white;
        font-size: 20px;
        cursor: pointer;
        padding: 4px;
        opacity: 0.7;
    }

    .modal-close:hover {
        opacity: 1;
    }


    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding: 16px 24px;
        border-top: 1px solid var(--builder-border);
        background: var(--builder-bg);
    }

    /* Form */
    .form-label {
        display: block;
        font-weight: 600;
        font-size: 14px;
        color: #374151;
        margin-bottom: 6px;
    }

    .form-input {
        width: 100%;
        padding: 10px 14px;
        border: 2px solid var(--builder-border);
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.2s;
        background: white;
    }

    .form-input:focus {
        outline: none;
        border-color: var(--builder-accent);
        box-shadow: 0 0 0 3px rgba(193, 236, 74, 0.2);
    }
    
    /* Custom Select - avoids Bootstrap conflicts */
    .custom-select-input {
        display: block;
        width: 100%;
        height: 48px;
        padding: 12px 16px;
        font-size: 14px;
        font-weight: 400;
        line-height: 1.5;
        color: #1a202c;
        background-color: #fff;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        cursor: pointer;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    
    .custom-select-input:hover {
        border-color: #1a202c;
    }

    .custom-select-input:focus {
        border-color: #c1ec4a;
        outline: 0;
        box-shadow: 0 0 0 3px rgba(193, 236, 74, 0.25);
    }
    
    .custom-select-input option {
        padding: 12px;
    }
    
    .form-group {
        margin-bottom: 20px;
        position: relative;
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

    /* Toast */
    .toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1100;
    }

    .toast {
        background: var(--builder-primary);
        color: white;
        padding: 14px 20px;
        border-radius: 8px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        animation: slideIn 0.3s ease;
    }

    .toast.success {
        background: var(--builder-success);
    }

    .toast.error {
        background: var(--builder-danger);
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    /* Empty state */
    .empty-options {
        color: #9ca3af;
        font-size: 13px;
        font-style: italic;
    }
</style>
@endpush

@section('content')
<div class="options-container">
    <div class="page-header">
        <div>
            <h1 class="page-title">
                <i class="fas fa-sliders-h mr-2"></i>
                Global Options Manager
            </h1>
            <p class="page-subtitle">Manage dropdown options for survey sections (Location, Structure, Material, Defects, Remaining Life)</p>
        </div>
        <button class="btn-builder btn-builder-primary" onclick="openAddOptionTypeModal()">
            <i class="fas fa-plus"></i> Add Option Type
        </button>
    </div>

    <div class="option-type-grid">
        @foreach($optionTypes as $optionType)
        <div class="option-type-card" data-type-id="{{ $optionType->id }}">
            <div class="option-type-header">
                <div>
                    <h3 class="option-type-title">{{ $optionType->label }}</h3>
                    <div class="option-type-meta">Key: {{ $optionType->key_name }}</div>
                </div>
                <span class="option-type-badge">
                    {{ $optionType->is_multiple ? 'Multiple' : 'Single' }}
                </span>
            </div>
            <div class="option-type-content">
                @php
                    $organized = $organizedOptions[$optionType->key_name] ?? ['global' => collect(), 'by_category' => [], 'by_subcategory' => []];
                @endphp

                <!-- Global Options -->
                <div class="options-section">
                    <div class="options-section-title">Global Options</div>
                    <div class="options-tags" data-type-id="{{ $optionType->id }}" data-scope="global">
                        @forelse($organized['global'] as $option)
                            <span class="option-tag" data-option-id="{{ $option->id }}">
                                {{ $option->value }}
                                <span class="option-tag-remove" onclick="deleteOption({{ $option->id }})">&times;</span>
                            </span>
                        @empty
                            <span class="empty-options">No global options</span>
                        @endforelse
                        <input type="text" class="option-input" placeholder="Type and Enter..." 
                               onkeydown="handleOptionInput(event, {{ $optionType->id }}, 'global', null)">
                    </div>
                </div>

                <!-- Category-Scoped Options -->
                @if(count($organized['by_category']) > 0)
                    @foreach($organized['by_category'] as $categoryId => $data)
                    <div class="scoped-section">
                        <div class="scoped-title">
                            <i class="fas fa-folder"></i> {{ $data['category']->display_name }}
                        </div>
                        <div class="options-tags" data-type-id="{{ $optionType->id }}" data-scope="category" data-scope-id="{{ $categoryId }}">
                            @foreach($data['options'] as $option)
                                <span class="option-tag" data-option-id="{{ $option->id }}">
                                    {{ $option->value }}
                                    <span class="option-tag-remove" onclick="deleteOption({{ $option->id }})">&times;</span>
                                </span>
                            @endforeach
                            <input type="text" class="option-input" placeholder="Add..." 
                                   onkeydown="handleOptionInput(event, {{ $optionType->id }}, 'category', {{ $categoryId }})">
                        </div>
                    </div>
                    @endforeach
                @endif

                <!-- Subcategory-Scoped Options -->
                @if(count($organized['by_subcategory']) > 0)
                    @foreach($organized['by_subcategory'] as $subcategoryId => $data)
                    <div class="scoped-section" style="background: #eff6ff; border-color: #bfdbfe;">
                        <div class="scoped-title" style="color: #1e40af;">
                            <i class="fas fa-layer-group"></i> {{ $data['subcategory']->category->display_name ?? '' }} > {{ $data['subcategory']->display_name }}
                        </div>
                        <div class="options-tags" data-type-id="{{ $optionType->id }}" data-scope="subcategory" data-scope-id="{{ $subcategoryId }}">
                            @foreach($data['options'] as $option)
                                <span class="option-tag" data-option-id="{{ $option->id }}">
                                    {{ $option->value }}
                                    <span class="option-tag-remove" onclick="deleteOption({{ $option->id }})">&times;</span>
                                </span>
                            @endforeach
                            <input type="text" class="option-input" placeholder="Add..." 
                                   onkeydown="handleOptionInput(event, {{ $optionType->id }}, 'subcategory', {{ $subcategoryId }})">
                        </div>
                    </div>
                    @endforeach
                @endif

                <!-- Add Scoped Options Button -->
                <button class="btn-builder btn-builder-secondary" style="margin-top: 12px; width: 100%; background: var(--builder-bg); color: #374151; border: 1px solid var(--builder-border);" 
                        onclick="openAddScopedOptionModal({{ $optionType->id }}, '{{ $optionType->label }}')">
                    <i class="fas fa-plus"></i> Add Category/Subcategory Options
                </button>
            </div>
        </div>
        @endforeach

        <!-- Add New Option Type Card -->
        <div class="add-option-type-card" onclick="openAddOptionTypeModal()">
            <div class="add-option-type-content">
                <div class="add-option-type-icon">
                    <i class="fas fa-plus-circle"></i>
                </div>
                <div>Add New Option Type</div>
            </div>
        </div>
    </div>
</div>

<!-- Add Option Type Modal -->
<div class="builder-modal" id="addOptionTypeModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Add Option Type</h3>
            <button class="modal-close" onclick="closeModal('addOptionTypeModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="addOptionTypeForm">
                <div class="form-group">
                    <label class="form-label">Label *</label>
                    <input type="text" class="form-input" name="label" required placeholder="e.g., Construction Type">
                </div>
                <div class="form-group">
                    <label class="form-label">Key Name (auto-generated)</label>
                    <input type="text" class="form-input" name="key_name" readonly placeholder="Will be generated">
                </div>
                <div class="form-group">
                    <label class="form-checkbox">
                        <input type="checkbox" name="is_multiple" value="1">
                        <span>Allow multiple selections</span>
                    </label>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn-builder btn-builder-cancel" onclick="closeModal('addOptionTypeModal')">Cancel</button>
            <button class="btn-builder btn-builder-primary" onclick="saveOptionType()">
                <i class="fas fa-plus"></i> Add Option Type
            </button>
        </div>
    </div>
</div>

<!-- Add Scoped Option Modal -->
<div class="builder-modal" id="addScopedOptionModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Add Scoped Options</h3>
            <button class="modal-close" onclick="closeModal('addScopedOptionModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="addScopedOptionForm">
                <input type="hidden" name="option_type_id" id="scopedOptionTypeId">
                <div class="form-group">
                    <label class="form-label">Option Type</label>
                    <input type="text" class="form-input" id="scopedOptionTypeName" readonly style="background: #f3f4f6; color: #6b7280;">
                </div>
                <div class="form-group">
                    <label class="form-label">Scope *</label>
                    <select class="custom-select-input" name="scope_type" id="scopeTypeSelect" onchange="updateScopeSelector()">
                        <option value="category">Category</option>
                        <option value="subcategory">Subcategory</option>
                    </select>
                </div>
                <div class="form-group" id="categorySelectorGroup">
                    <label class="form-label">Category *</label>
                    <select class="custom-select-input" name="category_id" id="categorySelect" onchange="updateSubcategorySelector()">
                        <option value="">Select Category...</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->display_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" id="subcategorySelectorGroup" style="display: none;">
                    <label class="form-label">Subcategory *</label>
                    <select class="custom-select-input" name="subcategory_id" id="subcategorySelect">
                        <option value="">Select Subcategory...</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Option Value *</label>
                    <input type="text" class="form-input" name="value" required placeholder="Enter option value">
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn-builder btn-builder-cancel" onclick="closeModal('addScopedOptionModal')">Cancel</button>
            <button class="btn-builder btn-builder-primary" onclick="saveScopedOption()">
                <i class="fas fa-plus"></i> Add Option
            </button>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-container" id="toastContainer"></div>

@endsection

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Categories data for subcategory selector
const categoriesData = @json($categories);

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    initAutoSlug();
});

function initAutoSlug() {
    document.querySelectorAll('input[name="label"]').forEach(input => {
        input.addEventListener('input', function() {
            const form = this.closest('form');
            const keyInput = form.querySelector('input[name="key_name"]');
            if (keyInput && keyInput.hasAttribute('readonly')) {
                keyInput.value = slugify(this.value);
            }
        });
    });
}

function slugify(text) {
    return text.toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/[\s_-]+/g, '_')
        .replace(/^-+|-+$/g, '');
}

// Modal functions
function openModal(modalId) {
    document.getElementById(modalId).classList.add('active');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
    const form = document.querySelector('#' + modalId + ' form');
    if (form) form.reset();
}

function openAddOptionTypeModal() {
    openModal('addOptionTypeModal');
}

function openAddScopedOptionModal(optionTypeId, optionTypeName) {
    document.getElementById('scopedOptionTypeId').value = optionTypeId;
    document.getElementById('scopedOptionTypeName').value = optionTypeName;
    openModal('addScopedOptionModal');
}

// Scope selector logic
function updateScopeSelector() {
    const scopeType = document.getElementById('scopeTypeSelect').value;
    const subcategoryGroup = document.getElementById('subcategorySelectorGroup');
    
    if (scopeType === 'subcategory') {
        subcategoryGroup.style.display = 'block';
        updateSubcategorySelector();
    } else {
        subcategoryGroup.style.display = 'none';
    }
}

function updateSubcategorySelector() {
    const categoryId = document.getElementById('categorySelect').value;
    const subcategorySelect = document.getElementById('subcategorySelect');
    
    subcategorySelect.innerHTML = '<option value="">Select Subcategory...</option>';
    
    if (categoryId) {
        const category = categoriesData.find(c => c.id == categoryId);
        if (category && category.subcategories) {
            category.subcategories.forEach(sub => {
                subcategorySelect.innerHTML += `<option value="${sub.id}">${sub.display_name}</option>`;
            });
        }
    }
}

// API calls
async function apiCall(url, method = 'GET', data = null) {
    const options = {
        method,
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    };
    
    if (data && method !== 'GET') {
        options.body = JSON.stringify(data);
    }
    
    const response = await fetch(url, options);
    return response.json();
}

// Save Option Type
async function saveOptionType() {
    const form = document.getElementById('addOptionTypeForm');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    data.key_name = data.label;
    data.is_multiple = form.querySelector('input[name="is_multiple"]').checked;
    
    try {
        const result = await apiCall('/admin/api/option-types', 'POST', data);
        if (result.success) {
            showToast('Option type created', 'success');
            closeModal('addOptionTypeModal');
            location.reload();
        } else {
            showToast(result.message || 'Error', 'error');
        }
    } catch (error) {
        showToast('Error creating option type', 'error');
    }
}

// Handle option input (inline add)
async function handleOptionInput(event, optionTypeId, scopeType, scopeId) {
    if (event.key === 'Enter') {
        event.preventDefault();
        const value = event.target.value.trim();
        if (value) {
            await addOption(optionTypeId, value, scopeType, scopeId, event.target);
            event.target.value = '';
        }
    }
}

async function addOption(optionTypeId, value, scopeType, scopeId, inputElement) {
    try {
        const data = {
            option_type_id: optionTypeId,
            value: value,
            scope_type: scopeType,
            scope_id: scopeId
        };
        
        const result = await apiCall('/admin/api/options', 'POST', data);
        if (result.success) {
            // Add tag to container
            const container = inputElement.closest('.options-tags');
            const emptyMsg = container.querySelector('.empty-options');
            if (emptyMsg) emptyMsg.remove();
            
            const tag = document.createElement('span');
            tag.className = 'option-tag';
            tag.dataset.optionId = result.option.id;
            tag.innerHTML = `${value}<span class="option-tag-remove" onclick="deleteOption(${result.option.id})">&times;</span>`;
            container.insertBefore(tag, inputElement);
            
            showToast('Option added', 'success');
        } else {
            showToast(result.message || 'Error', 'error');
        }
    } catch (error) {
        showToast('Error adding option', 'error');
    }
}

// Save Scoped Option (from modal)
async function saveScopedOption() {
    const form = document.getElementById('addScopedOptionForm');
    const formData = new FormData(form);
    const data = {
        option_type_id: formData.get('option_type_id'),
        value: formData.get('value'),
        scope_type: formData.get('scope_type')
    };
    
    if (data.scope_type === 'category') {
        data.scope_id = formData.get('category_id');
    } else {
        data.scope_id = formData.get('subcategory_id');
    }
    
    if (!data.scope_id) {
        showToast('Please select a category or subcategory', 'error');
        return;
    }
    
    try {
        const result = await apiCall('/admin/api/options', 'POST', data);
        if (result.success) {
            showToast('Scoped option added', 'success');
            closeModal('addScopedOptionModal');
            location.reload();
        } else {
            showToast(result.message || 'Error', 'error');
        }
    } catch (error) {
        showToast('Error adding option', 'error');
    }
}

// Delete option
async function deleteOption(id) {
    try {
        const result = await apiCall(`/admin/api/options/${id}`, 'DELETE');
        if (result.success) {
            document.querySelector(`.option-tag[data-option-id="${id}"]`).remove();
            showToast('Option removed', 'success');
        } else {
            showToast(result.message || 'Error', 'error');
        }
    } catch (error) {
        showToast('Error removing option', 'error');
    }
}

// Toast
function showToast(message, type = 'info') {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
        ${message}
    `;
    container.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideIn 0.3s ease reverse';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
</script>
@endpush

