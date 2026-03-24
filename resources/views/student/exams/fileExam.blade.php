@extends('layouts.student_master')

@section('headerStyle')
<link href="{{ URL::asset('plugins/dropify/css/dropify.min.css')}}" rel="stylesheet">
@stop

@section('content')

<!-- Flash Messages -->
@include('layouts.partials.flash-messages')


@foreach ($exams as $exam)

        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card">
                    <h5 class="card-header bg-primary text-white mt-0">{{$exam->subject}} - {{$exam->id}}</h5>
                    <div class="card-body">
                        <h4 class="card-title mt-0">{{$exam->title}}</h4>
                        <p class="card-text text-muted">{{$exam->discription}}</p>
                        @if($finalSubmit==false)
                        <div class="footter text-center m-2">

                            <a href="{{$exam->fileUrl}}" target="_blank"  class="btn btn-primary waves-effect waves-light"><i class="fas fa-download mr-2"></i>Download</a>
                        </div>
                        <div>
                        <div class="mt-5">
                            <h5>Upload your answer sheet</h5>
                        <form action="{{ route('student.exams.fileExam') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="{{$exam->id}}">
                            <input type="file" name="file" id="file" class="dropify form-control" required/>
                            <button type="submit" class="btn btn-primary btn-lg btn-block mt-1"><i class="fas fa-upload mr-2"></i>Upload</button>
                            </form>

                        </div>
                        @endif

                        <div class="tab-pane p-3" id="img" role="tabpanel">
                            <div class="card-body pt-0">
                                <ul class="list-group wallet-bal-crypto">

                                    @foreach ($uploadFiles as $uploadFile)
                                        <a href="{{$uploadFile->fileUrl}}" target="_blank" >
                                            <div class="row ">
                                                <div class="col-md-10">
                                                    <li class="list-group-item align-items-center d-flex justify-content-between">
                                                        <div class="media">
                                                            <img src="{{ URL::asset('assets/images/files logo/jpeg.jpeg')}}" class="mr-3 thumb-sm align-self-center rounded-circle" alt="...">
                                                            <div class="media-body align-self-center">
                                                                <div class="coin-bal">
                                                                    @php
                                                                        $filename=basename($uploadFile->fileUrl);
                                                                        $fileSizes=intval(($uploadFile->fileSize)/1000);

                                                                    @endphp
                                                                    <h3 class="m-0">{{$filename}}</h3>
                                                                    <p class="text-muted mb-0">Size - {{$fileSizes}}KB</p>
                                                                </div>
                                                            </div><!--end media body-->
                                                        </div>
                                                        </a>
                                                                <div class=" ml-3 align-items-start text-center">
                                                                    @if($finalSubmit==false)
                                                                    <span> <a href="{{$uploadFile->id}}/{{$exam->id}}/delete"><i class="fas fa-trash-alt text-danger font-16"></i></a></span>
                                                                    @endif
                                                                    <span class="mt-3 badge badge-soft-purple">{{$uploadFile->created_at->format('d/M')}}</span>
                                                                </div>
                                                    </li>
                                                </div>
                                            </div>
                                    @endforeach

                                </ul>
                            </div><!--end card-body-->
                        </div>
                        <div>@if($finalSubmit==true)
                            <h3 class="bg-success text-danger b-round"><center><i class="mdi mdi-check-all mr-1"></i>Submitted Done</center></h3>
                            @else
                            <a href="{{$exam->id}}/submittedDone"  class="btn btn-danger b-round btn-lg btn-block mt-1" ><i class="mdi mdi-send mr-2"></i>Final Submit</a>
                            @endif
                        </div>

                    </div><!--end card-body-->
                </div><!--end card-->
            </div>
        </div>
        @endforeach

@stop

@section('footerScript')

<script src="{{ URL::asset('assets/pages/jquery.form-upload.init.js')}}"></script>
<script src="{{ URL::asset('plugins/dropify/js/dropify.min.js')}}"></script>
@stop
