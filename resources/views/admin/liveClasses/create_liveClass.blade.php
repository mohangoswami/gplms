@extends('layouts.admin_analytics-master')

@section('headerStyle')
<link href="{{ URL::asset('plugins/footable/css/footable.bootstrap.css')}}" rel="stylesheet" type="text/css">
@stop

@section('content')

<!-- Flash Messages -->
@include('layouts.partials.flash-messages')

<div class="row">
    <div class="col-lg-6">
        <div class="card m-5">
            <div class="card-body">
                <h4 class="mt-0 header-title">Create Class & Subject</h4>
                <p class="text-muted mb-3">Enter class and subject name <br>(class name type must be same for all same classes) </p>
                <form action="create_liveClass" method="post" >
                    @csrf
                    <div class="col-md-6">
                        <label class="">Select Class</label>
                        <select id="selectClass" name="selectClass"  class="select2 form-control mb-3 custom-select" style="width: 100%; height:36px;">
                            @isset($subCodes)
                            @foreach($subCodes as $subCode)
                            <option value="{{$subCode->id}}">{{$subCode->class}} -  {{$subCode->subject}} </option>
                            @endforeach
                            @endisset
                        </select>
                    </div><!-- end col -->
                    <div class="col-md-6">
                        <label class="my-3">Start time</label>
                                        <input class="form-control" type="time"  id="startTime" name="startTime" placeholder="Start time">
                    </div><!-- end col -->
                    <div class="col-md-6">
                        <label class="my-3">End time</label>
                                        <input class="form-control"  type="time"  id="endTime" name="endTime" placeholder="End time">
                    </div><!-- end col -->
                    <div>
                        <label class="col-md-3 my-3 control-label">Select Days</label>
                    </div>
                    <div class="form-group mb-0 row">

                        <div class="col-md-8">

                            <div class="form-check-inline my-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="Monday"  name = "Monday"  data-parsley-multiple="groups" data-parsley-mincheck="2">
                                    <label class="custom-control-label" for="Monday">Mon</label>
                                </div>
                            </div>
                            <div class="form-check-inline my-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="Tuesday" name = "Tuesday"  data-parsley-multiple="groups" data-parsley-mincheck="2">
                                    <label class="custom-control-label" for="Tuesday">Tue</label>
                                </div>
                            </div>
                            <div class="form-check-inline my-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="Wednesday"  name = "Wednesday"  data-parsley-multiple="groups" data-parsley-mincheck="2">
                                    <label class="custom-control-label" for="Wednesday">Wed</label>
                                </div>
                            </div>
                            <div class="form-check-inline my-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="Thursday"  name = "Thursday"  data-parsley-multiple="groups" data-parsley-mincheck="2">
                                    <label class="custom-control-label" for="Thursday">Thu</label>
                                </div>
                            </div>
                            <div class="form-check-inline my-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="Friday" name = "Friday"  data-parsley-multiple="groups" data-parsley-mincheck="2">
                                    <label class="custom-control-label" for="Friday">Fri</label>
                                </div>
                            </div>
                            <div class="form-check-inline my-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="Saturday"  name = "Saturday"  data-parsley-multiple="groups" data-parsley-mincheck="2">
                                    <label class="custom-control-label" for="Saturday">Sat</label>
                                </div>
                            </div>
                            <div class="form-check-inline my-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="Sunday" name = "Sunday"  data-parsley-multiple="groups" data-parsley-mincheck="2">
                                    <label class="custom-control-label" for="Sunday">Sun</label>
                                </div>
                            </div>
                        </div>
                    </div><!--end row-->

                    <div class="form-group mt-2">
                        <label for="lable_link ">Paste Live Class Link </label>
                        <input class="form-control" type="text" placeholder="Paste Live Class Link" id="link" name="link">
                    </div>

                    <button type="submit" class="btn btn-gradient-primary">Create</button>
                    <button type="button" class="btn btn-gradient-danger">Cancel</button>
                </form>
            </div><!--end card-body-->
        </div><!--end card-->
    </div><!--end col-->
</div>


@endsection


@section('footerScript')

<script src="{{ URL::asset('plugins/footable/js/footable.js')}}"></script>
        <script src="{{ URL::asset('plugins/moment/moment.js')}}"></script>
        <script src="{{ URL::asset('assets/pages/jquery.footable.init.js')}}"></script>

@stop
