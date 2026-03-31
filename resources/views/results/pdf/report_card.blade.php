<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>REPORT CARD</title>

<style>
body{ font-family: DejaVu Sans, sans-serif; font-size:14px; text-transform:uppercase; }
.center{text-align:center;}
.right{text-align:right;}
.left{text-align:left;}
.bold{font-weight:bold;}
table{width:100%;border-collapse:collapse;margin-bottom:8px;font-size:14px;}
th,td{border:1px solid #000;padding:4px;text-align:center;font-size:14px;}
.no-border td{border:none;padding:3px;}
.subject{text-align:left;font-weight:bold;}
.header{font-size:16px;font-weight:bold;}
.subheader{font-size:14px;font-weight:bold;}
.promote{text-align:left;font-weight:bold;}
.remarks{text-align:left;font-weight:bold;margin:15px 0 18px 0;}
.titles td{border:none;padding:3px;text-align:left;padding-left:30px;font-size:14px;}
.grand_total th{font-size:16px;}
.footer-block{margin-top:12px;font-size:11px;}
.signature-table td{border:none;padding-top:25px;text-align:center;vertical-align:bottom;}
.signature-line{display:inline-block;width:120px;border-top:1px solid #000;margin-top:30px;}
.signature-table td{border:none;padding:2px 4px;line-height:1.1;vertical-align:bottom;}
.instructions{text-align:center;font-size:10.5px;margin-top:10px;}
.grade-table{width:50%;margin:6px auto 0 auto;font-size:10.5px;}
.grade-table td{padding:2px 4px;}
</style>
</head>

<body>

@php use App\Services\ResultCalculationService as RC; @endphp

{{-- ================= STUDENT INFO ================= --}}
<table class="titles">
<tr>
    <td><b>ADM. NO :</b> {{ $student->admission_number }}</td>
    <td><b>STUDENT NAME :</b> {{ strtoupper($student->name) }}</td>
    <td><b>DOB :</b> {{ \Carbon\Carbon::parse($student->dob)->format('d-M-Y') }}</td>
</tr>
<tr>
    <td><b>CLASS :</b> {{ strtoupper($student->grade) }}</td>
    <td><b>FATHER'S NAME :</b> {{ strtoupper($student->fName) }}</td>
    <td><b>MOTHER'S NAME :</b> {{ strtoupper($student->mName) }}</td>
    <td colspan="2"></td>
</tr>
</table>

{{-- ================= SCHOLASTIC ================= --}}
@php
    $term1Id       = optional($terms->get(0))->id;
    $term2Id       = optional($terms->get(1))->id;
    $term1Subjects = $term1Id ? ($result['terms'][$term1Id]['subjects'] ?? []) : [];
    $term2Subjects = $term2Id ? ($result['terms'][$term2Id]['subjects'] ?? []) : [];

    // ── DYNAMIC MAX MARKS FOR HEADER (computed BEFORE merge so values stay per-subject) ──
    $peMax1 = '-'; $nbMax1 = '-'; $seMax1 = '-'; $examMax1 = '-'; $totalMax1 = '-';
    $ffm1 = reset($term1Subjects);
    if ($ffm1) {
        $totalMax1 = $ffm1['max'] ?? '-';
        foreach ($ffm1['components'] as $c) {
            if ($c['component_name'] === 'PE')               $peMax1   = (int)$c['max'];
            elseif ($c['component_name'] === 'NOTEBOOK')     $nbMax1   = (int)$c['max'];
            elseif ($c['component_name'] === 'SE')           $seMax1   = (int)$c['max'];
            elseif ($c['component_name'] === 'HALF YEARLY')  $examMax1 = (int)$c['max'];
        }
    }
    $peMax2 = '-'; $nbMax2 = '-'; $seMax2 = '-'; $examMax2 = '-'; $totalMax2 = '-';
    $ffm2 = reset($term2Subjects);
    if ($ffm2) {
        $totalMax2 = $ffm2['max'] ?? '-';
        foreach ($ffm2['components'] as $c) {
            if ($c['component_name'] === 'PE')           $peMax2   = (int)$c['max'];
            elseif ($c['component_name'] === 'NOTEBOOK') $nbMax2   = (int)$c['max'];
            elseif ($c['component_name'] === 'SE')       $seMax2   = (int)$c['max'];
            elseif ($c['component_name'] === 'ANNUAL')   $examMax2 = (int)$c['max'];
        }
    }

    // ── GRAND TOTAL (service already merged GK/MS; compute from final subjects) ──
    $grandObtained = 0;
    $grandMax = 0;
    foreach ($term1Subjects as $sid => $s1) {
        $s2 = $term2Subjects[$sid] ?? null;
        $grandObtained += ($s1['obtained'] ?? 0) + ($s2['obtained'] ?? 0);
        $grandMax += ($s1['max'] ?? 0) + ($s2['max'] ?? 0);
    }
    $grandPercent = $grandMax > 0 ? round($grandObtained / $grandMax * 100, 2) : 0;
    $grandGrade = RC::gradeFromPercentage($grandPercent);
    $overallSubjectMax = (is_numeric($totalMax1) && is_numeric($totalMax2))
        ? $totalMax1 + $totalMax2 : '-';
@endphp

<table>
<tr><th colspan="16">SCHOLASTIC AREAS</th></tr>
<tr>
    <th rowspan="2">SUBJECT</th>
    <th colspan="6">TERM-I ({{ $totalMax1 }})</th>
    <th colspan="6">TERM-II ({{ $totalMax2 }})</th>
    <th colspan="3">OVERALL</th>
</tr>
<tr>
    <th>PE ({{ $peMax1 }})</th><th>NB ({{ $nbMax1 }})</th><th>SE ({{ $seMax1 }})</th>
    <th>EXAM ({{ $examMax1 }})</th><th>TOTAL ({{ $totalMax1 }})</th><th>GRADE</th>
    <th>PE ({{ $peMax2 }})</th><th>NB ({{ $nbMax2 }})</th><th>SE ({{ $seMax2 }})</th>
    <th>EXAM ({{ $examMax2 }})</th><th>TOTAL ({{ $totalMax2 }})</th><th>GRADE</th>
    <th>TOTAL ({{ $overallSubjectMax }})</th><th>PERCENTAGE (%)</th><th>GRADE</th>
</tr>

@foreach($term1Subjects as $sid => $s1)
@php
    $s2 = $term2Subjects[$sid] ?? null;
    $overallTotal   = ($s1['obtained'] ?? 0) + ($s2['obtained'] ?? 0);
    $overallMax     = ($s1['max'] ?? 0)      + ($s2['max'] ?? 0);
    $overallPercent = $overallMax > 0 ? round(($overallTotal / $overallMax) * 100, 2) : 0;
@endphp
<tr>
    <td class="subject">{{ strtoupper($s1['subject']) }}</td>
    {{-- TERM I --}}
    <td>{{ RC::componentValue($s1['components'], 'PE') }}</td>
    <td>{{ RC::componentValue($s1['components'], 'NOTEBOOK') }}</td>
    <td>{{ RC::componentValue($s1['components'], 'SE') }}</td>
    <td>{{ RC::componentValue($s1['components'], 'HALF YEARLY') }}</td>
    <td>{{ $s1['obtained'] }}</td>
    <td>{{ $s1['grade'] }}</td>
    {{-- TERM II --}}
    <td>{{ RC::componentValue($s2['components'] ?? [], 'PE') }}</td>
    <td>{{ RC::componentValue($s2['components'] ?? [], 'NOTEBOOK') }}</td>
    <td>{{ RC::componentValue($s2['components'] ?? [], 'SE') }}</td>
    <td>{{ RC::componentValue($s2['components'] ?? [], 'ANNUAL') }}</td>
    <td>{{ $s2['obtained'] ?? '-' }}</td>
    <td>{{ $s2['grade'] ?? '-' }}</td>
    {{-- OVERALL --}}
    <td>{{ $overallTotal }}</td>
    <td>{{ $overallPercent }}%</td>
    <td>{{ RC::gradeFromPercentage($overallPercent) }}</td>
</tr>
@endforeach

<tr class="grand_total">
    <th colspan="13" class="right">GRAND TOTAL ({{ $grandMax }})</th>
    <th>{{ $grandObtained }}</th>
    <th>{{ $grandPercent }}%</th>
    <th>{{ $grandGrade }}</th>
</tr>
</table>

{{-- ================= CO-SCHOLASTIC ================= --}}
<table>
<tr><th colspan="3">CO-SCHOLASTIC AREAS</th></tr>
<tr>
    <th>AREA</th>
    @foreach($terms as $t)
        <th>{{ strtoupper($t->name) }}</th>
    @endforeach
</tr>
@foreach($coScholasticAreas as $area)
<tr>
    <td class="subject">{{ strtoupper($area->area_name) }}</td>
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

{{-- ================= HEALTH ================= --}}
<table>
<tr><th colspan="6">HEALTH &amp; ATTENDANCE</th></tr>
<tr>
    <td><b>HEIGHT (CM)</b></td>
    <td>{{ $health->height ?? '-' }}</td>
    <td><b>WEIGHT (KG)</b></td>
    <td>{{ $health->weight ?? '-' }}</td>
    <td><b>ATTENDANCE DAYS ({{ $attendance->working_days ?? '-' }})</b></td>
    <td>{{ $attendance->days_present ?? '-' }}</td>
</tr>
</table>

{{-- ================= FOOTER ================= --}}
<table class="no-border">
<tr>
    <td colspan="2">
        <div class="remarks">
            <b>CLASS TEACHER'S REMARKS :</b> {{ strtoupper($health->remark ?? '') }}
        </div>
    </td>
</tr>
<tr>
    <td colspan="2" class="promote">RESULT : {{ $result['overall']['result'] }}</td>
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
    PROMOTE TO CLASS :
    @if($result['overall']['result'] === 'PASS')
        {{ $promotionMap[strtoupper($student->grade)] ?? '—' }}
    @else
        DETAINED
    @endif
</td>
</tr>
</table>

{{-- ================= SIGNATURES ================= --}}
<div class="footer-block">
<table class="no-border signature-table">
<tr>
    <td style="text-align:left;"><b>PLACE :</b> HARIDWAR</td>
    <td><span class="signature-line"></span><br>CLASS TEACHER</td>
    <td><span class="signature-line"></span><br>PRINCIPAL</td>
</tr>
<tr>
    <td style="text-align:left;"><b>DATE :</b> 25-MARCH-2026</td>
    <td></td><td></td>
</tr>
</table>

<div class="instructions"><b>INSTRUCTIONS</b><br><br></div>
<div><b>GRADING SCALE FOR SCHOLASTIC AREAS:</b> GRADES ARE AWARDED ON A 8-POINT GRADING SCALE AS FOLLOWS –</div>
<table class="grade-table">
<thead><tr><th>MARKS</th><th>GRADE</th></tr></thead>
<tr><td>91 – 100</td><td>A 1</td></tr>
<tr><td>81 – 90</td><td>A 2</td></tr>
<tr><td>71 – 80</td><td>B 1</td></tr>
<tr><td>61 – 70</td><td>B 2</td></tr>
<tr><td>51 – 60</td><td>C 1</td></tr>
<tr><td>41 – 50</td><td>C 2</td></tr>
<tr><td>33 – 40</td><td>D</td></tr>
<tr><td>32 &amp; BELOW</td><td>E (NEEDS IMPROVEMENT)</td></tr>
</table>
<div style="font-size:10.5px;margin-top:6px;"><b>ABBREVIATIONS:</b>&nbsp;
PE = PERIODIC EVALUATION &nbsp;|&nbsp; NB = NOTEBOOK &nbsp;|&nbsp; SE = SUBJECT ENRICHMENT</div>
</div>

</body>
</html>
