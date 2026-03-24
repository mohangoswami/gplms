<?php

namespace App\Http\Controllers\Result;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ResultPerforma;
use App\ResultTerm;
use App\subCode;

class ResultPerformaTermController extends Controller
{

public function index(Request $request)
{
    // 1️⃣ Get available classes dynamically
    $classes = subCode::distinct()
        ->pluck('class')
        ->filter()
        ->values();

    // 2️⃣ Selected class (from dropdown)
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

        $terms = ResultTerm::where('performa_id', $performa->id)
            ->orderBy('order_no')
            ->get();
    }

    return view('admin.result_performa.terms', compact(
        'classes',
        'class',
        'performa',
        'terms'
    ));
}



    public function edit($class)
    {
        $performa = ResultPerforma::where('class', $class)
            ->where('is_default', 1)
            ->firstOrFail();

        $terms = ResultTerm::where('performa_id', $performa->id)
            ->orderBy('order_no')
            ->get();

        return view('admin.result_performa.terms', compact(
            'class', 'performa', 'terms'
        ));
    }
public function save(Request $request)
{
    $request->validate([
        'class' => 'required|string',
        'terms' => 'required|array|min:1',
        'terms.*.name' => 'required|string|max:50',
        'terms.*.order_no' => 'required|integer|min:1',
    ]);

    $class = $request->class;

    $performa = ResultPerforma::where('class', $class)
        ->where('is_default', 1)
        ->firstOrFail();

    foreach ($request->terms as $termId => $row) {

        // existing term
        if (is_numeric($termId)) {
            ResultTerm::where('id', $termId)
                ->where('performa_id', $performa->id)
                ->update([
                    'name' => $row['name'],
                    'order_no' => $row['order_no'],
                ]);
        }
        // new term
        else {
            ResultTerm::create([
                'performa_id' => $performa->id,
                'name' => $row['name'],
                'order_no' => $row['order_no'],
            ]);
        }
    }

    return back()->with('status', 'Terms updated successfully');
}

}
