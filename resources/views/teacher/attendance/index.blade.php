
@extends('layouts.teacher_analytics-master')

@section('content')
<div class="container mt-4">
    <h4>Mark Attendance</h4>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if(session('failed'))
        <div class="alert alert-danger">{{ session('failed') }}</div>
    @endif

    <div class="card p-3">
        <form method="POST" action="{{ route('teacher.attendance.show') }}">
            @csrf

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Class</label>
                    <select name="class" class="form-control" required>
                        <option value="">Select Class</option>
                        @foreach($classes as $class)
                            <option value="{{ $class }}">{{ $class }}</option>
                        @endforeach
                    </select>
                    @error('class')<small class="text-danger">{{ $message }}</small>@enderror
                </div>

                <div class="form-group col-md-4">
                    <label>Date</label>
                    <input type="date" name="date" class="form-control"
                           value="{{ old('date', now()->toDateString()) }}" required>
                    @error('date')<small class="text-danger">{{ $message }}</small>@enderror
                </div>
            </div>

            <button class="btn btn-primary mt-2">Show Students</button>
        </form>
    </div>
</div>
@endsection
