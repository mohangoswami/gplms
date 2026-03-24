@extends('layouts.admin_analytics-master')



@section('content')

<!-- Flash Messages -->
@include('layouts.partials.flash-messages')


<div class="row">
    <div class="col-lg-6">
        <div class="card m-5">
            <div class="card-body">
                <h4 class="mt-0 header-title">Create New Category</h4>
                <p class="text-muted mb-3">Create New Fee Category </p>
                <form action="/fee/addCategory" method="post"  enctype="multipart/form-data" >
                    @csrf
                    <div class="form-group">
                        <label for="lable_class">Name of Category</label>
                        <input class="form-control" type="text" placeholder="Enter Category Name" id="category" name="category" required>
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
                <h4 class="mt-0 header-title">Category</h4>
                <p class="text-muted mb-3">You can view or edit Category.
                </p>

                <div class="table-responsive">
                    <table id="datatable" class="table">
                        <thead class="thead-light">
                        <tr>
                            <th>Id</th>
                            <th>Category</th>

                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach ($categories as $category)

                        <tr>
                            <td>{{$category->id}}</td>
                            <td>{{$category->category}}</td>

                            <td>
                            <a href="editCategory/{{$category->id}}"><i class="fas fa-edit text-info font-16"></i></a>/
                            <a onclick="return confirm('Are you sure want to delete?')" href="deleteCategory/{{$category->id}}"><i class="fas fa-trash-alt text-danger font-16"></i></a>

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
