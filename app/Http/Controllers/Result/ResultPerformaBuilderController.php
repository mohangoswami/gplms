<?php

namespace App\Http\Controllers\Result;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ResultPerforma;
use App\ResultTerm;
use App\ResultComponent;
use App\ResultPerformaItem;
use App\SubCode;

class ResultPerformaBuilderController extends Controller
{


    public function edit($class)
    {
        $performa = ResultPerforma::where('class', $class)
            ->where('is_default', 1)
            ->firstOrFail();

        $terms = ResultTerm::with('components')
            ->where('performa_id', $performa->id)
            ->orderBy('order_no')
            ->get();

        $subjects = ResultPerformaItem::with('subCode')
            ->where('performa_id', $performa->id)
            ->orderBy('subject_order')
            ->get();

        return view('admin.result_performa.builder', compact(
            'class', 'performa', 'terms', 'subjects'
        ));
    }

    public function save(Request $request, $class)
    {
        // Abhi sirf structure save kareinge
        // (Next step me logic add hoga)

        return back()->with('status', 'Performa updated');
    }
}
