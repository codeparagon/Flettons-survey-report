@extends('layouts.survey')

@section('title', 'My Surveys')

@php
    use Illuminate\Support\Str;
    $levelsList = $surveys->pluck('level')->filter()->unique()->values();
    $statusesList = collect(['pending', 'in_progress', 'completed', 'late'])->filter(function ($st) use ($surveys) {
        return $surveys->where('status', $st)->count() > 0;
    });
@endphp

@section('filters')
    <div class="survey-filter-group">
        <label class="survey-filter-label">Status</label>
        <select id="filter-status" class="survey-filter-select">
            <option value="">All Statuses</option>
            @foreach ($statusesList as $st)
                <option value="{{ $st }}">{{ ucfirst(str_replace('_', ' ', $st)) }}</option>
            @endforeach
        </select>
    </div>
    <div class="survey-filter-group">
        <label class="survey-filter-label">Level</label>
        <select id="filter-level" class="survey-filter-select">
            <option value="">All Levels</option>
            @foreach ($levelsList as $lvl)
                <option value="{{ $lvl }}">{{ $lvl }}</option>
            @endforeach
        </select>
    </div>
    <div class="survey-filter-group">
        <label class="survey-filter-label">Date From</label>
        <input type="date" id="filter-from" class="survey-filter-input" />
    </div>
    <div class="survey-filter-group">
        <label class="survey-filter-label">Date To</label>
        <input type="date" id="filter-to" class="survey-filter-input" />
    </div>
@endsection

@section('content')
    <!-- Surveys Table Section -->
    <div class="survey-table-section">
        <x-datatable id="surveysTable" :columns="['Address', 'Level', 'Status', 'Survey Date', 'Due Date', 'Surveyor', 'Job Reference', 'Actions']" :search="false" :filter="false" :clickableRows="true"
            rowDataAttribute="data-href">
            @forelse($surveys as $survey)
                <tr class="clickable-row" data-href="{{ route('client.surveys.report', $survey) }}">
                    <td>{{ Str::limit($survey->full_address ?? $survey->property_address_full, 60) }}{{ $survey->postcode ? ', ' . $survey->postcode : '' }}</td>
                    <td>
                        <span class="survey-level">{{ $survey->level ?? 'N/A' }}</span>
                    </td>
                    <td>
                        @php
                            $statusClass = match ($survey->status_badge) {
                                'badge-success' => 'status-completed',
                                'badge-info' => 'status-assigned',
                                'badge-warning' => 'status-in-progress',
                                'badge-danger' => 'status-cancelled',
                                'badge-secondary' => 'status-pending',
                                default => 'status-pending',
                            };
                        @endphp
                        <span class="survey-status {{ $statusClass }}">
                            {{ $survey->status_label }}
                        </span>
                    </td>
                    <td>{{ $survey->scheduled_date ? $survey->scheduled_date->format('d/m/Y') : '' }}</td>
                    <td>{{ $survey->due_date ? $survey->due_date->format('d/m/Y') : '' }}</td>
                    <td>{{ $survey->surveyor->name ?? 'Unassigned' }}</td>
                    <td>{{ $survey->job_reference ?? '' }}</td>
                    <td class="text-center">
                        <a href="{{ route('client.surveys.show', $survey) }}" class="survey-row-action" title="View survey details" aria-label="View survey details">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-5 no-data">No surveys found. Submit a survey application from our website.</td>
                </tr>
            @endforelse
        </x-datatable>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/custom/datatable.css') }}">
<style>
        .survey-table-section {
            width: 100%;
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            min-height: 0;
        }

        /* Level - Text Colors with Theme Colors */
        .survey-level {
            display: inline-block;
            font-size: inherit;
            font-weight: 500;
            white-space: nowrap;
            color: #1A202C;
        }

        /* Status - Text Colors with Theme Colors Only */
        .survey-status {
            display: inline-block;
            font-size: inherit;
            font-weight: 500;
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

        /* Actions column – prevent row click when clicking the link */
        .survey-row-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            color: #1A202C;
            background: #f1f5f9;
            transition: background 0.15s, color 0.15s;
        }
        .survey-row-action:hover {
            background: #C1EC4A;
            color: #1A202C;
        }
</style>
@endpush

