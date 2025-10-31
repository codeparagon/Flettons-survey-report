@props([
    'id' => 'datatable',
    'columns' => [],
    'search' => true,
    'length' => true,
    'paging' => true,
    'ordering' => true,
    'info' => true,
    'responsive' => true,
    'buttons' => false,
    'exportButtons' => false,
    'itemsPerPage' => '10, 25, 50, 100'
])

<div class="card datatable-card">
    <div class="card-body" style="padding: 0 !important;">
        <div class="table-responsive" style="padding: 0 15px;">
            <table id="{{ $id }}" class="table table-hover" style="width: 100%">
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
</div>

@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#{{ $id }}').DataTable({
        "language": {
            "lengthMenu": "Show _MENU_ entries",
            "zeroRecords": "No records found",
            "info": "Showing _START_ to _END_ of _TOTAL_ entries",
            "infoEmpty": "Showing 0 to 0 of 0 entries",
            "infoFiltered": "(filtered from _MAX_ total entries)",
            "search": "Search:",
            "paginate": {
                "first": "First",
                "last": "Last",
                "next": "Next",
                "previous": "Previous"
            }
        },
        "searching": {{ $search ? 'true' : 'false' }},
        "paging": {{ $paging ? 'true' : 'false' }},
        "ordering": {{ $ordering ? 'true' : 'false' }},
        "info": {{ $info ? 'true' : 'false' }},
        "responsive": {{ $responsive ? 'true' : 'false' }},
        "scrollX": true,
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        "order": [[0, "asc"]],
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
        "autoWidth": false,
        "processing": true,
        "stateSave": false
    });
});
</script>
@endpush
