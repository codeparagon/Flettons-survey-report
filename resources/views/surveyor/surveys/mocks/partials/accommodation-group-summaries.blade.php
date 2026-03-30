@php
    $groupedAccommodations = collect($accommodationSections ?? [])->groupBy('accommodation_type_id');
@endphp

@if($groupedAccommodations->isNotEmpty())
    <div class="survey-data-mock-accommodation-group-summaries">
        <div class="survey-data-mock-accommodation-group-summaries-header" data-accommodation-group-toggle>
            <h3 class="survey-data-mock-accommodation-group-summaries-title">Group summaries</h3>
            <span class="survey-data-mock-accommodation-group-summaries-hint">Combined narrative per component for all rooms of the same type (e.g. all bedrooms)</span>
            <i class="fas fa-chevron-down survey-data-mock-accommodation-group-summaries-chevron"></i>
        </div>
        <div class="survey-data-mock-accommodation-group-summaries-body collapse show">
            @foreach($groupedAccommodations as $typeId => $rows)
                @php
                    $firstRow = $rows->first();
                    $typeLabel = $firstRow['accommodation_type_name'] ?? $firstRow['name'] ?? 'Accommodation';
                    $components = $firstRow['components'] ?? [];
                @endphp
                <div class="survey-data-mock-accommodation-group-panel" data-accommodation-type-id="{{ $typeId }}">
                    <h4 class="survey-data-mock-accommodation-group-panel-title">{{ $typeLabel }} — all instances</h4>
                    @foreach($components as $component)
                        <div class="survey-data-mock-accommodation-group-component"
                             data-accommodation-type-id="{{ $typeId }}"
                             data-component-key="{{ $component['component_key'] }}">
                            <div class="survey-data-mock-accommodation-group-component-head">
                                <span class="survey-data-mock-accommodation-group-component-label">{{ $component['component_name'] ?? $component['component_key'] }}</span>
                                <span class="survey-data-mock-group-summary-status" aria-live="polite">Not generated</span>
                            </div>
                            <textarea class="survey-data-mock-group-summary-textarea" rows="6" placeholder="Generate a combined narrative for this component across all {{ strtolower($typeLabel) }} instances ({{ $rows->count() }} section{{ $rows->count() === 1 ? '' : 's' }})…"></textarea>
                            <div class="survey-data-mock-accommodation-group-component-actions">
                                <button type="button" class="survey-data-mock-btn survey-data-mock-btn--primary survey-data-mock-group-summary-generate">
                                    Generate / Regenerate
                                </button>
                                <button type="button" class="survey-data-mock-btn survey-data-mock-btn--secondary survey-data-mock-group-summary-copy">
                                    Copy
                                </button>
                                <button type="button" class="survey-data-mock-btn survey-data-mock-btn--ghost survey-data-mock-group-summary-clear">
                                    Clear
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
@endif
