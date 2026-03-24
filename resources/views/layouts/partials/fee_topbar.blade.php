
        <!-- Top Bar Start -->
        <div class="topbar">
            <!-- Navbar -->
            <nav class="navbar-custom">
                <ul class="list-unstyled topbar-nav float-right mb-0">

                    <li class="dropdown">
                        <a class="nav-link dropdown-toggle waves-effect waves-light nav-user" data-toggle="dropdown" href="#" role="button"
                            aria-haspopup="false" aria-expanded="false">
                            <span class="ml-1 nav-user-name hidden-sm">{{ Auth::guard('admin')->user()->name }} <i class="mdi mdi-chevron-down"></i> </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                         <!--   <a class="dropdown-item" href="#"><i class="dripicons-user text-muted mr-2"></i> Profile</a>
                            <a class="dropdown-item" href="#"><i class="dripicons-wallet text-muted mr-2"></i> My Wallet</a>
                            <a class="dropdown-item" href="#"><i class="dripicons-gear text-muted mr-2"></i> Settings</a>
                            <a class="dropdown-item" href="#"><i class="dripicons-lock text-muted mr-2"></i> Lock screen</a>-->
                            <div class="dropdown-divider"></div>
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
                  <!--  <li class="mr-2">
                        <a href="#" class="nav-link" data-toggle="modal" data-animation="fade" data-target=".modal-rightbar">
                            <i data-feather="align-right" class="align-self-center"></i>
                        </a>
                    </li>-->
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
                    <li class="dropdown">
                        <!--<a class="nav-link dropdown-toggle waves-effect waves-light nav-user" data-toggle="dropdown" href="#" role="button"
                            aria-haspopup="false" aria-expanded="false">
                            <span class="ml-1 p-2 bg-soft-classic nav-user-name hidden-sm rounded">System <i class="mdi mdi-chevron-down"></i> </span>
                        </a>
                       <!-- <div class="dropdown-menu dropdown-xl dropdown-menu-left p-0">
                            <div class="row no-gutters">
                                <div class="col-12 col-lg-6">
                                    <div class="text-center system-text">
                                        <h4 class="text-white">The Poworfull Dashboard</h4>
                                        <p class="text-white">See all the pages Metrica.</p>
                                        <a href="#" class="btn btn-sm btn-pink mt-2">See Dashboard</a>
                                    </div>
                                    <div id="carouselExampleFade" class="carousel slide carousel-fade" data-ride="carousel">
                                        <div class="carousel-inner">
                                            <div class="carousel-item active">
                                                <img src="{{ URL::asset('assets/images/dashboard/dash-1.png')}}" class="d-block img-fluid" alt="...">
                                            </div>
                                            <div class="carousel-item">
                                                <img src="{{ URL::asset('assets/images/dashboard/dash-4.png')}}" class="d-block img-fluid" alt="...">
                                            </div>
                                            <div class="carousel-item">
                                                <img src="{{ URL::asset('assets/images/dashboard/dash-2.png')}}" class="d-block img-fluid" alt="...">
                                            </div>
                                            <div class="carousel-item">
                                                <img src="{{ URL::asset('assets/images/dashboard/dash-3.png')}}" class="d-block img-fluid" alt="...">
                                            </div>
                                        </div>
                                    </div>
                                </div><!--end col--
                                <div class="col-12 col-lg-6">
                                    <div class="divider-custom mb-0">
                                        <div class="divider-text bg-light">All Dashboard</div>
                                    </divi>
                                    <div class="p-4">
                                        <div class="row">
                                            <div class="col-6">
                                                <a class="dropdown-item mb-2" href="/analytics/analytics-index"> Analytics</a>
                                                <a class="dropdown-item mb-2" href="/crypto/crypto-index"> Crypto</a>
                                                <a class="dropdown-item mb-2" href="/crm/crm-index"> CRM</a>
                                                <a class="dropdown-item" href="/projects/projects-index"> Project</a>
                                            </div>
                                            <div class="col-6">
                                                <a class="dropdown-item mb-2" href="/ecommerce/ecommerce-index"> Ecommerce</a>
                                                <a class="dropdown-item mb-2" href="/helpdesk/helpdesk-index"> Helpdesk</a>
                                                <a class="dropdown-item" href="/hospital/hospital-index"> Hospital</a>
                                            </div>
                                        </div>
                                    </div>
                                </div><!--end col-->
                            </div><!--end row--
                        </div>--
                    </li>
                    <li class="hide-phone app-search">
                        <form role="search" class="">
                            <input type="text" id="AllCompo" placeholder="Search..." class="form-control">
                            <a href=""><i class="fas fa-search"></i></a>
                        </form>
                    </li>-->
                </ul>
            </nav>
            <!-- end navbar-->
        </div>
        <!-- Top Bar End -->
