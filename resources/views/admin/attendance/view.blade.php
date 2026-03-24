@include('layouts.partials.flash-errors')
@php
$layout = Auth::guard('admin')->check() ? 'layouts.admin_analytics-master' : 'layouts.cashier-master';
@endphp

@extends($layout)

@section('content')
<div class="container mt-4">
    <h4>View Attendance — Select Class & Month</h4>

    <div class="card mt-3">
        <div class="card-body">
            @if(isset($classes) && count($classes) > 0)
                <form method="POST" action="{{ route('admin.attendance.view.post') }}">
                    @csrf

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="class">Class</label>
                            <select name="class" id="class" class="form-control" required>
                                <option value="">-- Select class --</option>
                                @foreach($classes as $cls)
                                    <option value="{{ $cls }}">{{ $cls }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="month">Month</label>
                            <input type="month" id="month" name="month" class="form-control" value="{{ now()->format('Y-m') }}" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Show Monthly Attendance</button>
                </form>
            @else
                <div class="alert alert-info">No classes assigned.</div>
            @endif
        </div>
    </div>
</div>
@endsection
