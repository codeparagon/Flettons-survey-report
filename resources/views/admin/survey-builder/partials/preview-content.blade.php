@if(isset($section))
<div class="preview-section">
    <h4>{{ $section->display_name }}</h4>
    
    @if($section->sectionTypes && count($section->sectionTypes) > 0)
    <div class="preview-field">
        <div class="preview-label">Section Type</div>
        <div class="preview-options">
            @foreach($section->sectionTypes as $type)
            <span class="preview-opt">{{ $type }}</span>
            @endforeach
        </div>
    </div>
    @endif
    
    @if(isset($optionTypes['location']) && $optionTypes['location']->options->count() > 0)
    <div class="preview-field">
        <div class="preview-label">Location</div>
        <div class="preview-options">
            @foreach($optionTypes['location']->options as $option)
            <span class="preview-opt">{{ $option->value }}</span>
            @endforeach
        </div>
    </div>
    @endif
    
    @if(isset($optionTypes['structure']) && $optionTypes['structure']->options->count() > 0)
    <div class="preview-field">
        <div class="preview-label">Structure</div>
        <div class="preview-options">
            @foreach($optionTypes['structure']->options as $option)
            <span class="preview-opt">{{ $option->value }}</span>
            @endforeach
        </div>
    </div>
    @endif
    
    @if(isset($optionTypes['material']) && $optionTypes['material']->options->count() > 0)
    <div class="preview-field">
        <div class="preview-label">Material</div>
        <div class="preview-options">
            @foreach($optionTypes['material']->options as $option)
            <span class="preview-opt">{{ $option->value }}</span>
            @endforeach
        </div>
    </div>
    @endif
    
    @if(isset($optionTypes['defects']) && $optionTypes['defects']->options->count() > 0)
    <div class="preview-field">
        <div class="preview-label">Defects</div>
        <div class="preview-options">
            @foreach($optionTypes['defects']->options as $option)
            <span class="preview-opt">{{ $option->value }}</span>
            @endforeach
        </div>
    </div>
    @endif
    
    @if(isset($optionTypes['remaining_life']) && $optionTypes['remaining_life']->options->count() > 0)
    <div class="preview-field">
        <div class="preview-label">Remaining Life</div>
        <div class="preview-options">
            @foreach($optionTypes['remaining_life']->options as $option)
            <span class="preview-opt">{{ $option->value }}</span>
            @endforeach
        </div>
    </div>
    @endif
    
    <div class="preview-field">
        <div class="preview-label">Condition Rating</div>
        <div class="preview-options">
            <span class="preview-opt" style="background: #dcfce7; color: #166534;">1 - Good</span>
            <span class="preview-opt" style="background: #fef3c7; color: #92400e;">2 - Fair</span>
            <span class="preview-opt" style="background: #fee2e2; color: #dc2626;">3 - Poor</span>
        </div>
    </div>
    
    <div class="preview-field">
        <div class="preview-label">Notes</div>
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px; min-height: 80px; color: #9ca3af; font-size: 13px;">
            Surveyor notes will appear here...
        </div>
    </div>
    
    <div class="preview-field">
        <div class="preview-label">Photos</div>
        <div style="display: flex; gap: 8px;">
            <div style="width: 60px; height: 60px; background: #f3f4f6; border: 2px dashed #d1d5db; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #9ca3af;">
                <i class="fas fa-camera"></i>
            </div>
            <div style="width: 60px; height: 60px; background: #f3f4f6; border: 2px dashed #d1d5db; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #9ca3af;">
                <i class="fas fa-plus"></i>
            </div>
        </div>
    </div>
</div>
@else
<div class="preview-empty">
    <i class="fas fa-hand-pointer"></i>
    <p>Click on a section to preview how it will appear to surveyors.</p>
</div>
@endif
