{{-- Output Tab Content - Dynamic Report Sections --}}
@php
    $sectionTitles = [
        'executive-summary' => 'Executive Summary',
        'key-findings' => 'Key Findings',
        'recommendations' => 'Recommendations',
        'estimated-repair-costs' => 'Estimated Repair Costs',
        'maintenance-costs' => 'Maintenance Costs',
        'total-costs' => 'Total Costs',
        'survey-report' => 'Survey Report',
        'photographs' => 'Photographs',
        'supporting-documents' => 'Supporting Documents',
        'location-maps' => 'Location Maps',
        'satellite-images' => 'Satellite Images',
        'planning-maps' => 'Planning Maps',
        'complete-report' => 'Complete Report',
        'report-summary' => 'Report Summary',
        'appendices' => 'Appendices',
    ];
    
    $sectionTitle = $sectionTitles[$section] ?? 'Report Section';
@endphp

<div class="survey-tab-content">
    <div class="survey-report-section">
        <div class="survey-content-header">
            <h2 class="survey-tab-title">{{ $sectionTitle }}</h2>
        </div>
        
        <div class="survey-report-editor">
            <form class="survey-report-form" id="report-content-form">
                @csrf
                <input type="hidden" name="section" value="{{ $section }}">
                
                <div class="survey-form-section">
                    <label class="survey-form-label">Report Content</label>
                    <textarea class="survey-report-textarea" name="content" id="report-content" rows="20" 
                              placeholder="Enter report content for {{ $sectionTitle }}...">{{ old('content', '') }}</textarea>
                </div>
                
                @if(in_array($section, ['estimated-repair-costs', 'maintenance-costs', 'total-costs']))
                <div class="survey-form-section">
                    <label class="survey-form-label">Cost Breakdown</label>
                    <div class="survey-cost-breakdown">
                        <div class="survey-cost-item">
                            <input type="text" class="survey-form-input" name="cost_item[]" placeholder="Item description">
                            <input type="number" class="survey-form-input" name="cost_amount[]" placeholder="Amount (£)" step="0.01">
                            <button type="button" class="survey-remove-cost-btn" onclick="this.parentElement.remove()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <button type="button" class="survey-add-cost-btn" id="add-cost-item">
                        <i class="fas fa-plus"></i> Add Cost Item
                    </button>
                </div>
                @endif
                
                @if($section === 'photographs')
                <div class="survey-form-section">
                    <label class="survey-form-label">Uploaded Photographs</label>
                    <div class="survey-photos-grid" id="photos-grid">
                        <p class="survey-placeholder-text">No photographs uploaded yet.</p>
                    </div>
                    <button type="button" class="survey-add-image-btn" id="add-photo-btn">
                        <i class="fas fa-plus"></i> Add Photograph
                    </button>
                </div>
                @endif
                
                <div class="survey-form-actions">
                    <button type="button" class="survey-btn survey-btn-secondary" id="save-draft-btn">Save Draft</button>
                    <button type="submit" class="survey-btn survey-btn-primary" id="save-content-btn">Save & Continue</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
.survey-report-section {
    max-width: 100%;
}

.survey-report-editor {
    margin-top: 2rem;
}

.survey-report-textarea {
    width: 100%;
    min-height: 400px;
    padding: 1.5rem;
    font-size: 1.125rem;
    line-height: 1.7;
    border: 1px solid #D1D5DB;
    border-radius: 8px;
    background: #FFFFFF;
    color: #1A202C;
    font-family: inherit;
    resize: vertical;
}

.survey-report-textarea:focus {
    outline: none;
    border-color: #C1EC4A;
    box-shadow: 0 0 0 3px rgba(193, 236, 74, 0.1);
}

.survey-cost-breakdown {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 1rem;
}

.survey-cost-item {
    display: grid;
    grid-template-columns: 2fr 1fr auto;
    gap: 1rem;
    align-items: center;
}

.survey-remove-cost-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 1px solid #EF4444;
    background: #FFFFFF;
    color: #EF4444;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.survey-remove-cost-btn:hover {
    background: #FEE2E2;
}

.survey-add-cost-btn {
    padding: 0.875rem 1.75rem;
    font-size: 1.125rem;
    font-weight: 600;
    border: 1px solid #D1D5DB;
    border-radius: 8px;
    background: #F9FAFB;
    color: #374151;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
}

.survey-add-cost-btn:hover {
    background: #E5E7EB;
    border-color: #9CA3AF;
}

.survey-photos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
    min-height: 100px;
}

.survey-photos-grid .survey-placeholder-text {
    grid-column: 1 / -1;
    text-align: center;
    color: #6B7280;
    padding: 2rem;
    font-size: 1.125rem;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add cost item functionality
    const addCostBtn = document.getElementById('add-cost-item');
    if (addCostBtn) {
        addCostBtn.addEventListener('click', function() {
            const costBreakdown = document.querySelector('.survey-cost-breakdown');
            if (costBreakdown) {
                const newItem = document.createElement('div');
                newItem.className = 'survey-cost-item';
                newItem.innerHTML = `
                    <input type="text" class="survey-form-input" name="cost_item[]" placeholder="Item description">
                    <input type="number" class="survey-form-input" name="cost_amount[]" placeholder="Amount (£)" step="0.01">
                    <button type="button" class="survey-remove-cost-btn" onclick="this.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                costBreakdown.appendChild(newItem);
            }
        });
    }
    
    // Save draft functionality
    const saveDraftBtn = document.getElementById('save-draft-btn');
    if (saveDraftBtn) {
        saveDraftBtn.addEventListener('click', function() {
            const form = document.getElementById('report-content-form');
            const formData = new FormData(form);
            formData.append('is_draft', '1');
            
            // Save draft logic here
            console.log('Saving draft...', Object.fromEntries(formData));
            alert('Draft saved successfully!');
        });
    }
    
    // Save content functionality
    const reportForm = document.getElementById('report-content-form');
    if (reportForm) {
        reportForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            // Save content logic here
            console.log('Saving content...', Object.fromEntries(formData));
            alert('Content saved successfully!');
        });
    }
});
</script>
@endpush

