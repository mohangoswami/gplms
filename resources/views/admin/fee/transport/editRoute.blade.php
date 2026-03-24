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
                <h4 class="mt-0 header-title">Edit Fee Haed</h4>
                <p class="text-muted mb-3">Edit Fee Headings </p>
                @foreach ($routeNames as $routeName)
                <form action="/fee/editRoute" method="post"  enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{$id}}">
                    <div class="form-group">
                        <label for="lable_class">Name of Head</label>
                        <input class="form-control" type="text" placeholder="Enter name" id="routeName" name="routeName" value="{{$routeName->routeName}}" required>
                    </div>

                    <div class="form-group">
                        <label for="lable_subject">Frequency</label>
                        <select id="frequency" name="frequency" class="custom-select">
                            <option value="{{$routeName->frequency}}">{{$routeName->frequency}}</option>
                            <option value="MONTHLY">Monthly</option>
                            <option value="QUATERLY">Quaterly</option>
                            <option value="HALFYEARLY">Half Yearly</option>
                            <option value="YEARLY">Yearly</option>
                        </select>
                    </div>

                    <div class="form-group mb-0 row">

                        <div class="col-md-8">

                            <div class="form-check-inline my-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="january"  name = "january" @if($routeName->jan==1) checked @endif  data-parsley-multiple="groups" data-parsley-mincheck="2">
                                    <label class="custom-control-label" for="january">Jan</label>
                                </div>
                            </div>
                            <div class="form-check-inline my-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="february" name = "february" @if($routeName->feb==1) checked @endif  data-parsley-multiple="groups" data-parsley-mincheck="2">
                                    <label class="custom-control-label" for="february">Feb</label>
                                </div>
                            </div>
                            <div class="form-check-inline my-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="march"  name = "march" @if($routeName->mar==1) checked @endif  data-parsley-multiple="groups" data-parsley-mincheck="2">
                                    <label class="custom-control-label" for="march">Mar</label>
                                </div>
                            </div>
                            <div class="form-check-inline my-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="april"  name = "april" @if($routeName->apr==1) checked @endif  data-parsley-multiple="groups" data-parsley-mincheck="2">
                                    <label class="custom-control-label" for="april">Apr</label>
                                </div>
                            </div>
                            <div class="form-check-inline my-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="may" name = "may" @if($routeName->may==1) checked @endif  data-parsley-multiple="groups" data-parsley-mincheck="2">
                                    <label class="custom-control-label" for="may">May</label>
                                </div>
                            </div>
                            <div class="form-check-inline my-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="june"  name = "june" @if($routeName->jun==1) checked @endif  data-parsley-multiple="groups" data-parsley-mincheck="2">
                                    <label class="custom-control-label" for="june">Jun</label>
                                </div>
                            </div>
                            <div class="form-check-inline my-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="july" name = "july" @if($routeName->jul==1) checked @endif  data-parsley-multiple="groups" data-parsley-mincheck="2">
                                    <label class="custom-control-label" for="july">Jul</label>
                                </div>
                            </div>
                            <div class="form-check-inline my-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="august"  name = "august" @if($routeName->aug==1) checked @endif  data-parsley-multiple="groups" data-parsley-mincheck="2">
                                    <label class="custom-control-label" for="august">Aug</label>
                                </div>
                            </div>
                            <div class="form-check-inline my-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="september"  name = "september" @if($routeName->sep==1) checked @endif  data-parsley-multiple="groups" data-parsley-mincheck="2">
                                    <label class="custom-control-label" for="september">Sep</label>
                                </div>
                            </div>
                            <div class="form-check-inline my-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="october" name = "october" @if($routeName->oct==1) checked @endif  data-parsley-multiple="groups" data-parsley-mincheck="2">
                                    <label class="custom-control-label" for="october">Oct</label>
                                </div>
                            </div>
                            <div class="form-check-inline my-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="november"  name = "november" @if($routeName->nov==1) checked @endif  data-parsley-multiple="groups" data-parsley-mincheck="2">
                                    <label class="custom-control-label" for="november">Nov</label>
                                </div>
                            </div>
                            <div class="form-check-inline my-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="december" name = "december" @if($routeName->dec==1) checked @endif  data-parsley-multiple="groups" data-parsley-mincheck="2">
                                    <label class="custom-control-label" for="december">Dec</label>
                                </div>
                            </div>
                        </div>
                    </div><!--end row-->


                    <button type="submit" class="btn btn-gradient-primary">Edit</button>
                    <button type="button" class="btn btn-gradient-danger">Cancel</button>
                </form>
                @endforeach
            </div><!--end card-body-->
        </div><!--end card-->
    </div><!--end col-->
</div>
@endsection
