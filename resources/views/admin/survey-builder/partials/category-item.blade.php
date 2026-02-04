<div class="cat-item" data-id="{{ $category->id }}">
    <div class="cat-head" onclick="toggleCat(this)">
        <div class="drag-handle" onclick="event.stopPropagation()">
            <i class="fas fa-grip-vertical"></i>
        </div>
        <input type="checkbox" class="custom-check item-check" data-type="category" data-id="{{ $category->id }}" onclick="event.stopPropagation(); updateBulkBtns()">
        <i class="fas fa-chevron-down cat-toggle"></i>
        <div class="cat-icon">
            @php
                $iconClass = $category->icon ?? 'fa fa-folder';
                // Check if icon class contains Pro-only duotone prefix (fad)
                // Fallback to solid (fas) version if duotone icon is detected
                // Note: far (regular) and fal (light) are available in FA6 free, but fad (duotone) is Pro-only
                if (preg_match('/\bfad\s+fa-/', $iconClass)) {
                    // Convert duotone Pro icon to solid free equivalent
                    $iconClass = preg_replace('/\bfad\s+/', 'fas ', $iconClass);
                }
            @endphp
            <i class="{{ $iconClass }}"></i>
        </div>
        <div class="cat-info">
            <div class="cat-name" 
                 data-type="categorie" 
                 data-id="{{ $category->id }}"
                 ondblclick="event.stopPropagation(); enableEdit(this)">
                {{ $category->display_name }}
            </div>
            <div class="cat-meta">
                {{ $category->subcategories->count() }} subcategories • 
                {{ $category->subcategories->sum(fn($s) => $s->sectionDefinitions->count()) }} sections
                @if(!$category->is_active)
                    <span class="badge-sm badge-inactive" style="margin-left: 8px;">Inactive</span>
                @endif
            </div>
        </div>
        <div class="cat-actions" onclick="event.stopPropagation()">
            <button class="act-btn" title="Add Subcategory" onclick="openAddSubcatModal({{ $category->id }}, '{{ addslashes($category->display_name) }}')">
                <i class="fas fa-plus"></i>
            </button>
            <button class="act-btn" title="Edit" onclick="enableEdit(this.closest('.cat-head').querySelector('.cat-name'))">
                <i class="fas fa-edit"></i>
            </button>
            <button class="act-btn danger" title="Delete" onclick="deleteCat({{ $category->id }})">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>
    
    <div class="cat-body">
        <div class="subcat-list">
            @foreach($category->subcategories as $subcategory)
                @include('admin.survey-builder.partials.subcategory-item', ['subcategory' => $subcategory])
            @endforeach
        </div>
        
        <button class="add-btn" onclick="openAddSubcatModal({{ $category->id }}, '{{ addslashes($category->display_name) }}')">
            <i class="fas fa-plus"></i> Add Subcategory
        </button>
    </div>
</div>
