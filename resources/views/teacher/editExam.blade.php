@extends('layouts.teacher_analytics-master')

@section('headerStyle')
<link href="{{ URL::asset('plugins/dropify/css/dropify.min.css')}}" rel="stylesheet">

<script src="https://mozilla.github.io/pdf.js/build/pdf.js"></script>

<!--Datetime picker-->
<link href="{{ URL::asset('plugins/daterangepicker/daterangepicker.css')}}" rel="stylesheet" />
<link href="{{ URL::asset('plugins/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('plugins/timepicker/bootstrap-material-datetimepicker.css')}}" rel="stylesheet">
<link href="{{ URL::asset('plugins/bootstrap-touchspin/css/jquery.bootstrap-touchspin.min.css')}}" rel="stylesheet" />
@stop


@section('content')

<!-- Flash Messages -->
@include('layouts.partials.flash-messages')

<div class="row mt-4">
<div class="col-lg-6">
    <div class="card">
        <div class="card-body bg-Success">


            <!-- Tab panes -->

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <h3 class="mt-0 header-title">Edit Exam</h3>
                                    <h4 class="mb-3">Class- {{$class . ', Subject- ' . $subject}}</h4>
                                    <p class="text-muted mb-3">Discription- {{$discription}}</p>
                                    <form method="POST" action="{{ route('teacher.postEditExam') }}" enctype="multipart/form-data">
                                        @csrf
                                    <input type="hidden" name="id" value="{{$id}}">
                                        <div class="col-md-6">
                                        </div><!-- end col -->
                                        <label class="mb-3">Exam - Start and End time</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="datetimes">

                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="dripicons-calendar"></i></span>
                                            </div>
                                        </div>
                                        <div class="form-group mt-3 ">
                                            <label class="mb-3">Maximum Marks</label>
                                            <div class="col-sm-10">
                                            <input required id="maxMarks" type="number" value="{{$maxMarks}}" name="maxMarks"/>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-gradient-primary">Submit</button>
                                        <button type="button" onclick="window.location='/teacher/allExams'" class="btn btn-gradient-danger">Cancel</button>
                                    </form>
                                    <div>
                                    </div>

                                </div><!--end card-body-->
                            </div><!--end card-->
                        </div><!--end col-->
                    </div>

                </div>

            </div>
        </div><!--end card-body-->
    </div><!--end card-->

@stop

@section('footerScript')

<script src="{{ URL::asset('assets/pages/jquery.form-upload.init.js')}}"></script>
<script src="{{ URL::asset('plugins/dropify/js/dropify.min.js')}}"></script>
<!--Datetime picker-->
<script src="{{ URL::asset('plugins/moment/moment.js')}}"></script>
<script src="{{ URL::asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{ URL::asset('plugins/select2/select2.min.js')}}"></script>
<script src="{{ URL::asset('plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js')}}"></script>
<script src="{{ URL::asset('plugins/timepicker/bootstrap-material-datetimepicker.js')}}"></script>
<script src="{{ URL::asset('plugins/bootstrap-maxlength/bootstrap-maxlength.min.js')}}"></script>
<script src="{{ URL::asset('plugins/bootstrap-touchspin/js/jquery.bootstrap-touchspin.min.js')}}"></script>
<script src="{{ URL::asset('assets/pages/jquery.forms-advanced.js')}}"></script>



@stop
