@extends('layouts.student_master')

@section('headerStyle')
 <!-- DataTables -->
<link href="{{ URL::asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Responsive datatable examples -->
<link href="{{ URL::asset('plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('content')


<!-- Fee Deposit Form -->
<div class="row">
    <div class="col-lg-12">
        <div class="card m-3">
            <div class="card-body">
                <h2 class="mt-0 mb-3">Fee </h2>

                <form action="{{ route('postStudentFeeDetail') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ $id }}">
                    <input type="hidden" name="oldBalance" value="{{ $balance }}">

                    <!-- Student Information -->
                    <div class="form-group row">
                        @foreach([
                            ['label' => 'Admission No.', 'id' => 'srNo', 'value' => $user->id, 'type' => 'text'],
                            ['label' => 'Name', 'id' => 'editName', 'value' => $user->name, 'type' => 'text'],
                            ['label' => 'Class', 'id' => 'class', 'value' => $user->grade, 'type' => 'text'],
                            ['label' => 'Category', 'id' => 'category', 'value' => $user->category->category, 'type' => 'text'],
                        ] as $input)
                        <div class="col-md-3">
                            <label class="my-3">{{ $input['label'] }}</label>
                            <input class="form-control" type="{{ $input['type'] }}" id="{{ $input['id'] }}" name="{{ $input['id'] }}" value="{{ $input['value'] }}" disabled>
                        </div>
                        @endforeach
                    </div>

                    <!-- Additional Details -->
                    <div class="form-group row">
                        @foreach([
                            ['label' => 'Balance', 'id' => 'Balance', 'value' => $balance, 'type' => 'text'],
                            ['label' => 'Father Name', 'id' => 'fName', 'value' => $user->fName, 'type' => 'text'],
                            ['label' => 'Mobile Number', 'id' => 'mobile', 'value' => $user->mobile, 'type' => 'number'],
                            ['label' => 'Route', 'id' => 'route', 'value' => $user->route->routeName ?? null, 'type' => 'text'],
                        ] as $input)
                        <div class="col-md-3">
                            <label class="my-3">{{ $input['label'] }}</label>
                            <input class="form-control" type="{{ $input['type'] }}" id="{{ $input['id'] }}" name="{{ $input['id'] }}" value="{{ $input['value'] }}" disabled>
                        </div>
                        @endforeach
                    </div>

                    {{-- <!-- Monthly Checkboxes -->
                    <div class="form-group mb-0 row">
                        <div class="col-md-12">
                            @foreach([
                                'apr' => 'Apr', 'may' => 'May', 'jun' => 'Jun', 'jul' => 'Jul',
                                'aug' => 'Aug', 'sep' => 'Sep', 'oct' => 'Oct', 'nov' => 'Nov',
                                'dec' => 'Dec', 'jan' => 'Jan', 'feb' => 'Feb', 'mar' => 'Mar'
                            ] as $monthKey => $monthLabel)
                                @php
                                    $isMonthAvailable = $monthStatus[$monthKey] ?? false || $transportMonthStatus[$monthKey] ?? false;
                                @endphp
                                @if ($isMonthAvailable)
                                    <div class="form-check-inline my-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="{{ $monthKey }}" name="{{ $monthKey }}" data-parsley-multiple="groups">
                                            <label class="custom-control-label" for="{{ $monthKey }}">{{ $monthLabel }}</label>
                                        </div>
                                    </div>
                                @endif
                            @endforeach

                        </div>
                    </div> --}}

                    <!-- Monthly Checkboxes -->
<div class="form-group mb-0 row">
    <div class="col-md-12">
        @foreach([
            'apr' => 'Apr', 'may' => 'May', 'jun' => 'Jun', 'jul' => 'Jul',
            'aug' => 'Aug', 'sep' => 'Sep', 'oct' => 'Oct', 'nov' => 'Nov',
            'dec' => 'Dec', 'jan' => 'Jan', 'feb' => 'Feb', 'mar' => 'Mar'
        ] as $monthKey => $monthLabel)
            @php
                $isRegularMonth = array_key_exists($monthKey, $monthStatus);
                $isTransportMonth = array_key_exists($monthKey, $transportMonthStatus);
                $showMonth = $isRegularMonth || $isTransportMonth;

                // If true, means we want to tick it (optional logic)
                $isChecked = false; // you can change this to true for months you want ticked
            @endphp

            @if ($showMonth)
                <div class="form-check-inline my-2">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox"
                               class="custom-control-input"
                               id="{{ $monthKey }}"
                               name="{{ $monthKey }}"
                               {{ $isChecked ? 'checked' : '' }}>
                        <label class="custom-control-label" for="{{ $monthKey }}">{{ $monthLabel }}</label>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
</div>



                    <!-- Submit and Cancel Buttons -->
                    <div class="form-group mt-3">
                        <button type="submit" class="btn btn-gradient-primary">View</button>
                        <a href="/fee/allStudentsRecord" class="btn btn-gradient-danger">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footerScript')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('form');

        form.addEventListener('submit', function (event) {
            event.preventDefault(); // Prevent the default form submission

            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(() => {
                // Always refresh the page after submission
                window.location.href = window.location.href;
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });
    });


//     document.addEventListener('DOMContentLoaded', function () {
//     // Select all checked checkboxes
//     const checkedBoxes = document.querySelectorAll('input[type="checkbox"]:checked');

//     checkedBoxes.forEach((checkbox) => {
//         checkbox.addEventListener('click', function (event) {
//             event.preventDefault(); // Prevent user from unchecking
//         });
//     });
// });

    </script>

<script src="{{ URL::asset('plugins/footable/js/footable.js') }}"></script>
<script src="{{ URL::asset('plugins/moment/moment.js') }}"></script>
<script src="{{ URL::asset('assets/pages/jquery.footable.init.js') }}"></script>
@stop
