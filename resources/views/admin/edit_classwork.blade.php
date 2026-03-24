@extends('layouts.admin_analytics-master')

@section('headerStyle')
<link href="{{ URL::asset('plugins/dropify/css/dropify.min.css')}}" rel="stylesheet">

<script src="https://mozilla.github.io/pdf.js/build/pdf.js"></script>

@stop


@section('content')

<!-- Flash Messages -->
@include('layouts.partials.flash-messages')

<div class="row">
<div class="col-lg-6">
    <div class="card">
        <div class="card-body">
            <!-- Nav tabs -->


                   @if($type=="PDF")
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="mt-0 header-title">{{$class. ' - ' . $subject . ', Topic - ' . $title}}</h4>
                                    <h5 class="mt-0 header-title">Edit PDF</h5>
                                    <p class="text-muted mb-3">Basic example to demonstrate Bootstrap’s form styles.</p>
                                    <form method="POST" action="{{ route('admin.editPdfClasswork') }}" enctype="multipart/form-data">
                                        @csrf
                                    <input type="hidden" name="id" id="id" value="{{$id}}">
                                    <div class="col-md-6">
                                        <label class="">Select Term</label>
                                        <select id="selectTerm" name="selectTerm"  class="select2 form-control mb-3 custom-select" style="width: 100%; height:36px;">
                                            @foreach($terms as $term)
                                            @isset($term->term)
                                            <option value="{{$term->term}}">{{$term->term}} </option>
                                            @endisset
                                            @endforeach
                                        </select>
                                    </div><!-- end col -->
                                      <div class="col-md-6">

                                            <label class="">Select Existing Topic</label>
                                            <select id="selectTitle" name="selectTitle"  class="select2 form-control mb-3 custom-select" style="width: 100%; height:36px;">
                                                <option value="">None</option>
                                                @php
                                                    $titles[]=Null;
                                                @endphp
                                            @foreach($classDatas as $classData)
                                            @isset($classData->title)
                                            @if(!in_array($classData->title, $titles))
                                            <option value="{{$classData->title}}">{{$classData->title}} </option>
                                                @php
                                                $titles[]=$classData->title;
                                                @endphp
                                                 @endif
                                            @endisset

                                            @endforeach
                                            </select>

                                        </div><!-- end col -->


                                        <div class="form-group">
                                            <label for="pdfUpload">Edit PDF</label>
                                            <input name="fileName" class="form-control" type="text" placeholder="Enter file name" id="inputTitle" required>
                                            <input class="form-control" type="file" id="file" name="file" required/><br>
                                        </div>
                                        <div class="form-group form-check">
                                            <input name="studentWorkIsrequire" type="checkbox" class="form-check-input" id="studentWorkIsrequire" @if($studentReturn=="1") checked @endif>
                                            <label class="form-check-label" for="exampleCheck1">Student's return Work Require</label>
                                        </div>
                                        <button type="submit" class="btn btn-gradient-primary">Submit</button>
                                        <button type="button" onclick="window.location='/admin/allClasswork'"  class="btn btn-gradient-danger">Cancel</button>
                                    </form>
                                    <div>
                                    <canvas id="pdfViewer"></canvas>
                                    </div>
                                    @yield ('footerScript')

                                </div><!--end card-body-->
                            </div><!--end card-->
                        </div><!--end col-->
                    </div>
                    @endif

                    @if($type=="IMG")
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="mt-0 header-title">{{$class. ' - ' . $subject . ', Topic - ' . $title}}</h4>
                                    <form method="POST" action="{{ route('admin.editImageClasswork') }}" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="id" id="id" value="{{$id}}">
                                        <div class="col-md-6">
                                            <label class="">Select Term</label>
                                            <select id="selectTerm" name="selectTerm"  class="select2 form-control mb-3 custom-select" style="width: 100%; height:36px;">
                                                @foreach($terms as $term)
                                                @isset($term->term)
                                                <option value="{{$term->term}}">{{$term->term}} </option>
                                                @endisset
                                                @endforeach
                                            </select>
                                        </div><!-- end col -->

                                        <div class="col-md-6">
                                            <label class="">Select Existing Topic</label>
                                            <select id="selectTitle" name="selectTitle"  class="select2 form-control mb-3 custom-select" style="width: 100%; height:36px;">
                                                <option value="">None</option>
                                                @php
                                                    $titlesImg[]=Null;
                                                @endphp
                                            @foreach($classDatas as $classData)
                                            @isset($classData->title)

                                            @if(!in_array($classData->title, $titlesImg))
                                            <option value="{{$classData->title}}">{{$classData->title}} </option>
                                                @php
                                                $titlesImg[]=$classData->title;
                                                @endphp
                                                 @endif
                                            @endisset
                                            @endforeach
                                            </select>

                                        </div><!-- end col -->

                                            <div class="col-xl-12">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h4 class="mt-0 header-title">Edit Image</h4>
                                                        <p class="text-muted mb-3">Upload jpg/png/img image. (Max size - 10Mb)</p>
                                                        <input name="fileName" class="form-control" type="text" placeholder="Enter name" id="inputTitle" required>
                                                        <input name="file" type="file" id="file" class="dropify form-control" />
                                                    </div><!--end card-body-->
                                                </div><!--end card-->
                                            </div><!--end col-->

                                        <div class="form-group form-check">
                                            <input name="imgStudentWorkIsrequire" id="imgStudentWorkIsrequire" type="checkbox" class="form-check-input" @if($studentReturn=="1") checked @endif>
                                            <label name="lablestudentWorkIsrequire" id="lablestudentWorkIsrequire" class="form-check-label" for="exampleCheck1">Student's return Work Require</label>
                                        </div>
                                        <button type="submit" class="btn btn-gradient-primary">Submit</button>
                                        <button type="button" onclick="window.location='/admin/allClasswork'" class="btn btn-gradient-danger">Cancel</button>
                                    </form>
                                </div><!--end card-body-->
                            </div><!--end card-->
                        </div><!--end col-->
                    </div>
                    @endif


                    @if($type=="DOCS")
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="mt-0 header-title">{{$class. ' - ' . $subject . ', Topic - ' . $title}}</h4>
                                    <h4 class="mt-0 header-title">Edit Doc/PPT</h4>
                                    <p class="text-muted mb-3">Send the Word, Excel, Ppt, file. (Max size 10Mb)</p>
                                    <form method="POST" action="{{ route('admin.editDocsClasswork') }}" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="id" id="id" value="{{$id}}">
                                        <div class="col-md-6">
                                            <label class="">Select Term</label>
                                            <select id="selectTerm" name="selectTerm"  class="select2 form-control mb-3 custom-select" style="width: 100%; height:36px;">
                                                @foreach($terms as $term)
                                                @isset($term->term)
                                                <option value="{{$term->term}}">{{$term->term}} </option>
                                                @endisset
                                                @endforeach
                                            </select>
                                        </div><!-- end col -->
                                        <div class="col-md-6">
                                            <label class="">Select Existing Topic</label>
                                            <select id="selectTitle" name="selectTitle"  class="select2 form-control mb-3 custom-select" style="width: 100%; height:36px;">
                                                <option value="">None</option>
                                                @php
                                                    $titlesDoc[]=Null;
                                                @endphp
                                            @foreach($classDatas as $classData)
                                            @isset($classData->title)
                                            @if(!in_array($classData->title, $titlesDoc))
                                            <option value="{{$classData->title}}">{{$classData->title}} </option>
                                                @php
                                                $titlesDoc[]=$classData->title;
                                                @endphp
                                                 @endif
                                            @endisset

                                            @endforeach
                                            </select>
                                        </div><!-- end col -->

                                        <div class="form-group">
                                            <label for="pdfUpload">Upload File</label>
                                            <input name="fileName" class="form-control" type="text" placeholder="Enter name" id="inputTitle" required>
                                            <input type="file" id="file" name="file" required/><br>
                                        </div>
                                        <div class="form-group form-check">
                                            <input name="docStudentWorkIsrequire" id="docStudentWorkIsrequire" type="checkbox" class="form-check-input" id="exampleCheck1" @if($studentReturn=="1") checked @endif>
                                            <label class="form-check-label" for="exampleCheck1">Student's return Work Require</label>
                                        </div>
                                        <button type="submit" class="btn btn-gradient-primary">Submit</button>
                                        <button type="button" onclick="window.location='/admin/allClasswork'"  class="btn btn-gradient-danger">Cancel</button>
                                    </form>
                                </div><!--end card-body-->
                            </div><!--end card-->
                        </div><!--end col-->
                    </div>
                    @endif



                    @if($type=="YOUTUBE")
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="mt-0 header-title">{{$class. ' - ' . $subject . ', Topic - ' . $title}}</h4>
                                    <h4 class="mt-0 header-title">Edit Youtube Link</h4>
                                    <p class="text-muted mb-3">Copy Youtube link and paste here.</p>

                                    <form method="POST" action="{{ route('admin.editYoutubeLink') }}" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="id" id="id" value="{{$id}}">
                                        <div class="col-md-6">
                                            <label class="">Select Term</label>
                                            <select id="selectTerm" name="selectTerm"  class="select2 form-control mb-3 custom-select" style="width: 100%; height:36px;">
                                                @foreach($terms as $term)
                                                @isset($term->term)
                                                <option value="{{$term->term}}">{{$term->term}} </option>
                                                @endisset
                                                @endforeach
                                            </select>

                                        </div><!-- end col -->

                                        <div class="col-md-6">

                                            <label class="">Select Existing Topic </label>
                                            <select id="selectTitle" name="selectTitle"  class="select2 form-control mb-3 custom-select" style="width: 100%; height:36px;">
                                                <option value="">None</option>
                                                    @php
                                                        $titlesYtb[]=Null;
                                                    @endphp
                                                @foreach($classDatas as $classData)
                                                @isset($classData->title)
                                                @if(!in_array($classData->title, $titlesYtb))
                                                <option value="{{$classData->title}}">{{$classData->title}} </option>
                                                    @php
                                                    $titlesYtb[]=$classData->title;
                                                    @endphp
                                                     @endif
                                                @endisset
                                                @endforeach
                                            </select>
                                        </div><!-- end col -->

                                        <div class="form-group">
                                            <label for="lable_title">Youtube Link</label>
                                            <input name="youtubeLink"  value="{{$youtubeLink}}" class="form-control" type="text" placeholder="Paste Youtube Link Here" id="youtubeLink">
                                        </div>
                                        <div class="form-group form-check">
                                        <input name="ytStudentWorkIsrequire" id="studentWorkIsrequire" type="checkbox" class="form-check-input" id="exampleCheck1" @if($studentReturn=="1") checked @endif>
                                            <label class="form-check-label" for="exampleCheck1">Student's return Work Require</label>
                                        </div>
                                        <button type="submit" class="btn btn-gradient-primary">Submit</button>
                                        <button type="button" onclick="window.location='/admin/allClasswork'" class="btn btn-gradient-danger">Cancel</button>
                                    </form>
                                </div><!--end card-body-->
                            </div><!--end card-->
                        </div><!--end col-->
                        @endif
                    </div>
            </div>
        </div><!--end card-body-->
    </div><!--end card-->
