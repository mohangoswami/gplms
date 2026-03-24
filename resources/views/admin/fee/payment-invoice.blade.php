@php
$layout = Auth::guard('admin')->check() ? 'layouts.admin_analytics-master' : 'layouts.cashier-master';
@endphp

@extends($layout)

@section('title', 'Metrica - Admin & Dashboard Template')

@section('headerStyle')
<style>
    html, body {
        margin: 0;
        padding: 0;
        height: auto;
    }

    @media print {
        @page {
            size: A4 portrait;
            margin: 10mm; /* Adjust as needed */
        }

        body {
            margin: 0;
            padding: 0;
            font-size: 12px;
        }

        .no-print {
            display: none !important;
        }

        .printable-area {
            width: 100%;
            height: auto;
            overflow: hidden;
        }

        /* Ensure the row and columns are aligned properly */
        .row {
            display: flex !important; /* Ensure flexbox layout during printing */
            flex-wrap: nowrap; /* Prevent wrapping */
        }

        .col-md-6 {
            flex: 0 0 50%; /* Ensure both columns take up 50% of the row */
            max-width: 50%;
        }

        /* Specific alignment for left and right content */
        .align-items-center {
            display: flex;
            align-items: center;
        }

        .justify-content-end {
            display: flex;
            justify-content: flex-end;
        }

        .contact-detail {
            text-align: right; /* Align text to the right for contact details */
        }

        img.logo-sm {
            max-height: 30px;
        }
            /* For table borders */



    }
</style>


@stop

@section('content')

<!-- Flash Messages -->
@include('layouts.partials.flash-messages')


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
    $remark = $print['remark'];
    @endphp
@endforeach
     <!-- Print and Cancel Buttons -->
     <div class="row no-print mt-4">
        <div class="col-md-12 text-center">
            <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#yourModal">Print</button>
            <a href="/fee/allStudentsRecord" class="btn btn-gradient-danger">Cancel</a>
        </div>
    </div>

<div id="invoice" class="container-fluid">
    <div class="modal" id="yourModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <!-- end page title end breadcrumb -->
            <div class="printable-area bold-border">
                <div class="col-lg-8 mx-auto">
                    <div class="card">
                        <div class="card-body invoice-head">
                            <div class="row d-flex align-items-center justify-content-between">
                                <!-- Logo and Address -->
                                <div class="col-md-6 d-flex align-items-center">
                                    <img src="{{ URL::asset('assets/images/gpl_logo2.png')}}" alt="logo-small" class="logo-sm mr-3" height="60">
                                    <div>
                                        <h3 class="m-0">G P L M School</h3>
                                        <span class="text-muted mb-0">F-48, Industrial Area<br>Haridwar, 249401, Uttrakhand.</span>
                                    </div>
                                </div><!--end col-->

                                <!-- Contact Details -->
                                <div class="col-md-6 d-flex justify-content-end">
                                    <ul class="list-inline mb-0 contact-detail">
                                        <li class="list-inline-item align-items-center">
                                            <i class="mdi mdi-web mr-0"></i>
                                            <span class="text-muted">www.gplmschool.com</span>
                                        </li>
                                        <li class="list-inline-item align-items-center">
                                            <i class="mdi mdi-phone mr-0"></i>
                                            <span class="text-muted">+91 7983072625</span>
                                        </li>
                                    </ul>
                                </div><!--end col-->
                            </div><!--end row-->
                        </div><!--end card-body-->



                        <div class="card-body">
                            <div class="row d-flex align-items-center flex-nowrap">
                                <!-- Invoice Number and Regn. No -->
                                <div class="col-md-4 text-left">
                                    <h6 class="m-0"><b>Invoice No :</b> #{{$receiptId}}</h6>
                                    <h6 class="m-0"><b>Regn. No :</b> {{$user->admission_number}}</h6>
                                </div><!--end col-->

                                <!-- Student Name and Class -->
                                <div class="col-md-4 text-center">
                                    <h6 class="mt-0"><b>Student Name :</b> {{$user->name}}</h6>
                                    <h6 class="mt-0"><b>Class :</b> {{$user->grade}}</h6>
                                </div><!--end col-->

                                <!-- Father's Name and Date -->
                                <div class="col-md-4 text-right">
                                    <h6 class="m-0"><b>Father Name :</b> {{$user->fName}}</h6>
                                    <h6 class="mb-0"><b>Date :</b> {{$date ?? 'N/A'}}</h6>
                                </div><!--end col-->
                            </div><!--end row-->


                            <div class="row">
                                <div>
                                    <h6 class="mb-0"> Fee For Month(s) : {{ $print['paidMonths'] }}</h6>
                                </div>
                            </div><!--end row-->
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="table-responsive project-invoice">
                                        <!-- Fee Table -->
                                        <div class="table-responsive mt-4">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Description</th>
                                                        <th>Paid Months</th>
                                                        <th>Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                    $months = ['apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar'];
                                                    $feeTotal = null;
                                                    @endphp
                                                    @foreach ($receipts as $index => $print)

                                                        <tr>
                                                            <td>{{ $index + 1 }}</td>
                                                            <td>{{ $print['feeHead'] }}</td>
                                                            @php    $fee = null;
                                                                    $i = null;
                                                            @endphp
                                                            @foreach ($months as $month)
                                                                @isset($print[$month])
                                                                @php    $fee = $print[$month];
                                                                        $i++;
                                                                @endphp
                                                                @endisset
                                                            @endforeach
                                                            <td> {{$fee}} X {{$i}} </td>

                                                            <td>{{ $print['total'] }}</td>
                                                            @php $feeTotal += $print['total']; @endphp
                                                        </tr>
                                                    @endforeach
                                                    <tr>
                                                        <td colspan="3"><b>Total</b></td>
                                                        <td><b> {{$feeTotal}}</b></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <!-- Footer Details -->
                                    <div class="row mt-4">
                                        <div class="col-md-6">
                                            <p><b>Payment Mode:</b> {{$prints->first()['paymentType']}}</p>
                                            @if ($prints->first()['bankName'])
                                                <p><b>Bank Name:</b> {{$prints->first()['bankName']}}</p>
                                            @endif
                                            @if ($prints->first()['chequeNo'])
                                                <p><b>Cheque No:</b> {{$prints->first()['chequeNo']}}</p>
                                                <p><b>Cheque Date:</b> {{$prints->first()['chqDate']}}</p>
                                            @endif
                                        </div>
                                        <div class="col-md-6 text-right">
                                            @if ($prints->first()['oldBalance'] > 0)
                                            <p><b>Old Balance (+) :</b> {{$prints->first()['oldBalance']}}</p>
                                            @endif
                                            @if ($prints->first()['lateFee'] > 0)
                                            <p><b>Late Fee (+) :</b> {{$prints->first()['lateFee']}}</p>
                                            @endif
                                            @if ($prints->first()['concession'] > 0)
                                            <p><b>Concession (-) :</b> {{$prints->first()['concession']}}</p>
                                            @endif
                                            <p><b>Grand Total = :</b> {{$prints->first()['netFee']}}</p>
                                            <p><b>Received (-) :</b> {{$prints->first()['receivedAmt']}}</p>
                                            <p><b>Balance = :</b> {{$prints->first()['balance']}}</p>
                                        </div>
                                    </div>
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
            </div>
    </div><!-- container -->
</div>
@stop

