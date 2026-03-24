@extends('layouts.admin_analytics-master')
@section('content')
<div class="container mt-4">
    <h4>Edit Exam</h4>

    <form method="POST" action="{{ route('admin.exams.update', $exam->id) }}">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-4">
                <label>Term</label>
                <select name="term_id" class="form-control" required>
                    <option value="">Select Term</option>
                    @foreach($terms as $term)
                        <option value="{{ $term->id }}" {{ $exam->term_id == $term->id ? 'selected' : '' }}>
                            {{ $term->term }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label>Class</label>
                <select name="class" class="form-control" required>
                    <option value="">Select Class</option>
                    @foreach($classes as $class)
                        <option value="{{ $class }}" {{ $exam->class == $class ? 'selected' : '' }}>
                            {{ $class }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label>Subject</label>
                <select name="subject" class="form-control" required>
                    <option value="">Select Subject</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject }}" {{ $exam->subject == $subject ? 'selected' : '' }}>
                            {{ $subject }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-4">
                <label>Max Marks</label>
                <input type="number" name="maxMarks" value="{{ old('maxMarks', $exam->maxMarks) }}" class="form-control" required>
            </div>

            <div class="col-md-4">
                <label>Exam Type</label>
                <select name="type" class="form-control">
                    <option value="Written" {{ $exam->type == 'Written' ? 'selected' : '' }}>Written</option>
                    <option value="Oral" {{ $exam->type == 'Oral' ? 'selected' : '' }}>Oral</option>
                    <option value="Practical" {{ $exam->type == 'Practical' ? 'selected' : '' }}>Practical</option>
                </select>
            </div>
        </div>

        <button class="btn btn-primary mt-4">Update Exam</button>
    </form>
</div>
@endsection
