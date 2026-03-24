@extends('layouts.admin_analytics-master')

@section('headerStyle')
<link href="{{ URL::asset('plugins/footable/css/footable.bootstrap.css')}}" rel="stylesheet" type="text/css">

<!-- DataTables -->
<link href="{{ URL::asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Responsive datatable examples -->
<link href="{{ URL::asset('plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@stop



@section('content')

<!-- Flash Messages -->
@include('layouts.partials.flash-messages')


<div class="row m-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <button onclick="window.location.href='/fee/createFeeHead'" class="btn btn-gradient-primary px-4 float-right mt-0 mb-3"><i class="mdi mdi-plus-circle-outline mr-2"></i>Add New Fee Head</button>
                <h4 class="mt-0 header-title">Fee Haedings</h4>
                <p class="text-muted mb-3">You can view or edit Fee Haedings.
                </p>

                <div class="table-responsive">
                    <table id="datatable" class="table">
                        <thead class="thead-light">
                        <tr>
                            <th>Id</th>
                            <th>Name</th>
                            <th>Account Name</th>
                            <th>Frequency</th>
                            <th>Jan</th>
                            <th>Feb</th>
                            <th>Mar</th>
                            <th>Apr</th>
                            <th>May</th>
                            <th>Jun</th>
                            <th>Jul</th>
                            <th>Aug</th>
                            <th>Sep</th>
                            <th>Oct</th>
                            <th>Nov</th>
                            <th>Dec</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach ($feeHeads as $feeHead)

                        <tr>
                            <td>{{$feeHead->id}}</td>
                            <td>{{$feeHead->name}}</td>
                            <td>{{$feeHead->accountName}}</td>
                            <td>{{$feeHead->frequency}}</td>
                            <td> <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="jan"  name = "jan" @if($feeHead->jan==1) checked @endif disabled data-parsley-multiple="groups" data-parsley-mincheck="2">
                                <label class="custom-control-label" for="jan"></label>
                            </div>
                            </td>
                            <td> <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="feb"  name = "feb" @if($feeHead->feb==1) checked @endif disabled data-parsley-multiple="groups" data-parsley-mincheck="2">
                                <label class="custom-control-label" for="feb"></label>
                            </div>
                            </td>
                            <td> <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="mar"  name = "mar" @if($feeHead->mar==1) checked @endif disabled data-parsley-multiple="groups" data-parsley-mincheck="2">
                                <label class="custom-control-label" for="mar"></label>
                            </div>
                            </td>
                            <td> <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="apr"  name = "apr" @if($feeHead->apr==1) checked @endif disabled data-parsley-multiple="groups" data-parsley-mincheck="2">
                                <label class="custom-control-label" for="apr"></label>
                            </div>
                            </td>
                            <td> <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="may"  name = "may" @if($feeHead->may==1) checked @endif disabled data-parsley-multiple="groups" data-parsley-mincheck="2">
                                <label class="custom-control-label" for="may"></label>
                            </div>
                            </td>
                            <td> <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="jun"  name = "jun" @if($feeHead->jun==1) checked @endif disabled data-parsley-multiple="groups" data-parsley-mincheck="2">
                                <label class="custom-control-label" for="jun"></label>
                            </div>
                            </td>
                            <td> <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="jul"  name = "jul" @if($feeHead->jul==1) checked @endif disabled data-parsley-multiple="groups" data-parsley-mincheck="2">
                                <label class="custom-control-label" for="jul"></label>
                            </div>
                            </td>
                            <td> <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="aug"  name = "aug" @if($feeHead->aug==1) checked @endif disabled data-parsley-multiple="groups" data-parsley-mincheck="2">
                                <label class="custom-control-label" for="aug"></label>
                            </div>
                            </td>
                            <td> <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="sep"  name = "sep" @if($feeHead->sep==1) checked @endif disabled data-parsley-multiple="groups" data-parsley-mincheck="2">
                                <label class="custom-control-label" for="sep"></label>
                            </div>
                            </td>
                            <td> <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="oct"  name = "oct" @if($feeHead->oct==1) checked @endif disabled data-parsley-multiple="groups" data-parsley-mincheck="2">
                                <label class="custom-control-label" for="oct"></label>
                            </div>
                            </td>
                            <td> <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="nov"  name = "nov" @if($feeHead->nov==1) checked @endif disabled data-parsley-multiple="groups" data-parsley-mincheck="2">
                                <label class="custom-control-label" for="nov"></label>
                            </div>
                            </td>
                            <td> <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="dec"  name = "dec" @if($feeHead->dec==1) checked @endif disabled data-parsley-multiple="groups" data-parsley-mincheck="2">
                                <label class="custom-control-label" for="dec"></label>
                            </div>
                            </td>


                            <td>
                            <a href="editFeeHead/{{$feeHead->id}}"><i class="fas fa-edit text-info font-16"></i></a>/
                            <a onclick="return confirm('Are you sure want to delete?')" href="deleteFeeHead/{{$feeHead->id}}"><i class="fas fa-trash-alt text-danger font-16"></i></a>

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


@section('footerScript')

<script src="{{ URL::asset('plugins/footable/js/footable.js')}}"></script>
        <script src="{{ URL::asset('plugins/moment/moment.js')}}"></script>
        <script src="{{ URL::asset('assets/pages/jquery.footable.init.js')}}"></script>
         <!-- Required datatable js -->
         <script src="{{ URL::asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
         <script src="{{ URL::asset('plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
         <script src="{{ URL::asset('plugins/peity-chart/jquery.peity.min.js') }}"></script>
         <script src="{{ URL::asset('assets/pages/jquery.analytics_customers.init.js') }}"></script>
@stop
