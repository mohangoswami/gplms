@extends('layouts.admin_analytics-master')



@section('content')

<!-- Flash Messages -->
@include('layouts.partials.flash-messages')

<div class="row">
<div class="col-lg-6">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                        <h2>Create News</h2>

                            <form method="POST" action="{{ route('admin.postFlashNews') }}" enctype="multipart/form-data">
                                @csrf

                              <div >


                                <div class="form-group">
                                    <label for="pdfUpload">Create flash news</label>
                                    <input name="inputNews" class="form-control" type="text" placeholder="Enter news here" id="inputNews" required>
                                </div>

                                <button type="submit"  class="btn btn-gradient-primary">Submit</button>
                                <button type="button" class="btn btn-gradient-danger">Cancel</button>
                            </form>


                        </div><!--end card-body-->
                    </div><!--end card-->
                </div><!--end col-->
            </div>

        </div><!--end card-body-->
    </div><!--end card-->
</div><!--end col-->
</div><!--end row-->
@stop
