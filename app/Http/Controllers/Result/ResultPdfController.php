<?php

namespace App\Http\Controllers\Result;

use App\Http\Controllers\Controller;
use App\ResultCoScholasticArea;
use App\ResultFinalization;
use App\ResultPerforma;
use App\ResultStudentAttendance;
use App\ResultStudentCoScholastic;
use App\ResultStudentHealthRecord;
use App\ResultTerm;
use App\Services\ResultCalculationService;
use App\User;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class ResultPdfController extends Controller
{
    // ===============================
    // ADMIN PDF
    // ===============================
    public function adminPdf(User $student)
    {
        $performa = ResultPerforma::where('class', $student->grade)
            ->where('is_default', 1)
            ->firstOrFail();

        abort_unless(
            ResultFinalization::isFinal($student->id, $performa->id),
            403,
            'Final annual result not declared'
        );

        $terms = ResultTerm::where('performa_id', $performa->id)
            ->orderBy('order_no')
            ->get();

        $calculator = new ResultCalculationService($student->id, $terms);
        $result     = $calculator->calculate();

        return view('results.pdf.preview', compact('student', 'result'));
    }

    // ===============================
    // TEACHER PDF
    // ===============================
    public function teacherPdf(User $student)
    {
        return $this->adminPdf($student);
    }

    // ===============================
    // ANNUAL REPORT CARD PDF (single)
    // ===============================
    public function annualPdf(User $student)
    {
        abort_unless(
            auth('admin')->check() || auth('teacher')->check(),
            403
        );

        $performa = ResultPerforma::where('class', $student->grade)
            ->where('is_default', 1)
            ->firstOrFail();

        abort_unless(
            ResultFinalization::isFinal($student->id, $performa->id),
            403,
            'Annual result not finalized'
        );

        $terms = ResultTerm::where('performa_id', $performa->id)
            ->orderBy('order_no')
            ->get();

        $calculator = new ResultCalculationService($student->id, $terms);
        $result     = $calculator->calculate();

        $coScholasticAreas = ResultCoScholasticArea::where('performa_id', $performa->id)
            ->where('class', $student->grade)
            ->where('is_active', 1)
            ->orderBy('display_order')
            ->get();

        $coScholasticTerm1 = collect();
        $coScholasticTerm2 = collect();

        if ($terms->count() > 0) {
            $coScholasticTerm1 = ResultStudentCoScholastic::where([
                'student_id' => $student->id,
                'term_id'    => $terms[0]->id,
            ])->get()->keyBy('co_scholastic_area_id');
        }

        if ($terms->count() > 1) {
            $coScholasticTerm2 = ResultStudentCoScholastic::where([
                'student_id' => $student->id,
                'term_id'    => $terms[1]->id,
            ])->get()->keyBy('co_scholastic_area_id');
        }

        $attendance = ResultStudentAttendance::where('student_id', $student->id)
            ->orderByDesc('term_id')
            ->first();

        $health = ResultStudentHealthRecord::where('student_id', $student->id)->first();

        return view('results.pdf.report_card', compact(
            'student',
            'result',
            'attendance',
            'health',
            'coScholasticAreas',
            'coScholasticTerm1',
            'coScholasticTerm2',
            'terms'
        ));
    }

    // ================================================
    // UPLOAD SINGLE STUDENT PDF TO S3
    // ================================================
    public function uploadResultPDF(User $student)
    {
        abort_unless(auth('admin')->check(), 403);

        set_time_limit(120);

        try {
            $performa = ResultPerforma::where('class', $student->grade)
                ->where('is_default', 1)
                ->firstOrFail();

            if (!ResultFinalization::isFinal($student->id, $performa->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Result is not finalized yet.',
                ], 422);
            }

            $terms = ResultTerm::where('performa_id', $performa->id)
                ->orderBy('order_no')
                ->get();

            $calculator = new ResultCalculationService($student->id, $terms);
            $result     = $calculator->calculate();

            $coScholasticAreas = ResultCoScholasticArea::where('performa_id', $performa->id)
                ->where('class', $student->grade)
                ->where('is_active', 1)
                ->orderBy('display_order')
                ->get();

            $coScholasticTerm1 = collect();
            $coScholasticTerm2 = collect();

            if ($terms->count() > 0) {
                $coScholasticTerm1 = ResultStudentCoScholastic::where([
                    'student_id' => $student->id,
                    'term_id'    => $terms[0]->id,
                ])->get()->keyBy('co_scholastic_area_id');
            }

            if ($terms->count() > 1) {
                $coScholasticTerm2 = ResultStudentCoScholastic::where([
                    'student_id' => $student->id,
                    'term_id'    => $terms[1]->id,
                ])->get()->keyBy('co_scholastic_area_id');
            }

            $attendance = ResultStudentAttendance::where('student_id', $student->id)
                ->orderByDesc('term_id')
                ->first();

            $health = ResultStudentHealthRecord::where('student_id', $student->id)->first();

            $pdf = Pdf::loadView('results.pdf.report_card', compact(
                'student',
                'result',
                'attendance',
                'health',
                'coScholasticAreas',
                'coScholasticTerm1',
                'coScholasticTerm2',
                'terms'
            ));
            $pdf->setPaper('A4', 'portrait');

            $s3Path = "result-cards/{$student->id}/result_card.pdf";

            Storage::disk('s3')->put($s3Path, $pdf->output());

            $pdfUrl = Storage::disk('s3')->temporaryUrl($s3Path, now()->addHours(2));

            ResultFinalization::where([
                'student_id'  => $student->id,
                'performa_id' => $performa->id,
            ])->update(['pdf_path' => $s3Path]);

            return response()->json([
                'success' => true,
                'message' => 'PDF uploaded successfully.',
                'pdf_url' => $pdfUrl,
            ]);

        } catch (\Throwable $e) {
            \Log::error('PDF Upload Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // ================================================
    // VIEW UPLOADED PDF — redirect to signed S3 URL
    // ================================================
    public function viewResultPDF(User $student)
    {
        abort_unless(auth('admin')->check(), 403);

        $performa = ResultPerforma::where('class', $student->grade)
            ->where('is_default', 1)
            ->firstOrFail();

        $finalization = ResultFinalization::where([
            'student_id'  => $student->id,
            'performa_id' => $performa->id,
        ])->firstOrFail();

        abort_if(empty($finalization->pdf_path), 404, 'PDF not uploaded yet.');

        $url = Storage::disk('s3')->temporaryUrl($finalization->pdf_path, now()->addHours(2));

        return redirect($url);
    }

    // ================================================
    // BULK CLASS PDF — all finalized students in grade
    // ================================================
    public function classBulkPdf($grade)
    {
        abort_unless(auth('admin')->check(), 403);

        // Raise limits for large classes (up to 80 students)
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        // 1. Performa for this grade
        $performa = ResultPerforma::where('class', $grade)
            ->where('is_default', 1)
            ->firstOrFail();

        // 2. Terms (shared across all students in this grade)
        $terms = ResultTerm::where('performa_id', $performa->id)
            ->orderBy('order_no')
            ->get();

        // 3. Co-scholastic master data (same for entire grade)
        $coScholasticAreas = ResultCoScholasticArea::where('performa_id', $performa->id)
            ->where('class', $grade)
            ->where('is_active', 1)
            ->orderBy('display_order')
            ->get();

        // 4. Only students whose result is FINAL (subquery — avoids loading all IDs into PHP)
        $students = User::where('grade', $grade)
            ->whereIn('id', function ($q) use ($performa) {
                $q->select('student_id')
                  ->from('result_finalizations')
                  ->where('performa_id', $performa->id)
                  ->where('status', 'FINAL');
            })
            ->orderBy('name')
            ->get();

        if ($students->isEmpty()) {
            abort(404, 'No finalized students found for Class ' . $grade);
        }

        // 5. Build data array for all students
        $studentsData = [];

        foreach ($students as $student) {
            $calculator = new ResultCalculationService($student->id, $terms);
            $result     = $calculator->calculate();

            $coScholasticTerm1 = collect();
            $coScholasticTerm2 = collect();

            if ($terms->count() > 0) {
                $coScholasticTerm1 = ResultStudentCoScholastic::where([
                    'student_id' => $student->id,
                    'term_id'    => $terms[0]->id,
                ])->get()->keyBy('co_scholastic_area_id');
            }

            if ($terms->count() > 1) {
                $coScholasticTerm2 = ResultStudentCoScholastic::where([
                    'student_id' => $student->id,
                    'term_id'    => $terms[1]->id,
                ])->get()->keyBy('co_scholastic_area_id');
            }

            $attendance = ResultStudentAttendance::where('student_id', $student->id)
                ->orderByDesc('term_id')
                ->first();

            $health = ResultStudentHealthRecord::where('student_id', $student->id)->first();

            $studentsData[] = compact(
                'student', 'result', 'attendance', 'health',
                'coScholasticTerm1', 'coScholasticTerm2'
            );
        }

        // 6. Return browser view (same style as annualPdf)
        return view('results.pdf.bulk_preview', compact(
            'studentsData', 'coScholasticAreas', 'terms', 'grade'
        ));
    }
}
