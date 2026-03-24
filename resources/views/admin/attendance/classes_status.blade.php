{{-- resources/views/admin/attendance/classes_status.blade.php

Required variables (from controller):
- $date (string Y-m-d) optional — defaults to today
- $classes (Collection|array) — list of classes (strings) to check, e.g. ['1ST','2ND','3RD','4TH', ...]
- $attendanceSummary (associative array) optional — keyed by class name, each value:
    [
      'done' => bool,             // true if attendance exists for that class & date
      'present' => int|null,      // optional count of present students
      'absent' => int|null,       // optional count of absent students
      'total'  => int|null,       // optional total student count
    ]
  If $attendanceSummary is not provided, the blade will mark classes 'Not Done'.
--}}

@include('layouts.partials.flash-errors')
@php
$layout = Auth::guard('admin')->check() ? 'layouts.admin_analytics-master' : 'layouts.cashier-master';
@endphp

@extends($layout)

@section('headerStyle')
<link href="{{ URL::asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<style>
    .status-done { background: #d4edda; color: #155724; font-weight: 600; padding: 6px 10px; border-radius: 4px; display:inline-block; }
    .status-not { background: #f8d7da; color: #721c24; font-weight: 600; padding: 6px 10px; border-radius: 4px; display:inline-block; }
    .small-muted { font-size: 0.9rem; color: #6c757d; }
    .table .action-links a { margin-right: 6px; }
</style>
@stop

@section('content')
<div class="container mt-4">
    @php
        $date = $date ?? \Carbon\Carbon::now()->toDateString();
        // ensure attendanceSummary exists so blade can safely reference it
        $attendanceSummary = $attendanceSummary ?? [];
        $classesList = collect($classes ?? [])->unique()->values();
        $doneCount = $classesList->filter(fn($c) => isset($attendanceSummary[$c]) && ($attendanceSummary[$c]['done'] ?? false))->count();
        $notDoneCount = $classesList->count() - $doneCount;
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Attendance Status by Class — {{ \Carbon\Carbon::parse($date)->format('d M, Y') }}</h4>

        <div class="d-flex align-items-center">
            <form method="GET" action="{{ route('admin.attendance.classes.status') }}" class="form-inline mr-3" id="formDate">
                <label class="mr-2 small-muted">Date</label>
                <input type="date" name="date" class="form-control form-control-sm mr-2" value="{{ $date }}">
                <button class="btn btn-primary btn-sm" type="submit">Go</button>
            </form>

            <div class="text-right small-muted">
                <div>Classes: <strong>{{ $classesList->count() }}</strong></div>
                <div class="mt-1">Done: <span class="badge badge-success">{{ $doneCount }}</span>
                    &nbsp; Not done: <span class="badge badge-danger">{{ $notDoneCount }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="table-responsive">
                <table id="classesStatus" class="table table-sm table-hover table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th style="width:60px">#</th>
                            <th>Class</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">Present</th>
                            <th class="text-center">Absent</th>
                            <th class="text-center">Status</th>
                            <th style="width:210px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classesList as $idx => $className)
                            @php
                                $info = $attendanceSummary[$className] ?? null;
                                $done = $info['done'] ?? false;
                                $present = $info['present'] ?? null;
                                $absent = $info['absent'] ?? null;
                                $total  = $info['total'] ?? null;
                            @endphp
                            <tr class="{{ $done ? '' : 'table-warning' }}">
                                <td>{{ $idx + 1 }}</td>
                                <td><strong>{{ $className }}</strong></td>
                                <td class="text-center">{{ $total === null ? '-' : $total }}</td>
                                <td class="text-center">{{ $present === null ? '-' : $present }}</td>
                                <td class="text-center">{{ $absent === null ? '-' : $absent }}</td>
                                <td class="text-center">
                                    @if($done)
                                        <span class="status-done">Done</span>
                                    @else
                                        <span class="status-not">Not Done</span>
                                    @endif
                                </td>
                                <td class="action-links">
                                    {{-- View day attendance --}}
                                    <a href="{{ route('admin.attendance.day', ['class' => $className, 'date' => $date]) }}" class="btn btn-sm btn-outline-primary">View</a>

                                    {{-- Mark attendance (if you have a mark route) --}}
                                    <a href="{{ route('admin.attendance.index', ['class' => $className, 'date' => $date]) }}" class="btn btn-sm btn-outline-success">Mark</a>

                                    {{-- Export class (CSV of absent or full) --}}
                                    <a href="{{ route('admin.attendance.class.csv', array_merge(['class' => $className, 'date' => $date])) }}" class="btn btn-sm btn-outline-secondary">CSV</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No classes defined.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footerScript')
<script src="{{ URL::asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        try {
            $('#classesStatus').DataTable({
                paging: false,
                searching: true,
                ordering: true,
                info: false,
                columnDefs: [
                    { orderable: false, targets: [0,6] }
                ]
            });
        } catch (e) {
            console.warn('DataTables init failed', e);
        }
    });
</script>
@stop
