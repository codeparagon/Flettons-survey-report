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

@push('styles')
<style>
    .form-section-header {
        background-color: #1A202C !important;
        color: #C1EC4A !important;
        padding: 16px 24px;
        border-radius: 0;
        margin: 0 0 24px 0;
        border-bottom: 3px solid #C1EC4A;
        display: flex;
        align-items: center;
        gap: 12px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .form-section-header i {
        font-size: 20px;
        color: #C1EC4A;
    }
    
    .form-section-header h5 {
        color: #C1EC4A !important;
        font-weight: 600;
    }
    
    .form-section-header small {
        color: rgba(193, 236, 74, 0.8) !important;
    }
    
    .form-section {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 24px;
        margin-bottom: 24px;
    }
    
    .form-section-title {
        color: #1a202c;
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #e5e7eb;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Tag buttons (match defects/remaining-life style) */
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
        background: #fff;
        color: #1a202c;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        outline: none;
    }
    .tag-button:hover { border-color: #1a202c; background: #f9fafb; }
    .tag-button.selected {
        background-color: #C1EC4A !important;
        border-color: #C1EC4A !important;
        color: #1A202C !important;
        font-weight: 600 !important;
        transform: scale(1.05);
        box-shadow: 0 2px 8px rgba(193, 236, 74, 0.3);
    }
    .tag-button:active { transform: scale(0.98); }
</style>
@endpush

<div class="row">
    <div class="col-xl-8">
        <div class="card">
            <div class="form-section-header">
                <i class="fas fa-plus-circle"></i>
                <div>
                    <h5 class="mb-0">Create New Section</h5>
                    <small>Add a new survey section with all configurations</small>
                </div>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                @endif
                
                <form action="{{ route('admin.survey-sections.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Basic Information -->
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-info-circle text-primary"></i>
                            Basic Information
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">
                                        <i class="fas fa-tag text-muted"></i> Internal Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" 
                                           placeholder="e.g., roofs, walls" required>
                                    <small class="form-text text-muted">Used in code (lowercase, no spaces)</small>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="display_name">
                                        <i class="fas fa-signature text-muted"></i> Display Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('display_name') is-invalid @enderror" 
                                           id="display_name" name="display_name" value="{{ old('display_name') }}" 
                                           placeholder="e.g., Roofs, Walls" required>
                                    <small class="form-text text-muted">Name shown to users</small>
                                    @error('display_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="survey_category_id">
                                        <i class="fas fa-folder text-muted"></i> Category <span class="text-danger">*</span>
                                    </label>
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
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sort_order">
                                        <i class="fas fa-sort-numeric-down text-muted"></i> Sort Order <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                           id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" 
                                           min="0" required>
                                    <small class="form-text text-muted">Lower numbers appear first</small>
                                    @error('sort_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">
                                <i class="fas fa-align-left text-muted"></i> Description
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" 
                                      placeholder="Brief description of this section">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
                                       value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    <i class="fas fa-toggle-on"></i> Active Section
                                </label>
                            </div>
                            <small class="form-text text-muted">Inactive sections won't be shown to users</small>
                        </div>
                    </div>

                    <!-- Survey Levels -->
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-layer-group text-primary"></i>
                            Survey Levels
                        </div>
                        <div class="form-group">
                            <label>
                                <i class="fas fa-sitemap text-muted"></i> Assign to Levels
                            </label>
                            <div id="levels-container" class="tag-buttons-container">
                                @php $oldLevels = old('levels', []); @endphp
                                @foreach($levels as $level)
                                    <button type="button"
                                            class="tag-button level-tag {{ in_array($level->id, $oldLevels ?? []) ? 'selected' : '' }}"
                                            data-value="{{ $level->id }}">
                                        {{ $level->display_name }}
                                    </button>
                                @endforeach
                            </div>
                            <!-- Hidden holder, populated on submit -->
                            <div id="levels-hidden-holder"></div>
                            @error('levels')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted d-block mt-2">Click to select one or more levels. Selected levels are highlighted.</small>
                        </div>
                    </div>

                    <!-- Icon Configuration -->
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-image text-primary"></i>
                            Icon Configuration
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Upload Icon Image</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input @error('icon_file') is-invalid @enderror" 
                                           id="icon_file" name="icon_file" accept="image/*">
                                    <label class="custom-file-label" for="icon_file">Choose file...</label>
                                </div>
                                <small class="form-text text-muted">Upload a custom icon image (PNG, JPG, SVG, max 2MB)</small>
                                @error('icon_file')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
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

                    <!-- Generation Method -->
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-cogs text-primary"></i>
                            Generation Method
                        </div>
                        <div class="form-group">
                            <label for="generation_method">
                                <i class="fas fa-database text-muted"></i> Data Source <span class="text-danger">*</span>
                            </label>
                            <select class="form-control @error('generation_method') is-invalid @enderror" 
                                    id="generation_method" name="generation_method" required>
                                <option value="database" {{ old('generation_method', 'database') == 'database' ? 'selected' : '' }}>
                                    📊 Database-Driven (Surveyor input)
                                </option>
                                <option value="ai" {{ old('generation_method') == 'ai' ? 'selected' : '' }}>
                                    🤖 AI-Generated (ChatGPT)
                                </option>
                            </select>
                            <small class="form-text text-muted">
                                <strong>Database-Driven:</strong> Surveyors fill forms manually, you can set default report template.<br>
                                <strong>AI-Generated:</strong> AI generates report content, you can configure AI prompt template.
                            </small>
                            @error('generation_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        @php
                            $fieldConfig = old('field_config', []);
                        @endphp

                        {{-- AI Prompt Template (for AI-driven sections) --}}
                        <div class="form-group" id="ai-prompt-group" style="display: {{ old('generation_method', 'database') == 'ai' ? 'block' : 'none' }};">
                            <label for="ai_prompt_template">
                                <i class="fas fa-magic text-muted"></i> AI Prompt Template <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('field_config.ai_prompt_template') is-invalid @enderror" 
                                      id="ai_prompt_template" 
                                      name="field_config[ai_prompt_template]" 
                                      rows="5"
                                      placeholder="Analyze the property's section. Include: condition assessment, defects noted, recommendations, expected lifespan. Format: Professional surveyor report style.">{{ old('field_config.ai_prompt_template', $fieldConfig['ai_prompt_template'] ?? '') }}</textarea>
                            <small class="form-text text-muted">Template for AI generation.</small>
                            @error('field_config.ai_prompt_template')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            {{-- AI Prompt Helper --}}
                            <div class="mt-3">
                                <label for="ai_prompt_helper">
                                    <i class="fas fa-lightbulb text-muted"></i> AI Prompt Helper
                                </label>
                                <textarea class="form-control @error('field_config.ai_prompt_helper') is-invalid @enderror" 
                                          id="ai_prompt_helper" 
                                          name="field_config[ai_prompt_helper]" 
                                          rows="3"
                                          placeholder="Add helpful tips, examples, or guidelines for creating effective AI prompts...">{{ old('field_config.ai_prompt_helper', $fieldConfig['ai_prompt_helper'] ?? '') }}</textarea>
                                <small class="form-text text-muted">Helpful guidance text shown to admins when creating AI prompts (optional)</small>
                                @error('field_config.ai_prompt_helper')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Report Template (for Database-driven sections) --}}
                        <div class="form-group" id="report-template-group" style="display: {{ old('generation_method', 'database') == 'database' ? 'block' : 'none' }};">
                            <label for="report_template">
                                <i class="fas fa-file-alt text-muted"></i> Default Report Template
                            </label>
                            <textarea class="form-control @error('field_config.report_template') is-invalid @enderror" 
                                      id="report_template" 
                                      name="field_config[report_template]" 
                                      rows="6"
                                      placeholder="Enter default report text that will be pre-filled in the surveyor form. This can be edited by the surveyor...">{{ old('field_config.report_template', $fieldConfig['report_template'] ?? '') }}</textarea>
                            <small class="form-text text-muted">Default report text saved to database. Surveyors can edit this in the surveyor dashboard.</small>
                            @error('field_config.report_template')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @php
                            $defectsOptions = old('field_config.defects_options', ['Rot', 'Deflection', 'Moss', 'Lichen', 'ACMs']);
                            $remainingLifeOptions = old('field_config.remaining_life_options', ['0 yrs', '1-5 yrs', '6-10 yrs', '10+ yrs']);
                            if (!is_array($defectsOptions)) {
                                $defectsOptions = ['Rot', 'Deflection', 'Moss', 'Lichen', 'ACMs'];
                            }
                            if (!is_array($remainingLifeOptions)) {
                                $remainingLifeOptions = ['0 yrs', '1-5 yrs', '6-10 yrs', '10+ yrs'];
                            }
                        @endphp
                        
                        {{-- Defects and Remaining Life Options (Always Editable) --}}
                        <div class="form-group" id="default-fields-config-group">
                            <div class="alert alert-info mb-3">
                                <i class="fas fa-info-circle"></i> <strong>Default Fields Configuration</strong><br>
                                <small>Configure options for defects and remaining life fields. These will appear as selectable buttons in the surveyor form.</small>
                            </div>
                            
                            <!-- Defects Options -->
                            <div class="form-group">
                                <label>
                                    <i class="fas fa-exclamation-triangle text-muted"></i> Defects Options <span class="text-danger">*</span>
                                </label>
                                <div id="defects-options-list" class="mb-2">
                                    @foreach($defectsOptions as $index => $option)
                                        <div class="input-group mb-2 defects-option-item" data-index="{{ $index }}">
                                            <input type="text" 
                                                   class="form-control defects-option-input" 
                                                   name="field_config[defects_options][{{ $index }}]" 
                                                   value="{{ $option }}" 
                                                   placeholder="Enter defect option">
                                            <div class="input-group-append">
                                                <button type="button" 
                                                        class="btn btn-danger remove-defects-option" 
                                                        onclick="removeDefectsOption({{ $index }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" class="btn btn-sm btn-primary" onclick="addDefectsOption()">
                                    <i class="fas fa-plus"></i> Add Defect Option
                                </button>
                                <small class="form-text text-muted d-block mt-2">These options will appear as single-select buttons in the surveyor form</small>
                            </div>

                            <!-- Remaining Life Options -->
                            <div class="form-group">
                                <label>
                                    <i class="fas fa-clock text-muted"></i> Remaining Life Options <span class="text-danger">*</span>
                                </label>
                                <div id="remaining-life-options-list" class="mb-2">
                                    @foreach($remainingLifeOptions as $index => $option)
                                        <div class="input-group mb-2 remaining-life-option-item" data-index="{{ $index }}">
                                            <input type="text" 
                                                   class="form-control remaining-life-option-input" 
                                                   name="field_config[remaining_life_options][{{ $index }}]" 
                                                   value="{{ $option }}" 
                                                   placeholder="Enter remaining life option">
                                            <div class="input-group-append">
                                                <button type="button" 
                                                        class="btn btn-danger remove-remaining-life-option" 
                                                        onclick="removeRemainingLifeOption({{ $index }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" class="btn btn-sm btn-primary" onclick="addRemainingLifeOption()">
                                    <i class="fas fa-plus"></i> Add Remaining Life Option
                                </button>
                                <small class="form-text text-muted d-block mt-2">These options will appear as single-select buttons in the surveyor form</small>
                            </div>
                        </div>

                    </div>

                    <!-- Custom Fields Configuration -->
                    <div class="form-section" id="fields">
                        <div class="form-section-title">
                            <i class="fas fa-list-alt text-primary"></i>
                            Custom Fields Configuration (Optional)
                        </div>
                        <p class="text-muted mb-3">
                            <i class="fas fa-info-circle"></i> Add custom fields to replace default fields. If custom fields are added, they will be used instead of default fields (Report Content, Material, Defects, Remaining Life, Additional Notes).
                        </p>
                        <div class="form-group">
                            <div id="fields-list" class="mb-3">
                                <div class="alert alert-info mb-3">
                                    <i class="fas fa-info-circle"></i> No custom fields configured. Default fields will be used. After creating the section, you can add custom fields from the edit page.
                                </div>
                            </div>
                        </div>
                    </div>
                    
                <!-- Form Actions -->
                <div class="mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Create Section
                    </button>
                    <a href="{{ route('admin.survey-sections.index') }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <!-- Help Card -->
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-question-circle"></i> Help</h5>
            </div>
            <div class="card-body">
                <h6>Internal Name</h6>
                <p class="text-muted small">Use lowercase letters, numbers, and underscores only. This is used internally in the system.</p>
                
                <h6>Display Name</h6>
                <p class="text-muted small">This is what users will see. Use proper capitalization and spaces.</p>
                
                <h6>Category</h6>
                <p class="text-muted small">Select which category this section belongs to. Categories help organize sections.</p>
                
                <h6>Survey Levels</h6>
                <p class="text-muted small">Select which survey levels this section should be available in. Hold Ctrl/Cmd to select multiple.</p>
                
                <h6>Generation Method</h6>
                <p class="text-muted small">Choose how this section's content will be generated - manually by surveyors or by AI.</p>
                
                <h6>Icon</h6>
                <p class="text-muted small">Use FontAwesome icon classes or upload a custom image. Visit <a href="https://fontawesome.com/icons" target="_blank">FontAwesome</a> for available icons.</p>
                
                <h6>Sort Order</h6>
                <p class="text-muted small">Sections with lower numbers appear first in lists. Use increments of 10 for easy reordering.</p>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Generation Method Toggle
    const generationMethodSelect = document.getElementById('generation_method');
    const aiPromptGroup = document.getElementById('ai-prompt-group');
    const reportTemplateGroup = document.getElementById('report-template-group');
    
    if (generationMethodSelect) {
        generationMethodSelect.addEventListener('change', function() {
            if (this.value === 'ai') {
                aiPromptGroup.style.display = 'block';
                reportTemplateGroup.style.display = 'none';
                // Make AI prompt template required
                const aiPromptTemplate = document.getElementById('ai_prompt_template');
                if (aiPromptTemplate) {
                    aiPromptTemplate.setAttribute('required', 'required');
                }
            } else {
                aiPromptGroup.style.display = 'none';
                reportTemplateGroup.style.display = 'block';
                // Remove required from AI prompt template
                const aiPromptTemplate = document.getElementById('ai_prompt_template');
                if (aiPromptTemplate) {
                    aiPromptTemplate.removeAttribute('required');
                }
            }
        });
    }

    // File input label update
    const iconFileInput = document.querySelector('#icon_file');
    if (iconFileInput) {
        iconFileInput.addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || 'Choose file...';
            const label = document.querySelector('label[for="icon_file"]');
            if (label) {
                label.textContent = fileName;
            }
        });
    }
});

