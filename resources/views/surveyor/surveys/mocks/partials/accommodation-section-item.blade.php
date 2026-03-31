@php
    $accFormSubmitted = ($accommodation['form_submitted'] ?? false) || ($accommodation['has_report'] ?? false);
@endphp
<div class="survey-data-mock-section-item" data-section-id="{{ $accommodation['id'] }}" data-accommodation-id="{{ $accommodation['id'] }}" data-accommodation-type-id="{{ !empty($accommodation['accommodation_type_id']) ? $accommodation['accommodation_type_id'] : '' }}" data-clone-index="{{ $accommodation['clone_index'] ?? 0 }}" data-has-report="{{ ($accommodation['has_report'] ?? false) ? 'true' : 'false' }}" data-saved="{{ $accFormSubmitted ? 'true' : 'false' }}">
    <div class="survey-data-mock-section-header" data-expandable="true">
        <div class="survey-data-mock-section-name">
            {{ $accommodation['display_label'] ?? $accommodation['name'] ?? ($accommodation['accommodation_type_name'] ?? '') }}
        </div>
        <div class="survey-data-mock-section-status">
            @php
                $photoCount = count($accommodation['photos'] ?? []);
            @endphp
            <span class="survey-data-mock-status-info">
                <i class="fas fa-camera survey-data-mock-status-icon"></i>
                <span class="survey-data-mock-status-text">{{ $photoCount }}</span>
                <span class="survey-data-mock-status-separator">|</span>
                <i class="fas fa-sticky-note survey-data-mock-status-icon"></i>
                <span class="survey-data-mock-status-text">{{ $accommodation['completed_components'] ?? 0 }}/{{ $accommodation['total_components'] ?? 0 }}</span>
            </span>
            <span class="survey-data-mock-condition-badge survey-data-mock-condition-badge--{{ $accommodation['condition_rating'] ?? 'ni' }}" 
                  data-section-id="{{ $accommodation['id'] }}"
                  data-accommodation-id="{{ $accommodation['id'] }}"
                  data-current-rating="{{ $accommodation['condition_rating'] ?? 'ni' }}">
                {{ $accommodation['condition_rating'] ?? 'NI' }}
            </span>
            <i class="fas fa-chevron-down survey-data-mock-expand-icon"></i>
        </div>
    </div>
    
    <!-- Section Title Header (visible when expanded) -->
    <div class="survey-data-mock-section-title-bar" style="display: none;">
        <h3 class="survey-data-mock-section-title-text">{{ $accommodation['display_label'] ?? $accommodation['name'] ?? ($accommodation['accommodation_type_name'] ?? '') }}</h3>
        <div class="d-flex align-items-center" style="gap: 10px;">
        <span class="survey-data-mock-condition-badge survey-data-mock-condition-badge--{{ $accommodation['condition_rating'] ?? 'ni' }}" 
              data-section-id="{{ $accommodation['id'] }}"
              data-accommodation-id="{{ $accommodation['id'] }}"
              data-current-rating="{{ $accommodation['condition_rating'] ?? 'ni' }}">
            {{ $accommodation['condition_rating'] ?? 'NI' }}
        </span> 
        <i class="fas fa-chevron-up survey-data-mock-section-title-collapse"></i>
        </div>
    </div>
    
    <!-- Expanded: form (materials / defects / notes / photos) -->
    <div class="survey-data-mock-section-details" style="display: none;">
        <div class="survey-data-mock-section-details-content">
            <!-- Component Tabs Navigation -->
            <div class="survey-data-mock-component-tabs">
                @foreach($accommodation['components'] as $index => $component)
                    <button type="button" 
                            class="survey-data-mock-component-tab {{ $index === 0 ? 'active' : '' }}" 
                            data-component-index="{{ $index }}"
                            data-component-key="{{ $component['component_key'] }}">
                        {{ $component['component_name'] }}
                    </button>
                @endforeach
            </div>

            <!-- Form Grid: Left Carousel, Right Shared Content -->
            <div class="survey-data-mock-form-grid">
                <!-- Left Column: Carousel with Material and Defects -->
                <div class="survey-data-mock-form-column survey-data-mock-form-column-left" data-column="left">
                    <div class="survey-data-mock-carousel-wrapper">
                        <div class="survey-data-mock-carousel-track" data-carousel-track>
                            @foreach($accommodation['components'] as $index => $component)
                                <div class="survey-data-mock-carousel-slide {{ $index === 0 ? 'active' : '' }}" 
                                     data-slide-index="{{ $index }}"
                                     data-component-key="{{ $component['component_key'] }}">
                                    <div class="survey-data-mock-accommodation-component-form">
                                        <!-- Material Buttons -->
                                        <div class="survey-data-mock-field-group">
                                            <label class="survey-data-mock-field-label">
                                                {{ $component['component_name'] }} Material
                                            </label>
                                            <div class="survey-data-mock-button-group">
                                                @php
                                                    $materials = app(\App\Services\SurveyAccommodationDataService::class)->getComponentMaterials($component['component_key']);
                                                @endphp
                                                @foreach($materials as $material)
                                                    <button type="button" 
                                                            class="survey-data-mock-button {{ $component['material'] === $material ? 'active' : '' }}" 
                                                            data-value="{{ $material }}" 
                                                            data-group="material"
                                                            data-component-key="{{ $component['component_key'] }}">
                                                        {{ $material }}
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>

                                        <!-- Defects Buttons -->
                                        <div class="survey-data-mock-field-group">
                                            <label class="survey-data-mock-field-label">Defects</label>
                                            <div class="survey-data-mock-button-group">
                                                @php
                                                    $defects = app(\App\Services\SurveyAccommodationDataService::class)
                                                        ->getComponentDefects($component['component_key']);
                                                @endphp
                                                @foreach($defects as $defect)
                                                    <button type="button" 
                                                            class="survey-data-mock-button {{ in_array($defect, $component['defects'] ?? []) ? 'active' : '' }}" 
                                                            data-value="{{ $defect }}" 
                                                            data-group="defects"
                                                            data-multiple="true"
                                                            data-component-key="{{ $component['component_key'] }}">
                                                        {{ $defect }}
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <!-- Carousel Indicator Dots -->
                        <div class="survey-data-mock-carousel-indicators" data-carousel-indicators>
                            @foreach($accommodation['components'] as $index => $component)
                                <button type="button" 
                                        class="survey-data-mock-carousel-indicator {{ $index === 0 ? 'active' : '' }}" 
                                        data-slide-index="{{ $index }}"
                                        aria-label="Go to slide {{ $index + 1 }}">
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Draggable Divider -->
                <div class="survey-data-mock-form-grid-divider" data-divider>
                    <div class="survey-data-mock-form-grid-divider-handle">
                        <i class="fas fa-grip-vertical"></i>
                    </div>
                </div>

                <!-- Right Column: Shared Additional Notes and Images -->
                <div class="survey-data-mock-form-column survey-data-mock-form-column-right" data-column="right">
                    <!-- Additional Notes -->
                    <div class="survey-data-mock-field-group">
                        <label class="survey-data-mock-field-label">Additional Notes</label>
                        <div class="survey-data-mock-notes-wrapper">
                            <textarea class="survey-data-mock-notes-input" 
                                      rows="4" 
                                      placeholder="Enter additional notes..."
                                      data-accommodation-id="{{ $accommodation['id'] }}">{{ $accommodation['notes'] ?? '' }}</textarea>
                            <button type="button" class="survey-data-mock-mic-btn" title="Voice input">
                                <i class="fas fa-microphone"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Images Upload -->
                    <div class="survey-data-mock-field-group survey-data-mock-images-section">
                        @php $photoCount = count($accommodation['photos'] ?? []); @endphp
                        <input type="file" class="survey-data-mock-file-input" multiple accept="image/*" style="display: none;">
                        
                        <!-- Upload Area -->
                        <div class="survey-data-mock-upload-dropzone" data-accommodation-id="{{ $accommodation['id'] }}">
                            <i class="fas fa-cloud-upload-alt survey-data-mock-upload-icon-main"></i>
                            <p class="survey-data-mock-upload-title">
                                Drop images here ({{ $photoCount }} image{{ $photoCount == 1 ? '' : 's' }})
                            </p>
                            <p class="survey-data-mock-upload-subtitle">or <span class="survey-data-mock-upload-browse">browse</span> to upload</p>
                        </div>
                        
                        <!-- Image Preview Area (for unsaved files) -->
                        <div class="survey-data-mock-images-preview" style="display: none;">
                            <div class="survey-data-mock-images-grid-enhanced"></div>
                        </div>
                        
                        <!-- Existing Images Display -->
                        @if(isset($accommodation['photos']) && is_array($accommodation['photos']) && count($accommodation['photos']) > 0)
                            <div class="survey-data-mock-existing-images">
                                <div class="survey-data-mock-images-grid-enhanced">
                                    @foreach($accommodation['photos'] as $index => $photo)
                                        @if(is_array($photo) && isset($photo['id']))
                                            @php
                                                $imageUrl = $photo['url'] ?? '';
                                                if (empty($imageUrl) && isset($photo['file_path'])) {
                                                    $imageUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($photo['file_path']);
                                                }
                                                // Fallback for S3 URLs
                                                if (empty($imageUrl) && isset($photo['s3_url'])) {
                                                    $imageUrl = $photo['s3_url'];
                                                }
                                            @endphp
                                            <div class="survey-data-mock-image-card" data-photo-id="{{ $photo['id'] }}" data-image-url="{{ $imageUrl }}">
                                                <div class="survey-data-mock-image-wrapper">
                                                    <img src="{{ $imageUrl }}" alt="" class="survey-data-mock-image-thumb" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                    <div class="survey-data-mock-image-error"><i class="fas fa-image"></i></div>
                                                    <div class="survey-data-mock-image-overlay">
                                                        <button type="button" class="survey-data-mock-image-action survey-data-mock-image-preview-btn" title="Preview">
                                                            <i class="fas fa-expand"></i>
                                                        </button>
                                                        <button type="button" class="survey-data-mock-image-action survey-data-mock-image-delete" data-photo-id="{{ $photo['id'] }}" title="Delete">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="survey-data-mock-image-info">
                                                    <span class="survey-data-mock-image-number">#{{ $index + 1 }}</span>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="survey-data-mock-actions">
                <button type="button" 
                        class="survey-data-mock-action-btn survey-data-mock-action-delete" 
                        data-accommodation-id="{{ $accommodation['id'] }}">
                    Delete
                </button>
                <button type="button" 
                        class="survey-data-mock-action-btn survey-data-mock-action-clone" 
                        data-accommodation-id="{{ $accommodation['id'] }}"
                        data-accommodation-name="{{ $accommodation['name'] }}">
                    Save and Clone
                </button>
                <button type="button" 
                        class="survey-data-mock-action-btn survey-data-mock-action-save" 
                        data-accommodation-id="{{ $accommodation['id'] }}">
                    Save
                </button>
            </div>
        </div>
    </div>

    <!-- Submitted: plain-text selection summary (read-only) + icon bar; AI narrative is in Combined narratives above -->
    <div class="survey-data-mock-report-content survey-data-mock-report-content--accommodation" style="display: none;" data-accommodation-id="{{ $accommodation['id'] }}" data-initial-has-report="{{ $accFormSubmitted ? 'true' : 'false' }}">
        <p class="survey-data-mock-accommodation-report-hint" style="margin: 0 0 0.75rem 0; padding: 0 0.25rem; font-size: 0.8125rem; color: #64748b;">
            <i class="fas fa-check-circle" style="color:#22c55e;"></i> Submitted — summary of your selections below. Use <strong>Edit</strong> (pencil) to change materials, defects, or notes. For AI narrative across all rooms of this type, use <strong>Combined narratives</strong> above (or the arrow button).
        </p>
        <div class="survey-data-mock-report-content-wrapper">
            <textarea class="survey-data-mock-report-textarea survey-data-mock-accommodation-report-textarea" rows="12" placeholder="Your selections will appear here after saving…" @if($accFormSubmitted) disabled @endif>{{ $accommodation['report_content'] ?? '' }}</textarea>
            <div class="survey-data-mock-action-icons">
                <button type="button" class="survey-data-mock-action-icon-btn" data-action="speaker" title="Text to Speech">
                    <i class="fas fa-volume-up"></i>
                </button>
                <button type="button" class="survey-data-mock-action-icon-btn" data-action="edit" title="Edit form">
                    <i class="fas fa-pencil-alt"></i>
                </button>
                <button type="button" class="survey-data-mock-action-icon-btn survey-data-mock-accommodation-scroll-combined" title="Scroll to combined component reports for this accommodation type">
                    <i class="fas fa-arrow-up"></i>
                </button>
                <button type="button" class="survey-data-mock-action-icon-btn" data-action="eye" title="Preview">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
        </div>
    </div>
</div>

