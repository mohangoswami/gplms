<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">

<style>
@page {
    margin: 20px 25px;
}

body {
    font-family: DejaVu Sans;
    font-size: 11px;
    color: #000;
}

table {
    width: 100%;
    border-collapse: collapse;
}

td, th {
    border: 1px solid #000;
    padding: 3px;
    text-align: center;
}

.no-border td {
    border: none;
}

.left { text-align: left; }
.right { text-align: right; }
.bold { font-weight: bold; }
.small { font-size: 10px; }
</style>

</head>
<body>


    <table class="no-border">
<tr>
    <td class="left">Roll No. : {{ $student['id'] }}</td>
    <td class="left">Student's Name : {{ $student['name'] }}</td>
</tr>
<tr>
    <td class="left">Father's Name : {{ $student['father'] ?? '-' }}</td>
    <td class="left">Mother's Name : {{ $student['mother'] ?? '-' }}</td>
</tr>
<tr>
    <td class="left">Date of Birth : {{ $student['dob'] ?? '-' }}</td>
    <td class="left">Class / Section : {{ $student['class'] }}</td>
</tr>
</table>

<br>


@include('results._header')

@include('results._student-info')

@include('results._scholastic')

@include('results._result-summary')

@include('results._grading')

@include('results._footer')
</body>
</html>
