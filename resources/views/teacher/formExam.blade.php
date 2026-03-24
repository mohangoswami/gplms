@extends('layouts.teacher_analytics-master')

@section('headerStyle')
<link href="{{ URL::asset('plugins/dropify/css/dropify.min.css')}}" rel="stylesheet">
@stop

@section('content')

<!-- Flash Messages -->
@include('layouts.partials.flash-messages')

@foreach ($exams as $exam)

        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card">
                    <h5 class="card-header bg-warning text-white mt-0">{{$exam->subject}}</h5>
                    <div class="card-body">
                        <h4 class="card-title mt-0 ">Topic- {{$exam->title}}</h4>
                        <div >

                        </div>
                        {!! $exam->examUrl !!}

                    </div><!--end card-body-->
                </div><!--end card-->
            </div>
        </div>
        @endforeach

@stop

@section('footerScript')

<script src="{{ URL::asset('assets/pages/jquery.form-upload.init.js')}}"></script>
<script src="{{ URL::asset('plugins/dropify/js/dropify.min.js')}}"></script>
@stop
