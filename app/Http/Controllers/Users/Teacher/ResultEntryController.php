<?php

namespace App\Http\Controllers\Users\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ResultEntryPermission;
use App\ResultComponent;
use App\User;
use App\Http\Controllers\Result\MarksEntryController;
use App\ResultPerforma;
use App\ResultSubjectComponent;
use App\StudentExamEntry;
use DB;
use App\ResultPerformaItem;
use App\ResultFinalization;


class ResultEntryController extends Controller
{

        public function __construct()
    {
        $this->middleware('auth:teacher');
    }



   public function dashboard()
        {
            return redirect()->route('teacher.results.list');
        }


public function studentList(Request $request)
{
    $teacherId = auth()->guard('teacher')->id();
    $class = $request->get('class');

    // Allowed classes
    $classes = ResultEntryPermission::where('teacher_id', $teacherId)
        ->distinct()
        ->pluck('class')
        ->values();

    if ($class && !$classes->contains($class)) {
        abort(403);
    }

    $students = collect();
    $statusMap = collect();

    if ($class) {

        // Students
        $students = User::where('grade', $class)
            ->orderBy('name')
            ->orderBy('id')
            ->get();

        // 🔢 Total components teacher must fill
        $totalComponents = ResultEntryPermission::where([
                'teacher_id' => $teacherId,
                'class' => $class,
            ])->count();

        foreach ($students as $student) {

            $filledCount = StudentExamEntry::where('student_id', $student->id)
                ->whereIn(
                    'component_id',
                    ResultEntryPermission::where([
                        'teacher_id' => $teacherId,
                        'class' => $class,
                    ])->pluck('component_id')
                )
                ->count();

            if ($filledCount === 0) {
                $status = 'pending';      // 🔴
            } elseif ($filledCount < $totalComponents) {
                $status = 'partial';      // 🟡
            } else {
                $status = 'complete';     // 🟢
            }

            $statusMap[$student->id] = $status;
        }
    }
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


    return view(
        'results.student_list',
        compact(
            'classes',
            'class',
            'students',
            'statusMap',
            'finalizedMap')
    );
}



public function entry(Request $request, User $student)
{
    $allowed = ResultEntryPermission::where([
        'teacher_id' => auth()->guard('teacher')->id(),
        'class'      => $student->grade,
    ])->exists();

    abort_unless($allowed, 403);

    return app(\App\Http\Controllers\Result\MarksEntryController::class)
        ->create($request, $student);
}

    public function save(Request $request, User $student)
        {
            // 🔒 Permission again (never trust frontend)
            $allowed = ResultEntryPermission::where([
                'teacher_id' => auth()->guard('teacher')->id(),
                'class'      => $student->grade,
            ])->exists();

            abort_unless($allowed, 403, 'Unauthorized');

            // 🔁 Reuse CORE save logic
            return app(MarksEntryController::class)->save($request, $student);
        }



  public function finalize(User $student)
    {
        // dd($student);
        // abort_unless(auth('admin')->check(), 403);

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
    }



