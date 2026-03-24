
@extends('layouts.student_master')


@section('headerStyle')
        <link href="{{ URL::asset('plugins/dropify/css/dropify.min.css')}}" rel="stylesheet">
        <link href="{{ URL::asset('plugins/filter/magnific-popup.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{ URL::asset('plugins/lightpick/lightpick.css')}}" rel="stylesheet" />

@stop

@section('content')

@php
        $color = ["primary", "secondary", "success", "danger", "warning", "info"];
        $opposite_color = ["secondary", "primary", "danger", "warning", "info", "primary"];
        $c=0;
    @endphp
    <div class="m-3">
        <h3>Today's Live Classes</h3>
    </div>
    <div class="row mt-3">
        
        @foreach ($subCodes as $subCode)

@php
            $days=NULL;
          $currentTimestamp = strtotime(date('H:i:s'));
          if(isset($subCode->start_time)){
          $starttime = $subCode->start_time;
          $startTimestamp = strtotime($starttime);

        }else{
        continue;

        }
          $endtime =$subCode->end_time;
          $endTimestamp = strtotime($endtime);
          $numOfSecondsToReload = $startTimestamp - $currentTimestamp;
          $numOfSecondsToEnd = $endTimestamp - $currentTimestamp;
  
         /*echo date('H:i:s')."<br>";
          echo $starttime."<br>";
          echo $numOfSecondsToReload."<br>";
          echo $endtime."<br>";
          echo $numOfSecondsToEnd."<br>";*/
          $none = true; 
          if(isset($startTimestamp)){
            $liveat = true;  
          }else{
            $liveat = false;  
          }

          if($subCode->Monday=="on"){
              $Monday="Monday";
              $days[]="Monday";
          }else {
              $Monday=Null;
          }
          if($subCode->Tuesday=="on"){
              $Tuesday="Tuesday";
              $days[]="Tuesday";
          }else {
              $Tuesday=Null;
          }
          if($subCode->Wednesday=="on"){
              $Wednesday="Wednesday";
              $days[]="Wednesday";
          }else {
              $Wednesday=Null;
          }
          if($subCode->Thursday=="on"){
              $Thursday="Thursday";
              $days[]="Thursday";
          }else {
              $Thursday=Null;
          }
          if($subCode->Friday=="on"){
              $Friday="Friday";
              $days[]="Friday";
          }else {
              $Friday=Null;
          }
          if($subCode->Saturday=="on"){
              $Saturday="Saturday";
              $days[]="Saturday";
          }else {
              $Saturday=Null;
          }
          if($subCode->Sunday=="on"){
              $Sunday="Sunday";
              $days[]="Sunday";
          }else {
              $Sunday=Null;
          }
          $check = false;
          if(isset($days)){
          foreach ($days as $day) {
              if($day==date('l')){
                  $check = true;
              }
          }
        }
          if($currentTimestamp >= $startTimestamp && $currentTimestamp < $endTimestamp){
           
            if($Monday==date('l')){
           
            $none = false;
            $liveat = false;
          }
          if($Tuesday==date('l')){
        
            $none =false;
            $liveat =false;
          } if($Wednesday==date('l')){
            $none =false;
            $liveat =false;
           } if($Thursday==date('l')){
       
            $none =false;
            $liveat =false;
         } if($Friday==date('l')){
            $none =false;
            $liveat =false;
          } if($Saturday==date('l')){
            $none =false;
            $liveat =false;
          } if($Sunday==date('l')){
            $none =false;
            $liveat =false;
          }
        }
