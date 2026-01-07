<div class="item-card" data-type-id="{{ $type->id }}">
    <div class="item-drag-handle">
        <i class="fas fa-grip-vertical"></i>
    </div>
    <div class="item-icon">
        <i class="fas fa-door-open"></i>
    </div>
    <div class="item-info">
        <div class="item-name"
             data-type="accommodation-types"
             data-id="{{ $type->id }}"
             ondblclick="enableInlineEdit(this)">
            {{ $type->display_name }}
        </div>
        <div class="item-meta">
            Key: {{ $type->key_name }}
            @if(!$type->is_active)
                <span class="status-badge inactive ml-2">Inactive</span>
            @endif
        </div>
    </div>
    <div class="item-actions">
        <button class="action-btn" title="Clone" onclick="cloneType({{ $type->id }})">
            <i class="fas fa-clone"></i>
        </button>
        <button class="action-btn" title="Edit" ondblclick="enableInlineEdit(this.closest('.item-card').querySelector('.item-name'))">
            <i class="fas fa-edit"></i>
        </button>
        <button class="action-btn delete" title="Delete" onclick="deleteType({{ $type->id }})">
            <i class="fas fa-trash"></i>
        </button>
    </div>
</div>










