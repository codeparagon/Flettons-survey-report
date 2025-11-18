<div class="survey-data-mock-section-item" data-section-id="{{ $section['id'] }}">
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
         style="display: none;"
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
                        $isExterior = $categoryName === 'Exterior';
                        $sectionOptions = $isExterior 
                            ? ['Main Roof', 'Side Extension', 'Rear Extension', 'Dormer', 'Lean-to']
                            : [$section['name']];
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
                    <div class="survey-data-mock-field-group">
                        <label class="survey-data-mock-field-label">Location</label>
                        <div class="survey-data-mock-button-group">
                            <button type="button" class="survey-data-mock-button" data-value="Whole Property" data-group="location">Whole Property</button>
                            <button type="button" class="survey-data-mock-button" data-value="Right" data-group="location">Right</button>
                            <button type="button" class="survey-data-mock-button" data-value="Left" data-group="location">Left</button>
                            <button type="button" class="survey-data-mock-button" data-value="Front" data-group="location">Front</button>
                            <button type="button" class="survey-data-mock-button" data-value="Rear" data-group="location">Rear</button>
                        </div>
                    </div>

                    <!-- Structure Buttons -->
                    @php
                        $structureOptions = $isExterior 
                            ? ['Pitched', 'Flat', 'Inverted pitched', 'Mono-Pitch', 'Curved']
                            : ['Standard', 'Flat', 'Pitched', 'Suspended', 'Solid'];
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
                        $materialOptions = $isExterior 
                            ? ['Double Glazed Aluminium', 'Polycarbonate', 'Slate', 'Asphalt', 'Concrete Interlocking', 'Fibre Slate', 'Felt']
                            : ['Plasterboard', 'Plaster', 'Timber', 'Concrete', 'Mixed'];
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
                    <div class="survey-data-mock-field-group">
                        <label class="survey-data-mock-field-label">Defects</label>
                        <div class="survey-data-mock-button-group">
                            <button type="button" class="survey-data-mock-button" data-value="Holes" data-group="defects" data-multiple="true">Holes</button>
                            <button type="button" class="survey-data-mock-button" data-value="Perished" data-group="defects" data-multiple="true">Perished</button>
                            <button type="button" class="survey-data-mock-button" data-value="Thermal Sag" data-group="defects" data-multiple="true">Thermal Sag</button>
                            <button type="button" class="survey-data-mock-button" data-value="Deflection" data-group="defects" data-multiple="true">Deflection</button>
                            <button type="button" class="survey-data-mock-button" data-value="Rot" data-group="defects" data-multiple="true">Rot</button>
                            <button type="button" class="survey-data-mock-button" data-value="Moss" data-group="defects" data-multiple="true">Moss</button>
                            <button type="button" class="survey-data-mock-button" data-value="Lichen" data-group="defects" data-multiple="true">Lichen</button>
                            <button type="button" class="survey-data-mock-button" data-value="Slipped Tiles" data-group="defects" data-multiple="true">Slipped Tiles</button>
                            <button type="button" class="survey-data-mock-button" data-value="None" data-group="defects" data-multiple="true">None</button>
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
                    <div class="survey-data-mock-field-group">
                        <label class="survey-data-mock-field-label">Remaining Life (Years)</label>
                        <div class="survey-data-mock-button-group">
                            <button type="button" class="survey-data-mock-button" data-value="0" data-group="remaining_life">0</button>
                            <button type="button" class="survey-data-mock-button" data-value="1-5" data-group="remaining_life">1-5</button>
                            <button type="button" class="survey-data-mock-button" data-value="6-10" data-group="remaining_life">6-10</button>
                            <button type="button" class="survey-data-mock-button" data-value="10+" data-group="remaining_life">10+</button>
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
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($section['costs']) && count($section['costs']) > 0)
                                    @foreach($section['costs'] as $cost)
                                        <tr>
                                            <td>{{ $cost['category'] }}</td>
                                            <td>{{ $cost['description'] }}</td>
                                            <td>{{ $cost['due'] }}</td>
                                            <td>{{ $cost['cost'] }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="survey-data-mock-no-costs">No costs added</td>
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
                    <div class="survey-data-mock-field-group">
                        <label class="survey-data-mock-field-label">Images</label>
                        <div class="survey-data-mock-images-upload">
                            <i class="fas fa-cloud-upload-alt survey-data-mock-upload-icon"></i>
                            <p class="survey-data-mock-upload-text">Drag and Drop Photos or Upload</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="survey-data-mock-actions">
                <button type="button" class="survey-data-mock-action-btn survey-data-mock-action-delete" data-section-id="{{ $section['id'] }}">Delete</button>
                <button type="button" class="survey-data-mock-action-btn survey-data-mock-action-clone" data-section-id="{{ $section['id'] }}" data-section-name="{{ $section['name'] }}">Save & Clone</button>
                <button type="button" class="survey-data-mock-action-btn survey-data-mock-action-save" data-section-id="{{ $section['id'] }}">Save</button>
            </div>
        </div>
    </div>
</div>

