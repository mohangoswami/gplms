@extends('layouts.admin_analytics-master')

@section('title', 'Metrica - Admin & Dashboard Template')

@section('headerStyle')
<style>
     html, body {
    height:80%;
    margin: 0 !important;
    padding: 0 !important;
    overflow: hidden;
  }
</style>
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

<div id = "mainPage"class="container-fluid">
                    <!-- Page-Title -->
                    <div class="row">
                        <div class="col-sm-12">

                             @component('common-components.breadcrumb')
                                 @slot('title') Invoice @endslot
                                 @slot('item1') Metrica @endslot
                                 @slot('item2') Payments @endslot
                                 @endcomponent

                        </div><!--end col-->
                    </div>

                    <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#yourModal">Print</button>
                    <a type="button"  href="/fee/allStudentsRecord" class="btn btn-gradient-danger">Cancel</a>

                <div class="modal" id="yourModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <!-- end page title end breadcrumb -->
                    <div class="row">
                        <div class="col-lg-8 mx-auto">
                            <div class="card">
                                <div class="card-body invoice-head">
                                    <div class="row">
                                        <div class="col-md-4 align-self-center">
                                            <div class="media">
                                                <img src="{{ URL::asset('assets/images/logo-sm.png')}}" alt="logo-small" class="logo-sm mr-2" height="30">
                                                <div class="media-body align-self-center">
                                                    <h3 class="m-0">G P L M School</h3>
                                                    <div class="pl-1">
                                                        <p class="text-muted mb-0">F-48, Industrial Area<br>
                                                        Haridwar, 249401, Uttrakhand.</p>
                                                    </div>
                                                </div>
                                            </div>

                                        </div><!--end col-->
                                        <div class="col-md-8">

                                            <ul class="list-inline mb-0 contact-detail float-right">
                                                <li class="list-inline-item">
                                                    <div class="pl-3">
                                                        <i class="mdi mdi-web"></i>
                                                        <p class="text-muted mb-0">www.gplmschool.com</p>
                                                    </div>
                                                </li>
                                                <li class="list-inline-item">
                                                    <div class="pl-3">
                                                        <i class="mdi mdi-phone"></i>
                                                        <p class="text-muted mb-0">+91 79083072625</p>
                                                    </div>
                                                </li>

                                            </ul>
                                        </div><!--end col-->
                                    </div><!--end row-->
                                </div><!--end card-body-->
                                @foreach ($prints as $print)
                                    @php
                                        $date = $print['date'];
                                        $receiptId = $print['receiptId'];
                                        $paidMonths = $print['paidMonths'];
                                        $gTotal = $print['gTotal'];
                                        $oldBalance = $print['oldBalance'];
                                        $feeHead = $print['feeHead'] ?? 'N/A';
                                        $gTotal = $print['gTotal'];
                                        $lateFee = $print['lateFee'];
                                        $concession = $print['concession'];
                                        $totalConcession =  $concession;
                                        $netFee = $print['netFee'];
                                        $receivedAmt = $print['receivedAmt'];
                                        $balance = $print['balance'];
                                        $paymentType = $print['paymentType'];
                                        $bankName = $print['bankName'];
                                        $chequeNo = $print['chequeNo'];
                                        $chqDate = $print['chqDate'];
                                        $remark = $print['remark'];                                    @endphp
                                @endforeach
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 mb-4">
                                            <h6 class="m-0"><b>Invoice No :</b> #{{$user->receipts->last()->receiptId ?? 'N/A'}}</h6>
                                            <h6 class="m-0"><b>Regn. No :</b> {{$user->srNo}}</h6>

                                            </div><!--end col-->
                                        <div class="col-md-3 mb-3 ml-auto align-self-center">
                                            <h6 class="mt-0"><b>Student Name :</b> {{$user->name}}</h6>
                                            <h6 class="mt-0"><b>Class :</b> {{$user->grade}}</h6>

                                        </div><!--end col-->
                                        <div class="col-md-3 ml-auto mb-4">
                                            <h6 class="m-0"><b>Father Name :</b> {{$user->fName}}</h6>
                                            <h6 class="mb-0"><b>Date :</b> {{$user->receipts->last()->date ?? 'N/A'}}</h6>

                                        </div><!--end col-->

                                    </div><!--end row-->
                                    <div class="row">
                                        <div>
                                    {{-- <p class="m-0"><b>Fee For Month(s) : </b> --}}
                                        <h6 class="mb-0"> Fee For Month(s) : {{ $print['paidMonths'] }}</h6>

                                        {{-- @foreach ($paidMonths as $paidMonth )
                                        <h6 class="mb-0"> {{$paidMonth}},</h6>

                                        @endforeach --}}
                                    </p>
                                </div>
                                    @if($oldBalance!=0)
                                    <div class="col-md-4">
                                        <p class="border-0 font-11 float-right"><b>Old balance - {{$oldBalance}}</b></p>

                                    </div>
                                    @endif
                                </div><!--end row-->

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="table-responsive project-invoice">
                                                <table class="table table-bordered mb-0">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Description</th>
                                                            <th></th>
                                                            <th>Amount</th>
                                                        </tr><!--end tr-->
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                        $total = null;
                                                        $i = 1;
                                                        @endphp
                                                        @foreach ($prints as $print)
                                                        @if(isset($print))

                                                        <tr>
                                                            <td>{{$i}}</td>
                                                            <td>{{$print['feeHead']}}</td>
                                                            <td></td>
                                                        </td>
                                                            <td>{{$print['value']}}</td>
                                                            @php $total += $print['value']; @endphp
                                                        </tr><!--end tr-->
                                                        @endif
                                                        @php
                                                        $i++;
                                                        @endphp
                                                        @endforeach
                                                        <tr>
                                                            <th colspan="0" class="border-0"></th>
                                                            <td class="border-0 font-14">@if(isset($paymentType))Payment Mode - {{$paymentType}}@endif</td>
                                                            <td class="border-0 font-14"><b>Total</b></td>
                                                            <td class="border-0 font-14"><b>{{$total}}</b></td>
                                                        </tr><!--end tr-->

                                                        <tr>
                                                            <th colspan="0" class="border-0"></th>
                                                            <td class="border-0 font-14">@if(isset($bankName))bank Name - {{$bankName}}@endif</td>
                                                            @if($lateFee!=0)
                                                            <td class="border-0 font-14"><b>(+) late Fee</b></td>
                                                            <td class="border-0 font-14"><b>{{$lateFee}}</b></td>
                                                            @endif
                                                        </tr><!--end tr-->

                                                        <tr>
                                                            <th colspan="0" class="border-0"></th>
                                                            <td class="border-0 font-14">@if(isset($chequeNo))Cheq No. {{$chequeNo}}, dt. {{$chqDate}}@endif</td>
                                                            <td class="border-0 font-11 "><b> Grand Total</b></td>
                                                            <td class="border-0 font-11 "><b>{{$gTotal}}</b></td>
                                                        </tr><!--end tr-->
                                                        @if($totalConcession != null)
                                                        <tr>
                                                            <th colspan="0" class="border-0"></th>
                                                            <td class="border-0 font-14">@if(isset($remark))<b>Remarks - {{$remark}}</b>@endif</td>
                                                            <td class="border-0 font-14"><b>(-) Concession</b></td>
                                                            <td class="border-0 font-14"><b>{{$totalConcession}}</b></td>
                                                        </tr><!--end tr-->
                                                        @endif
                                                        <tr>
                                                            <th colspan="2" class="border-0"></th>
                                                            <td class="border-0 font-14"><b>Received</b></td>
                                                            <td class="border-0 font-14"><b>{{$receivedAmt}}</b></td>
                                                        </tr><!--end tr-->
                                                        <tr>
                                                            <th colspan="0" class="border-0"></th>
                                                            <td class="border-0 font-14">Auth. Signature - </td>
                                                            <td class="border-0 font-14"><b>Balance</b></td>
                                                            @if($balance!=0)
                                                            <td class="border-0 font-14"><b>{{$balance}}</b></td>
                                                            @else
                                                            <td class="border-0 font-14"><b>Nil</b></td>
                                                            @endif
                                                        </tr><!--end tr-->

                                                       </tbody>
                                                </table><!--end table-->
                                            </div>  <!--end /div-->
                                        </div>  <!--end col-->
                                    </div><!--end row-->

                                  <!--  <div class="row justify-content-center">
                                         <!--<div class="col-lg-6">
                                            <h5 class="mt-4">Terms And Condition :</h5>
                                           <ul class="pl-3">
                                                <li><small>All accounts are to be paid within 7 days from receipt of invoice. </small></li>
                                                <li><small>To be paid by cheque or credit card or direct payment online.</small></li>
                                            </ul>
                                        </div> <!--end col--
                                        <div class="col-lg-6 m-4 align-self-end">
                                            <div class="w-25 float-left">
                                                <p class="border-top">Auth. Signature</p>
                                            </div>
                                        </div><!--end col--
                                    </div><!--end row--
                                    <hr>-->
                                    <div class="row d-flex justify-content-center">
                                     <!--   <div class="col-lg-12 col-xl-6 ml-auto align-self-center">
                                            <div class="text-center text-muted"><small>Thank you very much for doing business with us. Thanks !</small></div>
                                        </div><!--end col-->
                                        <div class="col-lg-12 col-xl-4">
                                            <div class="float-right d-print-none">
                                                <a href="#"  onclick="window.print()" class="btn btn-gradient-primary"><i class="fa fa-print"></i> Print</a>
                                                <a type="button"  href="/fee/allStudentsRecord" class="btn btn-gradient-danger">Close</a>
                                            </div>
                                        </div><!--end col-->
                                    </div><!--end row-->
                                </div><!--end card-body-->
                            </div><!--end card-->
                        </div><!--end col-->
                    </div><!--end row-->
                </div><!-- container -->
            </div>
@stop

@section('footerScript')

<script type='text/javascript'>
    function prinReceipt(){


window.print();
    }
</script>