// Levels multi-select tag buttons
(function() {
    let selectedLevels = @json(old('levels', []));
    if (!Array.isArray(selectedLevels)) selectedLevels = [];

    function updateHiddenInputs() {
        const holder = document.getElementById('levels-hidden-holder');
        if (!holder) return;
        holder.innerHTML = '';
        selectedLevels.forEach(function(id) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'levels[]';
            input.value = id;
            holder.appendChild(input);
        });
    }

    function toggleLevel(button, id) {
        const idx = selectedLevels.indexOf(id);
        if (idx > -1) {
            selectedLevels.splice(idx, 1);
            button.classList.remove('selected');
        } else {
            selectedLevels.push(id);
            button.classList.add('selected');
        }
        updateHiddenInputs();
    }

    document.addEventListener('click', function(e) {
        let el = e.target;
        while (el && !el.classList?.contains('level-tag')) {
            el = el.parentElement;
        }
        if (el && el.classList.contains('level-tag')) {
            e.preventDefault();
            const id = parseInt(el.getAttribute('data-value'));
            if (!isNaN(id)) toggleLevel(el, id);
        }
    }, true);

    // Initialize hidden inputs on load
    updateHiddenInputs();
})();

// Defects Options Management
let defectsOptionIndex = {{ count($defectsOptions) }};

