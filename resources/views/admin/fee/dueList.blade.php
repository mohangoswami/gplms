@php
if (Auth::guard('admin')->check()) {
    $layout = 'layouts.admin_analytics-master';
} elseif (Auth::guard('teacher')->check()) {
    $layout = 'layouts.teacher_analytics-master';
} else {
    $layout = 'layouts.cashier-master';
}
@endphp

@extends($layout)

@section('headerStyle')
<link href="{{ URL::asset('plugins/footable/css/footable.bootstrap.css')}}" rel="stylesheet" type="text/css">
@stop



@section('content')

<!-- Flash Messages -->
@include('layouts.partials.flash-messages')


<div class="row">
    <div class="col-lg-12">
        <div class="card m-3">
            <div class="card-body ">
                <h2 class="mt-0 mb-3 mt-2">Due List</h2>
                <form action="{{ route('dueList') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group row">
                        <div class="form-group">
                            <label for="lable_subject">Select Route</label>
                            <select id="route" name="route" class="custom-select">
                                <option value="all">All</option>
                                @foreach ($routes as $route)
                                    <option value="{{ $route->routeName }}">{{ $route->routeName }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="lable_subject">Select Category</label>
                            <select id="category" name="category" class="custom-select">
                                <option value="all">All</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->category }}">{{ $category->category }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="lable_subject">Select Class</label>
                            <select id="class" name="class" class="custom-select">
                                <option value="all">All</option>
                                @foreach ($classes as $class)
                                    <option value="{{ $class->class }}">{{ $class->class }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div><!-- end row -->

                    <div class="form-group row">
                        <div class="col-md-3">
                            <label class="my-3">Minimum</label>
                            <input class="form-control" type="number" id="minimum" name="minimum" value="">
                        </div><!-- end col -->
                        <div class="col-md-3">
                            <label class="my-3">Maximum</label>
                            <input class="form-control" type="number" id="maximum" name="maximum" value="">
                        </div><!-- end col -->
                    </div><!-- end row -->

                    <div class="form-group mb-0 row align-self-center">
                        <div class="col-md-12">
                            <!-- Generate checkboxes with hidden inputs for all months -->
                            @php
                                $months = [
                                    'apr' => 'Apr', 'may' => 'May', 'jun' => 'Jun', 'jul' => 'Jul',
                                    'aug' => 'Aug', 'sep' => 'Sep', 'oct' => 'Oct',
                                    'nov' => 'Nov', 'dec' => 'Dec', 'jan' => 'Jan',
                                    'feb' => 'Feb', 'mar' => 'Mar'
                                ];
                            @endphp

                            @foreach ($months as $monthKey => $monthLabel)
                                <div class="form-check-inline my-2">
                                    <div class="custom-control custom-checkbox">
                                        <!-- Hidden input for default "off" value -->
                                        <input type="hidden" name="{{ $monthKey }}" value="off">
                                        <!-- Checkbox for the month -->
                                        <input type="checkbox" class="custom-control-input" id="{{ $monthKey }}" name="{{ $monthKey }}" value="on">
                                        <label class="custom-control-label" for="{{ $monthKey }}">{{ $monthLabel }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div><!-- end row -->

                    <button type="submit" class="btn btn-gradient-primary">View</button>
                    <a type="button" href="/fee/allStudentsRecord" class="btn btn-gradient-danger">Cancel</a>
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
