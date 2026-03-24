@extends('layouts.admin_analytics-master')

@section('title', 'Metrica - Admin & Dashboard Template')


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
                @foreach ($attendances as $attendance)
                @php
                    $name = $attendance->name;
                @endphp
                @endforeach
                <button onclick="window.location.href='/admin/teacher/teachersAttendance'" class="btn btn-gradient-primary px-4 float-right mt-0 mb-3">Back</button>

                <h4 class="mt-0 header-title">{{$name}}</h4>
                <p class="text-muted mb-3">
                    <img src="{{ URL::asset('assets/images/teacherImg/' . $name . '.jpg')}}"  class="rounded-circle thumb-xl">

                </p>

                <div class="table-responsive">
                    <table id="datatable" class="table">
                        <thead class="thead-light">
                        <tr>
                            <th>S.No.</th>
                            <th>Name</th>
                            <th>Att 1</th>
                            <th>Att 2</th>
                            <th>Att 3</th>
                            <th>Att 4</th>
                            <th>Att 5</th>
                            <th>Att 6</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $i=1;
                        @endphp
                        @foreach ($attendances as $attendance)

                        <tr>
                            <td>{{$i}}</td>
                           <td>{{$attendance->name}}</td>
                            <td>
                                @if (isset($attendance->att0))
                                {{date('H:i d-M-y', strtotime($attendance->att0))}}
                                @endif
                            </td>
                            <td>
                                @if (isset($attendance->att1))
                                {{date('H:i d-M-y', strtotime($attendance->att1))}}
                                @endif
                            </td>
                            <td>
                                @if (isset($attendance->att2))
                                {{date('H:i d-M-y', strtotime($attendance->att2))}}
                                @endif
                            </td>
                            <td>
                                @if (isset($attendance->att3))
                                {{date('H:i d-M-y', strtotime($attendance->att3))}}
                                @endif
                            </td>
                            <td>
                                @if (isset($attendance->att4))
                                {{date('H:i d-M-y', strtotime($attendance->att4))}}
                                @endif
                            </td>
                            <td>
                                @if (isset($attendance->att5))
                                {{date('H:i d-M-y', strtotime($attendance->att5))}}
                                @endif
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
