@extends('layouts.survey-mock')

@section('title', 'Survey Data')

@section('content')
<div class="survey-data-mock-content" data-survey-id="{{ $survey->id }}">
    <!-- Integrated Header Bar -->
    <div class="survey-data-mock-header-bar">
        <div class="survey-data-mock-header-left">
            <a href="{{ route('surveyor.surveys.index') }}" class="survey-data-mock-back">
                <i class="fas fa-chevron-left"></i>
            </a>
            <div class="survey-data-mock-header-title">
                <span class="survey-data-mock-header-address">{{ $survey->full_address ?? 'Property Address' }}</span>
                <span class="survey-data-mock-header-ref">{{ $survey->job_reference ?? 'Job Reference' }}</span>
            </div>
        </div>
        <div class="survey-data-mock-header-right">
            <span class="survey-data-mock-header-level">{{ $survey->level ?? 'Level 2' }}</span>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="survey-data-mock-body">
        @foreach($categories as $categoryName => $subCategories)
            <section class="survey-data-mock-category">
                <div class="survey-data-mock-category-header" data-category-toggle>
                    <h2 class="survey-data-mock-category-title">{{ $categoryName }}</h2>
                    <i class="fas fa-chevron-down survey-data-mock-category-toggle-icon"></i>
                </div>
                
                <div class="survey-data-mock-category-content collapse show">
                    <div class="survey-data-mock-sections">
                        @foreach($subCategories as $subCategoryName => $sections)
                            <div class="survey-data-mock-sub-category" data-sub-category="{{ $subCategoryName }}">
                                <h3 class="survey-data-mock-sub-category-title">{{ $subCategoryName }}</h3>
                                
                                <div class="survey-data-mock-sub-category-sections">
                                    @foreach($sections as $section)
                                        @include('surveyor.surveys.mocks.partials.section-item', ['section' => $section, 'categoryName' => $categoryName, 'subCategoryName' => $subCategoryName, 'optionsMapping' => $optionsMapping ?? []])
                                    @endforeach
                                    
                                    {{-- Content sections linked to this subcategory --}}
                                    @if(isset($contentSections['by_subcategory'][$categoryName][$subCategoryName]))
                                        @foreach($contentSections['by_subcategory'][$categoryName][$subCategoryName] as $contentSection)
                                            @include('surveyor.surveys.mocks.partials.content-section-item', ['contentSection' => $contentSection])
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        @endforeach
                        
                        {{-- Content sections linked to this category (not subcategory) --}}
                        @if(isset($contentSections['by_category'][$categoryName]) && count($contentSections['by_category'][$categoryName]) > 0)
                            <div class="survey-data-mock-sub-category">
                                <h3 class="survey-data-mock-sub-category-title">Content Sections</h3>
                                <div class="survey-data-mock-sub-category-sections">
                                    @foreach($contentSections['by_category'][$categoryName] as $contentSection)
                                        @include('surveyor.surveys.mocks.partials.content-section-item', ['contentSection' => $contentSection])
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </section>
        @endforeach

        <!-- Accommodation Configuration Section -->
        @if(isset($accommodationSections) && count($accommodationSections) > 0)
            <section class="survey-data-mock-category">
                <div class="survey-data-mock-category-header" data-category-toggle>
                    <h2 class="survey-data-mock-category-title">Configuration of Accommodation</h2>
                    <i class="fas fa-chevron-down survey-data-mock-category-toggle-icon"></i>
                </div>
                
                <div class="survey-data-mock-category-content collapse show">
                    <div class="survey-data-mock-sections" style="gap: 0.75rem;">
                        @foreach($accommodationSections as $accommodation)
                            @include('surveyor.surveys.mocks.partials.accommodation-section-item', ['accommodation' => $accommodation])
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <!-- Standalone Content Sections -->
        @if(isset($contentSections['standalone']) && count($contentSections['standalone']) > 0)
            <section class="survey-data-mock-category">
                <div class="survey-data-mock-category-header" data-category-toggle>
                    <h2 class="survey-data-mock-category-title">Content Sections</h2>
                    <i class="fas fa-chevron-down survey-data-mock-category-toggle-icon"></i>
                </div>
                
                <div class="survey-data-mock-category-content collapse show">
                    <div class="survey-data-mock-sections">
                        @foreach($contentSections['standalone'] as $contentSection)
                            @include('surveyor.surveys.mocks.partials.content-section-item', ['contentSection' => $contentSection])
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
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

<!-- Cost Modal -->
<div id="survey-data-mock-cost-modal" class="survey-data-mock-cost-modal">
    <div class="survey-data-mock-cost-modal-content">
        <div class="survey-data-mock-cost-modal-header">
            <h3 class="survey-data-mock-cost-modal-title">Add Cost</h3>
            <button type="button" class="survey-data-mock-cost-modal-close" id="cost-modal-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="survey-data-mock-cost-modal-body">
            <div class="survey-data-mock-cost-modal-field">
                <label class="survey-data-mock-cost-modal-label">Category</label>
                <input type="text" class="survey-data-mock-cost-modal-input" id="cost-modal-category" placeholder="e.g., Essential, Recommended, Optional">
            </div>
            <div class="survey-data-mock-cost-modal-field">
                <label class="survey-data-mock-cost-modal-label">Description</label>
                <textarea class="survey-data-mock-cost-modal-textarea" id="cost-modal-description" rows="3" placeholder="Enter cost description..."></textarea>
            </div>
            <div class="survey-data-mock-cost-modal-field">
                <label class="survey-data-mock-cost-modal-label">Due Year</label>
                <input type="text" class="survey-data-mock-cost-modal-input" id="cost-modal-due" placeholder="e.g., 2025">
            </div>
            <div class="survey-data-mock-cost-modal-field">
                <label class="survey-data-mock-cost-modal-label">Cost Amount (£)</label>
                <input type="text" class="survey-data-mock-cost-modal-input" id="cost-modal-cost" placeholder="0.00">
            </div>
        </div>
        <div class="survey-data-mock-cost-modal-footer">
            <button type="button" class="survey-data-mock-cost-modal-btn survey-data-mock-cost-modal-btn-cancel" id="cost-modal-cancel">Cancel</button>
            <button type="button" class="survey-data-mock-cost-modal-btn survey-data-mock-cost-modal-btn-save" id="cost-modal-save">Save</button>
        </div>
    </div>
</div>

