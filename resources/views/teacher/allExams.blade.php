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


    <!--Data table-->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <button onclick="window.location.href='/teacher/createExam'" class="btn btn-gradient-primary px-4 float-right mt-0 mb-3"><i class="mdi mdi-plus-circle-outline mr-2"></i>Add New Exam</button>
                <h4 class="header-title mt-0">All Exams</h4>
                    <div class="table-responsive dash-social">

                        <table id="datatable" class="table">
                            <thead class="thead-light">
                            <tr>
                                <th>Topic</th>
                                <th>Type</th>
                                <th> Start time</th>
                                <th>End time</th>
                                <th>Max Marks</th>
                                <th>Action</th>
                            </tr><!--end tr-->
                            </thead>

                            <tbody>
                                @foreach($exams as $exam)

                               @if($exam->type!="")
                            <tr>
                                <input type="hidden" class="delID" name="delID" id="delID" value="{{$exam->id}}">

                                <td>{{$exam->title}}</td>

                                @if($exam->type=='IMG')
                                    <td><a href="{{$exam->fileUrl}}" target="_blank" ><i class=" ti-image bg-soft-pink mr-2"></i></a></td>
                                @elseif($exam->type=='PDF')
                                    <td><a href="{{$exam->fileUrl}}" target="_blank" ><i class=" fas fa-file-pdf bg-soft-warning mr-2"></i></a></td>
                                @elseif($exam->type=='DOCS')
                                    <td><a href="{{$exam->fileUrl}}" target="_blank" ><i class="fas fa-file-word bg-soft-primary mr-2"></i></a></td>
                                @elseif($exam->type=='FORM')
                                    <td><a href="/teacher/formExam/{{$exam->id}}"><i class=" ti-file bg-soft-danger mr-2"></i></a></td>
                                @else
                                    <i class="mdi mdi-checkbox-marked-circle-outline bg-soft-success mr-2"></i>
                                @endif

                                @php
                                $fileSizes=intval(($exam->fileSize)/1000);

                              //  $filesizes = ($exam->fileSize)/1000000;
                            //    $filesize = number_format($filesizes, 3, '.', ',');
                                @endphp
                                    <td>{{date('d/M H:i', strtotime($exam->startExam))}}
                                    </td>
                                <td>{{date('d/M H:i', strtotime($exam->endExam))}}</td>
                                <td>{{$exam->maxMarks}}</td>
                                <td>
                                    <a href="/teacher/editExam/{{$exam->id}}" class="mr-2" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="Edit" data-trigger="hover"><i class="fas fa-edit text-info font-16"></i></a>
                                    <a onclick="return confirm('Are you sure want to delete?')" href="/teacher/deleteExam/{{$exam->id}}" class="delete" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="Delete" data-trigger="hover" ><i class="fas fa-trash-alt text-danger font-16"></i></a>
                                    <a href="/teacher/classworkAttendence/{{$exam->id}}" class="mr-2" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="Attendence" data-trigger="hover"><i class="fas fa-address-card text-info font-16"></i></a>
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
