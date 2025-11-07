{{-- Input Tab Content - Property Inspection Form --}}
<div class="survey-input-tab">
    <form id="survey-input-form" method="POST" action="" enctype="multipart/form-data">
        @csrf
    {{-- Assessment Sections Container --}}
    <div class="survey-assessments-container">
        {{-- Default Roofs Assessment Section --}}
        <div class="survey-assessment-section" data-assessment-id="" data-assessment-name="Roofs">
            {{-- Single Header with Section Name and Actions --}}
            <div class="survey-section-header">
                <div class="survey-section-header-content">
                    <div class="survey-section-header-left">
                        <h2 class="survey-section-title" id="main-section-title">Roofs</h2>
                        <span class="survey-section-subtitle" id="component-area-title">Select Area / Location</span>
                        <span class="component-new-badge" style="display: none;">New</span>
                    </div>
                    <div class="survey-section-rating">
                        <div class="component-rating-control">
                            <button type="button" class="rating-display rating-ni" title="Select condition rating">
                                <span class="rating-display-value">NI</span>
                            </button>
                            <div class="rating-options">
                                <button type="button" class="rating-option rating-1" data-rating="1" title="Rating 1 - Good condition">
                        <span>1</span>
                        </button>
                                <button type="button" class="rating-option rating-2" data-rating="2" title="Rating 2 - Fair condition">
                                    <span>2</span>
                        </button>
                                <button type="button" class="rating-option rating-3" data-rating="3" title="Rating 3 - Poor condition">
                                    <span>3</span>
                        </button>
                                <button type="button" class="rating-option rating-ni" data-rating="ni" title="Not Inspected - Requires additional notes">
                                    <span>NI</span>
                        </button>
                            </div>
                        </div>
                    </div>
                    <div class="survey-section-actions">
                        <button type="button" class="survey-action-btn survey-action-icon survey-add-component-btn" title="Add Component">
                            <i class="fas fa-plus"></i>
                        </button>
                        <button type="button" class="survey-action-btn survey-action-icon survey-clone-component-btn" title="Clone Component">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Form Sections --}}
            <div class="survey-input-content" id="component-form-container">
            {{-- Components Container --}}
            <div class="components-container" id="components-container">
        {{-- Component Form Template (Hidden) --}}
            <div class="component-form-template" style="display: none;" data-rating="ni" data-area-primary="">
            <input type="hidden" name="condition_rating[]" class="condition_rating_input" value="ni">
            <div class="survey-input-section">
                <label class="survey-input-label">Primary Area / Location</label>
                <div class="survey-button-group area-location-group" data-area-group="primary">
                    <button type="button" class="survey-input-button area-location-btn" data-group="primary" data-value="Main Roof">Main Roof</button>
                    <button type="button" class="survey-input-button area-location-btn" data-group="primary" data-value="Rear Extension Roof">Rear Extension Roof</button>
                    <button type="button" class="survey-input-button area-location-btn" data-group="primary" data-value="Side Roof">Side Roof</button>
                    <button type="button" class="survey-input-button area-location-btn" data-group="primary" data-value="Porch Conservatory">Porch Conservatory</button>
                    <button type="button" class="survey-input-button area-location-btn" data-group="primary" data-value="Other Roofs Front">Other Roofs Front</button>
                    <button type="button" class="survey-input-button area-location-btn" data-group="primary" data-value="Front Porch">Front Porch</button>
                    <button type="button" class="survey-input-button area-location-btn" data-group="primary" data-value="Side Extension">Side Extension</button>
                    <button type="button" class="survey-input-button area-location-btn" data-group="primary" data-value="Rear Extension">Rear Extension</button>
                    <button type="button" class="survey-input-button area-location-btn" data-group="primary" data-value="Dormer">Dormer</button>
                </div>
                <input type="hidden" name="area_primary[]" class="area_primary_input" data-area-group="primary" data-require-unique="true" value="">
            </div>

            <div class="survey-input-section">
                <label class="survey-input-label">Structure</label>
                <div class="survey-button-group">
                    <button type="button" class="survey-input-button structure-btn" data-value="Pitched">Pitched</button>
                    <button type="button" class="survey-input-button structure-btn" data-value="Inverted Pitched">Inverted Pitched</button>
                    <button type="button" class="survey-input-button structure-btn" data-value="Flat">Flat</button>
                    <button type="button" class="survey-input-button structure-btn" data-value="Curved">Curved</button>
                    <button type="button" class="survey-input-button structure-btn" data-value="Mono-pitched">Mono-pitched</button>
                </div>
                <input type="hidden" name="structure[]" class="structure_input" value="">
            </div>

            <div class="survey-input-section">
                <label class="survey-input-label">Defects</label>
                <div class="survey-button-group">
                    <button type="button" class="survey-input-button defects-btn" data-value="Rot">Rot</button>
                    <button type="button" class="survey-input-button defects-btn" data-value="Deflection">Deflection</button>
                    <button type="button" class="survey-input-button defects-btn" data-value="Moss">Moss</button>
                    <button type="button" class="survey-input-button defects-btn" data-value="Lichen">Lichen</button>
                    <button type="button" class="survey-input-button defects-btn" data-value="Slipped Tiles">Slipped Tiles</button>
                    <button type="button" class="survey-input-button defects-btn" data-value="Holes">Holes</button>
                    <button type="button" class="survey-input-button defects-btn" data-value="Perished">Perished</button>
                    <button type="button" class="survey-input-button defects-btn" data-value="Frost Damage">Frost Damage</button>
                </div>
                <input type="hidden" name="defects[]" class="defects_input" value="">
            </div>

            <div class="survey-input-section">
                <label class="survey-input-label">Material</label>
                <div class="survey-button-group">
                    <button type="button" class="survey-input-button material-btn" data-value="Slate">Slate</button>
                    <button type="button" class="survey-input-button material-btn" data-value="Fibre Slate">Fibre Slate</button>
                    <button type="button" class="survey-input-button material-btn" data-value="Concrete Interlocking">Concrete Interlocking</button>
                    <button type="button" class="survey-input-button material-btn" data-value="Stone">Stone</button>
                </div>
                <input type="hidden" name="material[]" class="material_input" value="">
            </div>

            <div class="survey-input-section">
                <label class="survey-input-label">Remaining Life</label>
                <div class="survey-button-group">
                    <button type="button" class="survey-input-button remaining-life-btn" data-value="0 yrs">0 yrs</button>
                    <button type="button" class="survey-input-button remaining-life-btn" data-value="1-5 yrs">1-5 yrs</button>
                    <button type="button" class="survey-input-button remaining-life-btn" data-value="6-10 yrs">6-10 yrs</button>
                    <button type="button" class="survey-input-button remaining-life-btn" data-value="10+ yrs">10+ yrs</button>
                </div>
                <input type="hidden" name="remaining_life[]" class="remaining_life_input" value="">
            </div>

            <div class="survey-input-section">
                <label class="survey-input-label">Additional Notes</label>
                <div class="survey-notes-wrapper">
                    <textarea class="survey-notes-input" name="additional_notes[]" class="additional_notes_input" 
                              placeholder="Enter additional notes..."></textarea>
                    <button type="button" class="survey-voice-btn" id="voice-input-btn">
                        <i class="fas fa-microphone"></i>
                    </button>
                </div>
            </div>

            <div class="survey-input-section">
                <label class="survey-input-label">Images</label>
                <div class="survey-images-section">
                    <div class="survey-cost-display">
                        <label class="survey-cost-label">Estimated Repair Cost</label>
                        <div class="survey-cost-controls">
                            <button type="button" class="survey-cost-btn survey-cost-decrement" title="Decrease by £50">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" class="survey-cost-input estimated_cost_input" name="estimated_cost[]" 
                                   value="100" min="0" step="50" placeholder="0">
                            <span class="survey-cost-display-value">£ 1.00</span>
                            <button type="button" class="survey-cost-btn survey-cost-increment" title="Increase by £50">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="survey-image-upload-zone image-upload-zone-template" id="image-upload-zone-template">
                        <div class="image-upload-dropzone">
                            <div class="image-upload-icon">
                                <i class="fas fa-cloud-upload-alt"></i>
                            </div>
                            <div class="image-upload-text">
                                <strong>Drag & drop images</strong>
                                <span>or click to browse</span>
                            </div>
                            <button type="button" class="survey-add-image-btn add-image-btn-template image-upload-trigger">
                                <i class="fas fa-folder-open"></i>
                                Browse Files
                        </button>
                        </div>
                        <input type="file" class="image-file-input-template" multiple accept="image/*" style="display: none;">
                        <div class="survey-images-preview images-preview-template"></div>
                    </div>
                </div>
                </div>

                {{-- Component Summary Card Template (Hidden by default) --}}
                <div class="component-summary-card" style="display: none;">
                    <div class="summary-card-content">
                        <div class="summary-card-info">
                            <div class="summary-card-item">
                                <span class="summary-label">Area:</span>
                                <span class="summary-value summary-area"></span>
                            </div>
                            <div class="summary-card-item">
                                <span class="summary-label">Material:</span>
                                <span class="summary-value summary-material"></span>
                            </div>
                            <div class="summary-card-item">
                                <span class="summary-label">Remaining Life:</span>
                                <span class="summary-value summary-life"></span>
                            </div>
                            <div class="summary-card-item">
                                <span class="summary-label">Cost:</span>
                                <span class="summary-value summary-cost"></span>
                            </div>
                        </div>
                        <div class="summary-card-actions">
                            <button type="button" class="survey-edit-btn edit-component-btn-template" data-component-id="">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                        </div>
                    </div>
                </div>
                
                {{-- Component Actions Template --}}
                <div class="component-actions" style="display: none;">
                    <button type="button" class="survey-delete-btn delete-component-btn-template" data-component-id="">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                    <button type="button" class="survey-save-btn save-component-btn-template" data-component-id="">
                        <i class="fas fa-save"></i> Save
                    </button>
                </div>
                
                {{-- Next Button Template --}}
                <div class="component-actions-next" style="display: none;">
                    <button type="button" class="survey-next-btn next-component-btn-template" data-component-id="">
                        <span>Next</span>
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
            {{-- Component 1 (Default) --}}
            <div class="component-form active" data-component-id="1" data-area-primary="" data-rating="ni">
                <input type="hidden" name="condition_rating[]" class="condition_rating_input" value="ni">
        
                {{-- Area / Location Section --}}
                <div class="survey-input-section">
                    <label class="survey-input-label">Primary Area / Location</label>
                    <div class="survey-button-group area-location-group" data-area-group="primary">
                        <button type="button" class="survey-input-button area-location-btn" data-group="primary" data-value="Main Roof">Main Roof</button>
                        <button type="button" class="survey-input-button area-location-btn" data-group="primary" data-value="Rear Extension Roof">Rear Extension Roof</button>
                        <button type="button" class="survey-input-button area-location-btn" data-group="primary" data-value="Side Roof">Side Roof</button>
                        <button type="button" class="survey-input-button area-location-btn" data-group="primary" data-value="Porch Conservatory">Porch Conservatory</button>
                        <button type="button" class="survey-input-button area-location-btn" data-group="primary" data-value="Other Roofs Front">Other Roofs Front</button>
                        <button type="button" class="survey-input-button area-location-btn" data-group="primary" data-value="Front Porch">Front Porch</button>
                        <button type="button" class="survey-input-button area-location-btn" data-group="primary" data-value="Side Extension">Side Extension</button>
                        <button type="button" class="survey-input-button area-location-btn" data-group="primary" data-value="Rear Extension">Rear Extension</button>
                        <button type="button" class="survey-input-button area-location-btn" data-group="primary" data-value="Dormer">Dormer</button>
                    </div>
                    <input type="hidden" name="area_primary[]" class="area_primary_input" data-area-group="primary" data-require-unique="true" value="">
                </div>

                {{-- Structure Section --}}
                <div class="survey-input-section">
                    <label class="survey-input-label">Structure</label>
                    <div class="survey-button-group">
                        <button type="button" class="survey-input-button structure-btn" data-value="Pitched">Pitched</button>
                        <button type="button" class="survey-input-button structure-btn" data-value="Inverted Pitched">Inverted Pitched</button>
                        <button type="button" class="survey-input-button structure-btn" data-value="Flat">Flat</button>
                        <button type="button" class="survey-input-button structure-btn" data-value="Curved">Curved</button>
                        <button type="button" class="survey-input-button structure-btn" data-value="Mono-pitched">Mono-pitched</button>
                    </div>
                    <input type="hidden" name="structure[]" class="structure_input" value="">
                </div>

                {{-- Defects Section --}}
                <div class="survey-input-section">
                    <label class="survey-input-label">Defects</label>
                    <div class="survey-button-group">
                        <button type="button" class="survey-input-button defects-btn" data-value="Rot">Rot</button>
                        <button type="button" class="survey-input-button defects-btn" data-value="Deflection">Deflection</button>
                        <button type="button" class="survey-input-button defects-btn" data-value="Moss">Moss</button>
                        <button type="button" class="survey-input-button defects-btn" data-value="Lichen">Lichen</button>
                        <button type="button" class="survey-input-button defects-btn" data-value="Slipped Tiles">Slipped Tiles</button>
                        <button type="button" class="survey-input-button defects-btn" data-value="Holes">Holes</button>
                        <button type="button" class="survey-input-button defects-btn" data-value="Perished">Perished</button>
                        <button type="button" class="survey-input-button defects-btn" data-value="Frost Damage">Frost Damage</button>
                    </div>
                    <input type="hidden" name="defects[]" class="defects_input" value="">
                </div>

                {{-- Material Section --}}
                <div class="survey-input-section">
                    <label class="survey-input-label">Material</label>
                    <div class="survey-button-group">
                        <button type="button" class="survey-input-button material-btn" data-value="Slate">Slate</button>
                        <button type="button" class="survey-input-button material-btn" data-value="Fibre Slate">Fibre Slate</button>
                        <button type="button" class="survey-input-button material-btn" data-value="Concrete Interlocking">Concrete Interlocking</button>
                        <button type="button" class="survey-input-button material-btn" data-value="Stone">Stone</button>
                    </div>
                    <input type="hidden" name="material[]" class="material_input" value="">
                </div>

                {{-- Remaining Life Section --}}
                <div class="survey-input-section">
                    <label class="survey-input-label">Remaining Life</label>
                    <div class="survey-button-group">
                        <button type="button" class="survey-input-button remaining-life-btn" data-value="0 yrs">0 yrs</button>
                        <button type="button" class="survey-input-button remaining-life-btn" data-value="1-5 yrs">1-5 yrs</button>
                        <button type="button" class="survey-input-button remaining-life-btn" data-value="6-10 yrs">6-10 yrs</button>
                        <button type="button" class="survey-input-button remaining-life-btn" data-value="10+ yrs">10+ yrs</button>
                    </div>
                    <input type="hidden" name="remaining_life[]" class="remaining_life_input" value="">
                </div>

                {{-- Additional Notes Section --}}
                <div class="survey-input-section">
                    <label class="survey-input-label">Additional Notes</label>
                    <div class="survey-notes-wrapper">
                        <textarea class="survey-notes-input additional_notes_input" name="additional_notes[]" 
                                  placeholder="Enter additional notes...">Evidence of localised deterioration, further inspection advised.</textarea>
                        <button type="button" class="survey-voice-btn voice-input-btn" title="Voice Input">
                            <i class="fas fa-microphone"></i>
                        </button>
                    </div>
                </div>

                {{-- Images Section --}}
                <div class="survey-input-section">
                    <label class="survey-input-label">Images</label>
                    <div class="survey-images-section">
                        <div class="survey-cost-display">
                            <label class="survey-cost-label">Estimated Repair Cost</label>
                            <div class="survey-cost-controls">
                                <button type="button" class="survey-cost-btn survey-cost-decrement" title="Decrease by £50">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" class="survey-cost-input estimated_cost_input" name="estimated_cost[]" 
                                       value="100" min="0" step="50" placeholder="0">
                                <span class="survey-cost-display-value">£ 1.00</span>
                                <button type="button" class="survey-cost-btn survey-cost-increment" title="Increase by £50">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="survey-image-upload-zone image-upload-zone-component">
                            <div class="image-upload-dropzone">
                                <div class="image-upload-icon">
                                    <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                                <div class="image-upload-text">
                                    <strong>Drag & drop images</strong>
                                    <span>or click to browse</span>
                    </div>
                                <button type="button" class="survey-add-image-btn add-image-btn-component image-upload-trigger">
                                    <i class="fas fa-folder-open"></i>
                                    Browse Files
                        </button>
                    </div>
                            <input type="file" class="image-file-input-component" accept="image/*" multiple style="display: none;">
                            <div class="survey-images-preview images-preview-component"></div>
                        </div>
                    </div>
                </div>
                
                {{-- Component Actions --}}
                <div class="component-actions">
                    <button type="button" class="survey-delete-btn" data-component-id="1">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                    <button type="button" class="survey-save-btn" data-component-id="1">
                        <i class="fas fa-save"></i> Save
                    </button>
                </div>
            </div>
            </div>
        </div>
        
        <!-- <div class="survey-section-footer">
            <button type="button" class="survey-action-btn survey-action-danger survey-delete-assessment-btn" title="Delete Assessment Section">
                <i class="fas fa-trash"></i>
                <span class="survey-action-label">Delete Section</span>
            </button>
        </div> -->
    </form>
    </div>

