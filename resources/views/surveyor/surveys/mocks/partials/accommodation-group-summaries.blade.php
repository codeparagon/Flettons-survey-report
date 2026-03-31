@php
    $groupedAccommodations = collect($accommodationSections ?? [])->groupBy('accommodation_type_id');
    $summaries = $accommodationComponentSummaries ?? [];
@endphp

@if($groupedAccommodations->isNotEmpty())
    <div class="survey-data-mock-accommodation-group-summaries">
        <h3 class="survey-data-mock-combined-narratives-title">Combined narratives (ChatGPT)</h3>
        <p class="survey-data-mock-combined-narratives-lead">One narrative per component across all rooms of the same type (e.g. all bedrooms). Generate after saving room data, or edit and save manually.</p>

        @foreach($groupedAccommodations as $typeId => $rows)
            @php
                $firstRow = $rows->first();
                $typeLabel = $firstRow['accommodation_type_name'] ?? $firstRow['name'] ?? 'Accommodation';
                $components = $firstRow['components'] ?? [];
            @endphp
            <div class="survey-data-mock-accommodation-group-panel" id="survey-accommodation-combined-panel-{{ $typeId }}" data-accommodation-type-id="{{ $typeId }}">
                <h4 class="survey-data-mock-accommodation-group-panel-title">{{ $typeLabel }} — all instances</h4>
                @foreach($components as $component)
                    @php
                        $ck = $component['component_key'] ?? '';
                        $cid = $component['component_id'] ?? null;
                        if (!$cid && $ck !== '') {
                            $cid = \App\Models\SurveyAccommodationComponent::where('key_name', $ck)->value('id');
                        }
                        $saved = $summaries[$typeId][$ck] ?? null;
                        $initialContent = $saved['content'] ?? '';
                    @endphp
                    <div class="survey-data-mock-accommodation-group-component"
                         data-accommodation-type-id="{{ $typeId }}"
                         data-component-id="{{ $cid }}"
                         data-component-key="{{ $ck }}">
                        <div class="survey-data-mock-accommodation-group-component-head">
                            <span class="survey-data-mock-accommodation-group-component-label">{{ $component['component_name'] ?? $ck }}</span>
                            <span class="survey-data-mock-group-summary-status" aria-live="polite">{{ trim($initialContent) !== '' ? 'Saved' : 'Not generated' }}</span>
                        </div>
                        <textarea class="survey-data-mock-group-summary-textarea" rows="8" placeholder="Combined narrative for {{ $component['component_name'] ?? $ck }} across all {{ strtolower($typeLabel) }} sections ({{ $rows->count() }} room{{ $rows->count() === 1 ? '' : 's' }})…">{{ $initialContent }}</textarea>
                        <div class="survey-data-mock-accommodation-group-component-actions">
                            <button type="button" class="survey-data-mock-btn survey-data-mock-btn--primary survey-data-mock-group-summary-generate">
                                Generate with ChatGPT
                            </button>
                            <button type="button" class="survey-data-mock-btn survey-data-mock-btn--secondary survey-data-mock-group-summary-save">
                                Save text
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
@endif
