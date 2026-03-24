
        <!-- Top Bar Start -->
        <div class="topbar">
            <!-- Navbar -->
            <nav class="navbar-custom">
                <ul class="list-unstyled topbar-nav float-right mb-0">


                    <li class="dropdown notification-list">
                        <a class="nav-link dropdown-toggle arrow-none waves-light waves-effect" data-toggle="dropdown" href="#" role="button"
                            aria-haspopup="false" aria-expanded="false">
                            <i class="ti-bell noti-icon"></i>
                            <span class="badge badge-danger badge-pill noti-icon-badge">0</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right dropdown-lg pt-0">

                            <h6 class="dropdown-item-text font-15 m-0 py-3 bg-primary text-white d-flex justify-content-between align-items-center">
                                Notifications <span class="badge badge-light badge-pill">0</span>
                            </h6>
                            <div class="slimscroll notification-list">
                                <!-- item-->

                            </div>
                            <!-- All-->

                        </div>
                    </li>

                    <li class="dropdown">
                        <a class="nav-link dropdown-toggle waves-effect waves-light nav-user" data-toggle="dropdown" href="#" role="button"
                            aria-haspopup="false" aria-expanded="false">
                            <div class="avatar-box thumb-md align-self-center mr-2">
                                <span class="avatar-title bg-soft-pink rounded-circle">
                                @php
                                    // echo substr(Auth::user()->name,0,1);
                                @endphp

                                </span>
                            </div>
                            @if(Auth::check())
                            <span class="ml-1 nav-user-name hidden-sm">{{ Auth::user()->name }} <i class="mdi mdi-chevron-down"></i></span>
                        @else
                            <span class="ml-1 nav-user-name hidden-sm">. <i class="mdi mdi-chevron-down"></i></span>
                        @endif
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="/teacher/teacher-update-password">
                                <i class="fas fa-edit text-info font-16"></i> Password Reset
                            </a>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                    <i class="dripicons-exit text-muted mr-2"></i> Logout
                                </a>

                                <form id="logout-form" action="{{ url('/logout') }}" method="post" class="d-none">
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
                    <li class="dropdown">

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
