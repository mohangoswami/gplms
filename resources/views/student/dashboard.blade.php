@extends('layouts.student_master')

@section('title', 'Metrica - Admin & Dashboard Template')

@section('headerStyle')
        <link href="{{ URL::asset('plugins/dropify/css/dropify.min.css')}}" rel="stylesheet">
        <link href="{{ URL::asset('plugins/filter/magnific-popup.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{ URL::asset('plugins/lightpick/lightpick.css')}}" rel="stylesheet" />
<!-- Top Scroller-->
<link href="{{ URL::asset('plugins/ticker/jquery.jConveyorTicker.css')}}" rel="stylesheet" type="text/css" />
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-KMDBFHEBCQ"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-KMDBFHEBCQ');
</script>
@stop

@section('content')

<!-- Flash News -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="wrap">
                    <div class="jctkr-label">
                        <span><i class="fas fa-exchange-alt mr-2"></i><i class="fas fa-bullhorn"></i></span>
                    </div>
                    <div class="js-conveyor-example">
                        <ul>
                            @php
                                $n=1;
                            @endphp
                            @isset($flashNews)
                            @foreach ($flashNews as $news)
                                @if($n<=5)
                            <li>
                                <span><i class="fas fa-rss "></i> </span>
                                <span class="usd-rate font-14"><b>{{$news->news}}</b></span>
                                <span class="mb-0 font-12 text-success">{{$news->created_at->format('d M')}}</span>
                            </li>
                                @endif
                                @php
                                    $n=$n+1;
                                @endphp
                            @endforeach
                            @endisset
                        </ul>
                    </div>
                </div>    
            </div><!--end card-body-->
        </div><!--end card-->
    </div><!--end col-->
