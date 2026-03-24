@extends('layouts.admin_analytics-master')

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

                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mt-0 header-title mb-0">All Classes</h4>
                    <form action="{{ route('admin.student.sendFeeReminderAllDue') }}"
                          method="POST"
                          style="display:inline;">
                        @csrf
                        <button type="submit"
                                onclick="return confirm('Send fee reminder to all students whose payable fee is more than 0?')"
                                class="btn btn-warning btn-sm">
                            <i class="fas fa-bell mr-1"></i> Send All Due Fee Reminders
                        </button>
                    </form>
                </div>
                <p class="text-muted mb-3">You can view or edit all students records.
                </p>

                <div class="table-responsive">
                    <table id="table1" class="table">
                        <thead class="thead-light">
                        <tr>
                            <th>Adm. No.</th>
                            <th>Name</th>
                            <th>Class</th>
                            <th>Section</th>
                            <th>Roll No.</th>
                            <th>Father Name</th>
                            <th>Mother Name</th>
                            <th>old Balance</th>
                            <th>DoB</th>
                            <th>Category</th>
                            <th>Route</th>
                            <th>Aadhar</th>
                            <th>PEN</th>
                            <th>APAAR</th>
                            <th>Address</th>
                            <th>Mobile</th>
                            <th>Email</th>
                            <th>App Per.</th>
                            <th>Exam Per.</th>


                            <th>Edit/Password/Delete</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach ($users as $user)

                        <tr>
                            <td>{{$user->admission_number ?? ''}}</td>
                            <td>{{$user->name ?? ''}}</td>
                            <td>{{$user->grade ?? ''}}</td>
                            <td>{{$user->section ?? ''}}</td>
                            <td>{{$user->rollNo ?? ''}}</td>
                            <td>{{$user->fName ?? ''}}</td>
                            <td>{{$user->mName ?? ''}}</td>
                            <td>{{$user->oldBalance ?? ''}}</td>
                            <td>{{ \Carbon\Carbon::parse($user->dob)->format('Y-m-d') ?? '' }}</td>
                            <td>{{$user->category->category ?? ''}}</td>
                            <td>{{$user->route->routeName ?? ''}}</td>
                            <td>{{$user->aadhar ?? ''}}</td>
                            <td>{{$user->pen ?? ''}}</td>
                            <td>{{$user->apaar ?? ''}}</td>
                            <td>{{$user->address ?? ''}}</td>
                            <td>{{$user->mobile ?? ''}}</td>
                            <td>{{$user->email ?? ''}}</td>
                            <td>{{$user->app_permission ?? ''}}</td>
                            <td>{{$user->exam_permission ?? ''}}</td>


                            <td>
                                <a href="editStudentRecord/{{$user->id}}">
                                    <i class="fas fa-edit text-info font-16"></i>
                                </a>/
                                <form action="{{ route('admin.student.sendFeeReminder', $user->id) }}"
                                      method="POST"
                                      style="display:inline;">
                                    @csrf
                                    <button type="submit"
                                            onclick="return confirm('Send fee reminder to this student?')"
                                            style="border:none;background:transparent;padding:0;"
                                            data-placement="right"
                                            title="Send Fee Reminder"
                                            data-original-title="Send Fee Reminder"
                                            data-trigger="hover">
                                        <i class="fas fa-bell text-warning font-16"></i>
                                    </button>
                                </form>/
                                <a href="/admin/student-update-password/{{$user->id}}"" data-placement="right" title="Password reset" data-original-title="Password reset" data-trigger="hover">
                                    <i class="fas fa-lock text-info font-16"></i>/
                                </a>
                                <a onclick="return confirm('Are you sure want to delete?')" href="deleteStudentRecord/{{$user->id}}" " data-placement="right" title="Delete" data-original-title="Delete" data-trigger="hover"><i class="fas fa-trash-alt text-danger font-16"></i></a>

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
