  <!-- leftbar-tab-menu -->
  <div class="leftbar-tab-menu">
    <div class="main-icon-menu">
        <a href="/cashier/dashboard" class="logo logo-metrica d-block text-center">
            <span>
                <img src="{{ URL::asset('assets/images/gpl_logo2.png')}}" alt="logo-small" class="logo-sm">
            </span>
        </a>
        <nav class="nav">
            <a href="#fee" class="nav-link" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="Fee" data-trigger="hover">
                <i data-feather="dollar-sign" class="align-self-center menu-icon icon-dual"></i>
            </a><!--end Fee-->

            <a href="#studentsRecord" class="nav-link" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="All Records" data-trigger="hover">
                <i data-feather="users" class="align-self-center menu-icon icon-dual"></i>
            </a><!--end Students Record-->

            {{-- <a href="#MetricaAnalytics" class="nav-link" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="Live Class" data-trigger="hover">
                <i data-feather="video" class="align-self-center menu-icon icon-dual"></i>
            </a><!--end MetricaAnalytics--> --}}

             <a href="#createUser" class="nav-link" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="Create" data-trigger="hover">
                <i data-feather="user-plus" class="align-self-center menu-icon icon-dual"></i>
            </a><!--end MetricaApps-->

            <!-- Attendance-->
            <a href="#Attendance" class="nav-link" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="Attendance" data-trigger="hover">
                <i data-feather="calendar" class="align-self-center menu-icon icon-dual"></i>
            </a><!--end Attendance-->

            {{--<a href="#flashNews" class="nav-link" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="Flash News" data-trigger="hover">
                <i data-feather="rss" class="align-self-center menu-icon icon-dual"></i>
            </a><!--end MetricaUikit-->

            <a href="#createSub" class="nav-link" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="Class & Subject" data-trigger="hover">
                <i data-feather="copy" class="align-self-center menu-icon icon-dual"></i>
            </a><!--end MetricaPages-->

            <a href="#allClasswork" class="nav-link" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="Classwork" data-trigger="hover">
                <i data-feather="file" class="align-self-center menu-icon icon-dual"></i>
            </a> <!--end allClasswork-->

            <a href="#attendance" class="nav-link" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="Attendance" data-trigger="hover">
                <i data-feather="calendar" class="align-self-center menu-icon icon-dual"></i>
            </a> <!--end attendance--> --}}

        </nav><!--end nav-->

    </div><!--end main-icon-menu-->

    <div class="main-menu-inner">
        <!-- LOGO -->
        <div class="topbar-left">
            <a href="/cashier/dashboard" class="logo">
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
                          <a class="nav-link" href="/fee/allStudentsRecord"><span class="w-100">All Student Record</span></a>
                      </li><!--end nav-item-->
                  </ul>
                    <ul class="nav metismenu">
                        <li class="nav-item">
                            <a class="nav-link" href="/fee/dayBook"><span class="w-100">Day Book</span></a>
                        </li><!--end nav-item-->
                        <li class="nav-item">
                            <a class="nav-link" href="/fee/dueList"><span class="w-100">Due List</span></a>
                        </li><!--end nav-item-->
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

            <div id="studentsRecord" class="main-icon-menu-pane">
                <div class="title-box">
                    <h6 class="menu-title">View All Record</h6>
                    <ul class="nav metismenu">
                          <li class="nav-item">
                            <a class="nav-link" href="/cashier/allStudentsRecord"><span class="w-100">Students</span></a>
                        </li><!--end nav-item-->
                        <li class="nav-item">
                            <a class="nav-link" href="/cashier/allTeachersRecord"><span class="w-100">Teachers</span></a>
                        </li><!--end nav-item-->
                    </ul>
                </div>

            </div><!-- end Student Record -->

            {{-- {{-- <div id="MetricaAnalytics" class="main-icon-menu-pane">
                <div class="title-box">
                    <h6 class="menu-title">Live Classes</h6>
                    <ul class="nav metismenu">
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/liveClasses/create_liveClass"><span class="w-100">Create</span></a>
                        </li><!--end nav-item-->
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/liveClasses/allLiveClasses"><span class="w-100">View all</span></a>
                        </li><!--end nav-item-->
                    </ul>
                </div>

            {{-- </div><!-- end Analytic --> --}}
            <div id="createUser" class="main-icon-menu-pane">
                <div class="title-box">
                    <h6 class="menu-title">Create New User</h6>
                </div>
                <ul class="nav">
                    <li class="nav-item"><a class="nav-link" href="/register">Student</a></li>


                </ul>
            </div><!-- end Pages -->

            <!-- Attendance -->
                    <div id="Attendance" class="main-icon-menu-pane">
                        <div class="title-box">
                            <h6 class="menu-title">Attendance</h6>
                        </div>
                        <ul class="nav">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.attendance.index') }}">Mark Attendance</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.attendance.view') }}">View Attendance</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.attendance.day.form') }}">Day Attendance</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.attendance.continuous.form') }}">Continuous Absent</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.attendance.classes.status') }}">Class Status</a>
                            </li>

                        </ul>
                    </div>
                    <!-- end Attendance -->

            {{-- <div id="flashNews" class="main-icon-menu-pane">
                <div class="title-box">
                    <h6 class="menu-title">Flash News</h6>
                </div>
                <ul class="nav">
                    <li class="nav-item"><a class="nav-link" href="/admin/createFlashNews">Create</a></li>
                    <li class="nav-item"><a class="nav-link" href="/admin/allFlashNews">All News</a></li>

                </ul>
            </div><!-- end flashNews -->

            <div id="createSub" class="main-icon-menu-pane">
                <div class="title-box">
                    <h6 class="menu-title">Create Class,Subject & Terms</h6>
                </div>
                <ul class="nav">
                    <li class="nav-item"><a class="nav-link" href="/admin/create_subCode">Create Subject & Class</a></li>
                    <li class="nav-item"><a class="nav-link" href="/admin/allSubCodes">View Subject & Class</a></li>
                    <li class="nav-item"><a class="nav-link" href="/admin/createTerms">Create Term</a></li>
                    <li class="nav-item"><a class="nav-link" href="/admin/allTerms">View Terms</a></li>

                </ul>
            </div><!-- end createSub -->

            <div id="allClasswork" class="main-icon-menu-pane">
                <div class="title-box">
                    <h6 class="menu-title">All Classwork</h6>
                </div>
                <ul class="nav">
                    <li class="nav-item"><a class="nav-link" href="/admin/allClasswork">All Classwork</a></li>

                </ul>
            </div><!-- end allClasswork -->

            <div id="attendance" class="main-icon-menu-pane">
                <div class="title-box">
                    <h6 class="menu-title">Attendance</h6>
                </div>
                <ul class="nav">
                    <li class="nav-item"><a class="nav-link" href="/admin/addHolidays">Add Holidays</a></li>
                    <li class="nav-item"><a class="nav-link" href="/admin/calendar">Calendar</a></li>
                    <ul class="nav metismenu">
                        <li class="nav-item">
                            <a class="nav-link" href="javascript: void(0);"><span class="w-100">Students</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                            <ul class="nav-second-level" aria-expanded="false">
                                <li><a href="/admin/student/classesList">Class Wise</a></li>
                                <li><a href="/admin/student/studentsAttendance">Student wise</a></li>
                            </ul>
                        </li><!--end nav-item-->
                    <ul class="nav metismenu">
                        <li class="nav-item">
                            <a class="nav-link" href="javascript: void(0);"><span class="w-100">Teachers</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                            <ul class="nav-second-level" aria-expanded="false">
                                <li><a href="/admin/teacher/dayswiseAttendance">Day Wise</a></li>
                                <li><a href="/admin/teacher/teachersAttendance">Teacher wise</a></li>
                            </ul>
                        </li><!--end nav-item-->
                </ul>
            </div><!-- end attendance -->  --}}

        </div><!--end menu-body-->
    </div><!-- end main-menu-inner-->
</div>
<!-- end leftbar-tab-menu-->
