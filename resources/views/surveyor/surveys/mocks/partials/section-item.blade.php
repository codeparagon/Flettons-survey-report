<div class="survey-data-mock-section-item" data-section-id="{{ $section['id'] }}" data-section-definition-id="{{ $section['section_id'] ?? $section['id'] }}" data-has-report="{{ ($section['has_report'] ?? false) ? 'true' : 'false' }}" data-saved="{{ ($section['has_report'] ?? false) ? 'true' : 'false' }}">
    <div class="survey-data-mock-section-header" data-expandable="true">
        <div class="survey-data-mock-section-name">
            {{ $section['name'] }}
        </div>
        <div class="survey-data-mock-section-status">
            @php
                $photoCount = count($section['photos'] ?? []);
            @endphp
            <span class="survey-data-mock-status-info">
                <i class="fas fa-camera survey-data-mock-status-icon"></i>
                <span class="survey-data-mock-status-text">{{ $photoCount }}</span>
                <span class="survey-data-mock-status-separator">|</span>
                <i class="fas fa-sticky-note survey-data-mock-status-icon"></i>
                <span class="survey-data-mock-status-text">{{ $section['completion'] }}/{{ $section['total'] }}</span>
            </span>
            <span class="survey-data-mock-condition-badge survey-data-mock-condition-badge--{{ $section['condition_rating'] ?? 'ni' }}" 
                  data-section-id="{{ $section['id'] }}"
                  data-current-rating="{{ $section['condition_rating'] ?? 'ni' }}">
                {{ $section['condition_rating'] ?? 'NI' }}
            </span>
            <i class="fas fa-chevron-down survey-data-mock-expand-icon"></i>
        </div>
    </div>
    
    <!-- Section Title Header (visible when expanded) -->
    <div class="survey-data-mock-section-title-bar" style="display: none;">
        <h3 class="survey-data-mock-section-title-text">{{ $section['name'] }}</h3>
        <div class="d-flex align-items-center" style="gap: 10px;">
        <span class="survey-data-mock-condition-badge survey-data-mock-condition-badge--{{ $section['condition_rating'] ?? 'ni' }}" 
              data-section-id="{{ $section['id'] }}"
              data-current-rating="{{ $section['condition_rating'] ?? 'ni' }}">
            {{ $section['condition_rating'] ?? 'NI' }}
        </span> 
        <i class="fas fa-chevron-up survey-data-mock-section-title-collapse"></i>
        </div>
    </div>

    <!-- Expanded Form Content -->
    <div class="survey-data-mock-section-details" 
         style="display: {{ ($section['has_report'] ?? false) ? 'none' : 'none' }};"
         data-selected-section="{{ $section['selected_section'] ?? '' }}"
         data-selected-location="{{ $section['location'] ?? '' }}"
         data-selected-structure="{{ $section['structure'] ?? '' }}"
         data-selected-material="{{ $section['material'] ?? '' }}"
         data-selected-defects="{{ json_encode($section['defects'] ?? []) }}"
         data-selected-remaining-life="{{ $section['remaining_life'] ?? '' }}">
        <div class="survey-data-mock-section-details-content">
            <div class="survey-data-mock-form-grid">
                <!-- Left Column -->
                <div class="survey-data-mock-form-column survey-data-mock-form-column-left" data-column="left">
                    <!-- Section Buttons - Dynamic based on category -->
                    @php
                        $sectionOptions = $optionsMapping[$categoryName]['section'] ?? [$section['name']];
                        if (empty($sectionOptions)) {
                            $sectionOptions = [$section['name']];
                        }
                    @endphp
                    <div class="survey-data-mock-field-group">
                        <label class="survey-data-mock-field-label">Section</label>
                        <div class="survey-data-mock-button-group">
                            @foreach($sectionOptions as $option)
                                <button type="button" class="survey-data-mock-button" data-value="{{ $option }}" data-group="section">{{ $option }}</button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Location Buttons -->
                    @php
                        $locationOptions = $optionsMapping['location'] ?? ['Whole Property', 'Right', 'Left', 'Front', 'Rear'];
                    @endphp
                    <div class="survey-data-mock-field-group">
                        <label class="survey-data-mock-field-label">Location</label>
                        <div class="survey-data-mock-button-group">
                            @foreach($locationOptions as $option)
                                <button type="button" class="survey-data-mock-button" data-value="{{ $option }}" data-group="location">{{ $option }}</button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Structure Buttons -->
                    @php
                        $structureOptions = $optionsMapping[$categoryName]['structure'] ?? ['Standard'];
                    @endphp
                    <div class="survey-data-mock-field-group">
                        <label class="survey-data-mock-field-label">Structure</label>
                        <div class="survey-data-mock-button-group">
                            @foreach($structureOptions as $option)
                                <button type="button" class="survey-data-mock-button" data-value="{{ $option }}" data-group="structure">{{ $option }}</button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Material Buttons -->
                    @php
                        $materialOptions = $optionsMapping[$categoryName]['material'] ?? ['Mixed'];
                    @endphp
                    <div class="survey-data-mock-field-group">
                        <label class="survey-data-mock-field-label">Material</label>
                        <div class="survey-data-mock-button-group">
                            @foreach($materialOptions as $option)
                                <button type="button" class="survey-data-mock-button" data-value="{{ $option }}" data-group="material">{{ $option }}</button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Defects Buttons -->
                    @php
                        $defectOptions = $optionsMapping['defects'] ?? ['Holes', 'Perished', 'Thermal Sag', 'Deflection', 'Rot', 'Moss', 'Lichen', 'Slipped Tiles', 'None'];
                    @endphp
                    <div class="survey-data-mock-field-group">
                        <label class="survey-data-mock-field-label">Defects</label>
                        <div class="survey-data-mock-button-group">
                            @foreach($defectOptions as $option)
                                <button type="button" class="survey-data-mock-button" data-value="{{ $option }}" data-group="defects" data-multiple="true">{{ $option }}</button>
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
                
                <!-- Right Column -->
                <div class="survey-data-mock-form-column survey-data-mock-form-column-right" data-column="right">
                    <!-- Remaining Life Buttons -->
                    @php
                        $remainingLifeOptions = $optionsMapping['remaining_life'] ?? ['0', '1-5', '6-10', '10+'];
                    @endphp
                    <div class="survey-data-mock-field-group">
                        <label class="survey-data-mock-field-label">Remaining Life (Years)</label>
                        <div class="survey-data-mock-button-group">
                            @foreach($remainingLifeOptions as $option)
                                <button type="button" class="survey-data-mock-button" data-value="{{ $option }}" data-group="remaining_life">{{ $option }}</button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Estimated Costs -->
                    <div class="survey-data-mock-field-group survey-data-mock-costs-group">
                        <div class="survey-data-mock-costs-header">
                            <label class="survey-data-mock-field-label">Estimated Costs</label>
                            <button type="button" class="survey-data-mock-add-cost-btn">
                                <i class="fas fa-plus"></i> Add Cost
                            </button>
                        </div>
                        <table class="survey-data-mock-costs-table">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Description</th>
                                    <th>Due</th>
                                    <th>cost (£)</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($section['costs']) && count($section['costs']) > 0)
                                    @foreach($section['costs'] as $index => $cost)
                                        <tr data-cost-index="{{ $index }}">
                                            <td>{{ $cost['category'] }}</td>
                                            <td>{{ $cost['description'] }}</td>
                                            <td>{{ $cost['due'] }}</td>
                                            <td>{{ $cost['cost'] }}</td>
                                            <td>
                                                <div class="survey-data-mock-cost-actions">
                                                    <button type="button" class="survey-data-mock-cost-edit-btn" data-cost-index="{{ $index }}">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="survey-data-mock-cost-delete-btn" data-cost-index="{{ $index }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="survey-data-mock-no-costs">No costs added</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <!-- Additional Notes -->
                    <div class="survey-data-mock-field-group">
                        <label class="survey-data-mock-field-label">Additional Notes</label>
                        <div class="survey-data-mock-notes-wrapper">
                            <textarea class="survey-data-mock-notes-input" rows="4" placeholder="Enter additional notes...">{{ $section['notes'] ?? '' }}</textarea>
                            <button type="button" class="survey-data-mock-mic-btn" title="Voice input">
                                <i class="fas fa-microphone"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Images Upload -->
                    <div class="survey-data-mock-field-group survey-data-mock-images-section">
                        <label class="survey-data-mock-field-label">
                            Images
                            @php $photoCount = count($section['photos'] ?? []); @endphp
                            @if($photoCount > 0)
                                <span class="survey-data-mock-image-count" data-image-count>({{ $photoCount }})</span>
                            @endif
                        </label>
                        <input type="file" class="survey-data-mock-file-input" multiple accept="image/*" style="display: none;">
                        
                        <!-- Upload Area -->
                        <div class="survey-data-mock-upload-dropzone">
                            <i class="fas fa-cloud-upload-alt survey-data-mock-upload-icon-main"></i>
                            <p class="survey-data-mock-upload-title">Drop images here</p>
                            <p class="survey-data-mock-upload-subtitle">or <span class="survey-data-mock-upload-browse">browse</span> to upload</p>
                        </div>
                        
                        <!-- Image Preview Area (for unsaved files) -->
                        <div class="survey-data-mock-images-preview" style="display: none;">
                            <div class="survey-data-mock-images-grid-enhanced"></div>
                        </div>
                        
                        <!-- Existing Images Display -->
                        @if(isset($section['photos']) && is_array($section['photos']) && count($section['photos']) > 0)
                            <div class="survey-data-mock-existing-images">
                                <div class="survey-data-mock-images-grid-enhanced">
                                    @foreach($section['photos'] as $index => $photo)
                                        @if(is_array($photo) && isset($photo['id']))
                                            @php
                                                $imageUrl = $photo['url'] ?? '';
                                                if (empty($imageUrl) && isset($photo['file_path'])) {
                                                    $imageUrl = asset('storage/' . ltrim($photo['file_path'], '/'));
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
                <button type="button" class="survey-data-mock-action-btn survey-data-mock-action-delete" data-section-id="{{ $section['id'] }}">Delete</button>
                <button type="button" class="survey-data-mock-action-btn survey-data-mock-action-clone" data-section-id="{{ $section['id'] }}" data-section-name="{{ $section['name'] }}">Save and Clone</button>
                <button type="button" class="survey-data-mock-action-btn survey-data-mock-action-save" data-section-id="{{ $section['id'] }}">Save</button>
            </div>
        </div>
    </div>

    <!-- Report Content Area (shown after save) -->
    <div class="survey-data-mock-report-content" style="display: none;" data-section-id="{{ $section['id'] }}" data-initial-has-report="{{ ($section['has_report'] ?? false) ? 'true' : 'false' }}">
        <div class="survey-data-mock-report-content-wrapper">
            <textarea class="survey-data-mock-report-textarea" rows="12" placeholder="Report content will be generated after saving...">{{ $section['report_content'] ?? '' }}</textarea>
            
            <!-- Action Icons Bar -->
            <div class="survey-data-mock-action-icons">
                <button type="button" class="survey-data-mock-action-icon-btn" data-action="speaker" title="Text to Speech">
                    <i class="fas fa-volume-up"></i>
                </button>
                <button type="button" class="survey-data-mock-action-icon-btn" data-action="lock" title="Lock/Unlock Editing">
                    <i class="fas fa-lock"></i>
                </button>
                <button type="button" class="survey-data-mock-action-icon-btn" data-action="edit" title="Edit Form">
                    <i class="fas fa-pencil-alt"></i>
                </button>
                <button type="button" class="survey-data-mock-action-icon-btn" data-action="refresh" title="Regenerate Content">
                    <i class="fas fa-sync-alt"></i>
                </button>
                <button type="button" class="survey-data-mock-action-icon-btn" data-action="eye" title="Preview">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
        </div>
    </div>
</div>

