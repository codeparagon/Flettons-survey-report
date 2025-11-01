@extends('layouts.app')

@section('title', $section->display_name . ' Assessment - ' . $survey->client_name)

@push('styles')
<style>
    /* Critical CSS - Prevent FOUC and layout issues */
    .dashboard-wrapper {
        position: relative !important;
        left: 0 !important;
        margin-left: 264px !important;
    }
    
    .dashboard-content {
        width: 100% !important;
        max-width: 100% !important;
        overflow-x: hidden;
    }
    
    .dashboard-content .row {
        margin-left: 0 !important;
        margin-right: 0 !important;
    }
    
    @media (min-width: 992px) {
        .dashboard-content .col-xl-8 {
            flex: 0 0 66.666667% !important;
            max-width: 66.666667% !important;
        }
        .dashboard-content .col-xl-4 {
            flex: 0 0 33.333333% !important;
            max-width: 33.333333% !important;
        }
    }
</style>
@endpush

@section('content')
<div class="row mb-3">
    <div class="col-xl-12">
        <div class="page-header">
            <h2 class="pageheader-title">{{ $section->display_name }} Assessment</h2>
            <div class="page-breadcrumb">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('surveyor.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('surveyor.surveys.index') }}">Surveys</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('surveyor.surveys.show', $survey) }}">Survey #{{ $survey->id }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('surveyor.survey.sections', $survey) }}">Sections</a></li>
                        <li class="breadcrumb-item active">{{ $section->display_name }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-8 col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ $section->display_name }} Assessment Form</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('surveyor.survey.section.save', [$survey, $section]) }}" 
                      method="POST" 
                      enctype="multipart/form-data">
                    @csrf
                    
                    {{-- ==========================================
                         FORM FIELD DISPLAY LOGIC
                         ==========================================
                         This form adapts based on section generation_method:
                         
                         1. CUSTOM_FIELDS: Shows only custom configured fields
                         2. AI: Shows Report Content + Material + Images + 
                                 Defects + Remaining Life + Notes
                         3. DATABASE: Shows Report Content + Material + Images + Notes
                                     (NO Defects/Remaining Life - those are AI-only)
                         
                         This matches the admin section creation/edit forms.
                         ========================================== --}}
                    
                    @php
                        // Initialize section service and load custom fields
                        $sectionService = app(\App\Services\SectionAssessmentService::class);
                        $section->refresh();
                        $section->load(['fields' => function($query) {
                            $query->where('is_active', true)
                                  ->orderBy('field_order')
                                  ->orderBy('field_label');
                        }]);
                        
                        $customFields = $section->fields;
                        $hasCustomFields = $customFields && $customFields->count() > 0;
                        $fieldValues = $hasCustomFields ? $sectionService->prepareFieldValuesForForm($section, $assessment) : [];
                        
                        // Get field configuration for database/AI sections
                        $fieldConfig = $section->field_config ?? [];
                        $generationMethod = $section->generation_method ?? 'database';
                    @endphp

                    {{-- ==========================================
                         CUSTOM FIELDS MODE
                         ========================================== --}}
                    @if($generationMethod === 'custom_fields')
                    @if($hasCustomFields)
                            {{-- Render custom fields --}}
                        @foreach($customFields as $field)
                            <x-dynamic-field 
                                :field="$field" 
                                :value="$fieldValues['field_' . $field->id] ?? ''" 
                                :errors="$errors" />
                        @endforeach
                    @else
                            {{-- Custom fields method but no fields configured yet --}}
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> 
                                <strong>Custom Fields Mode:</strong> This section is configured to use custom fields, but no custom fields have been added yet. Please contact an administrator to configure the fields for this section.
                            </div>
                        @endif
                    @else
                        {{-- ==========================================
                             DATABASE OR AI MODE
                             ========================================== --}}
                        @php
                            // Prepare report content value with level-specific template support
                            $defaultReportContent = '';
                            if ($generationMethod === 'database') {
                                $levelTemplates = $fieldConfig['report_templates'] ?? [];
                                
                                // Get level ID from survey level name (stable lookup)
                                $surveyLevelName = $survey->level ?? 'Level 1';
                                $surveyLevelModel = \App\Models\SurveyLevel::where('name', $surveyLevelName)->first();
                                
                                if ($surveyLevelModel) {
                                    // Try by level ID first (most stable)
                                    $defaultReportContent = $levelTemplates[$surveyLevelModel->id] 
                                        ?? $levelTemplates[$surveyLevelName] 
                                        ?? $fieldConfig['report_template'] 
                                        ?? '';
                                } else {
                                    // Fallback to level name if model not found, then legacy template
                                    $defaultReportContent = $levelTemplates[$surveyLevelName] 
                                        ?? $fieldConfig['report_template'] 
                                        ?? '';
                                }
                            }
                            $reportContentValue = old('report_content', $assessment->report_content ?: $defaultReportContent);
                            
                            // Prepare default fields values (only for AI mode)
                            $showDefaultFields = ($generationMethod === 'ai');
                            $defectsOptions = $showDefaultFields 
                                ? ($fieldConfig['defects_options'] ?? ['Rot', 'Deflection', 'Moss', 'Lichen', 'ACMs'])
                                : [];
                            $remainingLifeOptions = $showDefaultFields
                                ? ($fieldConfig['remaining_life_options'] ?? ['0 yrs', '1-5 yrs', '6-10 yrs', '10+ yrs'])
                                : [];
                            $selectedDefects = old('defects', $assessment->defects ?? []);
                            $selectedRemainingLife = old('remaining_life', $assessment->remaining_life ?? '');
                            if (!is_array($selectedDefects)) {
                                $selectedDefects = [];
                            }
                        @endphp

                        @if($generationMethod === 'database')
                            {{-- ==========================================
                                 DATABASE MODE: Only Report Content + Photos
                                 ========================================== --}}
                            
                            {{-- Report Content --}}
                            <div class="form-group">
                                <label for="report_content">Report Content</label>
                                <textarea class="form-control @error('report_content') is-invalid @enderror" 
                                          id="report_content" 
                                          name="report_content" 
                                          rows="6"
                                          placeholder="Enter or edit the report content...">{{ $reportContentValue }}</textarea>
                                <small class="form-text text-muted">
                                    Main report content (pre-filled from template, can be edited)
                                </small>
                                @error('report_content')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Images Upload --}}
                            <div class="form-group">
                                <label>Images</label>
                                <div class="media-upload-container">
                                    <div class="tab-content">
                                        <div class="tab-pane fade show active" id="image-panel" role="tabpanel">
                                            <div class="dropzone-container" id="image-dropzone">
                                                <div class="dropzone-area" id="image-drop-area">
                                                    <p class="dropzone-text">Drag & drop here or use 'Add Image'</p>
                                                </div>
                                                <button type="button" class="btn btn-primary mt-2" onclick="document.getElementById('photos').click()">
                                                    <i class="fas fa-plus"></i> Add Image
                                                </button>
                                                <input type="file" class="d-none" id="photos" name="photos[]" multiple accept="image/*" onchange="handleImageFiles(this.files)">
                                            </div>
                                            <div id="image-preview-container" class="mt-3 row">
                                                @if($assessment->photos && count($assessment->photos) > 0)
                                                    @foreach($assessment->photos as $photo)
                                                        <div class="col-md-3 mb-2 image-preview-item">
                                                            <div class="position-relative">
                                                                <img src="{{ Storage::url($photo) }}" class="img-thumbnail" style="width: 100%; height: 150px; object-fit: cover;">
                                                                <button type="button" class="btn btn-sm btn-danger position-absolute" style="top: 5px; right: 5px;" onclick="deletePhotoPreview(this, '{{ $photo }}')">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                                <input type="hidden" name="existing_photos[]" value="{{ $photo }}">
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @error('photos.*')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                        @elseif($generationMethod === 'ai')
                            {{-- ==========================================
                                 AI MODE: All fields, Report Content at bottom
                                 ========================================== --}}
                            
                            {{-- Material Input --}}
                            <div class="form-group">
                                <label for="material">Materials</label>
                                <input type="text" 
                                       class="form-control @error('material') is-invalid @enderror" 
                                       id="material" 
                                       name="material" 
                                       value="{{ old('material', $assessment->material) }}"
                                       placeholder="e.g., Clay tiles">
                                @error('material')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Images Upload --}}
                            <div class="form-group">
                                <label>Images</label>
                                <div class="media-upload-container">
                                    <div class="tab-content">
                                        <div class="tab-pane fade show active" id="image-panel" role="tabpanel">
                                            <div class="dropzone-container" id="image-dropzone">
                                                <div class="dropzone-area" id="image-drop-area">
                                                    <p class="dropzone-text">Drag & drop here or use 'Add Image'</p>
                                                </div>
                                                <button type="button" class="btn btn-primary mt-2" onclick="document.getElementById('photos').click()">
                                                    <i class="fas fa-plus"></i> Add Image
                                                </button>
                                                <input type="file" class="d-none" id="photos" name="photos[]" multiple accept="image/*" onchange="handleImageFiles(this.files)">
                                            </div>
                                            <div id="image-preview-container" class="mt-3 row">
                                                @if($assessment->photos && count($assessment->photos) > 0)
                                                    @foreach($assessment->photos as $photo)
                                                        <div class="col-md-3 mb-2 image-preview-item">
                                                            <div class="position-relative">
                                                                <img src="{{ Storage::url($photo) }}" class="img-thumbnail" style="width: 100%; height: 150px; object-fit: cover;">
                                                                <button type="button" class="btn btn-sm btn-danger position-absolute" style="top: 5px; right: 5px;" onclick="deletePhotoPreview(this, '{{ $photo }}')">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                                <input type="hidden" name="existing_photos[]" value="{{ $photo }}">
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @error('photos.*')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Default Fields (Defects & Remaining Life) - Only shown for AI mode --}}
                            {{-- Defects Single-select Tag Buttons (stored as array) --}}
                            <div class="form-group">
                                <label>Defects</label>
                                <div class="tag-buttons-container" id="defects-container">
                                    @foreach($defectsOptions as $option)
                                        <button type="button" 
                                                class="tag-button defects-tag {{ (is_array($selectedDefects) && count($selectedDefects) > 0 && $selectedDefects[0] == $option) ? 'selected' : '' }}" 
                                                data-value="{{ htmlspecialchars($option, ENT_QUOTES, 'UTF-8') }}">
                                            {{ $option }}
                                        </button>
                                    @endforeach
                                </div>
                                <input type="hidden" name="defects" id="defects-input" value="{{ json_encode($selectedDefects, JSON_HEX_QUOT | JSON_HEX_APOS) }}">
                                @error('defects')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Remaining Life Single-select Tag Buttons --}}
                            <div class="form-group">
                                <label>Remaining Life</label>
                                <div class="tag-buttons-container" id="remaining-life-container">
                                    @foreach($remainingLifeOptions as $option)
                                        <button type="button" 
                                                class="tag-button remaining-life-tag {{ $selectedRemainingLife == $option ? 'selected' : '' }}" 
                                                data-value="{{ htmlspecialchars($option, ENT_QUOTES, 'UTF-8') }}">
                                            {{ $option }}
                                        </button>
                                    @endforeach
                                </div>
                                <input type="hidden" name="remaining_life" id="remaining-life-input" value="{{ $selectedRemainingLife }}">
                                @error('remaining_life')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Additional Notes --}}
                            <div class="form-group">
                                <label for="notes">Additional Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" 
                                          name="notes" 
                                          rows="4"
                                          placeholder="Evidence of localised deterioration, further inspection advised.">{{ old('notes', $assessment->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Report Content - Moved to bottom for AI mode --}}
                            <div class="form-group">
                                <label for="report_content">Report Content</label>
                                <small class="form-text text-muted mb-2 d-block">
                                    <i class="fas fa-info-circle text-primary"></i> This report will be generated by AI based on the configured prompt template.
                                </small>
                                <textarea class="form-control @error('report_content') is-invalid @enderror" 
                                          id="report_content" 
                                          name="report_content" 
                                          rows="6"
                                          placeholder="AI-generated report content will appear here...">{{ $reportContentValue }}</textarea>
                                <small class="form-text text-muted">
                                    Main report content (generated by AI, can be edited)
                                </small>
                                @error('report_content')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                        @endif
                    @endif

                    <div class="form-navigation-bar">
                        <div class="nav-button-group">
                            @if($previousSection)
                                <a href="{{ route('surveyor.survey.section.form', [$survey, $previousSection]) }}" 
                                   class="btn-nav btn-nav-previous">
                                    <i class="fas fa-arrow-left"></i> Previous
                                </a>
                            @else
                                <button type="button" class="btn-nav btn-nav-previous" disabled>
                                    <i class="fas fa-arrow-left"></i> Previous
                                </button>
                            @endif
                        </div>
                        <div class="nav-button-group">
                            <button type="submit" class="btn-nav btn-nav-save">
                                <i class="fas fa-save"></i> Save & Continue
                            </button>
                            <a href="{{ route('surveyor.survey.categories', $survey) }}" 
                               class="btn-nav btn-nav-view-all">
                                <i class="fas fa-list"></i> View All
                            </a>
                        </div>
                        <div class="nav-button-group">
                            @if($nextSection)
                                <a href="{{ route('surveyor.survey.section.form', [$survey, $nextSection]) }}" 
                                   class="btn-nav btn-nav-next">
                                    Next <i class="fas fa-arrow-right"></i>
                                </a>
                            @else
                                <button type="button" class="btn-nav btn-nav-next" disabled>
                                    Next <i class="fas fa-arrow-right"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-4">
        {{-- Survey Information Card --}}
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Survey Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <th width="100">Client:</th>
                        <td>{{ $survey->client_name }}</td>
                    </tr>
                    <tr>
                        <th>Property:</th>
                        <td>{{ $survey->property_address_full }}</td>
                    </tr>
                    <tr>
                        <th>Level:</th>
                        <td><span class="badge badge-info">{{ $survey->level }}</span></td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Section Description Card --}}
        @if($section->description)
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-file-alt"></i> Section Description</h5>
            </div>
            <div class="card-body">
                <p class="card-text mb-0">{{ $section->description }}</p>
            </div>
        </div>
        @endif

        {{-- Hierarchy Sidebar --}}
        <x-survey-hierarchy-sidebar 
            :survey="$survey" 
            :section="$section" 
            :hierarchy="$hierarchy" 
            :progress="$progress ?? null" />
    </div>
