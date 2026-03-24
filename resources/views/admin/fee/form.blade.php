


@php
$layout = Auth::guard('admin')->check() ? 'layouts.admin_analytics-master' : 'layouts.cashier-master';
@endphp

@extends($layout)

@section('headerStyle')
<link href="{{ URL::asset('plugins/footable/css/footable.bootstrap.css')}}" rel="stylesheet" type="text/css">

@stop
@section('content')

<div class="row mt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body ">
                <h2 class="mt-0 header-title">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            {{ $errors->first('failed') }}
                        </div>
                    @endif
                </h2>



            </div><!--end card-body-->
        </div><!--end card-->
    </div><!--end col-->
</div>


@endsection


@section('footerScript')


@stop
