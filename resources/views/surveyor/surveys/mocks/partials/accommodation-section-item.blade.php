<div class="survey-data-mock-section-item" data-section-id="{{ $accommodation['id'] }}" data-accommodation-id="{{ $accommodation['id'] }}" data-accommodation-type-id="{{ !empty($accommodation['accommodation_type_id']) ? $accommodation['accommodation_type_id'] : '' }}">
    <div class="survey-data-mock-section-header" data-expandable="true">
        <div class="survey-data-mock-section-name">
            {{ $accommodation['accommodation_type_name'] ?? $accommodation['name'] }}
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
                <span class="survey-data-mock-status-text">0/10</span>
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
        <h3 class="survey-data-mock-section-title-text">{{ $accommodation['accommodation_type_name'] ?? $accommodation['name'] }}</h3>
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
    
    <!-- Expanded Carousel Content -->
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
                                                $defects = app(\App\Services\SurveyAccommodationDataService::class)->getComponentDefects();
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
                    <div class="survey-data-mock-field-group">
                        <label class="survey-data-mock-field-label">Images</label>
                        <div class="survey-data-mock-images-upload" data-accommodation-id="{{ $accommodation['id'] }}">
                            <i class="fas fa-cloud-upload-alt survey-data-mock-upload-icon"></i>
                            <p class="survey-data-mock-upload-text">
                                Drag and Drop Your<br>
                                Videos and Photos or<br>
                                <strong>Upload</strong>
                            </p>
                        </div>
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
</div>

