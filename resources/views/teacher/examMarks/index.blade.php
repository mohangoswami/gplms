@extends('layouts.teacher_analytics-master')

@section('headerStyle')
<!-- DataTables -->
<link href="{{ URL::asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('content')
<div class="container-fluid mt-3">

    <!-- Flash Messages -->
    @include('layouts.partials.flash-messages')

    <div class="card">
        <div class="card-body">
            <div class="d-flex mb-3 align-items-center">
                <h4 class="header-title m-0">Exam Marks Entry</h4>
            </div>

            <div class="table-responsive">
                <table id="datatable" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Term</th>
                            <th>Class</th>
                            <th>Subject</th>
                            <th>Exam Type</th>
                            <th>Max Marks</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($exams as $index => $exam)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $exam->term->term ?? '-' }}</td>
                                <td>{{ $exam->class }}</td>
                                <td>{{ $exam->subject }}</td>
                                <td>{{ $exam->type }}</td>
                                <td>{{ $exam->maxMarks ?? 100 }}</td>
                                <td>
                                    <a href="{{ route('teacher.examMarks.enter', $exam->id) }}"
                                       class="btn btn-sm btn-gradient-primary">
                                       <i class="mdi mdi-pencil mr-1"></i> Enter Marks
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    No exams available for your assigned subjects.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div><!--end table-responsive-->
        </div><!--end card-body-->
    </div><!--end card-->

</div><!--end container-fluid-->
@endsection

@section('footerScript')
<!-- Required datatable js -->
<script src="{{ URL::asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ URL::asset('plugins/datatables/dataTables.responsive.min.js') }}"></script>
<script src="{{ URL::asset('plugins/datatables/responsive.bootstrap4.min.js') }}"></script>

<script>
$(document).ready(function() {
    $('#datatable').DataTable({
        paging: true,
        ordering: false,
        pageLength: 25,
        language: {
            searchPlaceholder: "Search exams...",
            search: ""
        }
    });
});
</script>
@stop
