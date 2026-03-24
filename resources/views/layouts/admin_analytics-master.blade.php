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
        <title>GPLMSchool</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta content="Subodham.com E-Learning Solution" name="description" />
        <meta content="" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />

        <!-- App favicon -->
        <link rel="shortcut icon" href="{{ URL::asset('assets/images/favicon.ico') }}">

        @yield('headerStyle')

        <!-- App css -->
        <link href="{{ URL::asset('assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{ URL::asset('assets/css/jquery-ui.min.css')}}" rel="stylesheet">
        <link href="{{ URL::asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{ URL::asset('assets/css/metisMenu.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{ URL::asset('assets/css/app.min.css')}}" rel="stylesheet" type="text/css" />

    </head>

    <body>

         <!-- leftbar -->
        @include('layouts/partials/sidebar/admin-leftbar')

         <!-- toptbar -->
        @include('layouts/partials/admin_topbar')

        <div class="page-wrapper">

            <!-- Page Content-->
            <div class="page-content-tab">

             <!-- content -->
             @yield('content')

             <!-- extra Modal -->
             @include('layouts/partials/extra-modal')

              <!-- Footer -->
              @include('layouts/partials/footer')

            </div>
            <!-- end page content -->
        </div>
        <!-- end page-wrapper -->

        <!-- jQuery  -->
        <script src="{{ URL::asset('assets/js/jquery.min.js') }}"></script>
        <script src="{{ URL::asset('assets/js/jquery-ui.min.js') }}"></script>
        <script src="{{ URL::asset('assets/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ URL::asset('assets/js/metismenu.min.js') }}"></script>
        <script src="{{ URL::asset('assets/js/waves.js') }}"></script>
        <script src="{{ URL::asset('assets/js/feather.min.js') }}"></script>
        <script src="{{ URL::asset('assets/js/jquery.slimscroll.min.js') }}"></script>
        <script src="{{ URL::asset('plugins/apexcharts/apexcharts.min.js') }}"></script>

        <!-- footerScript -->
        @yield('footerScript')

        <!-- App js -->
        <script src="{{ URL::asset('assets/js/app.js') }}"></script>
        <!--Modal Script-->
    <script>

    $('#editModal').on('show.bs.modal', function(event) {


        var button = $(event.relatedTarget)
        var std = button.data('class')
        var subject = button.data('subject')
        var start_time = button.data('start_time')
        var end_time = button.data('end_time')
         var link = button.data('link_url')
        var mon = button.data('mon')
        var tue = button.data('tue')
        var wed = button.data('wed')
        var thu = button.data('thu')
        var fri = button.data('fri')
        var sat = button.data('sat')
        var sun = button.data('sun')
        var id = button.data('id')

        var modal = $(this)
    console.log(std, subject, start_time, end_time, link, mon, tue, wed, thu, fri, sat, sun, id);

    modal.find("#titleLable").text(std + " - " + subject);
    modal.find("#editStartTime").val(start_time);
    modal.find("#editEndTime").val(end_time);
    modal.find("#editCustomCheck1").prop('checked', mon);
    modal.find("#editCustomCheck2").prop('checked', tue);
    modal.find("#editCustomCheck3").prop('checked', wed);
    modal.find("#editCustomCheck4").prop('checked', thu);
    modal.find("#editCustomCheck5").prop('checked', fri);
    modal.find("#editCustomCheck6").prop('checked', sat);
    modal.find("#editCustomCheck7").prop('checked', sun);
    modal.find("#editLink").val(link);
    modal.find("#id").val(id);


});



</script>

    </body>
</html>
