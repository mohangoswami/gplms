@extends('layouts.student_master')

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
            <h3>{{$subject}}</h3>
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
                 <a href="/student/inner_classroom/{{$classData->id}}">
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


@endsection


@section('footerScript')
  <!-- Required datatable js -->
        <script src="{{ URL::asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ URL::asset('plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ URL::asset('plugins/peity-chart/jquery.peity.min.js') }}"></script>
        <script src="{{ URL::asset('assets/pages/jquery.analytics_customers.init.js') }}"></script>

@stop
