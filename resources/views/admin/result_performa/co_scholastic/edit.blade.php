@extends('layouts.admin_analytics-master')

@section('content')
<div class="container-fluid mt-4">

    <div class="card shadow-sm">
        <div class="card-header bg-warning">
            <h5 class="mb-0">Edit Co-Scholastic Area</h5>
        </div>

        <div class="card-body">

            <form method="POST"
                  action="{{ route('admin.result_performa.co_scholastic.update', $area->id) }}"
                  class="row g-3">
                @csrf
                @method('PUT')

                <div class="col-md-4">
                    <label class="form-label">Class / Performa</label>
                    <select name="performa_id" class="form-control" required>
                        @foreach($performas as $p)
                            <option value="{{ $p->id }}"
                                {{ $area->performa_id == $p->id ? 'selected' : '' }}>
                                Class {{ $p->class }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Area Name</label>
                    <input type="text"
                           name="area_name"
                           value="{{ $area->area_name }}"
                           class="form-control"
                           required>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="is_active" class="form-control">
                        <option value="1" {{ $area->is_active ? 'selected' : '' }}>
                            Active
                        </option>
                        <option value="0" {{ !$area->is_active ? 'selected' : '' }}>
                            Inactive
                        </option>
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-success w-100">
                        💾 Update
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
