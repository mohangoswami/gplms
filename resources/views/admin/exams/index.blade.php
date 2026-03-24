@extends('layouts.admin_analytics-master')

@section('headerStyle')
 <!-- DataTables -->
<link href="{{ URL::asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Responsive datatable examples -->
<link href="{{ URL::asset('plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@stop


@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="container mt-4">
    <h4>All Exams</h4>
    <a href="{{ route('admin.exams.create') }}" class="btn btn-primary mb-3">Create New Exam</a>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if(session('delete'))
        <div class="alert alert-danger">{{ session('delete') }}</div>
    @endif

    <table id="datatable" class="table">
        <thead>
            <tr>
                <th>Term</th>
                <th>Class</th>
                <th>Subject</th>
                <th>Type</th>
                <th>Max Marks</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($exams as $exam)
            <tr>
                <td>{{ $exam->term->term ?? '-' }}</td>
                <td>{{ $exam->class }}</td>
                <td>{{ $exam->subject }}</td>
                <td>{{ $exam->type }}</td>
                <td>{{ $exam->maxMarks }}</td>

                <td>
                    <a href="{{ route('admin.exams.edit', $exam->id) }}" class="btn btn-sm btn-info">Edit</a>
                    <form action="{{ route('admin.exams.destroy', $exam->id) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this exam?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
                </div><!--end card-body-->
            </div><!--end card-->
        </div> <!--end col-->
    </div><!--end row-->
@endsection


@section('footerScript')
  <!-- Required datatable js -->
        <script src="{{ URL::asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ URL::asset('plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ URL::asset('plugins/peity-chart/jquery.peity.min.js') }}"></script>
        <script src="{{ URL::asset('assets/pages/jquery.analytics_customers.init.js') }}"></script>


@stop
