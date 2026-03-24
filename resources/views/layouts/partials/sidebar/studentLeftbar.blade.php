  <!-- leftbar-tab-menu -->
  <div class="leftbar-tab-menu">
    <div class="main-icon-menu">
        <a href="/home" class="logo logo-metrica d-block text-center">
            <span>
                <img src="{{ URL::asset('assets/images/gpl_logo2.png')}}" alt="logo-small" class="rounded-circle logo-sm">
            </span>
        </a>
        <nav class="nav">
            <a href="#fee" class="nav-link" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="Fee" data-trigger="hover">
                <i data-feather="dollar-sign" class="align-self-center menu-icon icon-dual"></i>
            </a><!--end Fee-->

           <a href="#classroom" class="nav-link" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="classroom" data-trigger="hover">
                <i data-feather="book-open" class="align-self-center menu-icon icon-dual"></i>
            </a><!--end classroom-->

            <a href="#exams" class="nav-link" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="Exams" data-trigger="hover">
                <i data-feather="file-text" class="align-self-center menu-icon icon-dual"></i>
            </a><!--end MetricaApps-->

           {{--   <a href="#LiveClass" class="nav-link" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="Live Classes" data-trigger="hover">
                <i data-feather="video" class="align-self-center menu-icon icon-dual"></i>
            </a><!--end MetricaUikit-->

            <a href="#Results" class="nav-link" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="Results" data-trigger="hover">
                <i data-feather="clipboard" class="align-self-center menu-icon icon-dual"></i>
            </a><!--end MetricaResults-->

            <a href="#attendance" class="nav-link" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="Attendance" data-trigger="hover">
                <i data-feather="calendar" class="align-self-center menu-icon icon-dual"></i>
            </a> <!--end attendance--> --}}


        </nav><!--end nav--
        <div class="pro-metrica-end">
            <a href="" class="help" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="Chat">
                <i data-feather="message-circle" class="align-self-center menu-icon icon-md icon-dual mb-4"></i>
            </a>
            <a href="" class="profile">
                <img src="{{ URL::asset('assets/images/users/user-4.jpg')}}" alt="profile-user" class="rounded-circle thumb-sm">
            </a>
        </div>-->
    </div><!--end main-icon-menu-->

    <div class="main-menu-inner">
        <!-- LOGO -->
        <div class="topbar-left">
            <a href="/teacher/" class="logo">
                <div class="mt-4">
                 <h2 class="text-muted font-weight-bold">G P L M S</h2>
                </div>
            </a>
        </div>
        <!--end logo-->
        <div class="menu-body slimscroll">

            <div id="fee" class="main-icon-menu-pane">
                <div class="title-box">
                    <h6 class="menu-title">Fee</h6>
                </div>
                <ul class="nav metismenu">
                    <ul class="nav metismenu">
                        <li class="nav-item">
                            <a class="nav-link" href="/student/getStudentFeeDetail"><span class="w-100">Fee</span></a>
                      </li><!--end nav-item-->
                  </ul>
                    <ul class="nav metismenu">
                        <li class="nav-item">
                            <a class="nav-link" href="/student/studentFeeCard"><span class="w-100">Fee Card</span></a>
                        </li><!--end nav-item-->
                        {{-- <li class="nav-item">
                            <a class="nav-link" href="/fee/dueList"><span class="w-100">Due List</span></a>
                        </li><!--end nav-item--> --}}
                    </ul>
                    {{-- <li class="nav-item">
                        <a class="nav-link" href="javascript: void(0);"><span class="w-100">Create</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                        <ul class="nav metismenu">
                            <li class="nav-item">
                                <a class="nav-link" href="javascript: void(0);"><span class="w-100">Fee Head</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                                <ul class="nav-second-level" aria-expanded="false">
                                    <li><a href="/fee/viewFeeHead">View</a></li>
                                    <li><a href="/fee/createFeeHead">create New</a></li>
                                </ul>
                            </li><!--end nav-item-->
                        </ul>
                        <ul class="nav metismenu">
                            <li class="nav-item">
                                <a class="nav-link" href="javascript: void(0);"><span class="w-100">Transport</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                                <ul class="nav-second-level" aria-expanded="false">
                                    <li><a href="/fee/viewRoute">View Route</a></li>
                                    <li><a href="/fee/createRoute">New Route</a></li>
                                    <li><a href="/fee/routeFeePlan">Transport fee</a></li>
                                </ul>
                            </li><!--end nav-item-->
                        </ul>
                        <ul class="nav">
                            <li class="nav-item"><a class="nav-link" href="/fee/category">Category</a></li>
                            <li class="nav-item"><a class="nav-link" href="/fee/feePlan">Fee Plan</a></li>
                            <li class="nav-item"><a class="nav-link" href="/fee/apply-concession">Concession</a></li>

                        </ul>
                    </li> --}}

                </ul>
            </div><!-- end Fee -->

             <div id="classroom" class="main-icon-menu-pane">
                <div class="title-box">
                    <h6 class="menu-title">classroom</h6>
                </div>
                <ul class="nav">
                    @isset($subCodes)
                    @foreach($subCodes as $subCode)
                            <li class="nav-item"><a class="nav-link" href="{{route('student.classroom',[$subCode->id])}}">{{$subCode->class}} - {{$subCode->subject}}</a></li>
                    @endforeach
                    @endisset

                    </ul>
            </div><!-- end classroom -->

            <div id="exams" class="main-icon-menu-pane">
                <div class="title-box">
                    <h6 class="menu-title">Exams</h6>
                </div>
                <ul class="nav">
                    <li class="nav-item"><a class="nav-link" href="/student/exams/todayExams">Today's Exams</a></li>
                    <li class="nav-item"><a class="nav-link" href="/student/exams/upcomingExams">Upcoming Exams</a></li>
                    <li class="nav-item"><a class="nav-link" href="/student/exams/allExams">All Exams</a></li>
                    <li class="nav-item"><a class="nav-link" href="/student/exams/reportCard">Report Card</a></li>
                </ul>
            </div><!-- end Exams -->

         {{--   <div id="LiveClass" class="main-icon-menu-pane">
                <div class="title-box">
                    <h6 class="menu-title">Live Classes</h6>
                </div>
                <ul class="nav">
                    <li class="nav-item"><a class="nav-link" href="/student/liveClass">Today's Live Classes</a></li>

                </ul>

            </div><!-- end LiveClass -->

            <div id="Results" class="main-icon-menu-pane">
                <div class="title-box">
                    <h6 class="menu-title">Results</h6>
                </div>
                <ul class="nav">
                    <li class="nav-item"><a class="nav-link" href="/student/results">All Results</a></li>

                </ul>
            </div><!-- end Results -->

            <div id="attendance" class="main-icon-menu-pane">
                <div class="title-box">
                    <h6 class="menu-title">Attendance</h6>
                </div>
                <ul class="nav">
                    <li class="nav-item"><a class="nav-link" href="/student/calendar">View Attendance</a></li>

                </ul>
            </div><!-- end attendance -->

            <div id="MetricaAuthentication" class="main-icon-menu-pane">
                <div class="title-box">
                    <h6 class="menu-title">Authentication</h6>
                </div>
                <ul class="nav">
                    <li class="nav-item"><a class="nav-link" href="/authentication/auth-login">Log in</a></li>
                    <li class="nav-item"><a class="nav-link" href="/authentication/auth-login-alt">Log in alt</a></li>
                    <li class="nav-item"><a class="nav-link" href="/authentication/auth-register">Register</a></li>
                    <li class="nav-item"><a class="nav-link" href="/authentication/auth-register-alt">Register-alt</a></li>
                    <li class="nav-item"><a class="nav-link" href="/authentication/auth-recover-pw">Re-Password</a></li>
                    <li class="nav-item"><a class="nav-link" href="/authentication/auth-recover-pw-alt">Re-Password-alt</a></li>
                    <li class="nav-item"><a class="nav-link" href="/authentication/auth-lock-screen">Lock Screen</a></li>
                    <li class="nav-item"><a class="nav-link" href="/authentication/auth-lock-screen-alt">Lock Screen</a></li>
                    <li class="nav-item"><a class="nav-link" href="/authentication/auth-404">Error 404</a></li>
                    <li class="nav-item"><a class="nav-link" href="/authentication/auth-404-alt">Error 404-alt</a></li>
                    <li class="nav-item"><a class="nav-link" href="/authentication/auth-500">Error 500</a></li>
                    <li class="nav-item"><a class="nav-link" href="/authentication/auth-500-alt">Error 500-alt</a></li>
                </ul>
            </div><!-- end Authentication--> --}}
        </div><!--end menu-body-->
    </div><!-- end main-menu-inner-->
</div>
<!-- end leftbar-tab-menu-->