</div>

@push('styles')
<style>
/* Input Tab Base */
.survey-input-tab {
    background: #FFFFFF;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    overflow: visible;
    position: relative;
}

#survey-input-form {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 0;
}

/* Section Header - Combined */
.survey-section-header {
    background: linear-gradient(135deg, #111827, #1E293B);
    padding: 1.25rem 1.75rem;
    border-radius: 18px 18px 14px 14px;
    box-shadow: 0 22px 44px -26px rgba(15, 23, 42, 0.85);
    position: relative;
}

.survey-section-header::after {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: inherit;
    border: 1px solid rgba(148, 163, 184, 0.25);
    pointer-events: none;
    z-index: -1;
}

.survey-section-header-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1.5rem;
    flex-wrap: wrap;
}

.survey-section-header-left {
    display: flex;
    flex-direction: row;
    align-items: flex-start;
    gap: 0.35rem;
    min-width: 0;
    flex: 1 1 auto;
}

.survey-section-title {
    font-size: 1.35rem;
    font-weight: 700;
    color: #F8FAFC !important;
    margin: 0;
    padding: 0;
    border: none !important;
    line-height: 1.15;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
}

.survey-section-subtitle {
    font-size: 0.95rem;
    font-weight: 600;
    color: rgba(226, 232, 240, 0.85);
    display: flex;
    align-items: center;
    gap: 0.35rem;
    white-space: normal;
    line-height: 1.35;
}

.survey-section-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
    /* margin-left: 1rem; */
}

.survey-section-rating {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-left: auto;
}

