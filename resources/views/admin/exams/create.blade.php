@extends('layouts.admin_analytics-master')
@section('content')
<div class="container mt-4">
    <h4>Create New Exam</h4>
    <form method="POST" action="{{ route('admin.exams.store') }}">
        @csrf

        <div class="row">
            <div class="col-md-4">
                <label>Term</label>
                <select name="term_id" class="form-control" required>
                    <option value="">Select Term</option>
                    @foreach($terms as $term)
                        <option value="{{ $term->id }}">{{ $term->term }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label>Class</label>
                <select name="class" class="form-control" required>
                    <option value="">Select Class</option>
                    @foreach($classes as $class)
                        <option value="{{ $class }}">{{ $class }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label>Subject</label>
                <select name="subject" class="form-control" required>
                    <option value="">Select Subject</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject }}">{{ $subject }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-4">
                <label>Max Marks</label>
                <!-- Allow decimal input: use step="any" and inputmode for better mobile keyboards -->
                <input type="number" name="maxMarks" class="form-control" required step="any" inputmode="decimal" min="0">
            </div>

            <div class="col-md-4">
                <label>Exam Type</label>
                <select name="type" class="form-control">
                    <option value="Written">Written</option>
                    <option value="Notebook">Notebook</option>
                    <option value="Subject Enrichment">Subject Enrichment</option>
                    <option value="Oral">Oral</option>
                    <option value="Practical">Practical</option>
                </select>
            </div>
        </div>

        <button class="btn btn-success mt-4">Create Exam</button>
    </form>
</div>
@endsection
