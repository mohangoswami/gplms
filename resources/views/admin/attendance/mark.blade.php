
@include('layouts.partials.flash-errors')
@php
$layout = Auth::guard('admin')->check() ? 'layouts.admin_analytics-master' : 'layouts.cashier-master';
@endphp

@extends($layout)

@section('content')
<div class="container mt-4">
    <h4>Attendance — Class: {{ $class }} | Date: {{ \Carbon\Carbon::parse($date)->format('d-M-Y') }}</h4>

    @if(session('status'))
      <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if(session('failed'))
      <div class="alert alert-danger">{{ session('failed') }}</div>
    @endif

    <div class="card p-3">
        <form method="POST" action="{{ route('admin.attendance.save') }}">
            @csrf
            <input type="hidden" name="class" value="{{ $class }}">
            <input type="hidden" name="date" value="{{ $date }}">

                <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Admission / ID</th>
                        <th>Student Name</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $idx => $student)
                        @php
                            $record = $existing->get($student->id) ?? null;
                            $status = $record ? $record->status : 'P';
                        @endphp
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td>{{ $student->admission_number ?? $student->id }}</td>
                            <td>{{ $student->name }}</td>
                            <td class="text-center">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status[{{ $student->id }}]" id="status_{{ $student->id }}_P" value="P" {{ $status === 'P' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status_{{ $student->id }}_P">P</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status[{{ $student->id }}]" id="status_{{ $student->id }}_L" value="L" {{ $status === 'L' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status_{{ $student->id }}_L">L</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status[{{ $student->id }}]" id="status_{{ $student->id }}_A" value="A" {{ $status === 'A' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status_{{ $student->id }}_A">A</label>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">No students found for this class.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <button class="btn btn-success mt-2">Save Attendance</button>
            <a href="{{ route('admin.attendance.index') }}" class="btn btn-secondary mt-2">Back</a>
        </form>
    </div>
</div>
@endsection

<!-- radios used; no extra scripts required -->