.survey-action-btn {
    min-width: 32px;
    height: 32px;
    border-radius: 6px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    background: rgba(255, 255, 255, 0.1);
    color: #FFFFFF;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.375rem;
    padding: 0 0.625rem;
    transition: all 0.2s ease;
    font-size: 0.75rem;
    flex-shrink: 0;
    white-space: nowrap;
}

.survey-action-btn:hover {
    background: #C1EC4A;
    border-color: #C1EC4A;
    color: #1A202C;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(193, 236, 74, 0.3);
}

.survey-action-btn.survey-action-icon {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    padding: 0;
    font-size: 1rem;
    background: rgba(255, 255, 255, 0.12);
    border-color: rgba(255, 255, 255, 0.18);
    box-shadow: 0 12px 22px -14px rgba(12, 22, 52, 0.6);
}

.survey-action-btn.survey-action-icon i {
    font-size: 1rem;
}

.survey-action-btn.survey-action-icon:hover {
    background: #C1EC4A;
    border-color: #C1EC4A;
    color: #111827;
    box-shadow: 0 14px 24px -12px rgba(193, 236, 74, 0.55);
}

.survey-action-btn:active {
    transform: translateY(0) scale(0.95);
}

.survey-action-btn i {
    font-size: 0.85rem;
}

.survey-action-label {
    font-size: 0.6875rem;
    font-weight: 600;
    display: none;
}

@media (min-width: 768px) {
    .survey-action-label {
        display: inline;
    }
    
    .survey-action-btn {
        padding: 0 0.875rem;
    }
}

.survey-action-btn.survey-action-primary {
    background: rgba(193, 236, 74, 0.2);
    border-color: rgba(193, 236, 74, 0.4);
}

.survey-action-btn.survey-action-primary:hover {
    background: #C1EC4A;
    border-color: #C1EC4A;
    color: #1A202C;
}

.survey-action-btn.survey-action-danger {
    background: rgba(239, 68, 68, 0.15);
    border-color: rgba(239, 68, 68, 0.3);
}

.survey-action-btn.survey-action-danger:hover {
    background: #EF4444;
    border-color: #EF4444;
    color: #FFFFFF;
}

/* Dropdown Menu */
.survey-action-dropdown {
    position: relative;
    display: inline-block;
}

.survey-dropdown-arrow {
    font-size: 0.625rem;
    margin-left: 0.25rem;
}

.survey-action-dropdown.active .survey-dropdown-arrow {
    transform: rotate(180deg);
}

.survey-dropdown-menu {
    position: absolute;
    top: calc(100% + 0.5rem);
    right: 0;
    background: #FFFFFF;
    border: 1px solid #E5E7EB;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    min-width: 220px;
    z-index: 1000;
    display: none;
    overflow: hidden;
}

.survey-action-dropdown.active .survey-dropdown-menu {
    display: block;
}

.survey-dropdown-item {
    width: 100%;
    padding: 0.75rem 1rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: #FFFFFF;
    border: none;
    color: #1A202C;
    font-size: 0.9375rem;
    font-weight: 500;
    cursor: pointer;
    text-align: left;
}

.survey-dropdown-item:hover {
    background: #F9FAFB;
    color: #1A202C;
}

.survey-dropdown-item:active {
    background: #F3F4F6;
}

.survey-dropdown-item i {
    font-size: 1rem;
    color: #6B7280;
    width: 20px;
    text-align: center;
}

.survey-dropdown-item:hover i {
    color: #C1EC4A;
}

.survey-dropdown-divider {
    height: 1px;
    background: #E5E7EB;
    margin: 0.5rem 0;
}

/* New Component Badge */
.component-new-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.3rem 0.75rem;
    background: linear-gradient(135deg, #22C55E, #14B8A6);
    color: #FFFFFF;
    font-size: 0.7rem;
    font-weight: 700;
    border-radius: 999px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    box-shadow: 0 14px 32px -18px rgba(20, 184, 166, 0.6);
    align-self: flex-start;
    margin-top: 0.1rem;
}

.survey-section-footer {
    display: flex;
    justify-content: flex-end;
    padding: 1.25rem 1.5rem 1.75rem;
    gap: 0.75rem;
}

.survey-section-footer .survey-action-btn {
    min-width: auto;
    height: 40px;
    padding: 0 1rem;
}

.survey-section-footer .survey-action-label {
    display: inline;
    margin-left: 0.4rem;
}

/* Rating Selector */
.component-rating-control {
    display: flex;
    align-items: center;
    gap: 0.9rem;
    position: relative;
}

.rating-display {
    width: 46px;
    height: 46px;
    border-radius: 50%;
    border: 2px solid rgba(148, 163, 184, 0.35);
    background: rgba(15, 23, 42, 0.25);
    color: #E2E8F0;
    font-weight: 700;
    font-size: 1.05rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    padding: 0;
    font-variant-numeric: tabular-nums;
}

.rating-display .rating-display-value {
    min-width: 1.5rem;
    text-align: center;
}

.rating-display:hover {
    border-color: rgba(255, 255, 255, 0.55);
    box-shadow: 0 16px 34px -18px rgba(15, 23, 42, 0.8);
}

.component-rating-control.open .rating-display {
    border-color: rgba(255, 255, 255, 0.6);
    box-shadow: 0 18px 38px -18px rgba(15, 23, 42, 0.85);
}

.rating-display.rating-1 {
    background: #22C55E;
    border-color: #22C55E;
    color: #FFFFFF;
}

.rating-display.rating-2 {
    background: #FACC15;
    border-color: #FACC15;
    color: #FFFFFF;
}

.rating-display.rating-3 {
    background: #F43F5E;
    border-color: #F43F5E;
    color: #FFFFFF;
}

.rating-display.rating-ni {
    background: #475569;
    border-color: #475569;
    color: #FFFFFF;
}

.rating-options {
    display: none;
    gap: 0.6rem;
    align-items: center;
    position: absolute;
    right: 0;
    top: calc(100% + 0.85rem);
    padding: 0.6rem 0.95rem;
    background: #0F172A;
    border: 1px solid rgba(148, 163, 184, 0.3);
    border-radius: 999px;
    box-shadow: 0 28px 55px -24px rgba(15, 23, 42, 0.85);
    z-index: 25;
}

.component-rating-control.open .rating-options {
    display: inline-flex;
}

.rating-option {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    border: 2px solid transparent;
    background: rgba(255, 255, 255, 0.08);
    color: #94A3B8;
    font-weight: 700;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
}

.rating-option:hover {
    transform: translateY(-3px);
}

.rating-option.rating-1 {
    color: #22C55E;
}

.rating-option.rating-2 {
    color: #FACC15;
}

.rating-option.rating-3 {
    color: #F43F5E;
}

.rating-option.rating-ni {
    color: #94A3B8;
}

.rating-option.active {
    background: currentColor;
    color: #FFFFFF;
    border-color: currentColor;
    transform: translateY(0);
    box-shadow: 0 14px 26px -18px currentColor;
}


/* Status Indicators */
.survey-status-indicators {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.status-indicator {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.875rem;
    color: #FFFFFF;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
}

.status-indicator:hover {
    transform: scale(1.1);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

/* Status Colors */
.status-indicator.status-active {
    background: #10B981;
}

.status-indicator.status-in-progress {
    background: #F59E0B;
}

.status-indicator.status-urgent {
    background: #EF4444;
}

.status-indicator.status-not-inspected {
    background: #6B7280;
}

/* Status Popup Modal */
.status-popup-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.status-popup-content {
    background: #FFFFFF;
    border-radius: 12px;
    padding: 1.5rem;
    min-width: 280px;
    max-width: 90%;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
}

.status-popup-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #E5E7EB;
}

.status-popup-header h4 {
    margin: 0;
    font-size: 1.125rem;
    font-weight: 600;
    color: #1A202C;
}

.status-popup-close {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    border: none;
    background: #F3F4F6;
    color: #6B7280;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.status-popup-close:hover {
    background: #E5E7EB;
    color: #1A202C;
}

.status-popup-options {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.status-option-btn {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.875rem 1rem;
    border: 1px solid #E5E7EB;
    border-radius: 8px;
    background: #FFFFFF;
    color: #1A202C;
    cursor: pointer;
    transition: all 0.2s ease;
    text-align: left;
    width: 100%;
}

.status-option-btn:hover {
    background: #F9FAFB;
    border-color: #C1EC4A;
}

.status-option-color {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    flex-shrink: 0;
}

.status-option-label {
    font-size: 0.9375rem;
    font-weight: 500;
}


/* Component Management */
.components-container {
    position: relative;
    display: flex;
    flex-direction: column;
    gap: 1.75rem;
}

.component-form {
    display: none;
    position: relative;
    background: #FFFFFF;
    border-radius: 0 0 18px 18px;
    box-shadow: 0 24px 45px -32px rgba(15, 23, 42, 0.6);
    padding-bottom: 2.25rem;
}

.component-form.active {
    display: block;
}

.component-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid #E5E7EB;
    justify-content: flex-end;
}

.survey-delete-btn {
    padding: 0.875rem 1.5rem;
    font-size: 1.125rem;
    font-weight: 600;
    border: 1px solid #EF4444;
    border-radius: 8px;
    background: #EF4444;
    color: #FFFFFF;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.survey-delete-btn:hover {
    background: #DC2626;
    border-color: #DC2626;
}

.survey-save-btn {
    padding: 0.875rem 1.5rem;
    font-size: 1.125rem;
    font-weight: 600;
    border: 1px solid #C1EC4A;
    border-radius: 8px;
    background: #C1EC4A;
    color: #1A202C;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.survey-save-btn:hover {
    background: #B0D93F;
    border-color: #B0D93F;
}

.survey-edit-btn {
    padding: 0.875rem 1.5rem;
    font-size: 1.125rem;
    font-weight: 600;
    border: 1px solid #1A202C;
    border-radius: 8px;
    background: #FFFFFF;
    color: #1A202C;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.survey-edit-btn:hover {
    background: #F9FAFB;
    border-color: #C1EC4A;
    color: #C1EC4A;
}

/* Collapsible Section */
.survey-collapsible-section {
    margin-bottom: 2rem;
}

.survey-collapsible-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    padding: 0.75rem 0;
    border-bottom: 1px solid #E5E7EB;
}

