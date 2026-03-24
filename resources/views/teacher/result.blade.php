@extends('layouts.teacher_analytics-master')

@section('headerStyle')
<link href="{{ URL::asset('plugins/footable/css/footable.bootstrap.css')}}" rel="stylesheet" type="text/css">

<!-- DataTables -->
<link href="{{ URL::asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Responsive datatable examples -->
<link href="{{ URL::asset('plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">

@stop

@section('content')

<!-- Flash Messages -->
@include('layouts.partials.flash-messages')

<div class="row m-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                @foreach ($exams as $exam)
                <h4 class="mt-0 header-title">Exam - {{$exam->class}} - {{$exam->subject}}</h4>
                <p class="text-muted mb-3">{{$exam->title}}
                </p>

                <div>
                    <form method="POST" action="{{ route('teacher.topperSwitch') }}" enctype="multipart/form-data">
                        @csrf
                    <div>
                    <input type="hidden" name="id" value="{{$exam->id}}">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="topperShown" value=1 id="tooperShown" @if($exam->topperShown == 1) checked @endif>
                        <label class="form-check-label" for="flexRadioDefault1">
                        Topper Shown
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="topperShown" value=0 id="tooperShown" @if($exam->topperShown == 0) checked @endif>
                        <label class="form-check-label" for="flexRadioDefault2">
                            Topper Not Shown
                        </label>
                    </div>
                </div>
                <div>
                    <button type="submit" class="btn btn-sm b-round btn-gradient-primary">save</button>
                </div>
            </div>
        </form>

                @endforeach
                <div class="table-responsive">
                    <table id="datatable" class="table">
                        <thead class="thead-light">
                        <tr>
                            <th>S.No.</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Max Marks</th>
                            <th>Marks Obtain</th>


                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $i=1;
                        @endphp
                        @foreach ($results as $result)

                        <tr>
                            <td>{{$i}}</td>
                            <td>{{$result->name}}</td>
                            <td>{{$result->email}}</td>
                            <td>{{$result->maxMarks}}</td>
                            <td>{{$result->marksObtain}}</td>


                            <td>
                            <a href="/teacher/editStudentResult/{{$result->id}}"><i class="fas fa-edit text-info font-16"></i></a>/
                            <button type="button" class="btn btn-info waves-effect waves-light" data-toggle="modal" data-animation="bounce" data-target=".bs-example-modal-sm">Edit</button>

                            </td>
                        </tr>
                        @php
                            $i=$i+1;
                        @endphp
                        @endforeach

                        </tbody>
                    </table><!--end /table-->
                </div><!--end /tableresponsive-->
            </div><!--end card-body-->
        </div><!--end card-->
    </div> <!-- end col -->
</div>

@endsection


@section('footerScript')


<script src="{{ URL::asset('plugins/footable/js/footable.js')}}"></script>
        <script src="{{ URL::asset('plugins/moment/moment.js')}}"></script>
        <script src="{{ URL::asset('assets/pages/jquery.footable.init.js')}}"></script>
         <!-- Required datatable js -->
         <script src="{{ URL::asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
         <script src="{{ URL::asset('plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
         <script src="{{ URL::asset('plugins/peity-chart/jquery.peity.min.js') }}"></script>
         <script src="{{ URL::asset('assets/pages/jquery.analytics_customers.init.js') }}"></script>
@stop
