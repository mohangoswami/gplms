@extends('layouts.teacher_analytics-master')

@section('headerStyle')
<link href="{{ URL::asset('plugins/dropify/css/dropify.min.css')}}" rel="stylesheet">

<script src="https://mozilla.github.io/pdf.js/build/pdf.js"></script>

<!--Datetime picker-->
<link href="{{ URL::asset('plugins/daterangepicker/daterangepicker.css')}}" rel="stylesheet" />
<link href="{{ URL::asset('plugins/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('plugins/timepicker/bootstrap-material-datetimepicker.css')}}" rel="stylesheet">
<link href="{{ URL::asset('plugins/bootstrap-touchspin/css/jquery.bootstrap-touchspin.min.css')}}" rel="stylesheet" />
@stop


@section('content')

<!-- Flash Messages -->
@include('layouts.partials.flash-messages')

<div class="row">
<div class="col-lg-6">
    <div class="card">
        <div class="card-body bg-danger">
            <!-- Nav tabs -->
            <ul class="nav nav-pills nav-justified" role="tablist">
                <li class="nav-item waves-effect waves-light">
                    <a class="nav-link active" data-toggle="tab" href="#pdf" role="tab">PDF</a>
                </li>
                <li class="nav-item waves-effect waves-light">
                    <a class="nav-link" data-toggle="tab" href="#image" role="tab">Image</a>
                </li>
                <li class="nav-item waves-effect waves-light">
                    <a class="nav-link" data-toggle="tab" href="#docs" role="tab">Doc/PPT</a>
                </li>
                <li class="nav-item waves-effect waves-light">
                    <a class="nav-link" data-toggle="tab" href="#form" role="tab">Google Form</a>
                </li>
            </ul>

            <!-- Tab panes -->
            <div class="text-right"><a class="btn btn-primary mt-3" href="http://forms.google.com/" target="_blank">Google Forms<a></div>
            <div class="tab-content">
                <div class="tab-pane active p-3" id="pdf" role="tabpanel">

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="mt-0 header-title">Send Pdf</h4>
                                    <p class="text-muted mb-3">Basic example to demonstrate Bootstrap’s form styles.</p>
                                    <form method="POST" action="{{ route('teacher.pdfExam') }}" enctype="multipart/form-data">
                                        @csrf
                                        <div class="col-md-6">
                                            <label class="mb-3">Select Class & Subject</label>
                                            <select id="grade" name="grade"  class="select2 form-control mb-3 custom-select" style="width: 100%; height:36px;" required>
                                                <option value="">None</option>
                                                @foreach($subCodes as $subCode)
                                                    @foreach($classCodes as $classCode)
                                                        @if($subCode==$classCode->id)
                                                <option value="{{$classCode->id}}">{{$classCode->class}} - {{$classCode->subject}}  </option>
                                                        @endif
                                                    @endforeach
                                               @endforeach
                                            </select>
                                        </div><!-- end col -->
                                        <div class="form-group mt-3">
                                            <label for="lable_title">Topic</label>
                                            <input name="title" class="form-control" type="text" placeholder="Enter title name" id="title" required>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="lable_discription">Discription</label>
                                                    <textarea name="discription" class="form-control" rows="2" id="discription"></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="pdfUpload">PDF Upload</label>
                                            <input class="form-control" type="file" id="file" name="file" required/><br>
                                        </div>
                                        <label class="mb-3">Exam - Start and End time</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="datetimes">

                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="dripicons-calendar"></i></span>
                                            </div>
                                        </div>
                                        <div class="form-group mt-3">
                                            <label class="mb-3">Maximum Marks</label>
                                            <input required id="demo0" type="text" value="100" name="demo0" data-bts-min="0" data-bts-max="100" data-bts-init-val="" data-bts-step="1" data-bts-decimal="0" data-bts-step-interval="100" data-bts-force-step-divisibility="round" data-bts-step-interval-delay="500" data-bts-prefix="" data-bts-postfix="" data-bts-prefix-extra-class="" data-bts-postfix-extra-class="" data-bts-booster="true" data-bts-boostat="10" data-bts-max-boosted-step="false" data-bts-mousewheel="true" data-bts-button-down-class="btn btn-default" data-bts-button-up-class="btn btn-default"/>
                                        </div>
                                        <button type="submit" class="btn btn-gradient-primary">Submit</button>
                                        <button type="button" class="btn btn-gradient-danger">Cancel</button>
                                    </form>
                                    <div>
                                    <canvas id="pdfViewer"></canvas>
                                    </div>
                                    @yield ('footerScript')

                                </div><!--end card-body-->
                            </div><!--end card-->
                        </div><!--end col-->
                    </div>

                </div>
                <div class="tab-pane p-3" id="image" role="tabpanel">


                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="mt-0 header-title"> Image</h4>
                                    <p class="text-muted mb-3">Send any picture to the students.</p>
                                    <form method="POST" action="{{ route('teacher.imageExam') }}" enctype="multipart/form-data">
                                        @csrf
                                        <div class="col-md-6">
                                            <label class="mb-3">Select Class & Subject</label>
                                            <select id="imgGrade" name="imgGrade"  class="select2 form-control mb-3 custom-select" style="width: 100%; height:36px;" required>
                                                <option value="">None</option>
                                                @foreach($subCodes as $subCode)
                                                    @foreach($classCodes as $classCode)
                                                        @if($subCode==$classCode->id)
                                                <option value="{{$classCode->id}}">{{$classCode->class}} - {{$classCode->subject}}  </option>
                                                        @endif
                                                    @endforeach
                                               @endforeach
                                            </select>
                                        </div><!-- end col -->
                                        <div class="form-group mt-3">
                                            <label for="lable_title">Topic</label>
                                            <input name="imgTitle" class="form-control" type="text" placeholder="Enter title name" id="imgTitle" required>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="lable_discription">Discription</label>
                                                    <textarea name="imgDiscription" class="form-control" rows="2" id="imgDiscription"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                            <div class="col-xl-12">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h4 class="mt-0 header-title">Upload Image</h4>
                                                        <p class="text-muted mb-3">Upload jpg/png/img image. (Max size - 10Mb)</p>
                                                        <input name="file" type="file" id="file" class="dropify form-control" />
                                                    </div><!--end card-body-->
                                                </div><!--end card-->
                                            </div><!--end col-->

                                            <label class="mb-3">Exam - Start and End time</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="datetimes">

                                                <div class="input-group-append">
                                                    <span class="input-group-text"><i class="dripicons-calendar"></i></span>
                                                </div>
                                            </div>
                                            <div class="form-group mt-3">
                                                <label class="mb-3">Maximum Marks</label>
                                                <input required id="demo0" type="text" value="100" name="demo0" data-bts-min="0" data-bts-max="100" data-bts-init-val="" data-bts-step="1" data-bts-decimal="0" data-bts-step-interval="100" data-bts-force-step-divisibility="round" data-bts-step-interval-delay="500" data-bts-prefix="" data-bts-postfix="" data-bts-prefix-extra-class="" data-bts-postfix-extra-class="" data-bts-booster="true" data-bts-boostat="10" data-bts-max-boosted-step="false" data-bts-mousewheel="true" data-bts-button-down-class="btn btn-default" data-bts-button-up-class="btn btn-default"/>
                                            </div>

                                        <button type="submit" class="btn btn-gradient-primary">Submit</button>
                                        <button type="button" class="btn btn-gradient-danger">Cancel</button>
                                    </form>
                                </div><!--end card-body-->
                            </div><!--end card-->
                        </div><!--end col-->
                    </div>

                </div>
                <div class="tab-pane p-3" id="docs" role="tabpanel">


                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="mt-0 header-title">Doc/PPT</h4>
                                    <p class="text-muted mb-3">Send the Word, Excel, Ppt, file. (Max size 10Mb)</p>
                                    <form method="POST" action="{{ route('teacher.docsExam') }}" enctype="multipart/form-data">
                                        @csrf
                                        <div class="col-md-6">
                                            <label class="mb-3">Select Class & Subject</label>
                                            <select id="docGrade" name="docGrade"  class="select2 form-control mb-3 custom-select" style="width: 100%; height:36px;" required>
                                                <option value="">None</option>
                                                @foreach($subCodes as $subCode)
                                                    @foreach($classCodes as $classCode)
                                                        @if($subCode==$classCode->id)
                                                <option value="{{$classCode->id}}">{{$classCode->class}} - {{$classCode->subject}}  </option>
                                                        @endif
                                                    @endforeach
                                               @endforeach
                                            </select>
                                        </div><!-- end col -->
                                        <div class="form-group">
                                            <label for="lable_title mt-3">Topic</label>
                                            <input name="docTitle" class="form-control" type="text" placeholder="Enter title" id="docTitle">
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="lable_discription">Discription</label>
                                                    <textarea name="docDiscription" class="form-control" rows="2" id="docDiscription"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="pdfUpload">Upload File</label>
                                            <input type="file" id="file" name="file" required/><br>
                                        </div>
                                        <label class="mb-3">Exam - Start and End time</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="datetimes">

                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="dripicons-calendar"></i></span>
                                            </div>
                                        </div>
                                        <div class="form-group mt-3">
                                            <label class="mb-3">Maximum Marks</label>
                                            <input required id="demo0" type="text" value="100" name="demo0" data-bts-min="0" data-bts-max="100" data-bts-init-val="" data-bts-step="1" data-bts-decimal="0" data-bts-step-interval="100" data-bts-force-step-divisibility="round" data-bts-step-interval-delay="500" data-bts-prefix="" data-bts-postfix="" data-bts-prefix-extra-class="" data-bts-postfix-extra-class="" data-bts-booster="true" data-bts-boostat="10" data-bts-max-boosted-step="false" data-bts-mousewheel="true" data-bts-button-down-class="btn btn-default" data-bts-button-up-class="btn btn-default"/>
                                        </div>
                                        <button type="submit" class="btn btn-gradient-primary">Submit</button>
                                        <button type="button" class="btn btn-gradient-danger">Cancel</button>
                                    </form>
                                </div><!--end card-body-->
                            </div><!--end card-->
                        </div><!--end col-->
                    </div>

                </div>

                <div class="tab-pane p-3" id="form" role="tabpanel">


                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="mt-0 header-title"> Google Form</h4>
                                    <p class="text-muted mb-3">Copy Youtube link and paste here.</p>
                                    <form method="POST" action="{{ route('teacher.formLink') }}" enctype="multipart/form-data">
                                        @csrf
                                        <div class="col-md-6">
                                            <label class="mb-3">Select Class & Subject</label>
                                            <select id="formGrade" name="formGrade" class="select2 form-control mb-3 custom-select" style="width: 100%; height:36px;">
                                                <option value="">None</option>
                                                @foreach($subCodes as $subCode)
                                                    @foreach($classCodes as $classCode)
                                                        @if($subCode==$classCode->id)
                                                <option value="{{$classCode->id}}">{{$classCode->class}} - {{$classCode->subject}}  </option>
                                                        @endif
                                                    @endforeach
                                               @endforeach
                                            </select>
                                        </div><!-- end col -->
                                        <div class="form-group">
                                            <label for="lable_title">Topic</label>
                                            <input name="formTitle" class="form-control" type="text" placeholder="Enter title name" id="formTitle" required>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="lable_discription">Discription</label>
                                                    <textarea name="formDiscription" class="form-control" rows="2" id="formDiscription"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="lable_title">Google Form Link</label>
                                            <input name="formLink" class="form-control" type="text" placeholder="Paste form Link Here" id="formLink" required>
                                        </div>
                                        <label class="mb-3">Exam - Start and End time</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="datetimes">

                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="dripicons-calendar"></i></span>
                                            </div>
                                        </div>
                                        <div class="form-group mt-3">
                                            <label class="mb-3">Maximum Marks</label>
                                            <input required id="demo0" type="text" value="100" name="demo0" data-bts-min="0" data-bts-max="100" data-bts-init-val="" data-bts-step="1" data-bts-decimal="0" data-bts-step-interval="100" data-bts-force-step-divisibility="round" data-bts-step-interval-delay="500" data-bts-prefix="" data-bts-postfix="" data-bts-prefix-extra-class="" data-bts-postfix-extra-class="" data-bts-booster="true" data-bts-boostat="10" data-bts-max-boosted-step="false" data-bts-mousewheel="true" data-bts-button-down-class="btn btn-default" data-bts-button-up-class="btn btn-default"/>
                                        </div>
                                        <div class="form-group form-check">
                                            <input name="formStudentWorkIsrequire" id="formStudentWorkIsrequire" type="checkbox" class="form-check-input" id="exampleCheck1">
                                            <label class="form-check-label" for="exampleCheck1">Student's return Work Require</label>
                                        </div>
                                        <button type="submit" class="btn btn-gradient-primary">Submit</button>
                                        <button type="button" class="btn btn-gradient-danger">Cancel</button>
                                    </form>
                                </div><!--end card-body-->
                            </div><!--end card-->
                        </div><!--end col-->
                    </div>

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
<!--Datetime picker-->
<script src="{{ URL::asset('plugins/moment/moment.js')}}"></script>
<script src="{{ URL::asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{ URL::asset('plugins/select2/select2.min.js')}}"></script>
<script src="{{ URL::asset('plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js')}}"></script>
<script src="{{ URL::asset('plugins/timepicker/bootstrap-material-datetimepicker.js')}}"></script>
<script src="{{ URL::asset('plugins/bootstrap-maxlength/bootstrap-maxlength.min.js')}}"></script>
<script src="{{ URL::asset('plugins/bootstrap-touchspin/js/jquery.bootstrap-touchspin.min.js')}}"></script>
<script src="{{ URL::asset('assets/pages/jquery.forms-advanced.js')}}"></script>



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
