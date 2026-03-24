@php
$layout = Auth::guard('admin')->check()
    ? 'layouts.admin_analytics-master'
    : 'layouts.teacher_analytics-master';
@endphp

@extends($layout)

@section('headerStyle')
<style>
.section-card {
    border: 1px solid #ddd;
    border-radius: 6px;
    margin-bottom: 20px;
}
.section-card .card-header {
    background: #f8f9fa;
    font-weight: 600;
}
.table th, .table td {
    vertical-align: middle;
    text-align: center;
}
.readonly {
    background: #f5f5f5;
}
.marks-input {
    width: 30px;
    display: inline-block;
    text-align: center;
}
.student-slider {
    position: sticky;
    top: 70px;
    z-index: 100;
    background: #fff;
}

.status-dot {
    font-size: 25px;      /* 👈 size increase */
    line-height: 1;
    vertical-align: middle;
    margin-left: 4px;
}



.status-dot.success { color: #28a745; }
.status-dot.warning { color: #ffc107; }
.status-dot.danger  { color: #dc3545; }


.blank-cell {
    background-color: #fff3cd !important; /* light yellow */
    border: 2px solid #ffc107;
}

.marks-filled {
    font-weight: 700 !important;
    color: #000 !important;
}


</style>
@stop


@section('content')

<!-- Flash Messages -->
@include('layouts.partials.flash-messages')


<div class="container-fluid">

<h4 class="mb-3">
    Result Entry – {{ $student->name }} ({{ $student->grade }})

    @if(($statusMap[$student->id] ?? '') === 'complete')
        <span class="badge badge-success ml-2">Completed</span>
    @elseif(($statusMap[$student->id] ?? '') === 'partial')
        <span class="badge badge-warning ml-2">Partial</span>
    @else
        <span class="badge badge-danger ml-2">Pending</span>
    @endif
</h4>


    {{-- ===============================
    STUDENT SLIDER
    ================================ --}}
    <div class="card mb-3">
        <div class="card-body d-flex align-items-center justify-content-between">

            {{-- PREV --}}
            <div>
                @if($currentIndex > 0)
                    <a href="{{ route(
                        Auth::guard('teacher')->check()
                            ? 'teacher.results.entry'
                            : 'admin.results.entry',
                        $classStudents[$currentIndex - 1]->id
                    ) }}"
                    class="btn btn-outline-secondary btn-sm">
                        ← Prev
                    </a>
                @else
                    <button class="btn btn-outline-secondary btn-sm" disabled>
                        ← Prev
                    </button>
                @endif
            </div>

            {{-- STUDENT BUTTONS --}}
            <div style="overflow-x:auto; white-space:nowrap; max-width:70%;">
                @foreach($classStudents as $index => $s)
                    <a href="{{ route(
                        Auth::guard('teacher')->check()
                            ? 'teacher.results.entry'
                            : 'admin.results.entry',
                        $s->id
                    ) }}"
                    class="btn btn-sm mx-1
                        {{ $s->id === $student->id
                            ? 'btn-primary'
                            : 'btn-outline-primary' }}">

                        {{-- Roll / Index --}}
                        {{ !empty($s->name)
                            ? $s->name
                            : str_pad($loop->iteration, 3, '0', STR_PAD_LEFT) }}

                        {{-- Status dot --}}
                        @if(($statusMap[$s->id] ?? '') === 'complete')
                            <span class="status-dot success">●</span>
                        @elseif(($statusMap[$s->id] ?? '') === 'partial')
                            <span class="status-dot warning">●</span>
                        @else
                            <span class="status-dot danger">●</span>
                        @endif

                    </a>

                @endforeach
            </div>

            {{-- NEXT --}}
            <div>
                @if($currentIndex < $classStudents->count() - 1)
                    <a href="{{ route(
                        Auth::guard('teacher')->check()
                            ? 'teacher.results.entry'
                            : 'admin.results.entry',
                        $classStudents[$currentIndex + 1]->id
                    ) }}"
                    class="btn btn-outline-secondary btn-sm">
                        Next →
                    </a>
                @else
                    <button class="btn btn-outline-secondary btn-sm" disabled>
                        Next →
                    </button>
                @endif
            </div>

        </div>
    </div>
    {{-- ===============================
    End STUDENT SLIDER
    ================================ --}}

