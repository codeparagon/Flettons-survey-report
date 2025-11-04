@extends('layouts.app')

@section('title', 'My Surveys')

@php
    use Illuminate\Support\Str;
@endphp

@section('content')
@php
    $total = $assignedSurveys->count();
    $pending = $assignedSurveys->where('status', 'pending')->count();
    $inProgress = $assignedSurveys->where('status', 'in_progress')->count();
    $completed = $assignedSurveys->where('status', 'completed')->count();
    $levelsList = $assignedSurveys->pluck('level')->filter()->unique()->values();
    $statusesList = collect(['pending','in_progress','completed','late'])->filter(function($st) use ($assignedSurveys){
        return $assignedSurveys->where('status',$st)->count() > 0;
    });
@endphp

<!-- Page Header -->
<div class="row mb-1">
    <div class="col-xl-12">
        <h1 class="surveys-page-title">My Surveys</h1>
        <p class="surveys-page-subtitle">Your assigned jobs at a glance</p>
    </div>
</div>

<!-- KPI Cards -->
<div class="row mb-1">
    <div class="col-md-3 mb-1">
        <div class="kpi-card">
            <div class="kpi-content">
                <div class="kpi-label">Total Jobs</div>
                <div class="kpi-value">{{ $total }}</div>
            </div>
            <i class="fas fa-briefcase kpi-icon"></i>
        </div>
    </div>
    <div class="col-md-3 mb-1">
        <div class="kpi-card">
            <div class="kpi-content">
                <div class="kpi-label">Pending</div>
                <div class="kpi-value">{{ $pending }}</div>
            </div>
            <i class="fas fa-clock kpi-icon"></i>
        </div>
    </div>
    <div class="col-md-3 mb-1">
        <div class="kpi-card">
            <div class="kpi-content">
                <div class="kpi-label">In Progress</div>
                <div class="kpi-value">{{ $inProgress }}</div>
            </div>
            <i class="fas fa-spinner kpi-icon"></i>
        </div>
    </div>
    <div class="col-md-3 mb-1">
        <div class="kpi-card">
            <div class="kpi-content">
                <div class="kpi-label">Completed</div>
                <div class="kpi-value">{{ $completed }}</div>
            </div>
            <i class="fas fa-check-circle kpi-icon"></i>
        </div>
    </div>
</div>

