@extends('layouts.admin_analytics-master')

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

 <!--Data table-->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                <h4 class="header-title mt-0">All classwork</h4>
                    <div class="table-responsive dash-social">

                        <table id="datatable" class="table">
                            <thead class="thead-light">
                            <tr>
                                <th>Teacher Name</th>
                                <th>Class</th>
                                <th> Subject</th>
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
                                <td>{{$classData->name}}</td>
                                <td>{{$classData->class}}</td>
                                <td>{{$classData->subject}}</td>
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
                                    <a href="/admin/edit_classwork/{{$classData->id}}" class="mr-1" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="Edit" data-trigger="hover"><i class="fas fa-edit text-info font-16"></i></a>
                                    <a href="/admin/classworkAttendence/{{$classData->id}}" class="mr-1" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="Attendence" data-trigger="hover"><i class="fas fa-address-card text-info font-16"></i></a>
                                    <a href="/admin/studentReturnWork/{{$classData->id}}" class="mr-1" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="Student work" data-trigger="hover"><i class="fas fa-reply text-info font-16"></i></a>
                                    <a onclick="return confirm('Are you sure want to delete?')" href="/admin/classroom/{{$classData->id}}/delete"><i class="fas fa-trash-alt text-danger font-16"></i></a>
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
