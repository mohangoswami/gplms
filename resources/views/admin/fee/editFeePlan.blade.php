@extends('layouts.admin_analytics-master')

@section('headerStyle')
<!-- Plugins css -->
<link href="{{ URL::asset('plugins/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('content')

<!-- Flash Messages -->
@include('layouts.partials.flash-messages')

<div class="row">
    <div class="col-lg-6">
        <div class="card m-5">
            <div class="card-body">
                <h4 class="mt-0 header-title">Edit Fee Plan</h4>
                <p class="text-muted mb-3">Edit Fee Plan</p>

                <form action="/fee/editFeePlan" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ $id }}">

                    <div class="form-group">
                        <label for="feeHead">Select Fee Heading</label>
                        <select id="feeHead" name="feeHead" class="custom-select">
                            @foreach ($feeHeads as $feeHead)
                                <option value="{{ $feeHead->id }}" {{ $feePlan->feeHead_id == $feeHead->id ? 'selected' : '' }}>
                                    {{ $feeHead->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="category">Category</label>
                        <select id="category" name="category" class="form-control select2 custom-select" style="width: 100%;" data-placeholder="Choose Category">
                            @foreach ($categories as $category)
                                <option value="{{ $category->category }}" {{ $feePlan->category == $category->category ? 'selected' : '' }}>
                                    {{ $category->category }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="class">Class</label>
                        <select id="class" name="class" class="form-control select2 custom-select" style="width: 100%;" data-placeholder="Choose Class">
                            @foreach ($classes as $class)
                                <option value="{{ $class->class }}" {{ $feePlan->class == $class->class ? 'selected' : '' }}>
                                    {{ $class->class }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="value">Value</label>
                        <input class="form-control" type="number" placeholder="Enter Value" value="{{ $feePlan->value }}" id="value" name="value" required>
                    </div>

                    <button type="submit" class="btn btn-gradient-primary">Save Changes</button>
                    <a href="{{ url('fee/feePlan') }}" class="btn btn-gradient-danger">Cancel</a>
                </form>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div><!-- end col -->
</div>
@endsection

@section('footerScript')
<!-- Plugins js -->
<script src="{{ URL::asset('plugins/moment/moment.js')}}"></script>
<script src="{{ URL::asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{ URL::asset('plugins/select2/select2.min.js')}}"></script>
<script src="{{ URL::asset('assets/pages/jquery.forms-advanced.js')}}"></script>
<script src="{{ URL::asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ URL::asset('plugins/peity-chart/jquery.peity.min.js') }}"></script>
<script src="{{ URL::asset('assets/pages/jquery.analytics_customers.init.js') }}"></script>
@stop
