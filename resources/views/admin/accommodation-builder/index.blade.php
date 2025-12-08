@extends('layouts.app')

@section('title', 'Accommodation Builder')

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

    .builder-container {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 24px;
        min-height: calc(100vh - 200px);
    }

    .builder-main {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .builder-preview {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        position: sticky;
        top: 100px;
        max-height: calc(100vh - 150px);
        overflow: auto;
    }

    /* Header */
    .builder-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px;
        border-bottom: 1px solid var(--builder-border);
        background: var(--builder-primary);
        color: white;
    }

    .builder-title {
        font-size: 20px;
        font-weight: 700;
        margin: 0;
    }

    .builder-actions {
        display: flex;
        gap: 12px;
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
        transform: translateY(-1px);
    }

    .btn-builder-secondary {
        background: rgba(255,255,255,0.1);
        color: white;
        border: 1px solid rgba(255,255,255,0.2);
    }

    .btn-builder-secondary:hover {
        background: rgba(255,255,255,0.2);
    }

    /* Section Container */
    .builder-section {
        padding: 24px;
        border-bottom: 1px solid var(--builder-border);
    }

    .builder-section:last-child {
        border-bottom: none;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }

    .section-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--builder-primary);
        margin: 0;
    }

    .section-subtitle {
        font-size: 13px;
        color: #6b7280;
        margin-top: 4px;
    }

    /* Type/Component Item */
    .item-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .item-card {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        background: var(--builder-bg);
        border: 1px solid var(--builder-border);
        border-radius: 8px;
        transition: all 0.2s;
    }

    .item-card:hover {
        border-color: var(--builder-accent);
        background: white;
    }

    .item-drag-handle {
        cursor: grab;
        padding: 4px 8px;
        color: #9ca3af;
        margin-right: 12px;
    }

    .item-drag-handle:active {
        cursor: grabbing;
    }

    .item-icon {
        width: 40px;
        height: 40px;
        background: var(--builder-primary);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--builder-accent);
        margin-right: 12px;
        font-size: 16px;
    }

    .item-info {
        flex: 1;
    }

    .item-name {
        font-weight: 600;
        font-size: 15px;
        color: var(--builder-primary);
    }

    .item-name[contenteditable="true"] {
        outline: none;
        background: white;
        padding: 2px 8px;
        border-radius: 4px;
        border: 2px solid var(--builder-accent);
    }

    .item-meta {
        font-size: 12px;
        color: #6b7280;
        margin-top: 2px;
    }

    .item-actions {
        display: flex;
        gap: 6px;
        opacity: 0;
        transition: opacity 0.2s;
    }

    .item-card:hover .item-actions {
        opacity: 1;
    }

    .action-btn {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        background: white;
        color: #6b7280;
    }

    .action-btn:hover {
        background: var(--builder-primary);
        color: var(--builder-accent);
    }

    .action-btn.delete:hover {
        background: var(--builder-danger);
        color: white;
    }

    /* Materials/Defects Tags */
    .tags-container {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        padding: 12px;
        background: white;
        border: 1px dashed var(--builder-border);
        border-radius: 8px;
        min-height: 50px;
        margin-top: 8px;
    }

    .tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        background: var(--builder-bg);
        border: 1px solid var(--builder-border);
        border-radius: 6px;
        font-size: 13px;
        color: #374151;
    }

    .tag-remove {
        cursor: pointer;
        color: #9ca3af;
        font-size: 14px;
    }

    .tag-remove:hover {
        color: var(--builder-danger);
    }

    .tag-input {
        flex: 1;
        min-width: 120px;
        border: none;
        outline: none;
        font-size: 13px;
        padding: 6px;
        background: transparent;
    }

    /* Add Button */
    .add-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px;
        border: 2px dashed var(--builder-border);
        border-radius: 8px;
        background: transparent;
        color: #6b7280;
        cursor: pointer;
        transition: all 0.2s;
        width: 100%;
        font-size: 14px;
        margin-top: 12px;
    }

    .add-btn:hover {
        border-color: var(--builder-accent);
        color: var(--builder-primary);
        background: #fefce8;
    }

    /* Component with Materials */
    .component-item {
        border: 1px solid var(--builder-border);
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 12px;
    }

    .component-header {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        background: var(--builder-bg);
        cursor: pointer;
    }

    .component-header:hover {
        background: var(--builder-hover);
    }

    .component-content {
        padding: 16px;
        background: white;
    }

    .component-toggle {
        margin-right: 12px;
        color: #6b7280;
        transition: transform 0.2s;
    }

    .component-item.collapsed .component-toggle {
        transform: rotate(-90deg);
    }

    .component-item.collapsed .component-content {
        display: none;
    }

    /* Preview Panel */
    .preview-header {
        padding: 16px 20px;
        border-bottom: 1px solid var(--builder-border);
        background: var(--builder-primary);
        color: white;
    }

    .preview-title {
        font-size: 16px;
        font-weight: 600;
        margin: 0;
    }

    .preview-content {
        padding: 20px;
    }

    .preview-room {
        background: var(--builder-bg);
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 16px;
    }

    .preview-room-title {
        font-weight: 600;
        font-size: 15px;
        margin-bottom: 12px;
        color: var(--builder-primary);
    }

    .preview-tabs {
        display: flex;
        gap: 8px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }

    .preview-tab {
        padding: 8px 14px;
        background: white;
        border: 1px solid var(--builder-border);
        border-radius: 6px;
        font-size: 13px;
        cursor: pointer;
    }

    .preview-tab.active {
        background: var(--builder-accent);
        border-color: var(--builder-accent);
        color: var(--builder-primary);
        font-weight: 600;
    }

    .preview-field {
        margin-bottom: 16px;
    }

    .preview-field-label {
        font-size: 12px;
        font-weight: 600;
        color: #6b7280;
        margin-bottom: 6px;
        text-transform: uppercase;
    }

    .preview-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .preview-button {
        padding: 8px 14px;
        background: white;
        border: 1px solid var(--builder-border);
        border-radius: 6px;
        font-size: 13px;
        color: #374151;
    }

    /* Modal */
    .builder-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1050;
        align-items: center;
        justify-content: center;
    }

    .builder-modal.active {
        display: flex;
    }

    .modal-content {
        background: white;
        border-radius: 12px;
        width: 100%;
        max-width: 500px;
        max-height: 90vh;
        overflow: auto;
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
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

    .modal-body {
        padding: 24px;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding: 16px 24px;
        border-top: 1px solid var(--builder-border);
        background: var(--builder-bg);
    }

    /* Form styles */
    .form-group {
        margin-bottom: 20px;
    }

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
        border: 1px solid var(--builder-border);
        border-radius: 8px;
        font-size: 14px;
        transition: border-color 0.2s;
    }

    .form-input:focus {
        outline: none;
        border-color: var(--builder-accent);
        box-shadow: 0 0 0 3px rgba(193, 236, 74, 0.2);
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

    /* Status badge */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
    }

    .status-badge.active {
        background: #dcfce7;
        color: #166534;
    }

    .status-badge.inactive {
        background: #fee2e2;
        color: #991b1b;
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .builder-container {
            grid-template-columns: 1fr;
        }
        
        .builder-preview {
            display: none;
        }
    }