</div>
@endsection
@push('scripts')
<script>
// Initialize selected values from PHP (only if default fields are shown)
@php
    $generationMethod = $section->generation_method ?? 'database';
    $showDefaultFields = ($generationMethod === 'ai');
@endphp

@if($showDefaultFields)
let selectedDefects = @json($selectedDefects ?? []);
let selectedRemainingLife = '{{ $selectedRemainingLife ?? "" }}';

// Make sure arrays are properly initialized
if (!Array.isArray(selectedDefects)) {
    selectedDefects = [];
}
@else
    // Default fields not shown, initialize empty arrays
    let selectedDefects = [];
    let selectedRemainingLife = '';
@endif

// Tag button handler functions - defined immediately
(function() {
    'use strict';
    
    // Select defect (single-select, stored as array)
    function handleDefectClick(button, value) {
        if (!button || !value) return false;
        
        // Ensure array is initialized
        if (!Array.isArray(selectedDefects)) {
            selectedDefects = [];
        }
        
        // If clicking the same button, deselect
        if (selectedDefects.length === 1 && selectedDefects[0] === value) {
            selectedDefects = [];
            button.classList.remove('selected');
        } else {
            // Remove selected from all buttons first
            var allButtons = document.querySelectorAll('.defects-tag');
            for (var i = 0; i < allButtons.length; i++) {
                allButtons[i].classList.remove('selected');
            }
            // Select new button (store as array with single item)
            selectedDefects = [value];
            button.classList.add('selected');
        }
        
        // Update hidden input (always store as array)
        var input = document.getElementById('defects-input');
        if (input) {
            input.value = JSON.stringify(selectedDefects);
        }
        
        return false;
    }
    
    // Select remaining life (single-select only)
    function handleRemainingLifeClick(button, value) {
        if (!button || !value) return false;
        
        // If clicking the same button, deselect
        if (selectedRemainingLife === value) {
            selectedRemainingLife = '';
            button.classList.remove('selected');
        } else {
            // Remove selected from all buttons first
            var allButtons = document.querySelectorAll('.remaining-life-tag');
            for (var i = 0; i < allButtons.length; i++) {
                allButtons[i].classList.remove('selected');
            }
            // Select new button
            selectedRemainingLife = value;
            button.classList.add('selected');
        }
        
        // Update hidden input
        var input = document.getElementById('remaining-life-input');
        if (input) {
            input.value = selectedRemainingLife;
        }
        
        return false;
    }
    
    // Make functions globally available for onclick handlers
    window.toggleDefectTag = handleDefectClick;
    window.selectRemainingLife = handleRemainingLifeClick;
    
    // Set up event delegation when DOM is ready (only if default fields are shown)
    function initTagButtons() {
        // Defects container - event delegation (only initialize if element exists)
        var defectsContainer = document.getElementById('defects-container');
        if (defectsContainer) {
            defectsContainer.addEventListener('click', function(e) {
                var button = e.target;
                // Check if clicked element is a button or inside a button
                while (button && !button.classList.contains('defects-tag')) {
                    button = button.parentElement;
                }
                
                if (button && button.classList.contains('defects-tag')) {
                    e.preventDefault();
                    e.stopPropagation();
                    var value = button.getAttribute('data-value');
                    if (value) {
                        handleDefectClick(button, value);
                    }
                }
            }, true); // Use capture phase for better reliability
        }
        
        // Remaining life container - event delegation (only initialize if element exists)
        var remainingLifeContainer = document.getElementById('remaining-life-container');
        if (remainingLifeContainer) {
            remainingLifeContainer.addEventListener('click', function(e) {
                var button = e.target;
                // Check if clicked element is a button or inside a button
                while (button && !button.classList.contains('remaining-life-tag')) {
                    button = button.parentElement;
                }
                
                if (button && button.classList.contains('remaining-life-tag')) {
                    e.preventDefault();
                    e.stopPropagation();
                    var value = button.getAttribute('data-value');
                    if (value) {
                        handleRemainingLifeClick(button, value);
                    }
                }
            }, true); // Use capture phase for better reliability
        }
    }
    
    // Initialize when DOM is ready (always try, but handlers check for element existence)
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTagButtons);
    } else {
        initTagButtons();
    }
})();

