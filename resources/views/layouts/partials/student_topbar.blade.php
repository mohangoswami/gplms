
        <!-- Top Bar Start -->
        <div class="topbar">
            <!-- Navbar -->
            <nav class="navbar-custom">
                <ul class="list-unstyled topbar-nav float-right mb-0">


                    <li class="dropdown notification-list">
                        <a class="nav-link dropdown-toggle arrow-none waves-light waves-effect" data-toggle="dropdown" href="#" role="button"
                            aria-haspopup="false" aria-expanded="false">
                            <i class="ti-bell noti-icon"></i>

                            @php
                                $i = auth()->check() ? auth()->user()->unreadNotifications->count() : 0;
                            @endphp

                    <span class="badge badge-danger badge-pill noti-icon-badge">{{$i}}</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right dropdown-lg pt-0">

                            <h6 class="dropdown-item-text font-15 m-0 py-3 bg-primary text-white d-flex justify-content-between align-items-center">
                                Notifications

                                <span class="badge badge-light badge-pill">{{$i}}</span>
                            </h6>
                            <div class="slimscroll notification-list">


                                @if(auth()->check())

                                @foreach (Auth::user()->unreadNotifications as $notification)


                                <!-- item-->
                            @if($notification->data['workType']=='Classwork')
                                @if($notification->data['type']!='' && $notification->data['type']!='TOPIC')
                            <a href="/student/notificationClasswork/{{$notification->data['classworkId']}}/{{$notification->id}}" class="dropdown-item py-3">
                                @endif
                            @endif
                                @if($notification->data['workType']=='Exam')
                            <a href="/student/notificationExam/{{$notification->data['classworkId']}}/{{$notification->id}}" class="dropdown-item py-3">
                            @endif
                                <small class="float-right text-muted pl-2"> {{$notification->created_at->format('d/M')}}</small>
                                    <div class="media">
                                        <div class="avatar-md ">
                                                        @if($notification->data['type']=='IMG')
                                                            <img src="{{ URL::asset('assets/images/files logo/jpeg.jpeg')}}" class="mr-2 thumb-sm rounded-circle" alt="...">
                                                        @elseif($notification->data['type']=='PDF')
                                                            <img src="{{ URL::asset('assets/images/files logo/download.jpeg')}}" class="mr-2 thumb-sm rounded-circle" alt="...">
                                                        @elseif($notification->data['type']=='DOCS')
                                                            <img src="{{ URL::asset('assets/images/files logo/docs.png')}}" class="mr-2 thumb-sm rounded-circle" alt="...">
                                                        @elseif($notification->data['type']=='YOUTUBE')
                                                            <img src="{{ URL::asset('assets/images/files logo/youtube.png')}}" class="mr-2 thumb-sm rounded-circle" alt="...">

                                                        @elseif($notification->data['type']=='FORM')
                                                        <img src="{{ URL::asset('assets/images/files logo/docs.png')}}" class="mr-2 thumb-sm rounded-circle" alt="...">
                                                        @else
                                                        <i class="mdi mdi-checkbox-marked-circle-outline bg-soft-success"></i>
                                                        @endif

                                        </div>
                                        <div class="media-body align-self-center ml-2 text-truncate">
                                            <h6 class="my-0 font-weight-normal text-dark">
                                                {{$notification->data['subject']}}</h6>
                                            <small class="text-muted mb-0">{{$notification->data['workType']}}- {{$notification->data['title']}}</small>
                                        </div><!--end media-body-->
                                    </div><!--end media-->
                                </a><!--end-item-->
                                <!-- item-->
                               @endforeach
                               @endif
                        </div>
                    </li>

                    <li class="dropdown">
                        <a class="nav-link dropdown-toggle waves-effect waves-light nav-user" data-toggle="dropdown" href="#" role="button"
                            aria-haspopup="false" aria-expanded="false">
                            <div class="avatar-box thumb-md align-self-center mr-2">
                                <span class="avatar-title bg-soft-pink rounded-circle">
                            @php
                                echo substr(Auth::user()->name,0,1);
                            @endphp
                                </span>
                            </div>
                            <span class="ml-1 nav-user-name hidden-sm">{{Auth::user()->name}}<i class="mdi mdi-chevron-down"></i> </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="/student/student-update-password">
                                <i class="fas fa-edit text-info font-16"></i> Password Reset
                            </a>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                    <i class="dripicons-exit text-muted mr-2"></i> Logout
                                </a>

                                <form id="logout-form" action="{{ url('/logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>

                        </div>
                    </li>

                </ul><!--end topbar-nav-->

                <ul class="list-unstyled topbar-nav mb-0">
                    <li>
                        <a href="/home">
                            <span class="responsive-logo">
                                <img src="{{ URL::asset('assets/images/gpl_logo.png')}}" alt="logo-small" class="logo-sm align-self-center" height="34">
                            </span>
                        </a>
                    </li>
                    <li>
                        <button class="button-menu-mobile nav-link">
                            <i data-feather="menu" class="align-self-center"></i>
                        </button>
                    </li>

                    <li class="hide-phone app-search">
                        <form role="search" class="">
                            <input type="text" id="AllCompo" placeholder="Search..." class="form-control">
                            <a href=""><i class="fas fa-search"></i></a>
                        </form>
                    </li>
                </ul>
            </nav>
            <!-- end navbar-->
        </div>
        <!-- Top Bar End -->