<!-- Image Lightbox Modal - Clean Design -->
<div id="survey-data-mock-lightbox" class="survey-data-mock-lightbox">
    <div class="survey-data-mock-lightbox-backdrop"></div>
    <div class="survey-data-mock-lightbox-container">
        <!-- Close Button -->
        <button type="button" class="survey-data-mock-lightbox-close" id="lightbox-close" title="Close">
            <i class="fas fa-times"></i>
        </button>
        
        <!-- Image Display -->
        <div class="survey-data-mock-lightbox-body">
            <button type="button" class="survey-data-mock-lightbox-nav survey-data-mock-lightbox-prev" id="lightbox-prev">
                <i class="fas fa-chevron-left"></i>
            </button>
            <div class="survey-data-mock-lightbox-image-wrapper" id="lightbox-image-wrapper">
                <img src="" alt="Preview" class="survey-data-mock-lightbox-image" id="lightbox-image">
                <div class="survey-data-mock-lightbox-loader">
                    <div class="survey-data-mock-lightbox-spinner"></div>
                </div>
            </div>
            <button type="button" class="survey-data-mock-lightbox-nav survey-data-mock-lightbox-next" id="lightbox-next">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
        
        <!-- Counter -->
        <div class="survey-data-mock-lightbox-counter">
            <span id="lightbox-current">1</span> / <span id="lightbox-total">1</span>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    /* Poppins Font Family Throughout - Exclude Font Awesome icons */
    .survey-data-mock-content {
        font-family: 'Poppins', sans-serif;
        height: auto;
        display: flex;
        flex-direction: column;
        overflow: visible;
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
    }

    .survey-data-mock-header-left {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .survey-data-mock-back {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.1);
        color: #C1EC4A;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .survey-data-mock-back:hover {
        background: rgba(255, 255, 255, 0.15);
        color: #C1EC4A;
    }

    .survey-data-mock-back i {
        font-size:1.6rem;
        display: inline-block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    .survey-data-mock-header-title {
        display: flex;
        flex-direction: column;
        gap: 0.125rem;
    }

    .survey-data-mock-header-address {
        font-size: 16px;
        font-weight: 600;
        color: #FFFFFF;
    }

    .survey-data-mock-header-ref {
        font-size: 13px;
        color: #94A3B8;
    }

    .survey-data-mock-header-right {
        display: flex;
        align-items: center;
    }

    .survey-data-mock-header-level {
        font-size: 14px;
        font-weight: 600;
        color: #C1EC4A;
        padding: 0.5rem 1rem;
        background: rgba(193, 236, 74, 0.1);
        border-radius: 6px;
    }

    /* Main Body */
    .survey-data-mock-body {
        padding: 2rem 0;
        min-height: calc(100vh - 120px);
        height: auto;
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: visible;
    }

    /* Category Section */
    .survey-data-mock-category {
        margin-bottom: 2rem;
        background: transparent;
        overflow: visible;
        width: 100%;
    }

    .survey-data-mock-category:last-child {
        margin-bottom: 0;
    }

    /* Category Header - Collapsible */
    .survey-data-mock-category-header {
        background: #1E293B;
        padding: 1.25rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .survey-data-mock-category-header:hover {
        background: #334155;
    }

    .survey-data-mock-category-title {
        font-size: 30px;
        color: #FFFFFF!important;
        margin: 0;
        font-family: 'Poppins', sans-serif;
    }

    .survey-data-mock-category-toggle-icon {
        font-size: 1.8rem;
        color: #C1EC4A;
        transition: transform 0.3s ease;
        display: inline-block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    .survey-data-mock-category.collapsed .survey-data-mock-category-toggle-icon {
        transform: rotate(-90deg);
    }

    /* Category Content - Collapsible Container */
    .survey-data-mock-category-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease, opacity 0.2s ease 0.1s;
        opacity: 0;
        background: transparent;
        width: 100%;
    }

    .survey-data-mock-category-content.show {
        opacity: 1;
        transition: max-height 0.3s ease, opacity 0.2s ease;
        background: transparent;
        overflow: visible;
        width: 100%;
    }

    /* Sub-Categories */
    .survey-data-mock-sub-category {
        margin-bottom: 0.75rem;
        background: transparent;
        padding: 0;
        width: 100%;
    }

    .survey-data-mock-sub-category:last-child {
        margin-bottom: 0;
    }

    .survey-data-mock-sub-category-title {
        font-size: 20px;
        color: #FFFFFF;
        margin: 0 0 0.25rem 0;
        padding: 0;
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
        /* gap: 1rem; */
        padding-top: 1rem;
        flex: 1;
        min-height: 0;
        background: transparent;
        width: 100%;
        box-sizing: border-box;
    }

    .survey-data-mock-section-item {
        background: transparent;
        border-radius: 0;
        overflow: visible;
        border: none;
    }

    /* Cloned Section Styling - Same as main sections, no special styling */
    .survey-data-mock-section-item-cloned {
        /* No margin-left - cloned sections appear directly under parent */
        /* All other styling matches main sections */
    }

    .survey-data-mock-section-header {
        background: #FFFFFF;
        padding: 1rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        transition: background 0.2s ease;
        position: relative;
        border: 1px solid #1e293b1c;
    }

    .survey-data-mock-section-header:hover {
        background: #F8FAFC;
    }

    .survey-data-mock-section-name {
        font-size: 16px;
        color: #1A202C;
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
        color: #64748B;
    }

    .survey-data-mock-status-icon {
        font-size: 16px;
        color: #64748B;
    }

    .survey-data-mock-status-text {
        font-size: 15px;
        color: #64748B;
    }

    .survey-data-mock-status-separator {
        color: #CBD5E1;
        margin: 0 0.25rem;
        font-size: 10px;
    }

    .survey-data-mock-completion {
        font-size: 14px;
        color: #1A202C;
    }

    .survey-data-mock-condition-badge {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0;
        line-height: 0;
        color: transparent;
        border: 2px solid #FFFFFF;
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        flex-shrink: 0;
        pointer-events: auto;
        position: relative;
        z-index: 100;
        overflow: hidden;
        text-indent: -9999px;
    }
    
    .survey-data-mock-condition-badge::before,
    .survey-data-mock-condition-badge::after {
        display: none;
    }
    
    .survey-data-mock-condition-badge * {
        display: none !important;
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
        font-size: 1.8rem;
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
        border: 1px solid #1e293b1c;
    }

    .survey-data-mock-section-title-bar {
        background: #FFFFFF;
        padding: 1rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
        border: 1px solid #1e293b1c;
        border-bottom: none;
    }

    .survey-data-mock-section-item.expanded .survey-data-mock-section-title-bar {
        display: flex;
    }

    .survey-data-mock-section-title-text {
        font-size: 20px;
        color: #1A202C!important;
        margin: 0;
        font-family: 'Poppins', sans-serif;
    }

    .survey-data-mock-section-title-collapse {
        font-size: 1.8rem;
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
        overflow: visible;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
    }

    /* Two Column Grid Layout with Draggable Divider */
    .survey-data-mock-form-grid {
        display: flex;
        gap: 0;
        margin-bottom: 1rem;
        position: relative;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        overflow: hidden;
    }

    .survey-data-mock-form-grid-divider {
        width: 4px;
        background: #475569;
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
        box-sizing: border-box;
        overflow-x: auto;
        overflow-y: auto;
    }

    .survey-data-mock-form-column-left {
        flex: 0 0 50%;
        gap: 1rem;
        padding-right: 0.75rem;
        min-width: 0;
        box-sizing: border-box;
    }

    .survey-data-mock-form-column-right {
        flex: 0 0 50%;
        gap: 1rem;
        padding-left: 0.75rem;
        padding-right: 1rem;
        min-width: 300px;
        box-sizing: border-box;
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
        font-size: 16px;
        color: #64748B;
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
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 8px;
        background: #F3F4F6;
        color: #1a202c7a;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        font-family: 'Poppins', sans-serif;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .survey-data-mock-button:hover {
        background: #E5E7EB;
    }

    .survey-data-mock-button.active {
        background: #C1EC4A;
        color: #1A202C;
        font-weight: 600;
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
        width: 100%;
        box-sizing: border-box;
    }

    .survey-data-mock-add-cost-btn {
        padding: 0.5rem 1rem;
        border: 1px solid rgba(148, 163, 184, 0.4);
        border-radius: 4px;
        background: #FFFFFF;
        color: #1A202C;
        font-size: 13px;
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
        max-width: 100%;
        border-collapse: collapse;
        border: 1px solid rgba(148, 163, 184, 0.2);
        border-radius: 4px;
        overflow: hidden;
        box-sizing: border-box;
    }

    .survey-data-mock-costs-table thead {
        background: #F8FAFC;
    }

    .survey-data-mock-costs-table th {
        padding: 0.5rem;
        text-align: left;
        font-size: 12px;
        color: #64748B;
        text-transform: uppercase;
        border-bottom: 1px solid rgba(148, 163, 184, 0.2);
    }

    .survey-data-mock-costs-table th:last-child {
        text-align: center;
        width: 100px;
    }

    .survey-data-mock-costs-table td {
        padding: 0.5rem;
        font-size: 13px;
        color: #1A202C;
        border-bottom: 1px solid rgba(148, 163, 184, 0.1);
    }

    .survey-data-mock-costs-table td:last-child {
        text-align: center;
        padding: 0.25rem;
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

    /* ============================================
       IMAGE UPLOAD SYSTEM - Clean Design
       ============================================ */
    
    /* Images Section Container */
    .survey-data-mock-images-section {
        margin-top: 1.5rem;
    }

    .survey-data-mock-images-section .survey-data-mock-field-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
    }

    .survey-data-mock-image-count {
        font-size: 13px;
        color: #64748B;
        font-weight: 500;
    }

    /* Upload Dropzone - Matching Project Theme */
    .survey-data-mock-upload-dropzone {
        border: 2px dashed #CBD5E1;
        border-radius: 12px;
        padding: 2rem 1.5rem;
        background: #F8FAFC;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
    }

    .survey-data-mock-upload-dropzone:hover {
        border-color: #C1EC4A;
        background: #F1F5F9;
    }

    .survey-data-mock-upload-dropzone.dragover {
        border-color: #C1EC4A;
        border-style: solid;
        background: rgba(193, 236, 74, 0.1);
    }

    .survey-data-mock-upload-icon-main {
        font-size: 36px;
        color: #1E293B;
        margin-bottom: 0.75rem;
        display: inline-block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    .survey-data-mock-upload-dropzone:hover .survey-data-mock-upload-icon-main {
        color: #C1EC4A;
    }

    .survey-data-mock-upload-title {
        font-size: 16px;
        font-weight: 600;
        color: #1E293B;
        margin: 0 0 0.25rem 0;
    }

    .survey-data-mock-upload-subtitle {
        font-size: 14px;
        color: #64748B;
        margin: 0;
    }

    .survey-data-mock-upload-browse {
        color: #1E293B;
        font-weight: 600;
        text-decoration: underline;
        cursor: pointer;
    }

    .survey-data-mock-upload-browse:hover {
        color: #C1EC4A;
    }

    /* Image Grid */
    .survey-data-mock-images-preview,
    .survey-data-mock-existing-images {
        margin-top: 1rem;
    }

    .survey-data-mock-images-grid-enhanced {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        gap: 0.75rem;
    }

    /* Image Card */
    .survey-data-mock-image-card {
        position: relative;
        background: #FFFFFF;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #E2E8F0;
        transition: all 0.2s ease;
    }

    .survey-data-mock-image-card:hover {
        border-color: #C1EC4A;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .survey-data-mock-image-wrapper {
        position: relative;
        width: 100%;
        padding-top: 100%;
        overflow: hidden;
        background: #F1F5F9;
    }

    .survey-data-mock-image-thumb {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Image Error State */
    .survey-data-mock-image-error {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: none;
        align-items: center;
        justify-content: center;
        background: #F1F5F9;
        color: #94A3B8;
        font-size: 32px;
    }

    .survey-data-mock-image-error i {
        display: inline-block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    /* Image Overlay with Actions */
    .survey-data-mock-image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(30, 41, 59, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .survey-data-mock-image-card:hover .survey-data-mock-image-overlay {
        opacity: 1;
    }

    .survey-data-mock-image-action {
        width: 32px;
        height: 32px;
        border: none;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 13px;
    }

    .survey-data-mock-image-preview-btn {
        background: #FFFFFF;
        color: #1E293B;
    }

    .survey-data-mock-image-preview-btn:hover {
        background: #C1EC4A;
    }

    .survey-data-mock-image-delete {
        background: #EF4444;
        color: #FFFFFF;
    }

    .survey-data-mock-image-delete:hover {
        background: #DC2626;
    }

    .survey-data-mock-image-action i {
        display: inline-block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    /* Image Info Bar */
    .survey-data-mock-image-info {
        padding: 0.5rem;
        background: #FFFFFF;
        border-top: 1px solid #E2E8F0;
    }

    .survey-data-mock-image-number {
        font-size: 11px;
        font-weight: 600;
        color: #64748B;
    }

    /* ============================================
       LIGHTBOX MODAL - Clean Design
       ============================================ */
    
    .survey-data-mock-lightbox {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 99999;
        display: none;
        font-family: 'Poppins', sans-serif;
    }

    .survey-data-mock-lightbox.active {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .survey-data-mock-lightbox-backdrop {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(15, 23, 42, 0.95);
    }

    .survey-data-mock-lightbox-container {
        position: relative;
        z-index: 1;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Close Button */
    .survey-data-mock-lightbox-close {
        position: absolute;
        top: 1.5rem;
        right: 1.5rem;
        width: 44px;
        height: 44px;
        border: none;
        border-radius: 8px;
        background: #1E293B;
        color: #FFFFFF;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        transition: all 0.2s ease;
        z-index: 20;
    }

    .survey-data-mock-lightbox-close:hover {
        background: #EF4444;
    }

    .survey-data-mock-lightbox-close i {
        display: inline-block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    /* Lightbox Body */
    .survey-data-mock-lightbox-body {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        padding: 4rem;
    }

    .survey-data-mock-lightbox-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 48px;
        height: 48px;
        border: none;
        border-radius: 8px;
        background: #1E293B;
        color: #FFFFFF;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        z-index: 10;
    }

    .survey-data-mock-lightbox-nav:hover {
        background: #C1EC4A;
        color: #1E293B;
    }

    .survey-data-mock-lightbox-nav:disabled {
        opacity: 0.3;
        cursor: not-allowed;
    }

    .survey-data-mock-lightbox-nav:disabled:hover {
        background: #1E293B;
        color: #FFFFFF;
    }

    .survey-data-mock-lightbox-nav i {
        display: inline-block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    .survey-data-mock-lightbox-prev {
        left: 1.5rem;
    }

    .survey-data-mock-lightbox-next {
        right: 1.5rem;
    }

    .survey-data-mock-lightbox-image-wrapper {
        position: relative;
        max-width: calc(100% - 160px);
        max-height: calc(100vh - 120px);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .survey-data-mock-lightbox-image {
        max-width: 100%;
        max-height: calc(100vh - 120px);
        object-fit: contain;
        border-radius: 8px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
    }

    .survey-data-mock-lightbox-loader {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        display: none;
    }

    .survey-data-mock-lightbox-loader.active {
        display: block;
    }

    .survey-data-mock-lightbox-spinner {
        width: 40px;
        height: 40px;
        border: 3px solid rgba(255, 255, 255, 0.2);
        border-top-color: #C1EC4A;
        border-radius: 50%;
        animation: lightboxSpin 0.8s linear infinite;
    }

    @keyframes lightboxSpin {
        to { transform: rotate(360deg); }
    }

    /* Counter */
    .survey-data-mock-lightbox-counter {
        position: absolute;
        bottom: 1.5rem;
        left: 50%;
        transform: translateX(-50%);
        font-size: 14px;
        color: #94A3B8;
        background: #1E293B;
        padding: 0.5rem 1rem;
        border-radius: 6px;
    }

    .survey-data-mock-lightbox-counter span {
        font-weight: 600;
        color: #FFFFFF;
    }


    /* Legacy Support - Keep old classes working */
    .survey-data-mock-images-upload {
        border: 2px dashed rgba(148, 163, 184, 0.3);
        border-radius: 8px;
        padding: 2rem 1rem;
        text-align: center;
        background: #E0F2FE;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .survey-data-mock-images-upload:hover {
        border-color: #C1EC4A;
        background: #DBEAFE;
    }

    .survey-data-mock-images-upload.dragover {
        border-color: #C1EC4A;
        background: #BFDBFE;
        border-width: 3px;
    }

    .survey-data-mock-upload-icon {
        font-size: 40px;
        color: #64748B;
        margin-bottom: 0.75rem;
        transition: all 0.3s ease;
        display: inline-block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    .survey-data-mock-upload-text {
        font-size: 15px;
        color: #1A202C;
        margin: 0;
    }

    .survey-data-mock-images-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .survey-data-mock-image-item {
        position: relative;
        width: 100%;
        padding-top: 100%;
        background: #F8FAFC;
        border: 1px solid rgba(148, 163, 184, 0.3);
        border-radius: 4px;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .survey-data-mock-image-item:hover {
        border-color: #C1EC4A;
    }

    .survey-data-mock-image-thumbnail {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
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

    /* Report Content Area */
    .survey-data-mock-report-content {
        background: #FFFFFF;
        border-top: 1px solid rgba(148, 163, 184, 0.2);
    }

    .survey-data-mock-report-content-wrapper {
        padding: 1.5rem;
    }

    .survey-data-mock-report-textarea {
        width: 100%;
        padding: 1rem;
        border: 1px solid rgba(148, 163, 184, 0.4);
        border-radius: 4px;
        font-size: 14px;
        color: #1A202C;
        background: #FFFFFF;
        transition: border-color 0.2s ease;
        font-family: 'Poppins', sans-serif;
        resize: vertical;
        min-height: 300px;
        line-height: 1.6;
    }

    .survey-data-mock-report-textarea:focus {
        outline: none;
        border-color: #C1EC4A;
    }

    .survey-data-mock-report-textarea:disabled {
        background: #F8FAFC;
        color: #64748B;
        cursor: not-allowed;
    }

    /* Action Icons Bar */
    .survey-data-mock-action-icons {
        display: flex;
        gap: 0.75rem;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid rgba(148, 163, 184, 0.2);
        justify-content: flex-end;
    }

    .survey-data-mock-action-icon-btn {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(148, 163, 184, 0.4);
        border-radius: 4px;
        background: #FFFFFF;
        color: #475569;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 16px;
    }

    .survey-data-mock-action-icon-btn:hover {
        background: #F1F5F9;
        border-color: rgba(148, 163, 184, 0.6);
        color: #1E293B;
    }

    .survey-data-mock-action-icon-btn.active {
        background: #C1EC4A;
        border-color: #C1EC4A;
        color: #1A202C;
    }

    .survey-data-mock-action-icon-btn.locked {
        background: #F8FAFC;
        border-color: #CBD5E1;
        color: #94A3B8;
        cursor: not-allowed;
    }

    .survey-data-mock-action-icon-btn.locked:hover {
        background: #F8FAFC;
        border-color: #CBD5E1;
        color: #94A3B8;
    }

    .survey-data-mock-action-icon-btn i {
        display: inline-block !important;
        visibility: visible !important;
        opacity: 1 !important;
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

    .survey-data-mock-clone-location-btn {
        padding: 0.625rem 1.25rem;
        border: 1px solid rgba(148, 163, 184, 0.4);
        border-radius: 4px;
        background: #FFFFFF;
        color: #1A202C;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s ease;
        font-family: 'Poppins', sans-serif;
    }

    .survey-data-mock-clone-location-btn:hover {
        background: #F1F5F9;
        border-color: rgba(148, 163, 184, 0.6);
    }

    .survey-data-mock-clone-location-btn.active {
        background: #C1EC4A;
        border-color: #C1EC4A;
        color: #1A202C;
    }

    .survey-data-mock-clone-modal-input,
    .survey-data-mock-clone-modal-select {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid rgba(148, 163, 184, 0.4);
        border-radius: 4px;
        font-size: 14px;
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
        color: #FFFFFF;
        border: 2px solid #FFFFFF;
        margin-bottom: 0.75rem;
    }

    .survey-data-mock-rating-option-label {
        font-size: 14px;
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

    /* Cost Modal Styles */
    .survey-data-mock-cost-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 10002;
        align-items: center;
        justify-content: center;
    }

    .survey-data-mock-cost-modal.show {
        display: flex;
    }

    .survey-data-mock-cost-modal-content {
        background: #FFFFFF;
        border-radius: 8px;
        padding: 2rem;
        max-width: 500px;
        width: 90%;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }

    .survey-data-mock-cost-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .survey-data-mock-cost-modal-title {
        font-size: 20px;
        color: #1A202C;
        margin: 0;
    }

    .survey-data-mock-cost-modal-close {
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

    .survey-data-mock-cost-modal-close:hover {
        background: rgba(148, 163, 184, 0.1);
        color: #64748B;
    }

    .survey-data-mock-cost-modal-body {
        margin-bottom: 1.5rem;
    }

    .survey-data-mock-cost-modal-field {
        margin-bottom: 1.5rem;
    }

    .survey-data-mock-cost-modal-field:last-child {
        margin-bottom: 0;
    }

    .survey-data-mock-cost-modal-label {
        display: block;
        font-size: 13px;
        color: #64748B;
        margin-bottom: 0.75rem;
        text-transform: uppercase;
        font-weight: 600;
    }

    .survey-data-mock-cost-modal-input,
    .survey-data-mock-cost-modal-textarea {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid rgba(148, 163, 184, 0.4);
        border-radius: 4px;
        font-size: 14px;
        color: #1A202C;
        background: #FFFFFF;
        transition: border-color 0.2s ease;
        font-family: 'Poppins', sans-serif;
    }

    .survey-data-mock-cost-modal-textarea {
        resize: vertical;
        min-height: 80px;
    }

    .survey-data-mock-cost-modal-input:focus,
    .survey-data-mock-cost-modal-textarea:focus {
        outline: none;
        border-color: #C1EC4A;
    }

    .survey-data-mock-cost-modal-footer {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
    }

    .survey-data-mock-cost-modal-btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 4px;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s ease;
        font-family: 'Poppins', sans-serif;
    }

    .survey-data-mock-cost-modal-btn-cancel {
        background: #F1F5F9;
        color: #64748B;
        border: 1px solid rgba(148, 163, 184, 0.4);
    }

    .survey-data-mock-cost-modal-btn-cancel:hover {
        background: #E2E8F0;
        border-color: rgba(148, 163, 184, 0.6);
    }

    .survey-data-mock-cost-modal-btn-save {
        background: #1E293B;
        color: #FFFFFF;
    }

    .survey-data-mock-cost-modal-btn-save:hover {
        background: #0F172A;
    }

    /* Cost Table Actions */
    .survey-data-mock-costs-table td {
        position: relative;
    }

    .survey-data-mock-cost-actions {
        display: flex;
        gap: 0.5rem;
        justify-content: center;
        align-items: center;
    }

    .survey-data-mock-cost-edit-btn,
    .survey-data-mock-cost-delete-btn {
        padding: 0.25rem 0.5rem;
        border: 1px solid rgba(148, 163, 184, 0.4);
        border-radius: 4px;
        background: #FFFFFF;
        color: #64748B;
        font-size: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .survey-data-mock-cost-edit-btn:hover {
        background: #F1F5F9;
        border-color: rgba(148, 163, 184, 0.6);
        color: #1A202C;
    }

    .survey-data-mock-cost-delete-btn:hover {
        background: #FEF2F2;
        border-color: #EF4444;
        color: #EF4444;
    }

    .survey-data-mock-cost-edit-btn i,
    .survey-data-mock-cost-delete-btn i {
        display: inline-block !important;
        visibility: visible !important;
        opacity: 1 !important;
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
    /* Accommodation Configuration Section Styles */
    .survey-data-mock-accommodation-section {
        margin-top: 3rem;
        padding-top: 3rem;
        border-top: 2px solid rgba(148, 163, 184, 0.2);
    }

    .survey-data-mock-accommodation-item {
        background: #FFFFFF;
        border: 1px solid rgba(148, 163, 184, 0.2);
        border-radius: 4px;
        margin-bottom: 1.5rem;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08), 0 1px 3px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .survey-data-mock-accommodation-item:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12), 0 2px 6px rgba(0, 0, 0, 0.08);
        border-color: rgba(148, 163, 184, 0.25);
        transform: translateY(-1px);
    }

    .survey-data-mock-accommodation-item.expanded {
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12), 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .survey-data-mock-accommodation-header {
        padding: 1rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        background: #1E293B;
        border-bottom: 1px solid rgba(148, 163, 184, 0.2);
        transition: all 0.2s ease;
    }

    .survey-data-mock-accommodation-header:hover {
        background: #475569;
    }

    .survey-data-mock-accommodation-name {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 16px;
        color: #FFFFFF;
        font-family: 'Poppins', sans-serif;
    }

    .survey-data-mock-accommodation-expand-icon {
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        color: #C1EC4A;
        font-size: 1.8rem;
        display: inline-block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    .survey-data-mock-accommodation-item.expanded .survey-data-mock-accommodation-expand-icon {
        transform: rotate(90deg);
    }

    .survey-data-mock-accommodation-status {
        display: flex;
        align-items: center;
    }

    .survey-data-mock-accommodation-item.expanded .survey-data-mock-expand-icon {
        transform: rotate(180deg);
    }

    .survey-data-mock-accommodation-details {
        padding: 1rem 1.5rem;
        background: #FFFFFF;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        overflow: hidden;
    }

    /* Component Tabs */
    .survey-data-mock-component-tabs {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 2rem;
        border-bottom: 2px solid rgba(148, 163, 184, 0.15);
        padding-bottom: 0;
        flex-wrap: wrap;
        position: relative;
    }

    .survey-data-mock-component-tab {
        padding: 0.75rem 1.25rem;
        background: transparent;
        border: none;
        border-bottom: 3px solid transparent;
        color: #64748B;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        bottom: -2px;
        font-family: 'Poppins', sans-serif;
        border-radius: 4px 4px 0 0;
    }

    .survey-data-mock-component-tab:hover {
        color: #1A202C;
        background: rgba(148, 163, 184, 0.05);
    }

    .survey-data-mock-component-tab.active {
        color: #C1EC4A;
        border-bottom-color: #C1EC4A;
        background: rgba(193, 236, 74, 0.08);
    }

    /* Carousel Container - Only on Left Side */
    .survey-data-mock-accommodation-form-column-left .survey-data-mock-carousel-wrapper {
        position: relative;
        overflow: hidden;
        width: 100%;
        /* background: #F8FAFC; */
        border-radius: 8px;
        padding: 1.5rem;
        border: 1px solid rgba(148, 163, 184, 0.1);
        user-select: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
    }

    .survey-data-mock-carousel-wrapper {
        cursor: grab;
    }

    .survey-data-mock-carousel-wrapper.dragging {
        cursor: grabbing;
    }

    .survey-data-mock-carousel-wrapper button {
        cursor: pointer;
    }

    .survey-data-mock-carousel-track {
        position: relative;
        width: 100%;
        min-height: 250px;
    }

    .survey-data-mock-carousel-slide {
        width: 100%;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.4s cubic-bezier(0.4, 0, 0.2, 1), transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: absolute;
        top: 0;
        left: 0;
        pointer-events: none;
        transform: translateX(20px);
    }

    .survey-data-mock-carousel-slide.active {
        opacity: 1;
        visibility: visible;
        position: relative;
        pointer-events: auto;
        transform: translateX(0);
    }

    /* Carousel Indicator Dots */
    .survey-data-mock-carousel-indicators {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.5rem;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid rgba(148, 163, 184, 0.1);
    }

    .survey-data-mock-carousel-indicator {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: rgba(148, 163, 184, 0.3);
        border: none;
        cursor: pointer;
        padding: 0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        outline: none;
    }

    .survey-data-mock-carousel-indicator:hover {
        background: rgba(148, 163, 184, 0.5);
        transform: scale(1.2);
    }

    .survey-data-mock-carousel-indicator.active {
        background: #1E293B;
        width: 10px;
        height: 10px;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(193, 236, 74, 0.3);
    }

    .survey-data-mock-carousel-indicator.active:hover {
        transform: scale(1.1);
    }

    /* Accommodation Form Grid */
    .survey-data-mock-accommodation-form-grid {
        display: flex;
        gap: 0;
        padding: 0;
        position: relative;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        overflow: hidden;
    }

    .survey-data-mock-accommodation-form-column-left {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        flex: 0 0 50%;
        min-width: 0;
        padding-right: 0.75rem;
        box-sizing: border-box;
        overflow-x: auto;
        overflow-y: auto;
    }

    .survey-data-mock-accommodation-form-column-right {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        flex: 0 0 50%;
        min-width: 300px;
        padding-left: 0.75rem;
        padding-right: 1rem;
        box-sizing: border-box;
        overflow: visible;
    }


    /* Accommodation Form Grid Divider */
    .survey-data-mock-accommodation-form-grid-divider {
        width: 4px;
        background: rgba(148, 163, 184, 0.2);
        cursor: col-resize;
        position: relative;
        flex-shrink: 0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 10;
        margin: 0 0.5rem;
        border-radius: 2px;
    }

    .survey-data-mock-accommodation-form-grid-divider:hover,
    .survey-data-mock-accommodation-form-grid-divider.dragging {
        background: #C1EC4A;
        width: 5px;
        box-shadow: 0 0 8px rgba(193, 236, 74, 0.4);
    }

    .survey-data-mock-accommodation-form-grid-divider::before {
        content: '';
        position: absolute;
        left: -4px;
        right: -4px;
        top: 0;
        bottom: 0;
        cursor: col-resize;
    }

    .survey-data-mock-accommodation-form-grid-divider-handle {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 24px;
        height: 48px;
        background: #C1EC4A;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        pointer-events: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .survey-data-mock-accommodation-form-grid-divider:hover .survey-data-mock-accommodation-form-grid-divider-handle,
    .survey-data-mock-accommodation-form-grid-divider.dragging .survey-data-mock-accommodation-form-grid-divider-handle {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1.1);
    }

    .survey-data-mock-accommodation-form-grid-divider-handle i {
        color: #1A202C;
        font-size: 14px;
        display: inline-block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .survey-data-mock-accommodation-form-grid {
            grid-template-columns: 1fr;
        }

        .survey-data-mock-component-tabs {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .survey-data-mock-component-tab {
            white-space: nowrap;
        }
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Category Collapse/Expand Functionality
    $('[data-category-toggle]').on('click', function() {
        const $header = $(this);
        const $category = $header.closest('.survey-data-mock-category');
        const $content = $category.find('.survey-data-mock-category-content');
        
        // Toggle collapsed class
        const isCollapsed = $category.hasClass('collapsed');
        
        if (isCollapsed) {
            // Expanding
            $category.removeClass('collapsed');
            // First set a large max-height for transition, then remove constraint
            const scrollHeight = $content[0].scrollHeight || 10000;
            $content.css('max-height', scrollHeight + 'px');
            $content.addClass('show');
            // After transition, remove max-height constraint for full expansion
            setTimeout(function() {
                if (!$category.hasClass('collapsed')) {
                    $content.css('max-height', 'none');
                }
            }, 350);
        } else {
            // Collapsing
            $category.addClass('collapsed');
            // Get current height and set it for smooth transition
            const currentHeight = $content[0].scrollHeight || 0;
            $content.css('max-height', currentHeight + 'px');
            // Force reflow
            $content[0].offsetHeight;
            // Remove show class and set to 0
            $content.removeClass('show');
            setTimeout(function() {
                if ($category.hasClass('collapsed')) {
                    $content.css('max-height', '0');
                }
            }, 10);
        }
    });

    // Initialize expanded categories on page load
    $('.survey-data-mock-category-content.show').each(function() {
        const $content = $(this);
        const $category = $content.closest('.survey-data-mock-category');
        if (!$category.hasClass('collapsed')) {
            const scrollHeight = $content[0].scrollHeight || 10000;
            $content.css('max-height', scrollHeight + 'px');
            // After a brief delay, remove constraint for full expansion
            setTimeout(function() {
                if (!$category.hasClass('collapsed')) {
                    $content.css('max-height', 'none');
                }
            }, 350);
        }
    });

    // Options mapping from SurveyDataService
    const optionsMapping = @json($optionsMapping ?? []);

    // Helper function to get options with fallback
    function getOptions(categoryName, optionType, fallback = []) {
        if (optionType === 'location' || optionType === 'remaining_life' || optionType === 'defects') {
            return optionsMapping[optionType] || fallback;
        }
        
        if (optionsMapping[categoryName] && optionsMapping[categoryName][optionType]) {
            return optionsMapping[categoryName][optionType];
        }
        
        return fallback;
    }

    // Mock GPT Content Generator for Accommodation Sections
    function generateAccommodationReportContent(formData, accommodationName) {
        const notes = formData.notes || '';
        const components = formData.components || [];
        
        let report = `**${accommodationName}**\n\n`;
        report += `The ${accommodationName.toLowerCase()} has been inspected and assessed as part of the property survey.\n\n`;
        
        if (components.length > 0) {
            report += `**Component Assessment:**\n\n`;
            
            components.forEach(comp => {
                const componentName = comp.component_name || comp.component_key || 'Component';
                const material = comp.material || 'not specified';
                const defects = comp.defects && comp.defects.length > 0 && !comp.defects.includes('None')
                    ? comp.defects.join(', ')
                    : 'no significant defects';
                
                report += `*${componentName}:*\n`;
                report += `- Material: ${material}\n`;
                
                if (defects !== 'no significant defects') {
                    report += `- Defects identified: ${defects}\n`;
                } else {
                    report += `- Condition: Good, no significant defects observed\n`;
                }
                report += `\n`;
            });
        }
        
        if (notes) {
            report += `**Additional Notes:**\n${notes}\n\n`;
        }
        
        report += `**Recommendations:**\n`;
        
        const hasDefects = components.some(comp => 
            comp.defects && comp.defects.length > 0 && !comp.defects.includes('None')
        );
        
        if (hasDefects) {
            report += `It is recommended that the identified defects in the ${accommodationName.toLowerCase()} be addressed in a timely manner to prevent further deterioration. `;
            report += `Regular maintenance and monitoring is advised. `;
        } else {
            report += `The ${accommodationName.toLowerCase()} is in satisfactory condition. Regular maintenance is recommended to maintain its current state. `;
        }
        
        report += `Any necessary repairs should be carried out by qualified professionals in accordance with current building regulations.`;
        
        return report;
    }

    // Mock GPT Content Generator
    function generateMockReportContent(formData, sectionName, categoryName) {
        const location = formData.location || 'the property';
        const structure = formData.structure || 'standard';
        const material = formData.material || 'standard materials';
        const defects = formData.defects && formData.defects.length > 0 && !formData.defects.includes('None') 
            ? formData.defects.join(', ') 
            : 'no significant defects';
        const remainingLife = formData.remainingLife || 'not specified';
        const notes = formData.notes || '';
        const costs = formData.costs || [];
        
        let report = `**${sectionName}**\n\n`;
        report += `The ${sectionName.toLowerCase()} at ${location} has been inspected and assessed. `;
        report += `The structure is of ${structure.toLowerCase()} construction with ${material.toLowerCase()} materials. `;
        
        if (defects !== 'no significant defects') {
            report += `During the inspection, the following defects were identified: ${defects}. `;
        } else {
            report += `The inspection revealed no significant defects. `;
        }
        
        if (remainingLife !== 'not specified') {
            report += `The estimated remaining life is ${remainingLife} years. `;
        }
        
        if (notes) {
            report += `\n\n**Additional Notes:**\n${notes}\n`;
        }
        
        if (costs.length > 0) {
            report += `\n\n**Estimated Costs:**\n`;
            costs.forEach(cost => {
                report += `- ${cost.category}: ${cost.description} - Due ${cost.due} - £${cost.cost}\n`;
            });
        }
        
        report += `\n\n**Recommendations:**\n`;
        if (defects !== 'no significant defects') {
            report += `It is recommended that the identified defects be addressed in a timely manner to prevent further deterioration. `;
            report += `Regular maintenance and monitoring of the ${sectionName.toLowerCase()} is advised. `;
        } else {
            report += `The ${sectionName.toLowerCase()} is in good condition. Regular maintenance is recommended to maintain its current state. `;
        }
        
        report += `Any necessary repairs should be carried out by qualified professionals in accordance with current building regulations.`;
        
        return report;
    }

    // Expand/Collapse Section Items (exclude content sections)
    $('.survey-data-mock-section-header[data-expandable="true"]').on('click', function() {
        const $sectionItem = $(this).closest('.survey-data-mock-section-item');
        
        // Skip if this is a content section (handled separately)
        if ($sectionItem.hasClass('survey-data-mock-content-section-item')) {
            return;
        }
        
        const $details = $sectionItem.find('.survey-data-mock-section-details');
        const $reportContent = $sectionItem.find('.survey-data-mock-report-content');
        const $titleBar = $sectionItem.find('.survey-data-mock-section-title-bar');
        const hasReport = $sectionItem.attr('data-has-report') === 'true' || $sectionItem.attr('data-saved') === 'true';
        
        $sectionItem.toggleClass('expanded');
        
        if ($sectionItem.hasClass('expanded')) {
            $titleBar.slideDown(300);
            if (hasReport) {
                // Show report if report_content exists, hide form
                $details.hide();
                $reportContent.slideDown(300);
            } else {
                // Show form/details if no report_content
                $details.slideDown(300, function() {
                    // Re-initialize carousels after the section is fully visible
                    if (isSwiperAvailable()) {
                        initializeSectionCarousels($sectionItem);
                    }
                });
                $reportContent.hide();
            }
        } else {
            $details.slideUp(300);
            $reportContent.slideUp(300);
            $titleBar.slideUp(300);
        }
    });

    // Collapse from title bar (exclude content sections)
    $(document).on('click', '.survey-data-mock-section-title-collapse', function(e) {
        e.stopPropagation();
        const $sectionItem = $(this).closest('.survey-data-mock-section-item');
        
        // Skip if this is a content section (handled separately)
        if ($sectionItem.hasClass('survey-data-mock-content-section-item')) {
            return;
        }
        
        const $details = $sectionItem.find('.survey-data-mock-section-details');
        const $reportContent = $sectionItem.find('.survey-data-mock-report-content');
        const $titleBar = $sectionItem.find('.survey-data-mock-section-title-bar');
        
        $sectionItem.removeClass('expanded');
        $details.slideUp(300);
        $reportContent.slideUp(300);
        $titleBar.slideUp(300);
    });

    // Rating Badge Click Handler
    let currentRatingSectionId = null;
    let selectedRating = null;

    $(document).on('click', '.survey-data-mock-condition-badge', function(e) {
        e.stopPropagation();
        e.preventDefault();
        
        const $badge = $(this);
        // Check for section-id first, then accommodation-id as fallback
        let sectionId = $badge.data('section-id');
        if (!sectionId) {
            sectionId = $badge.data('accommodation-id');
        }
        const currentRating = $badge.data('current-rating') || 'ni';
        
        console.log('Rating badge clicked:', { sectionId, currentRating });
        
        if (!sectionId) {
            console.error('No section ID or accommodation ID found on badge');
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
        // Check both section-id and accommodation-id
        let $badges = $(`.survey-data-mock-condition-badge[data-section-id="${currentRatingSectionId}"]`);
        if ($badges.length === 0) {
            $badges = $(`.survey-data-mock-condition-badge[data-accommodation-id="${currentRatingSectionId}"]`);
        }
        
        // Update all badges
        const ratingText = selectedRating === 'ni' ? 'NI' : selectedRating;
        const ratingClass = selectedRating === 'ni' ? 'survey-data-mock-condition-badge--ni' : `survey-data-mock-condition-badge--${selectedRating}`;
        
        $badges.each(function() {
            const $badge = $(this);
            
            // Remove old rating class
            $badge.removeClass('survey-data-mock-condition-badge--1 survey-data-mock-condition-badge--2 survey-data-mock-condition-badge--3 survey-data-mock-condition-badge--ni');
            
            // Add new rating class
            $badge.addClass(ratingClass);
            
            // Update badge data (no text - badge shows only color)
            $badge.data('current-rating', selectedRating);
            // Also update section-id if it was accommodation-id
            if ($badge.data('accommodation-id') && !$badge.data('section-id')) {
                $badge.attr('data-section-id', currentRatingSectionId);
            }
        });
        
        // Check if assessment exists in backend (sectionId is numeric)
        const sectionId = currentRatingSectionId;
        const isNumericId = /^\d+$/.test(sectionId.toString());
        
        if (isNumericId) {
            // Assessment exists - update backend (works for both section and accommodation assessments)
            const surveyId = $('.survey-data-mock-content').data('survey-id');
            const assessmentId = parseInt(sectionId);
            
            console.log('Saving condition rating:', {
                surveyId: surveyId,
                assessmentId: assessmentId,
                rating: selectedRating,
                sectionId: sectionId
            });
            
            // Show loading state
            const $saveBtn = $('#rating-modal-save');
            const originalBtnText = $saveBtn.text();
            $saveBtn.prop('disabled', true).text('Saving...');
            
            // Send AJAX request to update condition rating
            $.ajax({
                url: `/surveyor/surveys/${surveyId}/assessments/${assessmentId}/condition-rating`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    condition_rating: selectedRating
                },
                success: function(response) {
                    console.log('Condition rating updated in backend:', response);
                    
                    // Close modal
                    $('#survey-data-mock-rating-modal').removeClass('show');
                    currentRatingSectionId = null;
                    selectedRating = null;
                    $('.survey-data-mock-rating-option').removeClass('selected');
                    $saveBtn.prop('disabled', false).text(originalBtnText);
                },
                error: function(xhr) {
                    console.error('Failed to update condition rating:', {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        response: xhr.responseJSON,
                        url: xhr.responseURL || `/surveyor/surveys/${surveyId}/assessments/${assessmentId}/condition-rating`
                    });
                    let errorMessage = 'Failed to update condition rating. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMessage = xhr.responseJSON.error;
                    } else if (xhr.status === 404) {
                        errorMessage = 'Assessment not found. Please save the accommodation first.';
                    } else if (xhr.status === 403) {
                        errorMessage = 'You are not authorized to update this rating.';
                    }
                    if (typeof toastr !== 'undefined') {
                        toastr.error(errorMessage);
                    } else {
                        alert(errorMessage);
                    }
                    $saveBtn.prop('disabled', false).text(originalBtnText);
                }
            });
        } else {
            // No assessment in backend - just update JS
            console.log('Rating updated in JS only (no backend assessment yet):', {
                sectionId: sectionId,
                rating: selectedRating
            });
            
            // Close modal
            $('#survey-data-mock-rating-modal').removeClass('show');
            currentRatingSectionId = null;
            selectedRating = null;
            $('.survey-data-mock-rating-option').removeClass('selected');
        }
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

    // Initialize Swiper carousels for a specific section only
    function initializeSectionCarousels($sectionItem) {
        $sectionItem.find('.survey-data-mock-button-group').each(function() {
            initializeSingleCarousel($(this));
        });
    }

    // Initialize a single carousel/button group
    function initializeSingleCarousel($buttonGroup) {
        // Skip if already initialized as Swiper
        if ($buttonGroup.hasClass('swiper-initialized')) {
            // If already a swiper, just update it
            if ($buttonGroup[0].swiper) {
                $buttonGroup[0].swiper.update();
            }
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
        
        // Skip if width is 0 (element not visible)
        if (availableWidth <= 0) {
            return;
        }
        
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
                    scrollInterval = setInterval(smoothScroll, 16);
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
                    scrollInterval = setInterval(smoothScroll, 16);
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
                
                // Force update after a short delay to ensure proper rendering
                setTimeout(function() {
                    swiper.update();
                    swiper.navigation.update();
                }, 50);
                
            } catch (error) {
                console.error('Swiper initialization error:', error, groupLabel);
            }
        }
    }

    // Initialize Swiper carousels for all visible button groups
    function initializeCarousels() {
        $('.survey-data-mock-button-group').each(function() {
            initializeSingleCarousel($(this));
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
                
                // Remove max-width constraints to allow proper resizing
                $leftColumn.css('max-width', 'none');
                $rightColumn.css('max-width', 'none');
                
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
                
                // Calculate new widths (min 20%, max 80% for each column, but right column min 300px)
                const minWidth = availableWidth * 0.2;
                const maxWidth = availableWidth * 0.8;
                const minRightWidth = 300; // Minimum width for right column to prevent content clipping
                
                let newLeftWidth = startLeftWidth + diffX;
                let newRightWidth = startRightWidth - diffX;
                
                // Constrain to min/max - ensure right column doesn't go below 300px
                if (newRightWidth < minRightWidth) {
                    newRightWidth = minRightWidth;
                    newLeftWidth = availableWidth - minRightWidth;
                } else if (newLeftWidth < minWidth) {
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
    
    // Initialize section states on page load
    function initializeSectionStates() {
        $('.survey-data-mock-section-item').each(function() {
            const $sectionItem = $(this);
            const $details = $sectionItem.find('.survey-data-mock-section-details');
            const $reportContent = $sectionItem.find('.survey-data-mock-report-content');
            const hasReport = $sectionItem.attr('data-has-report') === 'true' || $reportContent.attr('data-initial-has-report') === 'true';
            
            // Set active buttons based on saved data
            const selectedSection = $details.data('selected-section');
            const selectedLocation = $details.data('selected-location');
            const selectedStructure = $details.data('selected-structure');
            const selectedMaterial = $details.data('selected-material');
            const selectedDefects = $details.data('selected-defects') || [];
            const selectedRemainingLife = $details.data('selected-remaining-life');
            
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
            if (Array.isArray(selectedDefects)) {
                selectedDefects.forEach(function(defect) {
                    $details.find(`[data-group="defects"][data-value="${defect}"]`).addClass('active');
                });
            }
            if (selectedRemainingLife) {
                $details.find(`[data-group="remaining_life"][data-value="${selectedRemainingLife}"]`).addClass('active');
            }
            
            // Set initial visibility - both should be hidden initially (shown when expanded)
            $details.hide();
            $reportContent.hide();
        });
    }
    
    // Start initialization
    initOnReady();
    
    // Initialize section states after DOM is ready
    $(document).ready(function() {
        initializeSectionStates();
    });
    
    // Also initialize after full page load
    $(window).on('load', function() {
        if (isSwiperAvailable()) {
            setTimeout(function() {
                initializeCarousels();
                initializeDividers();
            }, 200);
        }
        initializeSectionStates();
    });
    
    // Reinitialize when sections expand (for dynamically added content, exclude content sections)
    $(document).on('click', '.survey-data-mock-section-header[data-expandable="true"]', function() {
        const $sectionItem = $(this).closest('.survey-data-mock-section-item');
        
        // Skip if this is a content section
        if ($sectionItem.hasClass('survey-data-mock-content-section-item')) {
            return;
        }
        
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
        const $details = $sectionItem.find('.survey-data-mock-section-details');
        const buttonValue = $button.data('value');
        
        // Validate location selection - check for duplicate section + location combination
        if (group === 'location') {
            const $subCategory = $sectionItem.closest('.survey-data-mock-sub-category');
            const currentSection = $details.find('[data-group="section"].active').data('value') || '';
            const currentSectionId = $sectionItem.data('section-id');
            
            // Check if this section + location combination already exists in another item
            let isDuplicate = false;
            $subCategory.find('.survey-data-mock-section-item').each(function() {
                const $item = $(this);
                if ($item.data('section-id') === currentSectionId) return true; // skip self
                
                const $itemDetails = $item.find('.survey-data-mock-section-details');
                const existingSection = $itemDetails.data('selected-section') || 
                                      $itemDetails.find('[data-group="section"].active').data('value') || '';
                const existingLocation = $itemDetails.find('[data-group="location"].active').data('value') || '';
                
                if (existingSection === currentSection && existingLocation === buttonValue) {
                    isDuplicate = true;
                    return false; // break loop
                }
            });
            
            if (isDuplicate) {
                alert(`"${currentSection}" with location "${buttonValue}" already exists in this sub-category. Please select a different location.`);
                return;
            }
        }
        
        // Validate section selection - check for duplicate section + location combination
        if (group === 'section') {
            const $subCategory = $sectionItem.closest('.survey-data-mock-sub-category');
            const currentLocation = $details.find('[data-group="location"].active').data('value') || '';
            const currentSectionId = $sectionItem.data('section-id');
            
            // Only validate if a location is already selected
            if (currentLocation) {
                let isDuplicate = false;
                $subCategory.find('.survey-data-mock-section-item').each(function() {
                    const $item = $(this);
                    if ($item.data('section-id') === currentSectionId) return true; // skip self
                    
                    const $itemDetails = $item.find('.survey-data-mock-section-details');
                    const existingSection = $itemDetails.data('selected-section') || 
                                          $itemDetails.find('[data-group="section"].active').data('value') || '';
                    const existingLocation = $itemDetails.find('[data-group="location"].active').data('value') || '';
                    
                    if (existingSection === buttonValue && existingLocation === currentLocation) {
                        isDuplicate = true;
                        return false; // break loop
                    }
                });
                
                if (isDuplicate) {
                    alert(`"${buttonValue}" with location "${currentLocation}" already exists in this sub-category. Please select a different section or change the location.`);
                    return;
                }
            }
        }
        
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
        
        // Update header with section name and location
        if (group === 'section' || group === 'location') {
            updateSectionHeader($sectionItem);
            // Also update data attributes for validation
            if (group === 'section') {
                $details.data('selected-section', buttonValue);
            }
            if (group === 'location') {
                $details.data('selected-location', buttonValue);
            }
        }
        
        // Store selection
        const sectionId = $sectionItem.data('section-id');
        console.log('Section', sectionId, 'Group', group, 'Value', buttonValue);
    });
    
    // Function to update section header with section name and location
    function updateSectionHeader($sectionItem) {
        // Skip content sections - they have their own title that shouldn't be changed
        if ($sectionItem.hasClass('survey-data-mock-content-section-item')) {
            return;
        }
        
        const $details = $sectionItem.find('.survey-data-mock-section-details');
        const $headerName = $sectionItem.find('.survey-data-mock-section-name');
        const $titleText = $sectionItem.find('.survey-data-mock-section-title-text');
        
        const selectedSection = $details.find('[data-group="section"].active').data('value') || '';
        const selectedLocation = $details.find('[data-group="location"].active').data('value') || '';
        
        // Build display name: "Section [Location]"
        let displayName = selectedSection || 'Select Section';
        if (selectedLocation) {
            displayName = `${selectedSection} [${selectedLocation}]`;
        }
        
        $headerName.text(displayName);
        $titleText.text(displayName);
    }

    // Initialize button states from mock data
    $('.survey-data-mock-section-item').each(function() {
        const $sectionItem = $(this);
        
        // Skip content sections - they don't have button selections
        if ($sectionItem.hasClass('survey-data-mock-content-section-item')) {
            return;
        }
        
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
        
        // Update header with section name and location
        updateSectionHeader($sectionItem);
    });

    // Cost Management Functionality
    let currentCostSectionItem = null;
    let currentCostIndex = null;

    // Add Cost Button
    $(document).on('click', '.survey-data-mock-add-cost-btn', function(e) {
        e.stopPropagation();
        const $sectionItem = $(this).closest('.survey-data-mock-section-item');
        currentCostSectionItem = $sectionItem;
        currentCostIndex = null;
        
        // Reset modal form
        $('#cost-modal-category').val('');
        $('#cost-modal-description').val('');
        $('#cost-modal-due').val('');
        $('#cost-modal-cost').val('');
        $('.survey-data-mock-cost-modal-title').text('Add Cost');
        
        // Show modal
        $('#survey-data-mock-cost-modal').addClass('show');
    });

    // Edit Cost Button
    $(document).on('click', '.survey-data-mock-cost-edit-btn', function(e) {
        e.stopPropagation();
        e.preventDefault();
        const $sectionItem = $(this).closest('.survey-data-mock-section-item');
        const $row = $(this).closest('tr');
        const costIndex = parseInt($row.data('cost-index'));
        
        currentCostSectionItem = $sectionItem;
        currentCostIndex = costIndex;
        
        // Get cost data from table row
        const category = $row.find('td:eq(0)').text().trim();
        const description = $row.find('td:eq(1)').text().trim();
        const due = $row.find('td:eq(2)').text().trim();
        const cost = $row.find('td:eq(3)').text().trim();
        
        // Populate modal form
        $('#cost-modal-category').val(category);
        $('#cost-modal-description').val(description);
        $('#cost-modal-due').val(due);
        $('#cost-modal-cost').val(cost);
        $('.survey-data-mock-cost-modal-title').text('Edit Cost');
        
        // Show modal
        $('#survey-data-mock-cost-modal').addClass('show');
    });

    // Delete Cost Button
    $(document).on('click', '.survey-data-mock-cost-delete-btn', function(e) {
        e.stopPropagation();
        e.preventDefault();
        const $sectionItem = $(this).closest('.survey-data-mock-section-item');
        const $row = $(this).closest('tr');
        
        if (confirm('Are you sure you want to delete this cost?')) {
            $row.fadeOut(300, function() {
                $(this).remove();
                updateCostsTable($sectionItem);
                
                // Check if assessment exists in backend (sectionId is numeric)
                const sectionId = $sectionItem.data('section-id');
                const isNumericId = /^\d+$/.test(sectionId.toString());
                
                if (isNumericId) {
                    // Assessment exists - save updated costs to backend immediately
                    const surveyId = $('.survey-data-mock-content').data('survey-id');
                    const assessmentId = parseInt(sectionId);
                    
                    // Collect all remaining costs from table
                    const $table = $sectionItem.find('.survey-data-mock-costs-table tbody');
                    const costs = [];
                    $table.find('tr[data-cost-index]').each(function() {
                        const $costRow = $(this);
                        const cat = $costRow.find('td:eq(0)').text().trim();
                        const desc = $costRow.find('td:eq(1)').text().trim();
                        const dueYear = $costRow.find('td:eq(2)').text().trim();
                        const costAmount = $costRow.find('td:eq(3)').text().trim();
                        
                        if (cat && cat !== 'No costs added') {
                            costs.push({
                                category: cat,
                                description: desc,
                                due: dueYear,
                                cost: costAmount
                            });
                        }
                    });
                    
                    // Send AJAX request to update costs
                    $.ajax({
                        url: `/surveyor/surveys/${surveyId}/assessments/${assessmentId}/costs`,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            costs: costs
                        },
                        success: function(response) {
                            console.log('Costs updated in backend after delete:', response);
                        },
                        error: function(xhr) {
                            console.error('Failed to update costs:', xhr.responseJSON);
                            alert('Failed to update costs. Please try again.');
                        }
                    });
                }
            });
        }
    });

    // Save Cost from Modal
    $('#cost-modal-save').on('click', function() {
        const category = $('#cost-modal-category').val().trim();
        const description = $('#cost-modal-description').val().trim();
        const due = $('#cost-modal-due').val().trim();
        const cost = $('#cost-modal-cost').val().trim();
        
        // Validate
        if (!category || !description) {
            alert('Please fill in Category and Description fields.');
            return;
        }
        
        if (!currentCostSectionItem) {
            alert('Error: Section item not found.');
            return;
        }
        
        const $table = currentCostSectionItem.find('.survey-data-mock-costs-table tbody');
        
        if (currentCostIndex !== null) {
            // Edit existing cost
            const $row = $table.find(`tr[data-cost-index="${currentCostIndex}"]`);
            $row.find('td:eq(0)').text(category);
            $row.find('td:eq(1)').text(description);
            $row.find('td:eq(2)').text(due);
            $row.find('td:eq(3)').text(cost);
        } else {
            // Add new cost
            const $noCostsRow = $table.find('tr:has(.survey-data-mock-no-costs)');
            if ($noCostsRow.length > 0) {
                $noCostsRow.remove();
            }
            
            // Get next index
            const maxIndex = $table.find('tr[data-cost-index]').length > 0 
                ? Math.max(...$table.find('tr[data-cost-index]').map(function() {
                    return parseInt($(this).data('cost-index')) || 0;
                }).get())
                : -1;
            const newIndex = maxIndex + 1;
            
            const $newRow = $('<tr data-cost-index="' + newIndex + '">' +
                '<td>' + category + '</td>' +
                '<td>' + description + '</td>' +
                '<td>' + due + '</td>' +
                '<td>' + cost + '</td>' +
                '<td>' +
                    '<div class="survey-data-mock-cost-actions">' +
                        '<button type="button" class="survey-data-mock-cost-edit-btn" data-cost-index="' + newIndex + '">' +
                            '<i class="fas fa-edit"></i>' +
                        '</button>' +
                        '<button type="button" class="survey-data-mock-cost-delete-btn" data-cost-index="' + newIndex + '">' +
                            '<i class="fas fa-trash"></i>' +
                        '</button>' +
                    '</div>' +
                '</td>' +
            '</tr>');
            
            $table.append($newRow);
        }
        
        updateCostsTable(currentCostSectionItem);
        
        // Check if assessment exists in backend (sectionId is numeric)
        const sectionId = currentCostSectionItem.data('section-id');
        const isNumericId = /^\d+$/.test(sectionId.toString());
        
        if (isNumericId) {
            // Assessment exists - save costs to backend immediately
            const surveyId = $('.survey-data-mock-content').data('survey-id');
            const assessmentId = parseInt(sectionId);
            
            // Collect all costs from table
            const costs = [];
            $table.find('tr[data-cost-index]').each(function() {
                const $row = $(this);
                const cat = $row.find('td:eq(0)').text().trim();
                const desc = $row.find('td:eq(1)').text().trim();
                const dueYear = $row.find('td:eq(2)').text().trim();
                const costAmount = $row.find('td:eq(3)').text().trim();
                
                if (cat && cat !== 'No costs added') {
                    costs.push({
                        category: cat,
                        description: desc,
                        due: dueYear,
                        cost: costAmount
                    });
                }
            });
            
            // Show loading state
            const $saveBtn = $('#cost-modal-save');
            const originalBtnText = $saveBtn.text();
            $saveBtn.prop('disabled', true).text('Saving...');
            
            // Send AJAX request to update costs
            $.ajax({
                url: `/surveyor/surveys/${surveyId}/assessments/${assessmentId}/costs`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    costs: costs
                },
                success: function(response) {
                    console.log('Costs saved to backend:', response);
                    $saveBtn.prop('disabled', false).text(originalBtnText);
                    
                    // Close modal
                    $('#survey-data-mock-cost-modal').removeClass('show');
                    currentCostSectionItem = null;
                    currentCostIndex = null;
                },
                error: function(xhr) {
                    console.error('Failed to save costs:', xhr.responseJSON);
                    alert('Failed to save costs. Please try again.');
                    $saveBtn.prop('disabled', false).text(originalBtnText);
                }
            });
        } else {
            // No assessment in backend - just update JS, will be saved on form submit
            console.log('Costs updated in JS only (no backend assessment yet)');
            
            // Close modal
            $('#survey-data-mock-cost-modal').removeClass('show');
            currentCostSectionItem = null;
            currentCostIndex = null;
        }
    });

    // Close Cost Modal
    $('#cost-modal-close, #cost-modal-cancel').on('click', function() {
        $('#survey-data-mock-cost-modal').removeClass('show');
        currentCostSectionItem = null;
        currentCostIndex = null;
    });

    // Close modal on background click
    $('#survey-data-mock-cost-modal').on('click', function(e) {
        if ($(e.target).hasClass('survey-data-mock-cost-modal')) {
            $(this).removeClass('show');
            currentCostSectionItem = null;
            currentCostIndex = null;
        }
    });

    // Update costs table (ensure no empty row if costs exist)
    function updateCostsTable($sectionItem) {
        const $table = $sectionItem.find('.survey-data-mock-costs-table tbody');
        const $costRows = $table.find('tr[data-cost-index]');
        
        if ($costRows.length === 0) {
            // Add "No costs" row if table is empty
            if ($table.find('tr:has(.survey-data-mock-no-costs)').length === 0) {
                $table.append('<tr><td colspan="5" class="survey-data-mock-no-costs">No costs added</td></tr>');
            }
        }
    }

    // Microphone Button
    $('.survey-data-mock-mic-btn').on('click', function(e) {
        e.stopPropagation();
        alert('Voice input functionality - to be implemented');
        // TODO: Implement voice input
    });

    // Image Upload Functionality
    // Initialize image upload for each section
    function initializeImageUpload($sectionItem) {
        const $uploadArea = $sectionItem.find('.survey-data-mock-images-upload');
        const $fileInput = $sectionItem.find('.survey-data-mock-file-input');
        const $previewArea = $sectionItem.find('.survey-data-mock-images-preview');
        const $previewGrid = $sectionItem.find('.survey-data-mock-images-grid');
        const $existingImages = $sectionItem.find('.survey-data-mock-existing-images');
        
        // Store selected files per section
        if (!$sectionItem.data('selectedFiles')) {
            $sectionItem.data('selectedFiles', []);
        }
        
        // Click on upload area to trigger file input
        $uploadArea.on('click', function(e) {
            e.stopPropagation();
            $fileInput.click();
        });
        
        // File input change handler
        $fileInput.on('change', function(e) {
            e.stopPropagation();
            const files = Array.from(this.files);
            handleFilesSelected($sectionItem, files);
        });
        
        // Drag and drop handlers
        $uploadArea.on('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).addClass('dragover');
        });
        
        $uploadArea.on('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).removeClass('dragover');
        });
        
        $uploadArea.on('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).removeClass('dragover');
            
            const files = Array.from(e.originalEvent.dataTransfer.files).filter(file => file.type.startsWith('image/'));
            if (files.length > 0) {
                handleFilesSelected($sectionItem, files);
            }
        });
        
        // Delete button for existing images
        $sectionItem.on('click', '.survey-data-mock-image-delete', function(e) {
            e.stopPropagation();
            e.preventDefault();
            const $imageItem = $(this).closest('.survey-data-mock-image-item');
            const photoId = $imageItem.data('photo-id');
            
            if (photoId && confirm('Are you sure you want to delete this image?')) {
                deleteExistingPhoto($sectionItem, photoId, $imageItem);
            }
        });
    }
    
    // Handle selected files
    function handleFilesSelected($sectionItem, files) {
        const selectedFiles = $sectionItem.data('selectedFiles') || [];
        const validFiles = files.filter(file => file.type.startsWith('image/'));
        
        // Check if assessment exists in backend (sectionId is numeric)
        const sectionId = $sectionItem.data('section-id');
        const isNumericId = /^\d+$/.test(sectionId.toString());
        
        if (isNumericId && validFiles.length > 0) {
            // Assessment exists - upload photos to backend immediately
            const surveyId = $('.survey-data-mock-content').data('survey-id');
            const assessmentId = parseInt(sectionId);
            
            // Create FormData for file upload
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            validFiles.forEach((file, index) => {
                formData.append(`photos[${index}]`, file);
            });
            
            // Show loading state
            const $uploadArea = $sectionItem.find('.survey-data-mock-images-upload');
            const originalText = $uploadArea.find('.survey-data-mock-upload-text').text();
            $uploadArea.find('.survey-data-mock-upload-text').text('Uploading...');
            $uploadArea.css('pointer-events', 'none');
            
            // Send AJAX request to upload photos
            $.ajax({
                url: `/surveyor/surveys/${surveyId}/assessments/${assessmentId}/photos`,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log('Photos uploaded to backend:', response);
                    
                    // Add uploaded photos to existing images display
                    if (response.photos && response.photos.length > 0) {
                        let $existingContainer = $sectionItem.find('.survey-data-mock-existing-images');
                        let $existingGrid = $existingContainer.find('.survey-data-mock-images-grid');
                        
                        if ($existingContainer.length === 0) {
                            // Create existing images container if it doesn't exist
                            $existingContainer = $('<div class="survey-data-mock-existing-images" style="margin-top: 1rem;">');
                            $existingGrid = $('<div class="survey-data-mock-images-grid">');
                            $existingContainer.append($existingGrid);
                            $sectionItem.find('.survey-data-mock-images-upload').after($existingContainer);
                        }
                        
                        response.photos.forEach(function(photo) {
                            const $imageItem = $('<div class="survey-data-mock-image-item" data-photo-id="' + photo.id + '">');
                            const $img = $('<img src="' + photo.url + '" alt="Photo" class="survey-data-mock-image-thumbnail">');
                            const $deleteBtn = $('<button type="button" class="survey-data-mock-image-delete" data-photo-id="' + photo.id + '">')
                                .html('<i class="fas fa-times"></i>');
                            
                            $imageItem.append($img).append($deleteBtn);
                            $existingGrid.append($imageItem);
                        });
                        
                        $existingContainer.show();
                    }
                    
                    // Reset upload area
                    $uploadArea.find('.survey-data-mock-upload-text').text(originalText);
                    $uploadArea.css('pointer-events', 'auto');
                    
                    // Clear file input
                    $sectionItem.find('.survey-data-mock-file-input').val('');
                },
                error: function(xhr) {
                    console.error('Failed to upload photos:', xhr.responseJSON);
                    alert('Failed to upload photos. Please try again.');
                    
                    // Reset upload area
                    $uploadArea.find('.survey-data-mock-upload-text').text(originalText);
                    $uploadArea.css('pointer-events', 'auto');
                    
                    // Still add to preview for form submit
                    validFiles.forEach(file => {
                        const isDuplicate = selectedFiles.some(f => f.name === file.name && f.size === file.size);
                        if (!isDuplicate) {
                            selectedFiles.push(file);
                        }
                    });
                    $sectionItem.data('selectedFiles', selectedFiles);
                    updateImagePreview($sectionItem);
                }
            });
        } else {
            // No assessment in backend - just update JS, will be saved on form submit
            validFiles.forEach(file => {
                // Check if file already selected (by name and size)
                const isDuplicate = selectedFiles.some(f => f.name === file.name && f.size === file.size);
                if (!isDuplicate) {
                    selectedFiles.push(file);
                }
            });
            
            $sectionItem.data('selectedFiles', selectedFiles);
            updateImagePreview($sectionItem);
        }
    }
    
    // Update image preview display
    function updateImagePreview($sectionItem) {
        const selectedFiles = $sectionItem.data('selectedFiles') || [];
        const $previewArea = $sectionItem.find('.survey-data-mock-images-preview');
        const $previewGrid = $sectionItem.find('.survey-data-mock-images-preview .survey-data-mock-images-grid');
        
        if (selectedFiles.length === 0) {
            $previewArea.hide();
            return;
        }
        
        $previewGrid.empty();
        selectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const $imageItem = $('<div class="survey-data-mock-image-item" data-file-index="' + index + '">');
                const $img = $('<img src="' + e.target.result + '" alt="Preview" class="survey-data-mock-image-thumbnail">');
                const $deleteBtn = $('<button type="button" class="survey-data-mock-image-delete" data-file-index="' + index + '">')
                    .html('<i class="fas fa-times"></i>');
                
                $imageItem.append($img).append($deleteBtn);
                $previewGrid.append($imageItem);
            };
            reader.readAsDataURL(file);
        });
        
        $previewArea.show();
    }
    
    // Delete preview image
    $(document).on('click', '.survey-data-mock-image-delete[data-file-index]', function(e) {
        e.stopPropagation();
        e.preventDefault();
        const $sectionItem = $(this).closest('.survey-data-mock-section-item');
        const fileIndex = parseInt($(this).data('file-index'));
        const selectedFiles = $sectionItem.data('selectedFiles') || [];
        
        // Remove file from selection
        selectedFiles.splice(fileIndex, 1);
        $sectionItem.data('selectedFiles', selectedFiles);
        
        // Update preview
        updateImagePreview($sectionItem);
    });
    
    // Delete existing photo from server
    function deleteExistingPhoto($sectionItem, photoId, $imageItem) {
        const surveyId = $('.survey-data-mock-content').data('survey-id');
        const assessmentId = $sectionItem.data('section-id');
        
        // Check if assessmentId is numeric (saved assessment)
        if (!assessmentId || !/^\d+$/.test(assessmentId)) {
            alert('Please save the assessment first before deleting photos.');
            return;
        }
        
        $.ajax({
            url: `/surveyor/surveys/${surveyId}/assessments/${assessmentId}/photos/${photoId}/delete`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    $imageItem.fadeOut(300, function() {
                        $(this).remove();
                        // Hide existing images container if empty
                        const $existingContainer = $sectionItem.find('.survey-data-mock-existing-images');
                        if ($existingContainer.find('.survey-data-mock-image-item').length === 0) {
                            $existingContainer.hide();
                        }
                    });
                    
                    if (typeof toastr !== 'undefined') {
                        toastr.success('Photo deleted successfully');
                    }
                }
            },
            error: function(xhr) {
                let errorMessage = 'Failed to delete photo. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }
                
                if (typeof toastr !== 'undefined') {
                    toastr.error(errorMessage);
                } else {
                    alert(errorMessage);
                }
            }
        });
    }
    
    // Image upload initialization moved to enhanced upload system below
    // Old initializeImageUpload disabled to prevent duplicate uploads

    // Action Buttons
    $('.survey-data-mock-action-delete').on('click', function(e) {
        e.stopPropagation();
        if (confirm('Are you sure you want to delete this section?')) {
            alert('Delete functionality - to be implemented');
            // TODO: Implement delete
        }
    });

    // Removed duplicate clone handler - using delegated handler below with specific selector

    // Save Button Handler
    $(document).on('click', '.survey-data-mock-action-save', function(e) {
        e.stopPropagation();
        const $button = $(this);
        const sectionId = $button.data('section-id');
        const $sectionItem = $button.closest('.survey-data-mock-section-item');
        const sectionDefinitionId = $sectionItem.data('section-definition-id');
        const $details = $sectionItem.find('.survey-data-mock-section-details');
        const $reportContent = $sectionItem.find('.survey-data-mock-report-content');
        const $categorySection = $sectionItem.closest('.survey-data-mock-category');
        const categoryName = $categorySection.find('.survey-data-mock-category-title').text().trim();
        const sectionName = $sectionItem.find('.survey-data-mock-section-name').text().trim();
        
        // Get survey ID from header
        const surveyId = $('.survey-data-mock-content').data('survey-id');
        
        // Validate required data
        if (!surveyId || !sectionDefinitionId) {
            alert('Error: Missing survey or section information. Please refresh the page.');
            return;
        }
        
        // Collect all form data
        const formData = {
            section: $details.find('[data-group="section"].active').data('value') || '',
            location: $details.find('[data-group="location"].active').data('value') || '',
            structure: $details.find('[data-group="structure"].active').data('value') || '',
            material: $details.find('[data-group="material"].active').data('value') || '',
            defects: $details.find('[data-group="defects"].active').map(function() {
                return $(this).data('value');
            }).get(),
            remaining_life: $details.find('[data-group="remaining_life"].active').data('value') || '',
            notes: $details.find('.survey-data-mock-notes-input').val() || '',
            costs: []
        };
        
        // Collect costs
        $details.find('.survey-data-mock-costs-table tbody tr[data-cost-index]').each(function() {
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
        
        // Get condition rating
        const conditionRating = $sectionItem.find('.survey-data-mock-condition-badge').data('current-rating') || 'ni';
        formData.condition_rating = conditionRating;
        
        // Get selected image files
        const selectedFiles = $sectionItem.data('selectedFiles') || [];
        
        // Create FormData for file upload
        const formDataObj = new FormData();
        formDataObj.append('_token', '{{ csrf_token() }}');
        formDataObj.append('section_id', sectionId);
        formDataObj.append('section', formData.section);
        formDataObj.append('location', formData.location);
        formDataObj.append('structure', formData.structure);
        formDataObj.append('material', formData.material);
        formDataObj.append('remaining_life', formData.remaining_life);
        formDataObj.append('notes', formData.notes);
        formDataObj.append('condition_rating', conditionRating);
        
        // Append defects array
        if (formData.defects && formData.defects.length > 0) {
            formData.defects.forEach((defect, index) => {
                formDataObj.append(`defects[${index}]`, defect);
            });
        }
        
        // Append costs array
        if (formData.costs && formData.costs.length > 0) {
            formData.costs.forEach((cost, index) => {
                formDataObj.append(`costs[${index}][category]`, cost.category || '');
                formDataObj.append(`costs[${index}][description]`, cost.description || '');
                formDataObj.append(`costs[${index}][due]`, cost.due || '');
                formDataObj.append(`costs[${index}][cost]`, cost.cost || '');
            });
        }
        
        // Append image files
        selectedFiles.forEach((file, index) => {
            formDataObj.append(`photos[${index}]`, file);
        });
        
        // Show loading state
        $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        
        // Send AJAX request to save assessment
        $.ajax({
            url: `/surveyor/surveys/${surveyId}/sections/${sectionDefinitionId}/save`,
            method: 'POST',
            data: formDataObj,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    // Update section ID if it's a new assessment (clones get real database IDs)
                    if (response.assessment_id && typeof response.assessment_id === 'number') {
                        $sectionItem.attr('data-section-id', response.assessment_id);
                        // Update all references to the section ID
                        $sectionItem.find('[data-section-id]').attr('data-section-id', response.assessment_id);
                    }
                    
                    // Clear selected files after successful save
                    $sectionItem.data('selectedFiles', []);
                    $sectionItem.find('.survey-data-mock-images-preview').hide();
                    $sectionItem.find('.survey-data-mock-images-preview .survey-data-mock-images-grid').empty();
                    
                    // Hide form, show report
                    $details.slideUp(300);
                    $reportContent.find('.survey-data-mock-report-textarea').val(response.report_content || '');
                    $reportContent.slideDown(300);
                    
                    // Mark section as saved and has report
                    $sectionItem.attr('data-saved', 'true');
                    $sectionItem.attr('data-has-report', response.report_content ? 'true' : 'false');
                    $sectionItem.attr('data-locked', 'false');
                    
                    // Initialize lock state (unlocked by default)
                    updateLockState($sectionItem, false);
                    
                    // Note: Images will be visible after page refresh or when section is reopened
                    // The uploaded images are now saved in the database
                    
                    // Show success message
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message || 'Assessment saved successfully');
                    } else {
                        console.log('Assessment saved successfully');
                    }
                } else {
                    throw new Error(response.error || 'Failed to save assessment');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Failed to save assessment. Please try again.';
                
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    // Validation errors
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMessage = errors.join('\n');
                } else if (xhr.status === 403) {
                    errorMessage = 'You are not authorized to save this assessment.';
                } else if (xhr.status === 404) {
                    errorMessage = 'Section not found. Please refresh the page.';
                }
                
                if (typeof toastr !== 'undefined') {
                    toastr.error(errorMessage);
                } else {
                    alert(errorMessage);
                }
                
                console.error('Error saving assessment:', xhr);
            },
            complete: function() {
                // Reset button state
                $button.prop('disabled', false).html('Save');
            }
        });
    });

    // Helper function to update lock state
    function updateLockState($sectionItem, isLocked) {
        const $reportContent = $sectionItem.find('.survey-data-mock-report-content');
        const $textarea = $reportContent.find('.survey-data-mock-report-textarea');
        const $lockBtn = $reportContent.find('[data-action="lock"]');
        
        if (isLocked) {
            $textarea.prop('disabled', true);
            $lockBtn.addClass('locked active');
            $lockBtn.find('i').removeClass('fa-lock').addClass('fa-lock-open');
            $sectionItem.attr('data-locked', 'true');
        } else {
            $textarea.prop('disabled', false);
            $lockBtn.removeClass('locked active');
            $lockBtn.find('i').removeClass('fa-lock-open').addClass('fa-lock');
            $sectionItem.attr('data-locked', 'false');
        }
    }

    // Action Icons Handlers
    $(document).on('click', '.survey-data-mock-action-icon-btn', function(e) {
        e.stopPropagation();
        const $button = $(this);
        const action = $button.data('action');
        const $sectionItem = $button.closest('.survey-data-mock-section-item');
        const $reportContent = $sectionItem.find('.survey-data-mock-report-content');
        const $details = $sectionItem.find('.survey-data-mock-section-details');
        const $categorySection = $sectionItem.closest('.survey-data-mock-category');
        const categoryName = $categorySection.find('.survey-data-mock-category-title').text().trim();
        const sectionName = $sectionItem.find('.survey-data-mock-section-name').text().trim();
        
        switch(action) {
            case 'speaker':
                // Text to Speech placeholder
                alert('Text to Speech functionality - to be implemented');
                break;
                
            case 'lock':
                // Toggle lock state
                const isCurrentlyLocked = $sectionItem.attr('data-locked') === 'true';
                updateLockState($sectionItem, !isCurrentlyLocked);
                break;
                
            case 'edit':
                // Toggle back to form view
                $reportContent.slideUp(300);
                $details.slideDown(300, function() {
                    // Re-initialize carousels after the form is fully visible
                    if (isSwiperAvailable()) {
                        initializeSectionCarousels($sectionItem);
                    }
                });
                break;
                
            case 'refresh':
                // Regenerate content
                const $detailsForRefresh = $sectionItem.find('.survey-data-mock-section-details');
                const isAccommodationRefresh = $sectionItem.attr('data-accommodation-id') !== undefined;
                
                if (isAccommodationRefresh) {
                    // Regenerate accommodation report
                    const accommodationFormData = {
                        notes: $detailsForRefresh.find('.survey-data-mock-notes-input').val() || '',
                        components: []
                    };
                    
                    // Collect component data from carousel slides
                    $detailsForRefresh.find('.survey-data-mock-carousel-slide').each(function() {
                        const $slide = $(this);
                        const componentKey = $slide.data('component-key');
                        const componentName = $sectionItem.find(`.survey-data-mock-component-tab[data-component-key="${componentKey}"]`).text().trim() || componentKey;
                        
                        if (componentKey) {
                            accommodationFormData.components.push({
                                component_key: componentKey,
                                component_name: componentName,
                                material: $slide.find('[data-group="material"].active').data('value') || '',
                                defects: $slide.find('[data-group="defects"].active').map(function() {
                                    return $(this).data('value');
                                }).get()
                            });
                        }
                    });
                    
                    const newAccommodationReport = generateAccommodationReportContent(accommodationFormData, sectionName);
                    $reportContent.find('.survey-data-mock-report-textarea').val(newAccommodationReport);
                } else {
                    // Regenerate regular section report
                    const formData = {
                        section: $detailsForRefresh.find('[data-group="section"].active').data('value') || '',
                        location: $detailsForRefresh.find('[data-group="location"].active').data('value') || '',
                        structure: $detailsForRefresh.find('[data-group="structure"].active').data('value') || '',
                        material: $detailsForRefresh.find('[data-group="material"].active').data('value') || '',
                        defects: $detailsForRefresh.find('[data-group="defects"].active').map(function() {
                            return $(this).data('value');
                        }).get(),
                        remainingLife: $detailsForRefresh.find('[data-group="remaining_life"].active').data('value') || '',
                        notes: $detailsForRefresh.find('.survey-data-mock-notes-input').val() || '',
                        costs: []
                    };
                    
                    // Collect costs
                    $detailsForRefresh.find('.survey-data-mock-costs-table tbody tr[data-cost-index]').each(function() {
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
                    
                    const newReportContent = generateMockReportContent(formData, sectionName, categoryName);
                    $reportContent.find('.survey-data-mock-report-textarea').val(newReportContent);
                }
                break;
                
            case 'eye':
                // Preview placeholder
                alert('Preview functionality - to be implemented');
                break;
        }
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
                    <div class="survey-data-mock-clone-modal-field">
                        <label class="survey-data-mock-clone-modal-label">Select Location</label>
                        <div class="survey-data-mock-clone-section-buttons" id="clone-location-buttons">
                            <!-- Location buttons will be dynamically generated -->
                        </div>
                        <p class="survey-data-mock-clone-modal-help" id="clone-location-error" style="color: #EF4444; display: none;"></p>
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
    let currentCloneSectionDefinitionId = null;
    let currentCloneData = null;
    let currentCloneCategory = null;
    let selectedCloneSection = null;
    let selectedCloneLocation = null;
    let currentCloneSubCategory = null;

    // Open Clone Modal (only for regular sections, not accommodations)
    // Use more specific selector to avoid conflicts with accommodation handler
    $(document).on('click', '.survey-data-mock-section-item:not([data-accommodation-id]) .survey-data-mock-action-clone', function(e) {
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
        $details.find('.survey-data-mock-costs-table tbody tr[data-cost-index]').each(function() {
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
        // Get and convert section definition ID to integer
        let sectionDefId = $sectionItem.data('section-definition-id');
        if (sectionDefId) {
            sectionDefId = parseInt(sectionDefId, 10);
        }
        currentCloneSectionDefinitionId = sectionDefId;
        currentCloneData = formData;
        currentCloneCategory = categoryName;
        selectedCloneSection = null;
        selectedCloneLocation = null;
        
        const $subCategory = $sectionItem.closest('.survey-data-mock-sub-category');
        currentCloneSubCategory = $subCategory;

        // Get available sections based on category - use dynamic options mapping
        let allSections = [];
        
        // Get section options from mapping
        const sectionOptions = getOptions(categoryName, 'section', []);
        
        if (sectionOptions && sectionOptions.length > 0) {
            allSections = sectionOptions;
        } else {
            // Fallback: get from button group
            $details.find('[data-group="section"]').each(function() {
                const sectionValue = $(this).data('value');
                if (sectionValue) {
                    allSections.push(sectionValue);
                }
            });
        }

        // Generate section buttons in modal - NO RESTRICTION on sections
        const $cloneButtons = $('#clone-section-buttons');
        $cloneButtons.empty();
        
        allSections.forEach(function(section) {
            const $btn = $('<button>')
                .addClass('survey-data-mock-clone-section-btn')
                .attr('type', 'button')
                .data('section', section)
                .text(section);
            
            $cloneButtons.append($btn);
        });
        
        // Generate location buttons
        const $locationButtons = $('#clone-location-buttons');
        $locationButtons.empty();
        $('#clone-location-error').hide();
        
        const locationOptions = optionsMapping['location'] || ['Whole Property', 'Right', 'Left', 'Front', 'Rear'];
        locationOptions.forEach(function(location) {
            const $btn = $('<button>')
                .addClass('survey-data-mock-clone-location-btn')
                .attr('type', 'button')
                .data('location', location)
                .text(location);
            
            $locationButtons.append($btn);
        });
        
        // Show modal
        $('#survey-data-mock-clone-modal').addClass('show');
        
        // Enable/disable clone button based on selection
        updateCloneButtonState();
    });

    // Handle section selection in clone modal
    // Handle section selection in clone modal
    $(document).on('click', '.survey-data-mock-clone-section-btn', function() {
        const $btn = $(this);
        
        $('.survey-data-mock-clone-section-btn').removeClass('active');
        $btn.addClass('active');
        selectedCloneSection = $btn.data('section');
        
        // Validate location when section changes
        validateCloneSelection();
        updateCloneButtonState();
    });
    
    // Handle location selection in clone modal
    $(document).on('click', '.survey-data-mock-clone-location-btn', function() {
        const $btn = $(this);
        
        $('.survey-data-mock-clone-location-btn').removeClass('active');
        $btn.addClass('active');
        selectedCloneLocation = $btn.data('location');
        
        // Validate the combination
        validateCloneSelection();
        updateCloneButtonState();
    });
    
    // Validate that section + location combination doesn't already exist
    function validateCloneSelection() {
        if (!selectedCloneSection || !selectedCloneLocation || !currentCloneSubCategory) {
            $('#clone-location-error').hide();
            return true;
        }
        
        // Check if this section + location combination already exists
        let isDuplicate = false;
        currentCloneSubCategory.find('.survey-data-mock-section-item').each(function() {
            const $item = $(this);
            const $itemDetails = $item.find('.survey-data-mock-section-details');
            if ($itemDetails.length > 0) {
                const existingSection = $itemDetails.data('selected-section') || 
                                      $itemDetails.find('[data-group="section"].active').data('value') || '';
                const existingLocation = $itemDetails.find('[data-group="location"].active').data('value') || '';
                
                if (existingSection === selectedCloneSection && existingLocation === selectedCloneLocation) {
                    isDuplicate = true;
                    return false; // break loop
                }
            }
        });
        
        if (isDuplicate) {
            $('#clone-location-error').text(`"${selectedCloneSection}" with location "${selectedCloneLocation}" already exists. Please select a different location.`).show();
            return false;
        } else {
            $('#clone-location-error').hide();
            return true;
        }
    }

    function updateCloneButtonState() {
        const isValid = selectedCloneSection && selectedCloneLocation && validateCloneSelection();
        $('#clone-modal-confirm').prop('disabled', !isValid);
    }

    // Close Clone Modal
    $('#survey-data-mock-clone-modal-close, #clone-modal-cancel').on('click', function() {
        $('#survey-data-mock-clone-modal').removeClass('show');
        currentCloneSectionId = null;
        currentCloneSectionDefinitionId = null;
        currentCloneData = null;
        currentCloneCategory = null;
        currentCloneSubCategory = null;
        selectedCloneSection = null;
        selectedCloneLocation = null;
        $('.survey-data-mock-clone-section-btn').removeClass('active');
        $('.survey-data-mock-clone-location-btn').removeClass('active');
        $('#clone-location-error').hide();
        $('#clone-modal-confirm').prop('disabled', true);
    });

    // Close modal on background click
    $('#survey-data-mock-clone-modal').on('click', function(e) {
        if ($(e.target).hasClass('survey-data-mock-clone-modal')) {
            $(this).removeClass('show');
            currentCloneSectionId = null;
            currentCloneSectionDefinitionId = null;
            currentCloneData = null;
            currentCloneCategory = null;
            currentCloneSubCategory = null;
            selectedCloneSection = null;
            selectedCloneLocation = null;
            $('.survey-data-mock-clone-section-btn').removeClass('active');
            $('.survey-data-mock-clone-location-btn').removeClass('active');
            $('#clone-location-error').hide();
            $('#clone-modal-confirm').prop('disabled', true);
        }
    });

    // Confirm Clone
    $('#clone-modal-confirm').on('click', function() {
        if (!selectedCloneSection) {
            alert('Please select a target section');
            return;
        }
        
        if (!selectedCloneLocation) {
            alert('Please select a location');
            return;
        }
        
        if (!validateCloneSelection()) {
            return;
        }

        // Use specific selector to only match regular sections (not accommodations)
        const $sourceItem = $(`.survey-data-mock-section-item:not([data-accommodation-id])[data-section-id="${currentCloneSectionId}"]`);
        const $categorySection = $sourceItem.closest('.survey-data-mock-category');
        const $subCategoryContainer = $sourceItem.closest('.survey-data-mock-sub-category');
        
        // Get survey ID from page
        const surveyId = $('.survey-data-mock-content').data('survey-id');
        if (!surveyId) {
            alert('Error: Survey ID not found');
            return;
        }
        
        // Get section definition ID from source item (this is the actual section definition ID)
        let sourceSectionDefinitionId = $sourceItem.data('section-definition-id');
        // Convert to integer if it's a string
        if (sourceSectionDefinitionId) {
            sourceSectionDefinitionId = parseInt(sourceSectionDefinitionId, 10);
        }
        if (!sourceSectionDefinitionId || isNaN(sourceSectionDefinitionId)) {
            alert('Error: Section definition ID not found. Please refresh the page.');
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
        
        // Prepare form data for AJAX request
        // Use the selected section and location from the modal
        const formData = {
            section: selectedCloneSection || '',
            location: selectedCloneLocation || '',
            structure: currentCloneData.structure || '',
            material: currentCloneData.material || '',
            defects: currentCloneData.defects || [],
            remainingLife: currentCloneData.remainingLife || '',
            remaining_life: currentCloneData.remainingLife || '', // for AJAX request
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
                source_section_definition_id: parseInt(currentCloneSectionDefinitionId || sourceSectionDefinitionId, 10),
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
                    
                    // Get the new section item (only regular sections, not accommodations)
                    const $newSectionItem = $(`.survey-data-mock-section-item:not([data-accommodation-id])[data-section-id="${response.section_id}"]`);
                    
                    if ($newSectionItem.length) {
                        // Initialize button states from form data
                        initializeSectionButtons($newSectionItem, formData);
                        
                        // Initialize event handlers for the new section (expand/collapse only)
                        initializeSectionHandlers($newSectionItem);
                        
                        // Initialize carousels and dividers in the new section
                        setTimeout(function() {
                            initializeCarousels();
                            initializeDividers();
                        }, 100);
                        
                        // Update the header with section name and location
                        updateSectionHeader($newSectionItem);
                    }
                    
                    // Close modal
                    $('#survey-data-mock-clone-modal').removeClass('show');
                    currentCloneSectionId = null;
                    currentCloneSectionDefinitionId = null;
                    currentCloneData = null;
                    currentCloneCategory = null;
                    currentCloneSubCategory = null;
                    selectedCloneSection = null;
                    selectedCloneLocation = null;
                    $('.survey-data-mock-clone-section-btn').removeClass('active');
                    $('.survey-data-mock-clone-location-btn').removeClass('active');
                    $('#clone-location-error').hide();
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
            // Get and convert section definition ID to integer
            let sectionDefId = $item.data('section-definition-id');
            if (sectionDefId) {
                sectionDefId = parseInt(sectionDefId, 10);
            }
            currentCloneSectionDefinitionId = sectionDefId;
            currentCloneData = formData;
            currentCloneCategory = categoryName;
            selectedCloneSection = null;

            // Get available sections based on category - use dynamic options mapping
            let allSections = [];
            const currentSelectedSection = formData.section;
            
            // Get section options from mapping
            const sectionOptions = getOptions(categoryName, 'section', []);
            
            if (sectionOptions && sectionOptions.length > 0) {
                allSections = sectionOptions;
            } else {
                // Fallback: get from button group
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
        
        // Build section options using dynamic mapping
        let sectionOptions = getOptions(categoryName, 'section', [sectionName]);
        if (!sectionOptions || sectionOptions.length === 0) {
            sectionOptions = [sectionName];
        }
        
        // Build structure options using dynamic mapping
        const structureOptions = getOptions(categoryName, 'structure', ['Standard']);
        
        // Build material options using dynamic mapping
        const materialOptions = getOptions(categoryName, 'material', ['Mixed']);
        
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
                            <i class="fas fa-circle-notch survey-data-mock-status-icon"></i>
                            <span class="survey-data-mock-status-text">${formData.photos ? formData.photos.length : 0}</span>
                            <span class="survey-data-mock-status-separator">|</span>
                            <i class="fas fa-sticky-note survey-data-mock-status-icon"></i>
                            <span class="survey-data-mock-status-text">${completion}/${total}</span>
                        </span>
                        <span class="survey-data-mock-condition-badge survey-data-mock-condition-badge--${conditionRating || 'ni'}" 
                              data-section-id="${sectionId}"
                              data-current-rating="${conditionRating || 'ni'}">
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
                                        ${getOptions(null, 'location', ['Whole Property', 'Right', 'Left', 'Front', 'Rear']).map(opt => `
                                            <button type="button" class="survey-data-mock-button" data-value="${opt}" data-group="location">${opt}</button>
                                        `).join('')}
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
                                        ${getOptions(null, 'defects', ['Holes', 'Perished', 'Thermal Sag', 'Deflection', 'Rot', 'Moss', 'Lichen', 'Slipped Tiles', 'None']).map(opt => `
                                            <button type="button" class="survey-data-mock-button" data-value="${opt}" data-group="defects" data-multiple="true">${opt}</button>
                                        `).join('')}
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
                                        ${getOptions(null, 'remaining_life', ['0', '1-5', '6-10', '10+']).map(opt => `
                                            <button type="button" class="survey-data-mock-button" data-value="${opt}" data-group="remaining_life">${opt}</button>
                                        `).join('')}
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
                            <button type="button" class="survey-data-mock-action-btn survey-data-mock-action-clone" data-section-id="${sectionId}" data-section-name="${sectionName}">Save and Clone</button>
                            <button type="button" class="survey-data-mock-action-btn survey-data-mock-action-save" data-section-id="${sectionId}">Save</button>
                        </div>
                    </div>
                </div>

                <!-- Report Content Area (shown after save) -->
                <div class="survey-data-mock-report-content" style="display: none;" data-section-id="${sectionId}">
                    <div class="survey-data-mock-report-content-wrapper">
                        <textarea class="survey-data-mock-report-textarea" rows="12" placeholder="Report content will be generated after saving..."></textarea>
                        
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
        `;
        
        return html;
    }
    
    // Helper function to initialize button states for a section
    function initializeSectionButtons($sectionItem, formData) {
        const $details = $sectionItem.find('.survey-data-mock-section-details');
        
        // Set active buttons (single selection groups)
        if (formData.section) {
            $details.find(`[data-group="section"][data-value="${formData.section}"]`).addClass('active');
            // Update data attribute for validation
            $details.data('selected-section', formData.section);
        }
        if (formData.location) {
            $details.find(`[data-group="location"][data-value="${formData.location}"]`).addClass('active');
            // Update data attribute for validation
            $details.data('selected-location', formData.location);
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
            const $reportContent = $item.find('.survey-data-mock-report-content');
            const $titleBar = $item.find('.survey-data-mock-section-title-bar');
            const hasReport = $item.attr('data-has-report') === 'true' || $item.attr('data-saved') === 'true';
            
            $item.toggleClass('expanded');
            
            if ($item.hasClass('expanded')) {
                $titleBar.slideDown(300);
                if (hasReport) {
                    // Show report if report_content exists, hide form
                    $details.hide();
                    $reportContent.slideDown(300);
                } else {
                    // Show form if no report_content, hide report
                    $details.slideDown(300, function() {
                        // Re-initialize carousels after the section is fully visible
                        if (isSwiperAvailable()) {
                            initializeSectionCarousels($item);
                        }
                    });
                    $reportContent.hide();
                }
            } else {
                $details.slideUp(300);
                $reportContent.slideUp(300);
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
        
        // Save handler - use the same logic as the main save handler
        $sectionItem.find('.survey-data-mock-action-save').off('click').on('click', function(e) {
            e.stopPropagation();
            const $button = $(this);
            const sectionId = $button.data('section-id');
            const $sectionItem = $button.closest('.survey-data-mock-section-item');
            const sectionDefinitionId = $sectionItem.data('section-definition-id');
            const $details = $sectionItem.find('.survey-data-mock-section-details');
            const $reportContent = $sectionItem.find('.survey-data-mock-report-content');
            const $categorySection = $sectionItem.closest('.survey-data-mock-category');
            const categoryName = $categorySection.find('.survey-data-mock-category-title').text().trim();
            const sectionName = $sectionItem.find('.survey-data-mock-section-name').text().trim();
            
            // Get survey ID from header
            const surveyId = $('.survey-data-mock-content').data('survey-id');
            
            // Validate required data
            if (!surveyId || !sectionDefinitionId) {
                alert('Error: Missing survey or section information. Please refresh the page.');
                return;
            }
            
            // Collect all form data
            const formData = {
                section: $details.find('[data-group="section"].active').data('value') || '',
                location: $details.find('[data-group="location"].active').data('value') || '',
                structure: $details.find('[data-group="structure"].active').data('value') || '',
                material: $details.find('[data-group="material"].active').data('value') || '',
                defects: $details.find('[data-group="defects"].active').map(function() {
                    return $(this).data('value');
                }).get(),
                remaining_life: $details.find('[data-group="remaining_life"].active').data('value') || '',
                notes: $details.find('.survey-data-mock-notes-input').val() || '',
                costs: []
            };
            
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
            
            // Get condition rating
            const conditionRating = $sectionItem.find('.survey-data-mock-condition-badge').data('current-rating') || 'ni';
            formData.condition_rating = conditionRating;
            
            // Get selected image files
            const selectedFiles = $sectionItem.data('selectedFiles') || [];
            
            // Create FormData for file upload
            const formDataObj = new FormData();
            formDataObj.append('_token', '{{ csrf_token() }}');
            formDataObj.append('section_id', sectionId);
            formDataObj.append('section', formData.section);
            formDataObj.append('location', formData.location);
            formDataObj.append('structure', formData.structure);
            formDataObj.append('material', formData.material);
            formDataObj.append('remaining_life', formData.remaining_life);
            formDataObj.append('notes', formData.notes);
            formDataObj.append('condition_rating', conditionRating);
            
            // Append defects array
            if (formData.defects && formData.defects.length > 0) {
                formData.defects.forEach((defect, index) => {
                    formDataObj.append(`defects[${index}]`, defect);
                });
            }
            
            // Append costs array
            if (formData.costs && formData.costs.length > 0) {
                formData.costs.forEach((cost, index) => {
                    formDataObj.append(`costs[${index}][category]`, cost.category || '');
                    formDataObj.append(`costs[${index}][description]`, cost.description || '');
                    formDataObj.append(`costs[${index}][due]`, cost.due || '');
                    formDataObj.append(`costs[${index}][cost]`, cost.cost || '');
                });
            }
            
            // Append image files
            selectedFiles.forEach((file, index) => {
                formDataObj.append(`photos[${index}]`, file);
            });
            
            // Show loading state
            $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
            
            // Send AJAX request to save assessment
            $.ajax({
                url: `/surveyor/surveys/${surveyId}/sections/${sectionDefinitionId}/save`,
                method: 'POST',
                data: formDataObj,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        // Update section ID if it's a new assessment (clones get real database IDs)
                        if (response.assessment_id && typeof response.assessment_id === 'number') {
                            $sectionItem.attr('data-section-id', response.assessment_id);
                            // Update all references to the section ID
                            $sectionItem.find('[data-section-id]').attr('data-section-id', response.assessment_id);
                        }
                        
                        // Clear selected files after successful save
                        $sectionItem.data('selectedFiles', []);
                        $sectionItem.find('.survey-data-mock-images-preview').hide();
                        $sectionItem.find('.survey-data-mock-images-preview .survey-data-mock-images-grid').empty();
                        
                        // Hide form, show report
                        $details.slideUp(300);
                        $reportContent.find('.survey-data-mock-report-textarea').val(response.report_content || '');
                        $reportContent.slideDown(300);
                        
                        // Mark section as saved and has report
                        $sectionItem.attr('data-saved', 'true');
                        $sectionItem.attr('data-has-report', response.report_content ? 'true' : 'false');
                        $sectionItem.attr('data-locked', 'false');
                        
                        // Initialize lock state (unlocked by default)
                        updateLockState($sectionItem, false);
                        
                        // Show success message
                        if (typeof toastr !== 'undefined') {
                            toastr.success(response.message || 'Assessment saved successfully');
                        } else {
                            console.log('Assessment saved successfully');
                        }
                        
                        // Note: Images will be visible after page refresh or when section is reopened
                        // The uploaded images are now saved in the database
                    } else {
                        throw new Error(response.error || 'Failed to save assessment');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Failed to save assessment. Please try again.';
                    
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMessage = xhr.responseJSON.error;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        // Validation errors
                        const errors = Object.values(xhr.responseJSON.errors).flat();
                        errorMessage = errors.join('\n');
                    } else if (xhr.status === 403) {
                        errorMessage = 'You are not authorized to save this assessment.';
                    } else if (xhr.status === 404) {
                        errorMessage = 'Section not found. Please refresh the page.';
                    }
                    
                    if (typeof toastr !== 'undefined') {
                        toastr.error(errorMessage);
                    } else {
                        alert(errorMessage);
                    }
                    
                    console.error('Error saving assessment:', xhr);
                },
                complete: function() {
                    // Reset button state
                    $button.prop('disabled', false).html('Save');
                }
            });
        });
        
        // Add Cost handler - already handled by document-level delegation above
        
        // Microphone handler
        $sectionItem.find('.survey-data-mock-mic-btn').off('click').on('click', function(e) {
            e.stopPropagation();
            alert('Voice input functionality - to be implemented');
        });
        
        // Image upload handler
        $sectionItem.find('.survey-data-mock-images-upload').off('click').on('click', function() {
            alert('Image upload functionality - to be implemented');
        });
    }

    // Accommodation Carousel Functionality
    function initializeAccommodationCarousel($accommodationItem) {
        const $carouselTrack = $accommodationItem.find('[data-carousel-track]');
        const $slides = $accommodationItem.find('.survey-data-mock-carousel-slide');
        const $tabs = $accommodationItem.find('.survey-data-mock-component-tab');
        const $indicators = $accommodationItem.find('.survey-data-mock-carousel-indicator');
        let currentSlide = 0;
        const totalSlides = $slides.length;

        // Function to go to specific slide
        function goToSlide(index) {
            if (index < 0 || index >= totalSlides) return;
            
            currentSlide = index;
            
            // Update slides - hide all, show active
            $slides.removeClass('active');
            $slides.eq(index).addClass('active');
            
            // Update tabs
            $tabs.removeClass('active');
            $tabs.eq(index).addClass('active');
            
            // Update indicator dots
            $indicators.removeClass('active');
            $indicators.eq(index).addClass('active');
        }

        // Tab click handler
        $tabs.on('click', function() {
            const index = $(this).data('component-index');
            goToSlide(index);
        });

        // Indicator dot click handler
        $indicators.on('click', function() {
            const index = $(this).data('slide-index');
            goToSlide(index);
        });

        // Touch and mouse swipe functionality - on the entire carousel wrapper
        const $carouselWrapper = $accommodationItem.find('.survey-data-mock-carousel-wrapper');
        let startX = 0;
        let startY = 0;
        let isDragging = false;
        let hasMoved = false;

        // Prevent default on touch to allow smooth scrolling
        $carouselWrapper.on('touchstart', function(e) {
            // Allow button clicks to work normally
            if ($(e.target).closest('button').length) {
                return;
            }
            
            const touch = e.originalEvent.touches[0];
            startX = touch.clientX;
            startY = touch.clientY;
            isDragging = true;
            hasMoved = false;
        });

        $carouselWrapper.on('touchmove', function(e) {
            if (!isDragging) return;
            
            // Allow button clicks to work normally
            if ($(e.target).closest('button').length) {
                isDragging = false;
                return;
            }
            
            const touch = e.originalEvent.touches[0];
            const diffX = touch.clientX - startX;
            const diffY = touch.clientY - startY;
            
            // Only prevent default if horizontal movement is greater than vertical (swipe)
            if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 10) {
                e.preventDefault();
                hasMoved = true;
            }
        });

        $carouselWrapper.on('touchend', function(e) {
            if (!isDragging) return;
            
            // Allow button clicks to work normally
            if ($(e.target).closest('button').length) {
                isDragging = false;
                return;
            }
            
            if (hasMoved) {
                const touch = e.originalEvent.changedTouches[0];
                const diffX = touch.clientX - startX;
                const threshold = 50;
                
                if (Math.abs(diffX) > threshold) {
                    if (diffX > 0 && currentSlide > 0) {
                        // Swipe right - go to previous slide
                        goToSlide(currentSlide - 1);
                    } else if (diffX < 0 && currentSlide < totalSlides - 1) {
                        // Swipe left - go to next slide
                        goToSlide(currentSlide + 1);
                    }
                }
            }
            
            isDragging = false;
            hasMoved = false;
        });

        // Mouse drag functionality
        $carouselWrapper.on('mousedown', function(e) {
            // Don't prevent default on mousedown to allow button clicks
            if ($(e.target).closest('button').length) {
                return;
            }
            
            startX = e.clientX;
            startY = e.clientY;
            isDragging = true;
            hasMoved = false;
            $carouselWrapper.addClass('dragging');
            
            e.preventDefault();
        });

        $(document).on('mousemove.accommodationCarousel', function(e) {
            if (!isDragging) return;
            
            const diffX = e.clientX - startX;
            const diffY = e.clientY - startY;
            
            // Only consider it a swipe if horizontal movement is greater than vertical
            if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 10) {
                hasMoved = true;
            }
        });

        $(document).on('mouseup.accommodationCarousel', function(e) {
            if (!isDragging) return;
            
            // Don't interfere with button clicks
            if ($(e.target).closest('button').length) {
                isDragging = false;
                hasMoved = false;
                $carouselWrapper.removeClass('dragging');
                return;
            }
            
            $carouselWrapper.removeClass('dragging');
            
            if (hasMoved) {
                const diffX = e.clientX - startX;
                const threshold = 50;
                
                if (Math.abs(diffX) > threshold) {
                    if (diffX > 0 && currentSlide > 0) {
                        // Drag right - go to previous slide
                        goToSlide(currentSlide - 1);
                    } else if (diffX < 0 && currentSlide < totalSlides - 1) {
                        // Drag left - go to next slide
                        goToSlide(currentSlide + 1);
                    }
                }
            }
            
            isDragging = false;
            hasMoved = false;
        });

        // Clean up on accommodation item removal
        $accommodationItem.on('remove', function() {
            $(document).off('mousemove.accommodationCarousel mouseup.accommodationCarousel');
        });

        // Material and Defects button handlers - use event delegation to ensure clicks work
        $accommodationItem.on('click', '[data-group="material"]', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const $button = $(this);
            const componentKey = $button.data('component-key');
            
            // Remove active from other material buttons in same component
            $accommodationItem.find('[data-group="material"][data-component-key="' + componentKey + '"]').removeClass('active');
            $button.addClass('active');
        });

        $accommodationItem.on('click', '[data-group="defects"]', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const $button = $(this);
            const componentKey = $button.data('component-key');
            const value = $button.data('value');
            
            // Toggle active state
            $button.toggleClass('active');
            
            // Only handle "No Defects" logic - allow all other defects to be selected freely
            if (value === 'No Defects') {
                // If "No Defects" is selected, deselect all other defects
                if ($button.hasClass('active')) {
                    $accommodationItem.find('[data-group="defects"][data-component-key="' + componentKey + '"]').not($button).removeClass('active');
                }
            } else {
                // If any other defect is selected, deselect "No Defects" (but allow multiple other defects)
                if ($button.hasClass('active')) {
                    $accommodationItem.find('[data-group="defects"][data-component-key="' + componentKey + '"][data-value="No Defects"]').removeClass('active');
                }
            }
        });

    }

    // Initialize accommodation carousels (now using section-item class)
    $('.survey-data-mock-section-item[data-accommodation-id]').each(function() {
        initializeAccommodationCarousel($(this));
    });

    // Accommodation sections now use the same handler as regular sections
    // No separate handler needed - they use .survey-data-mock-section-item class

    // Accommodation action buttons
    $(document).on('click', '.survey-data-mock-section-item[data-accommodation-id] .survey-data-mock-action-delete', function(e) {
        e.stopPropagation();
        const $item = $(this).closest('.survey-data-mock-section-item[data-accommodation-id]');
        if (confirm('Are you sure you want to delete this accommodation?')) {
            $item.fadeOut(300, function() {
                $(this).remove();
            });
        }
    });

    $(document).on('click', '.survey-data-mock-section-item[data-accommodation-id] .survey-data-mock-action-save', function(e) {
        e.stopPropagation();
        const $button = $(this);
        const $item = $(this).closest('.survey-data-mock-section-item[data-accommodation-id]');
        const accommodationId = $item.data('accommodation-id');
        const accommodationTypeId = $item.data('accommodation-type-id');
        const $details = $item.find('.survey-data-mock-section-details');
        const accommodationName = $item.find('.survey-data-mock-section-name').text().trim();
        
        // Get survey ID from header
        const surveyId = $('.survey-data-mock-content').data('survey-id');
        
        // Validate required data
        if (!surveyId) {
            alert('Error: Missing survey information. Please refresh the page.');
            return;
        }
        
        // If accommodation_type_id is missing, try to get it from the item attribute
        let finalAccommodationTypeId = accommodationTypeId;
        if (!finalAccommodationTypeId || finalAccommodationTypeId === '' || finalAccommodationTypeId === null || finalAccommodationTypeId === undefined) {
            // Try to get from data attribute (jQuery .data() might not work if value is empty string)
            finalAccommodationTypeId = $item.attr('data-accommodation-type-id');
            
            // If still empty, try to parse as integer or use default
            if (!finalAccommodationTypeId || finalAccommodationTypeId === '' || finalAccommodationTypeId === 'null' || finalAccommodationTypeId === 'undefined') {
                console.error('Accommodation type ID missing', {
                    accommodationId: accommodationId,
                    accommodationName: accommodationName,
                    itemData: $item.data()
                });
                alert('Error: Missing accommodation type information. Please refresh the page.\n\nIf this error persists, please contact support.');
                return;
            }
        }
        
        // Ensure it's a valid number
        finalAccommodationTypeId = parseInt(finalAccommodationTypeId);
        if (isNaN(finalAccommodationTypeId) || finalAccommodationTypeId <= 0) {
            console.error('Invalid accommodation type ID', finalAccommodationTypeId);
            alert('Error: Invalid accommodation type information. Please refresh the page.');
            return;
        }
        
        // Get condition rating
        const conditionRating = $item.find('.survey-data-mock-condition-badge').data('current-rating') || 'ni';
        
        // Collect all form data
        const formData = {
            custom_name: accommodationName,
            components: [],
            notes: $details.find('.survey-data-mock-notes-input').val() || '',
            condition_rating: conditionRating
        };
        
        // Collect component data from carousel slides
        $details.find('.survey-data-mock-carousel-slide').each(function() {
            const $slide = $(this);
            const componentKey = $slide.data('component-key');
            
            if (!componentKey) {
                return;
            }
            
            // Get material for this component
            const material = $slide.find('[data-group="material"].active').data('value') || '';
            
            // Get defects for this component (multiple selection)
            const defects = $slide.find('[data-group="defects"].active').map(function() {
                return $(this).data('value');
            }).get();
            
            formData.components.push({
                component_key: componentKey,
                material: material,
                defects: defects
            });
        });
        
        // Get selected image files
        const selectedFiles = $item.data('selectedFiles') || [];
        
        // Create FormData for file upload
        const formDataObj = new FormData();
        formDataObj.append('_token', '{{ csrf_token() }}');
        formDataObj.append('accommodation_id', accommodationId || '');
        formDataObj.append('accommodation_type_id', finalAccommodationTypeId);
        formDataObj.append('custom_name', formData.custom_name);
        formDataObj.append('notes', formData.notes);
        formDataObj.append('condition_rating', conditionRating);
        
        // Append components array (even if empty, send empty array)
        formData.components.forEach((component, index) => {
            formDataObj.append(`components[${index}][component_key]`, component.component_key || '');
            formDataObj.append(`components[${index}][material]`, component.material || '');
            if (component.defects && component.defects.length > 0) {
                component.defects.forEach((defect, defectIndex) => {
                    formDataObj.append(`components[${index}][defects][${defectIndex}]`, defect);
                });
            }
        });
        
        // Append image files
        selectedFiles.forEach((file, index) => {
            formDataObj.append(`photos[${index}]`, file);
        });
        
        // Show loading state
        $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        
        // Send AJAX request to save accommodation assessment
        $.ajax({
            url: `/surveyor/surveys/${surveyId}/accommodations/save`,
            method: 'POST',
            data: formDataObj,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    // Update accommodation ID if it's a new assessment (clones get real database IDs)
                    if (response.assessment_id && typeof response.assessment_id === 'number') {
                        const newId = response.assessment_id;
                        $item.attr('data-accommodation-id', newId);
                        $item.attr('data-section-id', newId);
                        // Update all references to the accommodation ID, including rating badges
                        $item.find('[data-accommodation-id]').attr('data-accommodation-id', newId);
                        $item.find('.survey-data-mock-condition-badge').each(function() {
                            $(this).attr('data-section-id', newId);
                            $(this).attr('data-accommodation-id', newId);
                        });
                    }
                    
                    // Clear selected files after successful save
                    $item.data('selectedFiles', []);
                    $item.find('.survey-data-mock-images-preview').hide();
                    $item.find('.survey-data-mock-images-preview .survey-data-mock-images-grid').empty();
                    
                    // Generate report content for accommodation
                    const reportFormData = {
                        notes: formData.notes,
                        components: formData.components.map(comp => {
                            // Get component name from the tab
                            const componentName = $details.find(`.survey-data-mock-component-tab[data-component-key="${comp.component_key}"]`).text().trim() || comp.component_key;
                            return {
                                component_key: comp.component_key,
                                component_name: componentName,
                                material: comp.material,
                                defects: comp.defects
                            };
                        })
                    };
                    
                    const reportContent = response.report_content || generateAccommodationReportContent(reportFormData, accommodationName);
                    
                    // Get report content area
                    const $reportContent = $item.find('.survey-data-mock-report-content');
                    
                    // Hide form, show report
                    $details.slideUp(300);
                    $reportContent.find('.survey-data-mock-report-textarea').val(reportContent);
                    $reportContent.slideDown(300);
                    
                    // Mark section as saved and has report
                    $item.attr('data-saved', 'true');
                    $item.attr('data-has-report', 'true');
                    $item.attr('data-locked', 'false');
                    
                    // Show success message
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message || 'Accommodation assessment saved successfully');
                    } else {
                        console.log('Accommodation assessment saved successfully');
                    }
                    
                    // Note: Images will be visible after page refresh or when accommodation is reopened
                    // The uploaded images are now saved in the database
                } else {
                    throw new Error(response.error || 'Failed to save accommodation assessment');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Failed to save accommodation assessment. Please try again.';
                
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    // Validation errors
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMessage = errors.join('\n');
                } else if (xhr.status === 403) {
                    errorMessage = 'You are not authorized to save this accommodation assessment.';
                } else if (xhr.status === 404) {
                    errorMessage = 'Accommodation type not found. Please refresh the page.';
                }
                
                if (typeof toastr !== 'undefined') {
                    toastr.error(errorMessage);
                } else {
                    alert(errorMessage);
                }
                
                console.error('Error saving accommodation assessment:', xhr);
            },
            complete: function() {
                // Reset button state
                $button.prop('disabled', false).html('Save');
            }
        });
    });

    $(document).on('click', '.survey-data-mock-section-item[data-accommodation-id] .survey-data-mock-action-clone', function(e) {
        e.stopPropagation();
        const $button = $(this);
        const $item = $(this).closest('.survey-data-mock-section-item[data-accommodation-id]');
        const accommodationId = $item.data('accommodation-id');
        const accommodationTypeId = $item.data('accommodation-type-id');
        const $details = $item.find('.survey-data-mock-section-details');
        const accommodationName = $item.find('.survey-data-mock-section-name').text().trim();
        
        // Get survey ID from header
        const surveyId = $('.survey-data-mock-content').data('survey-id');
        
        // Validate required data
        if (!surveyId) {
            alert('Error: Missing survey information. Please refresh the page.');
            return;
        }
        
        // If accommodation_type_id is missing, try to get it from the item attribute
        let finalAccommodationTypeId = accommodationTypeId;
        if (!finalAccommodationTypeId || finalAccommodationTypeId === '' || finalAccommodationTypeId === null || finalAccommodationTypeId === undefined) {
            finalAccommodationTypeId = $item.attr('data-accommodation-type-id');
            
            if (!finalAccommodationTypeId || finalAccommodationTypeId === '' || finalAccommodationTypeId === 'null' || finalAccommodationTypeId === 'undefined') {
                console.error('Accommodation type ID missing for clone');
                alert('Error: Missing accommodation type information. Please refresh the page.');
                return;
            }
        }
        
        // Ensure it's a valid number
        finalAccommodationTypeId = parseInt(finalAccommodationTypeId);
        if (isNaN(finalAccommodationTypeId) || finalAccommodationTypeId <= 0) {
            console.error('Invalid accommodation type ID for clone', finalAccommodationTypeId);
            alert('Error: Invalid accommodation type information. Please refresh the page.');
            return;
        }
        
        // Get condition rating from badge
        const $badge = $item.find('.survey-data-mock-condition-badge');
        let conditionRating = 'ni';
        if ($badge.hasClass('survey-data-mock-condition-badge--3')) {
            conditionRating = '3';
        } else if ($badge.hasClass('survey-data-mock-condition-badge--2')) {
            conditionRating = '2';
        } else if ($badge.hasClass('survey-data-mock-condition-badge--1')) {
            conditionRating = '1';
        }
        
        // Collect all form data (copy all selected values)
        const formData = {
            custom_name: accommodationName,
            components: [],
            notes: $details.find('.survey-data-mock-notes-input').val() || '',
            condition_rating: conditionRating
        };
        
        // Collect component data from carousel slides (copy all selected values)
        $details.find('.survey-data-mock-carousel-slide').each(function() {
            const $slide = $(this);
            const componentKey = $slide.data('component-key');
            
            if (!componentKey) {
                return;
            }
            
            // Get material for this component
            const material = $slide.find('[data-group="material"].active').data('value') || '';
            
            // Get defects for this component (multiple selection)
            const defects = $slide.find('[data-group="defects"].active').map(function() {
                return $(this).data('value');
            }).get();
            
            formData.components.push({
                component_key: componentKey,
                material: material,
                defects: defects
            });
        });
        
        // Show loading state
        $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Cloning...');
        
        // Send AJAX request to clone endpoint (returns HTML, no page reload)
        $.ajax({
            url: `/surveyor/surveys/${surveyId}/clone-accommodation-item`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                source_accommodation_id: accommodationId,
                accommodation_type_id: finalAccommodationTypeId,
                form_data: formData
            },
            success: function(response) {
                if (response.success && response.html) {
                    // Insert cloned accommodation immediately after the source accommodation
                    $item.after(response.html);
                    
                    // Get the new accommodation item
                    const $newAccommodationItem = $(`.survey-data-mock-section-item[data-accommodation-id="${response.accommodation_id}"]`);
                    
                    // Initialize carousel for the new accommodation
                    if ($newAccommodationItem.length) {
                        initializeAccommodationCarousel($newAccommodationItem);
                        
                        // Initialize event handlers for expand/collapse
                        initializeSectionHandlers($newAccommodationItem);
                        
                        // Initialize dividers for the new accommodation
                        setTimeout(function() {
                            initializeDividers();
                        }, 100);
                    }
                    
                    // Show success message
                    if (typeof toastr !== 'undefined') {
                        toastr.success('Accommodation cloned successfully');
                    } else {
                        console.log('Accommodation cloned successfully');
                    }
                    
                    // Scroll to new accommodation
                    setTimeout(function() {
                        $('html, body').animate({
                            scrollTop: $newAccommodationItem.offset().top - 100
                        }, 500);
                    }, 200);
                    
                    // Reset button state
                    $button.prop('disabled', false).html('Save and Clone');
                } else {
                    throw new Error(response.error || 'Failed to clone accommodation');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Failed to clone accommodation. Please try again.';
                
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMessage = errors.join('\n');
                }
                
                if (typeof toastr !== 'undefined') {
                    toastr.error(errorMessage);
                } else {
                    alert(errorMessage);
                }
                $button.prop('disabled', false).html('Save and Clone');
            }
        });
    });

    // Accommodation Draggable Divider
    $('.survey-data-mock-accommodation-form-grid-divider').each(function() {
        const $divider = $(this);
        const $grid = $divider.closest('.survey-data-mock-accommodation-form-grid');
        const $leftColumn = $grid.find('[data-column="left"]');
        const $rightColumn = $grid.find('[data-column="right"]');
        
        if ($leftColumn.length === 0 || $rightColumn.length === 0) {
            return;
        }
        
        let isDragging = false;
        let startX = 0;
        let startLeftWidth = 0;
        let startRightWidth = 0;
        
        $divider.on('mousedown', function(e) {
            isDragging = true;
            $divider.addClass('dragging');
            $('body').css('cursor', 'col-resize');
            $('body').css('user-select', 'none');
            
            // Remove max-width constraints to allow proper resizing
            $leftColumn.css('max-width', 'none');
            $rightColumn.css('max-width', 'none');
            
            startX = e.clientX;
            startLeftWidth = $leftColumn.outerWidth();
            startRightWidth = $rightColumn.outerWidth();
            
            e.preventDefault();
        });
        
        $(document).on('mousemove.accommodationDivider', function(e) {
            if (!isDragging) return;
            
            const diff = e.clientX - startX;
            const gridWidth = $grid.width();
            const newLeftWidth = startLeftWidth + diff;
            const newRightWidth = startRightWidth - diff;
            
            // Minimum widths
            const minWidth = 200;
            if (newLeftWidth < minWidth || newRightWidth < minWidth) {
                return;
            }
            
            // Calculate percentages
            const leftPercent = (newLeftWidth / gridWidth) * 100;
            const rightPercent = (newRightWidth / gridWidth) * 100;
            
            $leftColumn.css('flex', '0 0 ' + leftPercent + '%');
            $rightColumn.css('flex', '0 0 ' + rightPercent + '%');
        });
        
        $(document).on('mouseup.accommodationDivider', function() {
            if (!isDragging) return;
            
            isDragging = false;
            $divider.removeClass('dragging');
            $('body').css('cursor', '');
            $('body').css('user-select', '');
            
        });
    });
});

// ============================================
// ENHANCED IMAGE UPLOAD & LIGHTBOX SYSTEM
// ============================================

$(document).ready(function() {
    
    // ============================================
    // ENHANCED UPLOAD DROPZONE
    // ============================================
    
    // Initialize enhanced upload areas
    function initEnhancedUpload($sectionItem) {
        const $dropzone = $sectionItem.find('.survey-data-mock-upload-dropzone');
        const $fileInput = $sectionItem.find('.survey-data-mock-file-input');
        
        if ($dropzone.length === 0) return;
        
        // Skip if already initialized
        if ($dropzone.data('upload-initialized')) return;
        $dropzone.data('upload-initialized', true);
        
        // Unbind old handlers first
        $dropzone.off('click.upload dragover.upload dragleave.upload drop.upload');
        $fileInput.off('change');
        
        // Click handler for dropzone
        $dropzone.on('click.upload', function(e) {
            e.stopPropagation();
            $fileInput.trigger('click');
        });
        
        // Drag and drop handlers
        $dropzone.on('dragover.upload', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).addClass('dragover');
        });
        
        $dropzone.on('dragleave.upload', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).removeClass('dragover');
        });
        
        $dropzone.on('drop.upload', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).removeClass('dragover');
            
            const files = Array.from(e.originalEvent.dataTransfer.files).filter(file => file.type.startsWith('image/'));
            if (files.length > 0) {
                handleEnhancedFilesSelected($sectionItem, files);
            }
        });
        
        // File input change - single handler
        $fileInput.on('change', function(e) {
            e.stopPropagation();
            const files = Array.from(this.files);
            if (files.length > 0) {
                handleEnhancedFilesSelected($sectionItem, files);
            }
            $(this).val(''); // Reset input
        });
    }
    
    // Handle enhanced file selection
    function handleEnhancedFilesSelected($sectionItem, files) {
        const validFiles = files.filter(file => file.type.startsWith('image/'));
        if (validFiles.length === 0) return;
        
        const sectionId = $sectionItem.data('section-id');
        const accommodationId = $sectionItem.data('accommodation-id');
        const isNumericId = /^\d+$/.test((sectionId || accommodationId || '').toString());
        const surveyId = $('.survey-data-mock-content').data('survey-id');
        
        const $dropzone = $sectionItem.find('.survey-data-mock-upload-dropzone');
        const $uploadTitle = $dropzone.find('.survey-data-mock-upload-title');
        const originalTitle = $uploadTitle.text();
        
        // Show uploading state
        $uploadTitle.html('<i class="fas fa-spinner fa-spin"></i> Uploading...');
        $dropzone.css('pointer-events', 'none');
        
        if (isNumericId && surveyId) {
            // Upload to backend
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            validFiles.forEach((file, index) => {
                formData.append(`photos[${index}]`, file);
            });
            
            const assessmentId = sectionId || accommodationId;
            const uploadUrl = accommodationId 
                ? `/surveyor/surveys/${surveyId}/accommodation-assessments/${assessmentId}/photos`
                : `/surveyor/surveys/${surveyId}/assessments/${assessmentId}/photos`;
            
            $.ajax({
                url: uploadUrl,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.photos && response.photos.length > 0) {
                        addPhotosToGrid($sectionItem, response.photos);
                        updateImageCount($sectionItem);
                    }
                    
                    $uploadTitle.text(originalTitle);
                    $dropzone.css('pointer-events', 'auto');
                    
                    showToast('Images uploaded successfully!', 'success');
                },
                error: function(xhr) {
                    console.error('Upload failed:', xhr.responseJSON);
                    $uploadTitle.text(originalTitle);
                    $dropzone.css('pointer-events', 'auto');
                    showToast('Failed to upload images. Please try again.', 'error');
                }
            });
        } else {
            // Preview mode (not saved yet)
            const $previewArea = $sectionItem.find('.survey-data-mock-images-preview');
            const $previewGrid = $previewArea.find('.survey-data-mock-images-grid-enhanced');
            
            validFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const $card = createImageCard(e.target.result, null, index);
                    $card.attr('data-file-index', index);
                    $previewGrid.append($card);
                };
                reader.readAsDataURL(file);
            });
            
            $previewArea.show();
            $uploadTitle.text(originalTitle);
            $dropzone.css('pointer-events', 'auto');
            updateImageCount($sectionItem);
        }
    }
    
    // Create image card HTML
    function createImageCard(imageUrl, photoId, index) {
        const $card = $(`
            <div class="survey-data-mock-image-card" data-image-url="${imageUrl}" ${photoId ? 'data-photo-id="' + photoId + '"' : ''}>
                <div class="survey-data-mock-image-wrapper">
                    <img src="${imageUrl}" alt="Photo ${index + 1}" class="survey-data-mock-image-thumb" loading="lazy" onerror="this.style.display='none'; this.parentElement.querySelector('.survey-data-mock-image-error') && (this.parentElement.querySelector('.survey-data-mock-image-error').style.display='flex');">
                    <div class="survey-data-mock-image-error" style="display:none;"><i class="fas fa-image"></i></div>
                    <div class="survey-data-mock-image-overlay">
                        <button type="button" class="survey-data-mock-image-action survey-data-mock-image-preview-btn" title="Preview">
                            <i class="fas fa-expand"></i>
                        </button>
                        <button type="button" class="survey-data-mock-image-action survey-data-mock-image-delete" ${photoId ? 'data-photo-id="' + photoId + '"' : ''} title="Delete">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="survey-data-mock-image-info">
                    <span class="survey-data-mock-image-number">#${index + 1}</span>
                </div>
            </div>
        `);
        return $card;
    }
    
    // Add photos to existing grid
    function addPhotosToGrid($sectionItem, photos) {
        let $existingContainer = $sectionItem.find('.survey-data-mock-existing-images');
        let $existingGrid = $existingContainer.find('.survey-data-mock-images-grid-enhanced');
        
        if ($existingContainer.length === 0) {
            $existingContainer = $(`
                <div class="survey-data-mock-existing-images">
                    <div class="survey-data-mock-images-grid-enhanced"></div>
                </div>
            `);
            $existingGrid = $existingContainer.find('.survey-data-mock-images-grid-enhanced');
            // Insert after the dropzone
            $sectionItem.find('.survey-data-mock-upload-dropzone').after($existingContainer);
        }
        
        const existingCount = $existingGrid.find('.survey-data-mock-image-card').length;
        
        photos.forEach(function(photo, index) {
            const $card = createImageCard(photo.url, photo.id, existingCount + index + 1);
            $existingGrid.append($card);
        });
        
        $existingContainer.show();
    }
    
    // Update image count display
    function updateImageCount($sectionItem) {
        const count = $sectionItem.find('.survey-data-mock-image-card, .survey-data-mock-image-item').length;
        const $countSpan = $sectionItem.find('[data-image-count]');
        const $gridCount = $sectionItem.find('.survey-data-mock-images-count');
        
        $countSpan.text(count > 0 ? `(${count})` : '');
        $gridCount.text(`${count} image(s)`);
    }
    
    // Initialize all sections
    $('.survey-data-mock-section-item').each(function() {
        initEnhancedUpload($(this));
    });
    
    // ============================================
    // LIGHTBOX FUNCTIONALITY
    // ============================================
    
    let lightboxImages = [];
    let currentLightboxIndex = 0;
    
    // Open lightbox
    function openLightbox($sectionItem, $clickedImage) {
        const $lightbox = $('#survey-data-mock-lightbox');
        
        if ($lightbox.length === 0) {
            console.error('Lightbox element not found in DOM');
            return;
        }
        
        // Move lightbox to body to escape stacking context (sidebar z-index issue)
        if (!$lightbox.parent().is('body')) {
            $lightbox.appendTo('body');
        }
        
        // Collect all images from the section
        lightboxImages = [];
        $sectionItem.find('.survey-data-mock-image-card, .survey-data-mock-image-item').each(function() {
            const $img = $(this).find('img');
            const url = $(this).data('image-url') || $img.attr('src');
            if (url && url.length > 0) {
                lightboxImages.push(url);
            }
        });
        
        if (lightboxImages.length === 0) {
            console.log('No images found in section');
            return;
        }
        
        // Find clicked image index
        const $card = $clickedImage.closest('.survey-data-mock-image-card, .survey-data-mock-image-item');
        const clickedUrl = $card.data('image-url') || $card.find('img').attr('src');
        currentLightboxIndex = lightboxImages.indexOf(clickedUrl);
        if (currentLightboxIndex === -1) currentLightboxIndex = 0;
        
        // Show lightbox
        $lightbox.addClass('active');
        showLightboxImage(currentLightboxIndex);
        
        // Prevent body scroll
        $('body').css('overflow', 'hidden');
    }
    
    // Close lightbox
    function closeLightbox() {
        $('#survey-data-mock-lightbox').removeClass('active');
        $('body').css('overflow', '');
        lightboxImages = [];
        currentLightboxIndex = 0;
    }
    
    // Show specific image
    function showLightboxImage(index) {
        if (index < 0 || index >= lightboxImages.length) return;
        
        currentLightboxIndex = index;
        const $image = $('#lightbox-image');
        const $loader = $('.survey-data-mock-lightbox-loader');
        const imageUrl = lightboxImages[index];
        
        // Show loader
        $loader.addClass('active');
        $image.css('opacity', '0');
        
        // Load image with timeout for error handling
        const img = new Image();
        const loadTimeout = setTimeout(function() {
            // If image takes too long, show anyway and hide loader
            $image.attr('src', imageUrl);
            $image.css('opacity', '1');
            $loader.removeClass('active');
        }, 3000);
        
        img.onload = function() {
            clearTimeout(loadTimeout);
            $image.attr('src', imageUrl);
            $image.css('opacity', '1');
            $loader.removeClass('active');
        };
        
        img.onerror = function() {
            clearTimeout(loadTimeout);
            $image.attr('src', imageUrl);
            $image.css('opacity', '1');
            $loader.removeClass('active');
        };
        
        img.src = imageUrl;
        
        // Update counter
        $('#lightbox-current').text(index + 1);
        $('#lightbox-total').text(lightboxImages.length);
        
        // Update nav buttons
        $('#lightbox-prev').prop('disabled', index === 0);
        $('#lightbox-next').prop('disabled', index === lightboxImages.length - 1);
    }
    
    // Event handlers - Preview button click
    $(document).on('click', '.survey-data-mock-image-preview-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const $sectionItem = $(this).closest('.survey-data-mock-section-item');
        openLightbox($sectionItem, $(this));
    });
    
    // Click on image thumbnail to preview
    $(document).on('click', '.survey-data-mock-image-card .survey-data-mock-image-thumb', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const $sectionItem = $(this).closest('.survey-data-mock-section-item');
        openLightbox($sectionItem, $(this));
    });
    
    // Lightbox control handlers (use delegation since lightbox may be moved to body)
    $(document).on('click', '#lightbox-close, .survey-data-mock-lightbox-backdrop', function(e) {
        e.preventDefault();
        closeLightbox();
    });
    
    $(document).on('click', '#lightbox-prev', function(e) {
        e.preventDefault();
        if (currentLightboxIndex > 0) {
            showLightboxImage(currentLightboxIndex - 1);
        }
    });
    
    $(document).on('click', '#lightbox-next', function(e) {
        e.preventDefault();
        if (currentLightboxIndex < lightboxImages.length - 1) {
            showLightboxImage(currentLightboxIndex + 1);
        }
    });
    
    // Keyboard navigation
    $(document).on('keydown', function(e) {
        if (!$('#survey-data-mock-lightbox').hasClass('active')) return;
        
        switch(e.key) {
            case 'Escape':
                closeLightbox();
                break;
            case 'ArrowLeft':
                if (currentLightboxIndex > 0) showLightboxImage(currentLightboxIndex - 1);
                break;
            case 'ArrowRight':
                if (currentLightboxIndex < lightboxImages.length - 1) showLightboxImage(currentLightboxIndex + 1);
                break;
        }
    });
    
    // ============================================
    // IMAGE DELETE HANDLER (ENHANCED)
    // ============================================
    
    $(document).on('click', '.survey-data-mock-image-card .survey-data-mock-image-delete', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const $card = $(this).closest('.survey-data-mock-image-card');
        const $sectionItem = $(this).closest('.survey-data-mock-section-item');
        const photoId = $(this).data('photo-id');
        const surveyId = $('.survey-data-mock-content').data('survey-id');
        const sectionId = $sectionItem.data('section-id');
        const accommodationId = $sectionItem.data('accommodation-id');
        
        if (!confirm('Are you sure you want to delete this image?')) return;
        
        if (photoId && surveyId) {
            const assessmentId = sectionId || accommodationId;
            const deleteUrl = accommodationId 
                ? `/surveyor/surveys/${surveyId}/accommodation-assessments/${assessmentId}/photos/${photoId}/delete`
                : `/surveyor/surveys/${surveyId}/assessments/${assessmentId}/photos/${photoId}/delete`;
            
            $.ajax({
                url: deleteUrl,
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        $card.fadeOut(300, function() {
                            $(this).remove();
                            updateImageCount($sectionItem);
                            
                            // Hide container if empty
                            const $container = $sectionItem.find('.survey-data-mock-existing-images');
                            if ($container.find('.survey-data-mock-image-card').length === 0) {
                                $container.hide();
                            }
                        });
                        showToast('Image deleted successfully!', 'success');
                    }
                },
                error: function() {
                    showToast('Failed to delete image.', 'error');
                }
            });
        } else {
            // Just remove from preview
            $card.fadeOut(300, function() {
                $(this).remove();
                updateImageCount($sectionItem);
            });
        }
    });
    
    // Toast notification helper
    function showToast(message, type) {
        if (typeof toastr !== 'undefined') {
            toastr[type](message);
        } else {
            console.log(type.toUpperCase() + ':', message);
        }
    }

    // ============================================
    // CONTENT SECTIONS FUNCTIONALITY
    // ============================================
    
    // Expand/Collapse Content Sections
    $(document).on('click', '.survey-data-mock-content-section-item .survey-data-mock-section-header[data-expandable="true"]', function(e) {
        e.stopPropagation();
        const $sectionItem = $(this).closest('.survey-data-mock-content-section-item');
        const $contentDetails = $sectionItem.find('.survey-data-mock-content-section-details');
        const $titleBar = $sectionItem.find('.survey-data-mock-section-title-bar');
        const $expandIcon = $sectionItem.find('.survey-data-mock-expand-icon');
        
        $sectionItem.toggleClass('expanded');
        
        if ($sectionItem.hasClass('expanded')) {
            $titleBar.slideDown(300);
            $contentDetails.slideDown(300);
            $expandIcon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
        } else {
            $contentDetails.slideUp(300);
            $titleBar.slideUp(300);
            $expandIcon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
        }
    });

    // Collapse from title bar for content sections
    $(document).on('click', '.survey-data-mock-content-section-item .survey-data-mock-section-title-collapse', function(e) {
        e.stopPropagation();
        const $sectionItem = $(this).closest('.survey-data-mock-content-section-item');
        const $contentDetails = $sectionItem.find('.survey-data-mock-content-section-details');
        const $titleBar = $sectionItem.find('.survey-data-mock-section-title-bar');
        const $expandIcon = $sectionItem.find('.survey-data-mock-expand-icon');
        
        $sectionItem.removeClass('expanded');
        $contentDetails.slideUp(300);
        $titleBar.slideUp(300);
        $expandIcon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
    });

    // Save Content Section
    $(document).on('click', '.survey-data-mock-content-section-item [data-action="save-content"]', function(e) {
        e.stopPropagation();
        const $button = $(this);
        const $sectionItem = $button.closest('.survey-data-mock-content-section-item');
        const contentSectionId = $sectionItem.data('content-section-id');
        const $textarea = $sectionItem.find('.survey-data-mock-content-textarea');
        const content = $textarea.val();
        const surveyId = $('.survey-data-mock-content').data('survey-id');
        
        if (!surveyId || !contentSectionId) {
            alert('Error: Missing survey or content section information. Please refresh the page.');
            return;
        }

        if (!content || content.trim() === '') {
            alert('Please enter some content before saving.');
            return;
        }

        // Show loading state
        const originalHtml = $button.html();
        $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

        $.ajax({
            url: `/surveyor/surveys/${surveyId}/content-sections/${contentSectionId}/update`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                content: content
            },
            success: function(response) {
                if (response.success) {
                    // Update the textarea with the saved content
                    $textarea.val(response.content_section.content);
                    
                    // Show success message
                    showToast('Content saved successfully!', 'success');
                    
                    // Mark as saved
                    $sectionItem.attr('data-saved', 'true');
                    $sectionItem.attr('data-has-content', 'true');
                } else {
                    alert(response.message || 'Error saving content. Please try again.');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Error saving content. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                alert(errorMessage);
            },
            complete: function() {
                // Restore button state
                $button.prop('disabled', false).html(originalHtml);
            }
        });
    });

    // Lock/Unlock Content Editing
    $(document).on('click', '.survey-data-mock-content-section-item [data-action="lock"]', function(e) {
        e.stopPropagation();
        const $button = $(this);
        const $sectionItem = $button.closest('.survey-data-mock-content-section-item');
        const $textarea = $sectionItem.find('.survey-data-mock-content-textarea');
        const isLocked = $textarea.prop('readonly');
        
        $textarea.prop('readonly', !isLocked);
        $button.find('i').toggleClass('fa-lock fa-unlock');
        $button.attr('title', isLocked ? 'Lock Editing' : 'Unlock Editing');
    });
});
</script>
@endpush
