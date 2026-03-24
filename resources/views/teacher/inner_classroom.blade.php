@extends('layouts.teacher_analytics-master')

@section('headerStyle')
 <!-- DataTables -->
<link href="{{ URL::asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Responsive datatable examples -->
<link href="{{ URL::asset('plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('content')

<!-- Flash Messages -->
@include('layouts.partials.flash-messages')


                                    @php
                                    $img=0;
                                    $pdf=0;
                                    $doc=0;
                                    $ytb=0;
                                @endphp
                                @foreach ($DBtitles as $classwork)

                                    @php

                                      if(isset($classwork->discription)){
                                    $discription= $classwork->discription;
                                        }else {
                                            $discription="";
                                        }

                                        if($classwork->type=='IMG'){
                                            $img=$img+1;
                                        }
                                        if($classwork->type=='PDF'){
                                            $pdf=$pdf+1;
                                        }
                                        if($classwork->type=='DOCS'){
                                            $doc=$doc+1;
                                        }
                                        if($classwork->type=='YOUTUBE'){
                                            $ytb=$ytb+1;
                                        }
                                    @endphp
                                @endforeach
<div class="container-fluid">

                <div class="row">
                    <div class="col-lg-6">

                        <div class="card mt-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-10">
                                        <div class="media">
                                            <h3 class="d-flex align-self-center mr-3  rounded-circle" alt="" height="50">{{$subject}}</h3>
                                            <div class="media-body align-self-center">
                                                <h4 class="mt-0 mb-2 font-24">Topic- {{$title}}</h4>
                                                <ul class="list-inline mb-0 text-muted">
                                                    <li class="list-inline-item mr-2"><span><i class="fas fa-user mr-2 text-info font-14"></i></span>{{$teacherName}}</li>
                                                </ul>
                                            <p><i class="far fa-envelope mr-2 text-info font-14"></i> {{$discription}}</p>
                                            </div><!--end media-body-->
                                        </div><!--end media-->
                                    </div><!--end col-->

                                </div><!--end row-->

                                    <!-- Nav tabs -->
                                <div class="mt-4">
                                    <ul class="nav nav-pills nav-justified " role="tablist">
                                        <li class="nav-item waves-effect waves-light">
                                            <a class="nav-link active" data-toggle="tab" href="#pdf" role="tab">
                                            <i class="fas fa-file-pdf"> Pdf {{$pdf}}</i></a>
                                        </li>
                                        <li class="nav-item waves-effect waves-light">
                                            <a class="nav-link" data-toggle="tab" href="#img" role="tab">
                                                <i class="fas fa-file-image"> Image {{$img}}</i></a>
                                        </li>
                                        <li class="nav-item waves-effect waves-light">
                                            <a class="nav-link" data-toggle="tab" href="#docs" role="tab">
                                                <i class="fas fa-file-word"> Docs {{$doc}}</i></a>
                                        </li>
                                        <li class="nav-item waves-effect waves-light">
                                            <a class="nav-link" data-toggle="tab" href="#youtube" role="tab">
                                                <i class="fas fa-video"> Videos {{$ytb}}</i></a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="progress bg-warning m-2" style="height:5px;">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: 100%" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                    <!-- Tab panes -->
                                    <div class="tab-content">
                                        <div class="tab-pane active p-0" id="pdf" role="tabpanel">
                                           <div class="card-body pt-0">
                                            <ul class="list-group wallet-bal-crypto">

                                                @foreach ($DBtitles as $pdfLoop)
                                                    @if($pdfLoop->type=='PDF')
                                                <a href="{{$pdfLoop->fileUrl}}" target="_blank" >
                                            <div class="row ">
                                                <div class="col-md-10">
                                                <li class="list-group-item align-items-center d-flex justify-content-between">
                                                    <div class="media">
                                                        <img src="{{ URL::asset('assets/images/files logo/download.jpeg')}}" class="mr-3 thumb-sm align-self-center rounded-circle" alt="...">
                                                        <div class="media-body align-self-center">
                                                            <div class="coin-bal">
                                                                @php
                                                                    $filename=basename($pdfLoop->fileUrl);
                                                                    $fileSizes=intval(($pdfLoop->fileSize)/1000);

                                                                @endphp
                                                                <h3 class="m-0">{{$filename}}</h3>
                                                                <p class="text-muted mb-0">Size - {{$fileSizes}}KB</p>
                                                            </div>
                                                        </div><!--end media body-->
                                                    </div>
                                                </a>
                                                            <div class=" ml-3 align-items-start">
                                                                <span ><a href="/teacher/edit_classwork/{{$pdfLoop->id}}" class="mr-1 "><i class="fas fa-edit text-info font-16"></i></a></span>
                                                                <span> <a onclick="return confirm('Are you sure want to delete?')" href="/teacher/classroom/{{$pdfLoop->id}}/delete"><i class="mr-1 fas fa-trash-alt text-danger font-16"></i></a></span>
                                                                <span>  <a href="/teacher/classworkAttendence/{{$pdfLoop->id}}" class="mr-1" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="Attendence" data-trigger="hover"><i class="fas fa-address-card text-info font-16"></i></a></span>
                                                                <span class="mt-3 badge badge-soft-purple">{{$pdfLoop->created_at->format('d/M')}}</span>
                                                            </div>
                                                </li>
                                                </div>
                                            </div>
                                                @endif
                                                @endforeach
                                            </ul>
                                        </div><!--end card-body-->
                                        </div>
                                        <div class="tab-pane p-3" id="img" role="tabpanel">
                                            <div class="card-body pt-0">
                                                <ul class="list-group wallet-bal-crypto">

                                                    @foreach ($DBtitles as $pdfLoop)
                                                        @if($pdfLoop->type=='IMG')
                                                        <a href="{{$pdfLoop->fileUrl}}" target="_blank" >
                                                    <div class="row ">
                                                        <div class="col-md-10">
                                                        <li class="list-group-item align-items-center d-flex justify-content-between">
                                                        <div class="media">
                                                            <img src="{{ URL::asset('assets/images/files logo/jpeg.jpeg')}}" class="mr-3 thumb-sm align-self-center rounded-circle" alt="...">
                                                            <div class="media-body align-self-center">
                                                                <div class="coin-bal">
                                                                    @php
                                                                        $filename=basename($pdfLoop->fileUrl);
                                                                        $fileSizes=intval(($pdfLoop->fileSize)/1000);

                                                                    @endphp
                                                                    <h3 class="m-0">{{$filename}}</h3>
                                                                    <p class="text-muted mb-0">Size - {{$fileSizes}}KB</p>
                                                                </div>
                                                            </div><!--end media body-->
                                                        </div>
                                                    </a>
                                                                <div class=" ml-3 align-items-start">
                                                                    <span ><a href="/teacher/edit_classwork/{{$pdfLoop->id}}" class="mr-2 "><i class="fas fa-edit text-info font-16"></i></a></span>
                                                                    <span> <a onclick="return confirm('Are you sure want to delete?')" href="/teacher/classroom/{{$pdfLoop->id}}/delete"><i class="fas fa-trash-alt text-danger font-16"></i></a></span>
                                                                    <span class="mt-3 badge badge-soft-purple">{{$pdfLoop->created_at->format('d/M')}}</span>
                                                                </div>
                                                    </li>
                                                    </div>
                                                </div>
                                                    @endif
                                                    @endforeach

                                                </ul>
                                            </div><!--end card-body-->
                                        </div>
                                        <div class="tab-pane p-3" id="docs" role="tabpanel">
                                            <div class="card-body pt-0">
                                                <ul class="list-group wallet-bal-crypto">

                                                    @foreach ($DBtitles as $pdfLoop)
                                                        @if($pdfLoop->type=='DOCS')
                                                        <a href="{{$pdfLoop->fileUrl}}" target="_blank" >
                                                <div class="row ">
                                                    <div class="col-md-10">
                                                            <li class="list-group-item align-items-center d-flex justify-content-between">
                                                        <div class="media">
                                                            <img src="{{ URL::asset('assets/images/files logo/docs.png')}}" class="mr-3 thumb-sm align-self-center rounded-circle" alt="...">
                                                            <div class="media-body align-self-center">
                                                                <div class="coin-bal">
                                                                    @php
                                                                        $filename=basename($pdfLoop->fileUrl);
                                                                        $fileSizes=intval(($pdfLoop->fileSize)/1000);

                                                                    @endphp
                                                                    <h3 class="m-0">{{$filename}}</h3>
                                                                    <p class="text-muted mb-0">Size - {{$fileSizes}}KB</p>
                                                                </div>
                                                            </div><!--end media body-->
                                                        </div>
                                                    </a>
                                                                <div class=" ml-3 align-items-start">
                                                                    <span ><a href="/teacher/edit_classwork/{{$pdfLoop->id}}" class="mr-2 "><i class="fas fa-edit text-info font-16"></i></a></span>
                                                                    <span> <a onclick="return confirm('Are you sure want to delete?')" href="/teacher/classroom/{{$pdfLoop->id}}/delete"><i class="fas fa-trash-alt text-danger font-16"></i></a></span>
                                                                    <span class="mt-3 badge badge-soft-purple">{{$pdfLoop->created_at->format('d/M')}}</span>
                                                                </div>
                                                    </li>
                                                    </div>
                                                </div>
                                                    @endif
                                                    @endforeach

                                                </ul>
                                            </div><!--end card-body-->
                                        </div>

                                        <div class="tab-pane p-3" id="youtube" role="tabpanel">
                                            <div class="card-body pt-0">
                                                <ul class="list-group wallet-bal-crypto">
                                                   @php
                                                       $i=1;
                                                   @endphp
                                                    @foreach ($DBtitles as $pdfLoop)
                                                        @if($pdfLoop->type=='YOUTUBE')
                                                        <a href="{{$pdfLoop->youtubeLink}}" target="_blank" >
                                                <div class="row ">
                                                    <div class="col-md-10">
                                                            <li class="list-group-item align-items-center d-flex justify-content-between">
                                                        <div class="media">
                                                            <img src="{{ URL::asset('assets/images/files logo/youtubeNew.png')}}" class="mr-3 thumb-sm align-self-center rounded-circle" alt="...">
                                                            <div class="media-body align-self-center">
                                                                <div class="coin-bal">

                                                                <h3 class="m-0">{{$i}}. Click to view video</h3>
                                                                </div>
                                                            </div><!--end media body-->
                                                        </div>
                                                    </a>
                                                                <div class=" ml-3 align-items-start">
                                                                    <span ><a href="/teacher/edit_classwork/{{$pdfLoop->id}}" class="mr-2 "><i class="fas fa-edit text-info font-16"></i></a></span>
                                                                    <span> <a  onclick="return confirm('Are you sure want to delete?')" href="/teacher/classroom/{{$pdfLoop->id}}/delete"><i class="fas fa-trash-alt text-danger font-16"></i></a></span>
                                                                    <span class="mt-3 badge badge-soft-purple">{{$pdfLoop->created_at->format('d/M')}}</span>
                                                                </div>
                                                    </li>
                                                    </div>
                                                </div>
                                                    @endif
                                                    @php
                                                        $i=$i+1;
                                                    @endphp
                                                    @endforeach

                                                </ul>
                                            </div><!--end card-body-->
                                        </div>
                                    </div>

                    </div><!--end row-->

                            </div><!--end card-body-->
                        </div><!--end card-->



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

@stop
