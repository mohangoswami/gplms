@include('layouts.partials.flash-errors')
@php
$layout = Auth::guard('admin')->check() ? 'layouts.admin_analytics-master' : 'layouts.cashier-master';
@endphp

@extends($layout)


@section('headerStyle')
 <!-- DataTables -->
<link href="{{ URL::asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Responsive datatable examples -->
<link href="{{ URL::asset('plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@stop


@section('content')


<div class="row m-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">


<div class="container mt-4">
    <h4>Attendance Matrix — Class: {{ $class }} | {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F, Y') }}</h4>

    <div class="d-flex justify-content-between align-items-center mb-3">
        @php
            $prev = \Carbon\Carbon::createFromDate($year, $month, 1)->subMonth();
            $next = \Carbon\Carbon::createFromDate($year, $month, 1)->addMonth();
        @endphp
        <div>
            <a href="{{ route('admin.attendance.month', ['class' => $class, 'year' => $prev->year, 'month' => $prev->month]) }}" class="btn btn-outline-primary">&larr; Prev</a>
            <a href="{{ route('admin.attendance.month', ['class' => $class, 'year' => $next->year, 'month' => $next->month]) }}" class="btn btn-outline-primary">Next &rarr;</a>
        </div>
        <div>
            <form method="get" action="{{ route('admin.attendance.month', ['class' => $class, 'year' => $year, 'month' => $month]) }}">
                <input type="month" name="monthpicker" value="{{ sprintf('%04d-%02d', $year, $month) }}"
                    onchange="(function(el){var parts=el.value.split('-');var base='{{ route('admin.attendance.month', ['class' => $class, 'year' => 'YEAR', 'month' => 'MONTH']) }}';var url=base.replace('YEAR', parts[0]).replace('MONTH', parts[1]);location=url;})(this)">
            </form>
        </div>
    </div>

    <style>
        /* Freeze first column (student name) and make header/footer sticky */
        .table-responsive { position: relative; }
        .attendance-table thead th {
            position: -webkit-sticky;
            position: sticky;
            top: 0;
            background: #fff;
            z-index: 10;
        }
        .attendance-table th:first-child,
        .attendance-table td:first-child {
            position: -webkit-sticky;
            position: sticky;
            left: 0;
            background: #fff;
            z-index: 6; /* lower than header/footer so they remain visible */
        }

        /* Make footer totals sticky at bottom so last row is always visible */
        .attendance-table tfoot td {
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
        /* Narrow day columns for better PDF fitting */
        .day-col { padding: 2px 6px; font-size: 12px; width: 28px; max-width: 28px; white-space: nowrap; }
        .attendance-table td.day-col, .attendance-table th.day-col { text-align: center; }
    </style>

    <div class="table-responsive">
    <table id="table1" class="table attendance-table">
            <thead>
                <tr>
                    <th>Student</th>
                    @foreach($days as $d)
                        <th class="text-center day-col">{{ $d }}</th>
                    @endforeach
                    <th class="text-center">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                    <tr>
                        <td>{{ $student->name ?? $student->id }}</td>
                        @foreach($days as $d)
                            @php
                                // Only show actual DB records. If there's no attendance record, show '-'.
                                $status = $attByStudent[$student->id][$d] ?? null;
                                $cellClass = '';
                                if ($status === 'P') $cellClass = 'status-present';
                                elseif ($status === 'L') $cellClass = 'status-leave';
                                elseif ($status === 'A') $cellClass = 'status-absent';
                                $display = $status ?? '-';
                            @endphp
                            <td class="text-center day-col {{ $cellClass }}">{{ $display }}</td>
                        @endforeach
                        <td class="text-center">{{ $presentTotals[$student->id] ?? 0 }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="totals-row">
                    <td class="text-center">Total Present</td>
                    @foreach($days as $d)
                        <td class="text-center">{{ $dayTotals[$d] ?? 0 }}</td>
                    @endforeach
                    <td class="text-center">{{ array_sum($presentTotals) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
            </div><!--end card-body-->
        </div><!--end card-->
    </div> <!-- end col -->
</div>
@endsection

@section('footerScript')

        <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.print.min.js"></script>

        <script src="{{ URL::asset('plugins/footable/js/footable.js')}}"></script>
        <script src="{{ URL::asset('plugins/moment/moment.js')}}"></script>
        <script src="{{ URL::asset('assets/pages/jquery.footable.init.js')}}"></script>
         <!-- Required datatable js -->
         <script src="{{ URL::asset('plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
         <script src="{{ URL::asset('plugins/peity-chart/jquery.peity.min.js') }}"></script>
         <script src="{{ URL::asset('assets/pages/jquery.analytics_customers.init.js') }}"></script>

<script>
   $(document).ready(function() {
    // prepare a filename for PDF exports (e.g., "november25-attendance")
    var exportMonth = "{{ \Carbon\Carbon::createFromDate($year,$month,1)->format('F') }}";
    var exportYear = "{{ \Carbon\Carbon::createFromDate($year,$month,1)->format('y') }}"; // two-digit year
    // include class in filename, slugify it to be filesystem-friendly
    var className = "{{ $class }}" || '';
    var classSlug = className.toString().toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/(^-|-$)/g,'');
    var exportFilename = (classSlug ? (classSlug + '-') : '') + exportMonth.toLowerCase() + exportYear + '-attendance';
    var table = $('#table1').DataTable({
        dom: 'Bfrtip',
        paging: false, // show all rows on a single page (remove pagination)
        searching: true,
        ordering: true,
        scrollX: true,
        buttons: [
            'copy',
            'csv',
            'excel',
            {
                extend: 'pdfHtml5',
                text: 'PDF',
                filename: exportFilename,
                title: exportMonth + ' Attendance',
                orientation: 'landscape',
                pageSize: 'A3',
                exportOptions: {
                    // export all columns (including those hidden by responsive)
                    // Use a function instead of the ':all' pseudo (unsupported by jQuery selector).
                    columns: function (idx, data, node) { return true; }
                },
                customize: function (doc) {
                    try {
                        // reduce font size so wider tables fit better
                        doc.defaultStyle.fontSize = 8;
                        doc.styles.tableHeader.fontSize = 9;

                        // set column widths: wider first column (student name), narrow day columns, and a slightly wider total column
                        if (doc.content && doc.content[1] && doc.content[1].table && doc.content[1].table.body) {
                            var colCount = doc.content[1].table.body[0].length;
                            // col layout: [name][day...][total]
                            var widths = [];
                            if (colCount >= 1) {
                                widths.push(120); // name column width in points
                            }
                            // number of middle day columns = colCount - 2 (minus name and total)
                            var middle = Math.max(0, colCount - 2);
                            for (var i = 0; i < middle; i++) {
                                widths.push(22); // narrow day column
                            }
                            if (colCount >= 2) {
                                widths.push(30); // total column
                            }
                            doc.content[1].table.widths = widths;

                            // Apply fill colors to cells based on status text so PDF matches on-screen colors
                            // Skip header row (index 0) and skip footer totals row (if first cell contains 'Total')
                            for (var r = 1; r < doc.content[1].table.body.length; r++) {
                                var firstCell = doc.content[1].table.body[r][0];
                                var firstText = '' + (typeof firstCell === 'object' && firstCell.text ? firstCell.text : firstCell);
                                if (firstText.toString().toLowerCase().indexOf('total') === 0) {
                                    // likely footer/totals row — skip coloring
                                    continue;
                                }
                                for (var c = 0; c < doc.content[1].table.body[r].length; c++) {
                                    var cell = doc.content[1].table.body[r][c];
                                    var cellText = '' + (typeof cell === 'object' && cell.text ? cell.text : cell);
                                    var txt = cellText.toString().toLowerCase();
                                    // map statuses to colors (match CSS used in view)
                                    if (txt === 'p' || txt.indexOf('present') !== -1) {
                                        doc.content[1].table.body[r][c] = { text: cellText, fillColor: '#d4edda' };
                                    } else if (txt === 'l' || txt.indexOf('leave') !== -1) {
                                        doc.content[1].table.body[r][c] = { text: cellText, fillColor: '#fff3cd' };
                                    } else if (txt === 'a' || txt.indexOf('absent') !== -1) {
                                        doc.content[1].table.body[r][c] = { text: cellText, fillColor: '#f8d7da' };
                                    }
                                }
                            }
                        }
                    } catch (e) {
                        console.warn('PDF customize failed:', e);
                    }
                }
            },
            'print'
        ]
    });

    // place buttons container (let DataTables place it by dom; this is a safe append if needed)
    try { table.buttons().container().appendTo('#table1_wrapper .col-md-6:eq(0)'); } catch(e){}

    // Debugging info: log availability of Buttons and pdfMake
    try {
        console.log('DataTable initialized. Buttons count:', table.buttons().count());
        console.log('pdfMake present:', typeof pdfMake !== 'undefined');
        console.log('pdfMake vfs present:', typeof pdfMake && typeof pdfMake.vfs !== 'undefined');
        console.log('buttons-pdf elements:', document.querySelectorAll('button.buttons-pdf').length);
    } catch(e) { console.warn('Debug logs failed', e); }

    // If PDF button is not present or not responding, provide a fallback visible button
    try {
        if (document.querySelectorAll('button.buttons-pdf').length === 0) {
            var fallback = document.createElement('button');
            fallback.id = 'pdfExportFallback';
            fallback.className = 'btn btn-outline-primary mb-2';
            fallback.innerText = 'Export PDF (fallback)';
            fallback.onclick = function() {
                try {
                    // Attempt to trigger the pdf button (index 3 by our buttons order)
                    table.button(3).trigger();
                } catch (err) {
                    console.error('Fallback PDF trigger failed', err);
                    alert('PDF export not available — check console for errors.');
                }
            };
            var wrap = document.getElementById('table1_wrapper');
            if (wrap && wrap.firstChild) wrap.insertBefore(fallback, wrap.firstChild);
        }
    } catch(e) { console.warn('PDF fallback setup failed', e); }
} );

</script>

@stop

