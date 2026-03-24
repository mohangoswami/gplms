@extends('layouts.teacher_analytics-master')

@section('content')

<!-- Flash Messages -->
@include('layouts.partials.flash-messages')

<div class="container mt-4">
    <h4>Result Entry Dashboard</h4>

    <div class="row mt-3">
        @forelse($classes as $class)
            <div class="col-md-3">
                <div class="card mb-3 shadow-sm">
                    <div class="card-body text-center">
                        <h5>{{ $class }}</h5>

                        <a href="{{ route('teacher.results.students', ['class' => $class]) }}"
                           class="btn btn-primary btn-sm mt-2">
                            Enter Marks
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-muted">
                No result permissions assigned yet.
            </div>
        @endforelse
    </div>
</div>
@endsection
