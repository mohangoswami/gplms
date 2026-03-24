@extends('layouts.admin_analytics-master')

@section('headerStyle')
<!-- DataTables -->
<link href="{{ URL::asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('content')
<div class="container-fluid mt-3">
    @include('layouts.partials.flash-messages')

    <div class="card">
        <div class="card-body">
            <div class="d-flex mb-3 align-items-center">
                <h4 class="m-0">Exam Marks (Admin)</h4>
                <div class="ml-auto">
                    <a href="{{ route('admin.exams.create') ?? url('/admin/createExam') }}" class="btn btn-gradient-primary">
                        <i class="mdi mdi-plus-circle-outline mr-2"></i> New Exam
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table id="exams-table" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
                    <thead class="thead-light">
                        <tr>
                            <th>Exam Type</th>
                            <th>Class</th>
                            <th>Subject</th>
                            <th>Term</th>
                            <th>Max Marks</th>
                            <th>Created</th>
                            <th style="width:120px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($exams as $exam)
                            <tr>
                                <td>{{ $exam->type }}</td>
                                <td>{{ $exam->class }}</td>
                                <td>{{ $exam->subject }}</td>
                                <td>{{ optional($exam->term)->term ?? '-' }}</td>
                                <td>{{ $exam->maxMarks ?? '-' }}</td>
                                <td>{{ $exam->created_at ? $exam->created_at->format('Y-m-d') : '-' }}</td>
                                <td>
                                    <a href="{{ route('admin.examMarks.enter', $exam->id) }}" class="btn btn-sm btn-primary">Enter</a>
                                    <a href="{{ route('admin.exams.edit', $exam->id) ?? url('/admin/editExam/'.$exam->id) }}" class="btn btn-sm btn-light">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div> <!-- table-responsive -->
        </div><!-- card-body -->
    </div><!-- card -->
</div>
@endsection

@section('footerScript')
<!-- Required datatable js -->
<script src="{{ URL::asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ URL::asset('plugins/datatables/dataTables.responsive.min.js') }}"></script>
<script src="{{ URL::asset('plugins/datatables/responsive.bootstrap4.min.js') }}"></script>

<script>
    $(document).ready(function() {
        var table = $('#exams-table').DataTable({
            responsive: true,
            // pageLength: 15,
            // lengthMenu: [ [10, 25, 50, 100], [10, 25, 50, 100] ],
            columnDefs: [
                { orderable: false, targets: -1 } // disable ordering on Action column
            ],
            // Optional: initial order
            order: [[5, 'desc']] // sort by Created desc (adjust column index if needed)
        });

        // If you want a separate search input, you can listen and apply:
        // $('#mySearchInput').on('keyup', function() {
        //     table.search(this.value).draw();
        // });
    });
</script>
@stop
