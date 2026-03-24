@include('layouts.partials.flash-errors')
@php
$layout = Auth::guard('admin')->check() ? 'layouts.admin_analytics-master' : 'layouts.cashier-master';
@endphp

@extends($layout)
@section('headerStyle')
<link href="{{ URL::asset('plugins/footable/css/footable.bootstrap.css') }}" rel="stylesheet" type="text/css">
@stop

@section('content')
<!-- Flash Messages -->
@include('layouts.partials.flash-messages')

<!-- Fee Receipt Form -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h2 class="mt-0 header-title">Fee Receipt</h2>
                <x-validation-errors />

                <!-- Student Details -->
                <form action="{{ route('post_receipt') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ $id }}">
                    <input type="hidden" id="oldBalance" name="oldBalance" value="{{ $oldBalance ?? 0 }}">
                    <input type="hidden" id="selectedMonths" name="selectedMonths" value="{{ json_encode($selectedMonths ?? []) }}">

                    <!-- Student Information -->
                    <div class="form-group row">
                        @foreach([
                            ['label' => 'Date', 'name' => 'date', 'type' => 'date', 'value' => '', 'required' => true],
                            ['label' => 'Adm. No.', 'name' => 'admission_number', 'type' => 'text', 'value' => $user->admission_number ?? 'N/A', 'disabled' => true],
                            ['label' => 'Name', 'name' => 'editName', 'type' => 'text', 'value' => $user->name ?? 'N/A', 'disabled' => true],
                            ['label' => 'Class', 'name' => 'class', 'type' => 'text', 'value' => $user->grade ?? 'N/A', 'disabled' => true],
                            ['label' => 'Category', 'name' => 'category', 'type' => 'text', 'value' => $user->category->category ?? 'N/A', 'disabled' => true],
                            ['label' => 'Father Name', 'name' => 'fName', 'type' => 'text', 'value' => $user->fName ?? 'N/A', 'disabled' => true],
                            ['label' => 'Route', 'name' => 'route', 'type' => 'text', 'value' => $user->route->routeName ?? 'N/A', 'disabled' => true],
                            ['label' => 'Old Bal', 'name' => 'oldBalance', 'type' => 'text', 'value' => $oldBalance ?? 0, 'disabled' => true],
                        ] as $input)
                        <div class="col-md-2">
                            <label>{{ $input['label'] }}</label>
                            <input class="form-control" type="{{ $input['type'] }}" id="{{ $input['name'] }}" name="{{ $input['name'] }}"
                                value="{{ $input['value'] }}" {{ $input['required'] ?? false ? 'required' : '' }} {{ $input['disabled'] ?? false ? 'disabled' : '' }}>
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
                                            {{ in_array($monthKey, $selectedMonths) ? 'checked' : '' }} disabled>

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
                                    @php $feeHeadTotal = 0; @endphp
                                    <tr>
                                        <td>{{ $feePlan->feeHead->name }}</td>
                                        @foreach (['apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar'] as $month)
                                        <td>
                                            @php
                                                $isPaid = isset($paidMonths[$feePlan->feeHead->name]) && in_array($month, $paidMonths[$feePlan->feeHead->name]);
                                            @endphp
                                            @if (in_array($month, $selectedMonths ?? []) && $feePlan->feeHead->{$month} == 1 && !$isPaid)
                                                @php $feeHeadTotal += $feePlan->value; @endphp
                                                <div class="form-check">
                                                    <input type="checkbox"
                                                           class="form-check-input fee-checkbox"
                                                           data-fee="{{ $feePlan->value }}"
                                                           data-head="{{ $feePlan->feeHead->name }}"
                                                           id="{{ $feePlan->feeHead->name . '-' . $month }}"
                                                           name="feeDetails[{{ $feePlan->feeHead->name }}][{{ $month }}]"
                                                           value="{{ $feePlan->value }}"
                                                           checked>
                                                    <label class="form-check-label" for="{{ $feePlan->feeHead->name . '-' . $month }}">
                                                        {{ $feePlan->value }}
                                                    </label>
                                                </div>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        @endforeach
                                        <td class="feeHeadTotal">{{ $feeHeadTotal }}</td>
                                    </tr>
                                @endif
                            @endforeach


                                {{-- Route Fee Details --}}
                                @isset($routeFeePlan)
                                    @php $routeHeadTotal = 0; @endphp
                                    <tr>
                                        <td>Transport</td>
                                        @foreach ([ 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar'] as $month)
                                        <td>
                                                @if (in_array($month, $selectedMonths ?? []) && in_array($month, $routeMonthsToPay ?? []))
                                                    @php $routeHeadTotal += $routeFeePlan->value; @endphp
                                                    <div class="form-check">
                                                        <input type="checkbox"
                                                            class="form-check-input fee-checkbox"
                                                            data-fee="{{ $routeFeePlan->value }}"
                                                            data-head="Transport"
                                                            id="Transport-{{ $month }}"
                                                            name="feeDetails[Transport][{{ $month }}]"
                                                            value="{{ $routeFeePlan->value }}"
                                                            checked>
                                                        <label class="form-check-label" for="Transport-{{ $month }}">
                                                            {{ $routeFeePlan->value }}
                                                        </label>
                                                    </div>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        @endforeach
                                        <td class="routeHeadTotal">{{ $routeHeadTotal }}</td>
                                    </tr>
                                @endisset

                            </tbody>
                        </table>
                    </div>


                    <!-- Fee Calculation -->
                    <div class="form-group row">
                        @foreach([
                            ['label' => 'Late Fee', 'name' => 'lateFee', 'type' => 'number', 'value' => $lateFee ?? 0],
                            ['label' => 'Concession', 'name' => 'concession', 'type' => 'text', 'value' => $totalConcession ?? 0],
                            ['label' => 'Net Fee', 'name' => 'netFee', 'id' => 'netFee', 'type' => 'number', 'value' => null],
                            ['label' => 'Received Amt.', 'name' => 'receivedAmt', 'type' => 'number', 'value' => 0],
                            ['label' => 'Balance', 'name' => 'balance', 'type' => 'text', 'value' => null],
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
                            ['label' => 'Payment Type', 'name' => 'paymentType', 'type' => 'text', 'value' => 'Cash'],
                            ['label' => 'Bank Name', 'name' => 'bankName', 'type' => 'text', 'value' => ''],
                            ['label' => 'Cheque/DD No.', 'name' => 'chequeNo', 'type' => 'text', 'value' => ''],
                            ['label' => 'Chq. Date', 'name' => 'chqDate', 'type' => 'text', 'value' => ''],
                            ['label' => 'Remarks', 'name' => 'remark', 'type' => 'text', 'value' => '']
                        ] as $input)
                        <div class="col-md-2">
                            <label>{{ $input['label'] }}</label>
                            <input class="form-control" type="{{ $input['type'] }}" id="{{ $input['name'] }}" name="{{ $input['name'] }}"
                            value="{{ $input['value'] }}"
                            {{ $input['required'] ?? false ? 'required' : '' }} {{ $input['disabled'] ?? false ? 'readonly' : '' }}>

                        </div>
                        @endforeach
                    </div>


                    <div class="form-group">
                        <button type="submit" class="btn btn-gradient-primary">Submit</button>
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
        const dateInput = document.getElementById('date');
        if (dateInput) {
            const today = new Date().toISOString().split('T')[0];
            dateInput.value = today;
        }

        const checkboxes = document.querySelectorAll('.fee-checkbox');
        const netFeeInput = document.getElementById('netFee');
        const concessionInput = document.getElementById('concession');
        const receivedAmtInput = document.getElementById('receivedAmt');
        const balanceInput = document.getElementById('balance');
        const lateFeeInput = document.getElementById('lateFee');
        const oldBalanceInput = document.getElementById('oldBalance');

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
            const receivedAmt = parseFloat(receivedAmtInput.value || 0);

            const netFee = totalFee + lateFee - concession + oldBalance;
            netFeeInput.value = netFee.toFixed(2);
            balanceInput.value = (netFee - receivedAmt).toFixed(2);
        }

        function hideEmptyFeeHeads() {
            const rows = document.querySelectorAll('table tbody tr');

            rows.forEach(row => {
                const feeInputs = row.querySelectorAll('input.fee-checkbox');
                let hasCheckedOrDisplayableFee = false;

                feeInputs.forEach(input => {
                    if (input.closest('div.form-check')) {
                        hasCheckedOrDisplayableFee = true;
                    }
                });

                row.style.display = hasCheckedOrDisplayableFee ? '' : 'none';
            });
        }

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                calculateNetFee();
                hideEmptyFeeHeads();
            });
        });

        [lateFeeInput, concessionInput, receivedAmtInput].forEach(input => {
            input.addEventListener('input', calculateNetFee);
        });

        calculateNetFee();
        hideEmptyFeeHeads(); // Initial check
    });
</script>
@stop


