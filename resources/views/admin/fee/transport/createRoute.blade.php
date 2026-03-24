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
                <h4 class="mt-0 header-title">Create New Route</h4>
                <p class="text-muted mb-3">Create New Transport Route </p>
                <form action="post_createRoute" method="post" >
                    @csrf
                    <div class="form-group">
                        <label for="lable_class">Name of Route</label>
                        <input class="form-control" type="text" placeholder="Enter name" id="routeName" name="routeName" required>
                    </div>

                    <div class="form-group">
                        <label for="lable_subject">Frequency</label>
                        <select id="frequency" name="frequency" class="custom-select">
                            <option selected="">Open this select menu</option>
                            <option value="NA">N/A</option>
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
                                    <input type="checkbox" class="custom-control-input" id="january"  name = "january"  data-parsley-multiple="groups" data-parsley-mincheck="2">
                                    <label class="custom-control-label" for="january">Jan</label>
                                </div>
                            </div>
                            <div class="form-check-inline my-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="february" name = "february"  data-parsley-multiple="groups" data-parsley-mincheck="2">
                                    <label class="custom-control-label" for="february">Feb</label>
                                </div>
                            </div>
                            <div class="form-check-inline my-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="march"  name = "march"  data-parsley-multiple="groups" data-parsley-mincheck="2">
                                    <label class="custom-control-label" for="march">Mar</label>
                                </div>
                            </div>
                            <div class="form-check-inline my-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="april"  name = "april"  data-parsley-multiple="groups" data-parsley-mincheck="2">
                                    <label class="custom-control-label" for="april">Apr</label>
                                </div>
                            </div>
                            <div class="form-check-inline my-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="may" name = "may"  data-parsley-multiple="groups" data-parsley-mincheck="2">
                                    <label class="custom-control-label" for="may">May</label>
                                </div>
                            </div>
                            <div class="form-check-inline my-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="june"  name = "june"  data-parsley-multiple="groups" data-parsley-mincheck="2">
                                    <label class="custom-control-label" for="june">Jun</label>
                                </div>
                            </div>
                            <div class="form-check-inline my-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="july" name = "july"  data-parsley-multiple="groups" data-parsley-mincheck="2">
                                    <label class="custom-control-label" for="july">Jul</label>
                                </div>
                            </div>
                            <div class="form-check-inline my-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="august"  name = "august"  data-parsley-multiple="groups" data-parsley-mincheck="2">
                                    <label class="custom-control-label" for="august">Aug</label>
                                </div>
                            </div>
                            <div class="form-check-inline my-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="september"  name = "september"  data-parsley-multiple="groups" data-parsley-mincheck="2">
                                    <label class="custom-control-label" for="september">Sep</label>
                                </div>
                            </div>
                            <div class="form-check-inline my-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="october" name = "october"  data-parsley-multiple="groups" data-parsley-mincheck="2">
                                    <label class="custom-control-label" for="october">Oct</label>
                                </div>
                            </div>
                            <div class="form-check-inline my-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="november"  name = "november"  data-parsley-multiple="groups" data-parsley-mincheck="2">
                                    <label class="custom-control-label" for="november">Nov</label>
                                </div>
                            </div>
                            <div class="form-check-inline my-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="december" name = "december"  data-parsley-multiple="groups" data-parsley-mincheck="2">
                                    <label class="custom-control-label" for="december">Dec</label>
                                </div>
                            </div>
                        </div>
                    </div><!--end row-->


                    <button type="submit" class="btn btn-gradient-primary">Create</button>
                    <button type="button" class="btn btn-gradient-danger">Cancel</button>
                </form>
            </div><!--end card-body-->
        </div><!--end card-->
    </div><!--end col-->
</div>
@endsection
