@extends('layouts.student_master')

@section('headerStyle')
<link href="{{ URL::asset('plugins/dropify/css/dropify.min.css')}}" rel="stylesheet">
@stop

@section('content')

<!-- Flash Messages -->
@include('layouts.partials.flash-messages')

        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card">
                    <h5 class="card-header bg-warning text-white mt-0">{{$subject}}</h5>
                    <div class="card-body">
                        <h4 class="card-title mt-0 ">Topic- {{$title}}</h4>
                        <div >
                               <ul>
                                   <li>File Name- {{$filename}}</li>
                                   <li>Size- {{$fileSizes}}KB</li>
                            </ul>
                            </div>
                        </div>
                        <div class="footter text-center">
                            <a href="{{$fileUrl}}" target="_blank" download  class="btn btn-primary waves-effect waves-light"><i class="fas fa-download mr-2"></i>Download</a>
                        </div>
                        <div>
                            @if($studentReturn==1)
                        <div class="mt-5 text-center">
                            <h5>Upload your answer sheet</h5>
                        <form action="{{ route('student.stuUploadFile') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="{{$id}}">
                            <input type="file" name="file" id="file" class="dropify form-control" required/>
                            <button type="submit" class="btn btn-primary btn-lg btn-block mt-1"><i class="fas fa-upload mr-2"></i>Upload</button>
                            </form>

                        </div>

                        <div class="tab-pane p-3" id="img" role="tabpanel">
                            <div class="card-body pt-0">
                                <ul class="list-group wallet-bal-crypto">

                                    @foreach ($stuHomeworkUploads as $stuHomeworkUpload)
                                        <a href="{{$stuHomeworkUpload->fileUrl}}" target="_blank" >
                                            <div class="row ">
                                                <div class="col-md-10">
                                                    <li class="list-group-item align-items-center d-flex justify-content-between">
                                                        <div class="media">
                                                            <img src="{{ URL::asset('assets/images/files logo/jpeg.jpeg')}}" class="mr-3 thumb-sm align-self-center rounded-circle" alt="...">
                                                            <div class="media-body align-self-center">
                                                                <div class="coin-bal">
                                                                    @php
                                                                        $filename=basename($stuHomeworkUpload->fileUrl);
                                                                        $fileSizes=intval(($stuHomeworkUpload->fileSize)/1000);

                                                                    @endphp
                                                                    <h3 class="m-0">{{$filename}}</h3>
                                                                    <p class="text-muted mb-0">Size - {{$fileSizes}}KB</p>
                                                                </div>
                                                            </div><!--end media body-->
                                                        </div>
                                                        </a>
                                                                <div class=" ml-3 align-items-start text-center">
                                                                <span>
                                                                    <a onclick="return confirm('Are you sure want to delete?')" href="{{$stuHomeworkUpload->id}}/{{$id}}/delete"><i class="fas fa-trash-alt text-danger font-16"></i></a>
                                                                </span>
                                                                    <span class="mt-3 badge badge-soft-purple">{{$stuHomeworkUpload->created_at->format('d/M')}}</span>
                                                                </div>
                                                    </li>
                                                </div>
                                            </div>
                                    @endforeach

                                </ul>
                            </div><!--end card-body-->
                        </div>
                        @endif
                    </div><!--end card-body-->
                </div><!--end card-->
            </div>
        </div>

@stop

@section('footerScript')

<script src="{{ URL::asset('assets/pages/jquery.form-upload.init.js')}}"></script>
<script src="{{ URL::asset('plugins/dropify/js/dropify.min.js')}}"></script>
@stop
