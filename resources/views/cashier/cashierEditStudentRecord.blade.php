@extends('layouts.cashier-master')

@section('headerStyle')
<link href="{{ URL::asset('plugins/footable/css/footable.bootstrap.css')}}" rel="stylesheet" type="text/css">

<!-- DataTables -->
<link href="{{ URL::asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<!-- Responsive datatable examples -->
<link href="{{ URL::asset('plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('content')

<!-- Flash Messages -->
@include('layouts.partials.flash-messages')

<div class="row">
        <div class="col-lg-6">
            <div class="card m-5">
                <div class="card-body">
                    <h2>Edit Student Record</h2>
                    <form action="{{ route('cashierEditStudentRecord') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" value="{{ $id }}">

                        <!-- Admission Number -->
                        <div class="mb-3">
                            <label>Edit Admission No.</label>
                            <input class="form-control" type="text" id="admission_number" name="admission_number" value="{{ $user->admission_number }}" required>
                        </div>

                        <!-- Name -->
                        <div class="mb-3">
                            <label>Edit Name</label>
                            <input class="form-control" type="text" id="editName" name="editName" value="{{ $user->name }}" required>
                        </div>

                        <!-- Class -->
                        <div class="mb-3">
                            <label>Select Class</label>
                            @isset($grades)
                                <select id="editClass" name="editClass" class="form-control select2" required>
                                    <option value="{{ $user->grade }}">{{ $user->grade }}</option>
                                    @foreach ($grades as $class)
                                        <option value="{{ $class }}">{{ $class }}</option>
                                    @endforeach
                                </select>
                            @endisset
                        </div>

                        <!-- Parent Details -->
                        <div class="mb-3">
                            <label>Edit Father Name</label>
                            <input class="form-control" type="text" id="fName" name="fName" value="{{ $user->fName }}" required>
                        </div>
                        <div class="mb-3">
                            <label>Edit Mother Name</label>
                            <input class="form-control" type="text" id="mName" name="mName" value="{{ $user->mName }}" required>
                        </div>
                        <div class="mb-3">
                            <label>Edit Old Balance</label>
                            <input class="form-control" type="number" id="oldBalance" name="oldBalance" value="{{ $user->oldBalance ?? 0 }}" required>
                        </div>
                        <!-- Date of Birth -->
                        <div class="mb-3">
                            <label>Edit Date of Birth</label>
                            <input class="form-control" type="date" id="dob" name="dob" value="{{ old('dob', $user->dob ? $user->dob->format('Y-m-d') : '') }}">
                        </div>

                        <!-- Category -->
                        <div class="mb-3">
                            <label>Category</label>
                            <select id="category" name="category" class="form-control select2" required>
                                <option value="{{ $user->category->category }}">{{ $user->category->category }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->category }}">{{ $category->category }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Route -->
                        <div class="mb-3">
                            <label>Select Route</label>
                            <select id="route" name="route" class="form-control select2">
                                <option value="{{ $user->route->routeName ?? "NA"}}">{{ $user->route->routeName ?? "NA"}}</option>
                                @foreach ($routes as $route)
                                    <option value="{{ $route->routeName }}">{{ $route->routeName }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Address and Contact -->
                        <div class="mb-3">
                            <label>Edit Address</label>
                            <input class="form-control" type="text" id="address" name="address" value="{{ $user->address }}" required>
                        </div>
                        <div class="mb-3">
                            <label>Edit Mobile Number</label>
                            <input class="form-control" type="number" id="mobile" name="mobile" value="{{ $user->mobile }}" required>
                        </div>

                        <!-- RFID -->
                        <div class="mb-3">
                            <label>Edit RFID</label>
                            <input class="form-control" type="text" id="rfid" name="rfid" value="{{ $user->rfid }}">
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label>Edit Email</label>
                            <input class="form-control" type="text" id="editEmail" name="editEmail" value="{{ $user->email }}" required>
                        </div>

                        <!-- Permissions -->
                        <div class="mb-3">
                            <label>Edit App Permission</label>
                            <select id="editAppPermission" name="editAppPermission" class="form-control select2" required>
                                <option value="1" {{ $user->app_permission == 1 ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ $user->app_permission == 0 ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Edit Exam Permission</label>
                            <select id="editExamPermission" name="editExamPermission" class="form-control select2" required>
                                <option value="1" {{ $user->exam_permission == 1 ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ $user->exam_permission == 0 ? 'selected' : '' }}>No</option>
                            </select>
                        </div>

                        <!-- Buttons -->
                        <div class="mt-4">
                            <button type="submit" class="btn btn-gradient-primary">Save Changes</button>
                            <button type="button" onclick="window.location='/admin/create_liveClass'" class="btn btn-gradient-danger">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footerScript')
<script src="{{ URL::asset('plugins/footable/js/footable.js') }}"></script>
<script src="{{ URL::asset('plugins/moment/moment.js') }}"></script>
<script src="{{ URL::asset('assets/pages/jquery.footable.init.js') }}"></script>
@stop
