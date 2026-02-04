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
    
    @if(isset($optionTypes) && $optionTypes->count() > 0)
        @foreach($optionTypes as $keyName => $optionType)
            @if($optionType->options && $optionType->options->count() > 0)
            <div class="preview-field">
                <div class="preview-label">{{ $optionType->label }}</div>
                <div class="preview-options">
                    @foreach($optionType->options as $option)
                    <span class="preview-opt">{{ $option->value }}</span>
                    @endforeach
                </div>
            </div>
            @else
            <div class="preview-field">
                <div class="preview-label">{{ $optionType->label }}</div>
                <div class="preview-options">
                    <span class="preview-opt" style="background: #f3f4f6; color: #9ca3af; font-style: italic;">No options available</span>
                </div>
            </div>
            @endif
        @endforeach
    @else
    <div class="preview-field">
        <div class="preview-label" style="color: #9ca3af;">Available Options</div>
        <div style="background: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px; color: #6b7280; font-size: 13px;">
            No options configured. Configure options in <a href="{{ route('admin.survey-options.index') }}" style="color: #1a202c; text-decoration: underline;">Global Options</a>.
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