@push('scripts')
<script>
        $(document).ready(function() {
            // Header search functionality
            const headerSearchInput = document.getElementById('survey-header-search');
            const headerSearchClear = document.getElementById('survey-search-clear');
            let searchTimeout;

            if (headerSearchInput) {
                headerSearchInput.addEventListener('input', function() {
                    const value = this.value.trim();

                    if (value) {
                        if (headerSearchClear) headerSearchClear.style.display = 'block';
                    } else {
                        if (headerSearchClear) headerSearchClear.style.display = 'none';
                    }

                    // Debounce search
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(function() {
                        if (window.surveysTable) {
                            window.surveysTable.search(value).draw();
                        }
                    }, 300);
                });

                if (headerSearchClear) {
                    headerSearchClear.addEventListener('click', function() {
                        headerSearchInput.value = '';
                        this.style.display = 'none';
                        if (window.surveysTable) {
                            window.surveysTable.search('').draw();
                        }
                    });
                }
            }

            // Sidebar collapse toggle
            const filterToggle = document.getElementById('survey-filter-toggle');
            const sidebar = document.getElementById('survey-sidebar');
            const mainContent = document.getElementById('survey-main-content');
            const sidebarCollapseBtn = document.getElementById('survey-sidebar-collapse');
            const filtersToggle = document.getElementById('survey-filters-toggle');
            const filtersContainer = document.getElementById('survey-filters-container');
            const sidebarOpenBtn = document.getElementById('survey-sidebar-open');

            if (sidebar && sidebar.classList.contains('collapsed') && sidebarOpenBtn) {
                sidebarOpenBtn.classList.add('show');
            }

            const updateSidebarCollapseUI = (isCollapsed) => {
                if (!sidebarCollapseBtn) return;
                const icon = sidebarCollapseBtn.querySelector('i');
                const label = sidebarCollapseBtn.querySelector('span');
                if (isCollapsed) {
                    if (icon) {
                        icon.classList.remove('fa-chevron-left');
                        icon.classList.add('fa-chevron-right');
                    }
                    if (label) label.textContent = 'Show Sidebar';
                } else {
                    if (icon) {
                        icon.classList.remove('fa-chevron-right');
                        icon.classList.add('fa-chevron-left');
                    }
                    if (label) label.textContent = 'Hide';
                }
            };

            const ensureSidebarOpen = () => {
                if (!sidebar || !mainContent) return;
                if (sidebar.classList.contains('collapsed')) {
                    sidebar.classList.remove('collapsed');
                    mainContent.classList.remove('sidebar-collapsed');
                    sidebarOpenBtn?.classList.remove('show');
                    updateSidebarCollapseUI(false);
                }
            };

            const toggleFiltersVisibility = (forceOpen) => {
                if (!filtersContainer) return;
                const shouldOpen = forceOpen ?? !filtersContainer.classList.contains('open');
                if (shouldOpen) {
                    ensureSidebarOpen();
                    filtersContainer.classList.add('open');
                    filtersToggle?.setAttribute('aria-expanded', 'true');
                    filterToggle?.classList.add('active');
                } else {
                    filtersContainer.classList.remove('open');
                    filtersToggle?.setAttribute('aria-expanded', 'false');
                    filterToggle?.classList.remove('active');
                }
            };

            if (filterToggle) {
                filterToggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    toggleFiltersVisibility(true);
                });
            }

            if (filtersToggle) {
                filtersToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    const isOpen = filtersContainer && filtersContainer.classList.contains('open');
                    toggleFiltersVisibility(!isOpen);
                });
            }

            if (sidebarCollapseBtn && sidebar && mainContent) {
                sidebarCollapseBtn.addEventListener('click', function() {
                    const isCollapsed = sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('sidebar-collapsed', isCollapsed);
                    if (sidebarOpenBtn) {
                        sidebarOpenBtn.classList.toggle('show', isCollapsed);
                    }
                    updateSidebarCollapseUI(isCollapsed);

                    if (isCollapsed) {
                        filtersContainer?.classList.remove('open');
                        filtersToggle?.setAttribute('aria-expanded', 'false');
                        filterToggle?.classList.remove('active');
                    }
                });
            }

            if (sidebarOpenBtn && sidebar && mainContent) {
                sidebarOpenBtn.addEventListener('click', function() {
                    ensureSidebarOpen();
                });
            }

            // Sidebar backdrop click handler
            const sidebarBackdrop = document.getElementById('survey-sidebar-backdrop');
            if (sidebarBackdrop && sidebar && mainContent) {
                sidebarBackdrop.addEventListener('click', function() {
                    sidebar.classList.add('collapsed');
                    mainContent.classList.add('sidebar-collapsed');
                    if (sidebarOpenBtn) {
                        sidebarOpenBtn.classList.add('show');
                    }
                    updateSidebarCollapseUI(true);
                    if (filtersContainer) {
                        filtersContainer.classList.remove('open');
                        if (filtersToggle) {
                            filtersToggle.setAttribute('aria-expanded', 'false');
                        }
                        if (filterToggle) {
                            filterToggle.classList.remove('active');
                        }
                    }
                });
            }

            // Profile dropdown
            const profileBtn = document.getElementById('survey-profile-btn');
            const profileMenu = document.getElementById('survey-profile-menu');

            if (profileBtn && profileMenu) {
                profileBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    profileMenu.classList.toggle('show');
                });

                document.addEventListener('click', function(e) {
                    if (!profileBtn.contains(e.target) && !profileMenu.contains(e.target)) {
                        profileMenu.classList.remove('show');
                    }
                });
            }

            // Close filters on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key !== 'Escape') return;

                if (filtersContainer && filtersContainer.classList.contains('open')) {
                    toggleFiltersVisibility(false);
                }

                if (profileMenu && profileMenu.classList.contains('show')) {
                    profileMenu.classList.remove('show');
                }
            });

            // Wait for datatable initialization event
            $(document).on('datatable:initialized', function(e, table, tableId) {
                if (tableId !== 'surveysTable') return;

                // Store table reference globally for header search
                window.surveysTable = table;

                console.log('Surveys table initialized, attaching filters...');

                // Filter functionality
                const statusSelect = document.getElementById('filter-status');
                const levelSelect = document.getElementById('filter-level');
                const fromInput = document.getElementById('filter-from');
                const toInput = document.getElementById('filter-to');
                const resetBtn = document.getElementById('filter-reset');

                // Status filter - column index 2 (Status)
                if (statusSelect) {
                    if (!window.__surveysTableStatusFilter) {
                        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                            const tableId = settings.sTableId || (settings.nTable ? settings.nTable.id : '');
                            if (tableId !== 'surveysTable') {
                                return true;
                            }

                            const statusSelectEl = document.getElementById('filter-status');
                            if (!statusSelectEl || !statusSelectEl.value) {
                                return true;
                            }

                            const statusFilterValue = statusSelectEl.value.toLowerCase();
                            const statusCell = (data[2] || '').toLowerCase().trim();

                            const statusMap = {
                                'pending': 'pending',
                                'in_progress': 'in progress',
                                'completed': 'completed',
                                'late': 'late',
                                'assigned': 'assigned',
                                'cancelled': 'cancelled'
                            };

                            const expectedLabel = statusMap[statusFilterValue] || statusFilterValue;
                            return statusCell.includes(expectedLabel);
                        });
                        window.__surveysTableStatusFilter = true;
                    }

                    statusSelect.addEventListener('change', function() {
                        table.draw();
                    });
                }

                // Level filter - column index 1 (Level)
                if (levelSelect) {
                    levelSelect.addEventListener('change', function() {
                        const val = this.value;
                        if (val) {
                            table.column(1).search(val, false, false).draw();
                        } else {
                            table.column(1).search('').draw();
                        }
                    });
                }

                // Date filter
                if (!window.__surveysTableDateFilter) {
                    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                        const tableId = settings.sTableId || (settings.nTable ? settings.nTable.id : '');
                        if (tableId !== 'surveysTable') {
                            return true;
                        }

                        const from = fromInput && fromInput.value ? new Date(fromInput.value + 'T00:00:00') : null;
                        const to = toInput && toInput.value ? new Date(toInput.value + 'T23:59:59') : null;
                        const dateStr = data[3];

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

                [fromInput, toInput].forEach(function(el) {
                    if (el) {
                        el.addEventListener('change', function() {
                            table.draw();
                        });
                    }
                });

                // Reset filters
                if (resetBtn) {
                    resetBtn.addEventListener('click', function(e) {
                        e.preventDefault();

                        if (statusSelect) statusSelect.value = '';
                        if (levelSelect) levelSelect.value = '';
                        if (fromInput) fromInput.value = '';
                        if (toInput) toInput.value = '';
                        if (headerSearchInput) {
                            headerSearchInput.value = '';
                            if (headerSearchClear) headerSearchClear.style.display = 'none';
                        }

                        table.columns().search('');
                        table.search('');
                        table.draw();
                    });
                }
            });
        });
</script>
@endpush
