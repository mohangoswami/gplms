@include('layouts.partials.flash-errors')
@php
$layout = Auth::guard('admin')->check() ? 'layouts.admin_analytics-master' : 'layouts.cashier-master';
@endphp

@extends($layout)

@section('content')
<div class="container mt-4">
    <h4>View Day Attendance</h4>

    <div class="card mt-3">
        <div class="card-body">
            <form action="{{ route('admin.attendance.day.form.post') }}" method="POST" class="form-inline">
                @csrf

                <div class="form-group mr-3">
                    <label class="mr-2">Class</label>
                    <select name="class" id="selClass" class="form-control" required>
                        <option value="">Select class</option>
                        @foreach($classes as $c)
                            <option value="{{ $c }}">{{ $c }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mr-3">
                    <label class="mr-2">Section</label>
                    <select name="section" id="selSection" class="form-control">
                        <option value="">All sections</option>
                        @foreach($sections as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mr-3">
                    <label class="mr-2">Date</label>
                    <input type="date" name="date" class="form-control" value="{{ now()->toDateString() }}" required>
                </div>

                <button class="btn btn-primary" type="submit">View Day</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Optional: if you want to populate sections based on selected class,
    // implement a fetch or a preloaded map. For now sections list is global.
</script>
@endpush
