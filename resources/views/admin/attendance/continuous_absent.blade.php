@include('layouts.partials.flash-errors')
@php
$layout = Auth::guard('admin')->check() ? 'layouts.admin_analytics-master' : 'layouts.cashier-master';
@endphp

@extends($layout)

@section('headerStyle')
<link href="{{ URL::asset('plugins/footable/css/footable.bootstrap.css')}}" rel="stylesheet" type="text/css">

<!-- DataTables -->
<link href="{{ URL::asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Responsive datatable examples -->
<link href="{{ URL::asset('plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

@stop

@section('content')
<div class="container mt-4">
    <h4>Continuous Absentees</h4>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.attendance.continuous.results') }}" class="form-inline">
                <div class="form-group mr-3">
                    <label class="mr-2">Days</label>
                    <input type="number" name="days" min="1" max="30" class="form-control" value="{{ old('days', $days ?? 3) }}" required>
                </div>

                <div class="form-group mr-3">
                    <label class="mr-2">End date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $end_date ?? \Carbon\Carbon::now()->toDateString()) }}" required>
                </div>

                <button class="btn btn-primary" type="submit">Show</button>
            </form>
        </div>
    </div>

    @if(isset($results))
        <div class="card">
            <div class="card-body">
                @if($results->isEmpty())
                    <div class="alert alert-info">No students were absent for the selected period.</div>
                @else
                    <div class="table-responsive">
                    <table id="table1" class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Student Name</th>
                                    <th>Father Name</th>
                                    <th>Class</th>
                                    <th>Mobile</th>
                                    <th>Days Absent</th>
                                    <th>Absent Dates</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($results as $idx => $r)
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td>{{ $r->name }}</td>
                                        <td>{{ $r->father_name }}</td>
                                        <td>{{ $r->class }}</td>
                                        <td>{{ $r->mobile }}</td>
                                        <td>{{ $r->days }}</td>
                                        <td>
                                        @if(!empty($r->absent_dates))
                                            @php
                                                $shortDates = collect($r->absent_dates)->map(function($d){
                                                    try {
                                                        return \Carbon\Carbon::parse($d)->format('d M');
                                                    } catch(\Exception $e) {
                                                        return $d;
                                                    }
                                                })->toArray();
                                            @endphp
                                            {{ implode(', ', $shortDates) }}
                                        @else
                                            -
                                        @endif
                                    </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-2">
                        <a href="{{ route('admin.attendance.continuous.form') }}" class="btn btn-outline-secondary">New search</a>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection


@section('footerScript')
<script>

   $(document).ready(function() {
    var table = $('#table1').DataTable();


    new $.fn.dataTable.Buttons( table, {
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'

        ]
    } );
    table.buttons( 0, null ).container().appendTo(
        table.table().container()
    );
} );

</script>
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


@stop

