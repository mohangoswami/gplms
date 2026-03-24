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

<!-- Flash Messages -->
@include('layouts.partials.flash-messages')

<div class="row m-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">

                <h4 class="mt-0 header-title">Fee</h4>
                <p class="text-muted mb-3">You can view or edit live classes and their schedule.
                </p>

                <div class="table-responsive">
                    <table id="table1" class="table">
                        <thead class="thead-light">
                        <tr>
                            <th>Name</th>
                            <th>Adm. No.</th>
                            <th>Class</th>
                            <th>Father Name</th>
                            <th>DoB</th>
                            <th>Mobile</th>
                            <th>Category</th>
                            <th>Route</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach ($users as $user)

                        <tr>

                            <td>{{$user->name}}</td>
                            <td>{{$user->admission_number}}</td>
                            <td>{{$user->grade}}</td>
                            <td>{{$user->fName}}</td>
                            <td>{{ \Carbon\Carbon::parse($user->dob)->format('Y-m-d') ?? '' }}</td>
                            <td>{{$user->mobile}}</td>
                            <td>{{$user->category->category ?? null}}</td>
                            <td>{{$user->route->routeName ?? null}}</td>
                            <td>
                                <a href="getFeeDetail/{{$user->id}}" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="Fee Deposit" data-trigger="hover">
                                    <i class="fas fa-rupee-sign text-info font-16"></i>
                                </a>
                                -
                                <a href="feeCard/{{$user->id}}" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="Fee Card" data-trigger="hover"><i class="fas fa-address-book text-info font-16"></i></a>
                            </td>
                        </tr>
                        @endforeach

                        </tbody>
                    </table><!--end /table-->
                </div><!--end /tableresponsive-->
        </div><!--end card-->
    </div> <!-- end col -->
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
