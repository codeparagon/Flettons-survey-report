@php
    use Illuminate\Support\Facades\Storage;
@endphp

<div class="survey-hierarchy-sidebar">
    <div class="card sticky-top" style="top: 80px;">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-list"></i> Survey Sections
            </h5>
        </div>
        <div class="card-body" style="max-height: calc(100vh - 200px); overflow-y: auto;">
            {{-- Progress Summary --}}
            @if(isset($progress))
            <div class="progress-summary mb-3 p-2" style="background-color: #f8f9fa; border-radius: 8px;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small">Progress</span>
                    <span class="font-weight-bold" style="color: #C1EC4A;">{{ $progress['percentage'] }}%</span>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar" 
                         style="width: {{ $progress['percentage'] }}%; background-color: #C1EC4A;" 
                         role="progressbar">
                    </div>
                </div>
                <small class="text-muted">
                    {{ $progress['completed'] }} of {{ $progress['total'] }} sections completed
                </small>
            </div>
            @endif

            {{-- Hierarchy Navigation --}}
            @if(isset($hierarchy) && $hierarchy->count() > 0)
                @foreach($hierarchy as $category)
                    @php
                        $sectionCount = is_array($category['sections']) ? count($category['sections']) : ($category['sections']->count() ?? 0);
                    @endphp
                    @if($sectionCount > 0)
                    <div class="category-group mb-3">
                        <div class="category-header" 
                             data-toggle="collapse" 
                             data-target="#category-{{ $category['id'] }}" 
                             aria-expanded="true"
                             style="cursor: pointer; padding: 10px; background-color: #f8f9fa; border-radius: 6px; display: flex; align-items: center; justify-content: space-between;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                @if($category['icon'])
                                    <i class="{{ $category['icon'] }}" style="color: #C1EC4A; font-size: 18px;"></i>
                                @endif
                                <span class="font-weight-bold" style="color: #1A202C;">{{ $category['display_name'] }}</span>
                            </div>
                            <i class="fas fa-chevron-down collapse-icon" style="color: #6B7280; transition: transform 0.3s;"></i>
                        </div>
                        
                        <div id="category-{{ $category['id'] }}" class="collapse show">
                            <div class="section-list mt-2">
                                @foreach($category['sections'] as $sectionItem)
                                    @php
                                        $isActive = isset($section) && $section->id === $sectionItem['id'];
                                        $isCompleted = $sectionItem['is_completed'] ?? false;
                                        $sectionModel = $sectionItem['model'] ?? null;
                                    @endphp
                                    @if($sectionModel)
                                    <a href="{{ route('surveyor.survey.section.form', [$survey, $sectionModel]) }}" 
                                       class="section-link {{ $isActive ? 'active' : '' }} {{ $isCompleted ? 'completed' : '' }}"
                                       style="display: block; padding: 10px 15px; margin-bottom: 5px; border-radius: 6px; text-decoration: none; transition: all 0.3s; border-left: 3px solid transparent;">
                                        <div style="display: flex; align-items: center; justify-content: space-between;">
                                            <div style="display: flex; align-items: center; gap: 10px; flex: 1;">
                                                @if($sectionItem['icon'])
                                                    <img src="{{ asset($sectionItem['icon']) }}" 
                                                         alt="{{ $sectionItem['display_name'] }}"
                                                         style="width: 24px; height: 24px; object-fit: contain;">
                                                @else
                                                    <i class="fas fa-circle" style="font-size: 8px; color: #C1EC4A;"></i>
                                                @endif
                                                <span style="color: #1A202C; font-size: 14px; font-weight: {{ $isActive ? '600' : '400' }};">
                                                    {{ $sectionItem['display_name'] }}
                                                </span>
                                            </div>
                                            @if($isCompleted)
                                                <i class="fas fa-check-circle" style="color: #C1EC4A; font-size: 16px;"></i>
                                            @endif
                                            @if($isActive)
                                                <i class="fas fa-arrow-right" style="color: #C1EC4A; font-size: 14px; margin-left: 5px;"></i>
                                            @endif
                                        </div>
                                    </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
            @endif
        </div>
    </div>
</div>

<style>
.survey-hierarchy-sidebar .section-link {
    background-color: #ffffff;
    border-left-color: transparent !important;
}

.survey-hierarchy-sidebar .section-link:hover {
    background-color: #f8f9fa;
    border-left-color: #C1EC4A !important;
    transform: translateX(3px);
}

.survey-hierarchy-sidebar .section-link.active {
    background-color: rgba(193, 236, 74, 0.1);
    border-left-color: #C1EC4A !important;
    font-weight: 600;
}

.survey-hierarchy-sidebar .section-link.completed {
    opacity: 0.8;
}

.survey-hierarchy-sidebar .category-header:hover {
    background-color: #e9ecef !important;
}

.survey-hierarchy-sidebar .category-header[aria-expanded="false"] .collapse-icon {
    transform: rotate(-90deg);
}

.survey-hierarchy-sidebar .category-header[aria-expanded="true"] .collapse-icon {
    transform: rotate(0deg);
}

.survey-hierarchy-sidebar .card-body {
    scrollbar-width: thin;
    scrollbar-color: #C1EC4A #f1f1f1;
}

.survey-hierarchy-sidebar .card-body::-webkit-scrollbar {
    width: 6px;
}

.survey-hierarchy-sidebar .card-body::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.survey-hierarchy-sidebar .card-body::-webkit-scrollbar-thumb {
    background: #C1EC4A;
    border-radius: 10px;
}

.survey-hierarchy-sidebar .card-body::-webkit-scrollbar-thumb:hover {
    background: #B0D93F;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-expand category containing active section
    const activeSection = document.querySelector('.section-link.active');
    if (activeSection) {
        const categoryCollapse = activeSection.closest('.collapse');
        if (categoryCollapse && !categoryCollapse.classList.contains('show')) {
            const categoryHeader = categoryCollapse.previousElementSibling;
            if (categoryHeader) {
                categoryHeader.click();
            }
        }
    }
});
</script>