<form method="POST"
      action="{{ Auth::guard('admin')->check()
            ? route('admin.results.entry.save', $student->id)
            : route('teacher.results.save', $student->id) }}">
@csrf

<input type="hidden" name="class" value="{{ $student->grade }}">
<input type="hidden" name="term_id" value="{{ $term->id }}">

{{-- ===============================
   1️⃣ STUDENT BASIC INFO
================================ --}}
<div class="section-card card">
    <div class="card-header">Student Information</div>
    <div class="card-body row">

        <div class="col-md-3">
            <label>Name</label>
            <input type="text" class="form-control readonly"
                   value="{{ $student->name }}" readonly>
        </div>

        <div class="col-md-3">
            <label>Father Name</label>
            <input type="text" class="form-control readonly"
                value="{{ $student->fName }}" readonly>

        </div>

        <div class="col-md-3">
            <label>Mother Name</label>
            <input type="text" class="form-control readonly"
                   value="{{ $student->mName }}" readonly>
        </div>

        <div class="col-md-3">
            <label>Admission No.</label>
            <input type="text" class="form-control readonly"
                   value="{{ $student->admission_number }}" readonly>
        </div>

    </div>

    <div class="card-body row">

        <div class="col-md-3">
            <label>Date of Birth</label>
            <input type="text" class="form-control readonly"
                   value="{{ $student->dob }}" readonly>
        </div>

        <div class="col-md-3">
            <label>Section</label>
            <input type="text" class="form-control readonly"
                   value="{{ $student->section }}" readonly>
        </div>

        <div class="col-md-3">
            <label>Roll No</label>
            <input type="text" class="form-control readonly"
                   value="{{ $student->rollNo }}" readonly>
        </div>

        <div class="col-md-3">
            <label>Mobile</label>
            <input type="text" class="form-control readonly"
                   value="{{ $student->mobile }}" readonly>
        </div>

    </div>
</div>

{{-- ===============================
   2️⃣ SCHOLASTIC PERFORMANCE
================================ --}}
<div class="section-card card">
    <div class="card-header">Scholastic Performance</div>
    <div class="card-body p-0">

        <table class="table table-bordered table-sm mb-0">
            <thead>
            <tr>
                <th rowspan="2">Subject</th>
                @foreach($terms as $t)
                    <th colspan="{{ $t->components->count() }}">
                        {{ $t->name }}
                    </th>
                @endforeach
            </tr>
            <tr>
                @foreach($terms as $t)
                    @foreach($t->components as $component)
                        <th>
                            {{ $component->name }}<br>
                            <small>({{ ucfirst($component->evaluation_type) }})</small>
                        </th>
                    @endforeach
                @endforeach
            </tr>
            </thead>

            @php $locked = $isFinalized ? 'disabled' : ''; @endphp

            <tbody>
                @foreach($subjects as $subject)
                <tr>
                    <td class="text-left font-weight-bold">
                        {{ $subject->subCode->subject }}
                    </td>

                    @foreach($terms as $t)
                        @foreach($t->components as $component)

                            @php
                                $mapping = $subject->subjectComponents
                                    ->firstWhere('component_id', $component->id);

                                if (!$mapping) {
                                    continue; // ❌ Not mapped → skip cell
                                }

                                $maxMarks = $mapping->max_marks_override ?? $component->max_marks;
                            @endphp

    {{-- <td> --}}
                {{-- CONTEXT --}}
                <input type="hidden"
                    name="marks[{{ $subject->id }}][{{ $component->id }}][component_id]"
                    value="{{ $component->id }}">

                <input type="hidden"
                    name="marks[{{ $subject->id }}][{{ $component->id }}][term_id]"
                    value="{{ $t->id }}">

            @php
            $entryKey = $subject->id . '_' . $component->id . '_' . $t->id;
            $existing = $existingEntries[$entryKey] ?? null;
            $isAbsent = $existing && $existing->grade === 'AB';
            $isBlank  = !$existing || (
                    $existing->marks === null &&
                    $existing->grade === null
            );
            @endphp
