<?php

namespace App\Http\Controllers\Result;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Exam;
use App\User;
use PDF;
use DB;
use App\ResultPerforma;
use App\ResultPerformaItem;
use App\subCode;
Use App\studentExams;

// MODELS
use App\StudentExamEntry;
use App\ResultStudentAttendance;
use App\ResultStudentCoScholastic;
use App\ResultStudentHealthRecord;
use App\ResultFinalization;
use App\ResultTerm;

class ResultController extends Controller
{

private function buildClassResults($class)
{
    /* ==========================
     * 1️⃣ Load default performa
     * ========================== */
    $performa = ResultPerforma::where('class', $class)
        ->where('is_default', 1)
        ->firstOrFail();

    /* ==========================
     * 2️⃣ Load performa items
     * ========================== */
    $items = ResultPerformaItem::with('subCode')
        ->where('performa_id', $performa->id)
        ->where('is_included', 1)
        ->orderBy('subject_order')
        ->orderBy('component_order')
        ->get();

    /* ==========================
     * 3️⃣ Load student exams WITH exam
     * ========================== */
    $examRows = studentExams::with('exam')
        ->where('class', $class)
        ->get();

        /* ==========================
     * 4️⃣ Term code → term name map
     * ========================== */
    $termMap = [
        'P1' => 'Periodic I',
        'HY' => 'Half Yearly',
        'P2' => 'Periodic II',
        'AN' => 'Annual',
    ];

    $results = [];

    /* ==========================
     * 5️⃣ Prepare student shell
     * ========================== */
    foreach ($examRows as $row) {
        $sid = $row->studentId;

        if (!isset($results[$sid])) {
            $results[$sid] = [
                'student' => [
                    'id'    => $sid,
                    'name'  => $row->name,
                    'class' => $row->class,
                ],
                'terms' => [],
                'grand_total' => 0,
                'max_total'   => 0,
            ];
        }
    }

    /* ==========================
     * 6️⃣ Fill performa structure
     * ========================== */
    foreach ($items as $item) {

        $termCode = $item->term;                 // P1 / HY / P2 / AN
        $termName = $termMap[$termCode];
        $subject  = $item->subCode->subject;
        $component = $item->component;           // PT / Notebook / Written

        foreach ($results as $sid => &$student) {

            if (!isset($student['terms'][$termCode][$subject])) {
                $student['terms'][$termCode][$subject] = [
                    '__mode' => $item->evaluation_type,
                ];
            }

            /* ==========================
             * Find matching exam row
             * ========================== */
            $row = $examRows->first(function ($r) use ($sid, $subject, $item, $termName) {

                if (!$r->exam || !$r->exam->term) {
                    return false;
                }

                return
                    $r->studentId == $sid
                    && trim($r->subject) === trim($subject)
                    && (float) $r->maxMarks === (float) $item->max_marks
                    && trim($r->exam->term->term) === trim($termName);
            });


            if ($item->evaluation_type === 'GRADE') {

                $student['terms'][$termCode][$subject]['grade']
                    = $row->marksObtain ?? '-';

            } else {

                $marks = ($row && is_numeric($row->marksObtain))
                    ? (float)$row->marksObtain
                    : null;

                $student['terms'][$termCode][$subject][$component] = [
                    'marks' => $marks,
                    'max'   => $item->max_marks,
                ];
            }
        }
    }

    /* ==========================
     * 7️⃣ Totals & Grades
     * ========================== */
    foreach ($results as &$student) {

        foreach ($student['terms'] as &$subjects) {

            foreach ($subjects as &$data) {

                if (($data['__mode'] ?? '') === 'MARKS') {

                    $total = 0;
                    $max   = 0;

                    foreach ($data as $val) {
                        if (is_array($val)) {
                            $total += $val['marks'] ?? 0;
                            $max   += $val['max'] ?? 0;
                        }
                    }

                    $data['total'] = $total;
                    $data['grade'] = $this->gradeFromMarks($total);

                    $student['grand_total'] += $total;
                    $student['max_total']   += $max;
                }
            }
        }

        $student['percentage'] = $student['max_total'] > 0
            ? round(($student['grand_total'] / $student['max_total']) * 100, 2)
            : 0;
    }
    // dd($results, $class, $performa, $items, $examRows, $termMap, $results, $student );
    return $results;
}


private function gradeFromMarks($marks)
{
    return match (true) {
        $marks >= 91 => 'A1',
        $marks >= 81 => 'A2',
        $marks >= 71 => 'B1',
        $marks >= 61 => 'B2',
        $marks >= 51 => 'C1',
        $marks >= 41 => 'C2',
        $marks >= 33 => 'D',
        default      => 'E',
    };
}


