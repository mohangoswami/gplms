@extends('layouts.admin_analytics-master')

@section('headerStyle')
<link href="{{ URL::asset('plugins/footable/css/footable.bootstrap.css')}}" rel="stylesheet" type="text/css">

<!-- DataTables -->
<link href="{{ URL::asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Responsive datatable examples -->
<link href="{{ URL::asset('plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('content')

<!-- Flash Messages -->
@include('layouts.partials.flash-messages')

<div class="row m-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">

                <h4 class="mt-0 header-title">Date wise Teachers attendance</h4>
                <p class="text-muted mb-3">You can view Teachers attendance ..
                    <button onclick="window.location.href='/admin/teacher/dayswiseAttendance'" class="btn btn-gradient-primary px-4 float-right mt-0 mb-3">View All</button>

                </p>

                <div class="table-responsive">
                    <table id="datatable" class="table">
                        <thead class="thead-light">
                        <tr>
                            <th>S.No.</th>
                            <th>Date</th>
                            <th>Time 1</th>
                            <th>Time 2</th>
                            <th>Name</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $i=1;
                        @endphp
                        @foreach ($attendances as $attendance)
                        @php
                            $date = date('d/M/y', strtotime($attendance->att0));
                        @endphp
                        @endforeach
                        @foreach ($absents as $absent)

                        <tr class="text-danger">
                            <td>{{$i}}</td>
                            <td >{{$date}}</a></td>
                            <td >Absent</td>
                            <td >Absent</td>
                            <td>{{$absent->name}}</td>

                            </td>

                        </tr>
                        @php
                            $i=$i+1;
                        @endphp
                        @endforeach

                        @foreach ($attendances as $attendance)

                        <tr class="text-primary">
                            <td>{{$i}}</td>
                            <td>{{$date}}</a></td>
                            <td>@if(isset($attendance->att0)){{date('H:i', strtotime($attendance->att0))}}@endif</td>
                            <td>@if(isset($attendance->att1)){{date('H:i', strtotime($attendance->att1))}}@endif</td>
                            <td>{{$attendance->name}}</td>

                            </td>

                        </tr>
                        @php
                            $i=$i+1;
                        @endphp
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
         <!-- Required datatable js -->
         <script src="{{ URL::asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
         <script src="{{ URL::asset('plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
         <script src="{{ URL::asset('plugins/peity-chart/jquery.peity.min.js') }}"></script>
         <script src="{{ URL::asset('assets/pages/jquery.analytics_customers.init.js') }}"></script>
@stop
