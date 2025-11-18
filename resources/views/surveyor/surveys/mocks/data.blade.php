@extends('layouts.survey-mock')

@section('title', 'Survey Data')

@section('content')
<div class="survey-data-mock-content" data-survey-id="{{ $survey->id }}">
    <!-- Integrated Header Bar -->
    <div class="survey-data-mock-header-bar">
        <div class="survey-data-mock-header-left">
            <a href="{{ route('surveyor.surveys.index') }}" class="survey-data-mock-back">
                <i class="fas fa-chevron-left"></i>
                <span>Survey Data Capture</span>
            </a>
        </div>
        <div class="survey-data-mock-header-right">
            <div class="survey-data-mock-jobref">
                <span class="survey-data-mock-jobref-label">Workspace</span>
                <span class="survey-data-mock-jobref-value">{{ $survey->level ?? 'Level 1' }} Assessment</span>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="survey-data-mock-body">
        @foreach($categories as $categoryName => $subCategories)
            <section class="survey-data-mock-category">
                <h2 class="survey-data-mock-category-title">{{ $categoryName }}</h2>
                
                <div class="survey-data-mock-sections">
                    @foreach($subCategories as $subCategoryName => $sections)
                        <div class="survey-data-mock-sub-category" data-sub-category="{{ $subCategoryName }}">
                            <h3 class="survey-data-mock-sub-category-title">{{ $subCategoryName }}</h3>
                            
                            <div class="survey-data-mock-sub-category-sections">
                                @foreach($sections as $section)
                                    @include('surveyor.surveys.mocks.partials.section-item', ['section' => $section, 'categoryName' => $categoryName, 'subCategoryName' => $subCategoryName])
                                                    @endforeach
                                                </div>
                                            </div>
                                                    @endforeach
                                                </div>
            </section>
                                                    @endforeach
                                                </div>
                                            </div>

<!-- Rating Modal -->
<div id="survey-data-mock-rating-modal" class="survey-data-mock-rating-modal">
    <div class="survey-data-mock-rating-modal-content">
        <div class="survey-data-mock-rating-modal-header">
            <h3 class="survey-data-mock-rating-modal-title">Update Condition Rating</h3>
            <button type="button" class="survey-data-mock-rating-modal-close" id="rating-modal-close">
                <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
        <div class="survey-data-mock-rating-modal-body">
            <div class="survey-data-mock-rating-options">
                <div class="survey-data-mock-rating-option survey-data-mock-rating-option--1" data-rating="1">
                    <div class="survey-data-mock-rating-option-badge">1</div>
                    <div class="survey-data-mock-rating-option-label">Excellent</div>
                    <div class="survey-data-mock-rating-option-description">Good condition</div>
                                            </div>
                <div class="survey-data-mock-rating-option survey-data-mock-rating-option--2" data-rating="2">
                    <div class="survey-data-mock-rating-option-badge">2</div>
                    <div class="survey-data-mock-rating-option-label">Good</div>
                    <div class="survey-data-mock-rating-option-description">Fair condition</div>
                                                </div>
                <div class="survey-data-mock-rating-option survey-data-mock-rating-option--3" data-rating="3">
                    <div class="survey-data-mock-rating-option-badge">3</div>
                    <div class="survey-data-mock-rating-option-label">Fair</div>
                    <div class="survey-data-mock-rating-option-description">Poor condition</div>
                                            </div>
                <div class="survey-data-mock-rating-option survey-data-mock-rating-option--ni" data-rating="ni">
                    <div class="survey-data-mock-rating-option-badge">NI</div>
                    <div class="survey-data-mock-rating-option-label">Not Inspected</div>
                    <div class="survey-data-mock-rating-option-description">Not applicable</div>
                                                </div>
                                            </div>
                                        </div>
        <div class="survey-data-mock-rating-modal-footer">
            <button type="button" class="survey-data-mock-rating-modal-btn survey-data-mock-rating-modal-btn-cancel" id="rating-modal-cancel">Cancel</button>
            <button type="button" class="survey-data-mock-rating-modal-btn survey-data-mock-rating-modal-btn-save" id="rating-modal-save">Save</button>
                                    </div>
                                    </div>
                                </div>

@endsection

