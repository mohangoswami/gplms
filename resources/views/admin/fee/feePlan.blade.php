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

<!-- Flash Messages -->
@include('layouts.partials.flash-messages')


<div class="row">
    <div class="col-lg-6">
        <div class="card m-2">
            <div class="card-body">
                <h4 class="mt-0 header-title">Create New Fees Plan</h4>
                <p class="text-muted mb-3">Create New Fees Plan </p>
                <form action="/fee/post_feePlan" method="post"  enctype="multipart/form-data" >
                    @csrf
                    <div class="form-group">
                        <label for="lable_subject">Select Fee Heading</label>
                        <select id="feeHead" name="feeHead" class="custom-select">
                            @foreach ($feeHeads as $feeHead)
                            <option value="{{$feeHead->id}}">{{$feeHead->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="lable_subject">Category</label>
                        <select id="category[]" name="category[]" class="form-control select2 mb-3 select2-multiple" style="width: 100%" multiple="multiple" data-placeholder="Choose Category ">
                            @foreach($categories as $category)
                        <option value="{{$category->category}}">{{$category->category}} </option>
                           @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="lable_subject">Classes</label>
                        <select id="class[]" name="class[]" class="form-control select2 mb-3 select2-multiple" style="width: 100%" multiple="multiple" data-placeholder="Choose Class ">
                            @foreach($classes as $class)
                        <option value="{{$class->class}}">{{$class->class}} </option>
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


<div class="row m-2">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h4 class="mt-0 header-title">Fees Plan</h4>
                <p class="text-muted mb-3">You can view or edit Fees Plan.
                </p>

                <div class="table-responsive">
                    <table id="datatable" class="table">
                        <thead class="thead-light">
                        <tr>
                            <th>class</th>
                            <th>Category</th>
                            <th>Fee Haed</th>
                            <th>value</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($feePlans as $feePlan)
                        <tr>
                            <td>{{$feePlan->class}}</td>
                            <td>{{$feePlan->category}}</td>
                            <td>{{$feePlan->feeHead->name}}</td>
                            <td>{{$feePlan->value}}</td>
                            <td>
                            <a href="editFeePlan/{{$feePlan->id}}"><i class="fas fa-edit text-info font-16"></i></a>/
                            <a onclick="return confirm('Are you sure want to delete?')" href="deleteFeePlan/{{$feePlan->id}}"><i class="fas fa-trash-alt text-danger font-16"></i></a>

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
