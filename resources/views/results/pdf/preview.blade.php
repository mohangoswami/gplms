@extends('layouts.admin_analytics-master')

@section('content')
<div class="container mt-4">

<h4>Result Preview (Finalized)</h4>

<p><strong>Student:</strong> {{ $student->name }}</p>

<pre>{{ print_r($result, true) }}</pre>

</div>
@endsection
