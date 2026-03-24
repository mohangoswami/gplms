@php
$layout = Auth::guard('admin')->check() ? 'layouts.admin_analytics-master' : 'layouts.cashier-master';
@endphp

@extends($layout)

@section('headerStyle')
<link href="{{ URL::asset('plugins/footable/css/footable.bootstrap.css')}}" rel="stylesheet" type="text/css">

<!-- DataTables -->
<link href="{{ URL::asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Responsive datatable examples -->
<link href="{{ URL::asset('plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

@stop

@section('content')
@if (session('status'))
    <div class="alert alert-success b-round mt-3 ">
        {{ session('status') }}
    </div>
@endif
@if (session('failed'))
<div class="alert alert-danger b-round  mt-3 ">
    {{ session('failed') }}
</div>
@endif
@if (session('delete'))
<div class="alert alert-warning b-round  mt-3">
    {{ session('delete') }}
</div>
@endif
<div class="row mt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body ">
                <h2 class="mt-0 header-title">Day Book</h2>

                <form action={{ route('searchDaybook') }} method="POST" enctype="multipart/form-data">
                    @csrf
                <div class="form-group row m-0">
                    <div class="form-group row">
                        <label for="from" class="col-md-4 col-form-label text-md-right">{{ __('From') }}</label>

                        <div class="col-md-8">
                            <input id="from" type="date" class="form-control @error('from') is-invalid @enderror" name="from" value="{{ old('to') }}" required >

                            @error('from')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row m-0">
                        <div class="form-group row">
                            <label for="to" class="col-md-4 col-form-label text-md-right">{{ __('To') }}</label>

                            <div class="col-md-8">
                                <input id="to" type="date" class="form-control @error('to') is-invalid @enderror" name="to" value="{{ old('from') }}" required >

                                @error('to')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                </div><!-- end row -->
                <div class="form-group row mb-0">
                    <div class="col-md-6 offset-md-4">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Search') }}
                        </button>
                    </div>
                </div>

            </div><!--end card-body-->
        </div><!--end card-->
    </div><!--end col-->
</div>


@isset($receipts)

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body ">
                    <div class="table-responsive">
                        <table id="table1" class="table">
                            <thead class="thead-light">
                            <tr>
                                <th>Date</th>
                                <th>Rec. No.</th>
                                <th>Adm. No.</th>
                                <th>Class</th>
                                <th>Name</th>
                                <th>F Name</th>
                                <th>For Months</th>
                                <th>Old Bal.</th>
                                <th>Total Fees</th>
                                <th>Late Fees</th>
                                <th>Concession</th>
                                <th>Net Fee</th>
                                <th>rec. Amt.</th>
                                <th>Type</th>
                                <th>Balance</th>
                                <th>Edit</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                            $receiptIdTrue = null;
                            $total = null;
                            @endphp
                            @foreach ($receipts as $receipt)
                                @if($receipt->receiptId != $receiptIdTrue )
                                @php $total += $receipt->receivedAmt; @endphp
                            <tr>

                                <td>{{$receipt->date}}</td>
                                <td>{{$receipt->receiptId}}</td>
                                <td>{{ \App\User::where(['id' => $receipt->user_id])->pluck('admission_number')->first() }}
                                </td>
                                <td>{{ \App\User::where(['id' => $receipt->user_id])->pluck('grade')->first() }}
                                </td>
                               <td>{{ \App\User::where(['id' => $receipt->user_id])->pluck('name')->first() }}
                                   </td>
                               <td>{{ \App\User::where(['id' => $receipt->user_id])->pluck('fName')->first() }}
                                   </td>
                                <td>

                                    @isset($receipt->jan) Jan @endisset
                                    @isset($receipt->feb) Feb @endisset
                                    @isset($receipt->mar) Mar @endisset
                                    @isset($receipt->apr) Apr @endisset
                                    @isset($receipt->may) May @endisset
                                    @isset($receipt->jun) Jun @endisset
                                    @isset($receipt->jul) Jul @endisset
                                    @isset($receipt->aug) Aug @endisset
                                    @isset($receipt->sep) Sep @endisset
                                    @isset($receipt->oct) Oct @endisset
                                    @isset($receipt->nov) Nov @endisset
                                    @isset($receipt->dec) Dec @endisset
                                </td>
                                <td>{{$receipt->oldBalance}}</td>
                                <td>{{$receipt->total}}</td>
                                <td>{{$receipt->lateFee}}</td>
                                <td>{{$receipt->concession}}</td>
                                <td>{{$receipt->netFee}}</td>
                                <td>{{$receipt->receivedAmt}}</td>

                                <td>{{$receipt->paymentMode}}</td>
                                <td>{{$receipt->balance}}</td>
                                <td>
                                    <a href="/fee/editFeeReceipt/{{$receipt->receiptId}}" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="Edit" data-trigger="hover"><i class="fas fa-edit text-info font-16"></i></a>
                                </td>
                            </tr>

                            @php $receiptIdTrue = $receipt->receiptId; @endphp
                            @endif
                            @endforeach
                            <tr>
                                <td><b>Total</b></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><b>{{$total}}</b></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            </tbody>
                        </table><!--end /table-->
                    </div><!--end /tableresponsive-->
            </div>
        </div>
    </div>
</div>
@endisset
@endsection


@section('footerScript')
<script>
    document.getElementById('from').valueAsDate = new Date();
    document.getElementById('to').valueAsDate = new Date();
</script>


<script>

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
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.print.min.js"></script>


        <script src="{{ URL::asset('plugins/footable/js/footable.js')}}"></script>
        <script src="{{ URL::asset('plugins/moment/moment.js')}}"></script>
        <script src="{{ URL::asset('assets/pages/jquery.footable.init.js')}}"></script>
         <!-- Required datatable js -->
         <script src="{{ URL::asset('plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
         <script src="{{ URL::asset('plugins/peity-chart/jquery.peity.min.js') }}"></script>
         <script src="{{ URL::asset('assets/pages/jquery.analytics_customers.init.js') }}"></script>


@stop
