@extends('layouts.admin_analytics-master')

@section('headerStyle')
<link href="{{ asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<link href="{{ asset('plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet">

<style>
    .marks-input {
        width: 80px;
        text-align: center;
    }
    .grade-select {
        width: 90px;
    }
    th, td {
        vertical-align: middle !important;
        text-align: center;
    }
    th.subject-header {
        background: #f8f9fa;
        font-weight: 600;
    }
</style>
@stop

@section('content')
<div class="container-fluid mt-3">

    <h4 class="mb-3">Marks Entry (Performa Driven)</h4>

    {{-- ==========================
         STATUS MESSAGES
    ========================== --}}
    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- ==========================
         FILTER BAR
    ========================== --}}
    <form method="GET" class="card mb-3">
        <div class="card-body row">

            <div class="col-md-3">
                <label>Class</label>
                <select name="class" class="form-control" onchange="this.form.submit()">
                    <option value="">Select Class</option>
                    @foreach($classes as $c)
                        <option value="{{ $c }}" {{ request('class') == $c ? 'selected' : '' }}>
                            {{ $c }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label>Term</label>
                <select name="term" class="form-control" onchange="this.form.submit()">
                    <option value="">Select Term</option>
                    @foreach($terms as $t)
                        <option value="{{ $t }}" {{ request('term') == $t ? 'selected' : '' }}>
                            {{ $t }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3 align-self-end">
                <a href="{{ url()->current() }}" class="btn btn-secondary">Reset</a>
            </div>

        </div>
    </form>

    {{-- ==========================
         MARKS ENTRY TABLE
    ========================== --}}
    @if($items->count() && $students->count())

    <form method="POST" action="{{ url()->current() }}/save">
        @csrf

        <input type="hidden" name="class" value="{{ request('class') }}">
        <input type="hidden" name="term" value="{{ request('term') }}">

        <div class="card">
            <div class="card-body table-responsive">

                <table id="marksTable" class="table table-bordered table-striped">
                    <thead>
                        {{-- SUBJECT HEADER --}}
                        <tr>
                            <th rowspan="2">Student</th>
                            @foreach($items->groupBy(fn($i) => $i->subCode->subject) as $subject => $comps)
                                <th colspan="{{ $comps->count() }}" class="subject-header">
                                    {{ $subject }}
                                </th>
                            @endforeach
                        </tr>

                        {{-- COMPONENT HEADER --}}
                        <tr>
                            @foreach($items as $item)
                                <th>
                                    {{ $item->component ?? 'Grade' }}
                                    @if($item->max_marks)
                                        <br>
                                        <small>({{ $item->max_marks }})</small>
                                    @endif
                                </th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($students as $student)
                        <tr>
                            <td class="text-left">{{ $student->name }}</td>

                            @foreach($items as $item)
                                @php
                                    $existing = $existingEntries[$student->id.'_'.$item->id] ?? null;
                                @endphp

                                <td>
                                    {{-- ========== GRADE ========== --}}
                                    @if($item->evaluation_type === 'GRADE')
                                        <select
                                            name="marks[{{ $student->id }}][{{ $item->id }}]"
                                            class="form-control grade-select">

                                            <option value="">-</option>
                                            @foreach(['A','B','C','D','E'] as $g)
                                                <option value="{{ $g }}"
                                                    {{ old(
                                                        'marks.'.$student->id.'.'.$item->id,
                                                        $existing->grade ?? ''
                                                    ) == $g ? 'selected' : '' }}>
                                                    {{ $g }}
                                                </option>
                                            @endforeach
                                        </select>

                                    {{-- ========== MARKS ========== --}}
                                    @else
                                        <input type="number"
                                               step="0.01"
                                               min="0"
                                               max="{{ $item->max_marks }}"
                                               name="marks[{{ $student->id }}][{{ $item->id }}]"
                                               class="form-control marks-input"
                                               value="{{ old(
                                                   'marks.'.$student->id.'.'.$item->id,
                                                   $existing->marks ?? ''
                                               ) }}">
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="text-right mt-3">
                    <button class="btn btn-primary px-4">Save Marks</button>
                </div>

            </div>
        </div>
    </form>

    @else
        <div class="alert alert-info">
            Select Class and Term to enter marks.
        </div>
    @endif

</div>
@endsection

@section('footerScript')
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.responsive.min.js') }}"></script>

<script>
    $(function () {
        $('#marksTable').DataTable({
            paging: false,
            searching: true,
            ordering: false,
            info: false,
            responsive: true,
            language: {
                searchPlaceholder: "Search student..."
            }
        });
    });
</script>
@stop
