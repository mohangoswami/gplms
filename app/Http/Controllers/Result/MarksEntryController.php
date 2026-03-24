<?php

namespace App\Http\Controllers\Result;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ResultPerforma;
use App\ResultPerformaItem;
use App\ResultEntryPermission;
use App\User;
use App\StudentExamEntry;
use DB;
use App\subCode;
use App\ResultTerm;
use Illuminate\Validation\ValidationException;
use App\ResultSubjectComponent;
use Illuminate\Database\QueryException;
use App\ResultStudentAttendance;
use App\ResultStudentCoScholastic;
use App\ResultStudentHealthRecord;
use App\ResultCoScholasticArea;
use App\ResultFinalization;
use Auth;
use App\Services\ResultCalculationService;




class MarksEntryController extends Controller
{


    public function __construct()
    {
        $this->middleware(function ($request, $next) {

            if (
                auth('admin')->check() ||
                auth('teacher')->check()
            ) {
                return $next($request);
            }

            abort(403);
        });
    }


public function create(Request $request, User $student)
{
    /*
    |--------------------------------------------------------------------------
    | 1️⃣ Fetch DEFAULT PERFORMA for student's class (ANNUAL BASE)
    |--------------------------------------------------------------------------
    */
    $performa = ResultPerforma::where('class', $student->grade)
        ->where('is_default', 1)
        ->first();

    if (!$performa) {
        return redirect()
            ->route('admin.results.studentList', ['class' => $student->grade])
            ->with(
                'error',
                'Result Performa is not configured for Class ' . $student->grade
            );
    }

    /*
    |--------------------------------------------------------------------------
    | 2️⃣ Load SUBJECTS (mapped & included)
    |--------------------------------------------------------------------------
    */
    $subjects = ResultPerformaItem::with([
            'subCode',
            'subjectComponents.component.term',
        ])
        ->where('performa_id', $performa->id)
        ->where('is_included', 1)
        ->whereHas('subjectComponents')
        ->orderBy('subject_order')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | 3️⃣ Load ALL TERMS of this PERFORMA (TERM I, TERM II, etc.)
    |--------------------------------------------------------------------------
    */
    $terms = ResultTerm::where('performa_id', $performa->id)
        ->whereHas('components.subjectMappings')
        ->with(['components' => function ($q) {
            $q->whereHas('subjectMappings');
        }])
        ->orderBy('order_no')
        ->get();

    if ($terms->isEmpty()) {
        return back()->with('error', 'No terms configured for this performa.');
    }

    /*
    |--------------------------------------------------------------------------
    | 4️⃣ Resolve CURRENT TERM (UI only, NOT for finalization)
    |--------------------------------------------------------------------------
    */
    $termId = $request->get('term_id', $terms->first()->id);

    $term = $terms->firstWhere('id', $termId);

    if (!$term) {
        abort(404, 'Invalid term selected.');
    }

    /*
    |--------------------------------------------------------------------------
    | 5️⃣ Co-Scholastic Areas
    |--------------------------------------------------------------------------
    */
    $coScholasticAreas = ResultCoScholasticArea::where('performa_id', $performa->id)
        ->where('class', $student->grade)
        ->where('is_active', 1)
        ->orderBy('display_order')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | 6️⃣ Existing MARKS (ONLY for selected TERM – entry purpose)
    |--------------------------------------------------------------------------
    */
    $existingEntries = StudentExamEntry::where('student_id', $student->id)
        ->get()
        ->keyBy(function ($item) {
            return $item->result_performa_item_id
                . '_' . $item->component_id
                . '_' . $item->term_id;
        });



    /*
    |--------------------------------------------------------------------------
    | 7️⃣ Attendance (TERM wise)
    |--------------------------------------------------------------------------
    */
    $attendance = ResultStudentAttendance::where([
        'student_id' => $student->id,
        'term_id'    => $term->id,
    ])->first();

    /*
    |--------------------------------------------------------------------------
    | 8️⃣ Co-Scholastic Grades (TERM wise)
    |--------------------------------------------------------------------------
    */
    $coScholastic = ResultStudentCoScholastic::where([
            'student_id' => $student->id,
        ])
        ->get()
        ;

    /*
    |--------------------------------------------------------------------------
    | 9️⃣ Health Record (ANNUAL)
    |--------------------------------------------------------------------------
    */
    $health = ResultStudentHealthRecord::where('student_id', $student->id)->first();

    /*
    |--------------------------------------------------------------------------
    | 🔟 Class Students (slider navigation)
    |--------------------------------------------------------------------------
    */
    $classStudents = User::where('grade', $student->grade)
        ->orderBy('name')
        ->orderBy('id')
        ->get(['id', 'name']);

    $currentIndex = $classStudents
        ->pluck('id')
        ->search($student->id);

    /*
    |--------------------------------------------------------------------------
    | 1️⃣1️⃣ STATUS MAP (TERM-WISE completeness for UI)
    |--------------------------------------------------------------------------
    */
    $allComponentIds = ResultSubjectComponent::whereIn(
            'performa_item_id',
            $subjects->pluck('id')
        )
        ->pluck('component_id')
        ->unique();

    $totalComponents = $allComponentIds->count();

    $statusMap = collect();

    foreach ($classStudents as $s) {

        $filledCount = StudentExamEntry::where('student_id', $s->id)
            ->where('term_id', $term->id)
            ->whereIn('component_id', $allComponentIds)
            ->count();

        if ($filledCount === 0) {
            $statusMap[$s->id] = 'pending';
        } elseif ($filledCount < $totalComponents) {
            $statusMap[$s->id] = 'partial';
        } else {
            $statusMap[$s->id] = 'complete';
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 1️⃣2️⃣ FINALIZATION CHECK (ANNUAL – PERFORMA BASED)
    |--------------------------------------------------------------------------
    */
    $isFinalized = ResultFinalization::isFinal(
        $student->id,
        $performa->id   // ✅ NOT term_id
    );

    /*
    |--------------------------------------------------------------------------
    | 1️⃣3️⃣ If FINALIZED → redirect to FINAL PDF (ALL TERMS)
    |--------------------------------------------------------------------------
    */
    // if ($isFinalized) {
    //     return redirect()->route(
    //         Auth::guard('admin')->check()
    //             ? 'admin.results.pdf'
    //             : 'teacher.results.pdf',
    //         $student->id
    //     );
    // }

    $totalEntries = StudentExamEntry::where('student_id', $student->id)
        ->whereIn('term_id', $terms->pluck('id'))
        ->count();


    // 🔴 CHECK: any BLANK marks across ALL TERMS
    $blankTrue = StudentExamEntry::where('student_id', $student->id)
        ->whereIn('term_id', $terms->pluck('id'))
        ->where(function ($q) {
            $q->whereNull('marks')
            ->whereNull('grade'); // ❌ blank only (AB allowed)
        })
        ->exists();

    $hasBlank = ($totalEntries === 0) || $blankTrue;


    /*
    |--------------------------------------------------------------------------
    | 1️⃣4️⃣ Load ENTRY VIEW (EDITABLE – until annual finalize)
    |--------------------------------------------------------------------------
    */
    return view(
        'results.student_result_entry',
        compact(
            'student',
            'performa',
            'terms',
            'term',
            'subjects',
            'coScholasticAreas',
            'existingEntries',
            'attendance',
            'coScholastic',
            'health',
            'classStudents',
            'currentIndex',
            'statusMap',
            'isFinalized',
            'hasBlank'
        )
    );
}

public function index(Request $request)
{
    /*
    |--------------------------------------------------------------------------
    | 1️⃣ All classes (dropdown)
    |--------------------------------------------------------------------------
    */
    $classes = subCode::distinct()
        ->pluck('class')
        ->filter()
        ->values();

    $class = $request->get('class');

    $students     = collect();
    $statusMap    = collect();   // pending / partial / complete
    $finalizedMap = collect();   // annual finalized or not

    if ($class) {

        /*
        |--------------------------------------------------------------------------
        | 2️⃣ Students of selected class
        |--------------------------------------------------------------------------
        */
        $students = User::where('grade', $class)
            ->orderBy('name')
            ->orderBy('id')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 3️⃣ Default ANNUAL Performa
        |--------------------------------------------------------------------------
        */
        $performa = ResultPerforma::where('class', $class)
            ->where('is_default', 1)
            ->first();

        if ($performa) {

            /*
            |--------------------------------------------------------------------------
            | 4️⃣ All required COMPONENT IDs for this performa
            |--------------------------------------------------------------------------
            */
            $componentIds = ResultSubjectComponent::whereIn(
                    'performa_item_id',
                    ResultPerformaItem::where('performa_id', $performa->id)
                        ->where('is_included', 1)
                        ->pluck('id')
                )
                ->pluck('component_id')
                ->unique();

            $totalComponents = $componentIds->count();

            /*
            |--------------------------------------------------------------------------
            | 5️⃣ Build STATUS MAP + FINALIZED MAP
            |--------------------------------------------------------------------------
            */
            foreach ($students as $s) {

                // 🔹 MARKS STATUS (across all terms)
                $filledCount = StudentExamEntry::where('student_id', $s->id)
                    ->whereIn('component_id', $componentIds)
                    ->count();

                if ($filledCount === 0) {
                    $statusMap[$s->id] = 'pending';
                } elseif ($filledCount < $totalComponents) {
                    $statusMap[$s->id] = 'partial';
                } else {
                    $statusMap[$s->id] = 'complete';
                }

                // 🔒 ANNUAL FINALIZATION STATUS (PERFORMA BASED)
                $finalizedMap[$s->id] = ResultFinalization::where([
                    'student_id'  => $s->id,
                    'performa_id' => $performa->id,
                    'status'      => 'FINAL',
                ])->exists();
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 6️⃣ Load student list view
    |--------------------------------------------------------------------------
    */
    return view(
        'results.student_list',
        compact(
            'classes',
            'class',
            'students',
            'statusMap',
            'finalizedMap'
        )
    );
}


public function save(Request $request, User $student)
{
    // dd($request->all());
    /*
    |--------------------------------------------------------------------------
    | 0️⃣ BASIC CONTEXT
    |--------------------------------------------------------------------------
    */
    $performa = ResultPerforma::where('class', $student->grade)
        ->where('is_default', 1)
        ->firstOrFail();

    /*
    |--------------------------------------------------------------------------
    | 🔒 BLOCK ONLY IF ANNUAL FINALIZED
    |--------------------------------------------------------------------------
    */
    if (ResultFinalization::isFinal($student->id, $performa->id)) {
        return back()->with(
            'error',
            'Final annual result already declared. Editing not allowed.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 1️⃣ VALIDATION
    |--------------------------------------------------------------------------
    */
    $request->validate([
        'marks.*.*.value'        => 'nullable|numeric|min:0',
        'marks.*.*.component_id' => 'required|exists:result_components,id',
        'marks.*.*.term_id'      => 'required|exists:result_terms,id',
    ]);

    /*
    |--------------------------------------------------------------------------
    | 2️⃣ AUTH USER
    |--------------------------------------------------------------------------
    */
    if (auth('admin')->check()) {
        $role = 'admin';
        $user = auth('admin')->user();
    } elseif (auth('teacher')->check()) {
        $role = 'teacher';
        $user = auth('teacher')->user();
    } else {
        abort(401);
    }

    DB::beginTransaction();

    try {

        /*
        |--------------------------------------------------------------------------
        | 3️⃣ SAVE MARKS (MULTI-TERM SAFE)
        |--------------------------------------------------------------------------
        */
        foreach ($request->marks as $itemId => $components) {

            foreach ($components as $componentId => $row) {

                // ✅ ABSENT CASE
                if (!empty($row['absent'])) {

                    StudentExamEntry::updateOrCreate(
                        [
                            'student_id'              => $student->id,
                            'result_performa_item_id' => $itemId,
                            'component_id'            => $row['component_id'],
                            'term_id'                 => $row['term_id'],
                        ],
                        [
                            'marks' => null,
                            'grade' => 'AB',
                            'entered_by_id'   => $user->id,
                            'entered_by_role' => $role,          ]
                    );

                    continue;
                }

                // ❌ BLANK → NOT SAVED (Finalize block)
                if (!array_key_exists('value', $row) || $row['value'] === '') {
                    continue;
                }

                // ✅ NORMAL / ZERO MARKS
                StudentExamEntry::updateOrCreate(
                    [
                        'student_id'              => $student->id,
                        'result_performa_item_id' => $itemId,
                        'component_id'            => $row['component_id'],
                        'term_id'                 => $row['term_id'],
                    ],
                    [
                        'marks' => $row['value'], // 0 allowed
                        'grade' => null,
                        'entered_by_id'   => $user->id,
                        'entered_by_role' => $role,          ]
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 4️⃣ ATTENDANCE (TERM-WISE)
        |--------------------------------------------------------------------------
        */
        if ($request->filled('attendance')) {
            ResultStudentAttendance::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'term_id'    => $request->term_id,
                ],
                [
                    'days_present' => $request->attendance['present'] ?? null,
                    'working_days' => $request->attendance['working'] ?? null,
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 5️⃣ CO-SCHOLASTIC (TERM-WISE)
        |--------------------------------------------------------------------------
        */
        foreach ($request->co_scholastic ?? [] as $termId => $areas) {

            foreach ($areas as $areaId => $grade) {

                if (!$grade) continue;

                ResultStudentCoScholastic::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'term_id'    => $termId,
                        'co_scholastic_area_id' => $areaId,
                    ],
                    [
                        'grade' => $grade,
                    ]
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 6️⃣ HEALTH + REMARK (ANNUAL)
        |--------------------------------------------------------------------------
        */
        if ($request->filled('health') || $request->filled('remark')) {
            ResultStudentHealthRecord::updateOrCreate(
                ['student_id' => $student->id],
                [
                    'height' => $request->health['height'] ?? null,
                    'weight' => $request->health['weight'] ?? null,
                    'remark' => $request->remark ?? null,
                ]
            );
        }

        DB::commit();

        return back()->with('status', 'Marks saved successfully.');

    } catch (\Throwable $e) {

        DB::rollBack();

        \Log::error('Result Save Failed', [
            'student_id' => $student->id,
            'error'      => $e->getMessage(),
        ]);

        return back()->with('error', 'Save failed: ' . $e->getMessage());
    }
}



protected function getNextStudent(User $student)
{
    return User::where('grade', $student->grade)
        ->where(function ($q) use ($student) {
            $q->where('name', '>', $student->name)
              ->orWhere(function ($q2) use ($student) {
                  $q2->where('name', $student->name)
                     ->where('id', '>', $student->id);
              });
        })
        ->orderBy('name')
        ->orderBy('id')
        ->first();
}



  public function finalize(User $student)
    {
        abort_unless(auth('admin')->check(), 403);

        /*
        |--------------------------------------------------------------------------
        | 1️⃣ Get DEFAULT ANNUAL PERFORMA
        |--------------------------------------------------------------------------
        */
        $performa = ResultPerforma::where('class', $student->grade)
            ->where('is_default', 1)
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | 2️⃣ BLOCK FINALIZE IF ANY MARK IS BLANK (NOT AB)
        |--------------------------------------------------------------------------
        | marks = NULL AND grade = NULL  → ❌ NOT ALLOWED
        */
        $hasBlank = StudentExamEntry::where('student_id', $student->id)
            ->whereNull('marks')
            ->whereNull('grade') // ✅ means not entered, not AB
            ->exists();

        if ($hasBlank) {
            return back()->with(
                'failed',
                'Cannot finalize. Some subject marks are missing.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 3️⃣ FINALIZE ANNUAL RESULT (PERFORMA BASED)
        |--------------------------------------------------------------------------
        */
        ResultFinalization::updateOrCreate(
            [
                'student_id'  => $student->id,
                'performa_id' => $performa->id,
            ],
            [
                'status'            => 'FINAL',
                'finalized_by_id'   => auth('admin')->id(),
                'finalized_by_role' => 'admin',
                'finalized_at'      => now(),
            ]
        );

        return back()->with(
            'status',
            'Annual result finalized successfully.'
        );
    }


    public function reopen(User $student)
    {
        abort_unless(auth('admin')->check(), 403);

        $deleted = ResultFinalization::where([
            'student_id' => $student->id,
            'status'     => 'FINAL',
        ])->delete();

        if (!$deleted) {
            return back()->with(
                'error',
                'Result is not finalized or already reopened.'
            );
        }

        return back()->with(
            'status',
            '✅ Annual result reopened for editing.'
        );
    }


    public function finalizeAll(string $class)
    {
        abort_unless(auth('admin')->check(), 403);

        $performa = ResultPerforma::where('class', $class)
            ->where('is_default', 1)
            ->firstOrFail();

        $students = User::where('grade', $class)->get();

        $finalized = 0;
        $skipped   = 0;

        foreach ($students as $student) {
            // Skip already finalized
            $alreadyDone = ResultFinalization::where([
                'student_id'  => $student->id,
                'performa_id' => $performa->id,
                'status'      => 'FINAL',
            ])->exists();

            if ($alreadyDone) {
                $finalized++;
                continue;
            }

            // Same logic as individual Finalize button:
            // skip if 0 entries OR any entry with marks=null AND grade=null
            $totalEntries = StudentExamEntry::where('student_id', $student->id)->count();
            $blankTrue    = StudentExamEntry::where('student_id', $student->id)
                ->whereNull('marks')
                ->whereNull('grade')
                ->exists();

            if (($totalEntries === 0) || $blankTrue) {
                $skipped++;
                continue;
            }

            ResultFinalization::updateOrCreate(
                [
                    'student_id'  => $student->id,
                    'performa_id' => $performa->id,
                ],
                [
                    'status'            => 'FINAL',
                    'finalized_by_id'   => auth('admin')->id(),
                    'finalized_by_role' => 'admin',
                    'finalized_at'      => now(),
                ]
            );

            $finalized++;
        }

        $msg = "Finalized {$finalized} student(s).";
        if ($skipped > 0) {
            $msg .= " Skipped {$skipped} student(s) with missing marks.";
        }

        return redirect()
            ->route('admin.results.studentList', ['class' => $class])
            ->with('status', $msg);
    }


    public function reopenAll(string $class)
    {
        abort_unless(auth('admin')->check(), 403);

        $performa = ResultPerforma::where('class', $class)
            ->where('is_default', 1)
            ->firstOrFail();

        $studentIds = User::where('grade', $class)->pluck('id');

        $deleted = ResultFinalization::whereIn('student_id', $studentIds)
            ->where('performa_id', $performa->id)
            ->where('status', 'FINAL')
            ->delete();

        return redirect()
            ->route('admin.results.studentList', ['class' => $class])
            ->with('status', "Reopened {$deleted} student result(s) for editing.");
    }


        //Result pass fail final preparation
public function calculateResult(User $student, Request $request)
{
    $termId = $request->term_id;

    // 🔒 Step 11 dependency
    if (!ResultFinalization::isFinal($student->id, $termId)) {
        abort(403, 'Result not finalized yet');
    }

    // 🧮 CALL SERVICE (NO LOGIC HERE)


    $terms = ResultTerm::where('performa_id', $performa->id)->get();

    $calculator = new ResultCalculationService(
        $student->id,
        $terms
    );

    $result = $calculator->calculate();

    // 👇 for now testing / later view / pdf
    return response()->json($result);
}

}
