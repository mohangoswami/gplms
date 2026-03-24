@extends('layouts.admin_analytics-master')

@section('title', 'Metrica - Admin & Dashboard Template')

@section('content')

<!-- Flash Messages -->
@include('layouts.partials.flash-messages')

<div class="container-fluid">

        <!-- Page-Title -->
        <div class="row">
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mt-0 mb-3">Return work sent by Students</h4>
                            <div class="slimscroll crm-dash-activity">
                                <div class="activity">
                                 @php
                                   $currentUser = Null;
                                 @endphp
                                  @foreach ($stuHomeworkUploads as $stuHomeworkUpload)
                                  <div class="activity-info">
                                    <div class="icon-info-activity">

                                        @if($stuHomeworkUpload->email != $currentUser)
                                        <br><h4 class="mb-2"><li>{{$stuHomeworkUpload->name}}</li></h3>
                                        @endif
                                        <a href="{{$stuHomeworkUpload->fileUrl}}" target="_blank"  class="waves-effect waves-light">
                                        <div>
                                            <div>
                                                @php
                                                    $currentUser = $stuHomeworkUpload->email;
                                                    $filename = basename($stuHomeworkUpload->fileUrl);
                                                @endphp
                                                <h6 class="m-0"> * {{$filename}}</h6>
                                            </div>
                                            <div>
                                                <p class="text-muted mb-0">
                                                    {{$stuHomeworkUpload->created_at->format('d/M h:ia')}}
                                                </p>
                                            </div>
                                        </div>
                                        </a>
                                    @endforeach
                                </div><!--end activity-->
                            </div><!--end crm-dash-activity-->
                        </div>  <!--end card-body-->
                    </div><!--end card-->
                </div><!--end col-->

             </div>
            </div>
        </div>
</div>

        @endsection
