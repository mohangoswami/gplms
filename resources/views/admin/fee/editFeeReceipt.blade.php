@include('layouts.partials.flash-errors')
@php
$layout = Auth::guard('admin')->check() ? 'layouts.admin_analytics-master' : 'layouts.cashier-master';
@endphp

@extends($layout)

@section('headerStyle')
<link href="{{ URL::asset('plugins/footable/css/footable.bootstrap.css')}}" rel="stylesheet" type="text/css">
@stop


<!-- Edit Fee Receipt -->

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h2 class="mt-0 header-title">Edit Fee Receipt</h2>
                <x-validation-errors />

                <!-- Fee Receipt Edit Form -->
                <form action="{{ route('editFeeReceipt', ['id' => $id]) }}" method="POST">
                    @csrf
                    @method('PUT') <!-- For Update -->
                    <input type="hidden" name="receiptId" value="{{ $receipt->receiptId }}">
                    <input type="hidden" name="id" value="{{ $id }}">
                    <input type="hidden" id="oldBalance" name="oldBalance" value="{{ $receipt->oldBalance ?? 0 }}">
                    <input type="hidden" name="user_id" value="{{ $receipt->user_id }}">

                    <!-- Existing Receipt Information -->
                    <div class="form-group row">
                        @foreach([

                            ['label' => 'Receipt No.', 'name' => 'receiptNo', 'type' => 'text', 'value' => $receipt->receiptId, 'disabled' => true],
                            ['label' => 'Date', 'name' => 'date', 'type' => 'date', 'value' => $receipt->date],
                            ['label' => 'Adm. No.', 'name' => 'admission_number', 'type' => 'text', 'value' => $user->admission_number, 'disabled' => true],
                            ['label' => 'Father Name', 'name' => 'fName', 'type' => 'text', 'value' => $user->fName ?? 'N/A', 'disabled' => true],
                            ['label' => 'Route', 'name' => 'route', 'type' => 'text', 'value' => $user->route->routeName ?? 'N/A', 'disabled' => true],

                       ] as $input)
                        <div class="col-md-2">
                            <label>{{ $input['label'] }}</label>
                            <input class="form-control" type="{{ $input['type'] }}" id="{{ $input['name'] }}" name="{{ $input['name'] }}"
                                value="{{ $input['value'] }}" {{ $input['disabled'] ?? false ? 'disabled' : '' }}>
                        </div>
                        @endforeach
                    </div>

                    <div class="form-group row">
                        @foreach([
                            ['label' => 'Name', 'name' => 'name', 'id' => 'name', 'type' => 'text', 'value' => $user->name, 'disabled' => true],
                            ['label' => 'Class', 'name' => 'class', 'id' => 'class', 'type' => 'text', 'value' => $user->grade, 'disabled' => true],
                            ['label' => 'Category', 'name' => 'category', 'id' => 'category', 'type' => 'text', 'value' => $user->category->category, 'disabled' => true],
                            ['label' => 'Old Bal', 'name' => 'oldBalance', 'id' => 'oldBalance', 'type' => 'text', 'value' => $receipt->oldBalance ?? 0, 'disabled' => true],
                        ] as $input)
                        <div class="col-md-2">
                            <label>{{ $input['label'] }}</label>
                            <input class="form-control" type="{{ $input['type'] }}" id="{{ $input['id'] }}" name="{{ $input['name'] }}" value="{{ $input['value'] }}"  disabled = true oninput="calculateNetFee()" >
                        </div>
                        @endforeach
                    </div>

                    <!-- Pre-fill the Monthly Fee Details -->
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Fee Head</th>
                                    @foreach ($months as $month)
                                    <th>{{ ucfirst($month) }}</th>
                                    @endforeach
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($feePlans as $feePlan)
                                    @if ($feePlan->feeHead->name !== 'LATE FEE') <!-- Exclude Late Fee -->
                                        <tr>
                                            <td>{{ $feePlan->feeHead->name }}</td>
                                            @php
                                                $totalFee = 0; // Initialize total fee for the fee head
                                            @endphp
                                            @foreach ($months as $month)
                                                @if (in_array($month, $feeHeadApplicableMonths[$feePlan->feeHead->name])) <!-- Only render for applicable months -->
                                                    <td>
                                                        @php
                                                            $isChecked = false;
                                                            $isDisabled = false;
                                                            $deposited = false;

                                                            // Check if this month is paid in previous receipts for this feeHead
                                                            if (isset($paidMonthsFromPreviousReceipts[$feePlan->feeHead->name]) && in_array($month, $paidMonthsFromPreviousReceipts[$feePlan->feeHead->name])) {
                                                                $isChecked = true;
                                                                $isDisabled = true; // Disable checkbox if already deposited
                                                                $deposited = true;
                                                            } elseif (isset($selectedMonths[$feePlan->feeHead->name]) && in_array($month, $selectedMonths[$feePlan->feeHead->name])) {
                                                                $isChecked = true; // If it's selected in the current receipt, it's editable
                                                            }

                                                            // Add to total if checked and not disabled
                                                            if ($isChecked && !$isDisabled) {
                                                                $totalFee += $feePlan->value;
                                                            }
                                                        @endphp

                                                        @if($deposited)
                                                            <!-- Show fee as deposited with a green color -->
                                                            <span style="color: green;">{{ $feePlan->value }}</span>
                                                        @else
                                                            <!-- Show fee with checkbox -->
                                                            <input type="hidden" name="feeDetails[{{ $feePlan->feeHead->name }}][{{ $month }}]" value="null">
                                                            <input type="checkbox"
                                                                   class="form-check-input fee-checkbox"
                                                                   data-fee="{{ $feePlan->value }}"
                                                                   data-head="{{ $feePlan->feeHead->name }}"
                                                                   data-total-id="total_{{ $feePlan->feeHead->name }}"
                                                                   name="feeDetails[{{ $feePlan->feeHead->name }}][{{ $month }}]"
                                                                   value="{{ $feePlan->value }}"
                                                                   {{ $isChecked ? 'checked' : '' }}
                                                                   {{ $isDisabled ? 'disabled' : '' }}>
                                                            <span style="color: red;">{{ $feePlan->value }}</span>
                                                        @endif
                                                    </td>
                                                @else
                                                    <td>-</td> <!-- Render a placeholder if not applicable -->
                                                @endif
                                            @endforeach
                                            <td style="font-weight: bold;" id="total_{{ $feePlan->feeHead->name }}">0.00</td>
                                        </tr>
                                    @endif
                                @endforeach
                                    <tr>
                                        <td>Transport Fee</td>
                                        @foreach ($months as $month)
                                            @php
                                                $isTransportPaid = in_array($month, $transportPaidMonths);
                                            @endphp
                                            <td>


                                                    {{-- <input type="hidden" name="transportFee[{{ $month }}]" value="null"> --}}

                                                    @if($user->route->routeName !== "NA")

                                                        <input type="checkbox"
                                                                    class="form-check-input fee-checkbox"
                                                                    data-fee="{{ $transportFee }}"
                                                                    data-head="{{ "Transport" }}"
                                                                    data-total-id="total_{{ "Transport" }}"
                                                                    name="feeDetails[{{ "Transport" }}][{{ $month }}]"
                                                                    value="{{ $transportFee }}"
                                                                    {{ $isTransportPaid ? 'checked ' : '' }}>

                                                    <span style="color: red;">{{ $transportFee }}</span>
                                                @endif
                                            </td>
                                        @endforeach
                                        <td style="font-weight: bold;" id="total_transport">{{ $totalTransportFee }}</td>
                                    </tr>
                            </tbody>


                        </table>
                    </div>

                    <!-- Fee Calculation -->
                    <div class="form-group row">
                        @foreach([
                            ['label' => 'Late Fee', 'name' => 'lateFee', 'type' => 'number', 'value' => $receipt->lateFee ?? 0],
                            ['label' => 'Concession', 'name' => 'concession', 'type' => 'text', 'value' => $receipt->concession ?? 0],
                            ['label' => 'Net Fee', 'name' => 'netFee', 'id' => 'netFee', 'type' => 'number', 'value' => $receipt->netFee ?? 0],
                            ['label' => 'Received Amt.', 'name' => 'receivedAmt', 'type' => 'number', 'value' => $receipt->receivedAmt ?? 0],
                            ['label' => 'Balance', 'name' => 'balance', 'type' => 'text', 'value' => $receipt->balance ?? 0],
                        ] as $input)
                        <div class="col-md-2">
                            <label>{{ $input['label'] }}</label>
                            <input class="form-control" type="{{ $input['type'] }}" id="{{ $input['name'] }}" name="{{ $input['name'] }}" value="{{ $input['value'] }}" oninput="calculateNetFee()">
                        </div>
                        @endforeach
                    </div>



                    <!-- Payment Details -->
                    <div class="form-group row">
                        @foreach([
                            ['label' => 'Payment Type', 'name' => 'paymentType', 'type' => 'text', 'value' => $receipt->paymentMode ?? ''],
                            ['label' => 'Bank Name', 'name' => 'bankName', 'type' => 'text', 'value' =>  $receipt->bankName ?? ''],
                            ['label' => 'Cheque/DD No.', 'name' => 'chequeNo', 'type' => 'text', 'value' =>  $receipt->chequeNo ?? ''],
                            ['label' => 'Chq. Date', 'name' => 'chqDate', 'type' => 'text', 'value' =>  $receipt->chequeDate ?? ''],
                            ['label' => 'Remarks', 'name' => 'remark', 'type' => 'text', 'value' =>  $receipt->remarks ?? '']
                        ] as $input)
                        <div class="col-md-2">
                            <label>{{ $input['label'] }}</label>
                            <input class="form-control" type="{{ $input['type'] }}" id="{{ $input['name'] }}" name="{{ $input['name'] }}" value="{{ $input['value'] }}">
                        </div>
                        @endforeach
                    </div>


                    <div class="form-group">
                        <button type="submit" class="btn btn-gradient-primary">Update</button>
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
    calculateTotals(); // Ensure all totals are correctly calculated at the start
});



