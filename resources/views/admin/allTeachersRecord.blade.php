@extends('layouts.admin_analytics-master')

@section('headerStyle')
<link href="{{ URL::asset('plugins/footable/css/footable.bootstrap.css')}}" rel="stylesheet" type="text/css">

<!-- DataTables -->
<link href="{{ URL::asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Responsive datatable examples -->
<link href="{{ URL::asset('plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('content')

<!-- Flash Messages -->
@include('layouts.partials.flash-messages')

<div class="row m-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">

                <h4 class="mt-0 header-title">All Classes</h4>
                <p class="text-muted mb-3">You can view or edit Teachers and their classes & subjects..
                </p>

                <div class="table-responsive">
                    <table id="datatable" class="table">
                        <thead class="thead-light">
                        <tr>
                            <th>S.No.</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Serial No.</th>
                            <th>Father Name</th>
                            <th>Mother Name</th>
                            <th>Date of Birth</th>
                            <th>Date of Joining</th>
                            <th>Address</th>
                            <th>Mobile</th>
                            <th>RFID</th>
                            <th>Email</th>
                            <th>Sub 1</th>
                            <th>Sub 2</th>
                            <th>Sub 3</th>
                            <th>Sub 4</th>
                            <th>Sub 5</th>
                            <th>Sub 6</th>
                            <th>Sub 7</th>
                            <th>Sub 8</th>
                            <th>Sub 9</th>
                            <th>Sub 10</th>
                            <th>Sub 11</th>
                            <th>Sub 12</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $i=1;
                        @endphp
                        @foreach ($teachers as $teacher)

                        <tr>
                            <td>{{$i}}</td>
                            <td>
                                <img src="{{ Storage::disk('s3')->url('teacherImg/' . $teacher->name . '.jpg') }}" class="rounded-circle thumb-xl">
                                {{-- <img src="{{ URL::asset('assets/images/teacherImg/' . $teacher->name . '.jpg')}}"  class="rounded-circle thumb-xl"> --}}
                            </td>
                            <td>{{$teacher->name}}</td>
                            <td>{{$teacher->srNo}}</td>
                            <td>{{$teacher->fName}}</td>
                            <td>{{$teacher->mName}}</td>
                            <td>{{$teacher->dob}}</td>
                            <td>{{$teacher->doj}}</td>
                            <td>{{$teacher->address}}</td>
                            <td>{{$teacher->mobile}}</td>
                            <td>{{$teacher->rfid}}</td>
                            <td>{{$teacher->email}}</td>
                        </td>
                        <td>
                           @foreach ($subCodes as $code0)
                            @if( $code0->id == $teacher->class_code0)
                           {{$code0->class}} {{$code0->subject}}
                            @endif
                            @endforeach
                        </td>
                        <td>
                            @foreach ($subCodes as $code1)
                            @if( $code1->id == $teacher->class_code1)
                           {{$code1->class}} {{$code1->subject}}
                            @endif
                            @endforeach
                        </td>
                        <td>
                           @foreach ($subCodes as $code2)
                            @if( $code2->id == $teacher->class_code2)
                          {{$code2->class}} {{$code2->subject}}
                            @endif
                            @endforeach
                        </td>
                        <td>
                            @foreach ($subCodes as $code3)
                            @if( $code3->id == $teacher->class_code3)
                           {{$code3->class}} {{$code3->subject}}
                            @endif
                            @endforeach
                        </td>
                        <td>
                            @foreach ($subCodes as $code4)
                            @if( $code4->id == $teacher->class_code4)
                         {{$code4->class}} {{$code4->subject}}
                            @endif
                            @endforeach
                        </td>
                        <td>
                            @foreach ($subCodes as $code5)
                            @if( $code5->id == $teacher->class_code5)
                           {{$code5->class}} {{$code5->subject}}
                            @endif
                            @endforeach
                        </td>
                        <td>
                           @foreach ($subCodes as $code6)
                            @if( $code6->id == $teacher->class_code6)
                          {{$code6->class}} {{$code6->subject}}
                            @endif
                            @endforeach
                        </td>
                        <td>
                            @foreach ($subCodes as $code7)
                            @if( $code7->id == $teacher->class_code7)
                           {{$code7->class}} {{$code7->subject}}
                            @endif
                            @endforeach
                        </td>
                        <td>
                           @foreach ($subCodes as $code8)
                            @if( $code8->id == $teacher->class_code8)
                          {{$code8->class}} {{$code8->subject}}
                            @endif
                            @endforeach
                        </td>
                        <td>
                             @foreach ($subCodes as $code9)
                            @if( $code9->id == $teacher->class_code9)
                            {{$code9->class}} {{$code9->subject}}
                            @endif
                            @endforeach
                            </td>
                            <td>
                            @foreach ($subCodes as $code10)
                            @if( $code10->id == $teacher->class_code10)
                            {{$code10->class}} {{$code10->subject}}
                            @endif
                            @endforeach
                            </td>
                            <td>
                            @foreach ($subCodes as $code11)
                            @if( $code11->id == $teacher->class_code11)
                            {{$code11->class}} {{$code11->subject}}
                            @endif
                            @endforeach
                            </td>
                            <td>
                            <a href="editTeacherRecord/{{$teacher->id}}"><i class="fas fa-edit text-info font-16"></i></a>/
                            <a onclick="return confirm('Are you sure want to delete?')" href="deleteTeacherRecord/{{$teacher->id}}"><i class="fas fa-trash-alt text-danger font-16"></i></a>

                            </td>
                        </tr>
                        @php
                            $i=$i+1;
                        @endphp
                        @endforeach

                        </tbody>
                    </table><!--end /table-->
                </div><!--end /tableresponsive-->
            </div><!--end card-body-->
        </div><!--end card-->
    </div> <!-- end col -->
</div>

@endsection


@section('footerScript')

<script src="{{ URL::asset('plugins/footable/js/footable.js')}}"></script>
        <script src="{{ URL::asset('plugins/moment/moment.js')}}"></script>
        <script src="{{ URL::asset('assets/pages/jquery.footable.init.js')}}"></script>
         <!-- Required datatable js -->
         <script src="{{ URL::asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
         <script src="{{ URL::asset('plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
         <script src="{{ URL::asset('plugins/peity-chart/jquery.peity.min.js') }}"></script>
         <script src="{{ URL::asset('assets/pages/jquery.analytics_customers.init.js') }}"></script>
@stop