@endphp
        @if($check==true)
        @php
        if($c==6){
            $c=0;
        }
        @endphp
            <div class="col-lg-4">
            <div class="card profile-card bg-info "style="border-radius: 10%" >
            <div class="card-body p-0 bg-{{$color[$c]}} "style="border-radius: 20%">
                    <div class="media p-3  align-items-center" >                                                
                        <img src="{{ URL::asset('assets/images/cards/google-meet-logo.png')}}" alt="user" class="rounded-circle thumb-xl">                                        
                        <div class="media-body ml-3 align-self-center">
                            <h5 class="pro-title text-{{$opposite_color[$c]}}">{{$subCode->subject}} 
                                <span class="badge badge-warning font-10" @if($liveat==true)style="display: none;"@endif >Live Now</span></h5>
                            <p class="mb-0"><i class="fa fa-clock text-{{$opposite_color[$c]}} mr-1"></i><i class="fas fa-play text-{{$opposite_color[$c]}} mr-1"></i>{{date('h:ia', strtotime($subCode->start_time))}}</p>                                              
                            <p class="mb-0"><i class="fa fa-clock text-{{$opposite_color[$c]}} mr-1"></i><i class="fas fa-stop text-{{$opposite_color[$c]}} mr-1"></i>{{date('h:ia', strtotime($subCode->end_time))}}</p>                                              
                        </div>
                        @if($liveat==false)
                        <div class="button-list btn-social-icon">                                                
                            <a href="/student/liveAttendence/{{$subCode->id}}" class="btn btn-{{$opposite_color[$c]}} btn-circle " >
                                Join 
                            </a>
                        </div>
                        @endif                                                                           
                    </div>
                    <div class="ml-4 mb-3 class="d-flex justify-content-between">
                      @if($subCode->Monday=="on")
                        <div class="avatar-box thumb-sm align-self-center mr-2">
                            <span class="avatar-title bg-soft-{{$opposite_color[$c]}} rounded-circle">Mon</span>
                        </div>
                        @endif
                        @if($subCode->Tuesday=="on")
                        <div class="avatar-box thumb-sm align-self-center mr-2">
                            <span class="avatar-title bg-soft-{{$opposite_color[$c]}}  rounded-circle">Tue</span>
                        </div>
                        @endif
                        @if($subCode->Wednesday=="on")
                        <div class="avatar-box thumb-sm align-self-center mr-2">
                            <span class="avatar-title bg-soft-{{$opposite_color[$c]}}  rounded-circle">Wed</span>
                        </div>
                        @endif
                        @if($subCode->Thursday=="on")
                        <div class="avatar-box thumb-sm align-self-center mr-2">
                            <span class="avatar-title bg-soft-{{$opposite_color[$c]}}  rounded-circle">Thu</span>
                        </div>
                        @endif
                        @if($subCode->Friday=="on")
                        <div class="avatar-box thumb-sm align-self-center mr-2">
                            <span class="avatar-title bg-soft-{{$opposite_color[$c]}}  rounded-circle">Fri</span>
                        </div>
                        @endif
                        @if($subCode->Saturday=="on")
                        <div class="avatar-box thumb-sm align-self-center mr-2">
                            <span class="avatar-title bg-soft-{{$opposite_color[$c]}}  rounded-circle">Sat</span>
                        </div>
                        @endif
                        @if($subCode->Sunday=="on")
                        <div class="avatar-box thumb-sm align-self-center mr-2">
                            <span class="avatar-title bg-soft-{{$opposite_color[$c]}}  rounded-circle">Sun</span>
                        </div>
                        @endif

                        
                    </div>                                     
                </div><!--end card-body-->                 
            </div><!--end card--> 
    </div><!--end col-->
    @php
        $c=$c+1;
    @endphp
        
        @endif
        @endforeach
    </div><!--end row-->    
@stop

@section('footerScript')
<script type="text/javascript">
  
     window.onload = function() {
 var timeout =  setInterval(function() {
    location.reload(true);
  }, 60000); 

};
</script>

<script src="{{ URL::asset('plugins/dropify/js/dropify.min.js')}}"></script>
        <script src="{{ URL::asset('plugins/moment/moment.js')}}"></script>
        <script src="{{ URL::asset('plugins/filter/isotope.pkgd.min.js')}}"></script>
        <script src="{{ URL::asset('plugins/filter/masonry.pkgd.min.js')}}"></script>
        <script src="{{ URL::asset('plugins/filter/jquery.magnific-popup.min.js')}}"></script>
        <script src="{{ URL::asset('plugins/chartjs/chart.min.js')}}"></script>
        <script src="{{ URL::asset('plugins/chartjs/roundedBar.min.js')}}"></script>
        <script src="{{ URL::asset('plugins/lightpick/lightpick.js')}}"></script>
        <script src="{{ URL::asset('assets/pages/jquery.profile.init.js')}}"></script>

@stop