// Update hidden inputs and initialize button states on page load
// Only initialize if default fields are shown (AI mode)
document.addEventListener('DOMContentLoaded', function() {
    const defectsInput = document.getElementById('defects-input');
    const remainingLifeInput = document.getElementById('remaining-life-input');
    
    // Only initialize if elements exist (they only exist in AI mode)
    if (!defectsInput || !remainingLifeInput) {
        return; // Default fields not shown, skip initialization
    }
    
    // Initialize hidden inputs
    defectsInput.value = JSON.stringify(selectedDefects);
        remainingLifeInput.value = selectedRemainingLife;
    
    // Convert defects JSON to array format on form submit for Laravel validation
    const form = defectsInput.closest('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Ensure defects is sent as array format for Laravel
            // The validation rule expects: 'defects' => 'nullable|array'
            var defectsArray = [];
            try {
                var currentValue = defectsInput.value;
                if (currentValue) {
                    var parsed = JSON.parse(currentValue);
                    if (Array.isArray(parsed)) {
                        defectsArray = parsed.filter(function(item) {
                            return item !== null && item !== '' && item !== undefined;
                        });
                    }
                }
            } catch(err) {
                console.error('Error parsing defects:', err);
                defectsArray = [];
            }
            
            // Remove the JSON input before submit
            var oldInputValue = defectsInput.value;
            defectsInput.name = 'defects_json_old'; // Rename to avoid conflict
            defectsInput.style.display = 'none';
            
            // Add array notation inputs for Laravel to receive as array
            if (defectsArray.length > 0) {
                defectsArray.forEach(function(defect) {
                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'defects[]';
                    input.value = defect;
                    form.appendChild(input);
                });
            } else {
                // Send empty array by sending a single empty array notation
                // Laravel will receive it as an empty array
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'defects[]';
                input.value = '';
                form.appendChild(input);
            }
        }, false);
    }
    
    // Initialize button states based on saved values
    if (Array.isArray(selectedDefects) && selectedDefects.length > 0) {
        selectedDefects.forEach(function(defect) {
            const button = document.querySelector('.defects-tag[data-value="' + CSS.escape(defect) + '"]');
            if (button) {
                button.classList.add('selected');
            }
        });
    }
    
    if (selectedRemainingLife) {
        const button = document.querySelector('.remaining-life-tag[data-value="' + CSS.escape(selectedRemainingLife) + '"]');
        if (button) {
            button.classList.add('selected');
        }
    }
    
    // Initialize button visual states based on saved values
    // Defects: single-select, stored as array
    if (Array.isArray(selectedDefects) && selectedDefects.length > 0) {
        // Only show the first item selected (single select)
        var selectedDefect = selectedDefects[0];
        var buttons = document.querySelectorAll('.defects-tag');
        for (var i = 0; i < buttons.length; i++) {
            if (buttons[i].getAttribute('data-value') === selectedDefect) {
                buttons[i].classList.add('selected');
                break;
            }
        }
    }
    
    if (selectedRemainingLife) {
        var buttons = document.querySelectorAll('.remaining-life-tag');
        for (var i = 0; i < buttons.length; i++) {
            if (buttons[i].getAttribute('data-value') === selectedRemainingLife) {
                buttons[i].classList.add('selected');
                break;
            }
        }
    }
});

