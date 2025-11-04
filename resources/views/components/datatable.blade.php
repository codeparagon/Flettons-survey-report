@props([
    'id' => 'datatable',
    'columns' => [],
    'title' => null,
    'search' => true,
    'searchPlaceholder' => 'Search...',
    'filter' => false,
    'length' => true,
    'paging' => true,
    'ordering' => true,
    'info' => true,
    'responsive' => true,
    'buttons' => false,
    'exportButtons' => false,
    'itemsPerPage' => '10, 25, 50, 100',
    'pageLength' => 10,
    'clickableRows' => false,
    'rowDataAttribute' => 'data-href',
])

<div class="datatable-container">
    @if($title || $search || $filter)
    <!-- Header with Collapsible Search and Filter -->
    <div class="datatable-header">
        @if($title)
            <h2 class="datatable-title">{{ $title }}</h2>
        @endif
        @if($search || $filter)
            <div class="datatable-actions">
                @if($search)
                    <button type="button" class="action-btn" id="search-toggle-{{ $id }}" title="Search">
                        <i class="fas fa-search"></i>
                    </button>
                @endif
                @if($filter)
                    <button type="button" class="action-btn" id="filter-toggle-{{ $id }}" title="Filter">
                        <i class="fas fa-filter"></i>
                    </button>
                @endif
            </div>
        @endif
    </div>
    @endif

    @if($search)
    <!-- Collapsible Search Panel -->
    <div class="collapsible-panel" id="search-panel-{{ $id }}">
        <div class="panel-content">
            <div class="search-container">
                <input type="text" id="table-search-input-{{ $id }}" class="search-input" placeholder="{{ $searchPlaceholder }}">
                <button type="button" class="search-clear-btn" id="search-clear-{{ $id }}" style="display:none;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
    @endif

    @if($filter)
    <!-- Collapsible Filter Panel -->
    <div class="collapsible-panel" id="filter-panel-{{ $id }}">
        <div class="panel-content">
            <div class="filter-container">
                @isset($filters)
                    {!! $filters !!}
                @else
                    {{ $filters ?? '' }}
                @endisset
            </div>
        </div>
    </div>
    @endif

    <!-- Table -->
    <div class="table-wrapper">
        <table id="{{ $id }}" class="datatable-table">
                <thead>
                    <tr>
                        @foreach($columns as $column)
                            <th>{{ $column }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    {{ $slot }}
                </tbody>
            </table>
        </div>
    </div>

@push('styles')
<style>
    /* Datatable Container - styles moved to datatable.css */

    .datatable-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #E5E7EB;
    }

    .datatable-title {
        font-size: 1.75rem;
        font-weight: 600;
        color: #1A202C;
        margin: 0;
    }

    .datatable-actions {
        display: flex;
        gap: 0.75rem;
    }

    .action-btn {
        background: transparent;
        border: 1px solid #D1D5DB;
        border-radius: 8px;
        padding: 0.625rem 1rem;
        color: #374151;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
    }

    .action-btn:hover {
        background: #F9FAFB;
        border-color: #9CA3AF;
    }

    .action-btn.active {
        background: #F9FAFB;
        border-color: #C1EC4A;
        color: #1A202C;
    }

    /* Collapsible Panels */
    .collapsible-panel {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
        margin-bottom: 1.5rem;
    }

    .collapsible-panel.active {
        max-height: 500px;
        margin-bottom: 1.5rem;
    }

    .panel-content {
        padding: 1.5rem;
        background: #F9FAFB;
        border-radius: 8px;
        border: 1px solid #E5E7EB;
    }

    /* Search Panel */
    .search-container {
        position: relative;
        display: flex;
        align-items: center;
    }

    .search-input {
        width: 100%;
        padding: 0.875rem 3rem 0.875rem 1rem;
        font-size: 1rem;
        border: 1px solid #D1D5DB;
        border-radius: 8px;
        background: #FFFFFF;
        color: #1A202C;
        transition: border-color 0.2s ease;
    }

    .search-input:focus {
        outline: none;
        border-color: #C1EC4A;
        box-shadow: 0 0 0 3px rgba(193, 236, 74, 0.1);
    }

    .search-clear-btn {
        position: absolute;
        right: 0.75rem;
        background: transparent;
        border: none;
        color: #6B7280;
        cursor: pointer;
        padding: 0.5rem;
        font-size: 0.875rem;
        transition: color 0.2s ease;
    }

    .search-clear-btn:hover {
        color: #1A202C;
    }

    /* Filter Panel */
    .filter-container {
        width: 100%;
    }

    .filter-row {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        align-items: flex-end;
    }

    .filter-group {
        flex: 1;
        min-width: 150px;
    }

    .filter-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .filter-select,
    .filter-input {
        width: 100%;
        padding: 0.75rem;
        font-size: 1rem;
        border: 1px solid #D1D5DB;
        border-radius: 8px;
        background: #FFFFFF;
        color: #1A202C;
        transition: border-color 0.2s ease;
    }

    .filter-select:focus,
    .filter-input:focus {
        outline: none;
        border-color: #C1EC4A;
        box-shadow: 0 0 0 3px rgba(193, 236, 74, 0.1);
    }

    .filter-actions {
        flex: 0 0 auto;
        min-width: auto;
    }

    .filter-reset-btn {
        padding: 0.75rem 1.25rem;
        background: transparent;
        border: 1px solid #D1D5DB;
        border-radius: 8px;
        color: #374151;
        font-size: 0.9375rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .filter-reset-btn:hover {
        background: #F3F4F6;
        border-color: #9CA3AF;
    }

    /* Table Wrapper */
    .table-wrapper {
        overflow-x: auto;
    }

    /* Clickable Rows */
    .datatable-table tbody tr.clickable-row {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .datatable-table tbody tr.clickable-row:hover {
        background-color: #F9FAFB;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .datatable-title {
            font-size: 1.5rem;
        }

        .filter-row {
            flex-direction: column;
        }

        .filter-group {
            min-width: 100%;
        }
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    if (typeof $.fn.dataTable === 'undefined') {
        console.error('DataTables is not loaded');
        return;
    }

    const tableId = '{{ $id }}';
    const $table = $('#' + tableId);
    
    // Destroy existing DataTable instance if any
    if ($.fn.dataTable.isDataTable($table)) {
        $table.DataTable().destroy();
    }

    // Parse itemsPerPage string to array
    const itemsPerPageArray = '{{ $itemsPerPage }}'.split(',').map(function(item) {
        return parseInt(item.trim()) || -1;
    });
    const itemsPerPageLabels = itemsPerPageArray.map(function(item) {
        return item === -1 ? 'All' : item;
    });

    // Initialize DataTable
    const table = $table.DataTable({
        "language": {
            "lengthMenu": "Show _MENU_ entries",
            "zeroRecords": "No records found",
            "info": "Showing _START_ to _END_ of _TOTAL_ entries",
            "infoEmpty": "Showing 0 to 0 of 0 entries",
            "infoFiltered": "(filtered from _MAX_ total entries)",
            "paginate": {
                "first": "First",
                "last": "Last",
                "next": "Next",
                "previous": "Previous"
            }
        },
        "searching": true,
        "paging": {{ $paging ? 'true' : 'false' }},
        "ordering": {{ $ordering ? 'true' : 'false' }},
        "info": {{ $info ? 'true' : 'false' }},
        "responsive": {{ $responsive ? 'true' : 'false' }},
        "scrollX": false,
        "pageLength": {{ $pageLength }},
        "lengthMenu": [itemsPerPageArray, itemsPerPageLabels],
        "order": [[0, "asc"]],
        "dom": 'rt<"bottom-wrapper"<"bottom-left"l><"bottom-right"ip>>',
        "autoWidth": false,
        "processing": false,
        "stateSave": false,
        @if($buttons || $exportButtons)
        "dom": 'Bfrtip',
        "buttons": [
            @if($exportButtons)
            {
                extend: 'excelHtml5',
                className: 'btn btn-sm btn-primary',
                text: '<i class="fas fa-file-excel"></i> Excel',
                exportOptions: {
                    columns: ':not(.no-export)'
                }
            },
            {
                extend: 'pdfHtml5',
                className: 'btn btn-sm btn-primary',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                exportOptions: {
                    columns: ':not(.no-export)'
                }
            },
            {
                extend: 'print',
                className: 'btn btn-sm btn-primary',
                text: '<i class="fas fa-print"></i> Print',
                exportOptions: {
                    columns: ':not(.no-export)'
                }
            },
            @endif
            @if($buttons)
            {
                text: '<i class="fas fa-sync-alt"></i> Refresh',
                className: 'btn btn-sm btn-primary',
                action: function (e, dt, node, config) {
                    dt.ajax.reload();
                }
            }
            @endif
        ],
        @endif
    });

    // Make rows clickable if enabled
    @if($clickableRows)
    $table.on('click', 'tbody tr.clickable-row', function(e) {
        // Don't navigate if clicking on a badge, link, or button
        if ($(e.target).closest('.badge, a, button, .btn').length) {
            return;
        }
        const url = $(this).attr('{{ $rowDataAttribute }}');
        if (url) {
            window.location.href = url;
        }
    });
    @endif

    // Declare all elements once to avoid duplicate declarations
    let searchToggle = null;
    let searchPanel = null;
    let searchInput = null;
    let searchClear = null;
    let filterToggle = null;
    let filterPanel = null;

    @if($search)
    searchToggle = document.getElementById('search-toggle-' + tableId);
    searchPanel = document.getElementById('search-panel-' + tableId);
    searchInput = document.getElementById('table-search-input-' + tableId);
    searchClear = document.getElementById('search-clear-' + tableId);
    @endif

    @if($filter)
    filterToggle = document.getElementById('filter-toggle-' + tableId);
    filterPanel = document.getElementById('filter-panel-' + tableId);
    @endif

    // Search functionality
    @if($search)
    if (searchToggle && searchPanel) {
        searchToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const isActive = searchPanel.classList.toggle('active');
            searchToggle.classList.toggle('active', isActive);
            
            if (filterPanel && filterPanel.classList.contains('active')) {
                filterPanel.classList.remove('active');
                if (filterToggle) filterToggle.classList.remove('active');
            }

            if (isActive && searchInput) {
                setTimeout(() => searchInput.focus(), 100);
            }
        });
    }

    if (searchInput) {
        console.log('Search input found for table:', tableId);
        let searchTimeout;
        const performSearch = function() {
            const value = searchInput.value.trim();
            console.log('Performing search with value:', value);
            table.search(value).draw();
            if (searchClear) {
                searchClear.style.display = value ? 'block' : 'none';
            }
        };

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performSearch, 300);
        });

        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(searchTimeout);
                performSearch();
            }
        });
    } else {
        console.warn('Search input not found for table:', tableId);
    }

    if (searchClear) {
        searchClear.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (searchInput) {
                searchInput.value = '';
                table.search('').draw();
                this.style.display = 'none';
                searchInput.focus();
            }
        });
    }
    @endif

    // Filter panel toggle
    @if($filter)
    if (filterToggle && filterPanel) {
        filterToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const isActive = filterPanel.classList.toggle('active');
            filterToggle.classList.toggle('active', isActive);

            if (searchPanel && searchPanel.classList.contains('active')) {
                searchPanel.classList.remove('active');
                if (searchToggle) searchToggle.classList.remove('active');
            }
        });
    }
    @endif

    // Store table instance globally for custom filter handlers
    window['datatable_' + tableId] = table;

    // Trigger custom event when table is initialized - allows custom filter handlers to attach
    // Use setTimeout to ensure DOM is ready
    setTimeout(function() {
        console.log('Triggering datatable:initialized event for:', tableId);
        $(document).trigger('datatable:initialized', [table, tableId]);
    }, 100);
});
</script>
@endpush
