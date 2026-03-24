@extends('layouts.admin_analytics-master')


@section('headerStyle')

        <!-- Plugins css -->
<link href="{{ URL::asset('plugins/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />

<!-- DataTables -->
<link href="{{ URL::asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Responsive datatable examples -->
<link href="{{ URL::asset('plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@stop


@section('content')
@if (session('status'))
    <div class="alert alert-success b-round mt-3 ">
        {{ session('status') }}
    </div>
@endif
@if (session('failed'))
<div class="alert alert-danger b-round  mt-3 ">
    {{ session('failed') }}
</div>
@endif
@if (session('delete'))
<div class="alert alert-warning b-round  mt-3">
    {{ session('delete') }}
</div>
@endif
<div class="row">
    <div class="col-lg-6">
        <div class="card m-5">
            <div class="card-body">
                <h4 class="mt-0 header-title">Create New Route Fees Plan</h4>
                <p class="text-muted mb-3">Create New Route Fees Plan </p>
                <form action="/fee/post_routeFeePlan" method="post"  enctype="multipart/form-data" >
                    @csrf
                    <div class="form-group">
                        <label for="lable_subject">Select Route</label>
                        <select id="route" name="route" class="custom-select">
                            @foreach ($routeNames as $route)
                            <option value="{{$route->routeName}}">{{$route->routeName}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="lable_class">Value</label>
                        <input class="form-control" type="number" placeholder="Enter Category Name" id="value" name="value" required>
                    </div>
                    <button type="submit" class="btn btn-gradient-primary">Create</button>
                    <button type="button" class="btn btn-gradient-danger">Cancel</button>
                </form>
            </div><!--end card-body-->
        </div><!--end card-->
    </div><!--end col-->
</div>


<div class="row m-3">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h4 class="mt-0 header-title">Route Fees Plan</h4>
                <p class="text-muted mb-3">You can view or edit Route Fees Plan.
                </p>

                <div class="table-responsive">
                    <table id="datatable" class="table">
                        <thead class="thead-light">
                        <tr>
                            <th>Route</th>
                            <th>Value</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($routeFeePlans as $route)
                        <tr>
                            <td>{{$route->routeName}}</td>
                            <td>{{$route->value}}</td>
                            <td>
                            <a href="editRouteFeePlan/{{$route->id}}"><i class="fas fa-edit text-info font-16"></i></a>/
                            <a onclick="return confirm('Are you sure want to delete?')" href="deleteRouteFeePlan/{{$route->id}}"><i class="fas fa-trash-alt text-danger font-16"></i></a>

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
<!-- Plugins js -->
        <script src="{{ URL::asset('plugins/moment/moment.js')}}"></script>
        <script src="{{ URL::asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
        <script src="{{ URL::asset('plugins/select2/select2.min.js')}}"></script>
        <script src="{{ URL::asset('assets/pages/jquery.forms-advanced.js')}}"></script>
 <!-- Required datatable js -->
 <script src="{{ URL::asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
 <script src="{{ URL::asset('plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
 <script src="{{ URL::asset('plugins/peity-chart/jquery.peity.min.js') }}"></script>
 <script src="{{ URL::asset('assets/pages/jquery.analytics_customers.init.js') }}"></script>

@stop