function addDefectsOption(value = '') {
    const container = document.getElementById('defects-options-list');
    const index = defectsOptionIndex++;
    
    const div = document.createElement('div');
    div.className = 'input-group mb-2 defects-option-item';
    div.setAttribute('data-index', index);
    div.innerHTML = `
        <input type="text" 
               class="form-control defects-option-input" 
               name="field_config[defects_options][${index}]" 
               value="${value}" 
               placeholder="Enter defect option">
        <div class="input-group-append">
            <button type="button" 
                    class="btn btn-danger remove-defects-option" 
                    onclick="removeDefectsOption(${index})">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(div);
}

function removeDefectsOption(index) {
    const item = document.querySelector(`.defects-option-item[data-index="${index}"]`);
    if (item) {
        item.remove();
    }
}

// Remaining Life Options Management
let remainingLifeOptionIndex = {{ count($remainingLifeOptions) }};

function addRemainingLifeOption(value = '') {
    const container = document.getElementById('remaining-life-options-list');
    const index = remainingLifeOptionIndex++;
    
    const div = document.createElement('div');
    div.className = 'input-group mb-2 remaining-life-option-item';
    div.setAttribute('data-index', index);
    div.innerHTML = `
        <input type="text" 
               class="form-control remaining-life-option-input" 
               name="field_config[remaining_life_options][${index}]" 
               value="${value}" 
               placeholder="Enter remaining life option">
        <div class="input-group-append">
            <button type="button" 
                    class="btn btn-danger remove-remaining-life-option" 
                    onclick="removeRemainingLifeOption(${index})">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(div);
}

function removeRemainingLifeOption(index) {
    const item = document.querySelector(`.remaining-life-option-item[data-index="${index}"]`);
    if (item) {
        item.remove();
    }
}
</script>
@endpush
