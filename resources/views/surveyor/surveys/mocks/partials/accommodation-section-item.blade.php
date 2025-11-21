<div class="survey-data-mock-accommodation-item" data-accommodation-id="{{ $accommodation['id'] }}">
    <div class="survey-data-mock-accommodation-header" data-expandable="true">
        <div class="survey-data-mock-accommodation-name">
            <i class="fas fa-chevron-right survey-data-mock-accommodation-expand-icon"></i>
            {{ $accommodation['name'] }}
        </div>
        <div class="survey-data-mock-accommodation-status">
            <i class="fas fa-chevron-down survey-data-mock-expand-icon"></i>
        </div>
    </div>
    
    <!-- Expanded Carousel Content -->
    <div class="survey-data-mock-accommodation-details" style="display: none;">
        <div class="survey-data-mock-accommodation-carousel-container">
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
            <div class="survey-data-mock-accommodation-form-grid">
                <!-- Left Column: Carousel with Material and Defects -->
                <div class="survey-data-mock-accommodation-form-column survey-data-mock-accommodation-form-column-left" data-column="left">
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
                                                $materials = app(\App\Services\SurveyDataService::class)->getComponentMaterials($component['component_key']);
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
                                                $defects = app(\App\Services\SurveyDataService::class)->getComponentDefects();
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
                <div class="survey-data-mock-accommodation-form-grid-divider" data-divider>
                    <div class="survey-data-mock-accommodation-form-grid-divider-handle">
                        <i class="fas fa-grip-vertical"></i>
                    </div>
                </div>

                <!-- Right Column: Shared Additional Notes and Images -->
                <div class="survey-data-mock-accommodation-form-column survey-data-mock-accommodation-form-column-right" data-column="right">
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
                Save & Clone
            </button>
            <button type="button" 
                    class="survey-data-mock-action-btn survey-data-mock-action-save" 
                    data-accommodation-id="{{ $accommodation['id'] }}">
                Save
            </button>
        </div>
    </div>
</div>

