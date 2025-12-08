<div class="sec-item {{ !$section->is_active ? 'inactive' : '' }}" data-id="{{ $section->id }}" onclick="selectSec({{ $section->id }})">
    <div class="drag-handle" onclick="event.stopPropagation()">
        <i class="fas fa-grip-vertical"></i>
    </div>
    <input type="checkbox" class="custom-check item-check" data-type="section" data-id="{{ $section->id }}" onclick="event.stopPropagation(); updateBulkBtns()">
    <div class="sec-name"
         data-type="section-definition"
         data-id="{{ $section->id }}"
         ondblclick="event.stopPropagation(); enableEdit(this)">
        {{ $section->display_name }}
    </div>
    
    <div class="sec-badges">
        @if($section->is_clonable)
            <span class="badge-sm badge-clonable" title="Surveyors can duplicate this section">
                <i class="fas fa-layer-group"></i> Duplicatable
            </span>
        @endif
        
        @if(!$section->is_active)
            <span class="badge-sm badge-inactive">Inactive</span>
        @endif
    </div>
    
    <div class="sec-actions" onclick="event.stopPropagation()">
        <button class="act-btn-sm clone-action" title="Create a copy of this section" onclick="cloneSec({{ $section->id }})">
            <i class="fas fa-copy"></i>
        </button>
        <button class="act-btn-sm" title="Edit" onclick="openEditSecModal({{ $section->id }})">
            <i class="fas fa-edit"></i>
        </button>
        <button class="act-btn-sm danger" title="Delete" onclick="deleteSec({{ $section->id }})">
            <i class="fas fa-trash"></i>
        </button>
    </div>
</div>
