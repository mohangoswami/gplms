@include('layouts.partials.flash-errors')
@php
$layout = Auth::guard('admin')->check() ? 'layouts.admin_analytics-master' : 'layouts.cashier-master';
@endphp

@extends($layout)

@section('content')
<div class="container mt-4">
    <h4>Attendance for {{ $class }} — {{ \Carbon\Carbon::parse($date)->format('d M, Y') }}</h4>

    <div class="card mt-3">
        <div class="card-body">
            <div class="mb-3">
                <a href="{{ route('admin.attendance.view') }}" class="btn btn-secondary">Back</a>
                <a href="{{ route('admin.attendance.month', ['class' => $class, 'year' => \Carbon\Carbon::parse($date)->year, 'month' => \Carbon\Carbon::parse($date)->month]) }}" class="btn btn-outline-primary">View Month</a>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-sm responsive-stack">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                        @php $status = $attendances[$student->id] ?? null; @endphp
                        <tr>
                                <td data-label="Student">{{ $student->name ?? $student->id }}</td>
                                <td data-label="Status" class="text-center">
                                @if($status === 'P')
                                    <span class="badge badge-success">Present</span>
                                @elseif($status === 'L')
                                    <span class="badge badge-warning">Leave</span>
                                @elseif($status === 'A')
                                    <span class="badge badge-danger">Absent</span>
                                @else
                                    <span class="text-muted">Not marked</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="font-weight-bold">
                            <td data-label="Totals" class="text-right">Totals:</td>
                            <td data-label="Totals" class="text-center">P: {{ $totals['P'] ?? 0 }} &nbsp; L: {{ $totals['L'] ?? 0 }} &nbsp; A: {{ $totals['A'] ?? 0 }}</td>
                    </tr>
                </tfoot>
            </table>
                </div>
        </div>
    </div>
</div>
@endsection

@section('headerStyle')
<!-- DataTables -->
<link href="{{ URL::asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Responsive datatable examples -->
<link href="{{ URL::asset('plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<style>
    /* keep small status column and sticky header/footer similar to month view */
    .table-responsive { position: relative; }
    table.attendance-day thead th {
        position: -webkit-sticky;
        position: sticky;
        top: 0;
        background: #fff;
        z-index: 10;
    }
    table.attendance-day tfoot td {
        position: -webkit-sticky;
        position: sticky;
        bottom: 0;
        background: #f1f1f1;
        z-index: 9;
    }
    .status-present { background: #d4edda; }
    .status-leave { background: #fff3cd; }
    .status-absent { background: #f8d7da; }
    .totals-row { font-weight: 600; background: #f1f1f1; }
    .status-col { width: 120px; text-align: center; }

    /* Responsive table for small screens: keep normal 2-column layout */
    @media (max-width: 767.98px) {
        /* keep header visible */
        .responsive-stack thead {
            display: table-header-group;
        }

        /* keep normal table layout (no stacking into cards) */
        .responsive-stack tbody {
            display: table-row-group;
            width: auto;
        }

        .responsive-stack tr {
            display: table-row;
            width: auto;
        }

        .responsive-stack td {
            display: table-cell;
            width: auto;
            padding: 0.6rem 0.75rem;
            box-sizing: border-box;
            text-align: left;
            white-space: normal;
            word-break: break-word;
        }

        /* remove the label before each cell (we’re not stacking now) */
        .responsive-stack td::before {
            content: none !important;
        }

        /* footer/totals behave normally */
        .responsive-stack tfoot td {
            position: static;
            background: #f1f1f1;
        }

        /* allow horizontal scroll instead of stacking */
        .table-responsive {
            overflow-x: auto;
        }

        /* badges can wrap neatly */
        .responsive-stack .badge {
            display: inline-block;
            margin-top: 0.25rem;
        }
    }
</style>

@stop

@section('footerScript')

    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.print.min.js"></script>

    <script>
    $(function(){
        // build export filename including class and date (two-digit year)
        var className = "{{ $class }}" || '';
        var classSlug = className.toString().toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/(^-|-$)/g,'');
        var exportDate = "{{ \Carbon\Carbon::parse($date)->format('d-F') }}".toLowerCase();
        var exportYear = "{{ \Carbon\Carbon::parse($date)->format('y') }}";
        var exportFilename = (classSlug ? (classSlug + '-') : '') + exportDate + exportYear + '-attendance';

        // turn this small table into a DataTable with export buttons
        var table = $('table.table').addClass('attendance-day').DataTable({
            dom: 'Bfrtip',
            paging: false,
            searching: true,
            ordering: true,
            scrollX: true,
            buttons: [
                'copy', 'csv', 'excel',
                {
                    extend: 'pdfHtml5',
                    text: 'PDF',
                    filename: exportFilename,
                    title: (className ? (className + ' — ') : '') + '{{ \Carbon\Carbon::parse($date)->format("d M, Y") }} Attendance',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: { columns: ':visible' },
                    customize: function(doc){
                        try {
                            doc.defaultStyle.fontSize = 10;
                            doc.styles.tableHeader.fontSize = 11;
                            // set simple widths: name and status
                            if (doc.content && doc.content[1] && doc.content[1].table && doc.content[1].table.body) {
                                doc.content[1].table.widths = [250, 100];

                                // Colorize cells according to status so PDF matches on-screen view
                                for (var r = 1; r < doc.content[1].table.body.length; r++) {
                                    // skip footer if it looks like totals
                                    var firstCell = doc.content[1].table.body[r][0];
                                    var firstText = '' + (typeof firstCell === 'object' && firstCell.text ? firstCell.text : firstCell);
                                    if (firstText.toString().toLowerCase().indexOf('total') === 0) continue;

                                    for (var c = 0; c < doc.content[1].table.body[r].length; c++) {
                                        var cell = doc.content[1].table.body[r][c];
                                        var cellText = '' + (typeof cell === 'object' && cell.text ? cell.text : cell);
                                        var txt = cellText.toString().toLowerCase();
                                        if (txt.indexOf('present') !== -1 || txt === 'p') {
                                            doc.content[1].table.body[r][c] = { text: cellText, fillColor: '#d4edda' };
                                        } else if (txt.indexOf('leave') !== -1 || txt === 'l') {
                                            doc.content[1].table.body[r][c] = { text: cellText, fillColor: '#fff3cd' };
                                        } else if (txt.indexOf('absent') !== -1 || txt === 'a') {
                                            doc.content[1].table.body[r][c] = { text: cellText, fillColor: '#f8d7da' };
                                        }
                                    }
                                }
                            }
                        } catch(e) { console.warn('PDF customize failed', e); }
                    }
                }, 'print'
            ]
        });

        try { table.buttons().container().appendTo($('table.table').closest('.card-body').find('.col-md-6:eq(0)')); } catch(e){}

        // fallback PDF button if missing
        try {
            if (document.querySelectorAll('button.buttons-pdf').length === 0) {
                var fallback = document.createElement('button');
                fallback.id = 'pdfExportFallbackDay';
                fallback.className = 'btn btn-outline-primary mb-2';
                fallback.innerText = 'Export PDF (fallback)';
                fallback.onclick = function(){ try { table.button(3).trigger(); } catch(err){ alert('PDF export not available'); } };
                var wrap = $('table.table').closest('.table-responsive');
                if (wrap && wrap.length) wrap.prepend(fallback);
            }
        } catch(e){ console.warn('PDF fallback setup failed', e); }
    });
    </script>

@stop
