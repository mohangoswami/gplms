@extends('layouts.admin_analytics-master')

@section('headerStyle')
<!-- DataTables (for consistent table look with teacher section) -->
<link href="{{ URL::asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('content')
<div class="container-fluid mt-3">

    <!-- Flash Messages -->
    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if(session('failed'))
        <div class="alert alert-danger">{{ session('failed') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="d-flex mb-3 align-items-center">
                <h4 class="m-0">Enter Marks — {{ $exam->subject ?? $exam->title }} <small class="text-muted">(Class: {{ $exam->class }}, Term: {{ optional($exam->term)->term ?? '-' }})</small></h4>
                <div class="ml-auto">
                    <a href="{{ route('admin.examMarks.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="mdi mdi-arrow-left mr-1"></i> Back to Exams
                    </a>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.examMarks.save', ['examId' => $exam->id]) }}">
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
                                        @if($isGradeOnly)

                                            {{-- GRADE ONLY SUBJECT --}}
                                            <select name="marks[{{ $student->id }}]"
                                                    class="form-control">
                                                <option value="">-- Select Grade --</option>
                                                @foreach(['A','B','C','D','E'] as $g)
                                                    <option value="{{ $g }}"
                                                        {{ old('marks.'.$student->id, $existingMark) == $g ? 'selected' : '' }}>
                                                        {{ $g }}
                                                    </option>
                                                @endforeach
                                            </select>

                                        @else

                                            {{-- MARKS SUBJECT --}}
                                            <input type="number"
                                                min="0"
                                                max="{{ $exam->maxMarks }}"
                                                step="0.01"
                                                name="marks[{{ $student->id }}]"
                                                value="{{ old('marks.'.$student->id, $existingMark) }}"
                                                class="form-control"
                                                placeholder="Enter marks">

                                        @endif
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
