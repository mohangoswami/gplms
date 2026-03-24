@extends('layouts.app') {{-- or layouts.teacher_analytics-master --}}
@section('title','Student Result')

@section('styles')
<style>
/* Page & header */
.report-card {
  max-width: 900px;
  margin: 1rem auto;
  padding: 1rem 1.25rem;
  background: #fff;
  border: 1px solid #ddd;
  box-shadow: 0 1px 2px rgba(0,0,0,.03);
  font-family: "Helvetica Neue", Arial, sans-serif;
  color:#222;
  line-height:1.35;
}
.report-header {
  display:flex;
  justify-content:space-between;
  align-items:center;
  margin-bottom: .5rem;
}
.school-name { font-size: 1.05rem; font-weight:700; }
.small { font-size:0.85rem; color:#555; }

/* Student info block */
.student-meta { display:flex; justify-content:space-between; gap:1rem; margin-bottom: .75rem; }
.meta-left, .meta-right { flex:1; }
.meta-item { margin-bottom: .25rem; }

/* Tables */
.report-table {
  width:100%;
  border-collapse:collapse;
  margin-bottom: .75rem;
  font-size:0.92rem;
}
.report-table th, .report-table td {
  border:1px solid #dcdcdc;
  padding:.45rem .5rem;
  text-align:center;
}
.report-table th { background:#f7f7f7; font-weight:600; font-size:.9rem; }
.col-left { text-align:left; padding-left:.6rem; }

/* Two-term wrapper */
.terms-row { display:flex; gap:1rem; align-items:flex-start; }
.term-col { flex:1; }

/* Co-scholastic & remarks */
.co-scholastic { margin-top:.5rem; }
.remarks { border:1px dashed #ddd; padding:.5rem; min-height:56px; background:#fafafa; }

/* footer & grading scale */
.footer { display:flex; justify-content:space-between; align-items:flex-start; margin-top:1rem; gap:1rem; font-size:.88rem; }
.signatures { text-align:center; min-width:180px; }
.signature-line { margin-top:2.5rem; border-top:1px solid #000; width:85%; margin-left:auto; margin-right:auto; padding-top:.25rem; font-size:.82rem; }

@media (max-width:767px){
  .report-card { padding:.75rem; }
  .terms-row { flex-direction:column; }
  .student-meta { flex-direction:column; }
}

/* Print optimizations */
@media print {
  body * { visibility: hidden; }
  .report-card, .report-card * { visibility: visible; }
  .report-card { position: absolute; left: 0; top:0; width:100%; box-shadow:none; border:none; }
}
</style>
@endsection

@section('content')
<div class="report-card" id="reportCard">
  <div class="report-header">
    <div>
      <div class="school-name">{{ $school['name'] ?? 'SCHOOL NAME' }}</div>
      <div class="small">{{ $school['address'] ?? 'Address, City - State' }}</div>
    </div>
    <div style="text-align:right">
      <div class="small">Academic Year</div>
      <div style="font-weight:700">{{ $session ?? '2024 - 25' }}</div>
      <div class="small">Class: <strong>{{ $student['class'] ?? 'IVth' }}</strong></div>
    </div>
  </div>

  {{-- Student info --}}
  <div class="student-meta">
    <div class="meta-left">
      <div class="meta-item"><strong>Roll No. :</strong> {{ $student['roll_no'] ?? $student['id'] ?? '-' }}</div>
      <div class="meta-item"><strong>Student's Name :</strong> {{ $student['name'] ?? '-' }}</div>
      <div class="meta-item"><strong>Father's Name :</strong> {{ $student['father'] ?? '-' }}</div>
      <div class="meta-item"><strong>Mother's Name :</strong> {{ $student['mother'] ?? '-' }}</div>
    </div>
    <div class="meta-right">
      <div class="meta-item"><strong>Date of Birth :</strong> {{ $student['dob'] ?? '-' }}</div>
      <div class="meta-item"><strong>Admission ID :</strong> {{ $student['admission_number'] ?? '-' }}</div>
      <div class="meta-item"><strong>Attendance :</strong> {{ $attendance_total ?? '-' }} (Present: {{ $attendance_present ?? '-' }})</div>
    </div>
  </div>

  {{-- Scholastic Areas: two term tables side-by-side on wider screens --}}
  <h4 style="margin: .5rem 0;">Scholastic Areas</h4>
  <div class="terms-row">
    <div class="term-col">
      <table class="report-table">
        <thead>
          <tr>
            <th class="col-left">Subject Name</th>
            <th>Per Test (10)</th>
            <th>Note Book (5)</th>
            <th>Sub. Enrich. (5)</th>
            <th>Half Yearly (80)</th>
            <th>Marks Obtained (100)</th>
            <th>Grade</th>
          </tr>
        </thead>
        <tbody>
          {{-- term1 subjects --}}
          @foreach($subjects as $subject)
            @php $t1 = $marks['term1'][$subject['id']] ?? ['per_test'=>'','notebook'=>'','enrich'=>'','exam'=>'','total'=>'','grade'=>'']; @endphp
            <tr>
              <td class="col-left">{{ $subject['name'] }}</td>
              <td>{{ $t1['per_test'] }}</td>
              <td>{{ $t1['notebook'] }}</td>
              <td>{{ $t1['enrich'] }}</td>
              <td>{{ $t1['exam'] }}</td>
              <td>{{ $t1['total'] }}</td>
              <td>{{ $t1['grade'] }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="term-col">
      <table class="report-table">
        <thead>
          <tr>
            <th class="col-left">Subject Name</th>
            <th>Per Test (10)</th>
            <th>Note Book (5)</th>
            <th>Sub. Enrich. (5)</th>
            <th>Annual Exam (80)</th>
            <th>Marks Obtained (100)</th>
            <th>Grade</th>
          </tr>
        </thead>
        <tbody>
          {{-- term2 subjects --}}
          @foreach($subjects as $subject)
            @php $t2 = $marks['term2'][$subject['id']] ?? ['per_test'=>'','notebook'=>'','enrich'=>'','exam'=>'','total'=>'','grade'=>'']; @endphp
            <tr>
              <td class="col-left">{{ $subject['name'] }}</td>
              <td>{{ $t2['per_test'] }}</td>
              <td>{{ $t2['notebook'] }}</td>
              <td>{{ $t2['enrich'] }}</td>
              <td>{{ $t2['exam'] }}</td>
              <td>{{ $t2['total'] }}</td>
              <td>{{ $t2['grade'] }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  {{-- Totals / Aggregate --}}
  <div style="display:flex;justify-content:space-between;gap:1rem;margin-top:.5rem;">
    <div style="flex:1">
      <div><strong>Grand Total ({{ $grand_total_max ?? '-' }}) :</strong> {{ $grand_total ?? '-' }}</div>
      <div><strong>Aggregate % :</strong> {{ number_format($aggregate_percent ?? 0, 2) }}%</div>
      <div style="margin-top:.4rem;"><strong>Promote to Class :</strong> {{ $promote_to ?? '-' }}</div>
    </div>

    <div style="flex:1">
      <div class="small"><strong>Class Teacher's remarks :</strong></div>
      <div class="remarks">{{ $teacher_remarks ?? '—' }}</div>
    </div>
  </div>

  {{-- Co-scholastic --}}
  <div class="co-scholastic" style="margin-top:.75rem;">
    <h5 style="margin-bottom:.4rem;">Co-Scholastic Areas</h5>
    <table class="report-table">
      <thead>
        <tr>
          <th class="col-left">Activity</th>
          <th>Term-1 (A-C)</th>
          <th>Term-2 (A-C)</th>
        </tr>
      </thead>
      <tbody>
        @foreach($co_scholastic as $item)
          <tr>
            <td class="col-left">{{ $item['name'] }}</td>
            <td>{{ $item['term1'] ?? '-' }}</td>
            <td>{{ $item['term2'] ?? '-' }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  {{-- Grading scale & signatures --}}
  <div class="footer">
    <div class="small">
      <strong>Grades are awarded on an 8-point grading scale as follows -</strong>
      <table style="margin-top:.45rem; font-size:.85rem; border-collapse:collapse;">
        <tr><td style="padding:.18rem .4rem;">91 - 100</td><td style="padding:.18rem .4rem;">A1</td></tr>
        <tr><td style="padding:.18rem .4rem;">81 - 90</td><td style="padding:.18rem .4rem;">A2</td></tr>
        <tr><td style="padding:.18rem .4rem;">71 - 80</td><td style="padding:.18rem .4rem;">B1</td></tr>
        <tr><td style="padding:.18rem .4rem;">61 - 70</td><td style="padding:.18rem .4rem;">B2</td></tr>
        <tr><td style="padding:.18rem .4rem;">51 - 60</td><td style="padding:.18rem .4rem;">C1</td></tr>
        <tr><td style="padding:.18rem .4rem;">41 - 50</td><td style="padding:.18rem .4rem;">C2</td></tr>
        <tr><td style="padding:.18rem .4rem;">33 - 40</td><td style="padding:.18rem .4rem;">D</td></tr>
        <tr><td style="padding:.18rem .4rem;">32 & below</td><td style="padding:.18rem .4rem;">E</td></tr>
      </table>
    </div>

    <div class="signatures">
      <div>Place: {{ $school['place'] ?? '—' }}</div>
      <div>Date: {{ $report_date ?? now()->format('d-m-Y') }}</div>
      <div class="signature-line">Class Teacher</div>
      <div class="signature-line">Principal</div>
    </div>
  </div>
</div>

{{-- print button --}}
<div style="text-align:center; margin-top:1rem;">
  <button onclick="window.print()" class="btn btn-primary">Print / Save as PDF</button>
</div>
@endsection
