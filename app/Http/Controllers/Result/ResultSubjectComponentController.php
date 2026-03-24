<?php

namespace App\Http\Controllers\Result;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\subCode;
use App\ResultPerforma;
use App\ResultTerm;
use App\ResultPerformaItem;
use App\ResultSubjectComponent;
use App\ResultComponent;
use DB;

class ResultSubjectComponentController extends Controller
{


protected function seedSubjectsForPerforma(ResultPerforma $performa)
{
    $subjects = subCode::where('class', $performa->class)->get();

    foreach ($subjects as $index => $sub) {
        ResultPerformaItem::firstOrCreate(
            [
                'performa_id' => $performa->id,
                'sub_code_id' => $sub->id,
            ],
            [
                'subject_order' => $index + 1,
                'is_included' => 1,
            ]
        );
    }
}





  public function index(Request $request)
{
    $classes = subCode::distinct()
        ->pluck('class')
        ->filter()
        ->values();

    $class = $request->get('class');
    $subjectId = $request->get('subject_id');

    // ✅ ALWAYS define variables (very important)
    $performa = null;
    $terms = collect();
    $subjects = collect();
    $allSubjects = collect();   // ✅ FIX

    if ($class) {

        $performa = ResultPerforma::firstOrCreate(
            ['class' => $class, 'is_default' => 1],
            ['academic_year' => '2025-26', 'name' => 'Default Result Performa']
        );
    //seed subjects for the performa if not already seeded
        $this->seedSubjectsForPerforma($performa);

        $terms = ResultTerm::with('components')
            ->where('performa_id', $performa->id)
            ->orderBy('order_no')
            ->get();

        // 🔹 Base query with relations
        $baseQuery = ResultPerformaItem::with([
                'subCode',
                'subjectComponents'
            ])
            ->where('performa_id', $performa->id)
            ->where('is_included', 1)
            ->orderBy('subject_order');

        // 🔹 All subjects (for dropdown)
        $allSubjects = (clone $baseQuery)->get();

        // 🔹 Subjects for table (filtered or all)
        if ($subjectId) {
            $subjects = (clone $baseQuery)
                ->where('id', $subjectId)
                ->get();
        } else {
            $subjects = $allSubjects;
        }
    }

    return view('admin.result_performa.mapping', compact(
        'classes',
        'class',
        'terms',
        'subjects',
        'allSubjects',
        'subjectId'
    ));
}



public function save(Request $request)
{
    $request->validate([
        'class'   => 'required|string',
        'mapping' => 'array',
    ]);

    $performa = ResultPerforma::where('class', $request->class)
        ->where('is_default', 1)
        ->firstOrFail();

    DB::transaction(function () use ($request, $performa) {

        // 🔹 1. Delete old mappings for this performa
        $deletedCount = ResultSubjectComponent::whereIn(
            'performa_item_id',
            ResultPerformaItem::where('performa_id', $performa->id)->pluck('id')
        )->delete();



        // 🔹 2. Insert new mappings
        foreach ($request->mapping ?? [] as $subjectId => $components) {

            foreach ($components as $componentId => $row) {

                // Checkbox unchecked → skip
                if (empty($row['enabled'])) {
                    continue;
                }

                ResultSubjectComponent::create([
                    'performa_item_id'   => $subjectId,
                    'component_id'       => $componentId,
                'max_marks_override' => isset($row['max_marks'])
                    ? round($row['max_marks'], 2)
                    : null,
                ]);
            }
        }
    });

    return back()->with('status', 'Subject–Component mapping saved successfully');
}
}
