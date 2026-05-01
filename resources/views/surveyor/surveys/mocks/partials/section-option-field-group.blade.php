@php
    $keyName = $field['key_name'];
    $uiGroup = $field['data_group'];
    $isMultiple = !empty($field['is_multiple']);
    $typeOptions = $surveyDataService->getOptionsForOptionTypeKey($keyName, $categoryName, $subcategoryKey);
    if ($keyName === 'section_type' && empty($typeOptions)) {
        $typeOptions = [$section['name']];
    }

    // Special case: For "Accommodation" sub-categories, Location options come from the survey's Accommodation rooms
    // (including cloned ones) and allow multi-select.
    $useAccommodationRoomLocations = $keyName === 'location'
        && !empty($accommodationRoomOptions ?? [])
        && !empty($subCategoryName ?? '')
        && stripos((string) $subCategoryName, 'accommodation') !== false;

    if ($useAccommodationRoomLocations) {
        $typeOptions = $accommodationRoomOptions;
        $isMultiple = true;
    }
@endphp
<div class="survey-data-mock-field-group"
     data-option-key="{{ $keyName }}"
     data-ui-group="{{ $uiGroup }}"
     data-option-multiple="{{ $isMultiple ? 'true' : 'false' }}">
    <label class="survey-data-mock-field-label">
        {{ $useAccommodationRoomLocations ? 'Location' : $field['label'] }}
    </label>
    <div class="survey-data-mock-button-group">
        @forelse($typeOptions as $optVal)
            <button type="button" class="survey-data-mock-button"
                    data-value="{{ $optVal }}"
                    data-group="{{ $uiGroup }}"
                    @if($isMultiple) data-multiple="true" @endif>{{ $optVal }}</button>
        @empty
            <span class="text-muted small">No options configured for this field.</span>
        @endforelse
    </div>
</div>
