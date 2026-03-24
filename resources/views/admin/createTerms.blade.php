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
                        <h2>Create Terms</h2>

                            <p class="text-muted mb-3">---</p>
                            <form method="POST" action="{{ route('admin.createTerms') }}" enctype="multipart/form-data">
                                @csrf

                              <div >


                                <div class="form-group">
                                    <label for="pdfUpload">Create Terms</label>
                                    <input name="term" class="form-control" type="text" placeholder="Enter term" id="term" required>
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