// Image handling functions
function handleImageFiles(files) {
    const container = document.getElementById('image-preview-container');
    Array.from(files).forEach(file => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'col-md-3 mb-2 image-preview-item';
                div.innerHTML = `
                    <div class="position-relative">
                        <img src="${e.target.result}" class="img-thumbnail" style="width: 100%; height: 150px; object-fit: cover;">
                        <button type="button" class="btn btn-sm btn-danger position-absolute" style="top: 5px; right: 5px;" onclick="this.closest('.image-preview-item').remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                container.appendChild(div);
            };
            reader.readAsDataURL(file);
        }
    });
}

function deletePhotoPreview(button, photoPath) {
    if (confirm('Are you sure you want to delete this photo?')) {
        // Remove from preview
        button.closest('.image-preview-item').remove();
        
        // Send delete request
        fetch('{{ route("surveyor.survey.section.deletePhoto", [$survey, $section]) }}', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                photo_path: photoPath
            })
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                alert('Error deleting photo');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}

// Drag and Drop functionality
document.addEventListener('DOMContentLoaded', function() {
    const imageDropArea = document.getElementById('image-drop-area');
    const photosInput = document.getElementById('photos');
    
    if (imageDropArea && photosInput) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            imageDropArea.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        ['dragenter', 'dragover'].forEach(eventName => {
            imageDropArea.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            imageDropArea.addEventListener(eventName, unhighlight, false);
        });
        
        function highlight(e) {
            imageDropArea.classList.add('drag-over');
        }
        
        function unhighlight(e) {
            imageDropArea.classList.remove('drag-over');
        }
        
        imageDropArea.addEventListener('drop', handleDrop, false);
        
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            photosInput.files = files;
            handleImageFiles(files);
        }
        
        imageDropArea.addEventListener('click', function() {
            photosInput.click();
        });
    }
});

// Enhanced sidebar interactivity (non-layout affecting)
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scroll to active section in sidebar (only if element exists)
    const activeSection = document.querySelector('.section-link.active');
    if (activeSection) {
        // Use requestAnimationFrame to avoid blocking layout
        requestAnimationFrame(function() {
            setTimeout(() => {
                try {
                    activeSection.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'center' 
                    });
                } catch(e) {
                    // Fallback if scrollIntoView fails
                    console.log('Scroll animation skipped');
                }
            }, 500);
        });
    }

    // Add smooth transitions when navigating between sections
    const sectionLinks = document.querySelectorAll('.section-link');
    sectionLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Add loading state (non-layout affecting)
            const formCard = document.querySelector('.card-body');
            if (formCard) {
                formCard.style.opacity = '0.6';
                formCard.style.pointerEvents = 'none';
            }
        });
    });

    // Keyboard shortcuts for navigation
    document.addEventListener('keydown', function(e) {
        // Don't trigger shortcuts when typing in inputs/textarea
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.isContentEditable) {
            return;
        }

        // Ctrl+Arrow Right or Alt+Right for next section
        if ((e.ctrlKey || e.altKey) && e.key === 'ArrowRight') {
            e.preventDefault();
            const nextBtn = document.querySelector('a.btn-nav-next');
            if (nextBtn && !nextBtn.disabled) {
                nextBtn.click();
            }
        }

        // Ctrl+Arrow Left or Alt+Left for previous section
        if ((e.ctrlKey || e.altKey) && e.key === 'ArrowLeft') {
            e.preventDefault();
            const prevBtn = document.querySelector('a.btn-nav-previous');
            if (prevBtn && !prevBtn.disabled) {
                prevBtn.click();
            }
        }
    });
});
</script>
@endpush

@push('styles')
<style>
    /* Prevent FOUC - Hide content until CSS is loaded */
    .dashboard-wrapper {
        visibility: visible !important;
        opacity: 1 !important;
    }
    
    /* Fix layout issue - ensure content doesn't go under sidebar */
    .dashboard-content {
        overflow-x: hidden;
        position: relative;
        width: 100%;
        max-width: 100%;
    }
    
    /* Ensure proper layout regardless of JavaScript */
    .dashboard-content .row {
        margin-left: 0 !important;
        margin-right: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
    }
    
    .dashboard-content .col-xl-8,
    .dashboard-content .col-xl-4 {
        padding-left: 15px !important;
        padding-right: 15px !important;
        width: 100% !important;
    }
    
    @media (min-width: 992px) {
        .dashboard-content .col-xl-8 {
            flex: 0 0 66.666667% !important;
            max-width: 66.666667% !important;
        }
        .dashboard-content .col-xl-4 {
            flex: 0 0 33.333333% !important;
            max-width: 33.333333% !important;
        }
    }
    
    /* Tag Button Styles */
    .tag-buttons-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 8px;
    }
    
    .tag-button {
        padding: 8px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 20px;
        background: white;
        color: #1a202c;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        outline: none;
    }
    
    .tag-button:hover {
        border-color: #1a202c;
        background: #f9fafb;
    }
    
    .tag-button.selected {
        background-color: #C1EC4A !important;
        border-color: #C1EC4A !important;
        color: #1A202C !important;
        font-weight: 600 !important;
        transform: scale(1.05);
        box-shadow: 0 2px 8px rgba(193, 236, 74, 0.3);
    }
    
    .tag-button:active {
        transform: scale(0.98);
    }
    
    /* Dropzone Styles */
    .dropzone-area {
        border: 2px dashed #e5e7eb;
        border-radius: 8px;
        padding: 40px 20px;
        text-align: center;
        background: #f9fafb;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .dropzone-area:hover {
        border-color: #C1EC4A;
        background: #f0f9ff;
    }
    
    .dropzone-area.drag-over {
        border-color: #C1EC4A;
        background: #fffef0;
        border-style: solid;
    }
    
    .dropzone-text {
        margin: 0;
        color: #6b7280;
        font-size: 14px;
    }
    
    /* Tab Styles */
    .nav-tabs .nav-link {
        color: #6b7280;
        border: none;
        border-bottom: 2px solid transparent;
    }
    
    .nav-tabs .nav-link.active {
        color: #1A202C;
        border-bottom: 2px solid #C1EC4A;
        background: transparent;
    }
    
    .nav-tabs .nav-link:hover {
        border-color: transparent;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .image-preview-item img {
        border-radius: 8px;
    }
    
    /* Form Navigation Bar Styles */
    .form-navigation-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 0;
        margin-top: 30px;
        border-top: 1px solid #e5e7eb;
        gap: 15px;
    }
    
    .nav-button-group {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    
    .btn-nav {
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: none;
        outline: none;
    }
    
    .btn-nav i {
        font-size: 14px;
    }
    
    /* Previous Button - Theme Outline Style */
    .btn-nav-previous {
        background-color: #ffffff;
        color: var(--primary, #C1EC4A);
        border: 2px solid var(--primary, #C1EC4A);
    }
    
    .btn-nav-previous:hover:not(:disabled) {
        background-color: var(--primary, #C1EC4A);
        color: var(--ink, #1A202C);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(193, 236, 74, 0.3);
    }
    
    .btn-nav-previous:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    /* Save & Continue Button - Theme Primary Green */
    .btn-nav-save {
        background-color: var(--primary, #C1EC4A);
        color: var(--ink, #1A202C);
        border: 2px solid var(--primary, #C1EC4A);
        font-weight: 700;
    }
    
    .btn-nav-save:hover {
        background-color: #A8D83A;
        border-color: #A8D83A;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(193, 236, 74, 0.4);
    }
    
    .btn-nav-save:active {
        transform: translateY(0);
    }
    
    /* View All Button - Theme Outline Style */
    .btn-nav-view-all {
        background-color: #ffffff;
        color: var(--primary, #C1EC4A);
        border: 2px solid var(--primary, #C1EC4A);
    }
    
    .btn-nav-view-all:hover {
        background-color: var(--primary, #C1EC4A);
        color: var(--ink, #1A202C);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(193, 236, 74, 0.3);
    }
    
    /* Next Button - Theme Dark with Primary Green Text */
    .btn-nav-next {
        background-color: var(--ink, #1A202C);
        color: var(--primary, #C1EC4A);
        border: 2px solid var(--ink, #1A202C);
    }
    
    .btn-nav-next:hover:not(:disabled) {
        background-color: #2D3748;
        border-color: #2D3748;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(26, 32, 44, 0.4);
    }
    
    .btn-nav-next:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        background-color: #4A5568;
        color: #718096;
    }
    
    @media (max-width: 768px) {
        .form-navigation-bar {
            flex-direction: column;
            gap: 15px;
        }
        
        .nav-button-group {
            width: 100%;
            justify-content: center;
        }
        
        .btn-nav {
            flex: 1;
            justify-content: center;
        }
    }
</style>
@endpush