</style>
@endpush

@section('content')
<div class="builder-container">
    <!-- Main Builder Panel -->
    <div class="builder-main">
        <div class="builder-header">
            <h1 class="builder-title">
                <i class="fas fa-bed mr-2"></i>
                Accommodation Builder
            </h1>
            <div class="builder-actions">
                <button class="btn-builder btn-builder-secondary" onclick="togglePreviewPanel()">
                    <i class="fas fa-eye"></i> Preview
                </button>
            </div>
        </div>

        <!-- Accommodation Types Section -->
        <div class="builder-section">
            <div class="section-header">
                <div>
                    <h2 class="section-title">Accommodation Types</h2>
                    <p class="section-subtitle">Room types that surveyors can assess (Bedroom, Bathroom, Kitchen, etc.)</p>
                </div>
                <button class="btn-builder btn-builder-primary" onclick="openAddTypeModal()">
                    <i class="fas fa-plus"></i> Add Type
                </button>
            </div>

            <div class="item-list" id="typesList">
                @forelse($accommodationTypes as $type)
                    @include('admin.accommodation-builder.partials.type-item', ['type' => $type])
                @empty
                    <p style="color: #9ca3af; text-align: center; padding: 20px;">No accommodation types yet. Add your first type above.</p>
                @endforelse
            </div>
        </div>

        <!-- Components Section -->
        <div class="builder-section">
            <div class="section-header">
                <div>
                    <h2 class="section-title">Components</h2>
                    <p class="section-subtitle">Parts of a room to inspect (Ceiling, Walls, Windows, Doors, etc.)</p>
                </div>
                <button class="btn-builder btn-builder-primary" onclick="openAddComponentModal()">
                    <i class="fas fa-plus"></i> Add Component
                </button>
            </div>

            <div id="componentsList">
                @forelse($components as $component)
                    @include('admin.accommodation-builder.partials.component-item', [
                        'component' => $component,
                        'materials' => $materialsByComponent[$component->id] ?? []
                    ])
                @empty
                    <p style="color: #9ca3af; text-align: center; padding: 20px;">No components yet. Add your first component above.</p>
                @endforelse
            </div>
        </div>

        <!-- Global Defects Section -->
        <div class="builder-section">
            <div class="section-header">
                <div>
                    <h2 class="section-title">Global Defects</h2>
                    <p class="section-subtitle">Defect options available for all accommodation components</p>
                </div>
            </div>

            <div class="tags-container" id="defectsContainer">
                @foreach($globalDefects as $defect)
                    <span class="tag" data-id="{{ $defect->id }}">
                        {{ $defect->value }}
                        <span class="tag-remove" onclick="deleteDefect({{ $defect->id }})">&times;</span>
                    </span>
                @endforeach
                <input type="text" class="tag-input" placeholder="Type and press Enter to add..." onkeydown="handleDefectInput(event)">
            </div>
        </div>
    </div>

    <!-- Preview Panel -->
    <div class="builder-preview" id="previewPanel">
        <div class="preview-header">
            <h3 class="preview-title">
                <i class="fas fa-eye mr-2"></i>
                Live Preview
            </h3>
        </div>
        <div class="preview-content">
            <div class="preview-room">
                <div class="preview-room-title">Bedroom 1</div>
                
                <div class="preview-tabs">
                    @foreach($components as $index => $component)
                        <button class="preview-tab {{ $index === 0 ? 'active' : '' }}">
                            {{ $component->display_name }}
                        </button>
                    @endforeach
                </div>
                
                @if($components->count() > 0)
                    @php $firstComponent = $components->first(); @endphp
                    <div class="preview-field">
                        <div class="preview-field-label">{{ $firstComponent->display_name }} Material</div>
                        <div class="preview-buttons">
                            @foreach($materialsByComponent[$firstComponent->id] ?? [] as $material)
                                <span class="preview-button">{{ $material->value }}</span>
                            @endforeach
                            @if(empty($materialsByComponent[$firstComponent->id] ?? []))
                                <span class="preview-button" style="color: #9ca3af;">No materials</span>
                            @endif
                        </div>
                    </div>
                @endif
                
                <div class="preview-field">
                    <div class="preview-field-label">Defects</div>
                    <div class="preview-buttons">
                        @foreach($globalDefects as $defect)
                            <span class="preview-button">{{ $defect->value }}</span>
                        @endforeach
                        @if($globalDefects->isEmpty())
                            <span class="preview-button" style="color: #9ca3af;">No defects</span>
                        @endif
                    </div>
                </div>
                
                <div class="preview-field">
                    <div class="preview-field-label">Condition Rating</div>
                    <div class="preview-buttons">
                        <span class="preview-button" style="background: #dcfce7; color: #166534;">1 - Good</span>
                        <span class="preview-button" style="background: #fef9c3; color: #854d0e;">2 - Fair</span>
                        <span class="preview-button" style="background: #fee2e2; color: #991b1b;">3 - Poor</span>
                        <span class="preview-button">NI</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Type Modal -->
