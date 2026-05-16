@php
    $childCount = count($section['child_sections'] ?? []);
    $mergedText = trim((string) ($section['merged_report_content'] ?? ''));
    $hasReport = !empty($section['has_report']);
    $photoCount = isset($section['aggregate_photo_count']) ? (int) $section['aggregate_photo_count'] : count($section['photos'] ?? []);
@endphp
<div class="survey-data-mock-section-item survey-data-mock-acc-comp-merged survey-data-mock-combined-narrative-item{{ $hasReport ? ' expanded' : '' }}"
     data-merged-acc-host="1"
     data-section-definition-id="{{ $section['section_id'] }}"
     data-acc-component-key="{{ $section['acc_component_key'] ?? '' }}"
     data-has-report="{{ $hasReport ? 'true' : 'false' }}"
     data-saved="{{ $hasReport ? 'true' : 'false' }}"
     data-locked="false">
    <div class="survey-data-mock-section-header" data-expandable="true">
        <div class="survey-data-mock-section-name">
            {{ $section['name'] }}
            @if($childCount > 1)
                <span class="text-muted" style="font-size:0.8125rem;font-weight:400;">({{ $childCount }} locations)</span>
            @endif
        </div>
        <div class="survey-data-mock-section-status">
            <span class="survey-data-mock-status-info">
                <i class="fas fa-camera survey-data-mock-status-icon"></i>
                <span class="survey-data-mock-status-text">{{ $photoCount }}</span>
                <span class="survey-data-mock-status-separator">|</span>
                <i class="fas fa-sticky-note survey-data-mock-status-icon"></i>
                <span class="survey-data-mock-status-text">{{ $section['completion'] ?? 0 }}/{{ $section['total'] ?? 0 }}</span>
            </span>
            <span class="survey-data-mock-condition-badge survey-data-mock-condition-badge--{{ $section['condition_rating'] ?? 'ni' }}"
                  data-current-rating="{{ $section['condition_rating'] ?? 'ni' }}">
                {{ $section['condition_rating'] ?? 'NI' }}
            </span>
            <i class="fas fa-chevron-down survey-data-mock-expand-icon"></i>
        </div>
    </div>

    <div class="survey-data-mock-section-title-bar" style="{{ $hasReport ? '' : 'display: none;' }}">
        <h3 class="survey-data-mock-section-title-text">{{ $section['name'] }}</h3>
        <div class="d-flex align-items-center" style="gap: 10px;">
            <span class="survey-data-mock-condition-badge survey-data-mock-condition-badge--{{ $section['condition_rating'] ?? 'ni' }}"
                  data-current-rating="{{ $section['condition_rating'] ?? 'ni' }}">
                {{ $section['condition_rating'] ?? 'NI' }}
            </span>
            <i class="fas fa-chevron-up survey-data-mock-section-title-collapse"></i>
        </div>
    </div>

    <div class="survey-data-mock-report-content" data-merged-report-wrap="1" @if(!$hasReport) style="display: none;" @endif>
        <div class="survey-data-mock-report-content-wrapper">
            <p class="survey-data-mock-field-label" style="margin:0 0 0.5rem 0;">Surveyor's overall opinion (combined for all rooms)</p>
            <textarea class="survey-data-mock-report-textarea" rows="14" readonly
                      data-merged-component-report-textarea="1"
                      placeholder="Combined narrative will appear after you save each location form.">{{ $mergedText }}</textarea>
            <div class="survey-data-mock-action-icons">
                <button type="button" class="survey-data-mock-action-icon-btn" data-action="speaker" title="Text to Speech">
                    <i class="fas fa-volume-up"></i>
                </button>
                <button type="button" class="survey-data-mock-action-icon-btn" data-action="lock" title="Lock/Unlock Editing">
                    <i class="fas fa-lock"></i>
                </button>
                <button type="button" class="survey-data-mock-action-icon-btn" data-action="edit" title="Edit all location forms">
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

    <div class="survey-data-mock-acc-comp-merged-children" style="{{ $hasReport ? 'display: none;' : 'display: block;' }}">
        @foreach($section['child_sections'] ?? [] as $childSection)
            @include('surveyor.surveys.mocks.partials.section-item', [
                'section' => $childSection,
                'categoryName' => $categoryName,
                'subCategoryName' => $subCategoryName ?? null,
                'optionsMapping' => $optionsMapping ?? [],
                'accommodationRoomOptions' => $accommodationRoomOptions ?? [],
            ])
        @endforeach
    </div>
</div>
