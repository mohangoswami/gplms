@extends('layouts.admin_analytics-master')



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
                <h4 class="mt-0 header-title">Edit Route Fee Haed</h4>
                <p class="text-muted mb-3">Edit Route Fee Headings </p>
                <form action="/fee/editRouteFeePlan" method="post"  enctype="multipart/form-data">
                    @csrf
                    @foreach ($routeFeePlans as $routeFeePlan)
                    <input type="hidden" name="id" value="{{$id}}">
                    <div class="form-group">
                        <label for="lable_subject">Route Name</label>
                        <select id="routeName" name="routeName" class="custom-select">
                            <option value="{{$routeFeePlan->routeName}}">{{$routeFeePlan->routeName}}</option>
                            @foreach ($routeNames as $routeName)
                            <option value="{{$routeName->routeName}}">{{$routeName->routeName}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="lable_class">Value </label>
                        <input class="form-control" type="text" placeholder="Enter value" id="value" name="value" value="{{$routeFeePlan->value}}" required>
                    </div>
                    @endforeach
                    <button type="submit" class="btn btn-gradient-primary">Edit</button>
                    <button type="button" class="btn btn-gradient-danger">Cancel</button>
                </form>
            </div><!--end card-body-->
        </div><!--end card-->
    </div><!--end col-->
</div>
@endsection
