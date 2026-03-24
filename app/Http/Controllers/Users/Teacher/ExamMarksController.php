<?php
namespace App\Http\Controllers\Users\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\subCode;
use App\classwork;
use App\Exam;
use App\Term;
use Auth;
use Illuminate\Support\Facades\Validator;
use DB;
use Illuminate\Support\Facades\Storage;
use App\Notifications\emailNotification;
use App\User;
use App\stuHomeworkUpload;
use App\studentExams;
use Illuminate\Support\Facades\Log;

class ExamMarksController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:teacher');
    }

    /**
     * Show exams list (optionally filtered by term)
     *
     * URL:
     *  - /examMarks              -> all exams for this teacher
     *  - /examMarks/{termId}     -> exams for the given term only
     */
    public function index(Request $request, $termId = null)
    {
        // get teacher and their assigned subCodes (collection of sub_code models)
        $teacher = Auth::guard('teacher')->user();
        $subCodes = $teacher ? $teacher->subCodes() : collect();

        // if subCodes is empty, return empty collection quickly
        if ($subCodes->isEmpty()) {
            $exams = collect();
        } else {
            // get unique class names and subjects the teacher handles
            $classes  = $subCodes->pluck('class')->unique()->values()->all();
            $subjects = $subCodes->pluck('subject')->unique()->values()->all();

            // base query: exams that match any of teacher's classes AND any of teacher's subjects
            $query = Exam::with('term')->whereIn('class', $classes)->whereIn('subject', $subjects);

            // optional: filter by term if route provided termId
            if (!empty($termId)) {
                $query->where('term_id', $termId);
            }

            $exams = $query->orderByDesc('created_at')->get();
        }

        // Terms for leftbar / selection
        $terms = Term::orderBy('id')->get();

        return view('teacher.examMarks.index', compact('exams', 'terms', 'termId'));
    }

    /**
     * Older enterMarks route — keep for backward compatibility
     * Shows marking screen listing students & existing marks for an exam
     */
    public function enterMarks($examId)
        {
            $exam = Exam::with('term')->findOrFail($examId);

            $teacher = Auth::guard('teacher')->user();

            // If exam was explicitly assigned to a teacher (teacher_id), allow if it matches
            if (!empty($exam->teacher_id) && $exam->teacher_id == $teacher->id) {
                // authorized
            } else {
                // Otherwise ensure teacher teaches this class+subject via their subCodes
                $subCodes = $teacher ? $teacher->subCodes() : collect();

                // Normalize subCodes into subCode models (handle if subCodes contains IDs)
                if ($subCodes->isNotEmpty() && !is_object($subCodes->first())) {
                    // it's probably a collection/array of IDs — fetch models
                    $subCodes = \App\subCode::whereIn('id', $subCodes)->get();
                }

                $allowed = $subCodes->contains(function ($sc) use ($exam) {
                    // $sc is a subCode model; match both class and subject
                    return isset($sc->class, $sc->subject)
                        && trim($sc->class) === trim($exam->class)
                        && trim($sc->subject) === trim($exam->subject);
                });

                if (! $allowed) {
                    // Not allowed to enter marks for this exam
                    abort(403, 'Unauthorized Access — you do not teach this class/subject.');
                }
            }

            // students of that class (ordered by name)
            $students = \App\User::where('grade', $exam->class)->orderBy('name')->get();

            // existing marks keyed by studentId for quick lookup
            $existing = \App\studentExams::where('titleId', $exam->id)->get()->keyBy('studentId');

            $isGradeOnly = is_null($exam->maxMarks) || $exam->maxMarks == 0;
            return view('teacher.examMarks.enter', compact('exam', 'students', 'existing', 'isGradeOnly'));
        }


    /**
     * Show marking form for an exam (alternate view name)
     * Keep if you used marking_form blade earlier
     */
    public function markingForm($examId)
    {
        $exam = Exam::findOrFail($examId);

        // Get students for this exam's class
        $students = User::where('grade', $exam->class)
                        ->orderBy('name')
                        ->get();

        $existing = studentExams::where('titleId', $exam->id)->get()->keyBy('studentId');

        // use view 'teacher.examMarks.marking_form' if that's what you have
        return view('teacher.examMarks.marking_form', compact('exam', 'students', 'existing'));
    }

    /**
     * Save marks (bulk) — same functionality as before
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
            $teacherId = Auth::guard('teacher')->id() ?? null;

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
                    'teacherId'     => $teacherId,
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

        // redirect back to the marking page for the same exam
        return redirect()->back()
                         ->with('status', 'Marks saved successfully.');
    }
}
