@extends('layouts.teacher_analytics-master')

@section('headerStyle')
 <!-- DataTables -->
<link href="{{ URL::asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Responsive datatable examples -->
<link href="{{ URL::asset('plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Top Scroller-->
<link href="{{ URL::asset('plugins/ticker/jquery.jConveyorTicker.css')}}" rel="stylesheet" type="text/css" />
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-KMDBFHEBCQ"></script>

@stop

@section('content')


<!-- Flash News -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="wrap">
                    <div class="jctkr-label">
                        <span><i class="fas fa-exchange-alt mr-2"></i><i class="fas fa-bullhorn"></i></span>
                    </div>
                    <div class="js-conveyor-example">
                        <ul>
                            @php
                                $n=1;
                            @endphp
                            @isset($flashNews)
                            @foreach ($flashNews as $news)
                                @if($n<=5)
                            <li>
                                <span><i class="fas fa-rss "></i> </span>
                                <span class="usd-rate font-14"><b>{{$news->news}}</b></span>
                                <span class="mb-0 font-12 text-success">{{$news->created_at->format('d M')}}</span>
                            </li>
                                @endif
                                @php
                                    $n=$n+1;
                                @endphp
                            @endforeach
                            @endisset
                        </ul>
                    </div>
                </div>
            </div><!--end card-body-->
        </div><!--end card-->
    </div><!--end col-->
</div>  <!--end row-->

<div class="row">

<div class="container-fluid mt-3">
    <!-- Page-Title -->
    <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mt-0 mb-3">Latest Classwork</h4>
                        <div class="slimscroll crm-dash-activity">
                            <div class="activity">
                                @foreach($classworks as $classwork)
                                @if($classwork->type == 'TOPIC')
                                 @continue
                                @endif
                                <div class="activity-info">
                                    <div class="icon-info-activity">
                                        @if($classwork->type=='IMG')
                                        <i class=" ti-image bg-soft-pink"></i>
                                        @elseif($classwork->type=='PDF')
                                        <i class=" fas fa-file-pdf bg-soft-warning"></i>
                                        @elseif($classwork->type=='DOCS')
                                        <i class="fas fa-file-word bg-soft-primary"></i>
                                        @elseif($classwork->type=='YOUTUBE')
                                        <i class=" ti-youtube bg-soft-danger"></i>
                                       @else
                                        <i class="mdi mdi-checkbox-marked-circle-outline bg-soft-success"></i>
                                        @endif
                                    </div>
                                    <div class="activity-info-text">
                                        <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="m-0 w-75">{{$classwork->class}} - {{$classwork->subject}}</h6>
                                            <span class="text-muted d-block">{{$classwork->created_at->format('d/M')}}</span>
                                        </div>
                                        <div>
                                            <h7>{{$classwork->title}}</h7>
                                        </div>
                                        <p class="text-muted mt-3">{{$classwork->discription}}
                                            <a href="#" class="text-info">[more info]</a>
                                        </p>
                                    </div>
                                </div>
                                <hr>
                                @endforeach

                            </div><!--end activity-->
                        </div><!--end crm-dash-activity-->
                    </div>  <!--end card-body-->
                </div><!--end card-->
            </div><!--end col-->

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mt-0 mb-3">Latest Exams</h4>
                        <div class="slimscroll crm-dash-activity">
                            <div class="activity">
                                @foreach($exams as $exam)

                                <div class="activity-info">
                                    <div class="icon-info-activity">
                                        @if($exam->type=='IMG')
                                        <i class=" ti-image bg-soft-pink"></i>
                                        @elseif($exam->type=='PDF')
                                        <i class=" fas fa-file-pdf bg-soft-warning"></i>
                                        @elseif($exam->type=='DOCS')
                                        <i class="fas fa-file-word bg-soft-danger"></i>
                                        @elseif($exam->type=='FORM')
                                        <i class="fas fa-file-alt bg-soft-primary"></i>
                                       @else
                                        <i class="mdi mdi-checkbox-marked-circle-outline bg-soft-success"></i>
                                        @endif
                                    </div>
                                    <div class="activity-info-text">
                                        <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="m-0 w-75">{{$exam->class}} - {{$exam->subject}}</h6>
                                            <span class="text-muted d-block">{{$exam->created_at->format('d/M')}}</span>
                                        </div>
                                        <div>
                                            <h7>{{$exam->title}}</h7>
                                        </div>
                                        @php
                                            $startTime = strtotime($exam->startExam);
                                            $endTime = strtotime($exam->endExam);
                                        @endphp
                                        <p class="text-muted mt-3">Start at - {{date('d/M h:ia',$startTime)}}
                                          <br>  End at - {{date('d/M h:ia',$endTime)}}
                                          <br>    <a href="#" class="text-info">[more info]</a>
                                        </p>

                                    </div>
                                </div>
                                <hr>
                                @endforeach

                            </div><!--end activity-->
                        </div><!--end crm-dash-activity-->
                    </div>  <!--end card-body-->
                </div><!--end card-->
            </div><!--end col-->

        </div>
    </div>
</div>


@endsection


@section('footerScript')
  <!-- Required datatable js -->
        <script src="{{ URL::asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ URL::asset('plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ URL::asset('plugins/peity-chart/jquery.peity.min.js') }}"></script>
        <script src="{{ URL::asset('assets/pages/jquery.analytics_customers.init.js') }}"></script>

        <script src="{{ URL::asset('plugins/ticker/jquery.jConveyorTicker.min.js')}}"></script>
        <script src="{{ URL::asset('assets/pages/jquery.crypto-news.init.js')}}"></script>

@stop