<td class="{{ $isBlank ? 'blank-cell' : '' }}">


    {{-- MARKS TYPE --}}
        @if($component->evaluation_type === 'marks')
            <div class="d-flex align-items-center justify-content-center gap-1">

                {{-- MARKS INPUT --}}
                @php
                    $hasValue = (!$isAbsent && $existing && $existing->marks !== null);
                @endphp

                <input type="number"
    name="marks[{{ $subject->id }}][{{ $component->id }}][value]"
    class="form-control form-control-sm marks-input {{ $hasValue ? 'marks-filled' : '' }}"
    step="0.01"
    min="0"
    data-max="{{ $maxMarks }}"
    value="{{ $hasValue ? $existing->marks : '' }}"
    {{ $locked || $isAbsent ? 'disabled' : '' }}
    oninput="
        const max = parseFloat(this.dataset.max);
        if(this.value !== '' && parseFloat(this.value) > max){
            this.value = max;
        }
        if(this.value !== ''){
            this.classList.add('marks-filled');
        }else{
            this.classList.remove('marks-filled');
        }
    "
>



                <span class="text-muted">
                    / {{ rtrim(rtrim(number_format($maxMarks,2,'.',''),'0'),'.') }}
                </span>

                {{-- ABSENT --}}
                <label class="ml-1 mb-0">
    <input type="checkbox"
        name="marks[{{ $subject->id }}][{{ $component->id }}][absent]"
        value="1"
        {{ $isAbsent ? 'checked' : '' }}
        onclick="
            const input = this.closest('td').querySelector('.marks-input');
            if(this.checked){
                input.value = '';
                input.disabled = true;
                input.classList.remove('marks-filled');
            }else{
                input.disabled = false;
            }
        "
    >
    AB
</label>

            </div>

        {{-- GRADE TYPE --}}
        @else
            <select {{ $locked }} class="form-control form-control-sm"
                name="marks[{{ $subject->id }}][{{ $component->id }}][value]"
                style="width:70px;margin:auto;">
                <option value="">-</option>
                @foreach(['A','B','C','D'] as $g)
                    <option value="{{ $g }}"
                        {{ ($existing && $existing->grade === $g) ? 'selected' : '' }}>
                        {{ $g }}
                    </option>
                @endforeach
            </select>
        @endif
</td>


                        @endforeach
                    @endforeach
                </tr>
                @endforeach
                </tbody>

        </table>

    </div>
</div>

{{-- ===============================
   3️⃣ ATTENDANCE
================================ --}}
<div class="section-card card">
    <div class="card-header">Attendance</div>
    <div class="card-body row">

        <div class="col-md-3">
            <label>Days Present</label>
            <input type="number" {{ $locked }}
                   name="attendance[present]"
                   value="{{ $attendance->days_present ?? '' }}"
                   class="form-control">
        </div>

        <div class="col-md-3">
            <label>Working Days</label>
            <input type="number" {{ $locked }}
                   name="attendance[working]"
                   value="{{ $attendance->working_days ?? '' }}"
                   class="form-control">
        </div>

    </div>
</div>