.survey-collapsible-header:hover {
    background: rgba(26, 32, 44, 0.02);
}

.survey-collapsible-header .survey-input-label {
    margin-bottom: 0;
}

.survey-collapse-toggle {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    border: 1px solid #E5E7EB;
    background: #FFFFFF;
    color: #1A202C;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.survey-collapse-toggle:hover {
    background: #F9FAFB;
    border-color: #C1EC4A;
    color: #C1EC4A;
}

.survey-collapsible-content {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease, padding 0.3s ease;
    padding: 0;
}

.survey-collapsible-content.show {
    max-height: 1000px;
    padding: 1rem 0;
}

.survey-collapsible-content.collapse {
    max-height: 0;
    padding: 0;
}

/* Component Summary Card */
.component-summary-card {
    background: #F9FAFB;
    border: 1px solid #E5E7EB;
    border-radius: 8px;
    padding: 1.5rem;
    margin-top: 1.5rem;
}

.summary-card-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1.5rem;
}

.summary-card-info {
    flex: 1;
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

.summary-card-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.summary-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: #6B7280;
}

.summary-value {
    font-size: 1rem;
    font-weight: 600;
    color: #1A202C;
}

.summary-card-actions {
    flex-shrink: 0;
}

/* Component Form States */
.component-form.saved:not(.expanded) .survey-input-section:not(.summary-section) {
    display: none;
}

.component-form.saved:not(.expanded) .component-summary-card {
    display: block !important;
}

.component-form.saved:not(.expanded) .component-actions {
    display: none;
}

.component-form.saved:not(.expanded) .component-actions-next {
    display: flex;
}

.component-form.saved.expanded .component-summary-card {
    display: none !important;
}

.component-form.saved.expanded .component-actions {
    display: flex !important;
}

.component-form.saved.expanded .component-actions-next {
    display: none !important;
}

/* Next Button */
.component-actions-next {
    display: none;
    gap: 1rem;
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid #E5E7EB;
    justify-content: flex-end;
}

.survey-next-btn {
    padding: 0.875rem 1.5rem;
    font-size: 1.125rem;
    font-weight: 600;
    border: 1px solid #C1EC4A;
    border-radius: 8px;
    background: #C1EC4A;
    color: #1A202C;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.survey-next-btn:hover {
    background: #B0D93F;
    border-color: #B0D93F;
}

/* Drag and Drop Zone */
.survey-image-upload-zone {
    position: relative;
    min-height: 120px;
    border: 2px dashed #D1D5DB;
    border-radius: 8px;
    padding: 1.5rem;
    margin-top: 1rem;
    transition: all 0.2s ease;
    background: #FFFFFF;
}

/* Assessments Container */
.survey-assessments-container {
    display: flex;
    flex-direction: column;
    gap: 1.75rem;
    width: 100%;
    flex: 1;
}

.survey-input-content {
    padding: 1.5rem 1.75rem;
    overflow: visible;
    box-sizing: border-box;
    position: relative;
    width: 100%;
    background: #FFFFFF;
    border: 1px solid #E5E7EB;
    border-top: none;
    border-radius: 0 0 18px 18px;
    box-shadow: 0 18px 42px -28px rgba(15, 23, 42, 0.32);
}

.survey-input-section {
    margin-bottom: 1.75rem;
}


/* Assessment Section */
.survey-assessment-section {
    margin-bottom: 2rem;
    background: transparent;
}

.survey-input-label {
    display: block;
    font-size: 1rem;
    font-weight: 600;
    color: #1A202C;
    margin-bottom: 0.75rem;
}

.survey-input-optional {
    font-size: 0.85rem;
    font-weight: 500;
    color: #64748B;
}

.survey-button-group {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.area-location-group {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.survey-input-button {
    padding: 0.625rem 1.25rem;
    font-size: 0.9375rem;
    font-weight: 500;
    border: 1px solid #1A202C;
    border-radius: 6px;
    background: #FFFFFF;
    color: #1A202C;
    cursor: pointer;
    transition: all 0.2s ease;
    min-width: 100px;
    text-align: center;
}

.survey-input-button:hover {
    background: #F9FAFB;
}

.survey-input-button.active {
    background: #C1EC4A;
    border-color: #1A202C;
    color: #1A202C;
    font-weight: 600;
}

/* Defects - Multiple Selection */
.defects-btn.active {
    background: #C1EC4A;
    border-color: #1A202C;
    color: #1A202C;
    font-weight: 600;
}

/* Notes Input */
.survey-notes-wrapper {
    position: relative;
}

.survey-notes-input {
    width: 100%;
    min-height: 120px;
    padding: 1.25rem;
    padding-right: 3.5rem;
    font-size: 1.125rem;
    border: 1px solid #1A202C;
    border-radius: 8px;
    background: #FFFFFF;
    color: #1A202C;
    font-family: inherit;
    resize: vertical;
    line-height: 1.6;
}

.survey-notes-input:focus {
    outline: none;
    border-color: #C1EC4A;
    box-shadow: 0 0 0 3px rgba(193, 236, 74, 0.1);
}

.survey-voice-btn {
    position: absolute;
    bottom: 0.75rem;
    right: 0.75rem;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: none;
    background: #F3F4F6;
    color: #6B7280;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.survey-voice-btn:hover {
    background: #E5E7EB;
    color: #1A202C;
}

/* Images Section */
.survey-images-section {
    margin-top: 1rem;
}

.survey-cost-display {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
    color: #1A202C;
    font-weight: 600;
    font-size: 1.125rem;
}

.survey-cost-label {
    color: #1A202C;
    font-size: 1.125rem;
}

/* Cost Controls */

.survey-cost-controls {
    display: inline-flex;
    align-items: center;
    gap: 0.9rem;
    padding: 0.65rem 0.85rem;
    border-radius: 14px;
    background: linear-gradient(135deg, rgba(15, 23, 42, 0.82), rgba(30, 41, 59, 0.9));
    border: 1px solid rgba(148, 163, 184, 0.35);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08), 0 18px 32px -26px rgba(15, 23, 42, 0.65);
}

.survey-cost-btn {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    border: 1px solid rgba(193, 236, 74, 0.4);
    background: linear-gradient(145deg, rgba(193, 236, 74, 0.18), rgba(193, 236, 74, 0.08));
    color: #E7FF9F;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    font-size: 1.05rem;
    flex-shrink: 0;
    box-shadow: 0 10px 20px -18px rgba(193, 236, 74, 0.65);
    backdrop-filter: blur(6px);
}

.survey-cost-btn i {
    pointer-events: none;
}

.survey-cost-btn:hover {
    background: rgba(193, 236, 74, 0.35);
    border-color: rgba(193, 236, 74, 0.8);
    color: #0F172A;
    transform: translateY(-1px);
    box-shadow: 0 14px 26px -18px rgba(193, 236, 74, 0.75);
}

.survey-cost-btn:active {
    transform: scale(0.95);
}

.survey-cost-btn:disabled,
.survey-cost-btn[disabled] {
    opacity: 0.45;
    cursor: not-allowed;
    box-shadow: none;
}

.survey-cost-input {
    width: 110px;
    padding: 0.65rem 0.75rem;
    font-size: 1.125rem;
    font-weight: 600;
    border: 1px solid rgba(148, 163, 184, 0.45);
    border-radius: 12px;
    color: #F8FAFC;
    text-align: right;
    background: rgba(15, 23, 42, 0.65);
    display: inline-block;
    transition: all 0.2s ease;
    margin: 0;
}

.survey-cost-input:hover {
    border-color: rgba(193, 236, 74, 0.8);
    box-shadow: 0 12px 24px -18px rgba(193, 236, 74, 0.55);
}

.survey-cost-display-value {
    font-size: 1.125rem;
    font-weight: 600;
    color: #E2E8F0;
    min-width: 110px;
    text-align: right;
    display: inline-flex;
    justify-content: flex-end;
    font-variant-numeric: tabular-nums;
}

.survey-cost-input:focus {
    outline: none;
    border-color: rgba(193, 236, 74, 0.9);
    box-shadow: 0 0 0 3px rgba(193, 236, 74, 0.2);
}

.survey-image-upload-zone {
    position: relative;
    border-radius: 20px;
    border: 1px dashed rgba(148, 163, 184, 0.4);
    background: linear-gradient(145deg, rgba(15, 23, 42, 0.92), rgba(30, 41, 59, 0.92));
    padding: 1.75rem;
    transition: all 0.25s ease;
    margin-top: 1.25rem;
}

.survey-image-upload-zone.drag-over {
    border-color: #C4E75E;
    box-shadow: 0 28px 45px -28px rgba(156, 216, 76, 0.65);
}

.survey-image-upload-zone.drag-over .image-upload-icon {
    background: rgba(193, 236, 74, 0.18);
    color: #E8FF9C;
}

.survey-image-upload-zone.drag-over .image-upload-text strong {
    color: #E8FCE1;
}

.survey-image-upload-zone.drag-over .image-upload-text span {
    color: rgba(224, 231, 255, 0.85);
}

.survey-image-upload-zone.drag-over .survey-add-image-btn {
    border-color: rgba(193, 236, 74, 0.6);
    background: rgba(193, 236, 74, 0.18);
    color: #E4F99F;
}

.image-upload-dropzone {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 1rem;
}

.image-upload-icon {
    width: 58px;
    height: 58px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.12);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #CFE85F;
    font-size: 1.6rem;
    box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.08);
}

