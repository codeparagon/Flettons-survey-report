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
                @if(isset($defects) && count($defects) > 0)
                    &middot; {{ count($defects) }} defects
                @endif
                @if(isset($locations) && count($locations) > 0)
                    &middot; {{ count($locations) }} locations
                @endif
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

        <label style="font-weight: 600; font-size: 13px; color: #374151; margin: 16px 0 8px; display: block;">
            Component Defect Options
        </label>
        <div class="tags-container component-defects-container">
            @if(isset($defects))
                @foreach($defects as $defect)
                    <span class="tag" data-id="{{ $defect->id }}">
                        {{ $defect->value }}
                        <span class="tag-remove" onclick="deleteDefect({{ $defect->id }})">&times;</span>
                    </span>
                @endforeach
            @endif
            <input type="text"
                   class="tag-input"
                   placeholder="Type and press Enter..."
                   onkeydown="handleComponentDefectInput(event, {{ $component->id }})">
        </div>

        <label style="font-weight: 600; font-size: 13px; color: #374151; margin: 16px 0 8px; display: block;">
            Component Location Options (optional)
        </label>
        <div class="tags-container component-locations-container">
            @if(isset($locations))
                @foreach($locations as $loc)
                    <span class="tag" data-id="{{ $loc->id }}">
                        {{ $loc->value }}
                        <span class="tag-remove" onclick="deleteLocation({{ $loc->id }})">&times;</span>
                    </span>
                @endforeach
            @endif
            <input type="text"
                   class="tag-input"
                   placeholder="Type and press Enter..."
                   onkeydown="handleComponentLocationInput(event, {{ $component->id }})">
        </div>
    </div>
</div>













