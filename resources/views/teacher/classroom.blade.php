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

<div class="container-fluid mt-3">
    <!-- Page-Title -->
    <div class="row">
            <div class="col-lg-4">
            <h3>{{$class}} - {{$subject}}</h3>
                @php
                    $titles[]=Null;


                @endphp
                @foreach ($classDatas as $classData)
                @if(!in_array($classData->title, $titles))
                @php
                        $titles[] = $classData->title;
                        $img=0;
                    $pdf=0;
                    $doc=0;
                    $ytb=0;
                foreach($classDatas as $classData_title){
                    if($classData->title==$classData_title->title){
                        if($classData_title->type=='IMG'){
                            $img=$img+1;
                        }
                        if($classData_title->type=='PDF'){
                            $pdf=$pdf+1;
                        }
                        if($classData_title->type=='DOCS'){
                            $doc=$doc+1;
                        }
                        if($classData_title->type=='YOUTUBE'){
                            $ytb=$ytb+1;
                        }
                    }
                            # code...
                            }
                    @endphp
                 <a href="/teacher/inner_classroom/{{$classData->id}}">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="mt-0 header-title">{{$classData->title}}</h4>
                            <div class="img-group">
                                    <img src="{{ URL::asset('assets/images/files logo/download.jpeg')}}" alt="user" class="rounded-circle thumb-sm">
                                <span class="avatar-title bg-soft-danger rounded-circle font-13 mr-2">{{$pdf}}</span>
                                    <img src="{{ URL::asset('assets/images/files logo/jpeg.jpeg')}}" alt="user" class="rounded-circle thumb-sm">
                                <span class="avatar-title bg-soft-danger rounded-circle font-13 mr-2">{{$img}}</span>
                                    <img src="{{ URL::asset('assets/images/files logo/docs.png')}}" alt="user" class="rounded-circle thumb-sm">
                                <span class="avatar-title bg-soft-danger rounded-circle font-13 mr-2">{{$doc}}</span>
                                    <img src="{{ URL::asset('assets/images/files logo/youtube.png')}}" alt="user" class="rounded-circle thumb-sm">
                                <span class="avatar-title bg-soft-danger rounded-circle font-13 ">{{$ytb}}</span>
                            </div><!--end img-group-->
                        </div><!--end card-body-->
                    </div><!--end card-->
                </a>
                @endif
                @endforeach
        </div>
    </div>

<div class="container-fluid mt-3">
    <!-- Page-Title -->


    <!--Data table-->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <button onclick="window.location.href='/teacher/addMaterial/{{$id}}'" class="btn btn-gradient-primary px-4 float-right mt-0 mb-3"><i class="mdi mdi-plus-circle-outline mr-2"></i>Add New Work</button>
                <h4 class="header-title mt-0">All {{$class}} class {{$subject}} classwork</h4>
                    <div class="table-responsive dash-social">

                        <table id="datatable" class="table">
                            <thead class="thead-light">
                            <tr>
                                <th>Topic</th>
                                <th>Type</th>
                                <th> Size</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr><!--end tr-->
                            </thead>

                            <tbody>
                                @foreach($classDatas as $classData)

                               @if($classData->type!="")
                            <tr>
                                <input type="hidden" class="delID" name="delID" id="delID" value="{{$classData->id}}">

                                <td>{{$classData->title}}</td>

                                @if($classData->type=='IMG')
                                    <td><a href="{{$classData->fileUrl}}" target="_blank" ><i class=" ti-image bg-soft-pink mr-2"></i></a></td>
                                @elseif($classData->type=='PDF')
                                    <td><a href="{{$classData->fileUrl}}" target="_blank" ><i class=" fas fa-file-pdf bg-soft-warning mr-2"></i></a></td>
                                @elseif($classData->type=='DOCS')
                                    <td><a href="{{$classData->fileUrl}}" target="_blank" ><i class="fas fa-file-word bg-soft-primary mr-2"></i></a></td>
                                @elseif($classData->type=='YOUTUBE')
                                    <td><a href="{{$classData->youtubeLink}}" target="_blank" ><i class=" ti-youtube bg-soft-danger mr-2"></i></a></td>
                                @elseif($classData->type=='TOPIC')
                                    <td>Topic</td>
                                @else
                                    <i class="mdi mdi-checkbox-marked-circle-outline bg-soft-success mr-2"></i>
                                @endif

                                @php
                                $fileSizes=intval(($classData->fileSize)/1000);

                              //  $filesizes = ($classData->fileSize)/1000000;
                            //    $filesize = number_format($filesizes, 3, '.', ',');
                                @endphp
                                    <td> @if($classData->type!="YOUTUBE") {{$fileSizes}}KB @endif
                                    </td>
                                <td>{{$classData->created_at->format('d/M')}}</td>

                                <td>
                                    <a href="/teacher/edit_classwork/{{$classData->id}}" class="mr-1" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="Edit" data-trigger="hover"><i class="fas fa-edit text-info font-16"></i></a>
                                    <a href="/teacher/classworkAttendence/{{$classData->id}}" class="mr-1" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="Attendence" data-trigger="hover"><i class="fas fa-address-card text-info font-16"></i></a>
                                    <a href="/teacher/studentReturnWork/{{$classData->id}}" class="mr-1" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="Student work" data-trigger="hover"><i class="fas fa-reply text-info font-16"></i></a>
                                    <a onclick="return confirm('Are you sure want to delete?')" href="/teacher/classroom/{{$classData->id}}/delete"><i class="fas fa-trash-alt text-danger font-16"></i></a>
                                </td>
                            </tr><!--end tr-->
                            @endif
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                </div><!--end card-body-->
            </div><!--end card-->
        </div> <!--end col-->
    </div><!--end row-->
@endsection


@section('footerScript')
  <!-- Required datatable js -->
        <script src="{{ URL::asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ URL::asset('plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ URL::asset('plugins/peity-chart/jquery.peity.min.js') }}"></script>
        <script src="{{ URL::asset('assets/pages/jquery.analytics_customers.init.js') }}"></script>



@stop
