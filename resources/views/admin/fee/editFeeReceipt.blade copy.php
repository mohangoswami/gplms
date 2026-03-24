@include('layouts.partials.flash-errors')
@php
$layout = Auth::guard('admin')->check() ? 'layouts.admin_analytics-master' : 'layouts.cashier-master';
@endphp

@extends($layout)

@section('headerStyle')
<link href="{{ URL::asset('plugins/footable/css/footable.bootstrap.css')}}" rel="stylesheet" type="text/css">
@stop



@section('content')

<!-- Flash Messages -->
@include('layouts.partials.flash-messages')


<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body ">
                <h2 class="mt-0 header-title">Fee Receipt</h2>
                @php
                $class = $user->grade;
                $category = $user->category;
                @endphp
                <form action={{ route('editFeeReceipt') }} method="POST" enctype="multipart/form-data">
                @csrf  <div class="form-group row m-0">
                    <div class="col-md-2">
                        <label class="">Date</label>
                        <input class="form-control" type="date"  id="date" name="date" value="{{$receiptFirst->date}}"  required>
                    </div>
                     <div class="col-md-1">
                        <label class="">Adm. No.</label>
                        <input class="form-control" type="text"  id="admission_number" name="admission_number"  value="{{$user->admission_number ?? "N/A"}}" disabled>
                    </div>
                    <div class="col-md-2">
                        <label class="">Name</label>
                    <input class="form-control" type="text"  id="editName" name="editName"  value="{{$user->name ?? "N/A"}}" disabled>
                    </div><!-- end col -->
                    <div class="col-md-1">
                        <label class="">Class</label>
                        <input class="form-control" type="text"  id="class" name="class"  value="{{$user->grade ?? "N/A"}}" disabled>
                    </div><!-- end col -->

                    <div class="col-md-1">
                        <label class="">Category</label>
                        <input class="form-control" type="text"  id="category" name="category"  value="{{$user->category->category ?? "N/A"}}" disabled>
                    </div><!-- end col -->
                    <div class="col-md-2">
                        <label class="">Father Name</label>
                        <input class="form-control" type="text"  id="fName" name="fName"  value="{{$user->fName ?? "N/A"}}" disabled>
                    </div><!-- end col -->
                    <div class="col-md-1.5">
                        <label class="">Route</label>
                        <input class="form-control" type="text"  id="route" name="route"  value="{{$user->route->routeName ?? "N/A"}}" disabled>
                    </div><!-- end col -->
                    <div class="col-md-1">
                        <label class="">Old Bal</label>
                        <input class="form-control" type="text"  id="oldBalance" name="oldBalance"  value="{{$receiptFirst->oldBalance}}" disabled>
                    </div><!-- end col -->
                </div><!-- end row -->

                <div class="form-group mb-0 mt-2 row ">

                    <div class="col-md-16 ">
                        @foreach ($uniquePaidMonths as $uniquePaidMonth)
                            @if($uniquePaidMonths==='may')
                                {{dd($uniquePaidMonths)}}
                            <div class="form-check-inline my-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="apr"  name = "apr" checked disabled data-parsley-multiple="groups" data-parsley-mincheck="2">
                                    <label class="custom-control-label" for="apr">Apr</label>
                                </div>
                            </div>
                            @endif
                        @endforeach
                        @if(isset($uniquePaidMonths['apr']))
                        <div class="form-check-inline my-2">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="apr"  name = "apr" checked disabled data-parsley-multiple="groups" data-parsley-mincheck="2">
                                <label class="custom-control-label" for="apr">Apr</label>
                            </div>
                        </div>
                        @endif
                        @if(isset($uniquePaidMonths['may']))
                        <div class="form-check-inline my-2">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="may" name = "may" checked disabled data-parsley-multiple="groups" data-parsley-mincheck="2">
                                <label class="custom-control-label" for="may">May</label>
                            </div>
                        </div>
                        @endif
                        @if(isset($uniquePaidMonths['jun']))
                        <div class="form-check-inline my-2">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="jun"  name = "jun" checked disabled data-parsley-multiple="groups" data-parsley-mincheck="2">
                                <label class="custom-control-label" for="jun">Jun</label>
                            </div>
                        </div>
                        @endif
                        @if(isset($uniquePaidMonths['jul']))
                        <div class="form-check-inline my-2">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="jul" name = "jul" checked disabled data-parsley-multiple="groups" data-parsley-mincheck="2">
                                <label class="custom-control-label" for="jul">Jul</label>
                            </div>
                        </div>
                        @endif
                        @if(isset($uniquePaidMonths['aug']))
                        <div class="form-check-inline my-2">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="aug"  name = "aug" checked disabled data-parsley-multiple="groups" data-parsley-mincheck="2">
                                <label class="custom-control-label" for="aug">Aug</label>
                            </div>
                        </div>
                        @endif
                        @if(isset($uniquePaidMonths['sep']))
                        <div class="form-check-inline my-2">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="sep"  name = "sep" checked disabled data-parsley-multiple="groups" data-parsley-mincheck="2">
                                <label class="custom-control-label" for="sep">Sep</label>
                            </div>
                        </div>
                        @endif
                        @if(isset($uniquePaidMonths['oct']))
                        <div class="form-check-inline my-2">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="oct" name = "oct" checked disabled data-parsley-multiple="groups" data-parsley-mincheck="2">
                                <label class="custom-control-label" for="oct">Oct</label>
                            </div>
                        </div>
                        @endif
                        @if(isset($uniquePaidMonths['nov']))
                        <div class="form-check-inline my-2">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="nov"  name = "nov" checked disabled data-parsley-multiple="groups" data-parsley-mincheck="2">
                                <label class="custom-control-label" for="nov">Nov</label>
                            </div>
                        </div>
                        @endif
                        @if(isset($uniquePaidMonths['dec']))
                        <div class="form-check-inline my-2">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="dec" name = "dec" checked disabled data-parsley-multiple="groups" data-parsley-mincheck="2">
                                <label class="custom-control-label" for="dec">Dec</label>
                            </div>
                        </div>
                        @endif
                        @if(isset($uniquePaidMonths['jan']))
                        <div class="form-check-inline my-2">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="jan"  name = "jan" checked disabled data-parsley-multiple="groups" data-parsley-mincheck="2">
                                <label class="custom-control-label" for="jan">Jan</label>
                            </div>
                        </div>
                        @endif
                        @if(isset($uniquePaidMonths['feb']))
                        <div class="form-check-inline my-2">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="feb" name = "feb" checked disabled data-parsley-multiple="groups" data-parsley-mincheck="2">
                                <label class="custom-control-label" for="feb">Feb</label>
                            </div>
                        </div>
                        @endif
                        @if(isset($uniquePaidMonths['mar']))
                        <div class="form-check-inline my-2">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="mar"  name = "mar" checked disabled data-parsley-multiple="groups" data-parsley-mincheck="2">
                                <label class="custom-control-label" for="mar">Mar</label>
                            </div>
                        </div>
                        @endif
                    </div>
                </div><!--end row-->
            </div><!--end card-body-->
        </div><!--end card-->
    </div><!--end col-->
