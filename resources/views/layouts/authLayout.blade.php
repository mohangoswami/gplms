<!DOCTYPE html>
<html lang="en">

<head>
    <!-- PWA Manifest -->
    <link rel="manifest" href="https://gplmschool-dev-assets-yhvzvk0mit5j.s3.us-east-1.amazonaws.com/manifest.json">

    <!-- Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register("{{ 'https://gplmschool-dev-assets-yhvzvk0mit5j.s3.us-east-1.amazonaws.com/service-worker.js' }}")
                .then(reg => console.log("Service Worker Registered!", reg))
                .catch(err => console.log("Service Worker Registration Failed", err));
        }
    </script>
    <meta charset="utf-8" />
        <title>@yield('title')</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
        <meta content="" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />

        <!-- App favicon -->
        <link rel="shortcut icon" href="{{ URL::asset('assets/images/favicon.ico') }}">

        <!-- App css -->
        <link href="{{ URL::asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ URL::asset('assets/css/jquery-ui.min.css') }}" rel="stylesheet">
        <link href="{{ URL::asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ URL::asset('assets/css/metisMenu.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ URL::asset('assets/css/app.css')}}" rel="stylesheet" type="text/css" />
        @yield('headerStyle')
    </head>
@section('body')
@show
    <body class="account-body accountbg">

        <!-- content -->
        @yield('content')

        <!-- jQuery  -->
        <script src="{{ URL::asset('assets/js/jquery.min.js') }}"></script>
        <script src="{{ URL::asset('assets/js/jquery-ui.min.js') }}"></script>
        <script src="{{ URL::asset('assets/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ URL::asset('assets/js/metismenu.min.js') }}"></script>
        <script src="{{ URL::asset('assets/js/waves.js') }}"></script>
        <script src="{{ URL::asset('assets/js/feather.min.js') }}"></script>
        <script src="{{ URL::asset('assets/js/jquery.slimscroll.min.js') }}"></script>

        <!-- App js -->
        <script src="{{ URL::asset('assets/js/app.js') }}"></script>

    </body>

</html>
