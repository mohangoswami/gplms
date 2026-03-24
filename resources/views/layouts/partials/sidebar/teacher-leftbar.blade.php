  <!-- leftbar-tab-menu -->
        <div class="leftbar-tab-menu">
            <div class="main-icon-menu">
                <a href="/teacher/" class="logo logo-metrica d-block text-center">
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

                    <a href="#addMaterial" class="nav-link" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="Add Material" data-trigger="hover">
                        <i data-feather="plus-square" class="align-self-center menu-icon icon-dual"></i>
                    </a><!--end MetricaApps-->

                    <a href="#Examination" class="nav-link" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="Examination" data-trigger="hover">
                        <i data-feather="file-text" class="align-self-center menu-icon icon-dual"></i>
                    </a><!--end MetricaUikit-->

                    <a href="#liveClasses" class="nav-link" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="Live classes" data-trigger="hover">
                        <i data-feather="video" class="align-self-center menu-icon icon-dual"></i>
                    </a><!--end MetricaPages-->

                    <a href="#liveClassAttendence" class="nav-link" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="Live class Attendence" data-trigger="hover">
                        <i data-feather="file" class="align-self-center menu-icon icon-dual"></i>
                    </a><!--end MetricaPages-->

                    <a href="#Attendance" class="nav-link" data-toggle="tooltip-custom" data-placement="right" title="" data-original-title="Attendance" data-trigger="hover">
                        <i data-feather="calendar" class="align-self-center menu-icon icon-dual"></i>
                    </a><!--end Attendance-->

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


                                <li class="nav-item">
                                    <a class="nav-link" href="/teacher/teacherDueList"><span class="w-100">Due List</span></a>
                                </li><!--end nav-item-->
                            </ul>


                        </ul>
                    </div><!-- end Fee -->
                    {{-- Classroom --}}
                    <div id="classroom" class="main-icon-menu-pane">
                        <div class="title-box">
                            <h6 class="menu-title">Classroom</h6>
                        </div>
                        <ul class="nav">
                            @if(isset($subCodes) && isset($classCodes))
                                @php
                                    // Build a lookup map keyed by id => subCode model for faster lookup
                                    $classById = collect($classCodes)->keyBy(function($c){
                                        return is_object($c) ? $c->id : $c['id'] ?? null;
                                    });
                                @endphp

                                {{-- If subCodes items are IDs (int/string) or full subCode models --}}
                                @foreach($subCodes as $entry)
                                    @php
                                        // If entry is model, use it directly; otherwise try lookup by id
                                        $sub = is_object($entry) ? $entry : ($classById->get($entry) ?? null);
                                    @endphp

                                    @if($sub)
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ route('teacher.classroom', [$sub->id]) }}">
                                                {{ $sub->class }} - {{ $sub->subject }}
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            @elseif(isset($subCodes))
                                {{-- fallback: if only subCodes present and they are models --}}
                                @foreach($subCodes as $sub)
                                    @if(is_object($sub))
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ route('teacher.classroom', [$sub->id]) }}">
                                                {{ $sub->class }} - {{ $sub->subject }}
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            @endif
                        </ul>
                    </div>
                    <!-- end Classroom -->

                    {{-- Add Material --}}
                    <div id="addMaterial" class="main-icon-menu-pane">
                        <div class="title-box">
                            <h6 class="menu-title">Add Material</h6>
                        </div>
                        <ul class="nav">
                            @if(isset($subCodes) && isset($classCodes))
                                @php $classById = collect($classCodes)->keyBy(function($c){ return is_object($c) ? $c->id : $c['id'] ?? null; }); @endphp

                                @foreach($subCodes as $entry)
                                    @php $sub = is_object($entry) ? $entry : ($classById->get($entry) ?? null); @endphp

                                    @if($sub)
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ route('teacher.addMaterial', [$sub->id]) }}">
                                                {{ $sub->class }} - {{ $sub->subject }}
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            @elseif(isset($subCodes))
                                @foreach($subCodes as $sub)
                                    @if(is_object($sub))
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ route('teacher.addMaterial', [$sub->id]) }}">
                                                {{ $sub->class }} - {{ $sub->subject }}
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            @endif
                        </ul>
                    </div>
                    <!-- end Add Material -->

                    {{-- Live Class Attendance --}}
                    <div id="liveClassAttendence" class="main-icon-menu-pane">
                        <div class="title-box">
                            <h6 class="menu-title">Live class Attendence</h6>
                        </div>
                        <ul class="nav">
                            @if(isset($subCodes) && isset($classCodes))
                                @php $classById = collect($classCodes)->keyBy(function($c){ return is_object($c) ? $c->id : $c['id'] ?? null; }); @endphp

                                @foreach($subCodes as $entry)
                                    @php $sub = is_object($entry) ? $entry : ($classById->get($entry) ?? null); @endphp

                                    @if($sub)
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ url('/teacher/liveClassAttendence/'.$sub->id) }}">
                                                {{ $sub->class }} - {{ $sub->subject }}
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            @elseif(isset($subCodes))
                                @foreach($subCodes as $sub)
                                    @if(is_object($sub))
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ url('/teacher/liveClassAttendence/'.$sub->id) }}">
                                                {{ $sub->class }} - {{ $sub->subject }}
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            @endif
                        </ul>
                    </div>
                    <!-- end liveClassAttendence -->

                    <!-- Attendance -->
                    <div id="Attendance" class="main-icon-menu-pane">
                        <div class="title-box">
                            <h6 class="menu-title">Attendance</h6>
                        </div>
                        <ul class="nav">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('teacher.attendance.index') }}">Mark Attendance</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('teacher.attendance.view') }}">View Attendance</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('teacher.attendance.day.form') }}">Day Attendance</a>
                            </li>
                        </ul>
                    </div>
                    <!-- end Attendance -->

                    <!-- Examination Marks Entry -->
                    <div id="Examination" class="main-icon-menu-pane">
                        <div class="title-box">
                            <h6 class="menu-title">Examination</h6>
                        </div>
                        <ul class="nav">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('teacher.results.dashboard') }}">Marks Entry</a>
                            </li>

                        </ul>
                    </div>
                    <!-- end Examination -->

                    <!-- end Terms -->
                    <!-- end Examination -->


                </div><!--end menu-body-->
            </div><!-- end main-menu-inner-->
        </div>
        <!-- end leftbar-tab-menu-->
