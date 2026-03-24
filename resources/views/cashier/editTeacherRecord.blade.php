@extends('layouts.cashier-master')

@section('headerStyle')
<link href="{{ URL::asset('plugins/dropify/css/dropify.min.css')}}" rel="stylesheet">


<link href="{{ URL::asset('plugins/footable/css/footable.bootstrap.css')}}" rel="stylesheet" type="text/css">
@stop

@section('content')

<!-- Flash Messages -->
@include('layouts.partials.flash-messages')

<div class="row">
    <div class="col-lg-6">
        <div class="card m-5">
            <div class="card-body">
                <h3>Edit Live Class</h3>
                @foreach ($teachers as $teacher)
                <img src="{{ Storage::disk('s3')->url('teacherImg/' . $teacher->name . '.jpg') }}" class="rounded-circle thumb-xl">

                <h4 class="mt-0 header-title">Name- {{$teacher->name}} </h4>
                <p class="text-muted mb-3">Enter class and subject name <br>(class name type must be same for all same classes) </p>
                <form action={{ route('post_cashierEditTeacherRecord') }} method="POST" enctype="multipart/form-data">
                    @csrf
                <input type="hidden" name="id" value="{{$id}}">
                <div class="">
                <div class="">
                    <label class="my-3">Edit Name</label>
                <input class="form-control" type="text"  id="editName" name="editName"  value="{{$teacher->name}}" required>
                </div><!-- end col -->
                <div class="">
                    <label class="my-3">Edit Serial No.</label>
                <input class="form-control" type="text"  id="srNo" name="srNo"  value="{{$teacher->srNo}}" required>
                </div><!-- end col -->
                <div class="">
                    <label class="my-3">Edit Father Name</label>
                <input class="form-control" type="text"  id="fName" name="fName"  value="{{$teacher->fName}}" required>
                </div><!-- end col -->
                <div class="">
                    <label class="my-3">Edit Mother Name</label>
                <input class="form-control" type="text"  id="mName" name="mName"  value="{{$teacher->mName}}" required>
                </div><!-- end col -->
                <div class="">
                    <label class="my-3">Edit Date of Birth</label>
                <input class="form-control" type="date"  id="dob" name="dob"  value="{{$teacher->dob}}" required>
                </div><!-- end col -->
                <div class="">
                    <label class="my-3">Edit Date of Joining</label>
                <input class="form-control" type="date"  id="doj" name="doj"  value="{{$teacher->doj}}" required>
                </div><!-- end col -->
                <div class="">
                    <label class="my-3">Edit Address</label>
                <input class="form-control" type="text"  id="address" name="address"  value="{{$teacher->address}}" required>
                </div><!-- end col -->
                <div class="">
                    <label class="my-3">Edit Mobile</label>
                <input class="form-control" type="text"  id="mobile" name="mobile"  value="{{$teacher->mobile}}" required>
                </div><!-- end col -->
                <div class="">
                    <label class="my-3">Edit Password</label>
                    <input class="form-control" type="password" id="editPassword" name="editPassword" placeholder="Enter new password">
                </div>
                <div class="">
                    <label class="my-3">Edit RFID</label>
                <input class="form-control" type="text"  id="rfid" name="rfid"  value="{{$teacher->rfid}}" required>
                </div><!-- end col -->
                <div class="">
                        <label class="my-3">Edit Email</label>
                        <input class="form-control" type="text"  id="editEmail" name="editEmail"  value="{{$teacher->email}}" required>
                    </div><!-- end col -->

                    <div class="col-md-6">
                        <label class="mb-3">Subject 1</label>
                        <select id="editCode0" name="editCode0" class="select2 form-control mb-3 custom-select" style="width: 100%; height:36px;" >
                            @php
                            $isCode0 = FALSE;
                        @endphp
                            @foreach ($classes as $code0)

                            @if( $code0->id == $teacher->class_code0)
                                @php
                                $isCode0 = TRUE;
                                @endphp
                                  <option value="{{$code0->id}}"> {{$code0->class}} {{$code0->subject}}</option>
                                @endif

                            @endforeach
                            <option value=""></option>
                            @if($isCode0 == False)
                            <option value=""></option>
                            @endif
                            @isset($classes)
                                @foreach ($classes as $class)
                                    <option value="{{$class->id}}">{{$class->class}} {{$class->subject}}</option>
                                @endforeach
                        </select>
                            @endisset
                    </div><!-- end col -->

                    <div class="col-md-6">
                        <label class="mb-3">Subject 2</label>
                        <select id="editCode1" name="editCode1" class="select2 form-control mb-3 custom-select" style="width: 100%; height:36px;">
                            @foreach ($classes as $code1)
                                    @php
                                    $isCode1 = FALSE;
                                @endphp
                            @if( $code1->id == $teacher->class_code1)
                                @php
                                $isCode1 = TRUE;
                                @endphp
                                <option value="{{$code1->id}}"> {{$code1->class}} {{$code1->subject}}</option>
                                @endif

                            @endforeach
                            <option value=""></option>
                            @if($isCode1 == False)
                            <option value=""></option>
                            @endif
                            @isset($classes)
                                @foreach ($classes as $class)
                                    <option value="{{$class->id}}">{{$class->class}} {{$class->subject}}</option>
                                @endforeach
                        </select>
                            @endisset
                    </div><!-- end col -->
                    <div class="col-md-6">
                        <label class="mb-3">Subject 3</label>
                        <select id="editCode2" name="editCode2" class="select2 form-control mb-3 custom-select" style="width: 100%; height:36px;">
                            @php
                            $isCode2 = FALSE;
                            @endphp
                          @foreach ($classes as $code2)
                            @if( $code2->id == $teacher->class_code2)
                                @php
                                $isCode2 = TRUE;
                                @endphp
                                <option value="{{$code2->id}}"> {{$code2->class}} {{$code2->subject}}</option>
                                @endif
                            @endforeach
                            <option value=""></option>
                            @if($isCode2 == False)
                            <option value=""></option>
                            @endif
                            @isset($classes)
                                @foreach ($classes as $class)
                                    <option value="{{$class->id}}">{{$class->class}} {{$class->subject}}</option>
                                @endforeach
                        </select>
                            @endisset
                    </div><!-- end col -->
                    <div class="col-md-6">
                        <label class="mb-3">Subject 4</label>
                        <select id="editCode3" name="editCode3" class="select2 form-control mb-3 custom-select" style="width: 100%; height:36px;">
                            @php
                            $isCode3 = FALSE;
                        @endphp
                            @foreach ($classes as $code3)
                        @if( $code3->id == $teacher->class_code3)
                            @php
                            $isCode3 = TRUE;
                            @endphp
                            <option value="{{$code3->id}}"> {{$code3->class}} {{$code3->subject}}</option>
                            @endif
                            @endforeach
                            <option value=""></option>
                            @if($isCode3 == False)
                            <option value=""></option>
                            @endif
                            @isset($classes)
                                @foreach ($classes as $class)
                                    <option value="{{$class->id}}">{{$class->class}} {{$class->subject}}</option>
                                @endforeach
                        </select>
                            @endisset
                    </div><!-- end col -->
                    <div class="col-md-6">
                        <label class="mb-3">Subject 5</label>
                        <select id="editCode4" name="editCode4" class="select2 form-control mb-3 custom-select" style="width: 100%; height:36px;">
                            @php
                            $isCode4 = FALSE;
                         @endphp
                        @foreach ($classes as $code4)
                            @if( $code4->id == $teacher->class_code4)
                                @php
                                $isCode4 = TRUE;
                                @endphp
                                <option value="{{$code4->id}}"> {{$code4->class}} {{$code4->subject}}</option>
                                @endif
                            @endforeach
                            <option value=""></option>
                            @if($isCode4 == False)
                            <option value=""></option>
                            @endif
                            @isset($classes)
                                @foreach ($classes as $class)
                                    <option value="{{$class->id}}">{{$class->class}} {{$class->subject}}</option>
                                @endforeach
                        </select>
                            @endisset
                    </div><!-- end col -->
                    <div class="col-md-6">
                        <label class="mb-3">Subject 6</label>
                        <select id="editCode5" name="editCode5" class="select2 form-control mb-3 custom-select" style="width: 100%; height:36px;">
                            @php
                                    $isCode5 = FALSE;
                                @endphp
                            @foreach ($classes as $code5)
                            @if( $code5->id == $teacher->class_code5)
                                @php
                                $isCode5 = TRUE;
                                @endphp
                                <option value="{{$code5->id}}"> {{$code5->class}} {{$code5->subject}}</option>
                                @endif
                            @endforeach
                            <option value=""></option>
                            @if($isCode5 == False)
                            <option value=""></option>
                            @endif
                            @isset($classes)
                                @foreach ($classes as $class)
                                    <option value="{{$class->id}}">{{$class->class}} {{$class->subject}}</option>
                                @endforeach
                        </select>
                            @endisset
                    </div><!-- end col -->
                    <div class="col-md-6">
                        <label class="mb-3">Subject 7</label>
                        <select id="editCode6" name="editCode6" class="select2 form-control mb-3 custom-select" style="width: 100%; height:36px;">
                            @php
                            $isCode6 = FALSE;
                        @endphp
                            @foreach ($classes as $code6)
                            @if( $code6->id == $teacher->class_code6)
                                @php
                                $isCode6 = TRUE;
                                @endphp
                                <option value="{{$code6->id}}"> {{$code6->class}} {{$code6->subject}}</option>
                                @endif
                            @endforeach
                            <option value=""></option>
                            @if($isCode6 == False)
                            <option value=""></option>
                            @endif
                            @isset($classes)
                                @foreach ($classes as $class)
                                    <option value="{{$class->id}}">{{$class->class}} {{$class->subject}}</option>
                                @endforeach
                        </select>
                            @endisset
                    </div><!-- end col -->
                    <div class="col-md-6">
                        <label class="mb-3">Subject 8</label>
                        <select id="editCode7" name="editCode7" class="select2 form-control mb-3 custom-select" style="width: 100%; height:36px;">
                            @php
                            $isCode7 = FALSE;
                            @endphp
                            @foreach ($classes as $code7)
                            @if( $code7->id == $teacher->class_code7)
                                @php
                                $isCode7 = TRUE;
                                @endphp
                                <option value="{{$code7->id}}"> {{$code7->class}} {{$code7->subject}}</option>
                                @endif
                            @endforeach
                            <option value=""></option>
                            @if($isCode7 == False)
                            <option value=""></option>
                            @endif
                            @isset($classes)
                                @foreach ($classes as $class)
                                    <option value="{{$class->id}}">{{$class->class}} {{$class->subject}}</option>
                                @endforeach
                        </select>
                            @endisset
                    </div><!-- end col -->
                    <div class="col-md-6">
                        <label class="mb-3">Subject 9</label>
                        <select id="editCode8" name="editCode8" class="select2 form-control mb-3 custom-select" style="width: 100%; height:36px;">
                            @php
                            $isCode8 = FALSE;
                            @endphp
                            @foreach ($classes as $code8)
                            @if( $code8->id == $teacher->class_code8)
                                @php
                                $isCode8 = TRUE;
                                @endphp
                                <option value="{{$code8->id}}"> {{$code8->class}} {{$code8->subject}}</option>
                                @endif
                            @endforeach
                            <option value=""></option>
                            @if($isCode8 == False)
                            <option value=""></option>
                            @endif
                            @isset($classes)
                                @foreach ($classes as $class)
                                    <option value="{{$class->id}}">{{$class->class}} {{$class->subject}}</option>
                                @endforeach
                        </select>
                            @endisset
                    </div><!-- end col -->
                    <div class="col-md-6">
                        <label class="mb-3">Subject 10</label>
                        <select id="editCode9" name="editCode9" class="select2 form-control mb-3 custom-select" style="width: 100%; height:36px;">
                            @php
                            $isCode9 = FALSE;
                        @endphp
                            @foreach ($classes as $code9)
                            @if( $code9->id == $teacher->class_code9)
                                @php
                                $isCode9 = TRUE;
                                @endphp
                                <option value="{{$code9->id}}"> {{$code9->class}} {{$code9->subject}}</option>
                                @endif
                            @endforeach
                            <option value=""></option>
                            @if($isCode9 == False)
                            <option value=""></option>
                            @endif
                            @isset($classes)
                                @foreach ($classes as $class)
                                    <option value="{{$class->id}}">{{$class->class}} {{$class->subject}}</option>
                                @endforeach
                        </select>
                            @endisset
                    </div><!-- end col -->
                    <div class="col-md-6">
                        <label class="mb-3">Subject 11</label>
                        <select id="editCode10" name="editCode10" class="select2 form-control mb-3 custom-select" style="width: 100%; height:36px;">
                            @php
                            $isCode10 = FALSE;
                            @endphp
                            @foreach ($classes as $code10)
                            @if( $code10->id == $teacher->class_code10)
                                @php
                                $isCode10 = TRUE;
                                @endphp
                                <option value="{{$code10->id}}"> {{$code10->class}} {{$code10->subject}}</option>
                                @endif
                            @endforeach
                            <option value=""></option>
                            @if($isCode10 == False)
                            <option value=""></option>
                            @endif
                            @isset($classes)
                                @foreach ($classes as $class)
                                    <option value="{{$class->id}}">{{$class->class}} {{$class->subject}}</option>
                                @endforeach
                        </select>
                            @endisset
                    </div><!-- end col -->
                    <div class="col-md-6">
                        <label class="mb-3">Subject 12</label>
                        <select id="editCode11" name="editCode11" class="select2 form-control mb-3 custom-select" style="width: 100%; height:36px;">
                            @php
                            $isCode11 = FALSE;
                            @endphp
                            @foreach ($classes as $code11)
                            @if( $code11->id == $teacher->class_code11)
                                @php
                                $isCode11 = TRUE;
                                @endphp
                                <option value="{{$code11->id}}"> {{$code11->class}} {{$code11->subject}}</option>
                                @endif
                            @endforeach
                            <option value=""></option>
                            @if($isCode11 == False)
                            <option value=""></option>
                            @endif
                            @isset($classes)
                                @foreach ($classes as $class)
                                    <option value="{{$class->id}}">{{$class->class}} {{$class->subject}}</option>
                                @endforeach
                        </select>
                            @endisset
                    </div><!-- end col -->

                    <div class="col-md-6">
                        <label class="mb-3">Upload Image</label>
                               <input name="file" type="file" id="file" class="dropify form-control" />
                        </div><!--end col-->

                </div><!--end row-->
                    <button type="submit" class="btn btn-gradient-primary">Save Changes</button>
                    <button type="button" onclick="window.location='/admin/create_liveClass'" class="btn btn-gradient-danger">Cancel</button>
                </form>
                @endforeach
            </div><!--end card-body-->
        </div><!--end card-->
    </div><!--end col-->
</div>

@endsection


@section('footerScript')
<script src="{{ URL::asset('assets/pages/jquery.form-upload.init.js')}}"></script>
<script src="{{ URL::asset('plugins/dropify/js/dropify.min.js')}}"></script>

<script src="{{ URL::asset('plugins/footable/js/footable.js')}}"></script>
        <script src="{{ URL::asset('plugins/moment/moment.js')}}"></script>
        <script src="{{ URL::asset('assets/pages/jquery.footable.init.js')}}"></script>

@stop
