# Dual Area Component Flow

## Overview
Rework the survey input “+” flow so each new component gets its own distinct header and supports selecting both a primary and secondary area/location.

## Tasks

1. Update the component template and default component to include separate Primary and Secondary area selectors (with matching hidden inputs).
2. Change the add-component handler so every new component renders with a unique header label (e.g., Component 2) before areas are chosen.
3. Adjust JS helpers (`setupSingleSelection`, `updateComponentHeaderTitle`, `saveComponent`, `copyComponentData`, `updateAssessmentIndices`) to manage the dual area values and keep headings/data in sync.
4. Refresh the header styling to ensure area text stays bright white and visually distinct for every component.

## Files
- `resources/views/surveyor/surveys/tabs/input.blade.php`


