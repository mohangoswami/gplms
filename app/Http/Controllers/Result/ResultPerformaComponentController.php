<?php

namespace App\Http\Controllers\Result;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\subCode;
use App\ResultPerforma;
use App\ResultTerm;
use App\ResultComponent;

class ResultPerformaComponentController extends Controller
{
    public function index(Request $request)
    {
        // 1️⃣ Get classes dynamically
        $classes = subCode::distinct()
            ->pluck('class')
            ->filter()
            ->values();

        $class = $request->get('class');

        $performa = null;
        $terms = collect();

        if ($class) {
            $performa = ResultPerforma::firstOrCreate(
                [
                    'class' => $class,
                    'is_default' => 1,
                ],
                [
                    'academic_year' => '2025-26',
                    'name' => 'Default Result Performa',
                ]
            );

            $terms = ResultTerm::with(['components' => function ($q) {
                    $q->orderBy('order_no');
                }])
                ->where('performa_id', $performa->id)
                ->orderBy('order_no')
                ->get();
        }

        return view('admin.result_performa.components', compact(
            'classes',
            'class',
            'performa',
            'terms'
        ));
    }

    public function save(Request $request)
    {
        $request->validate([
            'class' => 'required|string',
            'components' => 'required|array',
        ]);

        $class = $request->class;

        $performa = ResultPerforma::where('class', $class)
            ->where('is_default', 1)
            ->firstOrFail();

        foreach ($request->components as $termId => $components) {
            foreach ($components as $componentId => $row) {

                // Existing component
                if (is_numeric($componentId)) {
                    ResultComponent::where('id', $componentId)
                        ->where('term_id', $termId)
                        ->update([
                            'name' => $row['name'],
                            'evaluation_type' => $row['evaluation_type'],
                            'max_marks' => $row['max_marks'] ?? null,
                            'order_no' => $row['order_no'],
                            'is_included' => isset($row['is_included']) ? 1 : 0,
                        ]);
                }
                // New component
                else {
                    ResultComponent::create([
                        'term_id' => $termId,
                        'name' => $row['name'],
                        'evaluation_type' => $row['evaluation_type'],
                        'max_marks' => $row['max_marks'] ?? null,
                        'order_no' => $row['order_no'],
                        'is_included' => isset($row['is_included']) ? 1 : 0,
                    ]);
                }
            }
        }

        return back()->with('status', 'Components updated successfully');
    }
}
