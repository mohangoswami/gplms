@extends('layouts.student_master')

@section('headerStyle')
 <!-- DataTables -->
<link href="{{ URL::asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Responsive datatable examples -->
<link href="{{ URL::asset('plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('content')


<!-- Flash Messages -->
@include('layouts.partials.flash-messages')


@isset($failed)
@if($failed!=null)
<div class="alert alert-danger b-round  mt-3 ">
    {{ $failed }}
</div>
@endif
@endisset
<div class="row mt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body ">
                <h2 class="mt-0 header-title">Fee Card</h2>
                {{-- @foreach ($users as $user) --}}
                @php
                $class = $user->grade;
                $category = $user->category;
              @endphp
                <form action={{ route('post_receipt') }} method="POST" enctype="multipart/form-data">
                    @csrf
                {{-- <input type="hidden" name="id" value="{{$id}}"> --}}
                <div class="form-group row m-0">
                    <div class="col-md-1">
                        <label class="">Adm. No.</label>
                        <input class="form-control" type="text"  id="srNo" name="srNo"  value="{{$user->srNo ?? "N/A"}}" disabled>
                    </div>
                    <div class="col-md-2">
                        <label class="">Name</label>
                    <input class="form-control" type="text"  id="editName" name="editName"  value="{{$user->name ?? "N/A"}}" disabled>
                    </div><!-- end col -->
                    <div class="col-md-1">
                        <label class="">Class</label>
                        <input class="form-control" type="text"  id="class" name="class"  value="{{$user->grade ?? "N/A"}}" disabled>
                    </div><!-- end col -->

                    <div class="col-md-2">
                        <label class="">Category</label>
                        <input class="form-control" type="text"  id="category" name="category"  value="{{$user->category->category ?? "N/A"}}" disabled>
                    </div><!-- end col -->
                    <div class="col-md-2">
                        <label class="">Father Name</label>
                        <input class="form-control" type="text"  id="fName" name="fName"  value="{{$user->fName ?? "N/A"}}" disabled>
                    </div><!-- end col -->
                    <div class="col-md-2">
                        <label class="">Route</label>
                        <input class="form-control" type="text"  id="route" name="route"  value="{{$user->route->routeName ?? "N/A"}}" disabled>
                    </div><!-- end col -->

                </div><!-- end row -->


                {{-- @endforeach --}}
            </div><!--end card-body-->
        </div><!--end card-->
    </div><!--end col-->
</div>

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
                                <th>Months</th>
                                <th>Old Bal.</th>
                                {{-- <th>Total Fees</th> --}}
                                <th>Late Fees</th>
                                <th>Concession</th>
                                <th>Net Fee</th>
                                <th>rec. Amt.</th>
                                <th>Balance</th>
                                <th>View Reciept</th>
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
                                <td>
                                    @isset($receipt->apr) Apr @endisset
                                    @isset($receipt->may) May @endisset
                                    @isset($receipt->jun) Jun @endisset
                                    @isset($receipt->jul) Jul @endisset
                                    @isset($receipt->aug) Aug @endisset
                                    @isset($receipt->sep) Sep @endisset
                                    @isset($receipt->oct) Oct @endisset
                                    @isset($receipt->nov) Nov @endisset
                                    @isset($receipt->dec) Dec @endisset
                                    @isset($receipt->jan) Jan @endisset
                                    @isset($receipt->feb) Feb @endisset
                                    @isset($receipt->mar) Mar @endisset
                                </td>
                                <td>{{$receipt->oldBalance}}</td>
                                {{-- <td>{{$receipt->total}}</td> --}}
                                <td>{{$receipt->lateFee}}</td>
                                <td>{{$receipt->concession}}</td>
                                <td>{{$receipt->netFee}}</td>
                                <td>{{$receipt->receivedAmt}}</td>

                                <td>{{$receipt->balance}}</td>
                                <td>
                                    <a href="/student/printReceipt/{{$receipt->receiptId}}" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="Print" data-trigger="hover"><i class="fas fa-print text-info font-16"></i></a>
                                </td>
                            </tr>

                            @php $receiptIdTrue = $receipt->receiptId; @endphp
                            @endif
                            @endforeach
                            {{-- <tr>
                                <td><b>Total</b></td>
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
                            </tr> --}}
                            </tbody>
                        </table><!--end /table-->
                    </div><!--end /tableresponsive-->
            </div>
        </div>
    </div>
</div>

@endsection


@section('footerScript')
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
