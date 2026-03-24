@extends('layouts.master')

@section('title', 'Metrica - Admin & Dashboard Template')

@section('headerStyle')
    <link href="{{ URL::asset('plugins/fullcalendar/packages/core/main.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('plugins/fullcalendar/packages/daygrid/main.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('plugins/fullcalendar/packages/bootstrap/main.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('plugins/fullcalendar/packages/timegrid/main.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('plugins/fullcalendar/packages/list/main.css') }}" rel="stylesheet" />
@stop

@section('content')
 <div class="container-fluid">
                    <!-- Page-Title -->
                    <div class="row">
                        <div class="col-sm-12">
                            
                             @component('common-components.breadcrumb')
                                 @slot('title') Calendar @endslot
                                 @slot('item1') Metrica @endslot
                                 @slot('item2') Apps @endslot
                              @endcomponent

                        </div><!--end col-->
  
                                      
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-body">
                                    <div id='calendar'></div>
                                    <div style='clear:both'></div>
                                </div><!--end card-body-->
                            </div><!--end card-->
                        </div><!--end col-->
                    </div><!-- End row -->

                </div>
@stop

@section('footerScript')
        <script src="{{ URL::asset('plugins/moment/moment.js') }}"></script>
        <script src="{{ URL::asset('plugins/fullcalendar/packages/core/main.js') }}"></script>
        <script src="{{ URL::asset('plugins/fullcalendar/packages/daygrid/main.js') }}"></script>
        <script src="{{ URL::asset('plugins/fullcalendar/packages/timegrid/main.js') }}"></script>
        <script src="{{ URL::asset('plugins/fullcalendar/packages/interaction/main.js') }}"></script>
        <script src="{{ URL::asset('plugins/fullcalendar/packages/list/main.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    var holidays = <?php echo json_encode($events); ?>;
    console.log(holidays);
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
      plugins: [  'dayGrid' ],
      header: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
      },
      events: holidays,
      overlap: false

    });

    calendar.render();
  });
</script>
@stop