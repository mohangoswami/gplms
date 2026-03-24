<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Bulk Report Cards – Class {{ $grade }}</title>

<style>
body{ font-family: DejaVu Sans, sans-serif; font-size:14px; }
.center{text-align:center;}
.right{text-align:right;}
.left{text-align:left;}
.bold{font-weight:bold; }
table{width:100%;border-collapse:collapse;margin-bottom:8px; font-size:14px;}
th,td{border:1px solid #000;padding:4px;text-align:center; font-size:14px;}
.no-border td{border:none;padding:3px; }
.subject{text-align:left;font-weight:bold;}
.header{font-size:16px;font-weight:bold;}
.subheader{font-size:14px;font-weight:bold;}
.promote{text-align:left; font-weight:bold; }
.remarks{text-align:left; font-weight:bold; margin:15px 0 18px 0;}
.titles td{border:none;padding:3px;text-align:left;padding-left:30px;font-size:14px;}
.grand_total th{ font-size:16px; }
.footer-block{ margin-top:12px; font-size:11px; }
.signature-table td{ border:none; padding-top:25px; text-align:center; vertical-align:bottom; }
.signature-line{ display:inline-block; width:120px; border-top:1px solid #000; margin-top:30px; }
.signature-table td{ border:none; padding:2px 4px; line-height:1.1; vertical-align:bottom; }
.instructions{ text-align:center; font-size:10.5px; margin-top:10px; }
.grade-table{ width:50%; margin:6px auto 0 auto; font-size:10.5px; }
.grade-table td{ padding:2px 4px; }

/* page break between students (visible when printing) */
.student-block { page-break-after: always; padding-bottom: 30px; border-bottom: 2px dashed #ccc; margin-bottom: 30px; }
.student-block:last-child { page-break-after: auto; border-bottom: none; }
</style>
</head>

<body>

@php use App\Services\ResultCalculationService as RC; @endphp

@foreach($studentsData as $data)
@php
    $student           = $data['student'];
    $result            = $data['result'];
    $attendance        = $data['attendance'];
    $health            = $data['health'];
    $coScholasticTerm1 = $data['coScholasticTerm1'];
    $coScholasticTerm2 = $data['coScholasticTerm2'];
@endphp

<div class="student-block">

{{-- STUDENT INFO --}}
<table class="titles">
<tr>
    <td><b>Admission No :</b> {{ $student->admission_number }}</td>
    <td><b>Student Name :</b> {{ $student->name }}</td>
    <td><b>DOB :</b> {{ \Carbon\Carbon::parse($student->dob)->format('d-M-Y') }}</td>
</tr>
<tr>
    <td><b>Class / Section :</b> {{ $student->grade }} {{ $student->section }}</td>
    <td><b>Father's Name :</b> {{ $student->fName }}</td>
    <td><b>Mother's Name :</b> {{ $student->mName }}</td>
    <td colspan="2"></td>
</tr>
</table>

{{-- SCHOLASTIC --}}
<table>
<tr>
    <tr><th colspan="16">Scholastic Areas</th></tr>
    <th rowspan="2">Subject</th>
    <th colspan="6">TERM-I (100)</th>
    <th colspan="6">TERM-II (100)</th>
    <th colspan="3">OVERALL</th>
</tr>
<tr>
    <th>PE</th><th>NB</th><th>SE</th><th>Exam</th><th>Total</th><th>Grade</th>
    <th>PE</th><th>NB</th><th>SE</th><th>Exam</th><th>Total</th><th>Grade</th>
    <th>Total</th><th>Percentage %</th><th>Grade</th>
</tr>

@foreach($result['terms'][1]['subjects'] as $sid => $s1)
@php
    $s2 = $result['terms'][2]['subjects'][$sid] ?? null;
    $overallTotal   = ($s1['obtained'] ?? 0) + ($s2['obtained'] ?? 0);
    $overallMax     = ($s1['max'] ?? 0) + ($s2['max'] ?? 0);
    $overallPercent = $overallMax > 0 ? round(($overallTotal / $overallMax) * 100, 2) : 0;
@endphp
<tr>
    <td class="subject">{{ $s1['subject'] }}</td>
    <td>{{ RC::componentValue($s1['components'], 'PE') }}</td>
    <td>{{ RC::componentValue($s1['components'], 'NOTEBOOK') }}</td>
    <td>{{ RC::componentValue($s1['components'], 'SE') }}</td>
    <td>{{ RC::componentValue($s1['components'], 'HALF YEARLY') }}</td>
    <td>{{ $s1['obtained'] }}</td>
    <td>{{ $s1['grade'] }}</td>
    <td>{{ RC::componentValue($s2['components'] ?? [], 'PE') }}</td>
    <td>{{ RC::componentValue($s2['components'] ?? [], 'NOTEBOOK') }}</td>
    <td>{{ RC::componentValue($s2['components'] ?? [], 'SE') }}</td>
    <td>{{ RC::componentValue($s2['components'] ?? [], 'ANNUAL') }}</td>
    <td>{{ $s2['obtained'] ?? '-' }}</td>
    <td>{{ $s2['grade'] ?? '-' }}</td>
    <td>{{ $overallTotal }}</td>
    <td>{{ $overallPercent }}%</td>
    <td>{{ RC::gradeFromPercentage($overallPercent) }}</td>
</tr>
@endforeach

<tr class="grand_total">
    <th colspan="13" class="right">Grand Total ({{ $result['overall']['grand_max'] }})</th>
    <th>{{ $result['overall']['grand_total'] }}</th>
    <th>{{ $result['overall']['percentage'] }}%</th>
    <th>{{ $result['overall']['grade'] }}</th>
</tr>
</table>

{{-- CO-SCHOLASTIC --}}
<table>
<tr><th colspan="3">Co-Scholastic Areas</th></tr>
<tr>
    <th>Area</th>
    @foreach($terms as $t)<th>{{ $t->name }}</th>@endforeach
</tr>
@foreach($coScholasticAreas as $area)
<tr>
    <td class="subject">{{ $area->area_name }}</td>
    @foreach($terms as $t)
    <td>{{
        optional(
            collect($coScholasticTerm1)->merge($coScholasticTerm2)
                ->where('co_scholastic_area_id', $area->id)
                ->where('term_id', $t->id)
                ->first()
        )->grade ?? '-'
    }}</td>
    @endforeach
</tr>
@endforeach
</table>

{{-- HEALTH --}}
<table>
<tr><th colspan="6">Health &amp; Attendance</th></tr>
<tr>
    <td><b>Height (cm)</b></td><td>{{ $health->height ?? '-' }}</td>
    <td><b>Weight (kg)</b></td><td>{{ $health->weight ?? '-' }}</td>
    <td><b>Attendance Days ({{ $attendance->working_days ?? '-' }})</b></td>
    <td>{{ $attendance->days_present ?? '-' }}</td>
</tr>
</table>

{{-- FOOTER --}}
<table class="no-border">
<tr>
    <td colspan="2">
        <div class="remarks"><b>Class Teacher's Remarks :</b> {{ $health->remark ?? '' }}</div>
    </td>
</tr>
<tr>
    <td colspan="2" class="promote">Result : {{ $result['overall']['result'] }}</td>
</tr>
<tr>
@php
$promotionMap = [
    'NURSERY'=>'L.K.G.','LKG'=>'U.K.G.','UKG'=>'I',
    '1ST'=>'II','2ND'=>'III','3RD'=>'IV','4TH'=>'V',
    '5TH'=>'VI','6TH'=>'VII','7TH'=>'VIII','8TH'=>'IX','9TH'=>'X',
];
@endphp
<td colspan="2" class="promote">
    Promote to Class :
    @if($result['overall']['result'] === 'PASS')
        {{ $promotionMap[strtoupper($student->grade)] ?? '—' }}
    @else
        DETAINED
    @endif
</td>
</tr>
</table>

{{-- SIGNATURES --}}
<div class="footer-block">
<table class="no-border signature-table">
<tr>
    <td style="text-align:left;"><b>Place :</b> Haridwar</td>
    <td><span class="signature-line"></span><br>Class Teacher</td>
    <td><span class="signature-line"></span><br>Principal</td>
</tr>
<tr>
    <td style="text-align:left;"><b>Date :</b> ____________</td>
    <td></td><td></td>
</tr>
</table>

<div class="instructions"><b>Instructions</b><br><br></div>
<div><b>Grading scale for scholastic areas:</b> Grades are awarded on a 8-point grading scale as follows –</div>
<table class="grade-table">
<thead><tr><th>Marks</th><th>Grade</th></tr></thead>
<tr><td>91 – 100</td><td>A 1</td></tr>
<tr><td>81 – 90</td><td>A 2</td></tr>
<tr><td>71 – 80</td><td>B 1</td></tr>
<tr><td>61 – 70</td><td>B 2</td></tr>
<tr><td>51 – 60</td><td>C 1</td></tr>
<tr><td>41 – 50</td><td>C 2</td></tr>
<tr><td>33 – 40</td><td>D</td></tr>
<tr><td>32 &amp; Below</td><td>E (Needs improvement)</td></tr>
</table>
</div>

</div>{{-- /.student-block --}}
@endforeach

</body>
</html>