document.addEventListener('DOMContentLoaded', function () {
    // Get all checkboxes and inputs
    const checkboxes = document.querySelectorAll('.fee-checkbox');
    const transportCheckboxes = document.querySelectorAll('.transport-checkbox');
    const netFeeInput = document.getElementById('netFee');
    const lateFeeInput = document.getElementById('lateFee');
    const concessionInput = document.getElementById('concession');
    const receivedAmtInput = document.getElementById('receivedAmt');
    const balanceInput = document.getElementById('balance');
    const oldBalanceInput = document.getElementById('oldBalance');
    const totalTransportElement = document.getElementById('total_transport');
    console.log(oldBalanceInput);

    function calculateTotals() {
        let totalNetFee = 0; // Overall net fee
        let totalTransport = 0; // Transport fee

        // Reset totals for each fee head
        const feeHeadTotals = {};

        // Calculate total fee for each fee head
        checkboxes.forEach(checkbox => {
            const feeValue = parseFloat(checkbox.getAttribute('data-fee')) || 0;
            const totalId = checkbox.getAttribute('data-total-id');
            const feeHead = checkbox.getAttribute('data-head');

            // Initialize total for this fee head if not already done
            if (!feeHeadTotals[feeHead]) {
                feeHeadTotals[feeHead] = 0;
            }

            if (checkbox.checked) {
                // Add fee value to the fee head's total
                feeHeadTotals[feeHead] += feeValue;
                totalNetFee += feeValue;
            }
        });

        // Update total fee for each fee head in the DOM
        for (const [feeHead, total] of Object.entries(feeHeadTotals)) {
            const totalElement = document.getElementById(`total_${feeHead}`);
            if (totalElement) {
                totalElement.textContent = total.toFixed(2); // Update total in the DOM
            }
        }

        // Calculate transport fee
        transportCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                totalTransport += parseFloat(checkbox.getAttribute('data-fee')) || 0;
            }
        });

        // Update transport fee in UI
        totalTransportElement.textContent = totalTransport.toFixed(2);

        // Calculate the final net fee
        const lateFee = parseFloat(lateFeeInput.value) || 0;
        const concession = parseFloat(concessionInput.value) || 0;
        const oldBalance = parseFloat(oldBalanceInput.value) || 0;
        const netFee = totalNetFee + totalTransport + lateFee - concession + oldBalance;

        // Update net fee and balance
        netFeeInput.value = netFee.toFixed(2);
        const receivedAmt = parseFloat(receivedAmtInput.value) || 0;
        balanceInput.value = (netFee - receivedAmt).toFixed(2);
    }

    // Add event listeners to checkboxes
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', calculateTotals);
    });

    // Add event listeners to transport checkboxes
    transportCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', calculateTotals);
    });

    // Add event listeners to late fee, concession, and received amount inputs
    [lateFeeInput, concessionInput, receivedAmtInput].forEach(input => {
        input.addEventListener('input', calculateTotals);
    });

    // Perform initial calculation on page load
    calculateTotals();
});


    document.addEventListener('DOMContentLoaded', function () {
    const transportCheckboxes = document.querySelectorAll('.transport-checkbox');
    const totalTransportElement = document.getElementById('total_transport');

    function calculateTransportFee() {
        let totalTransport = 0;
        transportCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                totalTransport += parseFloat(checkbox.getAttribute('data-fee')) || 0;
            }
        });
        totalTransportElement.textContent = totalTransport.toFixed(2);
    }

    transportCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', calculateTransportFee);
    });

    calculateTransportFee();
});

</script>
@endsection
