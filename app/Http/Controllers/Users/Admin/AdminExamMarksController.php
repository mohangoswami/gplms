<?php

namespace App\Http\Controllers\Users\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exam;
use App\Term;
use App\User;
use App\studentExams;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminExamMarksController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Show all exams (admin can see all)
     */
    public function index(Request $request)
        {
            // fetch all exams (with term) — DataTables will paginate client-side
            // If dataset is big, change to ->paginate(...) or server-side DT.
            $exams = Exam::with('term')->orderByDesc('created_at')->get();

            // pass terms for leftbar / filtering if needed
            $terms = Term::orderBy('id')->get();

            return view('admin.examMarks.index', compact('exams', 'terms'));
        }

    /**
     * Show mark entry form for an exam
     */
    public function enterMarks($examId)
    {
        $exam = Exam::with('term')->findOrFail($examId);

        // Admin can access all exams — no class/subject restriction
        $students = User::where('grade', $exam->class)->orderBy('name')->get();

        // Existing marks keyed by studentId
        $existing = studentExams::where('titleId', $exam->id)->get()->keyBy('studentId');

                    $isGradeOnly = is_null($exam->maxMarks) || $exam->maxMarks == 0;

        return view('admin.examMarks.enter', compact('exam', 'students', 'existing', 'isGradeOnly' ));
    }

    /**
     * Save marks entered by admin
     */
    public function saveMarks(Request $request, $examId)
    {
        $exam = Exam::findOrFail($examId);

        $isGradeOnly = is_null($exam->maxMarks) || $exam->maxMarks == 0;

        $rules = [
            'marks'   => 'required|array',
        ];

        if ($isGradeOnly) {
            $rules['marks.*'] = 'nullable|in:A,B,C,D,E';
        } else {
            $rules['marks.*'] = 'nullable|numeric|min:0|max:' . $exam->maxMarks;
        }

        $data = $request->validate($rules);




        $marks   = $data['marks'];
        $remarks = $data['remark'] ?? [];

        DB::transaction(function () use ($marks, $remarks, $exam) {
            $adminId = Auth::guard('admin')->id();

            foreach ($marks as $studentId => $obtained) {
                $student = User::find($studentId);
                if (!$student) {
                    Log::warning('saveMarks: student not found', compact('studentId', 'exam'));
                    continue;
                }
                $values = [
                    'class'         => $student->grade,
                    'name'          => $student->name,
                    'email'         => $student->email,
                    'subject'       => $exam->subject,
                    'title'         => $exam->title,
                    'submittedDone' => 1,
                    'maxMarks'      => $exam->maxMarks,
                    'marksObtain'   => $obtained === '' ? null : (string) $obtained,
                    'teacherId'     => $adminId, // or rename to admin_id if you added that
                    'updated_at'    => now(),
                ];

                if (isset($remarks[$studentId])) {
                    $values['remark'] = $remarks[$studentId];
                }

                studentExams::updateOrCreate(
                    ['titleId' => $exam->id, 'studentId' => $studentId],
                    $values
                );
            }
        });

        return redirect()->route('admin.examMarks.index')
                         ->with('status', 'Marks saved successfully.');
    }
}
