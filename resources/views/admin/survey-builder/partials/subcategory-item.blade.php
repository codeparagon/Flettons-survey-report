<div class="subcat-item" data-id="{{ $subcategory->id }}">
    <div class="subcat-head" onclick="toggleSubcat(this)">
        <div class="drag-handle" onclick="event.stopPropagation()">
            <i class="fas fa-grip-vertical"></i>
        </div>
        <input type="checkbox" class="custom-check item-check" data-type="subcategory" data-id="{{ $subcategory->id }}" onclick="event.stopPropagation(); updateBulkBtns()">
        <i class="fas fa-chevron-down subcat-toggle"></i>
        <div class="subcat-name"
             data-type="subcategorie"
             data-id="{{ $subcategory->id }}"
             ondblclick="event.stopPropagation(); enableEdit(this)">
            {{ $subcategory->display_name }}
        </div>
        <span class="subcat-count">{{ $subcategory->sectionDefinitions->count() }} sections</span>
        @if(!$subcategory->is_active)
            <span class="badge-sm badge-inactive" style="margin-right: 8px;">Inactive</span>
        @endif
        <div class="subcat-actions" onclick="event.stopPropagation()">
            <button class="act-btn-sm" title="Add Section" onclick="openAddSecModal({{ $subcategory->id }})">
                <i class="fas fa-plus"></i>
            </button>
            <button class="act-btn-sm" title="Edit" onclick="enableEdit(this.closest('.subcat-head').querySelector('.subcat-name'))">
                <i class="fas fa-edit"></i>
            </button>
            <button class="act-btn-sm danger" title="Delete" onclick="deleteSubcat({{ $subcategory->id }})">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>
    
    <div class="subcat-body">
        <div class="sec-list">
            @foreach($subcategory->sectionDefinitions as $section)
                @include('admin.survey-builder.partials.section-item', ['section' => $section])
            @endforeach
        </div>
        
        <button class="add-btn" onclick="openAddSecModal({{ $subcategory->id }})" style="margin-left: 38px;">
            <i class="fas fa-plus"></i> Add Section
        </button>
    </div>
</div>