.image-upload-text {
    text-align: center;
    color: rgba(226, 232, 240, 0.8);
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.image-upload-text strong {
    font-size: 1.05rem;
    font-weight: 600;
    color: #F8FAFC;
}

.image-upload-text span {
    font-size: 0.9rem;
    color: rgba(148, 163, 184, 0.9);
}

.survey-add-image-btn {
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
    font-weight: 600;
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.14);
    color: #F8FAFC;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    box-shadow: 0 16px 30px -22px rgba(15, 23, 42, 0.8);
}

.survey-add-image-btn i {
    font-size: 1rem;
}

.survey-add-image-btn:hover {
    border-color: rgba(255, 255, 255, 0.35);
    background: rgba(220, 252, 231, 0.12);
    color: #C1EC4A;
    transform: translateY(-1px);
}

.survey-images-preview {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 1rem;
    margin-top: 1.5rem;
}

.survey-image-preview-item {
    position: relative;
    aspect-ratio: 1;
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid #E5E7EB;
}

.survey-image-preview-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.survey-image-remove {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    border: none;
    background: rgba(239, 68, 68, 0.9);
    color: #FFFFFF;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
}

/* Footer */
.survey-input-footer {
    background: #1A202C;
    padding: 1rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: sticky;
    bottom: 0;
}

.survey-footer-text {
    color: #FFFFFF;
    font-size: 1rem;
    font-weight: 600;
}

.survey-footer-toggle {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: none;
    background: transparent;
    color: #FFFFFF;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.survey-footer-toggle:hover {
    background: rgba(255, 255, 255, 0.1);
}

