@extends('layouts.cashier-master')

@section('headerStyle')
<link href="{{ URL::asset('plugins/footable/css/footable.bootstrap.css')}}" rel="stylesheet" type="text/css">

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Cashier Dashboard') }}</div>
                <div class="card-body">
                    Welcome, {{ Auth::guard('cashier')->user()->name }} !
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