@push('styles')
<style>
    /* Poppins Font Family Throughout - Exclude Font Awesome icons */
    .survey-data-mock-content {
        font-family: 'Poppins', sans-serif;
    }

    /* Ensure Font Awesome icons use correct font */
    .survey-data-mock-content i,
    .survey-data-mock-content i.fas,
    .survey-data-mock-content i.far,
    .survey-data-mock-content i.fab {
        font-family: "Font Awesome 5 Free", "Font Awesome 5 Pro", "Font Awesome 6 Free", "Font Awesome 6 Pro", "FontAwesome" !important;
        font-weight: 900 !important;
        display: inline-block !important;
        font-style: normal !important;
    }

    /* Header Bar */
    .survey-data-mock-header-bar {
        background: #1E293B;
        padding: 1rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid rgba(148, 163, 184, 0.2);
    }

    .survey-data-mock-header-left {
        display: flex;
        align-items: center;
    }

    .survey-data-mock-back {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #C1EC4A;
        text-decoration: none;
        font-size: 16px;
        font-weight: 600;
        transition: color 0.2s ease;
    }

    .survey-data-mock-back:hover {
        color: #A8D043;
    }

    .survey-data-mock-back i {
        font-size: 14px;
        display: inline-block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    .survey-data-mock-header-right {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .survey-data-mock-jobref {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 0.25rem;
    }

    .survey-data-mock-jobref-label {
        font-size: 12px;
        color: #94A3B8;
        text-transform: uppercase;
        font-weight: 600;
    }

    .survey-data-mock-jobref-value {
        font-size: 16px;
        color: #FFFFFF;
        font-weight: 700;
    }

    /* Main Body */
    .survey-data-mock-body {
        padding: 2rem 1.5rem;
        background: #F1F5F9;
        min-height: calc(100vh - 120px);
    }

    /* Category Section */
    .survey-data-mock-category {
        margin-bottom: 3rem;
    }

    .survey-data-mock-category:last-child {
        margin-bottom: 0;
    }

    .survey-data-mock-category-title {
        font-size: 28px;
        font-weight: 800;
        color: #1A202C;
        margin: 0 0 1.5rem 0;
        font-family: 'Poppins', sans-serif;
    }

    /* Sub-Categories */
    .survey-data-mock-sub-category {
        margin-bottom: 1.5rem;
    }

    .survey-data-mock-sub-category-title {
        font-size: 20px;
        font-weight: 600;
        color: #334155;
        margin: 1rem 0 0.75rem 0;
        font-family: 'Poppins', sans-serif;
    }

    .survey-data-mock-sub-category-sections {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    /* Section Items */
    .survey-data-mock-sections {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .survey-data-mock-section-item {
        background: #FFFFFF;
        border-radius: 4px;
        overflow: hidden;
        border: 1px solid rgba(148, 163, 184, 0.2);
    }

    /* Cloned Section Styling - Same as main sections, no special styling */
    .survey-data-mock-section-item-cloned {
        /* No margin-left - cloned sections appear directly under parent */
        /* All other styling matches main sections */
    }

    .survey-data-mock-section-header {
        background: #1E293B;
        padding: 1rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        transition: background 0.2s ease;
        position: relative;
    }

    .survey-data-mock-section-header:hover {
        background: #475569;
    }

    .survey-data-mock-section-name {
        font-size: 16px;
        font-weight: 600;
        color: #FFFFFF;
    }

    .survey-data-mock-section-status {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .survey-data-mock-status-info {
        display: flex;
        align-items: center;
        gap: 0.375rem;
        font-size: 11px;
        color: #94A3B8;
        font-weight: 400;
    }

    .survey-data-mock-status-icon {
        font-size: 14px;
        color: #94A3B8;
    }

    .survey-data-mock-status-text {
        font-size: 15px;
        color: #94A3B8;
        font-weight: 400;
    }

    .survey-data-mock-status-separator {
        color: #CBD5E1;
        margin: 0 0.25rem;
        font-size: 10px;
    }

    .survey-data-mock-completion {
        font-size: 14px;
        font-weight: 600;
    }

    .survey-data-mock-condition-badge {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 700;
        color: #FFFFFF;
        border: 2px solid #FFFFFF;
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        flex-shrink: 0;
        pointer-events: auto;
        position: relative;
        z-index: 100;
    }

    .survey-data-mock-condition-badge:hover {
        transform: scale(1.1);
        box-shadow: 0 0 8px rgba(255, 255, 255, 0.3);
    }

    /* Centered badge in expanded section title bar */
    .survey-data-mock-section-title-bar .survey-data-mock-condition-badge {
        /* position: absolute;
        left: 50%;
        transform: translateX(-50%); */
    }

    .survey-data-mock-section-title-bar .survey-data-mock-condition-badge:hover {
        /* transform: translateX(-50%) scale(1.1); */
    }

    .survey-data-mock-condition-badge--3 {
        background: #EF4444;
    }

    .survey-data-mock-condition-badge--2 {
        background: #F59E0B;
    }

    .survey-data-mock-condition-badge--1 {
        background: #10B981;
    }

    .survey-data-mock-condition-badge--ni {
        background: #94A3B8;
    }

    .survey-data-mock-expand-icon {
        font-size: 14px;
        color: #C1EC4A;
        transition: transform 0.3s ease;
        display: inline-block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    .survey-data-mock-section-item.expanded .survey-data-mock-expand-icon {
        transform: rotate(180deg);
    }

    .survey-data-mock-section-item.expanded .survey-data-mock-section-header {
        display: none;
    }

    /* Expanded Details */
    .survey-data-mock-section-details {
        background: #FFFFFF;
        border-top: 1px solid rgba(148, 163, 184, 0.2);
    }

    .survey-data-mock-section-title-bar {
        background: #1E293B;
        padding: 1rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid rgba(148, 163, 184, 0.2);
        position: relative;
    }

    .survey-data-mock-section-item.expanded .survey-data-mock-section-title-bar {
        display: flex;
    }

    .survey-data-mock-section-title-text {
        font-size: 20px;
        font-weight: 700;
        color: #FFFFFF!important;
        margin: 0;
        font-family: 'Poppins', sans-serif;
    }

    .survey-data-mock-section-title-collapse {
        font-size: 16px;
        color: #C1EC4A;
        cursor: pointer;
        transition: transform 0.3s ease;
        display: inline-block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    .survey-data-mock-section-title-collapse:hover {
        color: #A8D043;
    }

    .survey-data-mock-section-details-content {
        padding: 1rem 1.5rem;
        max-height: calc(120vh - 200px);
        overflow-y: auto;
    }

    /* Two Column Grid Layout with Draggable Divider */
    .survey-data-mock-form-grid {
        display: flex;
        gap: 0;
        margin-bottom: 1rem;
        position: relative;
    }

    .survey-data-mock-form-grid-divider {
        width: 4px;
        background: #E2E8F0;
        cursor: col-resize;
        position: relative;
        flex-shrink: 0;
        transition: background 0.2s ease;
        z-index: 10;
    }

    .survey-data-mock-form-grid-divider:hover,
    .survey-data-mock-form-grid-divider.dragging {
        background: #C1EC4A;
    }

    .survey-data-mock-form-grid-divider::before {
        content: '';
        position: absolute;
        left: -2px;
        right: -2px;
        top: 0;
        bottom: 0;
        cursor: col-resize;
    }

    .survey-data-mock-form-grid-divider-handle {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 20px;
        height: 40px;
        background: #C1EC4A;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.2s ease;
        pointer-events: none;
    }

    .survey-data-mock-form-grid-divider:hover .survey-data-mock-form-grid-divider-handle,
    .survey-data-mock-form-grid-divider.dragging .survey-data-mock-form-grid-divider-handle {
        opacity: 1;
    }

    .survey-data-mock-form-grid-divider-handle i {
        color: #1A202C;
        font-size: 12px;
    }

    .survey-data-mock-form-column {
        display: flex;
        flex-direction: column;
        min-width: 0; /* Allow flex items to shrink below content size */
    }

    .survey-data-mock-form-column-left {
        flex: 0 0 50%;
        gap: 1rem;
        padding-right: 0.75rem;
    }

    .survey-data-mock-form-column-right {
        flex: 0 0 50%;
        gap: 1rem;
        padding-left: 0.75rem;
    }

    /* Field Groups */
    .survey-data-mock-field-group {
        margin-bottom: 1rem;
    }

    .survey-data-mock-field-group:last-child {
        margin-bottom: 0;
    }

    .survey-data-mock-field-label {
        display: block;
        font-size: 13px;
        color: #64748B;
        font-weight: 600;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        font-family: 'Poppins', sans-serif;
    }

    /* Button Groups */
    .survey-data-mock-button-group-wrapper {
        position: relative;
        width: 100%;
    }

    .survey-data-mock-button-group {
        display: flex;
        flex-wrap: wrap;
        gap: 0.375rem;
        position: relative;
    }

    /* Swiper Carousel Styles */
    .survey-data-mock-button-group.swiper {
        width: 100%;
        overflow: hidden!important;
        position: relative;
        margin: 0;
        padding: 0 40px; /* Add padding to prevent first/last buttons from being cut off */
    }

    .survey-data-mock-button-group.swiper .swiper-wrapper {
        display: flex;
        align-items: center;
        padding: 0;
        margin: 0;
    }

    .survey-data-mock-button-group.swiper .swiper-slide {
        width: auto !important;
        height: auto !important;
        flex-shrink: 0;
        margin: 0;
    }
    
    .survey-data-mock-button-group.swiper .swiper-slide .survey-data-mock-button {
        white-space: nowrap;
        margin: 0;
    }
    
    /* Remove gap when using Swiper (gap is handled by spaceBetween) */
    .survey-data-mock-button-group.swiper {
        gap: 0;
    }

    .survey-data-mock-button-group .swiper-button-next,
    .survey-data-mock-button-group .swiper-button-prev {
        width: 32px !important;
        height: 32px !important;
        background: rgba(255, 255, 255, 0.95) !important;
        border: 1px solid rgba(148, 163, 184, 0.3) !important;
        border-radius: 50% !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
        transition: all 0.2s ease !important;
        margin-top: 0 !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        z-index: 10 !important;
        position: absolute !important;
    }

    .survey-data-mock-button-group .swiper-button-prev {
        left: 4px !important;
        right: auto !important;
    }

    .survey-data-mock-button-group .swiper-button-next {
        right: 4px !important;
        left: auto !important;
    }
    
    /* Add padding to wrapper to prevent content from being hidden behind buttons */
    .survey-data-mock-field-group {
        position: relative;
    }

    .survey-data-mock-button-group .swiper-button-next:after,
    .survey-data-mock-button-group .swiper-button-prev:after {
        font-size: 12px !important;
        color: #64748B !important;
        font-weight: 600 !important;
    }

    .survey-data-mock-button-group .swiper-button-next:hover,
    .survey-data-mock-button-group .swiper-button-prev:hover {
        background: #FFFFFF !important;
        border-color: #C1EC4A !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
    }

    .survey-data-mock-button-group .swiper-button-next:hover:after,
    .survey-data-mock-button-group .swiper-button-prev:hover:after {
        color: #1A202C !important;
    }

    .survey-data-mock-button-group .swiper-button-disabled {
        opacity: 0.35 !important;
        cursor: auto !important;
        pointer-events: none !important;
    }
    


    .survey-data-mock-carousel-arrow {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.95);
        border: 1px solid rgba(148, 163, 184, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 10;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.2s ease;
        opacity: 0.8;
        pointer-events: auto;
    }

    .survey-data-mock-button-group-wrapper:hover .survey-data-mock-carousel-arrow {
        opacity: 1;
    }

    .survey-data-mock-carousel-arrow.hidden {
        opacity: 0;
        pointer-events: none;
    }

    .survey-data-mock-carousel-arrow:hover {
        background: #FFFFFF;
        border-color: #C1EC4A;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transform: translateY(-50%) scale(1.1);
    }

    .survey-data-mock-carousel-arrow:active {
        transform: translateY(-50%) scale(0.95);
    }

    .survey-data-mock-carousel-arrow-left {
        left: 0;
        padding-right: 2px;
    }

    .survey-data-mock-carousel-arrow-right {
        right: 0;
        padding-left: 2px;
    }

    .survey-data-mock-carousel-arrow i {
        font-size: 12px;
        color: #64748B;
        transition: color 0.2s ease;
    }

    .survey-data-mock-carousel-arrow:hover i {
        color: #1A202C;
    }

    .survey-data-mock-carousel-arrow.hidden {
        display: none;
    }

    .survey-data-mock-button {
        padding: 0.4rem 0.875rem;
        border: 1px solid rgba(148, 163, 184, 0.4);
        border-radius: 4px;
        background: #FFFFFF;
        color: #1A202C;
        font-size: 14px;
        font-weight: 400;
        cursor: pointer;
        transition: all 0.2s ease;
        font-family: 'Poppins', sans-serif;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .survey-data-mock-button:hover {
        background: #F1F5F9;
        border-color: rgba(148, 163, 184, 0.6);
    }

    .survey-data-mock-button.active {
        background: #C1EC4A;
        border-color: #C1EC4A;
        color: #1A202C;
    }

    /* Costs Table */
    .survey-data-mock-costs-group {
        margin-top: 0;
    }

    .survey-data-mock-costs-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .survey-data-mock-add-cost-btn {
        padding: 0.5rem 1rem;
        border: 1px solid rgba(148, 163, 184, 0.4);
        border-radius: 4px;
        background: #FFFFFF;
        color: #1A202C;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
        font-family: 'Poppins', sans-serif;
    }

    .survey-data-mock-add-cost-btn:hover {
        background: #F1F5F9;
        border-color: rgba(148, 163, 184, 0.6);
    }

    .survey-data-mock-add-cost-btn i {
        font-size: 12px;
        display: inline-block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    .survey-data-mock-costs-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid rgba(148, 163, 184, 0.2);
        border-radius: 4px;
        overflow: hidden;
    }

    .survey-data-mock-costs-table thead {
        background: #F8FAFC;
    }

    .survey-data-mock-costs-table th {
        padding: 0.5rem;
        text-align: left;
        font-size: 12px;
        font-weight: 600;
        color: #64748B;
        text-transform: uppercase;
        border-bottom: 1px solid rgba(148, 163, 184, 0.2);
    }

    .survey-data-mock-costs-table td {
        padding: 0.5rem;
        font-size: 13px;
        color: #1A202C;
        border-bottom: 1px solid rgba(148, 163, 184, 0.1);
    }

    .survey-data-mock-costs-table tbody tr:last-child td {
        border-bottom: none;
    }

    .survey-data-mock-no-costs {
        text-align: center;
        color: #94A3B8;
        font-style: italic;
    }

    /* Notes Input */
    .survey-data-mock-notes-wrapper {
        position: relative;
    }

    .survey-data-mock-notes-input {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid rgba(148, 163, 184, 0.4);
        border-radius: 4px;
        font-size: 13px;
        font-weight: 400;
        color: #1A202C;
        background: #FFFFFF;
        transition: border-color 0.2s ease;
        font-family: 'Poppins', sans-serif;
        resize: vertical;
        min-height: 60px;
    }

    .survey-data-mock-notes-input:focus {
        outline: none;
        border-color: #C1EC4A;
    }

    .survey-data-mock-mic-btn {
        position: absolute;
        bottom: 0.5rem;
        right: 0.5rem;
        background: rgba(148, 163, 184, 0.1);
        border: 1px solid rgba(148, 163, 184, 0.2);
        border-radius: 4px;
        padding: 0.5rem;
        cursor: pointer;
        color: #64748B;
        transition: all 0.2s ease;
    }

    .survey-data-mock-mic-btn:hover {
        background: rgba(148, 163, 184, 0.2);
        color: #475569;
    }

    .survey-data-mock-mic-btn i {
        font-size: 16px;
        display: inline-block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    /* Images Upload */
    .survey-data-mock-images-upload {
        border: 2px dashed rgba(148, 163, 184, 0.4);
        border-radius: 4px;
        padding: 1.5rem 1rem;
        text-align: center;
        background: #FAFBFC;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .survey-data-mock-images-upload:hover {
        border-color: #C1EC4A;
        background: #F8FAFC;
    }

    .survey-data-mock-upload-icon {
        font-size: 36px;
        color: #94A3B8;
        margin-bottom: 0.5rem;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    .survey-data-mock-upload-text {
        font-size: 14px;
        color: #64748B;
        font-weight: 600;
        margin: 0;
    }

    /* Action Buttons */
    .survey-data-mock-actions {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid rgba(148, 163, 184, 0.2);
    }

    .survey-data-mock-action-btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        font-family: 'Poppins', sans-serif;
    }

    .survey-data-mock-action-delete {
        background: #EF4444;
        color: #FFFFFF;
        margin-right: auto;
    }

    .survey-data-mock-action-delete:hover {
        background: #DC2626;
    }

    .survey-data-mock-action-clone {
        background: #475569;
        color: #FFFFFF;
        margin-left: auto;
    }

    .survey-data-mock-action-clone:hover {
        background: #334155;
    }

    .survey-data-mock-action-save {
        background: #1E293B;
        color: #FFFFFF;
    }

    .survey-data-mock-action-save:hover {
        background: #0F172A;
    }

    /* Clone Modal */
    .survey-data-mock-clone-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 10000;
        align-items: center;
        justify-content: center;
    }

    .survey-data-mock-clone-modal.show {
        display: flex;
    }

    .survey-data-mock-clone-modal-content {
        background: #FFFFFF;
        border-radius: 4px;
        padding: 2rem;
        max-width: 500px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
    }

    .survey-data-mock-clone-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .survey-data-mock-clone-modal-title {
        font-size: 20px;
        font-weight: 700;
        color: #1A202C;
        margin: 0;
    }

    .survey-data-mock-clone-modal-close {
        background: none;
        border: none;
        font-size: 24px;
        color: #94A3B8;
        cursor: pointer;
        padding: 0;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        transition: all 0.2s ease;
    }

    .survey-data-mock-clone-modal-close:hover {
        background: rgba(148, 163, 184, 0.1);
        color: #64748B;
    }

    .survey-data-mock-clone-modal-body {
        margin-bottom: 1.5rem;
    }

    .survey-data-mock-clone-modal-field {
        margin-bottom: 1.5rem;
    }

    .survey-data-mock-clone-modal-field:last-child {
        margin-bottom: 0;
    }

    .survey-data-mock-clone-modal-label {
        display: block;
        font-size: 13px;
        color: #64748B;
        font-weight: 600;
        margin-bottom: 0.75rem;
        text-transform: uppercase;
    }

    .survey-data-mock-clone-section-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .survey-data-mock-clone-section-btn {
        padding: 0.625rem 1.25rem;
        border: 1px solid rgba(148, 163, 184, 0.4);
        border-radius: 4px;
        background: #FFFFFF;
        color: #1A202C;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        font-family: 'Poppins', sans-serif;
    }

    .survey-data-mock-clone-section-btn:hover {
        background: #F1F5F9;
        border-color: rgba(148, 163, 184, 0.6);
    }

    .survey-data-mock-clone-section-btn.active {
        background: #C1EC4A;
        border-color: #C1EC4A;
        color: #1A202C;
    }

    .survey-data-mock-clone-section-btn.disabled {
        background: #F1F5F9;
        border-color: #E2E8F0;
        color: #94A3B8;
        cursor: not-allowed;
        opacity: 0.6;
    }

    .survey-data-mock-clone-section-btn.disabled:hover {
        background: #F1F5F9;
        border-color: #E2E8F0;
    }

    .survey-data-mock-clone-modal-input,
    .survey-data-mock-clone-modal-select {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid rgba(148, 163, 184, 0.4);
        border-radius: 4px;
        font-size: 14px;
        font-weight: 400;
        color: #1A202C;
        background: #FFFFFF;
        transition: border-color 0.2s ease;
        font-family: 'Poppins', sans-serif;
    }

    .survey-data-mock-clone-modal-input:focus,
    .survey-data-mock-clone-modal-select:focus {
        outline: none;
        border-color: #C1EC4A;
    }

    .survey-data-mock-clone-modal-help {
        display: block;
        font-size: 12px;
        color: #94A3B8;
        margin-top: 0.5rem;
        font-style: italic;
    }

    .survey-data-mock-clone-modal-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .survey-data-mock-clone-modal-btn:disabled:hover {
        background: #1E293B;
    }

    .survey-data-mock-clone-modal-footer {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
    }

    .survey-data-mock-clone-modal-btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        font-family: 'Poppins', sans-serif;
    }

    .survey-data-mock-clone-modal-btn-cancel {
        background: #F1F5F9;
        color: #64748B;
        border: 1px solid rgba(148, 163, 184, 0.4);
    }

    .survey-data-mock-clone-modal-btn-cancel:hover {
        background: #E2E8F0;
        border-color: rgba(148, 163, 184, 0.6);
    }

    .survey-data-mock-clone-modal-btn-clone {
        background: #1E293B;
        color: #FFFFFF;
    }

    .survey-data-mock-clone-modal-btn-clone:hover {
        background: #0F172A;
    }

    /* Rating Modal Styles */
    .survey-data-mock-rating-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 10001;
        align-items: center;
        justify-content: center;
    }

    .survey-data-mock-rating-modal.show {
        display: flex;
    }

    .survey-data-mock-rating-modal-content {
        background: #FFFFFF;
        border-radius: 8px;
        padding: 2rem;
        max-width: 450px;
        width: 90%;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }

    .survey-data-mock-rating-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .survey-data-mock-rating-modal-title {
        font-size: 20px;
        font-weight: 700;
        color: #1A202C;
        margin: 0;
    }

    .survey-data-mock-rating-modal-close {
        background: none;
        border: none;
        font-size: 24px;
        color: #94A3B8;
        cursor: pointer;
        padding: 0;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        transition: all 0.2s ease;
    }

    .survey-data-mock-rating-modal-close:hover {
        background: rgba(148, 163, 184, 0.1);
        color: #64748B;
    }

    .survey-data-mock-rating-options {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .survey-data-mock-rating-option {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 1.5rem 1rem;
        border: 2px solid rgba(148, 163, 184, 0.3);
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        background: #FFFFFF;
    }

    .survey-data-mock-rating-option:hover {
        border-color: #C1EC4A;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .survey-data-mock-rating-option.selected {
        border-color: #C1EC4A;
        background: rgba(193, 236, 74, 0.1);
    }

    .survey-data-mock-rating-option-badge {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        font-weight: 700;
        color: #FFFFFF;
        border: 2px solid #FFFFFF;
        margin-bottom: 0.75rem;
    }

    .survey-data-mock-rating-option-label {
        font-size: 14px;
        font-weight: 600;
        color: #1A202C;
    }

    .survey-data-mock-rating-option-description {
        font-size: 12px;
        color: #64748B;
        margin-top: 0.25rem;
        text-align: center;
    }

    .survey-data-mock-rating-option--1 .survey-data-mock-rating-option-badge {
        background: #10B981;
    }

    .survey-data-mock-rating-option--2 .survey-data-mock-rating-option-badge {
        background: #F59E0B;
    }

    .survey-data-mock-rating-option--3 .survey-data-mock-rating-option-badge {
        background: #EF4444;
    }

    .survey-data-mock-rating-option--ni .survey-data-mock-rating-option-badge {
        background: #94A3B8;
    }

    .survey-data-mock-rating-modal-footer {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
    }

    .survey-data-mock-rating-modal-btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        font-family: 'Poppins', sans-serif;
    }

    .survey-data-mock-rating-modal-btn-cancel {
        background: #F1F5F9;
        color: #64748B;
        border: 1px solid rgba(148, 163, 184, 0.4);
    }

    .survey-data-mock-rating-modal-btn-cancel:hover {
        background: #E2E8F0;
        border-color: rgba(148, 163, 184, 0.6);
    }

    .survey-data-mock-rating-modal-btn-save {
        background: #1E293B;
        color: #FFFFFF;
    }

    .survey-data-mock-rating-modal-btn-save:hover {
        background: #0F172A;
    }

    @media (max-width: 1024px) {
        .survey-data-mock-body {
            padding: 1.5rem 1rem;
        }

        .survey-data-mock-header-bar {
            padding: 0.75rem 1rem;
        }

        .survey-data-mock-button-group {
            gap: 0.375rem;
        }

        .survey-data-mock-button {
            padding: 0.4rem 0.75rem;
            font-size: 13px;
        }

        .survey-data-mock-form-grid {
            flex-direction: column;
        }
        
        .survey-data-mock-form-grid-divider {
            width: 100%;
            height: 4px;
            cursor: row-resize;
        }
        
        .survey-data-mock-form-grid-divider::before {
            left: 0;
            right: 0;
            top: -2px;
            bottom: -2px;
            cursor: row-resize;
        }
        
        .survey-data-mock-form-column-left,
        .survey-data-mock-form-column-right {
            flex: 0 0 auto !important;
            width: 100% !important;
            padding-right: 0 !important;
            padding-left: 0 !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Expand/Collapse Section Items
    $('.survey-data-mock-section-header[data-expandable="true"]').on('click', function() {
        const $sectionItem = $(this).closest('.survey-data-mock-section-item');
        const $details = $sectionItem.find('.survey-data-mock-section-details');
        const $titleBar = $sectionItem.find('.survey-data-mock-section-title-bar');
        
        $sectionItem.toggleClass('expanded');
        
        if ($sectionItem.hasClass('expanded')) {
            $details.slideDown(300);
            $titleBar.slideDown(300);
        } else {
            $details.slideUp(300);
            $titleBar.slideUp(300);
        }
    });

    // Collapse from title bar
    $(document).on('click', '.survey-data-mock-section-title-collapse', function(e) {
        e.stopPropagation();
        const $sectionItem = $(this).closest('.survey-data-mock-section-item');
        const $details = $sectionItem.find('.survey-data-mock-section-details');
        const $titleBar = $sectionItem.find('.survey-data-mock-section-title-bar');
        
        $sectionItem.removeClass('expanded');
        $details.slideUp(300);
        $titleBar.slideUp(300);
    });

    // Rating Badge Click Handler
    let currentRatingSectionId = null;
    let selectedRating = null;

    $(document).on('click', '.survey-data-mock-condition-badge', function(e) {
        e.stopPropagation();
        e.preventDefault();
        
        const $badge = $(this);
        const sectionId = $badge.data('section-id');
        const currentRating = $badge.data('current-rating') || 'ni';
        
        console.log('Rating badge clicked:', { sectionId, currentRating });
        
        if (!sectionId) {
            console.error('No section ID found on badge');
            return;
        }
        
        currentRatingSectionId = sectionId;
        selectedRating = currentRating;
        
        // Highlight current rating
        $('.survey-data-mock-rating-option').removeClass('selected');
        const ratingToSelect = currentRating === 'ni' ? 'ni' : currentRating.toString();
        $(`.survey-data-mock-rating-option[data-rating="${ratingToSelect}"]`).addClass('selected');
        
        // Show modal
        const $modal = $('#survey-data-mock-rating-modal');
        if ($modal.length === 0) {
            console.error('Rating modal not found in DOM');
            return;
        }
        $modal.addClass('show');
        console.log('Modal should be visible now');
    });

    // Rating Option Selection
    $(document).on('click', '.survey-data-mock-rating-option', function() {
        $('.survey-data-mock-rating-option').removeClass('selected');
        $(this).addClass('selected');
        selectedRating = $(this).data('rating');
    });

    // Close Rating Modal
    $('#rating-modal-close, #rating-modal-cancel').on('click', function() {
        $('#survey-data-mock-rating-modal').removeClass('show');
        currentRatingSectionId = null;
        selectedRating = null;
        $('.survey-data-mock-rating-option').removeClass('selected');
    });

    // Save Rating
    $('#rating-modal-save').on('click', function() {
        if (!currentRatingSectionId || !selectedRating) {
            return;
        }
        
        // Find all badges for this section (collapsed header and expanded title bar)
        const $badges = $(`.survey-data-mock-condition-badge[data-section-id="${currentRatingSectionId}"]`);
        
        // Update all badges
        const ratingText = selectedRating === 'ni' ? 'NI' : selectedRating;
        const ratingClass = selectedRating === 'ni' ? 'survey-data-mock-condition-badge--ni' : `survey-data-mock-condition-badge--${selectedRating}`;
        
        $badges.each(function() {
            const $badge = $(this);
            
            // Remove old rating class
            $badge.removeClass('survey-data-mock-condition-badge--1 survey-data-mock-condition-badge--2 survey-data-mock-condition-badge--3 survey-data-mock-condition-badge--ni');
            
            // Add new rating class
            $badge.addClass(ratingClass);
            
            // Update badge text and data
            $badge.text(ratingText);
            $badge.data('current-rating', selectedRating);
        });
        
        // Close modal
        $('#survey-data-mock-rating-modal').removeClass('show');
        currentRatingSectionId = null;
        selectedRating = null;
        $('.survey-data-mock-rating-option').removeClass('selected');
        
        // TODO: Save to backend via AJAX
        const updatedSectionId = currentRatingSectionId;
        const updatedRating = selectedRating;
        
        console.log('Rating updated:', {
            sectionId: updatedSectionId,
            rating: updatedRating
        });
    });

    // Close modal on backdrop click
    $('#survey-data-mock-rating-modal').on('click', function(e) {
        if ($(e.target).hasClass('survey-data-mock-rating-modal')) {
            $(this).removeClass('show');
            currentRatingSectionId = null;
            selectedRating = null;
            $('.survey-data-mock-rating-option').removeClass('selected');
        }
    });

    // Initialize Swiper carousels for button groups
    function initializeCarousels() {
        $('.survey-data-mock-button-group').each(function() {
            const $buttonGroup = $(this);
            
            // Skip if already initialized as Swiper
            if ($buttonGroup.hasClass('swiper-initialized')) {
                return;
            }
            
            // Get buttons (either direct children or from swiper-slide if already converted)
            let $buttons = $buttonGroup.find('.survey-data-mock-button');
            if ($buttons.length === 0) {
                $buttons = $buttonGroup.children('.survey-data-mock-button');
            }
            
            // Skip if no buttons
            if ($buttons.length === 0) {
                return;
            }
            
            // Check if content overflows
            const $fieldGroup = $buttonGroup.closest('.survey-data-mock-field-group');
            const availableWidth = $fieldGroup.width() || $buttonGroup.parent().width() || $buttonGroup.width();
            
            // Temporarily set to nowrap to measure actual content width
            const originalFlexWrap = $buttonGroup.css('flex-wrap');
            const originalOverflow = $buttonGroup.css('overflow');
            
            $buttonGroup.css({
                'flex-wrap': 'nowrap',
                'overflow': 'visible'
            });
            
            // Force reflow
            $buttonGroup[0].offsetHeight;
            $buttonGroup[0].scrollWidth;
            
            const contentWidth = $buttonGroup[0].scrollWidth;
            const needsCarousel = contentWidth > (availableWidth + 10);
            
            // Restore original styles
            $buttonGroup.css({
                'flex-wrap': originalFlexWrap,
                'overflow': originalOverflow
            });
            
            // Debug logging
            const groupLabel = $fieldGroup.find('label').text() || 'Unknown';
            console.log('Carousel check:', {
                group: groupLabel,
                contentWidth: contentWidth,
                availableWidth: availableWidth,
                needsCarousel: needsCarousel,
                buttonCount: $buttons.length
            });
            
            if (needsCarousel) {
                // If already has swiper structure, destroy it first
                if ($buttonGroup[0].swiper) {
                    $buttonGroup[0].swiper.destroy(true, true);
                }
                
                // Get all buttons (detach to preserve them)
                const buttons = [];
                $buttons.each(function() {
                    buttons.push($(this).detach());
                });
                
                // Clear the button group
                $buttonGroup.empty();
                
                // Add swiper class
                $buttonGroup.addClass('swiper');
                
                // Create swiper wrapper
                const $wrapper = $('<div class="swiper-wrapper"></div>');
                buttons.forEach(function($btn) {
                    const $slide = $('<div class="swiper-slide"></div>');
                    $slide.append($btn);
                    $wrapper.append($slide);
                });
                $buttonGroup.append($wrapper);
                
                // Add navigation buttons
                const $prevBtn = $('<div class="swiper-button-prev"></div>');
                const $nextBtn = $('<div class="swiper-button-next"></div>');
                $buttonGroup.append($prevBtn);
                $buttonGroup.append($nextBtn);
                
                // Initialize Swiper
                try {
                    const swiper = new Swiper($buttonGroup[0], {
                        slidesPerView: 'auto',
                        spaceBetween: 6,
                        freeMode: true,
                        navigation: {
                            nextEl: $nextBtn[0],
                            prevEl: $prevBtn[0],
                        },
                        watchOverflow: true,
                        resistance: true,
                        resistanceRatio: 0.85,
                        slidesOffsetBefore: 0,
                        slidesOffsetAfter: 0,
                    });
                    
                    // Add continuous smooth scrolling on button hold
                    let scrollInterval = null;
                    let isScrolling = false;
                    let scrollDirection = null;
                    
                    // Function to scroll smoothly
                    function smoothScroll() {
                        if (!isScrolling) return;
                        
                        const scrollAmount = 5; // pixels per scroll
                        const currentTranslate = swiper.getTranslate();
                        
                        if (scrollDirection === 'prev') {
                            swiper.setTranslate(currentTranslate - scrollAmount);
                        } else if (scrollDirection === 'next') {
                            swiper.setTranslate(currentTranslate + scrollAmount);
                        }
                        
                        // Update navigation state
                        swiper.navigation.update();
                    }
                    
                    // Left button - continuous smooth scroll
                    $prevBtn.on('mousedown touchstart', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        isScrolling = true;
                        scrollDirection = 'prev';
                        
                        // Immediate scroll
                        swiper.slidePrev();
                        
                        // Continuous smooth scroll
                        scrollInterval = setInterval(smoothScroll, 16); // ~60fps smooth scrolling
                    });
                    
                    // Right button - continuous smooth scroll
                    $nextBtn.on('mousedown touchstart', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        isScrolling = true;
                        scrollDirection = 'next';
                        
                        // Immediate scroll
                        swiper.slideNext();
                        
                        // Continuous smooth scroll
                        scrollInterval = setInterval(smoothScroll, 16); // ~60fps smooth scrolling
                    });
                    
                    // Stop scrolling on mouse up/leave/touch end
                    $(document).on('mouseup touchend mouseleave', function() {
                        if (scrollInterval) {
                            clearInterval(scrollInterval);
                            scrollInterval = null;
                            isScrolling = false;
                            scrollDirection = null;
                        }
                    });
                    
                    // Also handle single click for quick navigation
                    $prevBtn.on('click', function(e) {
                        if (!isScrolling) {
                            swiper.slidePrev();
                        }
                    });
                    
                    $nextBtn.on('click', function(e) {
                        if (!isScrolling) {
                            swiper.slideNext();
                        }
                    });
                    
                    // Update navigation visibility on init and slide change
                    swiper.on('init', function() {
                        swiper.navigation.update();
                    });
                    
                    swiper.on('slideChange', function() {
                        swiper.navigation.update();
                    });
                    
                    // Force update after a short delay to ensure proper rendering
                    setTimeout(function() {
                        swiper.update();
                        swiper.navigation.update();
                    }, 100);
                    
                    console.log('Swiper initialized for:', groupLabel);
                } catch (error) {
                    console.error('Swiper initialization error:', error, groupLabel);
                }
            }
        });
    }
    
    // Draggable Divider Functionality
    function initializeDividers() {
        $('.survey-data-mock-form-grid-divider').each(function() {
            const $divider = $(this);
            const $grid = $divider.closest('.survey-data-mock-form-grid');
            const $leftColumn = $grid.find('[data-column="left"]');
            const $rightColumn = $grid.find('[data-column="right"]');
            
            if ($leftColumn.length === 0 || $rightColumn.length === 0) {
                return;
            }
            
            let isDragging = false;
            let startX = 0;
            let startLeftWidth = 0;
            let startRightWidth = 0;
            let carouselUpdateTimeout = null; // Debounce for carousel updates during drag
            
            $divider.on('mousedown', function(e) {
                isDragging = true;
                $divider.addClass('dragging');
                $('body').css('cursor', 'col-resize');
                $('body').css('user-select', 'none');
                
                startX = e.pageX;
                startLeftWidth = $leftColumn[0].offsetWidth;
                startRightWidth = $rightColumn[0].offsetWidth;
                
                e.preventDefault();
            });
            
            $(document).on('mousemove', function(e) {
                if (!isDragging) return;
                
                const diffX = e.pageX - startX;
                const gridWidth = $grid[0].offsetWidth;
                const dividerWidth = $divider[0].offsetWidth;
                const availableWidth = gridWidth - dividerWidth;
                
                // Calculate new widths (min 20%, max 80% for each column)
                const minWidth = availableWidth * 0.2;
                const maxWidth = availableWidth * 0.8;
                
                let newLeftWidth = startLeftWidth + diffX;
                let newRightWidth = startRightWidth - diffX;
                
                // Constrain to min/max
                if (newLeftWidth < minWidth) {
                    newLeftWidth = minWidth;
                    newRightWidth = availableWidth - minWidth;
                } else if (newLeftWidth > maxWidth) {
                    newLeftWidth = maxWidth;
                    newRightWidth = availableWidth - maxWidth;
                }
                
                // Apply new widths
                $leftColumn.css('flex', `0 0 ${newLeftWidth}px`);
                $rightColumn.css('flex', `0 0 ${newRightWidth}px`);
                
                // Update carousels after a short delay (debounced for performance)
                if (carouselUpdateTimeout) {
                    clearTimeout(carouselUpdateTimeout);
                }
                carouselUpdateTimeout = setTimeout(function() {
                    if (isSwiperAvailable()) {
                        initializeCarousels();
                    }
                }, 100);
            });
            
            $(document).on('mouseup', function() {
                if (isDragging) {
                    isDragging = false;
                    $divider.removeClass('dragging');
                    $('body').css('cursor', '');
                    $('body').css('user-select', '');
                    
                    // Final carousel update after drag completes
                    if (carouselUpdateTimeout) {
                        clearTimeout(carouselUpdateTimeout);
                    }
                    setTimeout(function() {
                        if (isSwiperAvailable()) {
                            initializeCarousels();
                        }
                    }, 150);
                }
            });
        });
    }

    // Check if Swiper is available
    function isSwiperAvailable() {
        return typeof Swiper !== 'undefined';
    }
    
    // Initialize carousels and dividers on page load
    function initOnReady() {
        if (isSwiperAvailable()) {
            setTimeout(function() {
                initializeCarousels();
                initializeDividers();
            }, 200);
        } else {
            console.warn('Swiper not loaded yet, retrying...');
            setTimeout(initOnReady, 100);
        }
    }
    
    // Start initialization
    initOnReady();
    
    // Also initialize after full page load
    $(window).on('load', function() {
        if (isSwiperAvailable()) {
            setTimeout(function() {
                initializeCarousels();
                initializeDividers();
            }, 200);
        }
    });
    
    // Reinitialize when sections expand (for dynamically added content)
    $(document).on('click', '.survey-data-mock-section-header[data-expandable="true"]', function() {
        setTimeout(function() {
            if (isSwiperAvailable()) {
                initializeCarousels();
                initializeDividers();
            }
        }, 500); // After slide animation
    });

    // Reinitialize on window resize
    let resizeTimeout;
    $(window).on('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
            initializeCarousels();
            initializeDividers();
        }, 250);
    });


    // Button Selection (Single selection within each group) - Using event delegation for dynamically added sections
    $(document).on('click', '.survey-data-mock-button', function() {
        const $button = $(this);
        const group = $button.data('group');
        const isMultiple = $button.data('multiple') === true;
        const $group = $button.closest('.survey-data-mock-field-group').find(`[data-group="${group}"]`);
        const $sectionItem = $button.closest('.survey-data-mock-section-item');
        const buttonValue = $button.data('value');
        
        if (isMultiple) {
            // Special handling for defects group
            if (group === 'defects') {
                if (buttonValue === 'None') {
                    // If "None" is clicked, deselect all other defects and toggle "None"
                    $group.not($button).removeClass('active');
                    $button.toggleClass('active');
                } else {
                    // If any other defect is clicked, deselect "None" and toggle the clicked button
                    $group.filter('[data-value="None"]').removeClass('active');
                    $button.toggleClass('active');
                }
            } else {
                // Toggle for other multiple selection groups
                $button.toggleClass('active');
            }
        } else {
            // Single selection for other groups
            $group.removeClass('active');
            $button.addClass('active');
        }
        
        // Store selection
        const sectionId = $sectionItem.data('section-id');
        console.log('Section', sectionId, 'Group', group, 'Value', buttonValue);
    });

    // Initialize button states from mock data
    $('.survey-data-mock-section-item').each(function() {
        const $sectionItem = $(this);
        const sectionId = $sectionItem.data('section-id');
        const $details = $sectionItem.find('.survey-data-mock-section-details');
        
        // Initialize button states based on data attributes from PHP
        const selectedSection = $details.data('selected-section');
        const selectedLocation = $details.data('selected-location');
        const selectedStructure = $details.data('selected-structure');
        const selectedMaterial = $details.data('selected-material');
        const selectedDefects = $details.data('selected-defects') || [];
        const selectedRemainingLife = $details.data('selected-remaining-life');
        
        // Set active buttons (single selection groups)
        if (selectedSection) {
            $details.find(`[data-group="section"][data-value="${selectedSection}"]`).addClass('active');
        }
        if (selectedLocation) {
            $details.find(`[data-group="location"][data-value="${selectedLocation}"]`).addClass('active');
        }
        if (selectedStructure) {
            $details.find(`[data-group="structure"][data-value="${selectedStructure}"]`).addClass('active');
        }
        if (selectedMaterial) {
            $details.find(`[data-group="material"][data-value="${selectedMaterial}"]`).addClass('active');
        }
        if (selectedRemainingLife) {
            $details.find(`[data-group="remaining_life"][data-value="${selectedRemainingLife}"]`).addClass('active');
        }
        
        // Set active buttons (multiple selection - defects)
        if (selectedDefects && selectedDefects.length > 0) {
            selectedDefects.forEach(function(defect) {
                $details.find(`[data-group="defects"][data-value="${defect}"]`).addClass('active');
            });
        }
    });

    // Add Cost Button
    $('.survey-data-mock-add-cost-btn').on('click', function(e) {
        e.stopPropagation();
        alert('Add Cost functionality - to be implemented');
        // TODO: Open modal/form to add new cost entry
    });

    // Microphone Button
    $('.survey-data-mock-mic-btn').on('click', function(e) {
        e.stopPropagation();
        alert('Voice input functionality - to be implemented');
        // TODO: Implement voice input
    });

    // Image Upload Area
    $('.survey-data-mock-images-upload').on('click', function() {
        alert('Image upload functionality - to be implemented');
        // TODO: Trigger file input or drag-drop handler
    });

    // Action Buttons
    $('.survey-data-mock-action-delete').on('click', function(e) {
        e.stopPropagation();
        if (confirm('Are you sure you want to delete this section?')) {
            alert('Delete functionality - to be implemented');
            // TODO: Implement delete
        }
    });

    $('.survey-data-mock-action-clone').on('click', function(e) {
        e.stopPropagation();
        alert('Save & Clone functionality - to be implemented');
        // TODO: Implement save and clone
    });

    $('.survey-data-mock-action-save').on('click', function(e) {
        e.stopPropagation();
        alert('Save functionality - to be implemented');
        // TODO: Implement save
    });

    // Clone Modal
    const cloneModal = $(`
        <div class="survey-data-mock-clone-modal" id="survey-data-mock-clone-modal">
            <div class="survey-data-mock-clone-modal-content">
                <div class="survey-data-mock-clone-modal-header">
                    <h3 class="survey-data-mock-clone-modal-title">Clone Section</h3>
                    <button type="button" class="survey-data-mock-clone-modal-close" id="survey-data-mock-clone-modal-close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="survey-data-mock-clone-modal-body">
                    <div class="survey-data-mock-clone-modal-field">
                        <label class="survey-data-mock-clone-modal-label">Select Target Section</label>
                        <div class="survey-data-mock-clone-section-buttons" id="clone-section-buttons">
                            <!-- Buttons will be dynamically generated -->
                        </div>
                    </div>
                </div>
                <div class="survey-data-mock-clone-modal-footer">
                    <button type="button" class="survey-data-mock-clone-modal-btn survey-data-mock-clone-modal-btn-cancel" id="clone-modal-cancel">Cancel</button>
                    <button type="button" class="survey-data-mock-clone-modal-btn survey-data-mock-clone-modal-btn-clone" id="clone-modal-confirm" disabled>Clone</button>
                </div>
            </div>
        </div>
    `);

    $('body').append(cloneModal);

    let currentCloneSectionId = null;
    let currentCloneData = null;
    let currentCloneCategory = null;
    let selectedCloneSection = null;

    // Open Clone Modal
    $('.survey-data-mock-action-clone').on('click', function(e) {
        e.stopPropagation();
        const $button = $(this);
        const sectionId = $button.data('section-id');
        const sectionName = $button.data('section-name');
        const $sectionItem = $button.closest('.survey-data-mock-section-item');
        const $details = $sectionItem.find('.survey-data-mock-section-details');
        const $categorySection = $sectionItem.closest('.survey-data-mock-category');
        const categoryName = $categorySection.find('.survey-data-mock-category-title').text().trim();
        
        // Collect all form data
        const formData = {
            section: $details.find('[data-group="section"].active').data('value') || '',
            location: $details.find('[data-group="location"].active').data('value') || '',
            structure: $details.find('[data-group="structure"].active').data('value') || '',
            material: $details.find('[data-group="material"].active').data('value') || '',
            defects: $details.find('[data-group="defects"].active').map(function() {
                return $(this).data('value');
            }).get(),
            remainingLife: $details.find('[data-group="remaining_life"].active').data('value') || '',
            notes: $details.find('.survey-data-mock-notes-input').val() || '',
            costs: [],
            photos: [] // Initialize photos array
        };
        
        // Collect photos count (can be enhanced to get actual photos)
        const photosCount = $details.find('.survey-data-mock-images-upload img').length || 0;
        formData.photos = Array(photosCount).fill(null); // Create array with photos count

        // Collect costs
        $details.find('.survey-data-mock-costs-table tbody tr').each(function() {
            const $row = $(this);
            const category = $row.find('td:eq(0)').text().trim();
            const description = $row.find('td:eq(1)').text().trim();
            const due = $row.find('td:eq(2)').text().trim();
            const cost = $row.find('td:eq(3)').text().trim();
            
            if (category && category !== 'No costs added') {
                formData.costs.push({
                    category: category,
                    description: description,
                    due: due,
                    cost: cost
                });
            }
        });

        currentCloneSectionId = sectionId;
        currentCloneData = formData;
        currentCloneCategory = categoryName;
        selectedCloneSection = null;

        // Get available sections based on category - match section-item.blade.php logic
        let allSections = [];
        const currentSelectedSection = formData.section;
        
        if (categoryName === 'Exterior') {
            // For Exterior, use hardcoded list (matches section-item.blade.php line 56)
            allSections = ['Main Roof', 'Side Extension', 'Rear Extension', 'Dormer', 'Lean-to'];
        } else {
            // For other categories, get from button group (matches section-item.blade.php line 57)
            $details.find('[data-group="section"]').each(function() {
                const sectionValue = $(this).data('value');
                if (sectionValue) {
                    allSections.push(sectionValue);
                }
            });
        }
        
        // Get all already selected sections in the same sub-category to disable them
        const $subCategory = $sectionItem.closest('.survey-data-mock-sub-category');
        const alreadySelectedSections = [];
        
        $subCategory.find('.survey-data-mock-section-item').each(function() {
            const $item = $(this);
            const $itemDetails = $item.find('.survey-data-mock-section-details');
            if ($itemDetails.length > 0) {
                const selectedSection = $itemDetails.data('selected-section') || 
                                      $itemDetails.find('[data-group="section"].active').data('value') || '';
                if (selectedSection && !alreadySelectedSections.includes(selectedSection)) {
                    alreadySelectedSections.push(selectedSection);
                }
            }
        });

        // Generate section buttons in modal
        const $cloneButtons = $('#clone-section-buttons');
        $cloneButtons.empty();
        
        // Filter out already selected sections
        const availableSections = allSections.filter(section => !alreadySelectedSections.includes(section));
        
        if (availableSections.length === 0) {
            $cloneButtons.html('<p class="survey-data-mock-clone-modal-help" style="color: #94A3B8;">All sections in this sub-category have been used</p>');
            $('#clone-modal-confirm').prop('disabled', true);
        } else {
            allSections.forEach(function(section) {
                const isDisabled = alreadySelectedSections.includes(section);
                const $btn = $('<button>')
                    .addClass('survey-data-mock-clone-section-btn')
                    .attr('type', 'button')
                    .data('section', section)
                    .text(section);
                
                if (isDisabled) {
                    $btn.addClass('disabled')
                        .prop('disabled', true)
                        .attr('title', 'This section is already used in this sub-category');
                }
                
                $cloneButtons.append($btn);
            });
        }
        
        // Show modal
        $('#survey-data-mock-clone-modal').addClass('show');
        
        // Enable/disable clone button based on selection
        updateCloneButtonState();
    });

    // Handle section selection in clone modal
    $(document).on('click', '.survey-data-mock-clone-section-btn', function() {
        const $btn = $(this);
        // Don't allow selection of disabled buttons
        if ($btn.hasClass('disabled') || $btn.prop('disabled')) {
            return;
        }
        
        $('.survey-data-mock-clone-section-btn').removeClass('active');
        $btn.addClass('active');
        selectedCloneSection = $btn.data('section');
        updateCloneButtonState();
    });

    function updateCloneButtonState() {
        if (selectedCloneSection) {
            $('#clone-modal-confirm').prop('disabled', false);
        } else {
            $('#clone-modal-confirm').prop('disabled', true);
        }
    }

    // Close Clone Modal
    $('#survey-data-mock-clone-modal-close, #clone-modal-cancel').on('click', function() {
        $('#survey-data-mock-clone-modal').removeClass('show');
        currentCloneSectionId = null;
        currentCloneData = null;
        currentCloneCategory = null;
        selectedCloneSection = null;
        $('.survey-data-mock-clone-section-btn').removeClass('active');
        $('#clone-modal-confirm').prop('disabled', true);
    });

    // Close modal on background click
    $('#survey-data-mock-clone-modal').on('click', function(e) {
        if ($(e.target).hasClass('survey-data-mock-clone-modal')) {
            $(this).removeClass('show');
            currentCloneSectionId = null;
            currentCloneData = null;
            currentCloneCategory = null;
            selectedCloneSection = null;
            $('.survey-data-mock-clone-section-btn').removeClass('active');
            $('#clone-modal-confirm').prop('disabled', true);
        }
    });

    // Confirm Clone
    $('#clone-modal-confirm').on('click', function() {
        if (!selectedCloneSection) {
            alert('Please select a target section');
            return;
        }

        const $sourceItem = $(`.survey-data-mock-section-item[data-section-id="${currentCloneSectionId}"]`);
        const $categorySection = $sourceItem.closest('.survey-data-mock-category');
        const $subCategoryContainer = $sourceItem.closest('.survey-data-mock-sub-category');
        
        // Get survey ID from page
        const surveyId = $('.survey-data-mock-content').data('survey-id');
        if (!surveyId) {
            alert('Error: Survey ID not found');
            return;
        }
        
        // Get original section name from the source item
        const originalSectionName = $sourceItem.find('.survey-data-mock-section-name').text().trim();
        
        // Get category and sub-category names
        const categoryName = $categorySection.find('.survey-data-mock-category-title').text().trim();
        const subCategoryName = $subCategoryContainer.find('.survey-data-mock-sub-category-title').text().trim();
        
        // Get condition rating from source item
        const $sourceBadge = $sourceItem.find('.survey-data-mock-condition-badge');
        let conditionRating = 'ni'; // Default
        if ($sourceBadge.hasClass('survey-data-mock-condition-badge--3')) {
            conditionRating = '3';
        } else if ($sourceBadge.hasClass('survey-data-mock-condition-badge--2')) {
            conditionRating = '2';
        } else if ($sourceBadge.hasClass('survey-data-mock-condition-badge--1')) {
            conditionRating = '1';
        }
        
        // Prepare form data for AJAX request (convert camelCase to snake_case)
        const formData = {
            section: currentCloneData.section || '',
            location: currentCloneData.location || '',
            structure: currentCloneData.structure || '',
            material: currentCloneData.material || '',
            defects: currentCloneData.defects || [],
            remaining_life: currentCloneData.remainingLife || '',
            costs: currentCloneData.costs || [],
            notes: currentCloneData.notes || '',
            photos: currentCloneData.photos || [],
            condition_rating: conditionRating
        };
        
        // Show loading state
        const $confirmBtn = $('#clone-modal-confirm');
        const originalBtnText = $confirmBtn.text();
        $confirmBtn.prop('disabled', true).text('Cloning...');
        
        // Send AJAX request to clone endpoint
        $.ajax({
            url: `/surveyor/surveys/${surveyId}/clone-section-item`,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                source_section_id: currentCloneSectionId,
                source_section_name: originalSectionName,
                selected_section: selectedCloneSection,
                category_name: categoryName,
                sub_category_name: subCategoryName,
                form_data: formData
            },
            success: function(response) {
                if (response.success && response.html) {
                    // Insert cloned section immediately after the parent section
                    $sourceItem.after(response.html);
                    
                    // Get the new section item
                    const $newSectionItem = $(`.survey-data-mock-section-item[data-section-id="${response.section_id}"]`);
                    
                    // Initialize button states from form data
                    initializeSectionButtons($newSectionItem, formData);
                    
                    // Initialize event handlers for the new section
                    initializeSectionHandlers($newSectionItem);
                    
                    // Initialize carousels and dividers in the new section
                    setTimeout(function() {
                        initializeCarousels();
                        initializeDividers();
                    }, 100);
                    
                    // Re-attach clone handler (since it's dynamically created)
                    attachCloneHandler($newSectionItem);
                    
                    // Close modal
                    $('#survey-data-mock-clone-modal').removeClass('show');
                    currentCloneSectionId = null;
                    currentCloneData = null;
                    currentCloneCategory = null;
                    selectedCloneSection = null;
                    $('.survey-data-mock-clone-section-btn').removeClass('active');
                    $confirmBtn.prop('disabled', false).text(originalBtnText);
                    
                    // Scroll to new section
                    $('html, body').animate({
                        scrollTop: $newSectionItem.offset().top - 100
                    }, 500);
                } else {
                    alert('Error: Failed to clone section');
                    $confirmBtn.prop('disabled', false).text(originalBtnText);
                }
            },
            error: function(xhr, status, error) {
                console.error('Clone error:', error);
                alert('Error cloning section: ' + (xhr.responseJSON?.error || error));
                $confirmBtn.prop('disabled', false).text(originalBtnText);
            }
        });
    });
    
    // Helper function to attach clone handler to a section item
    function attachCloneHandler($sectionItem) {
        $sectionItem.find('.survey-data-mock-action-clone').off('click').on('click', function(e) {
            e.stopPropagation();
            const $button = $(this);
            const sectionId = $button.data('section-id');
            const sectionName = $button.data('section-name');
            const $item = $button.closest('.survey-data-mock-section-item');
            const $details = $item.find('.survey-data-mock-section-details');
            const $categorySection = $item.closest('.survey-data-mock-category');
            const categoryName = $categorySection.find('.survey-data-mock-category-title').text().trim();
            
            // Collect all form data
            const formData = {
                section: $details.find('[data-group="section"].active').data('value') || '',
                location: $details.find('[data-group="location"].active').data('value') || '',
                structure: $details.find('[data-group="structure"].active').data('value') || '',
                material: $details.find('[data-group="material"].active').data('value') || '',
                defects: $details.find('[data-group="defects"].active').map(function() {
                    return $(this).data('value');
                }).get(),
                remainingLife: $details.find('[data-group="remaining_life"].active').data('value') || '',
                notes: $details.find('.survey-data-mock-notes-input').val() || '',
                costs: [],
                photos: [] // Initialize photos array
            };
            
            // Collect photos count (can be enhanced to get actual photos)
            const photosCount = $details.find('.survey-data-mock-images-upload img').length || 0;
            formData.photos = Array(photosCount).fill(null); // Create array with photos count

            // Collect costs
            $details.find('.survey-data-mock-costs-table tbody tr').each(function() {
                const $row = $(this);
                const category = $row.find('td:eq(0)').text().trim();
                const description = $row.find('td:eq(1)').text().trim();
                const due = $row.find('td:eq(2)').text().trim();
                const cost = $row.find('td:eq(3)').text().trim();
                
                if (category && category !== 'No costs added') {
                    formData.costs.push({
                        category: category,
                        description: description,
                        due: due,
                        cost: cost
                    });
                }
            });

            currentCloneSectionId = sectionId;
            currentCloneData = formData;
            currentCloneCategory = categoryName;
            selectedCloneSection = null;

            // Get available sections based on category - match section-item.blade.php logic
            let allSections = [];
            const currentSelectedSection = formData.section;
            
            if (categoryName === 'Exterior') {
                // For Exterior, use hardcoded list (matches section-item.blade.php line 56)
                allSections = ['Main Roof', 'Side Extension', 'Rear Extension', 'Dormer', 'Lean-to'];
            } else {
                // For other categories, get from button group (matches section-item.blade.php line 57)
                $details.find('[data-group="section"]').each(function() {
                    const sectionValue = $(this).data('value');
                    if (sectionValue) {
                        allSections.push(sectionValue);
                    }
                });
            }
            
            // Get all already selected sections in the same sub-category to disable them
            const $subCategory = $item.closest('.survey-data-mock-sub-category');
            const alreadySelectedSections = [];
            
            $subCategory.find('.survey-data-mock-section-item').each(function() {
                const $subItem = $(this);
                const $subItemDetails = $subItem.find('.survey-data-mock-section-details');
                if ($subItemDetails.length > 0) {
                    const selectedSection = $subItemDetails.data('selected-section') || 
                                          $subItemDetails.find('[data-group="section"].active').data('value') || '';
                    if (selectedSection && !alreadySelectedSections.includes(selectedSection)) {
                        alreadySelectedSections.push(selectedSection);
                    }
                }
            });

            // Generate section buttons in modal
            const $cloneButtons = $('#clone-section-buttons');
            $cloneButtons.empty();
            
            // Filter out already selected sections
            const availableSections = allSections.filter(section => !alreadySelectedSections.includes(section));
            
            if (availableSections.length === 0) {
                $cloneButtons.html('<p class="survey-data-mock-clone-modal-help" style="color: #94A3B8;">All sections in this sub-category have been used</p>');
                $('#clone-modal-confirm').prop('disabled', true);
            } else {
                allSections.forEach(function(section) {
                    const isDisabled = alreadySelectedSections.includes(section);
                    const $btn = $('<button>')
                        .addClass('survey-data-mock-clone-section-btn')
                        .attr('type', 'button')
                        .data('section', section)
                        .text(section);
                    
                    if (isDisabled) {
                        $btn.addClass('disabled').prop('disabled', true);
                    }
                    
                    $cloneButtons.append($btn);
                });
            }
            
            // Show modal
            $('#survey-data-mock-clone-modal').addClass('show');
            
            // Enable/disable clone button based on selection
            updateCloneButtonState();
        });
    }
    
    /**
     * @deprecated This function is deprecated in favor of the AJAX endpoint cloneSectionItem.
     * The AJAX endpoint ensures exact match with section-item.blade.php partial.
     * This function is kept for backward compatibility but should not be used for new clones.
     */
    function buildSectionItemHTML(sectionId, sectionName, completion, total, conditionRating, formData, categoryName, isCloned = false) {
        const isExterior = categoryName === 'Exterior';
        const completionColor = completion == total ? '#10B981' : (completion > 0 ? '#F59E0B' : '#EF4444');
        
        // Build section options
        let sectionOptions = [];
        if (isExterior) {
            sectionOptions = ['Main Roof', 'Side Extension', 'Rear Extension', 'Dormer', 'Lean-to'];
        } else {
            sectionOptions = [sectionName];
        }
        
        // Build structure options
        const structureOptions = isExterior 
            ? ['Pitched', 'Flat', 'Inverted pitched', 'Mono-Pitch', 'Curved']
            : ['Standard', 'Flat', 'Pitched', 'Suspended', 'Solid'];
        
        // Build material options
        const materialOptions = isExterior 
            ? ['Double Glazed Aluminium', 'Polycarbonate', 'Slate', 'Asphalt', 'Concrete Interlocking', 'Fibre Slate', 'Felt']
            : ['Plasterboard', 'Plaster', 'Timber', 'Concrete', 'Mixed'];
        
        // Build costs table rows
        let costsRows = '';
        if (formData.costs && formData.costs.length > 0) {
            costsRows = formData.costs.map(cost => `
                <tr>
                    <td>${cost.category || ''}</td>
                    <td>${cost.description || ''}</td>
                    <td>${cost.due || ''}</td>
                    <td>${cost.cost || ''}</td>
                </tr>
            `).join('');
        } else {
            costsRows = '<tr><td colspan="4" class="survey-data-mock-no-costs">No costs added</td></tr>';
        }
        
        const clonedClass = isCloned ? ' survey-data-mock-section-item-cloned' : '';
        const html = `
            <div class="survey-data-mock-section-item${clonedClass}" data-section-id="${sectionId}" data-cloned="${isCloned ? 'true' : 'false'}">
                <div class="survey-data-mock-section-header" data-expandable="true">
                    <div class="survey-data-mock-section-name">
                        ${sectionName}
                    </div>
                    <div class="survey-data-mock-section-status">
                        <span class="survey-data-mock-status-info">
                            <i class="fas fa-camera survey-data-mock-status-icon"></i>
                            <span class="survey-data-mock-status-text">${formData.photos ? formData.photos.length : 0}</span>
                            <span class="survey-data-mock-status-separator">|</span>
                            <i class="fas fa-sticky-note survey-data-mock-status-icon"></i>
                            <span class="survey-data-mock-status-text">${completion}/${total}</span>
                        </span>
                        <span class="survey-data-mock-condition-badge survey-data-mock-condition-badge--${conditionRating || 'ni'}" 
                              data-section-id="${sectionId}"
                              data-current-rating="${conditionRating || 'ni'}">
                            ${conditionRating || 'NI'}
                        </span>
                        <i class="fas fa-chevron-down survey-data-mock-expand-icon"></i>
                    </div>
                </div>

                <!-- Section Title Header (visible when expanded) -->
                <div class="survey-data-mock-section-title-bar" style="display: none;">
                    <h3 class="survey-data-mock-section-title-text">${sectionName}</h3>
                    <div class="d-flex align-items-center" style="gap: 10px;">
                        <span class="survey-data-mock-condition-badge survey-data-mock-condition-badge--${conditionRating || 'ni'}" 
                              data-section-id="${sectionId}"
                              data-current-rating="${conditionRating || 'ni'}">
                            ${conditionRating || 'NI'}
                        </span>
                        <i class="fas fa-chevron-up survey-data-mock-section-title-collapse"></i>
                    </div>
                </div>

                <!-- Expanded Form Content -->
                <div class="survey-data-mock-section-details" 
                     style="display: none;"
                     data-selected-section="${formData.section || ''}"
                     data-selected-location="${formData.location || ''}"
                     data-selected-structure="${formData.structure || ''}"
                     data-selected-material="${formData.material || ''}"
                     data-selected-defects='${JSON.stringify(formData.defects || [])}'
                     data-selected-remaining-life="${formData.remainingLife || ''}">
                    <div class="survey-data-mock-section-details-content">
                        <div class="survey-data-mock-form-grid">
                            <!-- Left Column -->
                            <div class="survey-data-mock-form-column survey-data-mock-form-column-left" data-column="left">
                                <!-- Section Buttons -->
                                <div class="survey-data-mock-field-group">
                                    <label class="survey-data-mock-field-label">Section</label>
                                    <div class="survey-data-mock-button-group">
                                        ${sectionOptions.map(opt => `
                                            <button type="button" class="survey-data-mock-button" data-value="${opt}" data-group="section">${opt}</button>
                                        `).join('')}
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
                                <div class="survey-data-mock-field-group">
                                    <label class="survey-data-mock-field-label">Structure</label>
                                    <div class="survey-data-mock-button-group">
                                        ${structureOptions.map(opt => `
                                            <button type="button" class="survey-data-mock-button" data-value="${opt}" data-group="structure">${opt}</button>
                                        `).join('')}
                                    </div>
                                </div>

                                <!-- Material Buttons -->
                                <div class="survey-data-mock-field-group">
                                    <label class="survey-data-mock-field-label">Material</label>
                                    <div class="survey-data-mock-button-group">
                                        ${materialOptions.map(opt => `
                                            <button type="button" class="survey-data-mock-button" data-value="${opt}" data-group="material">${opt}</button>
                                        `).join('')}
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
                                            ${costsRows}
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Additional Notes -->
                                <div class="survey-data-mock-field-group">
                                    <label class="survey-data-mock-field-label">Additional Notes</label>
                                    <div class="survey-data-mock-notes-wrapper">
                                        <textarea class="survey-data-mock-notes-input" rows="4" placeholder="Enter additional notes...">${formData.notes || ''}</textarea>
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
                            <button type="button" class="survey-data-mock-action-btn survey-data-mock-action-delete" data-section-id="${sectionId}">Delete</button>
                            <button type="button" class="survey-data-mock-action-btn survey-data-mock-action-clone" data-section-id="${sectionId}" data-section-name="${sectionName}">Save & Clone</button>
                            <button type="button" class="survey-data-mock-action-btn survey-data-mock-action-save" data-section-id="${sectionId}">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        return html;
    }
    
    // Helper function to initialize button states for a section
    function initializeSectionButtons($sectionItem, formData) {
        const $details = $sectionItem.find('.survey-data-mock-section-details');
        
        // Set active buttons (single selection groups)
        if (formData.section) {
            $details.find(`[data-group="section"][data-value="${formData.section}"]`).addClass('active');
        }
        if (formData.location) {
            $details.find(`[data-group="location"][data-value="${formData.location}"]`).addClass('active');
        }
        if (formData.structure) {
            $details.find(`[data-group="structure"][data-value="${formData.structure}"]`).addClass('active');
        }
        if (formData.material) {
            $details.find(`[data-group="material"][data-value="${formData.material}"]`).addClass('active');
        }
        if (formData.remainingLife) {
            $details.find(`[data-group="remaining_life"][data-value="${formData.remainingLife}"]`).addClass('active');
        }
        
        // Set active buttons (multiple selection - defects)
        if (formData.defects && formData.defects.length > 0) {
            formData.defects.forEach(function(defect) {
                $details.find(`[data-group="defects"][data-value="${defect}"]`).addClass('active');
            });
        }
    }
    
    // Helper function to initialize event handlers for a section
    function initializeSectionHandlers($sectionItem) {
        // Expand/Collapse handler
        $sectionItem.find('.survey-data-mock-section-header[data-expandable="true"]').off('click').on('click', function() {
            const $item = $(this).closest('.survey-data-mock-section-item');
            const $details = $item.find('.survey-data-mock-section-details');
            const $titleBar = $item.find('.survey-data-mock-section-title-bar');
            
            $item.toggleClass('expanded');
            
            if ($item.hasClass('expanded')) {
                $details.slideDown(300);
                $titleBar.slideDown(300);
            } else {
                $details.slideUp(300);
                $titleBar.slideUp(300);
            }
        });
        
        // Delete handler
        $sectionItem.find('.survey-data-mock-action-delete').off('click').on('click', function(e) {
            e.stopPropagation();
            const $item = $(this).closest('.survey-data-mock-section-item');
            if (confirm('Are you sure you want to delete this section?')) {
                $item.fadeOut(300, function() {
                    $(this).remove();
                });
            }
        });
        
        // Save handler
        $sectionItem.find('.survey-data-mock-action-save').off('click').on('click', function(e) {
            e.stopPropagation();
            alert('Save functionality - to be implemented');
        });
        
        // Add Cost handler
        $sectionItem.find('.survey-data-mock-add-cost-btn').off('click').on('click', function(e) {
            e.stopPropagation();
            alert('Add Cost functionality - to be implemented');
        });
        
        // Microphone handler
        $sectionItem.find('.survey-data-mock-mic-btn').off('click').on('click', function(e) {
            e.stopPropagation();
            alert('Voice input functionality - to be implemented');
        });
        
        // Image upload handler
        $sectionItem.find('.survey-data-mock-images-upload').off('click').on('click', function() {
            alert('Image upload functionality - to be implemented');
        });
        
        // Initialize carousels for this section
        setTimeout(function() {
            initializeCarousels();
        }, 100);
    }
});
</script>
@endpush