</div>
<div class="row ">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">


                <div class="table-responsive">
                    <table id="datatable" class="table">
                        <thead class="thead-light">
                        <tr>
                            <th>Fee Head</th>
                            <th>Apr</th>
                            <th>May</th>
                            <th>Jun</th>
                            <th>jul</th>
                            <th>Aug</th>
                            <th>Sep</th>
                            <th>Oct</th>
                            <th>Nov</th>
                            <th>Dec</th>
                            <th>Jan</th>
                            <th>Feb</th>
                            <th>Mar</th>
                            <th>Total</th>
                        </tr>
                        </thead>
                        <tbody>
                            @php $gTotal = $receiptFirst->oldBalance; @endphp
                        @foreach ($receipts as $receipt)
                            @php $total = null; @endphp
                        <tr>

                            <td>{{$receipt->feeHead}}</td>
                            <td>
                                @isset($receipt->april)
                                    {{$receipt->april}}
                                    @php
                                    $total = $total + $receipt->april;
                                    $gTotal = $gTotal + $receipt->april;
                                    @endphp
                                @endisset
                            </td>
                            <td>
                                @isset($receipt->may)
                                    {{$receipt->may}}
                                    @php
                                    $total = $total + $receipt->may;
                                    $gTotal = $gTotal + $receipt->may;
                                    @endphp
                                @endisset
                            </td>
                            <td>
                                @isset($receipt->june)
                                    {{$receipt->june}}
                                    @php
                                    $total = $total + $receipt->june;
                                    $gTotal = $gTotal + $receipt->june;
                                    @endphp
                                @endisset
                            </td>
                            <td>
                                @isset($receipt->july)
                                    {{$receipt->july}}
                                    @php
                                    $total = $total + $receipt->july;
                                    $gTotal = $gTotal + $receipt->july;
                                    @endphp
                                @endisset
                            </td>
                            <td>
                                @isset($receipt->august)
                                    {{$receipt->august}}
                                    @php
                                    $total = $total + $receipt->august;
                                    $gTotal = $gTotal + $receipt->august;
                                    @endphp
                                @endisset
                            </td>
                            <td>
                                @isset($receipt->september)
                                    {{$receipt->september}}
                                    @php
                                    $total = $total + $receipt->september;
                                    $gTotal = $gTotal + $receipt->september;
                                    @endphp
                                @endisset
                            </td>
                            <td>
                                @isset($receipt->october)
                                    {{$receipt->october}}
                                    @php
                                    $total = $total + $receipt->october;
                                    $gTotal = $gTotal + $receipt->october;
                                    @endphp
                                @endisset
                            </td>
                            <td>
                                @isset($receipt->november)
                                    {{$receipt->november}}
                                    @php
                                    $total = $total + $receipt->november;
                                    $gTotal = $gTotal + $receipt->november;
                                    @endphp
                                @endisset
                            </td>
                            <td>
                                @isset($receipt->december)
                                    {{$receipt->december}}
                                    @php
                                    $total = $total + $receipt->december;
                                    $gTotal = $gTotal + $receipt->december;
                                    @endphp
                                @endisset
                            </td>
                            <td>
                                @isset($receipt->january)
                                    {{$receipt->january}}
                                    @php
                                    $total = $total + $receipt->january;
                                    $gTotal = $gTotal + $receipt->january;
                                    @endphp
                                @endisset
                            </td>
                            <td>
                                @isset($receipt->february)
                                    {{$receipt->february}}
                                    @php
                                    $total = $total + $receipt->february;
                                    $gTotal = $gTotal + $receipt->february;
                                    @endphp
                                @endisset
                            </td>
                            <td>
                                @isset($receipt->march)
                                    {{$receipt->march}}
                                    @php
                                    $total = $total + $receipt->march;
                                    $gTotal = $gTotal + $receipt->march;
                                    @endphp
                                @endisset
                            </td>

                            <td>{{$total}}</td>

                        </tr>
                        @endforeach

                        </tbody>
                    </table><!--end /table-->
                </div><!--end /tableresponsive-->
            </div><!--end card-body-->
        </div><!--end card-->
    </div> <!-- end col -->
