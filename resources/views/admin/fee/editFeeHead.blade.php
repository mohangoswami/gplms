@extends('layouts.admin_analytics-master')



@section('content')

<!-- Flash Messages -->
@include('layouts.partials.flash-messages')


<div class="row">
    <div class="col-lg-6">
        <div class="card m-5">
            <div class="card-body">
                <h4 class="mt-0 header-title">Edit Fee Haed</h4>
                <p class="text-muted mb-3">Edit Fee Headings </p>
                @foreach ($feeHeads as $feeHead)
                <form action="/fee/editFeeHead" method="post"  enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{$id}}">
                    <div class="form-group">
                        <label for="lable_class">Name of Head</label>
                        <input class="form-control" type="text" placeholder="Enter name" id="name" name="name" value="{{$feeHead->name}}" required>
                    </div>

                    <div class="form-group">
                        <label for="lable_subject">Account Name</label>
                        <select id="accountName" name="accountName" class="custom-select">
                            <option value="{{$feeHead->accountName}}">{{$feeHead->accountName}}</option>
                            <option value="TUITION FEE">Tuition Fee</option>
                            <option value="ANNUAL FEE">Annual Fee</option>
                            <option value="EXAMINATION FEE">Examination Fee</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="lable_subject">Frequency</label>
                        <select id="frequency" name="frequency" class="custom-select">
                            <option value="{{$feeHead->frequency}}">{{$feeHead->frequency}}</option>
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
                                    <input type="checkbox" class="custom-control-input" id="january"  name = "january" @if($feeHead->jan==1) checked @endif  data-parsley-multiple="groups" data-parsley-mincheck="2">
                                    <label class="custom-control-label" for="january">Jan</label>
                                </div>
                            </div>
                            <div class="form-check-inline my-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="february" name = "february" @if($feeHead->feb==1) checked @endif  data-parsley-multiple="groups" data-parsley-mincheck="2">
                                    <label class="custom-control-label" for="february">Feb</label>
                                </div>
                            </div>
                            <div class="form-check-inline my-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="march"  name = "march" @if($feeHead->mar==1) checked @endif  data-parsley-multiple="groups" data-parsley-mincheck="2">
                                    <label class="custom-control-label" for="march">Mar</label>
                                </div>
                            </div>
                            <div class="form-check-inline my-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="april"  name = "april" @if($feeHead->apr==1) checked @endif  data-parsley-multiple="groups" data-parsley-mincheck="2">
                                    <label class="custom-control-label" for="april">Apr</label>
                                </div>
                            </div>
                            <div class="form-check-inline my-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="may" name = "may" @if($feeHead->may==1) checked @endif  data-parsley-multiple="groups" data-parsley-mincheck="2">
                                    <label class="custom-control-label" for="may">May</label>
                                </div>
                            </div>
                            <div class="form-check-inline my-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="june"  name = "june" @if($feeHead->jun==1) checked @endif  data-parsley-multiple="groups" data-parsley-mincheck="2">
                                    <label class="custom-control-label" for="june">Jun</label>
                                </div>
                            </div>
                            <div class="form-check-inline my-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="july" name = "july" @if($feeHead->jul==1) checked @endif  data-parsley-multiple="groups" data-parsley-mincheck="2">
                                    <label class="custom-control-label" for="july">Jul</label>
                                </div>
                            </div>
                            <div class="form-check-inline my-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="august"  name = "august" @if($feeHead->aug==1) checked @endif  data-parsley-multiple="groups" data-parsley-mincheck="2">
                                    <label class="custom-control-label" for="august">Aug</label>
                                </div>
                            </div>
                            <div class="form-check-inline my-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="september"  name = "september" @if($feeHead->sep==1) checked @endif  data-parsley-multiple="groups" data-parsley-mincheck="2">
                                    <label class="custom-control-label" for="september">Sep</label>
                                </div>
                            </div>
                            <div class="form-check-inline my-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="october" name = "october" @if($feeHead->oct==1) checked @endif  data-parsley-multiple="groups" data-parsley-mincheck="2">
                                    <label class="custom-control-label" for="october">Oct</label>
                                </div>
                            </div>
                            <div class="form-check-inline my-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="november"  name = "november" @if($feeHead->nov==1) checked @endif  data-parsley-multiple="groups" data-parsley-mincheck="2">
                                    <label class="custom-control-label" for="november">Nov</label>
                                </div>
                            </div>
                            <div class="form-check-inline my-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="december" name = "december" @if($feeHead->dec==1) checked @endif  data-parsley-multiple="groups" data-parsley-mincheck="2">
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
