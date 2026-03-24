@extends('layouts.admin_analytics-master')

@section('headerStyle')
<style>
.term-row .handle {
    cursor: move;
    font-size: 18px;
}
</style>
@stop

@section('content')
<div class="container">

{{-- =========================
   CLASS SELECTOR (ALWAYS)
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

{{-- =========================
   TERMS UI (CONDITIONAL)
========================= --}}
@if($class)

<h4>Result Performa – Terms (Class {{ $class }})</h4>

@if(session('status'))
<div class="alert alert-success">{{ session('status') }}</div>
@endif

<form method="POST">
@csrf
<input type="hidden" name="class" value="{{ $class }}">

<table class="table table-bordered">
    <thead>
    <tr>
        <th width="60">Move</th>
        <th>Term Name</th>
    </tr>
    </thead>

    <tbody id="term-sortable">
    @foreach($terms as $term)
    <tr class="term-row">
        <td class="handle text-center">☰</td>
        <td>
            <input type="text"
                   class="form-control"
                   name="terms[{{ $term->id }}][name]"
                   value="{{ $term->name }}">

            <input type="hidden"
                   class="order-input"
                   name="terms[{{ $term->id }}][order_no]"
                   value="{{ $term->order_no }}">
        </td>
    </tr>
    @endforeach
    </tbody>
</table>

<button type="button" id="addTerm" class="btn btn-secondary btn-sm">
    + Add Term
</button>

<button class="btn btn-primary float-right">
    Save Terms
</button>

</form>

@else
<div class="alert alert-info">
    Please select a class to configure Result Terms.
</div>
@endif

</div>
@endsection

@section('footerScript')
<script>
$(document).ready(function () {

    function refreshOrder() {
        $("#term-sortable tr").each(function (index) {
            $(this).find('.order-input').val(index + 1);
        });
    }

    $("#term-sortable").sortable({
        handle: ".handle",
        axis: "y",
        update: refreshOrder
    });

    let newIndex = 1;

    $("#addTerm").click(function () {

        let row = `
        <tr class="term-row">
            <td class="handle text-center">☰</td>
            <td>
                <input type="text"
                       class="form-control"
                       name="terms[new_${newIndex}][name]"
                       placeholder="Term name">

                <input type="hidden"
                       class="order-input"
                       name="terms[new_${newIndex}][order_no]"
                       value="0">
            </td>
        </tr>
        `;

        $("#term-sortable").append(row);
        refreshOrder();
        newIndex++;
    });

});
</script>
@endsection
