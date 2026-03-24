@extends('layouts.admin_analytics-master')

@section('content')
<div class="container-fluid mt-4">

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Co-Scholastic Areas Management</h5>
        </div>

        <div class="card-body">

            {{-- Flash message --}}
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            {{-- ADD FORM --}}
            <form method="POST"
                  action="{{ route('admin.result_performa.co_scholastic.store') }}"
                  class="row g-3 mb-4">
                @csrf

                <div class="col-md-4">
                    <label class="form-label">Class / Performa</label>
                    <select name="performa_id" class="form-control" required>
                        <option value="">Select Class</option>

                        @foreach($classes as $class)
                            <option value="{{ $class }}">
                                Class {{ $class }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Area Name</label>
                    <input type="text"
                           name="area_name"
                           class="form-control"
                           placeholder="e.g. Poem / Computer"
                           required>
                </div>

                <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-success w-100">
                        ➕ Add Area
                    </button>
                </div>
            </form>

            {{-- LIST TABLE --}}
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th width="15%">Class</th>
                            <th width="20%">Performa</th>
                            <th>Area Name</th>
                            <th width="10%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($areas as $area)
                        <tr>
                            <td>{{ $area->class }}</td>
                            <td>Class {{ $area->performa->class ?? '-' }}</td>
                            <td>{{ $area->area_name }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.result_performa.co_scholastic.edit', $area->id) }}"
                                class="btn btn-sm btn-warning mb-1">
                                    ✏️
                                </a>

                                <form method="POST"
                                    action="{{ route('admin.result_performa.co_scholastic.delete', $area->id) }}"
                                    style="display:inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger"
                                            onclick="return confirm('Delete this area?')">
                                        ❌
                                    </button>
                                </form>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                No co-scholastic areas defined
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>
@endsection