</div>


<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body ">

                <input type="hidden" name="id" value="{{$id}}">
                <input type="hidden" name="user_id" value="{{$user->id}}">
                <input type="hidden"  id="oldBalance" name="oldBalance"  value="{{$receiptFirst->oldBalance ?? 0}}" >

                <div class="form-group row m-0">
                    <div class="col-md-1">
                        <label class="">Total</label>
                        <input class="form-control" type="text"  id="gTotal" name="gTotal"  value="{{$receiptFirst->total}}" >
                    </div>
                    <div class="col-md-2">
                        <label class="">Late Fee</label>
                    <input class="form-control" type="number"  id="lateFee" name="lateFee" value="{{$receiptFirst->lateFee}}" oninput="subtract()">
                    </div><!-- end col -->
                    <div class="col-md-1">
                        <label class="">Con. %</label>
                        <input class="form-control" type="text"  id="concessionP" name="concessionP"  value="{{$receiptFirst->concessionP}}" oninput="subtract()" >
                    </div><!-- end col -->

                    <div class="col-md-2">
                        <label class="">Concession</label>
                        <input class="form-control" type="number"  id="concession" name="concession" value="{{$receiptFirst->concession}}" oninput='subtract()'>
                    </div><!-- end col -->
                    <div class="col-md-2">
                        <label class="">Net Fee</label>
                        <input class="form-control" type="number"   id="netFee" name="netFee" value="{{$receiptFirst->netFee}}" >
                    </div><!-- end col -->
                    <div class="col-md-2">
                        <label class="">Received Amt.</label>
                        <input class="form-control" type="number"  id="receivedAmt" name="receivedAmt" value="{{$receiptFirst->receivedAmt}}" oninput="subtract()">
                    </div><!-- end col -->
                    <div class="col-md-2">
                        <label class="">Balance</label>
                        <input class="form-control" type="text"  id="balance" name="balance" value="{{$receiptFirst->balance}}" >
                    </div>
                </div><!-- end row -->

                <div class="form-group row m-0">
                    <div class="col-md-2">
                        <label class="">Payment Type</label>
                        <input class="form-control" type="text"  id="paymentType" name="paymentType"  value="Cash">
                    </div>
                    <div class="col-md-2">
                        <label class="">Bank Name</label>
                    <input class="form-control" type="text"  id="bankName" name="bankName">
                    </div><!-- end col -->
                    <div class="col-md-2">
                        <label class="">Cheque/DD No.</label>
                        <input class="form-control" type="text"  id="chequeNo" name="chequeNo" >
                    </div><!-- end col -->

                    <div class="col-md-2">
                        <label class="">Chq.Date</label>
                        <input class="form-control" type="text"  id="chqDate" name="chqDate">
                    </div><!-- end col -->
                    <div class="col-md-2">
                        <label class="">Remarks</label>
                        <input class="form-control" type="text"  id="remark" name="remark">
                    </div><!-- end col -->
                    <div class="col-md-2 mt-4">
                        <button type="submit" class="btn btn-gradient-primary">Update</button>
                        <a type="button"  href="/fee/allStudentsRecord" class="btn btn-gradient-danger">Cancel</a>
                    </div><!-- end col -->
                </div><!-- end row -->

        </form>
            </div><!--end card-body-->
        </div><!--end card-->
    </div><!--end col-->
