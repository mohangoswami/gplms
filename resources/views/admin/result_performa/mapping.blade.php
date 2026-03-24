@extends('layouts.admin_analytics-master')

@section('content')
<div class="container-fluid">

{{-- =========================
   CLASS SELECTOR
========================= --}}
<form method="GET" class="mb-3">
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

@if($class)

{{-- =========================
   SUBJECT FILTER
========================= --}}
<form method="GET" class="mb-3">
    <input type="hidden" name="class" value="{{ $class }}">

    <div class="row">
        <div class="col-md-4">
            <label>Select Subject</label>
            <select name="subject_id"
                    class="form-control"
                    onchange="this.form.submit()">
                <option value="">-- All Subjects --</option>
                @foreach($allSubjects as $sub)
                    <option value="{{ $sub->id }}"
                        {{ $subjectId == $sub->id ? 'selected' : '' }}>
                        {{ $sub->subCode->subject }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</form>

<h4>Subject × Component Mapping (Class {{ $class }})</h4>

@if(session('status'))
<div class="alert alert-success">{{ session('status') }}</div>
@endif

{{-- =========================
   MAPPING FORM
========================= --}}
<form method="POST">
@csrf
<input type="hidden" name="class" value="{{ $class }}">

<table class="table table-bordered table-sm">
<thead>
    {{-- FIRST ROW : TERMS --}}
    <tr>
        <th rowspan="2">Subject</th>
        @foreach($terms as $term)
            <th class="text-center"
                colspan="{{ $term->components->count() }}">
                {{ $term->name }}
            </th>
        @endforeach
    </tr>

    {{-- SECOND ROW : COMPONENTS --}}
    <tr>
        @foreach($terms as $term)
            @foreach($term->components as $component)
                <th class="text-center">
                    {{ $component->name }}
                    <br>
                    <small class="text-muted">
                        ({{ ucfirst($component->evaluation_type) }})
                    </small>
                </th>

            @endforeach
        @endforeach
    </tr>
</thead>

<tbody>
@foreach($subjects as $subject)
<tr>
    <td><strong>{{ $subject->subCode->subject }}</strong></td>

    @foreach($terms as $term)
        @foreach($term->components as $component)

        @php
            $mapped = $subject->subjectComponents
                ->firstWhere('component_id', $component->id);
        @endphp

      <td class="text-center">

            {{-- CONTEXT (IMPORTANT) --}}
            <input type="hidden"
                name="mapping[{{ $subject->id }}][{{ $component->id }}][component_id]"
                value="{{ $component->id }}">

            <input type="hidden"
                name="mapping[{{ $subject->id }}][{{ $component->id }}][term_id]"
                value="{{ $term->id }}">

            {{-- ENABLE / DISABLE --}}
            <input type="checkbox"
                name="mapping[{{ $subject->id }}][{{ $component->id }}][enabled]"
                value="1"
                {{ $mapped ? 'checked' : '' }}>

            {{-- MAX MARKS (DECIMAL SUPPORT) --}}
            @if($component->evaluation_type === 'marks')
                <input type="number"
                    step="0.01"
                    min="0"
                    class="form-control form-control-sm mt-1"
                    name="mapping[{{ $subject->id }}][{{ $component->id }}][max_marks]"
                    value="{{ $mapped->max_marks_override ?? $component->max_marks }}"
                    style="width:70px;margin:auto;">
            @endif

        </td>


        @endforeach
    @endforeach
</tr>
@endforeach
</tbody>
</table>

<button class="btn btn-primary">
    Save Mapping
</button>

</form>

@else
<div class="alert alert-info">
    Please select a class to configure mapping.
</div>
@endif

</div>
@endsection
