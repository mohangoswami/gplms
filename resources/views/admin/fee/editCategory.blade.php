@extends('layouts.admin_analytics-master')



@section('content')

<!-- Flash Messages -->
@include('layouts.partials.flash-messages')


<div class="row">
    <div class="col-lg-6">
        <div class="card m-5">
            <div class="card-body">
                <h4 class="mt-0 header-title">Edit Category</h4>
                <p class="text-muted mb-3">Edit Category </p>
                @foreach ($categories as $category)
                <form action="/fee/editCategory" method="post"  enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{$id}}">
                    <div class="form-group">
                        <label for="lable_class">Name of Category</label>
                        <input class="form-control" type="text" placeholder="Enter name" id="category" name="category" value="{{$category->category}}" required>
                    </div>

                    <button type="submit" class="btn btn-gradient-primary">Edit</button>
                    <button type="button" class="btn btn-gradient-danger">Cancel</button>
                </form>
                @endforeach
            </div><!--end card-body-->
        </div><!--end card-->
    </div><!--end col-->
</div>
@endsection
