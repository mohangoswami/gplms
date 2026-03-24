@extends('layouts.admin_analytics-master')

@section('content')

@include('layouts.partials.flash-messages')

<!-- Edit Concession Form -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h2 class="mt-0 header-title">Edit Concession    </h2>
                <h2 class="mt-0 header-title">  {{ $concession->user->name}},  {{ $concession->user->grade}}</h2>
                <form action="{{ route('updateConcession', $concession->id) }}" method="POST">
                    @csrf

                            <!-- Concession Fee -->
                            <div class="form-group row">
                            <label for="concession_fee" class="col-md-2 col-form-label">{{ $concession->user->name}}</label>
                            <div class="col-md-4">
                                <label for="concession_fee" class="col-md-8 col-form-label">{{ $concession->feePlan->feeHead }} - {{ $concession->feePlan->value }}</label>
                                <input type="hidden" name="fee_plan_id" id="fee_plan_id" value="{{ $concession->fee_plan_id }}">

                            </div>
                        </div>
                    {{-- <!-- Select Fee Plan -->
                    <div class="form-group row">
                        <label for="fee_plan_id" class="col-md-2 col-form-label">Select Fee Plan</label>
                        <div class="col-md-6">
                            <select name="fee_plan_id" id="fee_plan_id" class="form-control select2" required>
                                @foreach ($feePlans as $feePlan)
                                    <option value="{{ $feePlan->id }}"
                                        {{ $feePlan->id == $concession->fee_plan_id ? 'selected' : '' }}>
                                        {{ $feePlan->feeHead }} - {{ $feePlan->value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div> --}}

                    <!-- Concession Type -->
                    <div class="form-group row">
                        <label for="concession_type" class="col-md-2 col-form-label">Concession Type</label>
                        <div class="col-md-4">
                            <select name="concession_type" id="concession_type" class="form-control select2" required>
                                <option value="Amount" {{ $concession->concession_type == 'Amount' ? 'selected' : '' }}>Amount</option>
                            </select>
                        </div>
                    </div>

                    <!-- Concession Fee -->
                    <div class="form-group row">
                        <label for="concession_fee" class="col-md-2 col-form-label">Fee Concession</label>
                        <div class="col-md-4">
                            <input type="number" class="form-control" name="concession_fee" id="concession_fee"
                                value="{{ $concession->concession_fee }}" required>
                        </div>
                    </div>

                    <!-- Reason -->
                    <div class="form-group row">
                        <label for="reason" class="col-md-2 col-form-label">Reason</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="reason" id="reason"
                                value="{{ $concession->reason }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-gradient-primary">Update Concession</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
