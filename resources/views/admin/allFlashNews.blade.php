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

<div class="container-fluid mt-3">
    <!-- Page-Title -->

    <!--Data table-->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <button onclick="window.location.href='/admin/createFlashNews'" class="btn btn-gradient-primary px-4 float-right mt-0 mb-3"><i class="mdi mdi-plus-circle-outline mr-2"></i>Add New News</button>
                <h4 class="header-title mt-0">All Flash News</h4>
                    <div class="table-responsive dash-social">

                        <table id="datatable" class="table">
                            <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Flash News</th>

                                <th>Action</th>
                            </tr><!--end tr-->
                            </thead>

                            <tbody>
                                @php
                                    $n=1;
                                @endphp
                                @foreach($flashNews as $news)

                               @if($news->news!="")
                            <tr>

                                <td>{{$n}}</td>
                                <td>{{$news->news}}</td>

                                <td>
                                    <a onclick="return confirm('Are you sure want to delete?')" href="/admin/deleteFlashNews/{{$news->id}}" class="delete" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="Delete" data-trigger="hover" ><i class="fas fa-trash-alt text-danger font-16"></i></a>
                                </td>
                            </tr><!--end tr-->
                            @endif
                            @php
                                $n=$n+1;
                            @endphp
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