{{-- ===============================
   CO-SCHOLASTIC AREAS (TERM WISE)
================================ --}}
<div class="section-card card">
    <div class="card-header">Co-Scholastic Areas</div>

    <div class="card-body p-0">
        <table class="table table-bordered table-sm mb-0">
            <thead>
                <tr>
                    <th>Area</th>
                    @foreach($terms as $t)
                        <th>{{ $t->name }}</th>
                    @endforeach
                </tr>
            </thead>

            <tbody>
                @foreach($coScholasticAreas as $area)
                    <tr>
                        <td class="text-left font-weight-bold">
                            {{ $area->area_name }}
                        </td>

                        @foreach($terms as $t)

                            @php
                                $entry = $coScholastic
                                    ->where('co_scholastic_area_id', $area->id)
                                    ->where('term_id', $t->id)
                                    ->first();
                            @endphp

                            <td>
                                <select
                                    {{ $isFinalized ? 'disabled' : '' }}
                                    name="co_scholastic[{{ $t->id }}][{{ $area->id }}]"
                                    class="form-control form-control-sm">

                                    <option value="">-</option>
                                    @foreach(['A','B','C','D'] as $grade)
                                        <option value="{{ $grade }}"
                                            {{ optional($entry)->grade === $grade ? 'selected' : '' }}>
                                            {{ $grade }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>


    {{-- ===============================
    5️⃣ HEALTH + REMARKS
    ================================ --}}
    <div class="section-card card">
        <div class="card-header">Health & Remarks</div>
        <div class="card-body row">

            <div class="col-md-3">
                <label>Height (cm)</label>
                <input type="number" {{ $locked }} step="0.01"
                    name="health[height]"
                    value="{{ $health->height ?? '' }}"
                    class="form-control">
            </div>

            <div class="col-md-3">
                <label>Weight (kg)</label>
                <input type="number" {{ $locked }} step="0.01"
                    name="health[weight]"
                    value="{{ $health->weight ?? '' }}"
                    class="form-control">
            </div>

            <div class="col-md-6">
                <label>Class Teacher's Remark</label>
                <textarea {{ $locked }} name="remark" rows="2" class="form-control">
                {{ $health->remark ?? '' }}
                </textarea>
            </div>

        </div>
    </div>
{{-- ===============================
   6️⃣ FINALIZE / REOPEN
================================ --}}
    <div class="section-card card m-5">
    <div class="d-flex justify-content-between mb-3">



@if(!$isFinalized)
    <button class="btn btn-success btn-lg">Save Full Result</button>
@else
    <button class="btn btn-secondary btn-lg" disabled>🔒 Result Finalized (Locked)</button>
@endif
    </form>

        @if($isFinalized)
    <button class="btn btn-secondary btn-lg" disabled>
        🔒 Result Finalized
    </button>
@elseif($hasBlank)
    <button class="btn btn-primary btn-lg" disabled>
        ❌ Complete all marks to Finalize
    </button>
@else
    <button type="button"
        class="btn btn-primary btn-lg"
        data-toggle="modal"
        data-target="#finalizeModal">
        Finalize Result
    </button>
@endif

@if(auth('admin')->check() && !$isFinalized)

<form method="POST"
      action="{{ route('admin.results.delete.full', $student->id) }}"
      onsubmit="return confirm(
        '⚠️ This will DELETE the COMPLETE ANNUAL RESULT.\n\nMarks, Co-Scholastic, Attendance, Health & Finalization will be removed.\n\nAre you sure?'
      )">

    @csrf
    @method('DELETE')

    <button class="btn btn-danger btn-lg">
        ❌ Delete Result
    </button>
</form>

@endif


@if(auth('admin')->check() && $isFinalized)

<form method="POST"
      action="{{ route('admin.results.reopen', $student->id) }}"
      onsubmit="return confirm('⚠️ Reopen annual result for editing?')">

    @csrf

    <button class="btn btn-warning">
        🔓 Reopen Annual Result (Admin)
    </button>
</form>

@endif


</div>
    </div>


@endsection

{{-- ===============================
FINALIZE CONFIRMATION MODAL
================================ --}}
<div class="modal fade" id="finalizeModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">

      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Confirm Result Finalization</h5>
        <button type="button" class="close text-white" data-dismiss="modal">
          ×
        </button>
      </div>

      <div class="modal-body">
        <p class="mb-2">
            ⚠️ <strong>Once finalized:</strong>
        </p>
        <ul>
            <li>Marks cannot be edited</li>
            <li>Attendance cannot be changed</li>
            <li>Co-Scholastic & Health locked</li>
        </ul>

        <p class="text-danger mb-0">
            Are you sure you want to finalize this result?
        </p>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-dismiss="modal">
            Cancel
        </button>
    @if(auth('admin')->check() )
            <form method="POST"
                action="{{ route('admin.results.finalize', $student->id) }}">
    @elseif(auth('teacher')->check())
             <form method="POST"
                action="{{ route('teacher.results.finalize', $student->id) }}">
    @endif
            @csrf
            <input type="hidden" name="term_id" value="{{ $term->id }}">
            <button class="btn btn-danger">
                Yes, Finalize
            </button>
        </form>
      </div>

    </div>
  </div>
</div>
{{--
@section('footerScript')
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
@endsection --}}
