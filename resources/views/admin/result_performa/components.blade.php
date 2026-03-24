@extends('layouts.admin_analytics-master')

@section('headerStyle')
<style>
.term-box {
    border: 1px solid #ddd;
    padding: 15px;
    margin-bottom: 25px;
}
.handle {
    cursor: move;
    font-size: 18px;
}
.component-row td {
    vertical-align: middle;
}
</style>
@stop

@section('content')
<div class="container-fluid">
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
   COMPONENTS UI (CONDITIONAL)
========================= --}}
@if($class)

<h4>Result Performa – Components (Class {{ $class }})</h4>

@if(session('status'))
<div class="alert alert-success">{{ session('status') }}</div>
@endif

<form method="POST">
@csrf
<input type="hidden" name="class" value="{{ $class }}">

@foreach($terms as $term)
<h5 class="mt-4 d-flex justify-content-between align-items-center">
    {{ $term->name }}

    <button type="button"
            class="btn btn-sm btn-secondary addComponent"
            data-term="{{ $term->id }}">
        + Add Component
    </button>
</h5>

<table class="table table-bordered table-sm">
<thead>
<tr>
    <th width="60">Move</th>
    <th>Component</th>
    <th>Type</th>
    <th width="100">Max Marks</th>
    <th>Include</th>
</tr>
</thead>

<tbody class="component-sortable" data-term="{{ $term->id }}">
@foreach($term->components as $component)
<tr class="component-row">
    <td class="handle">☰</td>

    <td>
        <input type="text"
               class="form-control"
               name="components[{{ $term->id }}][{{ $component->id }}][name]"
               value="{{ $component->name }}">

        <input type="hidden"
               class="order-input"
               name="components[{{ $term->id }}][{{ $component->id }}][order_no]"
               value="{{ $component->order_no }}">
    </td>

    <td>
        <select class="form-control form-control-sm eval-type"
            name="components[{{ $term->id }}][{{ $component->id }}][evaluation_type]">
            <option value="marks" {{ $component->evaluation_type=='marks'?'selected':'' }}>Marks</option>
            <option value="grade" {{ $component->evaluation_type=='grade'?'selected':'' }}>Grade</option>
        </select>
    </td>

    <td>
        <input type="number"
               class="form-control form-control-sm max-marks"
               name="components[{{ $term->id }}][{{ $component->id }}][max_marks]"
               value="{{ $component->max_marks }}"
               {{ $component->evaluation_type=='grade'?'disabled':'' }}>
    </td>

    <td class="text-center">
        <input type="checkbox"
               name="components[{{ $term->id }}][{{ $component->id }}][is_included]"
               {{ $component->is_included ? 'checked' : '' }}>
    </td>
</tr>
@endforeach
</tbody>
</table>
@endforeach


<button class="btn btn-primary">
    Save Components
</button>

</form>

@else
<div class="alert alert-info">
    Please select a class to configure Components.
</div>
@endif

</div>
@endsection

@section('footerScript')
<script>
$(document).ready(function () {

    function refreshOrder(container) {
        container.find('tr').each(function (index) {
            $(this).find('.order-input').val(index + 1);
        });
    }

    $(".component-sortable").each(function () {
        $(this).sortable({
            handle: ".handle",
            axis: "y",
            update: function () {
                refreshOrder($(this));
            }
        });
    });

    // Marks / Grade toggle
    $(document).on('change', '.eval-type', function () {
        let row = $(this).closest('tr');
        let type = $(this).val();

        if (type === 'grade') {
            row.find('.max-marks').val('').prop('disabled', true);
        } else {
            row.find('.max-marks').prop('disabled', false);
        }
    });

    let newIndex = 1;

    $(".addComponent").click(function () {

        let termId = $(this).data('term');
        let tbody = $('.component-sortable[data-term="' + termId + '"]');

        let row = `
        <tr class="component-row">
            <td class="handle">☰</td>

            <td>
                <input type="text"
                       class="form-control"
                       name="components[${termId}][new_${newIndex}][name]"
                       placeholder="Component name">

                <input type="hidden"
                       class="order-input"
                       name="components[${termId}][new_${newIndex}][order_no]"
                       value="0">
            </td>

            <td>
                <select class="form-control form-control-sm eval-type"
                    name="components[${termId}][new_${newIndex}][evaluation_type]">
                    <option value="marks">Marks</option>
                    <option value="grade">Grade</option>
                </select>
            </td>

            <td>
                <input type="number"
                       class="form-control form-control-sm max-marks"
                       name="components[${termId}][new_${newIndex}][max_marks]">
            </td>

            <td class="text-center">
                <input type="checkbox"
                       name="components[${termId}][new_${newIndex}][is_included]"
                       checked>
            </td>
        </tr>
        `;

        tbody.append(row);
        refreshOrder(tbody);
        newIndex++;
    });

});

</script>
@endsection
