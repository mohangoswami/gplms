
        <!-- Top Bar Start -->
        <div class="topbar">
            <!-- Navbar -->
            <nav class="navbar-custom">
                <ul class="list-unstyled topbar-nav float-right mb-0">
                    <li class="hidden-sm">
                        <a class="nav-link dropdown-toggle waves-effect waves-light" data-toggle="dropdown" href="javascript: void(0);" role="button"
                            aria-haspopup="false" aria-expanded="false">
                            Fee  <i class="mdi mdi-chevron-down"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="/fee"><span> Deshboard </span></a>
                            <a class="dropdown-item" href="javascript: void(0);"><span> Deposit </span></a>

                        </div>
                    </li>



                    <li class="dropdown">
                        <a class="nav-link dropdown-toggle waves-effect waves-light nav-user" data-toggle="dropdown" href="#" role="button"
                            aria-haspopup="false" aria-expanded="false">
                            <span class="ml-1 nav-user-name hidden-sm">{{ Auth::guard('admin')->user()->name }} <i class="mdi mdi-chevron-down"></i> </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">

                            <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="/admin/admin-update-password">
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
                        <a href="/crm/crm-index">
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

                </ul>
            </nav>
            <!-- end navbar-->
        </div>
        <!-- Top Bar End -->