<div class="builder-modal" id="addTypeModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Add Accommodation Type</h3>
            <button class="modal-close" onclick="closeModal('addTypeModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="addTypeForm">
                <div class="form-group">
                    <label class="form-label">Display Name *</label>
                    <input type="text" class="form-input" name="display_name" required placeholder="e.g., Bedroom">
                </div>
                <div class="form-group">
                    <label class="form-label">System Key (auto-generated)</label>
                    <input type="text" class="form-input" name="key_name" readonly placeholder="Will be generated">
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn-builder btn-builder-secondary" onclick="closeModal('addTypeModal')">Cancel</button>
            <button class="btn-builder btn-builder-primary" onclick="saveType()">
                <i class="fas fa-plus"></i> Add Type
            </button>
        </div>
    </div>
</div>

<!-- Add Component Modal -->
<div class="builder-modal" id="addComponentModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Add Component</h3>
            <button class="modal-close" onclick="closeModal('addComponentModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="addComponentForm">
                <div class="form-group">
                    <label class="form-label">Display Name *</label>
                    <input type="text" class="form-input" name="display_name" required placeholder="e.g., Ceiling">
                </div>
                <div class="form-group">
                    <label class="form-label">System Key (auto-generated)</label>
                    <input type="text" class="form-input" name="key_name" readonly placeholder="Will be generated">
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn-builder btn-builder-secondary" onclick="closeModal('addComponentModal')">Cancel</button>
            <button class="btn-builder btn-builder-primary" onclick="saveComponent()">
                <i class="fas fa-plus"></i> Add Component
            </button>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-container" id="toastContainer"></div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    initSortables();
    initAutoSlug();
});

