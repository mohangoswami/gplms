<!-- leftbar-tab-menu -->
<div class="leftbar-tab-menu">

    <!-- Main Icon Menu -->
    <div class="main-icon-menu">
        <a href="/admin/" class="logo logo-metrica d-block text-center">
            <span>
                <img src="{{ asset('assets/images/gpl_logo2.png') }}" alt="logo-small" class="logo-sm">
            </span>
        </a>

        <nav class="nav">
            <a href="#fee" class="nav-link" data-toggle="tooltip-custom" data-placement="right" title="Fee" data-trigger="hover">
                <i data-feather="dollar-sign" class="align-self-center menu-icon icon-dual"></i>
            </a>

            <a href="#studentsRecord" class="nav-link" data-toggle="tooltip-custom" data-placement="right" title="All Records" data-trigger="hover">
                <i data-feather="users" class="align-self-center menu-icon icon-dual"></i>
            </a>

            <a href="#Attendance" class="nav-link" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="Attendance" data-trigger="hover">
                <i data-feather="calendar" class="align-self-center menu-icon icon-dual"></i>
            </a><!--end Attendance-->

            <a href="#MetricaAnalytics" class="nav-link" data-toggle="tooltip-custom" data-placement="right" title="Live Class" data-trigger="hover">
                <i data-feather="video" class="align-self-center menu-icon icon-dual"></i>
            </a>

            <a href="#createUser" class="nav-link" data-toggle="tooltip-custom" data-placement="right" title="Create User" data-trigger="hover">
                <i data-feather="user-plus" class="align-self-center menu-icon icon-dual"></i>
            </a>

            <a href="#createExam" class="nav-link" data-toggle="tooltip-custom" data-placement="right" title="Exams" data-trigger="hover">
                <i data-feather="file-text" class="align-self-center menu-icon icon-dual"></i>
            </a>

            <a href="#flashNews" class="nav-link" data-toggle="tooltip-custom" data-placement="right" title="Flash News" data-trigger="hover">
                <i data-feather="rss" class="align-self-center menu-icon icon-dual"></i>
            </a>

            <a href="#createSub" class="nav-link" data-toggle="tooltip-custom" data-placement="right" title="Class & Subject" data-trigger="hover">
                <i data-feather="copy" class="align-self-center menu-icon icon-dual"></i>
            </a>

           <a href="#results" class="nav-link" data-toggle="tooltip-custom" data-placement="right" title="Results" data-trigger="hover">
                <i data-feather="file" class="align-self-center menu-icon icon-dual"></i>
            </a>

            <a href="#allClasswork" class="nav-link" data-toggle="tooltip-custom" data-placement="right" title="Classwork" data-trigger="hover">
                <i data-feather="file" class="align-self-center menu-icon icon-dual"></i>
            </a>
        </nav>
    </div>
    <!-- End Main Icon Menu -->

    <!-- Main Menu Inner -->
    <div class="main-menu-inner">

        <!-- LOGO -->
        <div class="topbar-left">
            <a href="/admin/fee/dashboard" class="logo">
                <div class="mt-4">
                    <h2 class="text-muted font-weight-bold">G P L M S</h2>
                </div>
            </a>
        </div>
        <!-- End Logo -->

        <div class="menu-body slimscroll">

            <!-- Fee Section -->
            <div id="fee" class="main-icon-menu-pane">
                <div class="title-box">
                    <h6 class="menu-title">Fee</h6>
                </div>

                <ul class="nav metismenu">
                    <li class="nav-item"><a class="nav-link" href="/fee/allStudentsRecord">All Student Record</a></li>
                    <li class="nav-item"><a class="nav-link" href="/fee/dayBook">Day Book</a></li>
                    <li class="nav-item"><a class="nav-link" href="/fee/dueList">Due List</a></li>

                    <li class="nav-item">
                        <a class="nav-link" href="javascript:void(0);">
                            <span>Create</span>
                            <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li>
                                <a href="javascript:void(0);">
                                    <span>Fee Head</span>
                                    <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
                                </a>
                                <ul class="nav-third-level" aria-expanded="false">
                                    <li><a href="/fee/viewFeeHead">View</a></li>
                                    <li><a href="/fee/createFeeHead">Create New</a></li>
                                </ul>
                            </li>

                            <li>
                                <a href="javascript:void(0);">
                                    <span>Transport</span>
                                    <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
                                </a>
                                <ul class="nav-third-level" aria-expanded="false">
                                    <li><a href="/fee/viewRoute">View Route</a></li>
                                    <li><a href="/fee/createRoute">New Route</a></li>
                                    <li><a href="/fee/routeFeePlan">Transport Fee</a></li>
                                </ul>
                            </li>

                            <li><a href="/fee/category">Category</a></li>
                            <li><a href="/fee/feePlan">Fee Plan</a></li>
                            <li><a href="/fee/apply-concession">Concession</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <!-- End Fee Section -->

            <!-- Student Records -->
            <div id="studentsRecord" class="main-icon-menu-pane">
                <div class="title-box">
                    <h6 class="menu-title">View All Records</h6>
                </div>
                <ul class="nav metismenu">
                    <li><a class="nav-link" href="/admin/allStudentsRecord">Students</a></li>
                    <li><a class="nav-link" href="/admin/allTeachersRecord">Teachers</a></li>
                    <li><a class="nav-link" href="/admin/allCashierRecord">Cashier</a></li>
                </ul>
            </div>
            <!-- End Student Records -->

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

            <!-- Live Classes -->
            <div id="MetricaAnalytics" class="main-icon-menu-pane">
                <div class="title-box">
                    <h6 class="menu-title">Live Classes</h6>
                </div>
                <ul class="nav metismenu">
                    <li><a class="nav-link" href="/admin/liveClasses/create_liveClass">Create</a></li>
                    <li><a class="nav-link" href="/admin/liveClasses/allLiveClasses">View All</a></li>
                </ul>
            </div>
            <!-- End Live Classes -->

            <!-- Create Users -->
            <div id="createUser" class="main-icon-menu-pane">
                <div class="title-box">
                    <h6 class="menu-title">Create New User</h6>
                </div>
                <ul class="nav">
                    <li><a class="nav-link" href="/register">Student</a></li>
                    <li><a class="nav-link" href="/teacher/register">Teacher</a></li>
                    <li><a class="nav-link" href="/admin/register">Admin</a></li>
                </ul>
            </div>
            <!-- End Create Users -->

            <!-- Flash News -->
            <div id="flashNews" class="main-icon-menu-pane">
                <div class="title-box">
                    <h6 class="menu-title">Flash News</h6>
                </div>
                <ul class="nav">
                    <li><a class="nav-link" href="/admin/createFlashNews">Create</a></li>
                    <li><a class="nav-link" href="/admin/allFlashNews">All News</a></li>
                </ul>
            </div>
            <!-- End Flash News -->

            <!-- Create Exam -->
            <div id="createExam" class="main-icon-menu-pane">
                <div class="title-box">
                    <h6 class="menu-title">Exams Management</h6>
                </div>
                <ul class="nav">
                    <li><a class="nav-link" href="/admin/exams/create">Create Exam</a></li>
                    <li><a class="nav-link" href="/admin/exams/">View Exams</a></li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.examMarks.index') }}">
                            <i class="fas fa-clipboard-list"></i>
                            <span>Exam Marks Entry</span>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- End Create Exam -->

            <!-- Create Subjects -->
            <div id="createSub" class="main-icon-menu-pane">
                <div class="title-box">
                    <h6 class="menu-title">Class, Subject & Terms</h6>
                </div>
                <ul class="nav">
                    <li><a class="nav-link" href="/admin/create_subCode">Create Subject & Class</a></li>
                    <li><a class="nav-link" href="/admin/allSubCodes">View Subject & Class</a></li>
                    <li><a class="nav-link" href="/admin/createTerms">Create Term</a></li>
                    <li><a class="nav-link" href="/admin/allTerms">View Terms</a></li>
                </ul>
            </div>
            <!-- End Create Subjects -->


             <!-- Results -->
            <div id="results" class="main-icon-menu-pane">
                <div class="title-box">
                    <h6 class="menu-title">Results</h6>
                </div>

                <ul class="nav metismenu">

                    <!-- Result Permission -->
                    <li>
                        <a class="nav-link" href="/admin/result-permissions">
                            Result Permission
                        </a>
                    </li>

                    <li>
                        <a class="nav-link" href="/admin/result-permissions/summary">
                            Permission Summary
                        </a>
                    </li>

                    <!-- Marks Entry -->
                    <li>
                        <a class="nav-link" href="/admin/results/student-list">
                            Marks Entry
                        </a>
                    </li>

                    <!-- Result Performa -->
                    <li>
                        <a href="javascript:void(0);">
                            <span>Result Performa</span>
                            <span class="menu-arrow">
                                <i class="mdi mdi-chevron-right"></i>
                            </span>
                        </a>

                        <ul class="nav-second-level" aria-expanded="false">


                            <li>
                                <a href="/admin/result-performa/terms">
                                    Terms Setup
                                </a>
                            </li>
                            <li>
                                <a href="/admin/result-performa/components">
                                    Components Setup
                                </a>
                            </li>

                            <li>
                                <a href="/admin/result-performa/mapping">
                                    Mapping Setup
                                </a>
                            </li>
                            <li>
                                <a href="/admin/results/co-scholastic">
                                    Co-Scholastic
                                </a>
                            </li>
                        </ul>
                    </li>

                </ul>
            </div>

            <!-- End Results -->

            <!-- All Classwork -->
            <div id="allClasswork" class="main-icon-menu-pane">
                <div class="title-box">
                    <h6 class="menu-title">All Classwork</h6>
                </div>
                <ul class="nav">
                    <li><a class="nav-link" href="/admin/allClasswork">All Classwork</a></li>
                </ul>
            </div>
            <!-- End Classwork -->

        </div><!-- End menu-body -->
    </div><!-- End main-menu-inner -->
</div>
<!-- end leftbar-tab-menu -->
