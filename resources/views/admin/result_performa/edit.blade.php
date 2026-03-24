@extends('layouts.admin_analytics-master')

@section('headerStyle')
<style>
.handle {
    cursor: move;
    font-size: 18px;
}
</style>
@stop

@section('content')
<div class="container">

<h3>Result Performa – Class {{ $class }}</h3>

@if(session('status'))
<div class="alert alert-success">{{ session('status') }}</div>
@endif

<form method="POST">
@csrf
<input type="hidden" name="performa_id" value="{{ $performa->id }}">

<table class="table table-bordered">
<thead>
<tr>
    <th width="60">Move</th>
    <th width="80">Include</th>
    <th>Subject</th>
    <th width="120">Evaluation</th>
</tr>
</thead>

<tbody id="sortable">
@foreach($subjects as $row)
<tr>
    <td class="handle text-center">☰</td>

    <td class="text-center">
        <input type="checkbox"
            name="subjects[{{ $row->sub_code_id }}][is_included]"
            {{ $row->is_included ? 'checked' : '' }}>
    </td>

    <td>
        {{ $row->subCode->subject }}

        <input type="hidden"
            name="subjects[{{ $row->sub_code_id }}][sub_code_id]"
            value="{{ $row->sub_code_id }}">

        <input type="hidden"
            class="order-input"
            name="subjects[{{ $row->sub_code_id }}][order]"
            value="{{ $row->subject_order }}">
    </td>

    <td>
        <select name="subjects[{{ $row->sub_code_id }}][evaluation_type]"
                class="form-control form-control-sm">
            <option value="marks" {{ $row->evaluation_type === 'marks' ? 'selected' : '' }}>
                Marks
            </option>
            <option value="grade" {{ $row->evaluation_type === 'grade' ? 'selected' : '' }}>
                Grade
            </option>
        </select>
    </td>
</tr>
@endforeach
</tbody>
</table>

<button class="btn btn-primary mt-2">
    Save Performa
</button>

</form>
</div>
@endsection

@section('footerScript')
<script>
$(document).ready(function () {

    console.log('Sortable initializing');

    $("#sortable").sortable({
        handle: ".handle",
        axis: "y",
        cursor: "move",
        update: function () {
            $("#sortable tr").each(function (index) {
                $(this).find(".order-input").val(index + 1);
            });
        }
    });

});
</script>
@endsection
