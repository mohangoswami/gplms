@extends('layouts.admin_analytics-master')
@section('content')
<div class="container">
    <h4>Exam Terms</h4>
    <form method="POST" action="{{ route('admin.term.store') }}">
        @csrf
        <input type="text" name="term" placeholder="Enter term name (e.g. Periodic 1)" required>
        <button class="btn btn-primary btn-sm">Add</button>
    </form>
    <hr>
    <ul>
        @foreach($terms as $term)
            <li>{{ $term->term }}
                <form action="{{ route('admin.term.delete', $term->id) }}" method="POST" style="display:inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-sm">Delete</button>
                </form>
            </li>
        @endforeach
    </ul>
</div>
@endsection