    public function classResult($class)
    {
        $results = $this->buildClassResults($class);


        return view('results.class-preview', compact('results'));
    }

public function studentPdf($studentId)
{
    $results = $this->buildStudentResult($studentId);
    $student = User::findOrFail($studentId);
    // dd($results);
    return PDF::loadView(
        'results.student-pdf',
        compact('results', 'student')
    )->download('result.pdf');
}




private function buildStudentResult($studentId)
{
    $row = studentExams::where('studentId', $studentId)->firstOrFail();

    $classResults = $this->buildClassResults($row->class);

    if (!isset($classResults[$studentId])) {
        abort(404);
    }

    $raw = $classResults[$studentId];

    /* Pivot term → subject */
    $subjects = [];

    foreach ($raw['terms'] as $term => $termSubjects) {
        foreach ($termSubjects as $subject => $data) {
            $subjects[$subject]['__mode'] = $data['__mode'] ?? 'MARKS';
            $subjects[$subject][$term] = $data;
        }
    }

    return [
        'student'      => $raw['student'],
        'subjects'     => $subjects,
        'grand_total'  => $raw['grand_total'],
        'max_total'    => $raw['max_total'],
        'percentage'   => $raw['percentage'],
        'nextClass'    => $this->nextClass($raw['student']['class']),
    ];
}



private function nextClass($class)
{
    $map = [
        'NURSERY' => 'LKG',
        'LKG' => 'UKG',
        'UKG' => 'IST',
        '1ST' => '2ND',
        '2ND' => '3RD',
        '3RD' => '4TH',
        '4TH' => '5TH',
        '5TH' => '6TH',
        '6TH'  => '7TH',
        '7TH' => '8TH',
        '8TH'=> '9TH',
        '9TH'  => '10TH',
        '10TH'   => '11TH',
        '11TH'  => '12TH',
    ];

    return $map[$class] ?? '';
}


public function deleteFullResult(User $student)
{
    abort_unless(auth('admin')->check(), 403);

    DB::beginTransaction();

    try {

        /* ==========================
         * 🔒 BLOCK IF FINALIZED
         * ========================== */
        $isFinal = ResultFinalization::where([
            'student_id' => $student->id,
            'status'     => 'FINAL',
        ])->exists();

        if ($isFinal) {
            return back()->with(
                'error',
                'Result is finalized. Please reopen before deleting.'
            );
        }

        /* ==========================
         * 🗑 DELETE SCHOLASTIC (ALL TERMS)
         * ========================== */
        StudentExamEntry::where('student_id', $student->id)->delete();

        /* ==========================
         * 🗑 DELETE CO-SCHOLASTIC (ALL TERMS)
         * ========================== */
        ResultStudentCoScholastic::where('student_id', $student->id)->delete();

        /* ==========================
         * 🗑 DELETE ATTENDANCE
         * ========================== */
        ResultStudentAttendance::where('student_id', $student->id)->delete();

        /* ==========================
         * 🗑 DELETE HEALTH RECORD
         * ========================== */
        ResultStudentHealthRecord::where('student_id', $student->id)->delete();

        /* ==========================
         * 🗑 DELETE FINALIZATION RECORD
         * ========================== */
        ResultFinalization::where('student_id', $student->id)->delete();

        DB::commit();

        return back()->with(
            'status',
            '✅ Full annual result deleted successfully.'
        );

    } catch (\Throwable $e) {

        DB::rollBack();

        \Log::error('FULL RESULT DELETE FAILED', [
            'student_id' => $student->id,
            'error'      => $e->getMessage(),
        ]);

        return back()->with(
            'error',
            'Delete failed: ' . $e->getMessage()
        );
    }
}

//   /**
//      * ❌ DELETE COMPLETE RESULT (TERM WISE)
//      */
//     public function deleteTermResult(User $student, ResultTerm $term)
//     {
//         abort_unless(auth('admin')->check(), 403);

//         DB::beginTransaction();

//         try {

//             /* ==========================
//              * 🔒 BLOCK IF FINALIZED
//              * ========================== */
//             $isFinal = ResultFinalization::where([
//                 'student_id' => $student->id,
//                 'status'     => 'FINAL',
//             ])->exists();

//             if ($isFinal) {
//                 return back()->with(
//                     'error',
//                     'Result is finalized. Reopen before deleting.'
//                 );
//             }

//             /* ==========================
//              * 🗑 DELETE MARKS
//              * ========================== */
//             StudentExamEntry::where([
//                 'student_id' => $student->id,
//                 'term_id'    => $term->id,
//             ])->delete();

//             /* ==========================
//              * 🗑 DELETE CO-SCHOLASTIC
//              * ========================== */
//             ResultStudentCoScholastic::where([
//                 'student_id' => $student->id,
//                 'term_id'    => $term->id,
//             ])->delete();

//             /* ==========================
//              * 🗑 DELETE ATTENDANCE
//              * ========================== */
//             ResultStudentAttendance::where([
//                 'student_id' => $student->id,
//                 'term_id'    => $term->id,
//             ])->delete();

//             /* ==========================
//              * 🗑 DELETE HEALTH (term based)
//              * ========================== */
//             ResultStudentHealthRecord::where([
//                 'student_id' => $student->id,
//                 'term_id'    => $term->id,
//             ])->delete();

//             DB::commit();

//             return back()->with(
//                 'status',
//                 'Result deleted successfully for ' . $term->name
//             );

//         } catch (\Throwable $e) {

//             DB::rollBack();

//             \Log::error('Result Delete Failed', [
//                 'student_id' => $student->id,
//                 'term_id'    => $term->id,
//                 'error'      => $e->getMessage(),
//             ]);

//             return back()->with(
//                 'error',
//                 'Delete failed: ' . $e->getMessage()
//             );
//         }
//     }

}
