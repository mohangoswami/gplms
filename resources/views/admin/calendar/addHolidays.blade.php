@extends('layouts.admin_analytics-master')

@section('headerStyle')

<!--Datetime picker-->
<link href="{{ URL::asset('plugins/daterangepicker/daterangepicker.css')}}" rel="stylesheet" />
<link href="{{ URL::asset('plugins/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('plugins/timepicker/bootstrap-material-datetimepicker.css')}}" rel="stylesheet">
<link href="{{ URL::asset('plugins/bootstrap-touchspin/css/jquery.bootstrap-touchspin.min.css')}}" rel="stylesheet" />
@stop

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
                        <h2>Add Holidays</h2>

                            <p class="text-muted mb-3">Add Holidays for attendance.</p>
                            <form method="POST" action="{{ route('admin.postHolidays') }}" enctype="multipart/form-data">
                                @csrf

                              <div >


                                <div class="form-group">
                                    <label for="title">Title</label>
                                    <input name="title" class="form-control" type="text" placeholder="Enter title here" id="title" required>
                                </div>
                                <label class="mb-3">  Start and End Date</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="datetimes">

                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="dripicons-calendar"></i></span>
                                    </div>
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

@section('footerScript')

<!--Datetime picker-->
<script src="{{ URL::asset('plugins/moment/moment.js')}}"></script>
<script src="{{ URL::asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{ URL::asset('plugins/select2/select2.min.js')}}"></script>
<script src="{{ URL::asset('plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js')}}"></script>
<script src="{{ URL::asset('plugins/timepicker/bootstrap-material-datetimepicker.js')}}"></script>
<script src="{{ URL::asset('plugins/bootstrap-maxlength/bootstrap-maxlength.min.js')}}"></script>
<script src="{{ URL::asset('plugins/bootstrap-touchspin/js/jquery.bootstrap-touchspin.min.js')}}"></script>
<script src="{{ URL::asset('assets/pages/jquery.forms-advanced.js')}}"></script>


@stop
