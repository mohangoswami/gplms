@extends('layouts.student_master')


@section('content')

@php
        $view=false;
@endphp
    @foreach ($exams as $exam)


    @php
     $start_exam = new DateTime($exam->startExam);
    $end_exam = new DateTime($exam->endExam);

    $today_start_exam1 = date("Y-m-d", strtotime($exam->startExam));
    $today_end_exam1 = date("h:ia d-M", strtotime($exam->endExam));
    $today_start_exam = new DateTime($today_start_exam1);
    $today_end_exam = new DateTime($today_end_exam1);

        $currentTimestamp = new DateTime();

        $live = false;
        $viewPage = false;

        if($currentTimestamp >= $today_start_exam && $currentTimestamp <= $today_end_exam){
        $viewPage =true;
        }else{
        $viewPage = false;
        }


        if($currentTimestamp >= $start_exam && $currentTimestamp < $end_exam){
        $live =true;
        }else{
        $live = false;
        }
@endphp

    @if($viewPage==true)
    @php
    $view=true;
    @endphp
       <div class="row mt-5">
        <div class="col-lg-4">
            <h1 class=" text-center">Today's Exams</h1>
            <div class="card bg-light b-round">
                <div class="card-body b-round bg-success">
                    @if($live==true)
                    <div class="ribbon3 rib3-warning">
                        <span class="text-white text-center rib3-warning">Live</span>
                    </div><!--end ribbon-->
                    @endif
                    <h3 class="header-title b-round bg-light text-center mt-5 m-2">{{$exam->title}}</h3>
                    <div class="d-flex justify-content-between">
                        <div>
                            <h2 class="font-weight-semibold mr-2">{{$exam->subject}}</h2>
                        <h5>Max Marks - {{$exam->maxMarks}}</h5>
                        </div>
                        <div>
                            <ul class="list-unstyled mt-3">
                                <li>
                                    <i class="fa fa-clock text-primary fa-sm"></i>
                                    <i class="fas fa-play text-primary fa-sm "></i>
                                    <span>{{date('d/M h:ia', strtotime($exam->startExam))}}</span>
                                </li>
                                <li class="mt-2">
                                    <i class="fa fa-clock text-danger fa-sm"></i>
                                    <i class="fas fa-stop text-danger fa-sm "></i>
                                    <span>{{date('d/M h:ia', strtotime($exam->endExam))}}</span>
                                </li>

                            </ul>
                        </div>
                    </div>
                    @if($live==true)
                    <div class="card-footer b-round text-center">
                    <button onclick="window.location.href='/student/exams/attemptExam/{{$exam->id}}'" type="button" class="btn btn-primary btn-round waves-effect waves-light">
                        <i class="mdi mdi-send mr-2"></i>Attempt</button>
                </div>
                @endif
                </div><!--end card-body-->
            </div><!--end card-->
        </div><!--end col-->
        </div><!--end row-->
        @endif
        @endforeach
        @if($view==false)
        <div class="col-sm-6 offset-2 mt-5">
            <h3>
                Today is no Exam.
            </h3>
        </div>
        @endif
@stop

@section('footerScript')
<script type="text/javascript">

     window.onload = function() {
 var timeout =  setInterval(function() {
    location.reload(true);
  }, 60000);

};
</script>

@stop
