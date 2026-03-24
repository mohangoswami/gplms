@extends('layouts.student_master')

@section('headerStyle')
 <!-- DataTables -->
<link href="{{ URL::asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Responsive datatable examples -->
<link href="{{ URL::asset('plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('content')


<!-- Fee Receipt Form -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h2 class="mt-0 header-title">Fee Details</h2>
                <x-validation-errors />

                <!-- Student Details -->
                <form action="{{ route('post_FeeReceipt') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ $id }}">
                    <input type="hidden" id="oldBalance" name="oldBalance" value="{{ $oldBalance ?? 0 }}">
                    <input type="hidden" id="selectedMonths" name="selectedMonths" value="{{ json_encode($selectedMonths ?? []) }}">

                    <!-- Student Information -->
                    <div class="form-group row">
                        @foreach([
                            ['label' => 'Date', 'name' => 'date', 'type' => 'date', 'value' => '', 'required' => true],
                            ['label' => 'Adm. No.', 'name' => 'srNo', 'type' => 'text', 'value' => $user->srNo ?? 'N/A', 'readonly' => true],
                            ['label' => 'Name', 'name' => 'editName', 'type' => 'text', 'value' => $user->name ?? 'N/A', 'readonly' => true],
                            ['label' => 'Class', 'name' => 'class', 'type' => 'text', 'value' => $user->grade ?? 'N/A', 'readonly' => true],
                            // ['label' => 'Category', 'name' => 'category', 'type' => 'text', 'value' => $user->category->category ?? 'N/A', 'readonly' => true],
                            ['label' => 'Father Name', 'name' => 'fName', 'type' => 'text', 'value' => $user->fName ?? 'N/A', 'readonly' => true],
                            ['label' => 'Route', 'name' => 'route', 'type' => 'text', 'value' => $user->route->routeName ?? 'N/A', 'readonly' => true],
                            // ['label' => 'Old Bal', 'name' => 'oldBalance', 'type' => 'text', 'value' => $oldBalance ?? 0, 'readonly' => true],
                        ] as $input)
                        <div class="col-md-2">
                            <label>{{ $input['label'] }}</label>
                            <input class="form-control" type="{{ $input['type'] }}"
                            id="{{ $input['name'] }}"
                            name="{{ $input['name'] }}"
                            value="{{ $input['name'] === 'date' ? date('Y-m-d') : $input['value'] }}"
                            {{ $input['required'] ?? true ? 'required' : '' }}
                            {{ $input['readonly'] ?? true ? 'readonly' : '' }}>
                        </div>
                        @endforeach
                    </div>


                    <!-- Monthly Fee Details -->
                    <div class="form-group mb-0 row">
                        <div class="col-md-12">
                            @foreach (['apr' => 'Apr', 'may' => 'May', 'jun' => 'Jun', 'jul' => 'Jul', 'aug' => 'Aug', 'sep' => 'Sep', 'oct' => 'Oct', 'nov' => 'Nov', 'dec' => 'Dec', 'jan' => 'Jan', 'feb' => 'Feb', 'mar' => 'Mar'] as $monthKey => $monthLabel)
                                <div class="form-check-inline">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="{{ $monthKey }}" name="{{ $monthKey }}"
                                        {{ in_array($monthKey, $selectedMonths) ? 'checked' : '' }}
                                        onclick="return false;" > <!-- Prevent changes -->

                                        <label class="custom-control-label" for="{{ $monthKey }}">{{ $monthLabel }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>


                    <!-- Fee Table -->
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Fee Head</th>
                                    @foreach ([ 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar'] as $month)
                                        <th>{{ ucfirst($month) }}</th>
                                    @endforeach
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>


                            @foreach ($feePlans as $feePlan)
                                @if (strtoupper($feePlan->feeHead->name) !== "LATE FEE") <!-- Exclude Late Fee -->
                                @php
                                    $hasRelevantMonth = false;
                                    foreach (['apr','may','jun','jul','aug','sep','oct','nov','dec','jan','feb','mar'] as $month) {
                                        if (in_array($month, $selectedMonths) && $feePlan->feeHead->{$month} == 1 && !(
                                            isset($paidMonths[$feePlan->feeHead->name]) && in_array($month, $paidMonths[$feePlan->feeHead->name])
                                        )) {
                                            $hasRelevantMonth = true;
                                            break;
                                        }
                                    }
                                @endphp

                                @if (!$hasRelevantMonth)
                                    @continue
                                @endif

                                <tr>
                                        <td>{{ $feePlan->feeHead->name }}</td>
                                        @foreach (['apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar'] as $month)
                                        <td>
                                            @php
                                                $isPaid = isset($paidMonths[$feePlan->feeHead->name]) && in_array($month, $paidMonths[$feePlan->feeHead->name]);
                                            @endphp
                                            @if (in_array($month, $selectedMonths) && $feePlan->feeHead->{$month} == 1 && !$isPaid)
                                                @php $feeHeadTotal += $feePlan->value; @endphp
                                                <div class="form-check">
                                                    <input type="checkbox"
                                                        class="form-check-input fee-checkbox"
                                                        data-fee="{{ $feePlan->value }}"
                                                        data-head="{{ $feePlan->feeHead->name }}"
                                                        id="{{ $feePlan->feeHead->name . '-' . $month }}"
                                                        name="feeDetails[{{ $feePlan->feeHead->name }}][{{ $month }}]"
                                                        value="{{ $feePlan->value }}"
                                                        checked
                                                        onclick="return false;" >                                                    <label class="form-check-label" for="{{ $feePlan->feeHead->name . '-' . $month }}">
                                                        {{ $feePlan->value }}
                                                    </label>
                                                </div>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        @endforeach
                                        <td class="feeHeadTotal">
                                            {{-- @if($feeHeadTotal != null)
                                            <input type="hidden" name="feeDetails[{{ $feePlan->feeHead->name }}][total]" value="{{ $feeHeadTotal }}">
                                            @endif --}}
                                            {{ $feeHeadTotal }}
                                        </td>
                                    </tr>
                                    @php $feeHeadTotal = null; @endphp
                                @endif
                            @endforeach

                                {{-- Route Fee Details --}}
                                @isset($routeFeePlan)
                                    <tr>
                                        <td>Transport</td>
                                        @foreach ([ 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar'] as $month)
                                        <td>
                                                @if (in_array($month, $selectedMonths) && in_array($month, $routeMonthsToPay))
                                                    @php $routeHeadTotal += $routeFeePlan->value; @endphp
                                                    <div class="form-check">
                                                        <input type="checkbox"
                                                               class="form-check-input fee-checkbox"
                                                               data-fee="{{ $routeFeePlan->value }}"
                                                               data-head="Transport"
                                                               id="Transport-{{ $month }}"
                                                               name="feeDetails[Transport][{{ $month }}]"
                                                               value="{{ $routeFeePlan->value }}"
                                                               checked
                                                               onclick="return false;" >
                                                        <label class="form-check-label" for="Transport-{{ $month }}">
                                                            {{ $routeFeePlan->value }}
                                                        </label>
                                                    </div>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        @endforeach
                                        <td class="routeHeadTotal">
                                            {{-- <input type="hidden" name="feeDetails[Transport][total]" value="{{ $routeHeadTotal }}"> --}}
                                            {{ $routeHeadTotal }}
                                        </td>
                                    </tr>
                                @endisset
                            </tbody>
                        </table>
                    </div>

                    <!-- Fee Calculation -->
                <div class="form-group row">
                    @foreach([
                        ['label' => 'Old Bal', 'name' => 'oldBalance', 'type' => 'text', 'value' => $oldBalance ?? 0, 'readonly' => true],
                        ['label' => 'Late Fee', 'name' => 'lateFee', 'type' => 'number', 'value' => $lateFee ?? 0, 'readonly' => true],
                        ['label' => 'Concession', 'name' => 'concession', 'type' => 'text', 'value' => $totalConcession ?? 0, 'readonly' => true],
                        ['label' => 'Net Fee', 'name' => 'netFee', 'id' => 'netFee', 'type' => 'number', 'value' => null, 'readonly' => true],
                        ['label' => 'Total', 'name' => 'receivedAmt', 'type' => 'number', 'value' => 0, 'readonly' => true],
                        // ['label' => 'Balance', 'name' => 'balance', 'type' => 'text', 'value' => null],
                    ] as $input)
                    <div class="col-md-2">
                        <label>{{ $input['label'] }}</label>
                        <input class="form-control" type="{{ $input['type'] }}"
                            id="{{ $input['name'] }}"
                            name="{{ $input['name'] }}"
                            value="{{ $input['value'] }}"
                            oninput="{{ $input['name'] === 'receivedAmt' ? 'updateBalance()' : '' }}"
                            {{ isset($input['readonly']) && $input['readonly'] ? 'readonly' : '' }}>
                    </div>
                    @endforeach
                </div>




                    {{-- <!-- Payment Details -->
                    <div class="form-group row">
                        @foreach([
                            ['label' => 'Payment Type', 'name' => 'paymentType', 'type' => 'text', 'value' => 'Cash'],
                            ['label' => 'Bank Name', 'name' => 'bankName', 'type' => 'text', 'value' => ''],
                            ['label' => 'Cheque/DD No.', 'name' => 'chequeNo', 'type' => 'text', 'value' => ''],
                            ['label' => 'Chq. Date', 'name' => 'chqDate', 'type' => 'text', 'value' => ''],
                            ['label' => 'Remarks', 'name' => 'remark', 'type' => 'text', 'value' => '']
                        ] as $input)
                        <div class="col-md-2">
                            <label>{{ $input['label'] }}</label>
                            <input class="form-control" type="{{ $input['type'] }}" id="{{ $input['name'] }}" name="{{ $input['name'] }}" value="{{ $input['value'] }}">
                        </div>
                        @endforeach
                    </div> --}}


                    <div class="form-group">
                        {{-- <button type="button" class="btn btn-gradient-primary" id="payButton">Pay</button> --}}
                        <a href="/home" class="btn btn-gradient-danger">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footerScript')
<script>
    // Move this function definition ABOVE so it's available before being called
    function hideEmptyFeeRows() {
        const rows = document.querySelectorAll('table.table-bordered tbody tr');

        rows.forEach(row => {
            const feeInputs = row.querySelectorAll('input.fee-checkbox');
            const hasFee = Array.from(feeInputs).some(input => parseFloat(input.value) > 0 && input.checked);

            if (!hasFee) {
                row.style.display = 'none';
            }
        });
    }



    document.addEventListener('DOMContentLoaded', function () {
        const checkboxes = document.querySelectorAll('.fee-checkbox');
        const netFeeInput = document.getElementById('netFee');
        const concessionInput = document.getElementById('concession');
        const receivedAmtInput = document.getElementById('receivedAmt');
        const balanceInput = document.getElementById('balance');
        const lateFeeInput = document.getElementById('lateFee');
        const oldBalanceInput = document.getElementById('oldBalance');
        const dateInput = document.getElementById('date');

        function calculateNetFee() {
            let totalFee = 0;

            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    totalFee += parseFloat(checkbox.dataset.fee || 0);
                }
            });

            const lateFee = parseFloat(lateFeeInput.value || 0);
            const concession = parseFloat(concessionInput.value || 0);
            const oldBalance = parseFloat(oldBalanceInput.value || 0);

            const netFee = totalFee + lateFee - concession + oldBalance;

            netFeeInput.value = netFee.toFixed(2);
            receivedAmtInput.value = netFee.toFixed(2);
            updateBalance();
        }

        function updateBalance() {
            const netFee = parseFloat(netFeeInput.value || 0);
            const receivedAmt = parseFloat(receivedAmtInput.value || 0);
            balanceInput.value = (netFee - receivedAmt).toFixed(2);
        }

        checkboxes.forEach(checkbox => checkbox.addEventListener('change', calculateNetFee));
        [lateFeeInput, concessionInput, receivedAmtInput].forEach(input => input.addEventListener('input', calculateNetFee));

        calculateNetFee();
        hideEmptyFeeRows();  // Now this will work!

        if (dateInput && !dateInput.value) {
            const today = new Date().toISOString().split('T')[0];
            dateInput.value = today;
        }
    });
</script>



{{-- <script src="https://checkout.razorpay.com/v1/checkout.js"></script> --}}
<script>
   document.getElementById('payButton').addEventListener('click', function () {
    let csrfToken = '{{ csrf_token() }}';
    let amount = document.getElementById('netFee').value * 100; // Convert to paisa

    let options = {
        "key": "{{ env('RAZORPAY_KEY') }}",
        "amount": amount,
        "currency": "INR",
        "name": "Your School Name",
        "description": "Fee Payment",
        "image": "{{ asset('logo.png') }}",
        "handler": function (response) {
            console.log("✅ Razorpay Payment Success:", response);

            fetch("{{ url('/student/razorpay/payment/confirm') }}", {
    method: "POST",
    headers: {
        "Content-Type": "application/json",
        "Accept": "application/json",
        "X-CSRF-TOKEN": "{{ csrf_token() }}"
    },
    body: JSON.stringify({
        razorpay_payment_id: response.razorpay_payment_id,
        id: "{{ $id }}",
        netFee: amount / 100,
        date: document.getElementById('date').value,
        paymentType: 'Razorpay'
    })
})
.then(res => {
    if (!res.ok) {
        throw new Error(`HTTP error! Status: ${res.status}`);
    }
    return res.json();
})
.then(data => {
    console.log("✅ Server Response:", data);
    if (data.success) {
        alert("Payment successful!");
        window.location.href = data.redirect_url;  // Redirect on success
    } else {
        alert("Payment failed! Please try again.");
    }
})
.catch(err => {

    console.error("🚨 Fetch Error:", err);

    alert("An error occurred. Please try again.");
});



        },
        "prefill": {
            "name": "{{ $user->name }}",
            "email": "{{ $user->email ?? 'student@example.com' }}",
            "contact": "{{ $user->mobile ?? '9999999999' }}"
        },
        "theme": {
            "color": "#3399cc"
        }
    };

    let rzp = new Razorpay(options);
    rzp.open();
});

</script>


@stop


