@extends('layouts.cashier-master')

@section('headerStyle')
<link href="{{ URL::asset('plugins/footable/css/footable.bootstrap.css')}}" rel="stylesheet" type="text/css">

<!-- DataTables -->
<link href="{{ URL::asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Responsive datatable examples -->
<link href="{{ URL::asset('plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<style>
    /* Ensure the parent div allows scrolling */
    .table-responsive {
        overflow-x: auto !important;
        max-width: 100%;
        position: relative;
    }

    /* Force scrollbar to be always visible */
    .table-responsive::-webkit-scrollbar {
        height: 10px;  /* Adjust scrollbar thickness */
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: #888; /* Scrollbar color */
        border-radius: 5px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    /* Fix DataTables scrolling issue */
    .dataTables_wrapper {
        overflow-x: auto !important;
    }
</style>


@stop

@section('content')

<!-- Flash Messages -->
@include('layouts.partials.flash-messages')

<div class="row m-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">

                <h4 class="mt-0 header-title">All Classes</h4>
                <p class="text-muted mb-3">You can view or edit all students records.
                </p>

                <div class="table-responsive" style="overflow-x: auto; width: 100%; max-width: 100%; position: relative;">
                    <table id="table1" class="table table-striped table-bordered nowrap" width="100%">
                                            <thead class="thead-light">
                        <tr>
                            <th>Adm. No.</th>
                            <th>Name</th>
                            <th>Class</th>
                            <th>Father Name</th>
                            <th>Mother Name</th>
                            <th>Old Balance</th>
                            <th>DoB</th>
                            <th>Category</th>
                            <th>Route</th>
                            <th>Address</th>
                            <th>Mobile</th>
                            <th>Email</th>
                            <th>App Per.</th>
                            <th>Exam Per.</th>


                            <th>Edit/Password</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach ($users as $user)

                        <tr>
                            <td>{{$user->admission_number ?? ''}}</td>
                            <td>{{$user->name ?? ''}}</td>
                            <td>{{$user->grade ?? ''}}</td>
                            <td>{{$user->fName ?? ''}}</td>
                            <td>{{$user->mName ?? ''}}</td>
                            <td>{{$user->oldBalance ?? ''}}</td>
                            <td>{{ \Carbon\Carbon::parse($user->dob)->format('Y-m-d') ?? '' }}</td>
                            <td>{{$user->category->category ?? ''}}</td>
                            <td>{{$user->route->routeName ?? ''}}</td>
                            <td>{{$user->address ?? ''}}</td>
                            <td>{{$user->mobile ?? ''}}</td>
                            <td>{{$user->email ?? ''}}</td>
                            <td>{{$user->app_permission ?? ''}}</td>
                            <td>{{$user->exam_permission ?? ''}}</td>


                            <td>
                                <a href="cashierEditStudentRecord/{{$user->id}}">
                                    <i class="fas fa-edit text-info font-16"></i>
                                </a>/
                                <a href="/cashier/cashierStudent-update-password/{{$user->id}}"" data-placement="right" title="Password reset" data-original-title="Password reset" data-trigger="hover">
                                    <i class="fas fa-lock text-info font-16"></i>
                                </a>

                            </td>
                        </tr>
                        @endforeach

                        </tbody>
                    </table><!--end /table-->
                </div><!--end /tableresponsive-->
            </div><!--end card-body-->
        </div><!--end card-->
    </div> <!-- end col -->
</div>

@endsection


@section('footerScript')

<script>
    $(document).ready(function() {
        var table = $('#table1').DataTable({
            scrollX: true,  // Enables horizontal scrolling
            scrollCollapse: true,
            responsive: false,
            autoWidth: false,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });

        // Ensure buttons appear
        table.buttons().container().appendTo('#table1_wrapper .col-md-6:eq(0)');
    });
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
