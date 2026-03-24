@php
$layout = Auth::guard('admin')->check()
    ? 'layouts.admin_analytics-master'
    : 'layouts.teacher_analytics-master';
@endphp

@extends($layout)

@section('headerStyle')
<style>
body {
    font-family: Arial, sans-serif;
    font-size: 12px;
    color: #333;
}
.text-success { font-size: 14px; }
.text-danger { font-size: 14px; }
.table th {
    background-color: #f8f9fa;
}
.table-hover tbody tr:hover {
    background-color: #f1f1f1;
}

.status-dot {
    font-size: 25px;      /* 👈 size increase */
    line-height: 1;
    vertical-align: middle;
    margin-left: 4px;
}

.status-dot.success {
    color: #28a745;       /* green */
}

.status-dot.danger {
    color: #dc3545;       /* red */
}

.status-dot {
    font-size: 30px;
    line-height: 1;
    vertical-align: middle;
    margin-left: 6px;
}

.status-dot.success { color: #28a745; }  /* Green */
.status-dot.warning { color: #ffc107; }  /* Yellow */
.status-dot.danger  { color: #dc3545; }  /* Red */


</style>
@stop

@section('content')

<!-- Flash Messages -->
@include('layouts.partials.flash-messages')

<div class="container-fluid">

<h4 class="mb-3">Result Entry – Student List</h4>

{{-- =========================
   CLASS SELECTOR
========================= --}}
<form method="GET"
      action="{{ Auth::guard('teacher')->check()
            ? route('teacher.results.list')
            : route('admin.results.studentList') }}"
      class="mb-4">
    <div class="row">
        <div class="col-md-4">
            <label>Select Class</label>
            <select name="class"
                    class="form-control"
                    onchange="this.form.submit()">
                <option value="">-- Select Class --</option>
                @foreach($classes as $cls)
                    <option value="{{ $cls }}"
                        {{ $cls == $class ? 'selected' : '' }}>
                        {{ $cls }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</form>



{{-- =========================
   STUDENT TABLE
========================= --}}
@if($class)

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Students of Class {{ $class }}</span>

        @auth('admin')
        <a href="{{ route('admin.results.class.bulk.pdf', $class) }}"
           class="btn btn-success btn-sm">
            Download All Report Cards
        </a>
        @endauth
    </div>

    <div class="card-body p-0">
        <table class="table table-bordered table-hover mb-0">
            <thead>
                <tr>
                    <th width="80">S. No.</th>
                    <th>Student Name</th>
                    <th>Father Name</th>
                    <th width="150">Admission No</th>
                    <th width="160" class="text-center">Action</th>
                </tr>
            </thead>

            <tbody>
            @forelse($students as $student)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>
                        {{ $student->name }}
                            @if(($finalizedMap[$student->id] ?? false))
                                <span class="badge badge-danger ml-2">FINAL</span>

                            @elseif(($statusMap[$student->id] ?? '') === 'complete')
                                <span class="status-dot success" title="Completed">●</span>
                            @elseif(($statusMap[$student->id] ?? '') === 'partial')
                                <span class="status-dot warning" title="Partial">●</span>
                            @else
                                <span class="status-dot danger" title="Pending">●</span>
                            @endif

                    </td>
                    <td>{{ $student->fName }}</td>
                    <td>{{ $student->admission_number }}</td>
                    <td class="text-center">

                        {{-- 🔒 If FINALIZED → SHOW PDF --}}
                        @if(($finalizedMap[$student->id] ?? false) === true)

                            <a href="{{ Auth::guard('admin')->check()
                                    ? route('admin.results.annual.pdf', [$student->id, 'term_id' => request('term_id')])
                                    : route('teacher.results.pdf', [$student->id, 'term_id' => request('term_id')]) }}"
                            class="btn btn-sm btn-outline-danger"
                            target="_blank">
                                📄 View Result
                            </a>

                        {{-- ✏️ If NOT FINALIZED → SHOW ENTRY --}}
                        @else

                            @if(Auth::guard('admin')->check())
                                <a href="{{ route('admin.results.entry', $student->id) }}"
                                class="btn btn-sm btn-primary">
                                    Enter Result
                                </a>
                            @elseif(Auth::guard('teacher')->check())
                                <a href="{{ route('teacher.results.entry', $student->id) }}"
                                class="btn btn-sm btn-success">
                                    Enter Marks
                                </a>
                            @endif

                        @endif

                    </td>


                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">
                        No students found for this class
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@else
<div class="alert alert-info">
    Please select a class to view students.
</div>
@endif

</div>
@endsection
