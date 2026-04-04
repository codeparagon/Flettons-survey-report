@php
    $groupedAccommodations = collect($accommodationSections ?? [])->groupBy('accommodation_type_id');
    $summaries = $accommodationComponentSummaries ?? [];
@endphp

@if($groupedAccommodations->isNotEmpty())
    <div class="survey-data-mock-accommodation-group-summaries">
        <h3 class="survey-data-mock-combined-narratives-title">Combined narratives (ChatGPT)</h3>
        <p class="survey-data-mock-combined-narratives-lead">One narrative per component across all rooms of the same type (e.g. all bedrooms). Expand each block to edit; refresh runs ChatGPT; lock saves and locks the text.</p>

        @foreach($groupedAccommodations as $typeId => $rows)
            @php
                $firstRow = $rows->first();
                $typeLabel = $firstRow['accommodation_type_name'] ?? $firstRow['name'] ?? 'Accommodation';
                $components = $firstRow['components'] ?? [];
            @endphp
            <div class="survey-data-mock-combined-narratives-block" id="survey-accommodation-combined-panel-{{ $typeId }}" data-accommodation-type-id="{{ $typeId }}">
                <h4 class="survey-data-mock-combined-narratives-type-heading">{{ $typeLabel }} — all instances ({{ $rows->count() }} room{{ $rows->count() === 1 ? '' : 's' }})</h4>
                <div class="survey-data-mock-sections survey-data-mock-combined-narratives-sections" style="gap: 0.75rem;">
                    @foreach($components as $component)
                        @php
                            $ck = $component['component_key'] ?? '';
                            $cname = $component['component_name'] ?? $ck;
                            $cid = $component['component_id'] ?? null;
                            if (!$cid && $ck !== '') {
                                $cid = \App\Models\SurveyAccommodationComponent::where('key_name', $ck)->value('id');
                            }
                            $saved = $summaries[$typeId][$ck] ?? null;
                            $initialContent = $saved['content'] ?? '';
                            $titleText = $typeLabel . ' [' . $cname . ']';
                        @endphp
                        <div class="survey-data-mock-section-item survey-data-mock-combined-narrative-item survey-data-mock-accommodation-group-component"
                             data-accommodation-type-id="{{ $typeId }}"
                             data-component-id="{{ $cid }}"
                             data-component-key="{{ $ck }}"
                             data-locked="false">
                            <div class="survey-data-mock-section-header" data-expandable="true">
                                <div class="survey-data-mock-section-name">{{ $titleText }}</div>
                                <div class="survey-data-mock-section-status">
                                    <span class="survey-data-mock-status-info">
                                        <span class="survey-data-mock-group-summary-status {{ trim($initialContent) !== '' ? 'is-fresh' : '' }}" aria-live="polite">{{ trim($initialContent) !== '' ? 'Up to date' : 'Not generated' }}</span>
                                    </span>
                                    <span class="survey-data-mock-condition-badge survey-data-mock-condition-badge--ni">NI</span>
                                    <i class="fas fa-chevron-down survey-data-mock-expand-icon"></i>
                                </div>
                            </div>

                            <div class="survey-data-mock-section-title-bar" style="display: none;">
                                <h3 class="survey-data-mock-section-title-text">{{ $titleText }}</h3>
                                <div class="d-flex align-items-center" style="gap: 10px;">
                                    <span class="survey-data-mock-condition-badge survey-data-mock-condition-badge--ni">NI</span>
                                    <i class="fas fa-chevron-up survey-data-mock-section-title-collapse"></i>
                                </div>
                            </div>

                            <div class="survey-data-mock-report-content survey-data-mock-report-content--combined-narrative" style="display: none;">
                                <div class="survey-data-mock-report-content-wrapper">
                                    <textarea class="survey-data-mock-report-textarea survey-data-mock-group-summary-textarea" rows="12" placeholder="Combined narrative for {{ $cname }} across all {{ strtolower($typeLabel) }} sections ({{ $rows->count() }} room{{ $rows->count() === 1 ? '' : 's' }})…">{{ $initialContent }}</textarea>
                                    <div class="survey-data-mock-action-icons">
                                        <button type="button" class="survey-data-mock-action-icon-btn" data-action="speaker" title="Text to Speech">
                                            <i class="fas fa-volume-up"></i>
                                        </button>
                                        <button type="button" class="survey-data-mock-action-icon-btn" data-action="lock" title="Save and lock / unlock editing">
                                            <i class="fas fa-lock"></i>
                                        </button>
                                        <button type="button" class="survey-data-mock-action-icon-btn" data-action="edit" title="Unlock and edit">
                                            <i class="fas fa-pencil-alt"></i>
                                        </button>
                                        <button type="button" class="survey-data-mock-action-icon-btn" data-action="refresh" title="Regenerate with ChatGPT">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                        <button type="button" class="survey-data-mock-action-icon-btn" data-action="eye" title="Preview">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <p class="survey-data-mock-combined-narrative-util">
                                        <button type="button" class="survey-data-mock-group-summary-copy survey-data-mock-combined-util-link">Copy</button>
                                        <span class="survey-data-mock-combined-util-sep" aria-hidden="true">·</span>
                                        <button type="button" class="survey-data-mock-group-summary-clear survey-data-mock-combined-util-link">Clear</button>
                                    </p>
                                    <button type="button" class="survey-data-mock-group-summary-generate" tabindex="-1" aria-hidden="true" style="position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0;">Generate</button>
                                    <button type="button" class="survey-data-mock-group-summary-save" tabindex="-1" aria-hidden="true" style="position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0;">Save</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
@endif
