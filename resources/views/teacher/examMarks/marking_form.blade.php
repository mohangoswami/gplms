@extends('layouts.teacher_analytics-master')

@section('headerStyle')
<link href="{{ URL::asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('content')
<div class="container mt-3">
    <h3>Enter Marks — {{ $exam->title ?? $exam->subject }} (Class: {{ $exam->class }})</h3>

    @if(session('status'))
      <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('teacher.exams.mark.save', ['exam' => $exam->id]) }}">
        @csrf

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="marksTable" class="table table-bordered table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Admission / ID</th>
                                <th>Student Name</th>
                                <th>Marks (out of {{ $exam->maxMarks ?? 100 }})</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $idx => $student)
                                @php
                                    $rec = $existing->get($student->id) ?? null;
                                    $existingMark = $rec ? $rec->marksObtain : '';
                                @endphp
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td>{{ $student->admission_number ?? $student->id }}</td>
                                    <td>{{ $student->name }}</td>
                                    <td>
                                        <input type="number"
                                               min="0"
                                               max="{{ $exam->maxMarks ?? 100 }}"
                                               step="0.01"
                                               name="marks[{{ $student->id }}]"
                                               value="{{ old('marks.'.$student->id, $existingMark) }}"
                                               class="form-control"
                                               placeholder="Enter marks">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="text-right mt-3">
                    <button type="submit" class="btn btn-gradient-primary px-4">
                        <i class="mdi mdi-content-save mr-1"></i> Save Marks
                    </button>
                    <a href="{{ route('teacher.examMarks.index') }}" class="btn btn-secondary">
                        Back to Exams
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('footerScript')
<script src="{{ URL::asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ URL::asset('plugins/datatables/dataTables.responsive.min.js') }}"></script>
<script src="{{ URL::asset('plugins/datatables/responsive.bootstrap4.min.js') }}"></script>

<script>
    $(document).ready(function () {
        $('#marksTable').DataTable({
            responsive: true,
            paging: true,
            searching: true,
            info: false,
            ordering: false,
            pageLength: 25,
            language: {
                searchPlaceholder: "Search student..."
            }
        });
    });
</script>
@stop