// Initialize Sortable.js
function initSortables() {
    const typesList = document.getElementById('typesList');
    if (typesList) {
        new Sortable(typesList, {
            handle: '.item-drag-handle',
            animation: 150,
            onEnd: function() {
                reorderTypes();
            }
        });
    }
    
    const componentsList = document.getElementById('componentsList');
    if (componentsList) {
        new Sortable(componentsList, {
            handle: '.item-drag-handle',
            animation: 150,
            onEnd: function() {
                reorderComponents();
            }
        });
    }
}

// Auto-generate slug
function initAutoSlug() {
    document.querySelectorAll('input[name="display_name"]').forEach(input => {
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

function openAddTypeModal() {
    openModal('addTypeModal');
}

function openAddComponentModal() {
    openModal('addComponentModal');
}

// Toggle component
function toggleComponent(element) {
    const componentItem = element.closest('.component-item');
    componentItem.classList.toggle('collapsed');
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

// Save Type
async function saveType() {
    const form = document.getElementById('addTypeForm');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    data.key_name = data.display_name;
    
    try {
        const result = await apiCall('/admin/api/accommodation-types', 'POST', data);
        if (result.success) {
            showToast('Accommodation type created', 'success');
            closeModal('addTypeModal');
            document.getElementById('typesList').insertAdjacentHTML('beforeend', result.html);
            initSortables();
        } else {
            showToast(result.message || 'Error creating type', 'error');
        }
    } catch (error) {
        showToast('Error creating type', 'error');
    }
}

// Save Component
async function saveComponent() {
    const form = document.getElementById('addComponentForm');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    data.key_name = data.display_name;
    
    try {
        const result = await apiCall('/admin/api/accommodation-components', 'POST', data);
        if (result.success) {
            showToast('Component created', 'success');
            closeModal('addComponentModal');
            document.getElementById('componentsList').insertAdjacentHTML('beforeend', result.html);
            initSortables();
        } else {
            showToast(result.message || 'Error creating component', 'error');
        }
    } catch (error) {
        showToast('Error creating component', 'error');
    }
}

// Delete functions
async function deleteType(id) {
    if (!confirm('Delete this accommodation type?')) return;
    
    try {
        const result = await apiCall(`/admin/api/accommodation-types/${id}`, 'DELETE');
        if (result.success) {
            showToast('Type deleted', 'success');
            document.querySelector(`.item-card[data-type-id="${id}"]`).remove();
        } else {
            showToast(result.message || 'Cannot delete type', 'error');
        }
    } catch (error) {
        showToast('Error deleting type', 'error');
    }
}

async function deleteComponent(id) {
    if (!confirm('Delete this component and all its materials?')) return;
    
    try {
        const result = await apiCall(`/admin/api/accommodation-components/${id}`, 'DELETE');
        if (result.success) {
            showToast('Component deleted', 'success');
            document.querySelector(`.component-item[data-component-id="${id}"]`).remove();
        } else {
            showToast(result.message || 'Cannot delete component', 'error');
        }
    } catch (error) {
        showToast('Error deleting component', 'error');
    }
}

async function cloneType(id) {
    try {
        const result = await apiCall(`/admin/api/accommodation-types/${id}/clone`, 'POST');
        if (result.success) {
            showToast('Type cloned', 'success');
            document.getElementById('typesList').insertAdjacentHTML('beforeend', result.html);
            initSortables();
        } else {
            showToast(result.message || 'Error cloning type', 'error');
        }
    } catch (error) {
        showToast('Error cloning type', 'error');
    }
}

// Reorder
async function reorderTypes() {
    const order = Array.from(document.querySelectorAll('.item-card[data-type-id]')).map(item => item.dataset.typeId);
    await apiCall('/admin/api/accommodation-types/reorder', 'POST', { order });
}

async function reorderComponents() {
    const order = Array.from(document.querySelectorAll('.component-item')).map(item => item.dataset.componentId);
    await apiCall('/admin/api/accommodation-components/reorder', 'POST', { order });
}

// Material management
async function handleMaterialInput(event, componentId) {
    if (event.key === 'Enter') {
        event.preventDefault();
        const value = event.target.value.trim();
        if (value) {
            await addMaterial(componentId, value);
            event.target.value = '';
        }
    }
}

async function addMaterial(componentId, value) {
    try {
        const result = await apiCall('/admin/api/accommodation-options', 'POST', {
            option_type: 'material',
            value: value,
            component_id: componentId
        });
        if (result.success) {
            const container = document.querySelector(`.component-item[data-component-id="${componentId}"] .tags-container`);
            const input = container.querySelector('input');
            const tag = document.createElement('span');
            tag.className = 'tag';
            tag.dataset.id = result.option.id;
            tag.innerHTML = `${value}<span class="tag-remove" onclick="deleteMaterial(${result.option.id})">&times;</span>`;
            container.insertBefore(tag, input);
            showToast('Material added', 'success');
        }
    } catch (error) {
        showToast('Error adding material', 'error');
    }
}

async function deleteMaterial(id) {
    try {
        await apiCall(`/admin/api/accommodation-options/${id}`, 'DELETE');
        document.querySelector(`.tag[data-id="${id}"]`).remove();
        showToast('Material removed', 'success');
    } catch (error) {
        showToast('Error removing material', 'error');
    }
}

// Defect management
async function handleDefectInput(event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        const value = event.target.value.trim();
        if (value) {
            await addDefect(value);
            event.target.value = '';
        }
    }
}

async function addDefect(value) {
    try {
        const result = await apiCall('/admin/api/accommodation-options', 'POST', {
            option_type: 'defects',
            value: value
        });
        if (result.success) {
            const container = document.getElementById('defectsContainer');
            const input = container.querySelector('input');
            const tag = document.createElement('span');
            tag.className = 'tag';
            tag.dataset.id = result.option.id;
            tag.innerHTML = `${value}<span class="tag-remove" onclick="deleteDefect(${result.option.id})">&times;</span>`;
            container.insertBefore(tag, input);
            showToast('Defect added', 'success');
        }
    } catch (error) {
        showToast('Error adding defect', 'error');
    }
}

async function deleteDefect(id) {
    try {
        await apiCall(`/admin/api/accommodation-options/${id}`, 'DELETE');
        document.querySelector(`.tag[data-id="${id}"]`).remove();
        showToast('Defect removed', 'success');
    } catch (error) {
        showToast('Error removing defect', 'error');
    }
}

// Inline editing
function enableInlineEdit(element) {
    element.contentEditable = true;
    element.focus();
    
    const range = document.createRange();
    range.selectNodeContents(element);
    const selection = window.getSelection();
    selection.removeAllRanges();
    selection.addRange(range);
    
    element.addEventListener('blur', saveInlineEdit, { once: true });
    element.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            element.blur();
        }
        if (e.key === 'Escape') {
            element.contentEditable = false;
            location.reload();
        }
    });
}

async function saveInlineEdit(e) {
    const element = e.target;
    element.contentEditable = false;
    
    const type = element.dataset.type;
    const id = element.dataset.id;
    const value = element.textContent.trim();
    
    const url = `/admin/api/${type}/${id}`;
    const data = { display_name: value };
    
    try {
        await apiCall(url, 'PUT', data);
        showToast('Updated', 'success');
    } catch (error) {
        showToast('Error updating', 'error');
        location.reload();
    }
}

function togglePreviewPanel() {
    const panel = document.getElementById('previewPanel');
    panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
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



