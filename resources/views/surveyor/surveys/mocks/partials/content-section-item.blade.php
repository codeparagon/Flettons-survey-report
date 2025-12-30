<div class="survey-data-mock-section-item survey-data-mock-content-section-item" 
     data-content-section-id="{{ $contentSection->id }}" 
     data-has-content="{{ !empty($contentSection->content) ? 'true' : 'false' }}" 
     data-saved="true">
    <div class="survey-data-mock-section-header" data-expandable="true">
        <div class="survey-data-mock-section-name">
            <i class="fas fa-file-alt" style="margin-right: 8px; color: #6b7280;"></i>
            {{ $contentSection->title }}
        </div>
        <div class="survey-data-mock-section-status">
            <span class="survey-data-mock-status-info">
                <i class="fas fa-file-alt survey-data-mock-status-icon"></i>
                <span class="survey-data-mock-status-text">Content</span>
            </span>
            <i class="fas fa-chevron-down survey-data-mock-expand-icon"></i>
        </div>
    </div>
    
    <!-- Section Title Header (visible when expanded) -->
    <div class="survey-data-mock-section-title-bar" style="display: none;">
        <h3 class="survey-data-mock-section-title-text">
            <i class="fas fa-file-alt" style="margin-right: 8px;"></i>
            {{ $contentSection->title }}
        </h3>
        <div class="d-flex align-items-center" style="gap: 10px;">
            <i class="fas fa-chevron-up survey-data-mock-section-title-collapse"></i>
        </div>
    </div>

    <!-- Content Area (shown when expanded) -->
    <div class="survey-data-mock-content-section-details" style="display: none;">
        <div class="survey-data-mock-section-details-content">
            <div class="survey-data-mock-report-content-wrapper">
                <textarea class="survey-data-mock-report-textarea survey-data-mock-content-textarea" 
                          rows="12" 
                          placeholder="Enter content here...">{{ $contentSection->content }}</textarea>
                
                <!-- Action Icons Bar -->
                <div class="survey-data-mock-action-icons">
                    <button type="button" class="survey-data-mock-action-icon-btn" data-action="save-content" title="Save Content">
                        <i class="fas fa-save"></i>
                    </button>
                    <button type="button" class="survey-data-mock-action-icon-btn" data-action="lock" title="Lock/Unlock Editing">
                        <i class="fas fa-lock"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

