<?php

namespace App\Http\Controllers\Result;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\ResultEntryPermission;
use App\Teacher;
use App\subCode;
use App\ResultTerm;
use App\ResultComponent;

class ResultPermissionController extends Controller
{
    public function index()
    {
        $teachers = Teacher::orderBy('name')->get();

        $classes = subCode::distinct()
            ->pluck('class')
            ->filter()
            ->values();


        $components = ResultComponent::with('term')
            ->orderBy('term_id')
            ->orderBy('order_no')
            ->get();

        $existing = ResultEntryPermission::all()
            ->groupBy(fn ($r) => $r->teacher_id.'_'.$r->class)
            ->map(fn ($rows) => $rows->pluck('component_id')->toArray());

        return view(
            'admin.result_performa.permissions',
            compact('teachers','classes','components','existing')
        );
    }

public function save(Request $request)
{
    abort_unless(auth()->guard('admin')->check(), 403);

    $validated = $request->validate([
        'teacher_id'   => 'required|exists:teachers,id',
        'class'        => 'required|string',
        'components'   => 'nullable|array',
        'components.*' => 'exists:result_components,id',
    ]);

    $insertCount = 0;

    DB::transaction(function () use ($validated, &$insertCount) {

        ResultEntryPermission::where([
            'teacher_id' => $validated['teacher_id'],
            'class'      => $validated['class'],
        ])->delete();

        foreach ($validated['components'] ?? [] as $componentId) {

            ResultEntryPermission::create([
                'teacher_id'   => $validated['teacher_id'],
                'class'        => $validated['class'],
                'component_id' => $componentId,
            ]);

            $insertCount++;
        }
    });

    return back()->with(
        'status',
        $insertCount
            ? "Permissions updated ({$insertCount} components allowed)."
            : "All permissions removed successfully."
    );
}

public function fetch(Request $request)
{
    abort_unless(auth()->guard('admin')->check(), 403);

    $validated = $request->validate([
        'teacher_id' => 'required|exists:teachers,id',
        'class'      => 'required|string',
    ]);

    $components = ResultEntryPermission::where([
        'teacher_id' => $validated['teacher_id'],
        'class'      => $validated['class'],
    ])->pluck('component_id');

    return response()->json([
        'components' => $components,
    ]);
}




public function summary()
{
    $teachers = Teacher::orderBy('name')->get();

        $classes = subCode::distinct()
            ->pluck('class')
            ->filter()
            ->values();


    $components = ResultComponent::with('term')->get();

    $permissions = ResultEntryPermission::all()
        ->groupBy(fn ($p) => $p->class)
        ->map(function ($rows) {
            return $rows->groupBy('component_id')
                        ->map(fn ($r) => $r->pluck('teacher_id')->toArray());
        });

    return view(
        'admin.result_performa.summary',
        compact('teachers','classes','components','permissions')
    );
}


}
