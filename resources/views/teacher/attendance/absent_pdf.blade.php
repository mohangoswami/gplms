<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Absent Students - {{ $class }} - {{ $date }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h3, h4 { margin: 0 0 10px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 4px 6px; text-align: left; }
        th { background: #f0f0f0; }
    </style>
</head>
<body>
    <h3>Absent Students List</h3>
    <h4>Class: {{ $class }} | Date: {{ \Carbon\Carbon::parse($date)->format('d-M-Y') }}</h4>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Admission / ID</th>
                <th>Student Name</th>
            </tr>
        </thead>
        <tbody>
            @foreach($absentRows as $idx => $row)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td>{{ optional($row->student)->admission_number ?? optional($row->student)->id }}</td>
                    <td>{{ optional($row->student)->name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
