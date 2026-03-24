@php
use App\FeePlan; // Ensure the correct namespace for the FeePlan model
use App\User; // Ensure the correct namespace for the FeePlan model
@endphp

@extends('layouts.admin_analytics-master')

@section('headerStyle')


<link href="{{ URL::asset('plugins/footable/css/footable.bootstrap.css') }}" rel="stylesheet" type="text/css">
<link href="{{ URL::asset('plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css">
<!-- DataTables -->
<link href="{{ URL::asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

@stop

@section('content')

@include('layouts.partials.flash-messages')

<!-- Apply Concession Form -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h2 class="mt-0 header-title">Apply Concession</h2>
                <form action="{{ route('applyConcession') }}" method="POST">
                    @csrf

                    <!-- Select User -->
                    <div class="form-group row">
                        <label for="user_id" class="col-md-2 col-form-label">Select User</label>
                        <div class="col-md-8">
                            <select name="user_id" id="user_id" class="form-control select2" required>
                                <option value="">Search and select a student</option>
                                @foreach (User::all() as $user)
                                    <option value="{{ $user->id }}">
                                        {{ $user->name }} - Class: {{ $user->grade }} - F.Name: {{ $user->fName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>



                    <!-- Select Fee Plan -->
                    <div class="form-group row">
                        <label for="fee_plan_id" class="col-md-2 col-form-label">Select Fee Plan</label>
                        <div class="col-md-6">
                            <select name="fee_plan_id" id="fee_plan_id" class="form-control select2" required>

                                <option value="">Select a Fee Plan</option>
                            </select>
                        </div>
                    </div>

                    <!-- Select Fee Type -->
                    <div class="form-group row">
                        <label for="concession_type" class="col-md-2 col-form-label">Select type</label>
                        <div class="col-md-4">
                            <select name="concession_type" id="concession_type" class="form-control select2" required>
                                <option value="Amount">Amount</option>
                                <option value="Percentage">Percentage</option>
                            </select>
                        </div>
                    </div>

                    <!-- Concession on Fee -->
                    <div class="form-group row">
                        <label for="concession_fee" class="col-md-2 col-form-label">Fee Concession</label>
                        <div class="col-md-4">
                            <input type="number" class="form-control" name="concession_fee" id="concession_fee" placeholder="Enter concession on fee (optional)">
                        </div>
                    </div>

                    <!-- Applicable Fee -->
                    <div class="form-group row">
                        <label for="applicable_fee" class="col-md-2 col-form-label">Applicable Fee</label>
                        <div class="col-md-4">
                            <input type="number" class="form-control" name="applicable_fee" id="applicable_fee" disabled>
                        </div>
                    </div>

                    <!-- Reason for Concession -->
                    <div class="form-group row">
                        <label for="applicable_fee" class="col-md-2 col-form-label">Reason</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="reason" id="reason" >
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-gradient-primary">Apply Concession</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- List of Users with Concessions -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h2 class="mt-0 header-title">Users with Concessions</h2>
                <div class="table-responsive">
                    <table id="table1" class="table">
                        <thead>
                            <tr>
                                <th>Classe</th>
                                <th>Students</th>
                                <th>Father Name</th>
                                <th>Admission No.</th>
                                <th>Fee Plan</th>
                                <th>Fee Amount</th>
                                <th>Fee Concession</th>
                                <th>Actual Amount</th>
                                <th>Reason</th>
                                <th>Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($usersWithConcessions as $concession)

                                <tr>
                                    <td>{{ $concession->user->grade }}</td>
                                    <td>{{ $concession->user->name }}</td>
                                    <td>{{ $concession->user->fName }}</td>
                                    <td>{{ $concession->user->admission_number}}</td>
                                    <td>{{ $concession->feePlan->feeHead->name ?? 'N/A' }}</td>
                                    <td>{{ $concession->feePlan->value ?? 'N/A' }}</td>
                                    <td>{{ $concession->concession_fee }}</td>
                                    <td>{{ $concession->feePlan->value - $concession->concession_fee }}</td>
                                    <td>{{ $concession->reason }}</td>
                                    <td>

                                        <a href="{{ route('editConcession', $concession->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('deleteConcession', $concession->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this concession?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No concessions applied yet.</td>
                                </tr>
                            @endforelse
                        </tbody>


                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footerScript')
<script src="{{ URL::asset('plugins/footable/js/footable.js') }}"></script>
<script src="{{ URL::asset('plugins/moment/moment.js') }}"></script>
<script src="{{ URL::asset('plugins/select2/select2.min.js') }}"></script>
<script>
    $(document).ready(function () {
        $('.select2').select2({
            placeholder: "Search and select",
            allowClear: true,
        });
    });


    $('#user_id').on('change', function () {
        const userId = $(this).val();

        if (!userId) {
            $('#fee_plan_id').html('<option value="">Select a Fee Plan</option>');
            return;
        }

        $.ajax({
            url: `/fee/api/get-user-fee-plans/${userId}`,
            method: 'GET',
            success: function (response) {
    console.log(response); // Check API response in the console
    if (response.status === 'success') {
        let options = '<option value="">Select a Fee Plan</option>';
        response.feePlans.forEach(plan => {
            console.log(plan); // Check each fee plan object
            console.log(plan.feeHead); // Check if feeHead exists
            const feeHeadName = plan.feeHead ? plan.feeHead.name : 'N/A';
            options += `<option value="${plan.id}" data-value="${plan.value}">${feeHeadName} - ${plan.value}</option>`;
        });
        $('#fee_plan_id').html(options);
    } else {
        alert('No fee plans found.');
    }
},
            error: function (xhr) {
                console.error('Error:', xhr.responseText);
                alert('Error fetching fee plans for the user.');
            }
        });
    });




$('#concession_type, #concession_fee, #fee_plan_id').on('change input', function () {
    const feeType = $('#concession_type').val();
    const concessionValue = parseFloat($('#concession_fee').val()) || 0;
    const selectedPlan = $('#fee_plan_id option:selected');
    const feePlanValue = parseFloat(selectedPlan.data('value')) || 0;

    let applicableFee = feePlanValue;

    if (feeType === 'Percentage' && feePlanValue > 0) {
        const concessionAmount = (feePlanValue * concessionValue) / 100;
        applicableFee = feePlanValue - concessionAmount;
    } else if (feeType === 'Amount' && feePlanValue > 0) {
        applicableFee = feePlanValue - concessionValue;
    }

    // Prevent negative values
    applicableFee = applicableFee < 0 ? 0 : applicableFee;

    $('#applicable_fee').val(applicableFee.toFixed(2)); // Show the applicable fee in the input box
});

    $(document).ready(function() {
        var table = $('#table1').DataTable();


        new $.fn.dataTable.Buttons( table, {
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'

            ]
        } );
        table.buttons( 0, null ).container().appendTo(
            table.table().container()
        );
    } );

</script>
        <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>

            <!-- Required datatable js -->
            <script src="{{ URL::asset('plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
            <script src="{{ URL::asset('plugins/peity-chart/jquery.peity.min.js') }}"></script>
            <script src="{{ URL::asset('assets/pages/jquery.analytics_customers.init.js') }}"></script>


@stop