<!-- Surveys Table Section -->
<div class="row surveys-table-section">
    <div class="col-12">
        <x-datatable 
            id="surveysTable" 
            :columns="['Address', 'Level', 'Status', 'Survey Date']"
            title="My Assigned Surveys"
            :search="true"
            searchPlaceholder="Search surveys..."
            :filter="true"
            :clickableRows="true"
            rowDataAttribute="data-href"
        >
            @slot('filters')
                <div class="filter-row">
                    <div class="filter-group">
                        <label class="filter-label">Status</label>
                        <select id="filter-status" class="filter-select">
                            <option value="">All Statuses</option>
                            @foreach($statusesList as $st)
                                <option value="{{ $st }}">{{ ucfirst(str_replace('_',' ', $st)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Level</label>
                        <select id="filter-level" class="filter-select">
                            <option value="">All Levels</option>
                            @foreach($levelsList as $lvl)
                                <option value="{{ $lvl }}">{{ $lvl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Date From</label>
                        <input type="date" id="filter-from" class="filter-input" />
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Date To</label>
                        <input type="date" id="filter-to" class="filter-input" />
                    </div>
                    <div class="filter-group filter-actions">
                        <button type="button" class="filter-reset-btn" id="filter-reset">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                    </div>
                </div>
            @endslot

            @forelse($assignedSurveys as $survey)
                <tr class="clickable-row" data-href="{{ route('surveyor.surveys.show', $survey->id) }}">
                    <td>{{ Str::limit($survey->full_address, 60) }}</td>
                    <td>
                        <span class="survey-level">{{ $survey->level ?? 'N/A' }}</span>
                    </td>
                    <td>
                        @php
                            $statusClass = match($survey->status_badge) {
                                'badge-success' => 'status-completed',
                                'badge-info' => 'status-assigned',
                                'badge-warning' => 'status-in-progress',
                                'badge-danger' => 'status-cancelled',
                                'badge-secondary' => 'status-pending',
                                default => 'status-pending'
                            };
                        @endphp
                        <span class="survey-status {{ $statusClass }}">
                            {{ $survey->status_label }}
                        </span>
                    </td>
                    <td>{{ $survey->scheduled_date ? $survey->scheduled_date->format('Y-m-d') : '' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center py-5 no-data">No surveys assigned to you yet.</td>
                </tr>
            @endforelse
        </x-datatable>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/custom/datatable.css') }}">
<style>
    /* Typography - Original Sizes */
    .surveys-page-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: #1A202C;
        margin-bottom: 0.25rem;
        letter-spacing: -0.02em;
    }

    .surveys-page-subtitle {
        font-size: 1.125rem;
        color: #6B7280;
        margin-bottom: 0;
    }

    .surveys-table-title {
        font-size: 1.75rem;
        font-weight: 600;
        color: #1A202C;
        margin: 0;
    }

    /* KPI Cards - Clean Design */
    .kpi-card {
        background: #FFFFFF;
        padding: 1.25rem;
        border-radius: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: none;
        border: none;
        transition: transform 0.2s ease;
    }

    .kpi-card:hover {
        transform: translateY(-2px);
    }

    .kpi-label {
        font-size: 0.9375rem;
        color: #6B7280;
        margin-bottom: 0.5rem;
        font-weight: 500;
    }

    .kpi-value {
        font-size: 2.25rem;
        font-weight: 700;
        color: #1A202C;
        line-height: 1;
    }

    .kpi-icon {
        font-size: 2rem;
        color: #9CA3AF;
        opacity: 0.6;
    }


    /* Level - Text Colors with Theme Colors */
    .survey-level {
        display: inline-block;
        font-size: 1.125rem;
        font-weight: 700;
        white-space: nowrap;
        color: #1A202C;
    }

    /* Status - Text Colors with Theme Colors Only */
    .survey-status {
        display: inline-block;
        font-size: 1.125rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .survey-status.status-completed {
        color: #C1EC4A;
    }

    .survey-status.status-assigned {
        color: #1A202C;
    }

    .survey-status.status-in-progress {
        color: #1A202C;
    }

    .survey-status.status-cancelled {
        color: #1A202C;
    }

    .survey-status.status-pending {
        color: #1A202C;
    }

    /* No Data */
    .no-data {
        font-size: 1rem;
        color: #6B7280;
    }


    /* Ensure full width for table section */
    .surveys-table-section {
        width: 100%;
        padding-left: 0;
        padding-right: 0;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .surveys-page-title {
            font-size: 2rem;
        }

        .surveys-table-title {
            font-size: 1.5rem;
        }

        .kpi-value {
            font-size: 1.75rem;
        }

    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Wait for datatable initialization event
    $(document).on('datatable:initialized', function(e, table, tableId) {
        if (tableId !== 'surveysTable') return;

        console.log('Surveys table initialized, attaching filters...');

        // Filter functionality - wait for table to be ready
        const statusSelect = document.getElementById('filter-status');
        const levelSelect = document.getElementById('filter-level');
        const fromInput = document.getElementById('filter-from');
        const toInput = document.getElementById('filter-to');
        const resetBtn = document.getElementById('filter-reset');
        const searchInput = document.getElementById('table-search-input-surveysTable');
        const searchClear = document.getElementById('search-clear-surveysTable');

        console.log('Filter elements found:', {
            statusSelect: !!statusSelect,
            levelSelect: !!levelSelect,
            fromInput: !!fromInput,
            toInput: !!toInput,
            resetBtn: !!resetBtn
        });

        // Status filter - column index 2 (Status)
        // Match against the formatted status label in the table
        if (statusSelect) {
            if (!window.__surveysTableStatusFilter) {
                $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                    const tableId = settings.sTableId || (settings.nTable ? settings.nTable.id : '');
                    if (tableId !== 'surveysTable') {
                        return true;
                    }
                    
                    const statusSelectEl = document.getElementById('filter-status');
                    if (!statusSelectEl || !statusSelectEl.value) {
                        return true; // Show all if no filter selected
                    }
                    
                    const statusFilterValue = statusSelectEl.value.toLowerCase();
                    // Get the status cell content (formatted label like "In Progress", "Pending", etc.)
                    const statusCell = (data[2] || '').toLowerCase().trim();
                    
                    // Map status values to their formatted labels
                    const statusMap = {
                        'pending': 'pending',
                        'in_progress': 'in progress',
                        'completed': 'completed',
                        'late': 'late',
                        'assigned': 'assigned',
                        'cancelled': 'cancelled'
                    };
                    
                    // Check if status cell contains the formatted status
                    const expectedLabel = statusMap[statusFilterValue] || statusFilterValue;
                    return statusCell.includes(expectedLabel);
                });
                window.__surveysTableStatusFilter = true;
            }
            
            statusSelect.addEventListener('change', function() {
                const val = this.value;
                console.log('Status filter changed:', val);
                table.draw(); // Redraw to apply the custom search filter
            });
        } else {
            console.warn('Status select not found!');
        }

        // Level filter - column index 1 (Level)
        if (levelSelect) {
            levelSelect.addEventListener('change', function() {
                const val = this.value;
                console.log('Level filter changed:', val);
                if (val) {
                    table.column(1).search(val, false, false).draw();
                } else {
                    table.column(1).search('').draw();
                }
            });
        } else {
            console.warn('Level select not found!');
        }

        // Date filter using DataTables custom search
        if (!window.__surveysTableDateFilter) {
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                const tableId = settings.sTableId || (settings.nTable ? settings.nTable.id : '');
                if (tableId !== 'surveysTable') {
                    return true;
                }

                const from = fromInput && fromInput.value ? new Date(fromInput.value + 'T00:00:00') : null;
                const to = toInput && toInput.value ? new Date(toInput.value + 'T23:59:59') : null;
                const dateStr = data[3]; // Survey Date column (index 3)

                if (!from && !to) {
                    return true;
                }

                if (!dateStr || dateStr.trim() === '') {
                    return false;
                }

                try {
                    const cellDate = new Date(dateStr + 'T12:00:00');
                    if (from && cellDate < from) return false;
                    if (to && cellDate > to) return false;
                    return true;
                } catch (e) {
                    return false;
                }
            });
            window.__surveysTableDateFilter = true;
        }

        // Date inputs change handler
        [fromInput, toInput].forEach(function(el) {
            if (el) {
                el.addEventListener('change', function() {
                    console.log('Date filter changed');
                    table.draw();
                });
            }
        });

        // Reset filters
        if (resetBtn) {
            resetBtn.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Resetting all filters');
                
                if (statusSelect) statusSelect.value = '';
                if (levelSelect) levelSelect.value = '';
                if (fromInput) fromInput.value = '';
                if (toInput) toInput.value = '';
                if (searchInput) {
                    searchInput.value = '';
                    if (searchClear) searchClear.style.display = 'none';
                }

                table.columns().search('');
                table.search('');
                table.draw();
            });
        } else {
            console.warn('Reset button not found!');
        }
    }); // Close datatable:initialized event listener
}); // Close $(document).ready
</script>
@endpush