</div>  <!--end row-->

        <div class="row">
                 
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-body dash-info-carousel">
                        <h4 class="mt-0 header-title mb-4">Latest Work</h4>
                        <div id="carousel_1" class="carousel slide" data-ride="carousel">
                            <div class="carousel-inner">
                                @php
                                    $i=0;
                                @endphp
                                @isset($classWorks)
                                @foreach($classWorks as $classWork)
                                @if($classWork->type == 'TOPIC')
                                 @continue
                                @endif
                                @if($i==0)
                                <div class="carousel-item active">
                                    <div class="media">
                                        @if($classWork->type=='IMG')
                                        <a href="{{$classWork->fileUrl}}" target="_blank" >
                                        <img src="{{ URL::asset('assets/images/files logo/jpeg.jpeg')}}" class="mr-2 thumb-lg rounded-circle" alt="...">
                                        </a>
                                        @elseif($classWork->type=='PDF')
                                        <a href="{{$classWork->fileUrl}}"  target="_blank" >
                                            <img src="{{ URL::asset('assets/images/files logo/download.jpeg')}}" class="mr-2 thumb-lg rounded-circle" alt="...">
                                        </a>
                                        @elseif($classWork->type=='DOCS')
                                        <a href="{{$classWork->fileUrl}}" target="_blank" >
                                            <img src="{{ URL::asset('assets/images/files logo/youtube.png')}}" class="mr-2 thumb-lg rounded-circle" alt="...">
                                        </a>
                                        @elseif($classWork->type=='YOUTUBE')
                                        <a href="{{$classWork->youtubeLink}}" target="_blank" >
                                            <img src="{{ URL::asset('assets/images/files logo/youtube.png')}}" class="mr-2 thumb-lg rounded-circle" alt="...">
                                        </a>
                                        @else
                                        <i class="mdi mdi-checkbox-marked-circle-outline bg-soft-success"></i>
                                        @endif
                                        <div class="media-body align-self-center">                                                          
                                        <h4 class="mt-0 mb-1 title-text text-dark">{{$classWork->subject}} - {{$classWork->title}}</h4>
                                        <div class="">                                                                                
                                            <p class="text-muted mb-0">{{$classWork->name}}</p>
                                        </div>
                                        <div class="text-right">                                                                                
                                            <p class="text-muted mb-0">{{$classWork->created_at->format('d/M')}}</p>
                                        </div>                                                                            </div>
                                    </div>
                                </div>
                                @else
                                <div class="carousel-item">
                                    <div class="media">
                                        @if($classWork->type=='IMG')
                                        <a href="{{$classWork->fileUrl}}" target="_blank" >
                                            <img src="{{ URL::asset('assets/images/files logo/jpeg.jpeg')}}" class="mr-2 thumb-lg rounded-circle" alt="...">
                                        </a>
                                        @elseif($classWork->type=='PDF')
                                        <a href="{{$classWork->fileUrl}}" target="_blank" >
                                            <img src="{{ URL::asset('assets/images/files logo/download.jpeg')}}" class="mr-2 thumb-lg rounded-circle" alt="...">
                                        </a>
                                        @elseif($classWork->type=='DOCS')
                                        <a href="{{$classWork->fileUrl}}" target="_blank" >
                                            <img src="{{ URL::asset('assets/images/files logo/docs.png')}}" class="mr-2 thumb-lg rounded-circle" alt="...">
                                        </a>
                                        @elseif($classWork->type=='YOUTUBE')
                                                <a href="{{$classWork->youtubeLink}}" target="_blank" >
                                            <img src="{{ URL::asset('assets/images/files logo/youtube.png')}}" class="mr-2 thumb-lg rounded-circle" alt="...">
                                        </a>
                                        @else
                                        <i class="mdi mdi-checkbox-marked-circle-outline bg-soft-success"></i>
                                        @endif                                                                            
                                        <div class="media-body align-self-center">                                                          
                                            <h4 class="mt-0 mb-1 title-text">{{$classWork->subject}} - {{$classWork->title}}</h4>
                                            <div class="">                                                                                
                                                <p class="text-muted mb-0">{{$classWork->name}}</p>
                                            </div>
                                            <div class="text-right">                                                                                
                                                <p class="text-muted mb-0">{{$classWork->created_at->format('d/M')}}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @if($i>3)
                                @break
                                @endif
                                @php
                                    $i=$i+1;
                                @endphp
                                @endforeach
                                @endisset
                            </div>
                            <a class="carousel-control-prev" href="#carousel_1" role="button" data-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="sr-only">Previous</span>
                            </a>
                            <a class="carousel-control-next" href="#carousel_1" role="button" data-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="sr-only">Next</span>
                            </a>
                        </div>
                        <div class="row my-3">
                            <div class="col-sm-6">
                                <p class="mb-0 text-muted font-13"><i class="mdi mdi-album mr-2 text-secondary"></i>New uploaded Work</p>                            
                            </div><!-- end col-
                            <div class="col-sm-6">
                                <p class="mb-0 text-muted font-13"><i class="mdi mdi-album mr-2 text-warning"></i>New Leads Target</p>
                            </div><!-- end col-->
                        </div><!-- end row-->
                        <div class="progress bg-warning mb-3" style="height:5px;">
                            <div class="progress-bar bg-secondary" role="progressbar" style="width: 65%" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>                                                            
                    </div><!--end card-body-->
                </div><!--end card-->
            </div> <!--end col--
            
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="p-4 bg-light text-center align-item-center">                                                                    
                            <h1 class="font-weight-semibold">4.8</h1> 
                            <h4 class="header-title">Overall Rating</h4>  
                            <ul class="list-inline mb-0 product-review">
                                <li class="list-inline-item mr-0"><i class="mdi mdi-star text-warning font-24"></i></li>
                                <li class="list-inline-item mr-0"><i class="mdi mdi-star text-warning font-24"></i></li>
                                <li class="list-inline-item mr-0"><i class="mdi mdi-star text-warning font-24"></i></li>
                                <li class="list-inline-item mr-0"><i class="mdi mdi-star text-warning font-24"></i></li>
                                <li class="list-inline-item mr-0"><i class="mdi mdi-star-half text-warning font-24"></i></li>
                                <li class="list-inline-item"><small class="text-muted">Total Review (700)</small></li>
                            </ul>                                     
                        </div> 
                    </div><!--end card-body--                                                                                                
                </div><!--end card--
            </div><!--end col--
        </div><!--end row-->   
    </div>
        @php
            $color = ["primary", "secondary", "success", "danger", "warning", "info"];
            $opposite_color = ["secondary", "primary", "danger", "warning", "info", "primary"];
            $c=0;
        @endphp
        <div class="row">
            @isset($subjects)
            @foreach ($subjects as $subject)
            @foreach ($classWorks as $classWork)
            @php
            if($c==6){
                $c=0;
            }
            @endphp
                @if($subject->class==$classWork->class && $subject->subject==$classWork->subject)
            <div class="col-lg-4">
            <a href="{{route('student.classroom',[$subject->id])}}">
                <div class="card profile-card "style="border-radius: 10%" >
                <div class="card-body p-0 bg-{{$color[$c]}} "style="border-radius: 10%">
                        <div class="media p-3  align-items-center" >                                                
                            <img src="{{ URL::asset('assets/images/teacherImg/' . $classWork->name . '.jpg')}}"class="rounded-circle thumb-xl">                                        
                            <div class="media-body ml-3 align-self-center">
                                <h5 class="pro-title text-{{$opposite_color[$c]}}">{{$subject->subject}} <span class="badge badge-warning font-10">New</span></h5>
                                <p class="mb-1 ">{{$classWork->name}}</p> 
                                <p class="mb-0"><i class="mdi mdi-bell-ring-outline text-{{$opposite_color[$c]}} mr-1"></i>{{$classWork->title}} ({{$classWork->created_at->format('d/M')}})</p>                                              
                            </div>
                            <div class="action-btn">
                                
                            </div>                                                                              
                        </div>                                    
                    </div><!--end card-body-->                 
                </div><!--end card--> 
            </a>
        </div><!--end col-->
        @php
            $c=$c+1;
        @endphp
            @break    
            @endif
            @endforeach
            @endforeach
            @endisset
        </div><!--end row-->    

                @stop

@section('footerScript')
        <script src="{{ URL::asset('plugins/dropify/js/dropify.min.js')}}"></script>
        <script src="{{ URL::asset('plugins/moment/moment.js')}}"></script>
        <script src="{{ URL::asset('plugins/filter/isotope.pkgd.min.js')}}"></script>
        <script src="{{ URL::asset('plugins/filter/masonry.pkgd.min.js')}}"></script>
        <script src="{{ URL::asset('plugins/filter/jquery.magnific-popup.min.js')}}"></script>
        <script src="{{ URL::asset('plugins/chartjs/chart.min.js')}}"></script>
        <script src="{{ URL::asset('plugins/chartjs/roundedBar.min.js')}}"></script>
        <script src="{{ URL::asset('plugins/lightpick/lightpick.js')}}"></script>
        <script src="{{ URL::asset('assets/pages/jquery.profile.init.js')}}"></script>

        <script src="{{ URL::asset('plugins/ticker/jquery.jConveyorTicker.min.js')}}"></script>
        <script src="{{ URL::asset('assets/pages/jquery.crypto-news.init.js')}}"></script>


@stop