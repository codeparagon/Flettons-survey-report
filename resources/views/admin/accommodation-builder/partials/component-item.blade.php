<div class="component-item" data-component-id="{{ $component->id }}">
    <div class="component-header" onclick="toggleComponent(this)">
        <div class="item-drag-handle" onclick="event.stopPropagation()">
            <i class="fas fa-grip-vertical"></i>
        </div>
        <i class="fas fa-chevron-down component-toggle"></i>
        <div class="item-icon">
            <i class="fas fa-cube"></i>
        </div>
        <div class="item-info">
            <div class="item-name"
                 data-type="accommodation-components"
                 data-id="{{ $component->id }}"
                 ondblclick="event.stopPropagation(); enableInlineEdit(this)">
                {{ $component->display_name }}
            </div>
            <div class="item-meta">
                {{ count($materials) }} materials
                @if(!$component->is_active)
                    <span class="status-badge inactive ml-2">Inactive</span>
                @endif
            </div>
        </div>
        <div class="item-actions" onclick="event.stopPropagation()">
            <button class="action-btn" title="Edit" ondblclick="enableInlineEdit(this.closest('.component-header').querySelector('.item-name'))">
                <i class="fas fa-edit"></i>
            </button>
            <button class="action-btn delete" title="Delete" onclick="deleteComponent({{ $component->id }})">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>
    
    <div class="component-content">
        <label style="font-weight: 600; font-size: 13px; color: #374151; margin-bottom: 8px; display: block;">
            Material Options
        </label>
        <div class="tags-container">
            @foreach($materials as $material)
                <span class="tag" data-id="{{ $material->id }}">
                    {{ $material->value }}
                    <span class="tag-remove" onclick="deleteMaterial({{ $material->id }})">&times;</span>
                </span>
            @endforeach
            <input type="text" class="tag-input" placeholder="Type and press Enter..." onkeydown="handleMaterialInput(event, {{ $component->id }})">
        </div>
    </div>
</div>