</div><!--end col-->
</div><!--end row-->
@stop

@section('footerScript')

<script src="{{ URL::asset('assets/pages/jquery.form-upload.init.js')}}"></script>
<script src="{{ URL::asset('plugins/dropify/js/dropify.min.js')}}"></script>

<script src="{{ URL::asset('/assets/js/jquery.core.js')}}"></script>
<script>
    // Loaded via <script> tag, create shortcut to access PDF.js exports.
    var pdfjsLib = window['pdfjs-dist/build/pdf'];
    // The workerSrc property shall be specified.
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://mozilla.github.io/pdf.js/build/pdf.worker.js';

    $("#file").on("change", function(e){
        var file = e.target.files[0]
        if(file.type == "application/pdf"){
            var fileReader = new FileReader();
            fileReader.onload = function() {
                var pdfData = new Uint8Array(this.result);
                // Using DocumentInitParameters object to load binary data.
                var loadingTask = pdfjsLib.getDocument({data: pdfData});
                loadingTask.promise.then(function(pdf) {
                  console.log('PDF loaded');

                  // Fetch the first page
                  var pageNumber = 1;
                  pdf.getPage(pageNumber).then(function(page) {
                    console.log('Page loaded');

                    var scale = 1.5;
                    var viewport = page.getViewport({scale: scale});

                    // Prepare canvas using PDF page dimensions
                    var canvas = $("#pdfViewer")[0];
                    var context = canvas.getContext('2d');
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;

                    // Render PDF page into canvas context
                    var renderContext = {
                      canvasContext: context,
                      viewport: viewport
                    };
                    var renderTask = page.render(renderContext);
                    renderTask.promise.then(function () {
                      console.log('Page rendered');
                    });
                  });
                }, function (reason) {
                  // PDF loading error
                  console.error(reason);
                });
            };
            fileReader.readAsArrayBuffer(file);
        }
    });
    </script>
@stop
