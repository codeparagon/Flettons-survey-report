@props(['field', 'value' => '', 'errors' => null])

@php
    $fieldName = 'field_' . $field->id;
    $fieldId = 'field_' . $field->id;
    $errorClass = $errors && $errors->has($fieldName) ? 'is-invalid' : '';
    $errorMessage = $errors && $errors->has($fieldName) ? $errors->first($fieldName) : null;
@endphp

<div class="form-group">
    <label for="{{ $fieldId }}">
        {{ $field->field_label }}
        @if($field->is_required)
            <span class="text-danger">*</span>
        @endif
    </label>

    @switch($field->field_type)
        @case('textarea')
            <textarea class="form-control {{ $errorClass }}" 
                      id="{{ $fieldId }}" 
                      name="{{ $fieldName }}" 
                      rows="4"
                      @if($field->is_required) required @endif
                      placeholder="{{ $field->default_value ?? '' }}">{{ old($fieldName, $value) }}</textarea>
            @break

        @case('date')
            <input type="date" 
                   class="form-control {{ $errorClass }}" 
                   id="{{ $fieldId }}" 
                   name="{{ $fieldName }}" 
                   value="{{ old($fieldName, $value) }}"
                   @if($field->is_required) required @endif>
            @break

        @case('numeric')
            <input type="number" 
                   class="form-control {{ $errorClass }}" 
                   id="{{ $fieldId }}" 
                   name="{{ $fieldName }}" 
                   value="{{ old($fieldName, $value) }}"
                   @if(isset($field->validation_rules['min'])) min="{{ $field->validation_rules['min'] }}" @endif
                   @if(isset($field->validation_rules['max'])) max="{{ $field->validation_rules['max'] }}" @endif
                   step="0.01"
                   @if($field->is_required) required @endif>
            @break

        @case('dropdown')
            <select class="form-control {{ $errorClass }}" 
                    id="{{ $fieldId }}" 
                    name="{{ $fieldName }}" 
                    @if($field->is_required) required @endif>
                <option value="">-- Select --</option>
                @if($field->options && is_array($field->options))
                    @foreach($field->options as $option)
                        <option value="{{ $option }}" {{ old($fieldName, $value) == $option ? 'selected' : '' }}>
                            {{ $option }}
                        </option>
                    @endforeach
                @endif
            </select>
            @break

        @case('rating')
            <select class="form-control {{ $errorClass }}" 
                    id="{{ $fieldId }}" 
                    name="{{ $fieldName }}" 
                    @if($field->is_required) required @endif>
                <option value="">-- Select Rating --</option>
                <option value="excellent" {{ old($fieldName, $value) == 'excellent' ? 'selected' : '' }}>Excellent - No issues found</option>
                <option value="good" {{ old($fieldName, $value) == 'good' ? 'selected' : '' }}>Good - Minor maintenance needed</option>
                <option value="fair" {{ old($fieldName, $value) == 'fair' ? 'selected' : '' }}>Fair - Some repairs required</option>
                <option value="poor" {{ old($fieldName, $value) == 'poor' ? 'selected' : '' }}>Poor - Major repairs needed</option>
            </select>
            @break

        @case('single-text')
        @default
            <input type="text" 
                   class="form-control {{ $errorClass }}" 
                   id="{{ $fieldId }}" 
                   name="{{ $fieldName }}" 
                   value="{{ old($fieldName, $value ?: ($field->default_value ?? '')) }}"
                   @if($field->is_required) required @endif
                   placeholder="{{ $field->default_value ?? '' }}">
            @break
    @endswitch

    @if($field->help_text)
        <small class="form-text text-muted">{{ $field->help_text }}</small>
    @endif

    @if($errorMessage)
        <div class="invalid-feedback d-block">{{ $errorMessage }}</div>
    @endif
</div>




