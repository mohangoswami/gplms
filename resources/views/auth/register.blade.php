@extends(Auth::guard('admin')->check() ? 'layouts.admin_analytics-master' : 'layouts.cashier-master')

@section('headerStyle')
        <!-- Plugins css -->
<link href="{{ URL::asset('plugins/daterangepicker/daterangepicker.css')}}" rel="stylesheet" />
<link href="{{ URL::asset('plugins/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('plugins/timepicker/bootstrap-material-datetimepicker.css')}}" rel="stylesheet">
<link href="{{ URL::asset('plugins/bootstrap-touchspin/css/jquery.bootstrap-touchspin.min.css')}}" rel="stylesheet" />
@stop

@section('content')
@if (session('status'))
    <div class="alert alert-success b-round mt-3 ">
        {{ session('status') }}
    </div>
@endif
@if (session('failed'))
<div class="alert alert-danger b-round  mt-3 ">
    {{ session('failed') }}
</div>
@endif
@if (session('delete'))
<div class="alert alert-warning b-round  mt-3">
    {{ session('delete') }}
</div>
@endif
  {{-- <!-- Log In page -->
        <div class="container">
            <div class="row vh-200 ">
                <div class="col-12 align-self-center">
                    <div class="auth-page">
                        <div class="card auth-card shadow-lg">
                            <div class="card-body">
                                <div class="px-3">
                                    <div class="auth-logo-box">
                                        <a href="/" class="logo logo-admin"><img src="{{ URL::asset('assets/images/gpl_logo2.png') }}" height="55" alt="logo" class="auth-logo"></a>
                                    </div><!--end auth-logo-box-->

                                    <div class="text-center auth-logo-text">
                                        <h4 class="mt-0 mb-3 mt-5">Create New Student Record</h4>
                                        <p class="text-muted mb-0">Please provide information carfully.</p>
                                    </div> <!--end auth-logo-text-->

                                    <form class="form-horizontal auth-form my-4" method="POST" action="{{ route('register') }}">

                                     @csrf
                                     <div class="form-group">
                                        <label for="srNo">Admission Number</label>
                                        <div class="input-group mb-3">
                                            <span class="auth-form-icon">
                                                <i class="dripicons-user"></i>
                                            </span>

                                            <input id="srNo" placeholder="S R Number" type="text" class="form-control @error('name') is-invalid @enderror" name="srNo" value="{{ old('srNo') }}" required autocomplete="srNo" autofocus>

                                            @error('srNo')
                                            <span class="invalid-feedback" role="alert">
                                                  <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div><!--end form-group-->
                                    <div class="form-group">
                                        <label for="username">Name</label>
                                        <div class="input-group mb-3">
                                            <span class="auth-form-icon">
                                                <i class="dripicons-user"></i>
                                            </span>

                                            <input id="name" placeholder="Name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                            @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                  <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div><!--end form-group-->
                                    <div class="form-group">
                                        <label for="fName">Father Name</label>
                                        <div class="input-group mb-3">
                                            <span class="auth-form-icon">
                                                <i class="dripicons-user"></i>
                                            </span>

                                            <input id="fName" placeholder="Father Name" type="text" class="form-control @error('fName') is-invalid @enderror" name="fName" value="{{ old('fName') }}" required autocomplete="fName" autofocus>

                                            @error('fName')
                                            <span class="invalid-feedback" role="alert">
                                                  <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div><!--end form-group-->
                                    <div class="form-group">
                                        <label for="mName">Mother Name</label>
                                        <div class="input-group mb-3">
                                            <span class="auth-form-icon">
                                                <i class="dripicons-user"></i>
                                            </span>

                                            <input id="mName" placeholder="Mother Name" type="text" class="form-control @error('mName') is-invalid @enderror" name="mName" value="{{ old('mName') }}" required autocomplete="mName" autofocus>

                                            @error('mName')
                                            <span class="invalid-feedback" role="alert">
                                                  <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div><!--end form-group-->
                                    <div class="form-group">
                                        <label for="dob">Date of Birth</label>
                                        <div class="input-group mb-3">
                                            <span class="auth-form-icon">
                                                <i class="dripicons-clock"></i>
                                            </span>

                                            <input id="dob" placeholder="Date Of Birth" type="date" class="form-control @error('dob') is-invalid @enderror" name="dob" value="{{ old('dob') }}" required autocomplete="dob" autofocus>

                                            @error('dob')
                                            <span class="invalid-feedback" role="alert">
                                                  <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div><!--end form-group-->
                                    <div class="form-group">
                                        <label class="mb-3">Category</label>
                                       @isset($categories)
                                        <select id="category" name="category" class="select2 form-control mb-3 custom-select" style="width: 100%; height:36px;">
                                            @foreach($categories as $category)
                                            <option value="{{$category->category}}">{{$category->category}}</option>
                                           @endforeach
                                        </select>
                                        @endisset
                                    </div><!-- end col -->
                                    <div class="form-group">
                                        <label class="mb-3"> Route</label>
                                       @isset($routes)
                                        <select id="route" name="route" class="select2 form-control mb-3 custom-select" style="width: 100%; height:36px;">
                                            @foreach($routes as $route)
                                            <option value="{{$route->routeName}}">{{$route->routeName}}</option>
                                           @endforeach
                                        </select>
                                        @endisset
                                    </div><!-- end col -->
                                    <div class="form-group">
                                        <label for="address">Address</label>
                                        <div class="input-group mb-3">
                                            <span class="auth-form-icon">
                                                <i class="dripicons-user"></i>
                                            </span>

                                            <input id="address" placeholder="Address" type="text" class="form-control @error('address') is-invalid @enderror" name="address" value="{{ old('address') }}" required autocomplete="address" autofocus>

                                            @error('address')
                                            <span class="invalid-feedback" role="alert">
                                                  <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div><!--end form-group-->
                                    <div class="form-group">
                                        <label for="mobile">Mobile Number</label>
                                        <div class="input-group mb-3">
                                            <span class="auth-form-icon">
                                                <i class="dripicons-phone"></i>
                                            </span>

                                            <input id="mobile" placeholder="Mobile Number" type="number" class="form-control @error('mobile') is-invalid @enderror" name="mobile" value="{{ old('mobile') }}" required autocomplete="mobile" autofocus>

                                            @error('mobile')
                                            <span class="invalid-feedback" role="alert">
                                                  <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div><!--end form-group-->

                                        <div class="form-group">
                                        <label for="rfid">RFID</label>
                                        <div class="input-group mb-3">
                                            <span class="auth-form-icon">
                                                <i class="dripicons-feed"></i>
                                            </span>

                                            <input id="rfid" placeholder="RFID Number" type="text" class="form-control @error('rfid') is-invalid @enderror" name="rfid" value="{{ old('rfid') }}" required autocomplete="rfid" autofocus>

                                            @error('rfid')
                                            <span class="invalid-feedback" role="alert">
                                                  <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div><!--end form-group-->

                                        <div class="form-group">
                                            <label for="useremail">E-Mail Address</label>
                                            <div class="input-group mb-3">
                                                <span class="auth-form-icon">
                                                    <i class="dripicons-mail"></i>
                                                </span>
                                               <input id="email" placeholder="Email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                                          @error('email')
                                         <span class="invalid-feedback" role="alert">
                                               <strong>{{ $message }}</strong>
                                           </span>
                                           @enderror
                                            </div>
                                        </div><!--end form-group-->

                                        <div class="form-group">
                                            <label for="userpassword">Password</label>
                                            <div class="input-group mb-3">
                                                <span class="auth-form-icon">
                                                    <i class="dripicons-lock"></i>
                                                </span>
                                               <input id="password" type="password" placeholder="Password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                        @error('password')
                                        <span class="invalid-feedback" role="alert">
                                           <strong>{{ $message }}</strong>
                                        </span>
                                         @enderror

                                            </div>

                                        <div class="form-group">
                                            <label for="conf_password">Confirm Password</label>
                                            <div class="input-group mb-3">
                                                <span class="auth-form-icon">
                                                    <i class="dripicons-lock-open"></i>
                                                </span>

                                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password" required autocomplete="new-password">

                                            </div>

                                            <div class="form-group">
                                                <label class="mb-3"> Class</label>
                                               @isset($classes)
                                                <select id="grade" name="grade" class="select2 form-control mb-3 custom-select" style="width: 100%; height:36px;">
                                                    @foreach($classes as $class)
                                                    <option value="{{$class}}">{{$class}}</option>
                                                   @endforeach
                                                </select>
                                                @endisset
                                            </div><!-- end col -->
                                        </div><!--end form-group-->

                                        <div class="form-group mb-0 row">
                                            <div class="col-12 mt-2">
                                                <button class="btn btn-gradient-primary btn-round btn-block waves-effect waves-light" type="submit">Register <i class="fas fa-sign-in-alt ml-1"></i></button>
                                            </div><!--end col-->
                                        </div> <!--end form-group-->
                                    </form><!--end form-->
                                </div><!--end /div-->


                            </div><!--end card-body-->
                        </div><!--end card-->
                    </div><!--end auth-card-->
                </div><!--end col-->
            </div><!--end row-->
        </div><!--end container-->
        <!-- End Log In page --> --}}


        @section('content')
        <div class="container mt-5">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white text-center">
                    <h3>Student Registration Form</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('register') }}" method="POST">
                        @csrf

                        <div class="row g-3">



                            <div class="col-md-6">
                                <label for="admission_number">Admission Number</label>
                                    <input id="admission_number" placeholder="S R Number" type="text" class="form-control @error('name') is-invalid @enderror" name="admission_number" value="{{ old('admission_number') }}" required autocomplete="admission_number" autofocus>
                                    @error('admission_number')
                                    <span class="invalid-feedback" role="alert">
                                          <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                            </div><!--end form-group-->

                            <div class="col-md-6">
                                <label for="rollNo">Roll Number</label>


                                    <input id="rollNo" placeholder="S R Number" type="text" class="form-control @error('name') is-invalid @enderror" name="rollNo" value="{{ old('rollNo') }}" required autocomplete="rollNo" autofocus>

                                    @error('rollNo')
                                    <span class="invalid-feedback" role="alert">
                                          <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                            </div><!--end form-group-->
                            <div class="col-md-6">
                                <label for="username">Name</label>
                                    <input id="name" placeholder="Name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                          <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                            </div><!--end form-group-->
                            <div class="col-md-6">
                                <label for="fName">Father Name</label>
                                 <input id="fName" placeholder="Father Name" type="text" class="form-control @error('fName') is-invalid @enderror" name="fName" value="{{ old('fName') }}" required autocomplete="fName" autofocus>
                                    @error('fName')
                                    <span class="invalid-feedback" role="alert">
                                          <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                            </div><!--end form-group-->
                            <div class="col-md-6">
                                <label for="mName">Mother Name</label>
                                   <input id="mName" placeholder="Mother Name" type="text" class="form-control @error('mName') is-invalid @enderror" name="mName" value="{{ old('mName') }}" required autocomplete="mName" autofocus>
                                    @error('mName')
                                    <span class="invalid-feedback" role="alert">
                                          <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                            </div><!--end form-group-->

                            <div class="col-md-6">
                                <label for="dob">Date of Birth</label>
                                  <input id="dob" placeholder="Date Of Birth" type="date" class="form-control @error('dob') is-invalid @enderror" name="dob" value="{{ old('dob') }}" required autocomplete="dob" autofocus>
                                    @error('dob')
                                    <span class="invalid-feedback" role="alert">
                                          <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                            </div><!--end form-group-->
                            <div class="col-md-6">
                                <label class="mb-3">Category</label>
                               @isset($categories)
                                <select id="category" name="category" class="select2 form-control mb-3 custom-select" style="width: 100%; height:36px;">
                                    @foreach($categories as $category)
                                    <option value="{{$category->category}}">{{$category->category}}</option>
                                   @endforeach
                                </select>
                                @endisset
                            </div><!-- end col -->
                            <div class="col-md-6">
                                <label class="mb-3"> Route</label>
                               @isset($routes)
                                <select id="route" name="route" class="select2 form-control mb-3 custom-select" style="width: 100%; height:36px;">
                                    @foreach($routes as $route)
                                    <option value="{{$route->routeName}}">{{$route->routeName}}</option>
                                   @endforeach
                                </select>
                                @endisset
                            </div><!-- end col -->
                            <div class="col-md-6">
                                <label for="address">Address</label>
                                    <input id="address" placeholder="Address" type="text" class="form-control @error('address') is-invalid @enderror" name="address" value="{{ old('address') }}" required autocomplete="address" autofocus>

                                    @error('address')
                                    <span class="invalid-feedback" role="alert">
                                          <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                            </div><!--end form-group-->

                            <div class="col-md-6">
                                <label for="mobile">Mobile Number</label>
                                  <input id="mobile" placeholder="Mobile Number" type="number" class="form-control @error('mobile') is-invalid @enderror" name="mobile" value="{{ old('mobile') }}" required autocomplete="mobile" autofocus>

                                    @error('mobile')
                                    <span class="invalid-feedback" role="alert">
                                          <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                            </div><!--end form-group-->

                            <div class="col-md-6">
                                <label for="rfid">RFID</label>
                                    <span class="auth-form-icon">
                                        <i class="dripicons-feed"></i>
                                    </span>

                                    <input id="rfid" placeholder="RFID Number" type="text" class="form-control @error('rfid') is-invalid @enderror" name="rfid" value="{{ old('rfid') ?? 0 }} "  autocomplete="rfid" autofocus>

                                    @error('rfid')
                                    <span class="invalid-feedback" role="alert">
                                          <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                            </div><!--end form-group-->

                            <div class="col-md-6">
                                <label for="useremail">E-Mail Address</label>
                                   <input id="email" placeholder="Email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                              @error('email')
                             <span class="invalid-feedback" role="alert">
                                   <strong>{{ $message }}</strong>
                               </span>
                               @enderror
                            </div><!--end form-group-->

                            <div class="col-md-6">
                                <label for="userpassword">Password</label>
                                 <input id="password" type="password" placeholder="Password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                                </span>
                                @enderror

                            </div>

                            <div class="col-md-6">
                                <label for="conf_password">Confirm Password</label>
                                  <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password" required autocomplete="new-password">
                                </div>

                            <div class="col-md-6">
                                <label class="form-label">Class</label>
                               @isset($classes)
                                <select id="grade" name="grade" class="select2 form-control mb-3 custom-select" style="width: 100%; height:36px;">
                                    @foreach($classes as $class)
                                    <option value="{{$class}}">{{$class}}</option>
                                   @endforeach
                                </select>
                                @endisset
                            </div>


                            <div class="col-md-6">
                                <label class="form-label">Section</label>
                                <input type="text" name="section" value="{{ old('section') }}" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Aadhar Number</label>
                                <input type="text" name="aadhar" value="{{ old('aadhar') }}" maxlength="16" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">PEN Number</label>
                                <input type="text" name="pen" value="{{ old('pen') }}" maxlength="16" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">APAAR Number</label>
                                <input type="text" name="apaar" value="{{ old('apaar') }}" maxlength="16" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">House</label>
                                <input type="text" name="house" value="{{ old('house') }}" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Caste</label>
                                <input type="text" name="caste" value="{{ old('caste') }}" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Gender</label>
                                <select name="gender" class="form-select">
                                    <option value="">Select</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-success btn-lg mt-3">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endsection

@section('footerScript')
<!-- Plugins js -->
        <script src="{{ URL::asset('plugins/moment/moment.js')}}"></script>
        <script src="{{ URL::asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
        <script src="{{ URL::asset('plugins/select2/select2.min.js')}}"></script>
        <script src="{{ URL::asset('plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js')}}"></script>
        <script src="{{ URL::asset('plugins/timepicker/bootstrap-material-datetimepicker.js')}}"></script>
        <script src="{{ URL::asset('plugins/bootstrap-maxlength/bootstrap-maxlength.min.js')}}"></script>
        <script src="{{ URL::asset('plugins/bootstrap-touchspin/js/jquery.bootstrap-touchspin.min.js')}}"></script>
        <script src="{{ URL::asset('assets/pages/jquery.forms-advanced.js')}}"></script>


@stop
