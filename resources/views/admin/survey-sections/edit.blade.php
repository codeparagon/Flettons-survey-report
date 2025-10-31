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
    
    .section-info-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 16px;
        border-left: 4px solid #1a202c;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .section-info-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .section-info-item:last-child {
        border-bottom: none;
    }
    
    .section-info-label {
        font-weight: 600;
        color: #6b7280;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .section-info-value {
        color: #1a202c;
        font-weight: 500;
        text-align: right;
    }
    
    .section-icon-preview {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        background: #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin: 0 auto;
    }
    
    .section-icon-preview img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    /* Tag buttons for Levels (match theme) */
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
                <i class="fas fa-edit"></i>
                <div>
                    <h5 class="mb-0">Edit Section</h5>
                    <small>{{ $surveySection->display_name }}</small>
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
                
                <form action="{{ route('admin.survey-sections.update', $surveySection) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
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
                                           id="name" name="name" value="{{ old('name', $surveySection->name) }}" 
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
                                           id="display_name" name="display_name" value="{{ old('display_name', $surveySection->display_name) }}" 
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
                                            <option value="{{ $category->id }}" {{ old('survey_category_id', $surveySection->survey_category_id) == $category->id ? 'selected' : '' }}>
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
                                           id="sort_order" name="sort_order" value="{{ old('sort_order', $surveySection->sort_order) }}" 
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
                                      placeholder="Brief description of this section">{{ old('description', $surveySection->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
                                       value="1" {{ old('is_active', $surveySection->is_active) ? 'checked' : '' }}>
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
                                @php $selectedLevels = old('levels', $surveySection->levels->pluck('id')->toArray()); @endphp
                                @foreach($levels as $level)
                                    <button type="button"
                                            class="tag-button level-tag {{ in_array($level->id, $selectedLevels ?? []) ? 'selected' : '' }}"
                                            data-value="{{ $level->id }}">
                                        {{ $level->display_name }}
                                    </button>
                                @endforeach
                            </div>
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
                                @if($surveySection->icon && strpos($surveySection->icon, 'storage/') !== false)
                                    <div class="mt-3 text-center">
                                        <div class="section-icon-preview">
                                            <img src="{{ asset($surveySection->icon) }}" alt="Current icon" onerror="this.style.display='none'; this.parentElement.innerHTML='<i class=\'fas fa-image text-muted\'></i>';">
                                        </div>
                                        <small class="text-muted d-block mt-2">Current icon</small>
                                    </div>
                                @endif
                                @error('icon_file')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
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
                                <option value="database" {{ old('generation_method', $surveySection->generation_method ?? 'database') == 'database' ? 'selected' : '' }}>
                                    📊 Database-Driven (Surveyor input)
                                </option>
                                <option value="ai" {{ old('generation_method', $surveySection->generation_method ?? 'database') == 'ai' ? 'selected' : '' }}>
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
                            $fieldConfig = $surveySection->field_config ?? [];
                        @endphp

                        {{-- AI Prompt Template (for AI-driven sections) --}}
                        <div class="form-group" id="ai-prompt-group" style="display: {{ (old('generation_method', $surveySection->generation_method ?? 'database') == 'ai') ? 'block' : 'none' }};">
                            <label for="ai_prompt_template">
                                <i class="fas fa-magic text-muted"></i> AI Prompt Template <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('field_config.ai_prompt_template') is-invalid @enderror" 
                                      id="ai_prompt_template" 
                                      name="field_config[ai_prompt_template]" 
                                      rows="5"
                                      placeholder="Analyze the property's {{ strtolower($surveySection->display_name) }} section. Include: condition assessment, defects noted, recommendations, expected lifespan. Format: Professional surveyor report style.">{{ old('field_config.ai_prompt_template', $fieldConfig['ai_prompt_template'] ?? '') }}</textarea>
                            <small class="form-text text-muted">Template for AI generation. Use {{ $surveySection->display_name }} to reference section name.</small>
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
                        <div class="form-group" id="report-template-group" style="display: {{ (old('generation_method', $surveySection->generation_method ?? 'database') == 'database') ? 'block' : 'none' }};">
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
                            $hasCustomFields = $surveySection->fields && $surveySection->fields->where('is_active', true)->count() > 0;
                            $fieldConfig = $surveySection->field_config ?? [];
                            $defectsOptions = $fieldConfig['defects_options'] ?? ['Rot', 'Deflection', 'Moss', 'Lichen', 'ACMs'];
                            $remainingLifeOptions = $fieldConfig['remaining_life_options'] ?? ['0 yrs', '1-5 yrs', '6-10 yrs', '10+ yrs'];
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
                                <small class="form-text text-muted d-block mt-2">These options will appear as multi-select buttons in the surveyor form</small>
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
                            Custom Fields Configuration
                        </div>
                        <p class="text-muted mb-3">
                            <i class="fas fa-info-circle"></i> Add custom fields to replace default fields. If custom fields are added, they will be used instead of default fields (Condition Rating, Defects Noted, Recommendations, Notes).
                        </p>
                        <div class="form-group">
                            <div id="fields-list" class="mb-3">
                            @php
                                $activeFields = $surveySection->fields ? $surveySection->fields->where('is_active', true)->sortBy('field_order') : collect();
                            @endphp
                            @if($activeFields->count() > 0)
                                @foreach($activeFields as $field)
                                    <div class="card mb-2 field-item border-left-primary" data-field-id="{{ $field->id }}">
                                        <div class="card-body py-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="flex-grow-1">
                                                    <strong class="d-block">{{ $field->field_label }}</strong>
                                                    <div class="mt-1">
                                                        <span class="badge badge-secondary">{{ ucfirst(str_replace('-', ' ', $field->field_type)) }}</span>
                                                        @if($field->is_required)
                                                            <span class="badge badge-warning">Required</span>
                                                        @endif
                                                        @if($field->field_type === 'dropdown' && $field->options)
                                                            <span class="badge badge-info">{{ count($field->options) }} options</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-warning edit-field-btn" data-field-id="{{ $field->id }}" onclick="editField({{ $field->id }})" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger delete-field-btn" data-field-id="{{ $field->id }}" onclick="deleteField({{ $field->id }})" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="alert alert-info mb-3">
                                    <i class="fas fa-info-circle"></i> No custom fields configured. Default fields will be used.
                                </div>
                            @endif
                        </div>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#fieldModal">
                            <i class="fas fa-plus"></i> Add Field
                        </button>
                    </div>
                </div>
                    
                <!-- Form Actions -->
                <div class="mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Update Section
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
        <!-- Section Overview -->
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-eye"></i> Section Overview</h5>
            </div>
            <div class="card-body">
                <div class="">
                    <div class="text-center mb-3">
                        <div class="section-icon-preview">
                            @if($surveySection->icon)
                                @if(strpos($surveySection->icon, 'storage/') !== false)
                                    <img src="{{ asset($surveySection->icon) }}" alt="Icon" onerror="this.style.display='none'; this.parentElement.innerHTML='<i class=\'fas fa-image text-muted\'></i>';">
                                @else
                                    <i class="{{ $surveySection->icon }}"></i>
                                @endif
                            @else
                                <i class="fas fa-cube text-muted"></i>
                            @endif
                        </div>
                        <h5 class="mt-2 mb-1">{{ $surveySection->display_name }}</h5>
                        <small class="text-muted">{{ $surveySection->name }}</small>
                    </div>
                    
                    <div class="section-info-item">
                        <span class="section-info-label">
                            <i class="fas fa-tag"></i> Category
                        </span>
                        <span class="section-info-value">
                            @if($surveySection->category)
                                <span class="badge badge-info">{{ $surveySection->category->display_name }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </span>
                    </div>
                    
                    <div class="section-info-item">
                        <span class="section-info-label">
                            <i class="fas fa-cog"></i> Generation Method
                        </span>
                        <span class="section-info-value">
                            @php
                                $method = $surveySection->generation_method ?? 'database';
                            @endphp
                            @if($method === 'database')
                                <span class="badge badge-secondary">Database</span>
                            @elseif($method === 'ai')
                                <span class="badge badge-success">AI</span>
                            @else
                                <span class="badge badge-secondary">—</span>
                            @endif
                        </span>
                    </div>
                    
                    <div class="section-info-item">
                        <span class="section-info-label">
                            <i class="fas fa-list"></i> Custom Fields
                        </span>
                        <span class="section-info-value">
                            @php
                                $fieldCount = $surveySection->fields ? $surveySection->fields()->active()->count() : 0;
                            @endphp
                            @if($fieldCount > 0)
                                <span class="badge badge-primary">{{ $fieldCount }}</span>
                            @else
                                <span class="badge badge-secondary">Default</span>
                            @endif
                        </span>
                    </div>
                    
                    <div class="section-info-item">
                        <span class="section-info-label">
                            <i class="fas fa-clipboard-check"></i> Assessments
                        </span>
                        <span class="section-info-value">
                            <span class="badge badge-info">{{ $surveySection->assessments->count() }}</span>
                        </span>
                    </div>
                    
                    <div class="section-info-item">
                        <span class="section-info-label">
                            <i class="fas fa-toggle-on"></i> Status
                        </span>
                        <span class="section-info-value">
                            @if($surveySection->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-secondary">Inactive</span>
                            @endif
                        </span>
                    </div>
                    
                    <div class="section-info-item">
                        <span class="section-info-label">
                            <i class="fas fa-sort-numeric-down"></i> Sort Order
                        </span>
                        <span class="section-info-value">
                            <strong>{{ $surveySection->sort_order }}</strong>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Timeline -->
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-history"></i> Timeline</h5>
            </div>
            <div class="card-body">
                <div class="section-info-item">
                    <span class="section-info-label">
                        <i class="fas fa-calendar-plus"></i> Created
                    </span>
                    <span class="section-info-value">
                        <small>{{ $surveySection->created_at->format('M d, Y') }}</small><br>
                        <small class="text-muted">{{ $surveySection->created_at->format('h:i A') }}</small>
                    </span>
                </div>
                <div class="section-info-item">
                    <span class="section-info-label">
                        <i class="fas fa-edit"></i> Last Updated
                    </span>
                    <span class="section-info-value">
                        <small>{{ $surveySection->updated_at->format('M d, Y') }}</small><br>
                        <small class="text-muted">{{ $surveySection->updated_at->format('h:i A') }}</small>
                    </span>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.survey-sections.toggle-status', $surveySection) }}" method="POST" class="mb-2">
                    @csrf
                    <button type="submit" class="btn btn-block {{ $surveySection->is_active ? 'btn-warning' : 'btn-success' }}">
                        <i class="fas fa-{{ $surveySection->is_active ? 'pause' : 'play' }}"></i>
                        {{ $surveySection->is_active ? 'Deactivate' : 'Activate' }} Section
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

<!-- Field Modal -->
<div class="modal fade" id="fieldModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add/Edit Field</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="fieldForm" method="POST" action="{{ route('admin.survey-sections.fields.store', $surveySection) }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="field_id" name="field_id" value="">
                    
                    <div class="form-group">
                        <label for="field_label">Field Label <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="field_label" name="field_label" required>
                        <small class="form-text text-muted">Display name for this field</small>
                    </div>

                    <div class="form-group">
                        <label for="field_type">Field Type <span class="text-danger">*</span></label>
                        <select class="form-control" id="field_type" name="field_type" required>
                            <option value="textarea">Text Area (Multiple Lines)</option>
                            <option value="date">Date</option>
                            <option value="numeric">Numeric</option>
                            <option value="dropdown">Dropdown (Pick List)</option>
                            <option value="single-text">Single Text Input</option>
                            <option value="rating">Rating (Poor/Fair/Good/Excellent)</option>
                        </select>
                    </div>

                    <div class="form-group" id="options-group" style="display: none;">
                        <label>Dropdown Options</label>
                        <div id="dropdown-options-list" class="mb-2">
                            <!-- Options will be added here dynamically -->
                        </div>
                        <button type="button" class="btn btn-sm btn-secondary" id="add-option-btn" onclick="addDropdownOption()">
                            <i class="fas fa-plus"></i> Add Option
                        </button>
                        <input type="hidden" id="options" name="options" value="">
                        <small class="form-text text-muted d-block">Add options for the dropdown. At least one option is required.</small>
                    </div>

                    <div class="form-group">
                        <label for="field_order">Display Order</label>
                        <input type="number" class="form-control" id="field_order" name="field_order" min="0" value="0">
                        <small class="form-text text-muted">Lower numbers appear first</small>
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="is_required" name="is_required" value="1">
                            <label class="form-check-label" for="is_required">
                                Required Field
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="help_text">Help Text</label>
                        <textarea class="form-control" id="help_text" name="help_text" rows="2" placeholder="Additional guidance for surveyors..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="default_value">Default Value</label>
                        <input type="text" class="form-control" id="default_value" name="default_value" placeholder="Pre-filled value">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveFieldBtn">
                        <span id="saveBtnText">Save Field</span>
                        <span id="saveBtnLoader" class="spinner-border spinner-border-sm d-none" role="status"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Define functions in global scope so they're available for onclick handlers
// These must be defined BEFORE DOMContentLoaded to be available for onclick attributes

window.editField = function(fieldId) {
    console.log('Edit button clicked for field:', fieldId);
    
    try {
        const fieldModal = document.getElementById('fieldModal');
        const fieldForm = document.getElementById('fieldForm');
        const fieldTypeSelect = document.getElementById('field_type');
        const optionsGroup = document.getElementById('options-group');
        
        if (!fieldModal || !fieldForm) {
            alert('Form elements not found. Please refresh the page.');
            return;
        }
        
        // Construct URL properly - Laravel route model binding accepts IDs
        const baseUrl = '{{ url("/admin/survey-sections/{$surveySection->id}/fields") }}';
        const showUrl = baseUrl + '/' + fieldId;
        
        console.log('Fetching field data from:', showUrl);
        
        // Fetch field data and populate form
        fetch(showUrl, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                return response.text().then(text => {
                    console.error('Error response:', text);
                    throw new Error('Failed to load field data: ' + response.status);
                });
            }
            return response.json();
        })
        .then(field => {
            console.log('Field data loaded:', field);
            
            // Reset form first
            fieldForm.reset();
            
            // Clear dropdown options if function exists
            if (typeof window.clearDropdownOptions === 'function') {
                window.clearDropdownOptions();
            }
            
            // Populate form fields
            document.getElementById('field_id').value = field.id || '';
            document.getElementById('field_label').value = field.field_label || '';
            document.getElementById('field_type').value = field.field_type || 'textarea';
            document.getElementById('field_order').value = field.field_order || 0;
            document.getElementById('is_required').checked = (field.is_required === true || field.is_required === 1);
            document.getElementById('help_text').value = field.help_text || '';
            document.getElementById('default_value').value = field.default_value || '';
            
            // Handle dropdown options
            if (field.field_type === 'dropdown' && field.options && Array.isArray(field.options) && field.options.length > 0) {
                // Clear existing options first
                if (typeof window.clearDropdownOptions === 'function') {
                    window.clearDropdownOptions();
                }
                // Add options
                field.options.forEach(option => {
                    if (typeof window.addDropdownOption === 'function') {
                        window.addDropdownOption(option);
                    }
                });
                if (optionsGroup) optionsGroup.style.display = 'block';
            } else {
                if (typeof window.clearDropdownOptions === 'function') {
                    window.clearDropdownOptions();
                }
                const optionsInput = document.getElementById('options');
                if (optionsInput) optionsInput.value = '';
            }
            
            // Trigger change event on field type select
            if (fieldTypeSelect) {
                fieldTypeSelect.dispatchEvent(new Event('change'));
            }
            
            // Update modal title
            const modalTitle = document.querySelector('#fieldModal .modal-title');
            if (modalTitle) modalTitle.textContent = 'Edit Field';
            
            // Show modal using Bootstrap
            if (typeof $ !== 'undefined' && $) {
                $(fieldModal).modal('show');
            } else if (fieldModal) {
                fieldModal.classList.add('show');
                fieldModal.style.display = 'block';
                document.body.classList.add('modal-open');
            }
        })
        .catch(error => {
            console.error('Error loading field:', error);
            alert('Failed to load field data: ' + error.message);
        });
    } catch (error) {
        console.error('Error in editField:', error);
        alert('An error occurred while loading the field. Please refresh the page and try again.');
    }
};

window.deleteField = function(fieldId) {
    if (!confirm('Are you sure you want to delete this field? This action cannot be undone.')) {
        return;
    }
    
    console.log('Delete button clicked for field:', fieldId);
    
    try {
        // Construct URL properly - Laravel route model binding accepts IDs
        const baseUrl = '{{ url("/admin/survey-sections/{$surveySection->id}/fields") }}';
        const deleteUrl = baseUrl + '/' + fieldId;
        
        console.log('Deleting field at:', deleteUrl);
        
        // Create a form to submit DELETE request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = deleteUrl;
    
        // Add CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);
        
        // Add method spoofing
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
        // Submit form
        document.body.appendChild(form);
        form.submit();
    } catch (error) {
        console.error('Error in deleteField:', error);
        alert('An error occurred while deleting the field. Please refresh the page and try again.');
    }
};

document.addEventListener('DOMContentLoaded', function() {
    // Custom file input label update
    const iconFileInput = document.getElementById('icon_file');
    if (iconFileInput) {
        iconFileInput.addEventListener('change', function(e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : 'Choose file...';
            const label = e.target.nextElementSibling;
            if (label && label.classList.contains('custom-file-label')) {
                label.textContent = fileName;
            }
        });
    }
    
    const generationMethodSelect = document.getElementById('generation_method');
    const aiPromptGroup = document.getElementById('ai-prompt-group');
    const fieldTypeSelect = document.getElementById('field_type');
    const optionsGroup = document.getElementById('options-group');
    const fieldForm = document.getElementById('fieldForm');
    const fieldModal = document.getElementById('fieldModal');
    
    // Function to update default fields visibility (for custom fields - defects/remaining life always visible)
    function updateDefaultFieldsVisibility() {
        // Defects and remaining life options are always visible regardless of generation method or custom fields
        // This function is kept for future use if needed
    }
    
    // Toggle AI prompt and report template based on generation method
    const reportTemplateGroup = document.getElementById('report-template-group');
    generationMethodSelect.addEventListener('change', function() {
        if (this.value === 'ai') {
            aiPromptGroup.style.display = 'block';
            if (reportTemplateGroup) reportTemplateGroup.style.display = 'none';
        } else {
            aiPromptGroup.style.display = 'none';
            if (reportTemplateGroup) reportTemplateGroup.style.display = 'block';
        }
        // Defects and remaining life options remain visible for both methods
        updateDefaultFieldsVisibility();
    });
    
    // Functions for managing defects options
    window.addDefectsOption = function() {
        const list = document.getElementById('defects-options-list');
        const index = list.children.length;
        const optionDiv = document.createElement('div');
        optionDiv.className = 'input-group mb-2 defects-option-item';
        optionDiv.setAttribute('data-index', index);
        optionDiv.innerHTML = `
            <input type="text" 
                   class="form-control defects-option-input" 
                   name="field_config[defects_options][${index}]" 
                   value="" 
                   placeholder="Enter defect option">
            <div class="input-group-append">
                <button type="button" class="btn btn-danger remove-defects-option" onclick="removeDefectsOption(${index})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        list.appendChild(optionDiv);
        optionDiv.querySelector('input').focus();
    };
    
    window.removeDefectsOption = function(index) {
        const item = document.querySelector(`.defects-option-item[data-index="${index}"]`);
        if (item) {
            item.remove();
            // Reindex remaining items
            const items = document.querySelectorAll('.defects-option-item');
            items.forEach((item, newIndex) => {
                item.setAttribute('data-index', newIndex);
                const input = item.querySelector('input');
                const name = input.name.replace(/\[\d+\]/, `[${newIndex}]`);
                input.name = name;
                const btn = item.querySelector('button');
                btn.setAttribute('onclick', `removeDefectsOption(${newIndex})`);
            });
        }
    };
    
    // Functions for managing remaining life options
    window.addRemainingLifeOption = function() {
        const list = document.getElementById('remaining-life-options-list');
        const index = list.children.length;
        const optionDiv = document.createElement('div');
        optionDiv.className = 'input-group mb-2 remaining-life-option-item';
        optionDiv.setAttribute('data-index', index);
        optionDiv.innerHTML = `
            <input type="text" 
                   class="form-control remaining-life-option-input" 
                   name="field_config[remaining_life_options][${index}]" 
                   value="" 
                   placeholder="Enter remaining life option">
            <div class="input-group-append">
                <button type="button" class="btn btn-danger remove-remaining-life-option" onclick="removeRemainingLifeOption(${index})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        list.appendChild(optionDiv);
        optionDiv.querySelector('input').focus();
    };
    
    window.removeRemainingLifeOption = function(index) {
        const item = document.querySelector(`.remaining-life-option-item[data-index="${index}"]`);
        if (item) {
            item.remove();
            // Reindex remaining items
            const items = document.querySelectorAll('.remaining-life-option-item');
            items.forEach((item, newIndex) => {
                item.setAttribute('data-index', newIndex);
                const input = item.querySelector('input');
                const name = input.name.replace(/\[\d+\]/, `[${newIndex}]`);
                input.name = name;
                const btn = item.querySelector('button');
                btn.setAttribute('onclick', `removeRemainingLifeOption(${newIndex})`);
            });
        }
    };
    
    // Update visibility when fields are added/removed
    updateDefaultFieldsVisibility();
    
    // Function to add dropdown option - make it global
    window.addDropdownOption = function(value = '') {
        const optionsList = document.getElementById('dropdown-options-list');
        const optionId = 'option_' + Date.now();
        const optionDiv = document.createElement('div');
        optionDiv.className = 'input-group mb-2';
        optionDiv.id = optionId;
        optionDiv.innerHTML = `
            <input type="text" class="form-control option-input" value="${value}" placeholder="Enter option text" data-option-id="${optionId}">
            <div class="input-group-append">
                <button type="button" class="btn btn-danger" onclick="removeDropdownOption('${optionId}')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        optionsList.appendChild(optionDiv);
        updateOptionsHiddenField();
        
        // Focus on the new input
        optionDiv.querySelector('.option-input').focus();
    };
    
    // Function to remove dropdown option
    window.removeDropdownOption = function(optionId) {
        const optionElement = document.getElementById(optionId);
        if (optionElement) {
            optionElement.remove();
            updateOptionsHiddenField();
        }
    };
    
    // Function to clear all dropdown options - make it global
    window.clearDropdownOptions = function() {
        const optionsList = document.getElementById('dropdown-options-list');
        if (optionsList) {
            optionsList.innerHTML = '';
            updateOptionsHiddenField();
        }
    };
    
    // Function to update hidden field with options
    function updateOptionsHiddenField() {
        const optionsInputs = document.querySelectorAll('.option-input');
        const options = Array.from(optionsInputs)
            .map(input => input.value.trim())
            .filter(value => value !== '');
        document.getElementById('options').value = JSON.stringify(options);
    }
    
    // Add event listeners to option inputs for real-time updates
    document.addEventListener('input', function(e) {
        if (e.target && e.target.classList.contains('option-input')) {
            updateOptionsHiddenField();
        }
    });
    
    // Toggle options field based on field type
    fieldTypeSelect.addEventListener('change', function() {
        if (this.value === 'dropdown') {
            optionsGroup.style.display = 'block';
            // Add one empty option if list is empty
            const optionsList = document.getElementById('dropdown-options-list');
            if (optionsList.children.length === 0) {
                addDropdownOption();
            }
        } else {
            optionsGroup.style.display = 'none';
        }
    });
    
    // Handle field form submission - simple POST method
    fieldForm.addEventListener('submit', function(e) {
        const fieldId = document.getElementById('field_id').value;
        const saveBtn = document.getElementById('saveFieldBtn');
        const saveBtnText = document.getElementById('saveBtnText');
        const saveBtnLoader = document.getElementById('saveBtnLoader');
        
        // Show loading state
        saveBtn.disabled = true;
        saveBtnText.textContent = fieldId ? 'Updating...' : 'Saving...';
        saveBtnLoader.classList.remove('d-none');
        
        // If editing, update form action and method
        if (fieldId) {
            this.action = '{{ route("admin.survey-sections.fields.update", [$surveySection, ":fieldId"]) }}'.replace(':fieldId', fieldId);
            // Check if _method input already exists
            let methodInput = this.querySelector('input[name="_method"]');
            if (!methodInput) {
                methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                this.appendChild(methodInput);
            }
            methodInput.value = 'PUT';
        }
        
        // Form will submit naturally - no preventDefault needed
    });
    
    // Note: editField() and deleteField() are already defined globally above (before DOMContentLoaded)
    // They are available for onclick handlers - DO NOT redefine them here!
    
    // (Removed duplicate function definitions that were overriding the global ones)
    
    // Reset form when modal is closed
    $(fieldModal).on('hidden.bs.modal', function() {
        fieldForm.reset();
        document.getElementById('field_id').value = '';
        document.getElementById('is_required').checked = false;
        clearDropdownOptions();
        optionsGroup.style.display = 'none';
        
        // Reset modal title
        document.querySelector('#fieldModal .modal-title').textContent = 'Add Field';
        
        // Reset form action
        fieldForm.action = '{{ route("admin.survey-sections.fields.store", $surveySection) }}';
        
        // Remove any _method input if exists
        const methodInput = fieldForm.querySelector('input[name="_method"]');
        if (methodInput) {
            methodInput.remove();
        }
    });
    
});

// Levels multi-select tag buttons (Edit form)
(function() {
    let selectedLevels = @json(old('levels', $surveySection->levels->pluck('id')->toArray()));
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

    // Initialize on load
    updateHiddenInputs();
})();

</script>
@endpush
