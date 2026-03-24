@extends('layouts.teacher_analytics-master')

@section('title', 'Metrica - Admin & Dashboard Template')

@section('content')

<!-- Flash Messages -->
@include('layouts.partials.flash-messages')

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                @foreach ($studentResults as $studentResult)
                <h4 class="mt-0 header-title">{{$studentResult->class}} - {{$studentResult->subject}}</h4>

                <p class="text-muted mb-3">Create a new Topic. This topic can be used for furthur post materials. </p>
                <form method="POST" action="{{ route('teacher.postResult') }}" enctype="multipart/form-data">
                    @csrf
                <input type="hidden" name="editId" id="editId" value="{{$id}}">
                <input type="hidden" name="id" id="id" value="{{$studentResult->titleId}}">
                @endforeach
                <div class="form-group">
                        <label for="lable_title">Enter Marks</label>
                        <input name="editMarksObtain" class="form-control" type="number" placeholder="Enter Marks" id="editMarksObtain" required>
                    </div>


                    <button type="submit" class="btn btn-gradient-primary">Submit</button>

                    <button type="button" onclick="window.location.href='/teacher/addMaterial/{{$id}}'" class="btn btn-gradient-danger">Back</button>
                </form>
            </div><!--end card-body-->
        </div><!--end card-->
    </div><!--end col-->
    @stop
