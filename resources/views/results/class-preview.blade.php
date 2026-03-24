@extends('layouts.admin_analytics-master')

@section('content')

<!-- Flash Messages -->
@include('layouts.partials.flash-messages')


<div class="container">

    <h3>Class {{ request()->route('class') }} – Result Preview</h3>

    <table border="1" cellpadding="6" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th>Student ID</th>
            <th>Name</th>
            <th>Grand Total</th>
            <th>Percentage</th>
            <th>Action</th>
        </tr>
        </thead>

        <tbody>
        @foreach($results as $student)
            <tr>
                <td>{{ $student['student']['id'] }}</td>
                <td>{{ $student['student']['name'] }}</td>
                <td>{{ $student['grand_total'] }}</td>
                <td>{{ $student['percentage'] }}%</td>
                <td>
                    <a href="{{ url('admin/results/student/'.$student['student']['id'].'/pdf') }}">
                        View PDF
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

</div>
@endsection
