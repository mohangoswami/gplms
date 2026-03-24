@extends('layouts.teacher_analytics-master')

@section('title', 'Metrica - Admin & Dashboard Template')

@section('content')

<!-- Flash Messages -->
@include('layouts.partials.flash-messages')

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h4 class="mt-0 header-title">{{$class. ' - ' . $subject }}</h4>
                <p class="text-muted mb-3">Create a new Topic. This topic can be used for furthur post materials. </p>
                <form method="POST" action="{{ route('teacher.createTitlePost') }}" enctype="multipart/form-data">
                    @csrf
                <input type="hidden" name="id" id="id" value="{{$id}}">
                <div class="form-group">
                        <label for="lable_title">Create new Topic</label>
                        <input name="inputTitle" class="form-control" type="text" placeholder="Enter title name" id="inputTitle" required>
                    </div>
                    <div class="row mt-5">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="lable_discription">Discription</label>
                                <textarea name="discription" class="form-control" rows="2" id="discription"></textarea>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-gradient-primary">Submit</button>

                    <button type="button" onclick="window.location.href='/teacher/addMaterial/{{$id}}'" class="btn btn-gradient-danger">Back</button>
                </form>
            </div><!--end card-body-->
        </div><!--end card-->
    </div><!--end col-->
    @stop
