@extends('layouts.admin_analytics-master')

@section('headerStyle')
<style>
.term-box {
    border: 1px solid #ddd;
    margin-bottom: 20px;
    padding: 15px;
}
.handle {
    cursor: move;
}
.component-row td {
    vertical-align: middle;
}
</style>
@stop

@section('content')
<div class="container-fluid">

<h4>Result Performa Builder – Class {{ $class }}</h4>

@if(session('status'))
<div class="alert alert-success">{{ session('status') }}</div>
@endif

{{-- TERMS --}}
@foreach($terms as $term)
<div class="term-box">

    <h5>
        ☰ {{ $term->name }}
    </h5>

    <table class="table table-sm table-bordered">
        <thead>
        <tr>
            <th width="40">Move</th>
            <th>Component</th>
            <th width="120">Type</th>
            <th width="100">Max Marks</th>
            <th width="80">Include</th>
        </tr>
        </thead>

        <tbody class="component-sortable">
        @foreach($term->components as $component)
        <tr class="component-row">
            <td class="handle text-center">☰</td>
            <td>{{ $component->name }}</td>
            <td>{{ strtoupper($component->evaluation_type) }}</td>
            <td>{{ $component->max_marks }}</td>
            <td class="text-center">
                {{ $component->is_included ? '✔' : '✖' }}
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>

</div>
@endforeach

{{-- SUBJECTS --}}
<h5>Subjects (from SubCodes)</h5>

<table class="table table-bordered">
<thead>
<tr>
    <th width="40">Move</th>
    <th>Subject</th>
    <th width="80">Include</th>
</tr>
</thead>

<tbody id="subject-sortable">
@foreach($subjects as $row)
<tr>
    <td class="handle text-center">☰</td>
    <td>{{ $row->subCode->subject }}</td>
    <td class="text-center">
        {{ $row->is_included ? '✔' : '✖' }}
    </td>
</tr>
@endforeach
</tbody>
</table>

</div>
@endsection

@section('footerScript')
<script>
$(function () {

    $(".component-sortable").sortable({
        handle: ".handle",
        axis: "y"
    });

    $("#subject-sortable").sortable({
        handle: ".handle",
        axis: "y"
    });

});
</script>
@endsection