@media (max-width: 768px) {
    .survey-input-content {
        padding: 1.5rem 1rem;
    }
    
    .survey-button-group {
        flex-direction: column;
    }
    
    .survey-input-button {
        width: 100%;
    }

    .survey-section-header {
        padding: 1.1rem 1.25rem;
    }

    .survey-section-header-content {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }

    .survey-section-header-left {
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .survey-section-rating,
    .survey-section-actions {
        justify-content: flex-start;
        margin-left: 0;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const componentsContainer = document.getElementById('components-container');
    const template = document.querySelector('.component-form-template');
    const scrollTopBtn = document.getElementById('survey-scroll-top');
    
    // Check if elements exist
    if (!componentsContainer) {
        console.error('Components container not found');
        return;
    }
    
    if (!template) {
        console.error('Component template not found');
        return;
    }

    if (scrollTopBtn) {
        scrollTopBtn.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // Ensure default component resides inside the container for consistent indexing
    const defaultComponent = document.querySelector('.component-form[data-component-id="1"]');
    if (defaultComponent && !componentsContainer.contains(defaultComponent)) {
        componentsContainer.appendChild(defaultComponent);
    }
    
    // Initialize first component
    initializeComponent(1);
    
    // Setup rating indicators for first component
    const firstComponent = document.querySelector('.component-form[data-component-id="1"]');
    if (firstComponent) {
        setupRatingIndicators(firstComponent);
        updateComponentHeaderTitle(firstComponent);
        updateRatingDisplay(firstComponent);
    }
    
    // Initialize assessment indices
    updateAssessmentIndices();
    
    // Add component button handler
    document.addEventListener('click', function(e) {
        const addBtn = e.target.closest('.survey-add-component-btn');
        if (addBtn) {
            e.preventDefault();
            e.stopPropagation();
            const assessmentSection = addBtn.closest('.survey-assessment-section');
            if (assessmentSection) {
                addNewComponentToSection(assessmentSection);
            }
        }
    });

    // Clone component button handler
    document.addEventListener('click', function(e) {
        const cloneBtn = e.target.closest('.survey-clone-component-btn');
        if (cloneBtn) {
            e.preventDefault();
            e.stopPropagation();
            const assessmentSection = cloneBtn.closest('.survey-assessment-section');
        if (assessmentSection) {
                cloneActiveComponent(assessmentSection);
            }
        }
    });
    
    // Component area selection handler
    document.addEventListener('click', function(e) {
        if (e.target.closest('.area-location-btn')) {
            const btn = e.target.closest('.area-location-btn');
            const component = btn.closest('.component-form');
            if (component && component.classList.contains('active')) {
                updateComponentHeaderTitle(component);
            }
        }
    });
    
    // Delete Assessment Section
    document.addEventListener('click', function(e) {
        if (e.target.closest('.survey-delete-assessment-btn')) {
            e.preventDefault();
            e.stopPropagation();
            const btn = e.target.closest('.survey-delete-assessment-btn');
            const assessmentSection = btn.closest('.survey-assessment-section');
            if (assessmentSection) {
                deleteAssessmentSection(assessmentSection);
            }
            return false;
        }
    });
    
    
    // Delete component
    document.addEventListener('click', function(e) {
        if (e.target.closest('.survey-delete-btn')) {
            const btn = e.target.closest('.survey-delete-btn');
            const componentId = btn.dataset.componentId;
            deleteComponent(componentId);
        }
    });
    
    // Save component
    document.addEventListener('click', function(e) {
        if (e.target.closest('.survey-save-btn')) {
            const btn = e.target.closest('.survey-save-btn');
            const componentId = btn.dataset.componentId;
            saveComponent(componentId);
        }
    });
    
    // Edit component (expand saved component)
    document.addEventListener('click', function(e) {
        if (e.target.closest('.survey-edit-btn, .edit-component-btn-template')) {
            const btn = e.target.closest('.survey-edit-btn, .edit-component-btn-template');
            const componentId = btn.dataset.componentId;
            expandComponent(componentId);
        }
    });
    
    function addNewComponentToSection(currentSection, isClone = false, sourceComponent = null) {
        const assessmentsContainer = document.querySelector('.survey-assessments-container');
        if (!assessmentsContainer) return;

        const sectionCount = assessmentsContainer.querySelectorAll('.survey-assessment-section').length;
        const baseName = isClone && currentSection
            ? `${currentSection.dataset.assessmentName || 'Component'} Copy`
            : `Component ${sectionCount + 1}`;

        const newSection = createAssessmentSection(baseName);
        assessmentsContainer.appendChild(newSection);

        const source = isClone ? (sourceComponent || currentSection?.querySelector('.component-form')) : null;
        createComponentFromTemplate(newSection, { baseName, isClone, sourceComponent: source });

        updateAssessmentIndices();

        setTimeout(() => {
            newSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 100);
    }
    
    function cloneActiveComponent(assessmentSection) {
        const activeComponent = assessmentSection.querySelector('.component-form.active');
        if (!activeComponent) {
            // Try to find any component if none is active
            const anyComponent = assessmentSection.querySelector('.component-form');
            if (anyComponent) {
                addNewComponentToSection(assessmentSection, true, anyComponent);
            } else {
                alert('No component to clone');
            }
            return;
        }
        
        addNewComponentToSection(assessmentSection, true, activeComponent);
    }
    
    function showNewComponentBadge(assessmentSection) {
        const badge = assessmentSection.querySelector('.component-new-badge');
        if (badge) {
            badge.style.display = 'inline-flex';
            setTimeout(() => {
                badge.style.display = 'none';
            }, 4000);
        }
    }
    
    function updateComponentHeaderTitle(component) {
        const assessmentSection = component.closest('.survey-assessment-section');
        if (!assessmentSection) return;
        const subtitle = assessmentSection.querySelector('.survey-section-subtitle');
        if (!subtitle) return;
        const componentId = component.dataset.componentId || '';
        const primaryArea = component.dataset.areaPrimary || '';
        if (!primaryArea) {
            subtitle.textContent = 'Select Area / Location';
            return;
        }
        subtitle.textContent = primaryArea;
    }
    
    function addNewAssessment(options = {}) {
        const assessmentName = options.assessmentName || prompt('Enter assessment name:', 'Roofs');
        if (!assessmentName) return;
        
        const assessmentsContainer = document.querySelector('.survey-assessments-container');
        if (!assessmentsContainer) return;

        const newSection = createAssessmentSection(assessmentName);
        assessmentsContainer.appendChild(newSection);
        createComponentFromTemplate(newSection, { baseName: assessmentName });

        updateAssessmentIndices();
        newSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function createAssessmentSection(assessmentName) {
        const assessmentSection = document.createElement('div');
        assessmentSection.className = 'survey-assessment-section';
        assessmentSection.dataset.assessmentName = assessmentName;
        
        const sectionHeader = document.createElement('div');
        sectionHeader.className = 'survey-section-header';
        sectionHeader.innerHTML = `
            <div class="survey-section-header-content">
                <div class="survey-section-header-left">
                    <h2 class="survey-section-title">${assessmentName}</h2>
                    <span class="survey-section-subtitle">Select Area / Location</span>
                    <span class="component-new-badge" style="display: none;">New</span>
                </div>
                <div class="survey-section-rating">
                    <div class="component-rating-control">
                        <button type="button" class="rating-display rating-ni" title="Select condition rating">
                            <span class="rating-display-value">NI</span>
                    </button>
                        <div class="rating-options">
                            <button type="button" class="rating-option rating-1" data-rating="1" title="Rating 1 - Good condition">
                                <span>1</span>
                    </button>
                            <button type="button" class="rating-option rating-2" data-rating="2" title="Rating 2 - Fair condition">
                                <span>2</span>
                    </button>
                            <button type="button" class="rating-option rating-3" data-rating="3" title="Rating 3 - Poor condition">
                                <span>3</span>
                    </button>
                            <button type="button" class="rating-option rating-ni" data-rating="ni" title="Not Inspected - Requires additional notes">
                                <span>NI</span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="survey-section-actions">
                    <button type="button" class="survey-action-btn survey-action-icon survey-add-component-btn" title="Add Component">
                        <i class="fas fa-plus"></i>
                    </button>
                    <button type="button" class="survey-action-btn survey-action-icon survey-clone-component-btn" title="Clone Component">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            </div>
        `;
        
        const componentsContainer = document.createElement('div');
        componentsContainer.className = 'components-container';
        componentsContainer.id = `components-container-${assessmentName.toLowerCase().replace(/\s+/g, '-')}`;
        
        const contentWrapper = document.createElement('div');
        contentWrapper.className = 'survey-input-content';
        contentWrapper.appendChild(componentsContainer);
        
        const sectionFooter = document.createElement('div');
        sectionFooter.className = 'survey-section-footer';
        // sectionFooter.innerHTML = `
        //     <button type="button" class="survey-action-btn survey-action-danger survey-delete-assessment-btn" title="Delete Assessment Section">
        //         <i class="fas fa-trash"></i>
        //         <span class="survey-action-label">Delete Section</span>
        //     </button>
        // `;
        contentWrapper.appendChild(sectionFooter);

        assessmentSection.appendChild(sectionHeader);
        assessmentSection.appendChild(contentWrapper);
        return assessmentSection;
    }

    function createComponentFromTemplate(assessmentSection, { baseName, isClone = false, sourceComponent = null } = {}) {
        if (!template) {
            console.error('Component template not found');
            return null;
        }

        const container = assessmentSection.querySelector('.components-container');
        if (!container) {
            console.error('Components container not found in assessment section');
            return null;
        }

        const component = template.cloneNode(true);
        component.classList.remove('component-form-template');
        component.classList.add('component-form', 'active');
        component.style.display = 'block';

        const componentIndex = container.querySelectorAll('.component-form').length + 1;
        component.dataset.componentId = componentIndex;
        component.dataset.areaPrimary = isClone && sourceComponent ? (sourceComponent.dataset.areaPrimary || '') : '';
        component.dataset.rating = isClone && sourceComponent ? (sourceComponent.dataset.rating || 'ni') : 'ni';
        component.dataset.isSaved = 'false';

        container.querySelectorAll('.component-form').forEach(comp => {
            comp.classList.remove('active');
            comp.style.display = 'none';
        });

        container.appendChild(component);

        updateComponentIds(component, componentIndex);
        initializeComponent(componentIndex);

        if (isClone && sourceComponent) {
            copyComponentData(sourceComponent, component);
        } else {
            setComponentRating(componentIndex, 'ni', component, true);
        }

        setupRatingIndicators(component);
        updateComponentHeaderTitle(component);

        component.querySelectorAll('.component-actions').forEach(actions => {
            actions.style.display = 'flex';
        });

        if (!isClone) {
            showNewComponentBadge(assessmentSection);
        }

        component.classList.add('active');
        component.style.display = 'block';

        updateRatingDisplay(component);

        return component;
    }
    
    function updateAssessmentIndices() {
        // Update all assessment sections with proper indices and names
        document.querySelectorAll('.survey-assessment-section').forEach((section, index) => {
            section.dataset.assessmentIndex = index;
            const assessmentName = section.dataset.assessmentName || `Assessment ${index + 1}`;
            
            // Update all component inputs in this section
            section.querySelectorAll('.component-form').forEach((component, compIndex) => {
                const componentId = component.dataset.componentId || compIndex + 1;
                component.querySelectorAll('input, textarea').forEach(el => {
                    if (el.name) {
                        const fieldMatch = el.name.match(/(area_primary|structure|defects|material|remaining_life|additional_notes|estimated_cost|side|condition_rating)\[\]/);
                        if (fieldMatch) {
                            const fieldName = fieldMatch[1];
                            el.name = `assessments[${index}][components][${componentId}][${fieldName}]`;
                        }
                    }
                });
            });
            
            // Add hidden input for assessment name
            let nameInput = section.querySelector('input[name*="[name]"]');
            if (!nameInput) {
                nameInput = document.createElement('input');
                nameInput.type = 'hidden';
                nameInput.name = `assessments[${index}][name]`;
                section.appendChild(nameInput);
            }
            nameInput.value = assessmentName;
        });
    }
    
    function deleteAssessmentSection(assessmentSection) {
        // Check if this is the last section
        const allSections = document.querySelectorAll('.survey-assessment-section');
        if (allSections.length <= 1) {
            alert('Cannot delete the last assessment section. At least one section is required.');
            return;
        }
        
        // Confirm deletion
        const sectionName = assessmentSection.dataset.assessmentName || 'this section';
        if (!confirm(`Are you sure you want to delete "${sectionName}"? This action cannot be undone.`)) {
            return;
        }
        
        // Remove the section with animation
        assessmentSection.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
        assessmentSection.style.opacity = '0';
        assessmentSection.style.transform = 'translateX(-20px)';
        
        setTimeout(() => {
            assessmentSection.remove();
            
            // Update all assessment indices after deletion
            updateAssessmentIndices();
            
            // If no sections remain, create a default one
            const remainingSections = document.querySelectorAll('.survey-assessment-section');
            if (remainingSections.length === 0) {
                addNewAssessment({ assessmentName: 'Component 1' });
            }
        }, 300);
    }
    
    function updateComponentIds(component, id) {
        // Get the assessment section index
        const assessmentSection = component.closest('.survey-assessment-section');
        const assessmentIndex = assessmentSection ? 
            Array.from(document.querySelectorAll('.survey-assessment-section')).indexOf(assessmentSection) : 0;
        
        // Update all inputs, buttons, and related elements
        component.querySelectorAll('input, textarea').forEach(el => {
            if (el.name) {
                // Update component index in name
                el.name = el.name.replace(/\[\d*\]/, `[${id}]`);

                // Structure for backend: assessments[assessmentIndex][components][componentId][field]
                        const fieldMatch = el.name.match(/(area_primary|structure|defects|material|remaining_life|additional_notes|estimated_cost|side|condition_rating)\[\]/);
                if (fieldMatch && assessmentSection) {
                    const fieldName = fieldMatch[1];
                    el.name = `assessments[${assessmentIndex}][components][${id}][${fieldName}]`;
                }
            }
            if (el.id) {
                el.id = el.id.replace(/\d+/, id);
            }
        });
        
        // Update action buttons
        const deleteBtn = component.querySelector('.survey-delete-btn, .delete-component-btn-template');
        const saveBtn = component.querySelector('.survey-save-btn, .save-component-btn-template');
        const editBtn = component.querySelector('.survey-edit-btn, .edit-component-btn-template');
        const nextBtn = component.querySelector('.survey-next-btn, .next-component-btn-template');
        if (deleteBtn) {
            deleteBtn.dataset.componentId = id;
            deleteBtn.classList.remove('delete-component-btn-template');
        }
        if (saveBtn) {
            saveBtn.dataset.componentId = id;
            saveBtn.classList.remove('save-component-btn-template');
        }
        if (editBtn) {
            editBtn.dataset.componentId = id;
            editBtn.classList.remove('edit-component-btn-template');
        }
        if (nextBtn) {
            nextBtn.dataset.componentId = id;
            nextBtn.classList.remove('next-component-btn-template');
        }

        component.dataset.componentId = id;
    }
    
    function copyComponentData(source, target) {
        // Copy area locations
        const sourcePrimary = source.querySelector('.area_primary_input');
        const targetPrimary = target.querySelector('.area_primary_input');
        if (sourcePrimary && targetPrimary) {
            targetPrimary.value = sourcePrimary.value;
            const activePrimaryBtn = source.querySelector('.area-location-btn[data-group="primary"].active');
            if (activePrimaryBtn) {
                const targetBtn = target.querySelector(`.area-location-btn[data-group="primary"][data-value="${activePrimaryBtn.dataset.value}"]`);
                if (targetBtn) targetBtn.classList.add('active');
            }
        }

        target.dataset.areaPrimary = source.dataset.areaPrimary || '';
        
        // Copy defects
        const sourceDefects = source.querySelector('.defects_input');
        const targetDefects = target.querySelector('.defects_input');
        if (sourceDefects && targetDefects) {
            targetDefects.value = sourceDefects.value;
            source.querySelectorAll('.defects-btn.active').forEach(btn => {
                const targetBtn = target.querySelector(`.defects-btn[data-value="${btn.dataset.value}"]`);
                if (targetBtn) targetBtn.classList.add('active');
            });
        }
        
        // Copy material
        const sourceMaterial = source.querySelector('.material_input');
        const targetMaterial = target.querySelector('.material_input');
        if (sourceMaterial && targetMaterial) {
            targetMaterial.value = sourceMaterial.value;
            const activeBtn = source.querySelector('.material-btn.active');
            if (activeBtn) {
                const targetBtn = target.querySelector(`.material-btn[data-value="${activeBtn.dataset.value}"]`);
                if (targetBtn) targetBtn.classList.add('active');
            }
        }
        
        // Copy remaining life
        const sourceLife = source.querySelector('.remaining_life_input');
        const targetLife = target.querySelector('.remaining_life_input');
        if (sourceLife && targetLife) {
            targetLife.value = sourceLife.value;
            const activeBtn = source.querySelector('.remaining-life-btn.active');
            if (activeBtn) {
                const targetBtn = target.querySelector(`.remaining-life-btn[data-value="${activeBtn.dataset.value}"]`);
                if (targetBtn) targetBtn.classList.add('active');
            }
        }
        
        // Copy notes
        const sourceNotes = source.querySelector('.additional_notes_input');
        const targetNotes = target.querySelector('.additional_notes_input');
        if (sourceNotes && targetNotes) {
            targetNotes.value = sourceNotes.value;
        }
        
        // Copy cost
        const sourceCost = source.querySelector('.estimated_cost_input');
        const targetCost = target.querySelector('.estimated_cost_input');
        const targetCostDisplay = target.querySelector('.survey-cost-display-value');
        if (sourceCost && targetCost) {
            targetCost.value = sourceCost.value;
            if (targetCostDisplay) {
                updateCostDisplay(targetCost, targetCostDisplay);
            }
        }
        
        // Copy structure
        const sourceStructure = source.querySelector('.structure_input');
        const targetStructure = target.querySelector('.structure_input');
        if (sourceStructure && targetStructure) {
            targetStructure.value = sourceStructure.value;
            const activeBtn = source.querySelector('.structure-btn.active');
            if (activeBtn) {
                const targetBtn = target.querySelector(`.structure-btn[data-value="${activeBtn.dataset.value}"]`);
                if (targetBtn) targetBtn.classList.add('active');
            }
        }
        
        // Copy rating
        const sourceRating = (source.dataset.rating || 'ni').toString().toLowerCase();
        const targetId = target.dataset.componentId;
        if (typeof targetId !== 'undefined') {
            setComponentRating(targetId, sourceRating, target);
        }
        
    }
    
    
    function deleteComponent(id) {
        const component = document.querySelector(`.component-form[data-component-id="${id}"]`);
        if (!component) return;
        
        const assessmentSection = component.closest('.survey-assessment-section');
        if (confirm('Are you sure you want to delete this component?')) {
            component.remove();

            if (assessmentSection) {
                assessmentSection.remove();
            }

            updateAssessmentIndices();

            if (document.querySelectorAll('.survey-assessment-section').length === 0) {
                addNewAssessment({ assessmentName: 'Component 1' });
            }
        }
    }
    
    function saveComponent(id) {
        const component = document.querySelector(`.component-form[data-component-id="${id}"]`);
        if (!component) return;
        
        // Validate required fields
        const primaryAreaInput = component.querySelector('.area_primary_input');
        if (!primaryAreaInput || !primaryAreaInput.value) {
            alert('Please select a Primary Area / Location before saving.');
            return;
        }
        
        // Check if NI rating is selected and validate notes
        const rating = component.dataset.rating || '';
        if (rating.toLowerCase() === 'ni') {
            const notesInput = component.querySelector('.additional_notes_input');
            if (!notesInput || !notesInput.value.trim()) {
                alert('Additional notes are required when "NI" (Not Inspected) rating is selected.');
                return;
            }
        }
        
        // Update component data
        const assessmentSection = component.closest('.survey-assessment-section');
        const primaryArea = primaryAreaInput.value;
        component.dataset.areaPrimary = primaryArea;
        component.dataset.isSaved = 'true';
        
        // Remove "New" badge after saving
        const newBadge = assessmentSection?.querySelector('.component-new-badge');
        if (newBadge) {
            newBadge.style.display = 'none';
        }
        
        // Update component header title
        updateComponentHeaderTitle(component);
        
        // Populate summary card
        populateSummaryCard(component);
        
        // Add saved class to collapse component
        component.classList.add('saved');
        
        // Update assessment indices
        updateAssessmentIndices();
        
        // Show success message
        alert('Component saved successfully!');
    }
    
    function populateSummaryCard(component) {
        const summaryCard = component.querySelector('.component-summary-card');
        if (!summaryCard) return;
        
        // Get values from component
        const primaryAreaInput = component.querySelector('.area_primary_input');
        const materialInput = component.querySelector('.material_input');
        const remainingLifeInput = component.querySelector('.remaining_life_input');
        const costInput = component.querySelector('.estimated_cost_input');
        
        // Update summary values
        const areaValue = summaryCard.querySelector('.summary-area');
        const materialValue = summaryCard.querySelector('.summary-material');
        const lifeValue = summaryCard.querySelector('.summary-life');
        const costValue = summaryCard.querySelector('.summary-cost');
        
        if (areaValue) {
            const primary = primaryAreaInput?.value || 'Not selected';
            areaValue.textContent = primary;
        }
        if (materialValue && materialInput) {
            materialValue.textContent = materialInput.value || 'Not selected';
        }
        if (lifeValue && remainingLifeInput) {
            lifeValue.textContent = remainingLifeInput.value || 'Not selected';
        }
        if (costValue && costInput) {
            const cost = parseFloat(costInput.value) || 0;
            costValue.textContent = `£ ${(cost / 100).toFixed(2)}`;
        }
    }
    
    function expandComponent(componentId) {
        const component = document.querySelector(`.component-form[data-component-id="${componentId}"]`);
        if (!component) return;
        
        // Hide all other components in the same section
        const assessmentSection = component.closest('.survey-assessment-section');
        if (assessmentSection) {
            const allComponents = assessmentSection.querySelectorAll('.component-form');
            allComponents.forEach(comp => {
                if (comp !== component) {
                    comp.classList.remove('active', 'expanded');
                    comp.style.display = 'none';
                }
            });
        }
        
        // Expand this component - keep saved class but add expanded to show form
        component.classList.add('expanded', 'active');
        component.style.display = 'block';
        updateComponentHeaderTitle(component);
        updateRatingDisplay(component);
        
        // Scroll to component
        setTimeout(() => {
            component.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 100);
    }
    
    function collapseComponent(componentId) {
        const component = document.querySelector(`.component-form[data-component-id="${componentId}"]`);
        if (!component) return;
        
        // Remove expanded class to collapse back to saved state
        component.classList.remove('expanded');
    }
    
    function setupCostControls(component) {
        const costInput = component.querySelector('.estimated_cost_input');
        const costDisplay = component.querySelector('.survey-cost-display-value');
        const decrementBtn = component.querySelector('.survey-cost-decrement');
        const incrementBtn = component.querySelector('.survey-cost-increment');
        
        if (!costInput || !costDisplay) return;
        
        // Initialize display
        updateCostDisplay(costInput, costDisplay);
        
        // Decrement button
        if (decrementBtn) {
            decrementBtn.addEventListener('click', function() {
                const currentValue = parseFloat(costInput.value) || 0;
                const newValue = Math.max(0, currentValue - 50);
                costInput.value = newValue;
                updateCostDisplay(costInput, costDisplay);
            });
        }
        
        // Increment button
        if (incrementBtn) {
            incrementBtn.addEventListener('click', function() {
                const currentValue = parseFloat(costInput.value) || 0;
                const newValue = currentValue + 50;
                costInput.value = newValue;
                updateCostDisplay(costInput, costDisplay);
            });
        }
        
        // Wheel/scroll control
        if (costInput) {
            costInput.addEventListener('wheel', function(e) {
                e.preventDefault();
                const currentValue = parseFloat(costInput.value) || 0;
                const delta = e.deltaY > 0 ? -50 : 50;
                const newValue = Math.max(0, currentValue + delta);
                costInput.value = newValue;
                updateCostDisplay(costInput, costDisplay);
            });
            
            // Keyboard support
            costInput.addEventListener('keydown', function(e) {
                if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    incrementBtn?.click();
                } else if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    decrementBtn?.click();
                }
            });

            // Manual entry support
            costInput.addEventListener('input', function() {
                updateCostDisplay(costInput, costDisplay);
            });
        }
    }
    
    function updateCostDisplay(costInput, costDisplay) {
        if (!costInput || !costDisplay) return;
        const value = Math.max(0, parseFloat(costInput.value) || 0);
        costInput.value = value;
        const formatted = new Intl.NumberFormat('en-GB', {
            style: 'currency',
            currency: 'GBP',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(value);
        costDisplay.textContent = formatted;
    }
    
    function setupCollapsibleSection(component) {
        const collapsibleHeaders = component.querySelectorAll('.survey-collapsible-header');
        collapsibleHeaders.forEach(header => {
            header.addEventListener('click', function() {
                const content = this.nextElementSibling;
                const toggle = this.querySelector('.survey-collapse-toggle');
                if (content && toggle) {
                    content.classList.toggle('show');
                    content.classList.toggle('collapse');
                    const icon = toggle.querySelector('i');
                    if (icon) {
                        if (content.classList.contains('show')) {
                            icon.classList.remove('fa-chevron-up');
                            icon.classList.add('fa-chevron-down');
                        } else {
                            icon.classList.remove('fa-chevron-down');
                            icon.classList.add('fa-chevron-up');
                        }
                    }
                }
            });
        });
    }
    
    function setupDragAndDrop(component) {
        const uploadZone = component.querySelector('.survey-image-upload-zone');
        const fileInput = component.querySelector('.image-file-input-component, .image-file-input-template');
        const previewContainer = component.querySelector('.images-preview-component, .images-preview-template');
        
        if (!uploadZone || !fileInput || !previewContainer) return;
        
        // Prevent default drag behaviors
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadZone.addEventListener(eventName, preventDefaults, false);
            document.body.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        // Highlight drop zone when item is dragged over it
        ['dragenter', 'dragover'].forEach(eventName => {
            uploadZone.addEventListener(eventName, function() {
                uploadZone.classList.add('drag-over');
            }, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            uploadZone.addEventListener(eventName, function() {
                uploadZone.classList.remove('drag-over');
            }, false);
        });
        
        // Handle dropped files
        uploadZone.addEventListener('drop', function(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            handleImageUpload(files, previewContainer);
        }, false);
    }
    
    function initializeComponent(id) {
        const component = document.querySelector(`.component-form[data-component-id="${id}"]`);
        if (!component) return;
        
        // Initialize cost display if present
        const costInput = component.querySelector('.estimated_cost_input');
        const costDisplay = component.querySelector('.survey-cost-display-value');
        if (costInput && costDisplay) {
            updateCostDisplay(costInput, costDisplay);
        }
        
        // Area/Location - Primary & Secondary Selection
        const primaryAreaInput = component.querySelector('.area_primary_input');
        const primaryButtons = component.querySelectorAll('.area-location-btn[data-group="primary"]');

        setupSingleSelection(primaryButtons, primaryAreaInput);

        if (primaryAreaInput) {
            if (primaryAreaInput.value) {
                const activePrimaryBtn = component.querySelector(`.area-location-btn[data-group="primary"][data-value="${primaryAreaInput.value}"]`);
                if (activePrimaryBtn) {
                    activePrimaryBtn.classList.add('active');
                }
                component.dataset.areaPrimary = primaryAreaInput.value;
            } else {
                component.dataset.areaPrimary = '';
            }
        }

        updateComponentHeaderTitle(component);
        
        // Defects - Multiple Selection
        const defectsBtns = component.querySelectorAll('.defects-btn');
        const defectsInput = component.querySelector('.defects_input');
        setupMultipleSelection(defectsBtns, defectsInput);
        
        // Structure - Single Selection
        const structureBtns = component.querySelectorAll('.structure-btn');
        const structureInput = component.querySelector('.structure_input');
        setupSingleSelection(structureBtns, structureInput);
        
        // Material - Single Selection
        const materialBtns = component.querySelectorAll('.material-btn');
        const materialInput = component.querySelector('.material_input');
        setupSingleSelection(materialBtns, materialInput);
        
        // Remaining Life - Single Selection
        const remainingLifeBtns = component.querySelectorAll('.remaining-life-btn');
        const remainingLifeInput = component.querySelector('.remaining_life_input');
        setupSingleSelection(remainingLifeBtns, remainingLifeInput);
        
        // Cost Controls
        setupCostControls(component);
        
        // Collapsible Section
        setupCollapsibleSection(component);
        
        // Drag and Drop
        setupDragAndDrop(component);
        
        // Image Upload
        const addImageBtn = component.querySelector('.add-image-btn-component');
        const imageFileInput = component.querySelector('.image-file-input-component');
        const imagesPreview = component.querySelector('.images-preview-component');
        
        if (addImageBtn && imageFileInput) {
            addImageBtn.addEventListener('click', function() {
                imageFileInput.click();
            });
            
            imageFileInput.addEventListener('change', function(e) {
                handleImageUpload(e.target.files, imagesPreview);
            });
        }
        
        // Voice Input (Placeholder)
        const voiceInputBtn = component.querySelector('.voice-input-btn');
        if (voiceInputBtn) {
            voiceInputBtn.addEventListener('click', function() {
                alert('Voice input functionality will be implemented');
            });
        }
    }
    
    function setupSingleSelection(buttons, hiddenInput) {
        buttons.forEach(button => {
            button.addEventListener('click', function() {
                const selectedValue = this.dataset.value;
                const component = hiddenInput ? hiddenInput.closest('.component-form') : null;
                const group = hiddenInput?.dataset.areaGroup || this.dataset.group || null;
                const isAreaGroup = group === 'primary' || group === 'secondary';
                const requireUnique = isAreaGroup && hiddenInput?.dataset.requireUnique === 'true';
                const allowDuplicate = isAreaGroup && hiddenInput?.dataset.allowDuplicate === 'true';
                const groupContainer = isAreaGroup ? this.closest('.area-location-group') : null;
                const groupButtons = groupContainer ? groupContainer.querySelectorAll('.area-location-btn') : buttons;

                if (isAreaGroup && requireUnique && !allowDuplicate && component) {
                    const assessmentSection = component.closest('.survey-assessment-section');
                    if (assessmentSection) {
                        const duplicate = Array.from(assessmentSection.querySelectorAll('.component-form'))
                            .some(comp => comp !== component && (comp.dataset.areaPrimary || '').toLowerCase() === selectedValue.toLowerCase());
                        if (duplicate) {
                            alert(`Area/Location "${selectedValue}" is already used as a primary area in this section. Please select a different area.`);
                            return;
                        }
                    }
                }

                (groupButtons || buttons).forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                if (hiddenInput) {
                    hiddenInput.value = selectedValue;
                }

                if (component && isAreaGroup) {
                    if (group === 'primary') {
                        component.dataset.areaPrimary = selectedValue;
                    }
                    updateComponentHeaderTitle(component);
                }
            });
        });
    }
    
    function setupMultipleSelection(buttons, hiddenInput) {
        buttons.forEach(button => {
            button.addEventListener('click', function() {
                this.classList.toggle('active');
                const selected = Array.from(buttons)
                    .filter(btn => btn.classList.contains('active'))
                    .map(btn => btn.dataset.value)
                    .join(', ');
                if (hiddenInput) {
                    hiddenInput.value = selected;
                }
            });
        });
    }
    
    function handleImageUpload(files, previewContainer) {
        if (!previewContainer) return;
        Array.from(files).forEach(file => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewItem = document.createElement('div');
                    previewItem.className = 'survey-image-preview-item';
                    previewItem.innerHTML = `
                        <img src="${e.target.result}" alt="${file.name}">
                        <button type="button" class="survey-image-remove" onclick="this.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                    previewContainer.appendChild(previewItem);
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    function setupRatingIndicators(component) {
        if (!component) return;
        const assessmentSection = component.closest('.survey-assessment-section');
        if (!assessmentSection) return;
        const ratingControl = assessmentSection.querySelector('.component-rating-control');
        if (!ratingControl) return;
        updateRatingDisplay(component);
    }
    
    function updateRatingDisplay(component) {
        if (!component) return;
        const assessmentSection = component.closest('.survey-assessment-section');
        if (!assessmentSection) return;
        const ratingControl = assessmentSection.querySelector('.component-rating-control');
        if (!ratingControl) return;
        
        const ratingInput = component.querySelector('.condition_rating_input');
        const display = ratingControl.querySelector('.rating-display');
        const displayValue = ratingControl.querySelector('.rating-display-value');
        const options = ratingControl.querySelectorAll('.rating-option');
        
        const rawRating = (component.dataset.rating || '').toString().toLowerCase();
        const validRatings = ['1', '2', '3', 'ni'];
        const normalized = validRatings.includes(rawRating) ? rawRating : '';
        
        options.forEach(option => {
            option.classList.toggle('active', option.dataset.rating === normalized);
        });
        
        if (ratingInput) {
            ratingInput.value = normalized;
        }
        
        if (display) {
            display.classList.remove('rating-1', 'rating-2', 'rating-3', 'rating-ni');
            if (normalized) {
                display.classList.add(`rating-${normalized}`);
            }
        }
        
        if (displayValue) {
            displayValue.textContent = normalized ? (normalized === 'ni' ? 'NI' : normalized) : '--';
        }
    }
    
    function setComponentRating(componentId, rating, component, updateData = true) {
        if (!component) {
            component = document.querySelector(`.component-form[data-component-id="${componentId}"]`);
        }
        if (!component) return;
        
        const allowedRatings = ['1', '2', '3', 'ni'];
        const normalizedRatingRaw = (rating || '').toString().toLowerCase();
        const normalizedRating = allowedRatings.includes(normalizedRatingRaw) ? normalizedRatingRaw : 'ni';
        
        // Update component data
        if (updateData) {
            component.dataset.rating = normalizedRating;
        }
        
        // Update rating display for this specific component
        updateRatingDisplay(component);
        
        // Close the rating selector after choosing a rating
        const assessmentSection = component.closest('.survey-assessment-section');
        if (!assessmentSection) return;
        const ratingControl = assessmentSection.querySelector('.component-rating-control');
        if (ratingControl) {
            ratingControl.classList.remove('open');
        }
        
        // Update assessment indices to include rating in form data
        updateAssessmentIndices();
    }
    
    // Rating display toggle handler
    document.addEventListener('click', function(e) {
        const displayBtn = e.target.closest('.rating-display');
        if (displayBtn) {
            e.preventDefault();
            e.stopPropagation();
            const ratingControl = displayBtn.closest('.component-rating-control');
            if (ratingControl) {
                const isOpen = ratingControl.classList.contains('open');
                document.querySelectorAll('.component-rating-control.open').forEach(ctrl => {
                    if (ctrl !== ratingControl) {
                        ctrl.classList.remove('open');
                    }
                });
                ratingControl.classList.toggle('open', !isOpen);
            }
            return;
        }
    });
    
    // Rating option click handler (event delegation)
    document.addEventListener('click', function(e) {
        const ratingBtn = e.target.closest('.rating-option');
        if (ratingBtn) {
            e.preventDefault();
            e.stopPropagation();
            
            const rating = ratingBtn.dataset.rating;
            const assessmentSection = ratingBtn.closest('.survey-assessment-section');
            if (!assessmentSection) return false;
            const component = assessmentSection.querySelector('.component-form.active') || assessmentSection.querySelector('.component-form');
            if (component) {
                const componentId = component.dataset.componentId;
                setComponentRating(componentId, rating, component);
            }
            return false;
        }
    });
    
    // Close rating selectors when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.component-rating-control')) {
            document.querySelectorAll('.component-rating-control.open').forEach(ctrl => ctrl.classList.remove('open'));
        }
    });
});
</script>
@endpush
