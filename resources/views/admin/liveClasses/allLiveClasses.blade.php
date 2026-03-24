@extends('layouts.admin_analytics-master')

@section('headerStyle')
<link href="{{ URL::asset('plugins/footable/css/footable.bootstrap.css')}}" rel="stylesheet" type="text/css">
@stop



@section('content')

<!-- Flash Messages -->
@include('layouts.partials.flash-messages')

<div class="row m-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">

                <h4 class="mt-0 header-title">All Classes</h4>
                <p class="text-muted mb-3">You can view or edit live classes and their schedule.
                </p>

                <div class="table-responsive">
                    <table class="table mb-0 table-centered">
                        <thead>
                        <tr>
                            <th>Class</th>
                            <th>Subject</th>
                            <th>Monday</th>
                            <th>Tuesday</th>
                            <th>Wednesday</th>
                            <th>Thursday</th>
                            <th>Friday</th>
                            <th>Saturday</th>
                            <th>Sunday</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Link</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach ($subCodes as $subCode)

                        <tr>
                            <td>{{$subCode->class}}</td>
                            <td>{{$subCode->subject}}</td>
                            <td>{{$subCode->Monday}}</td>
                            <td>{{$subCode->Tuesday}}</td>
                            <td>{{$subCode->Wednesday}}</td>
                            <td>{{$subCode->Thursday}}</td>
                            <td>{{$subCode->Friday}}</td>
                            <td>{{$subCode->Saturday}}</td>
                            <td>{{$subCode->Sunday}}</td>
                            <td>{{date('H:i', strtotime($subCode->start_time))}}</td>
                            <td>{{date('H:i', strtotime($subCode->end_time))}}</td>
                            <td><a href="{{$subCode->link_url}}"> {{$subCode->link_url}}</a></td>
                            <td>
                            <a href="editLiveClass/{{$subCode->id}}"><i class="fas fa-edit text-info font-16"></i></a>

                            </td>
                        </tr>
                        @endforeach

                        </tbody>
                    </table><!--end /table-->
                </div><!--end /tableresponsive-->
            </div><!--end card-body-->
        </div><!--end card-->
    </div> <!-- end col -->
</div>

@endsection


@section('footerScript')

<script src="{{ URL::asset('plugins/footable/js/footable.js')}}"></script>
        <script src="{{ URL::asset('plugins/moment/moment.js')}}"></script>
        <script src="{{ URL::asset('assets/pages/jquery.footable.init.js')}}"></script>

@stop