</div>


@endsection


@section('footerScript')
<script>

        var gTotal1 = document.getElementById('gTotal').value;
		var lateFee1 = document.getElementById('lateFee').value;
        var concession1 = document.getElementById('concession').value;
        var netFee = document.getElementById('netFee');
        var balance = document.getElementById('balance');
        var oldBalance1 = document.getElementById('oldBalance').value;
        var receivedAmt1 = document.getElementById('receivedAmt').value;

        gTotal = parseFloat(gTotal1);
        oldBalance = parseFloat(oldBalance1);
        lateFee = parseFloat(lateFee1);
        concession = parseFloat(concession1);
        receivedAmt = parseFloat(receivedAmt1);
		var netF =   gTotal + lateFee - concession;
		netF = netF.toFixed(8);
		netFee.value = parseFloat(netF);
        balance.value = netFee.value;
function subtract() {
        var gTotal1 = document.getElementById('gTotal').value;
        var concession1 = document.getElementById('concession').value;
        var lateFee1 = document.getElementById('lateFee');
        var concession1 = document.getElementById('concession');
        var concessionP1 = document.getElementById('concessionP');
        var receivedAmt1 = document.getElementById('receivedAmt');

        gTotal = parseFloat(gTotal1);
        oldBalance = parseFloat(oldBalance1);
        concession = parseFloat(concession1.value) || 0;
        concessionP = parseFloat(concessionP1.value) || 0;
        lateFee = parseFloat(lateFee1.value) || 0;
        receivedAmt = parseFloat(receivedAmt1.value) || 0;
        var netFee = document.getElementById('netFee');
        var balance = document.getElementById('balance');
	    var result =  gTotal  - concession + lateFee;
	    result = result - ((result * concessionP)/100)
        var resultBal = result  - receivedAmt ;
        result = result.toFixed(8);
        resultBal = resultBal.toFixed(8);

    if(isNaN(result)==false){
        netFee.value = parseFloat(result) ;
    }
    if(isNaN(resultBal)==false){
        balance.value = parseFloat(resultBal) ;
    }

}
</script>

<script src="{{ URL::asset('plugins/footable/js/footable.js')}}"></script>
        <script src="{{ URL::asset('plugins/moment/moment.js')}}"></script>
        <script src="{{ URL::asset('assets/pages/jquery.footable.init.js')}}"></script>

@stop
