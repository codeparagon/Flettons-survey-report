@extends('layouts.survey')

@section('title', 'My Surveys')

@php
    use Illuminate\Support\Str;
    $levelsList = $assignedSurveys->pluck('level')->filter()->unique()->values();
    $statusesList = collect(['pending', 'in_progress', 'completed', 'late'])->filter(function ($st) use (
        $assignedSurveys,
    ) {
        return $assignedSurveys->where('status', $st)->count() > 0;
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
    <div class="survey-filter-group">
        <button type="button" class="survey-filter-reset" id="filter-reset">
            <i class="fas fa-undo"></i> Reset
        </button>
    </div>
@endsection

@section('content')
    <div class="survey-index-actions">
        <button type="button" class="survey-create-btn" id="survey-create-btn">
            <i class="fas fa-plus"></i>
            <span>Create</span>
        </button>
        <a href="{{ url('surveyor/surveys/detail-mock') }}" class="survey-index-secondary">
            <i class="fas fa-external-link-alt"></i>
            <span>Detail Preview</span>
        </a>
        <a href="{{ url('surveyor/surveys/desk-study-mock') }}" class="survey-index-secondary">
            <i class="fas fa-map-marked-alt"></i>
            <span>Desk Study Preview</span>
        </a>
    </div>
    <!-- Surveys Table Section -->
    <div class="survey-table-section">
        <x-datatable id="surveysTable" :columns="['Address', 'Level', 'Status', 'Survey Date']" :search="false" :filter="false" :clickableRows="true"
            rowDataAttribute="data-href">
            @forelse($assignedSurveys as $survey)
                <tr class="clickable-row" data-href="{{ route('surveyor.surveys.detail', $survey->id) }}">
                    <td>{{ Str::limit($survey->full_address, 60) }}</td>
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
                    <td>{{ $survey->scheduled_date ? $survey->scheduled_date->format('Y-m-d') : '' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center py-5 no-data">No surveys assigned to you yet.</td>
                </tr>
            @endforelse
        </x-datatable>
    </div>
    <div class="survey-create-overlay" id="survey-create-overlay" aria-hidden="true">
        <div class="survey-create-dialog" role="dialog" aria-modal="true" aria-labelledby="survey-create-title">
            <button type="button" class="survey-create-close" id="survey-create-close"
                aria-label="Close create survey form">
                <i class="fas fa-times"></i>
            </button>
            <h2 class="survey-create-title" id="survey-create-title">Setup Survey Service</h2>
            <form class="survey-create-form" id="survey-create-form" method="POST"
                action="{{ route('surveyor.surveys.createNewSurvey') }}">
                @csrf
                <div class="survey-create-grid full">
                    <div class="survey-create-field">
                        <label class="survey-create-label" for="create-survey-level">Survey Level</label>
                        <select id="create-survey-level" name="level" class="survey-create-select">
                            <option value="Level 1">Level 1</option>
                            <option value="Level 2" selected>Level 2</option>
                            <option value="Level 3">Level 3</option>
                            <option value="Specialist">Specialist</option>
                        </select>
                    </div>
                </div>

                <div class="survey-create-grid full">
                       <div class="survey-create-field">
                        <label class="survey-create-label" for="create-survey-date">Survey Date</label>
                        <div class="survey-create-date">
                            <input type="date" id="create-survey-date" name="scheduled_date" class="survey-create-input"
                                value="{{ now()->format('Y-m-d') }}">
                            <i class="fas fa-calendar-alt survey-create-date-icon" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>

                <div class="survey-create-grid full">
                    <div class="survey-create-field">
                        <label class="survey-create-label" for="create-full-address">Full Address</label>
                        <input type="text" id="create-full-address" name="full_address" class="survey-create-input"
                            placeholder="66 Sample Street, Kent" value="66 Sample Street, Kent">
                    </div>
                </div>

                <div class="survey-create-grid full">
                    <div class="survey-create-field">
                        <label class="survey-create-label" for="create-postcode">Postcode</label>
                        <input type="text" id="create-postcode" name="postcode" class="survey-create-input"
                            placeholder="DA9 9XZ" value="DA9 9XZ">
                        <p class="survey-create-helper">
                            Automatically create reference based on the address (door number, postcode and surveyor
                            initials)
                        </p>
                    </div>
                    <div class="survey-create-field">
                        <label class="survey-create-label" for="create-reference">Job Reference</label>
                        <input type="text" id="create-reference" name="job_reference" class="survey-create-input"
                            placeholder="66DA99XZ-SH" value="66DA99XZ-SH">
                    </div>
                </div>

                <div class="survey-create-grid full">
                    <div class="survey-create-field">
                        <label class="survey-create-label" for="create-property-type">Property Type</label>
                        <select id="create-property-type" name="house_or_flat" class="survey-create-select">
                            <option value="House" selected>House</option>
                            <option value="Flat">Flat</option>
                            <option value="Bungalow">Bungalow</option>
                            <option value="Cottage">Cottage</option>
                        </select>
                    </div>
                    <div class="survey-create-field">
                        <label class="survey-create-label" for="create-estate-holding">Estate Holding</label>
                        <select id="create-estate-holding" name="listed_building" class="survey-create-select">
                            <option value="Freehold" selected>Freehold</option>
                            <option value="Leasehold">Leasehold</option>
                            <option value="Commonhold">Commonhold</option>
                            <option value="Shared Ownership">Shared Ownership</option>
                        </select>
                    </div>
                </div>

                <div class="survey-create-quick">
                    <div class="survey-create-quick-field">
                        <label class="survey-create-quick-label" for="create-bedrooms">Bedrooms</label>
                        <select id="create-bedrooms" name="number_of_bedrooms"
                            class="survey-create-select survey-create-select-compact">
                            @for ($i = 0; $i <= 10; $i++)
                                <option value="{{ $i }}" {{ $i === 2 ? 'selected' : '' }}>{{ $i }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="survey-create-quick-field">
                        <label class="survey-create-quick-label" for="create-receptions">Receptions</label>
                        <select id="create-receptions" name="receptions"
                            class="survey-create-select survey-create-select-compact">
                            @for ($i = 0; $i <= 10; $i++)
                                <option value="{{ $i }}" {{ $i === 2 ? 'selected' : '' }}>{{ $i }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="survey-create-quick-field">
                        <label class="survey-create-quick-label" for="create-baths">Baths/Showers</label>
                        <select id="create-baths" name="bathrooms"
                            class="survey-create-select survey-create-select-compact">
                            @for ($i = 0; $i <= 10; $i++)
                                <option value="{{ $i }}" {{ $i === 2 ? 'selected' : '' }}>{{ $i }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div class="survey-create-footer">
                    <button type="button" class="survey-create-cancel" id="survey-create-cancel">Cancel</button>
                    <button type="submit" class="survey-create-submit">
                        <i class="fas fa-plus"></i>
                        Create
                    </button>
                </div>
            </form>

        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/custom/datatable.css') }}">
    <style>
        .survey-index-actions {
            width: 100%;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-right: 1.5rem;
            gap: 1rem;
        }

        .survey-index-secondary {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.6rem 1.15rem;
            border-radius: 10px;
            border: 1px solid rgba(148, 163, 184, 0.5);
            background: #FFFFFF;
            color: #0F172A;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .survey-index-secondary:hover {
            border-color: rgba(193, 236, 74, 0.7);
            color: #111827;
            box-shadow: 0 10px 22px -18px rgba(15, 23, 42, 0.3);
        }

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

            // Filter toggle button - Toggle sidebar with animation
            const filterToggle = document.getElementById('survey-filter-toggle');
            const sidebar = document.querySelector('.survey-sidebar');
            const sidebarBackdrop = document.getElementById('survey-sidebar-backdrop');
            const mainContent = document.querySelector('.survey-main-content');
            const createBtn = document.getElementById('survey-create-btn');
            const createOverlay = document.getElementById('survey-create-overlay');
            const createClose = document.getElementById('survey-create-close');
            const createCancel = document.getElementById('survey-create-cancel');
            const createForm = document.getElementById('survey-create-form');

            const openCreateModal = () => {
                if (!createOverlay) return;
                createOverlay.classList.add('show');
                createOverlay.setAttribute('aria-hidden', 'false');
                document.body.classList.add('modal-open-lite');
                const firstField = document.getElementById('create-survey-level');
                if (firstField) {
                    setTimeout(() => firstField.focus(), 120);
                }
            };

            const closeCreateModal = () => {
                if (!createOverlay) return;
                createOverlay.classList.remove('show');
                createOverlay.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('modal-open-lite');
            };

            if (filterToggle && sidebar) {
                filterToggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const isOpen = sidebar.classList.contains('show');

                    if (isOpen) {
                        // Close sidebar
                        sidebar.classList.remove('show');
                        if (sidebarBackdrop) sidebarBackdrop.classList.remove('show');
                        if (mainContent) mainContent.classList.remove('sidebar-open');
                        filterToggle.classList.remove('active');
                    } else {
                        // Open sidebar
                        sidebar.classList.add('show');
                        if (sidebarBackdrop) sidebarBackdrop.classList.add('show');
                        if (mainContent) mainContent.classList.add('sidebar-open');
                        filterToggle.classList.add('active');
                    }
                });

                // Close sidebar when clicking backdrop
                if (sidebarBackdrop) {
                    sidebarBackdrop.addEventListener('click', function() {
                        sidebar.classList.remove('show');
                        sidebarBackdrop.classList.remove('show');
                        if (mainContent) mainContent.classList.remove('sidebar-open');
                        filterToggle.classList.remove('active');
                    });
                }

                // Close sidebar on escape key
                document.addEventListener('keydown', function(e) {
                    if (e.key !== 'Escape') return;

                    if (sidebar.classList.contains('show')) {
                        sidebar.classList.remove('show');
                        if (sidebarBackdrop) sidebarBackdrop.classList.remove('show');
                        if (mainContent) mainContent.classList.remove('sidebar-open');
                        filterToggle.classList.remove('active');
                    }

                    if (createOverlay && createOverlay.classList.contains('show')) {
                        closeCreateModal();
                    }
                });
            }

            if (createBtn && createOverlay) {
                createBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    openCreateModal();
                });
            }

            if (createClose) {
                createClose.addEventListener('click', function() {
                    closeCreateModal();
                });
            }

            if (createCancel) {
                createCancel.addEventListener('click', function() {
                    closeCreateModal();
                });
            }

            if (createOverlay) {
                createOverlay.addEventListener('click', function(e) {
                    if (e.target === createOverlay) {
                        closeCreateModal();
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
                            const tableId = settings.sTableId || (settings.nTable ? settings.nTable
                                .id : '');
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
                        const tableId = settings.sTableId || (settings.nTable ? settings.nTable.id :
                            '');
                        if (tableId !== 'surveysTable') {
                            return true;
                        }

                        const from = fromInput && fromInput.value ? new Date(fromInput.value +
                            'T00:00:00') : null;
                        const to = toInput && toInput.value ? new Date(toInput.value +
                            'T23:59:59') : null;
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
