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

<div class="row">
    <div class="col-xl-12">
        <div class="page-header d-flex justify-content-between align-items-center">
            <div>
                <h2 class="pageheader-title mb-1">My Surveys</h2>
                <p class="pageheader-text mb-0">Your assigned jobs at a glance</p>
            </div>
        </div>
    </div>
</div>

<!-- KPI Cards -->
<div class="row mb-3">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted">Total Jobs</div>
                    <div class="h3 mb-0">{{ $total }}</div>
                </div>
                <i class="fas fa-briefcase" style="color:#C1EC4A"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted">Pending</div>
                    <div class="h3 mb-0">{{ $pending }}</div>
                </div>
                <i class="fas fa-clock" style="color:#C1EC4A"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted">In Progress</div>
                    <div class="h3 mb-0">{{ $inProgress }}</div>
                </div>
                <i class="fas fa-spinner" style="color:#C1EC4A"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted">Completed</div>
                    <div class="h3 mb-0">{{ $completed }}</div>
                </div>
                <i class="fas fa-check-circle" style="color:#C1EC4A"></i>
            </div>
        </div>
    </div>
</div>

@if($unassignedSurveys->count() > 0)
<!-- <div class="row">
    <div class="col-xl-12">
        <div class="card border-warning">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Available Surveys to Claim</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="surveys-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Client</th>
                                <th>Property Address</th>
                                <th>Level</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($unassignedSurveys as $survey)
                                <tr>
                                    <td>{{ $survey->id }}</td>
                                    <td>{{ $survey->client_name }}</td>
                                    <td>{{ Str::limit($survey->property_address_full, 40) }}</td>
                                    <td>
                                        <span class="badge badge-info">{{ $survey->level ?? 'N/A' }}</span>
                                    </td>
                                    <td>{{ $survey->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('surveyor.surveys.show', $survey->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-hand-paper"></i> Claim
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div> -->
@endif

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">My Assigned Surveys</h5>
                    <div class="d-flex align-items-center" style="gap:10px;">
                        <select id="filter-status" class="form-control form-control-sm" style="min-width:160px;">
                            <option value="">All Statuses</option>
                            @foreach($statusesList as $st)
                                <option value="{{ $st }}">{{ ucfirst(str_replace('_',' ', $st)) }}</option>
                            @endforeach
                        </select>
                        <select id="filter-level" class="form-control form-control-sm" style="min-width:120px;">
                            <option value="">All Levels</option>
                            @foreach($levelsList as $lvl)
                                <option value="{{ $lvl }}">{{ $lvl }}</option>
                            @endforeach
                        </select>
                        <input type="date" id="filter-from" class="form-control form-control-sm" />
                        <span class="text-muted" style="font-size:12px;">to</span>
                        <input type="date" id="filter-to" class="form-control form-control-sm" />
                        <button class="btn btn-sm btn-secondary" id="filter-reset"><i class="fas fa-undo"></i></button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <x-datatable id="surveysTable" :columns="['Address', 'Level', 'Status', 'Survey Date', 'Actions']">
                    @forelse($assignedSurveys as $survey)
                        <tr>
                            <td>{{ Str::limit($survey->property_address_full, 60) }}</td>
                            <td><span class="badge badge-info">{{ $survey->level ?? 'N/A' }}</span></td>
                            <td><span class="badge {{ $survey->status_badge }}">{{ $survey->status_label }}</span></td>
                            <td>{{ $survey->scheduled_date ? $survey->scheduled_date->format('Y-m-d') : '' }}</td>
                            <td>
                                <a href="{{ route('surveyor.surveys.show', $survey->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">No surveys assigned to you yet.</td>
                        </tr>
                    @endforelse
                </x-datatable>
            </div>
        </div>
    </div>
</div>
@endsection
@push('styles')
<style>
    /* Keep styling minimal; rely on theme */
    /* Show built-in search for this table */
    .dataTables_wrapper .dataTables_filter { display: block; }
    .badge.badge-info { background-color:#1A202C; color:#C1EC4A; }
    .badge.badge-success { background-color:#C1EC4A; color:#1A202C; }
    .badge.badge-warning { background-color:#F59E0B; }
    .badge.badge-danger { background-color:#EF4444; }
    .badge.badge-secondary { background:#6b7280; }
    .page-header { margin-bottom: 10px; }
    .card .h3 { font-weight:700; }
    .card i { font-size:20px; }
    @media (max-width: 768px) { .card .h3 { font-size: 20px; } }
    
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $ === 'undefined' || !$.fn || !$.fn.dataTable) return;
    const $table = $('#surveysTable');

    const statusSelect = document.getElementById('filter-status');
    const levelSelect = document.getElementById('filter-level');
    const fromInput = document.getElementById('filter-from');
    const toInput = document.getElementById('filter-to');
    const resetBtn = document.getElementById('filter-reset');

    function attachFilters(dt) {
        if (!dt) return;
        if (statusSelect) statusSelect.addEventListener('change', function(){
            const val = this.value ? '^' + this.value.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&') + '$' : '';
            dt.column(2).search(val, true, false).draw();
        });
        if (levelSelect) levelSelect.addEventListener('change', function(){
            const val = this.value || '';
            dt.column(1).search(val).draw();
        });

        if (!window.__surveysTableDateFilter) {
            $.fn.dataTable.ext.search.push(function(settings, data) {
                if (settings.nTable !== $table[0]) return true;
                const from = fromInput.value ? new Date(fromInput.value) : null;
                const to = toInput.value ? new Date(toInput.value) : null;
                const dateStr = data[3];
                if (!from && !to) return true;
                if (!dateStr) return false;
                const cellDate = new Date(dateStr);
                if (from && cellDate < from) return false;
                if (to && cellDate > to) return false;
                return true;
            });
            window.__surveysTableDateFilter = true;
        }

        [fromInput, toInput].forEach(function(el){ if (el) el.addEventListener('change', function(){ dt.draw(); }); });
        if (resetBtn) resetBtn.addEventListener('click', function(){
            if (statusSelect) statusSelect.value = '';
            if (levelSelect) levelSelect.value = '';
            if (fromInput) fromInput.value = '';
            if (toInput) toInput.value = '';
            dt.columns().search('');
            dt.search('');
            dt.draw();
        });
    }

    if ($.fn.dataTable.isDataTable($table)) {
        attachFilters($table.DataTable());
    } else {
        $table.on('init.dt', function(){ attachFilters($table.DataTable()); });
    }
});
</script>
@endpush

