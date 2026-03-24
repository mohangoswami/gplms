<?php

namespace App\Http\Controllers\Result;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ResultPerforma;
use App\ResultPerformaItem;

class ResultPerformaController extends Controller
{
    public function edit($class)
    {
        $performa = ResultPerforma::where('class', $class)
            ->where('is_default', 1)
            ->firstOrFail();

        // One row per subject (grouped)
        $subjects = ResultPerformaItem::with('subCode')
            ->where('performa_id', $performa->id)
            ->groupBy('sub_code_id')
            ->orderBy('subject_order')
            ->get();

        return view('admin.result_performa.edit',
            compact('class', 'performa', 'subjects'));
    }

    public function update(Request $request, $class)
{
    $data = $request->validate([
        'subjects' => 'required|array',
    ]);

    foreach ($data['subjects'] as $row) {

        ResultPerformaItem::where('performa_id', $request->performa_id)
            ->where('sub_code_id', $row['sub_code_id'])
            ->update([
                'subject_order'   => $row['order'],
                'is_included'     => isset($row['is_included']) ? 1 : 0,
                'evaluation_type' => $row['evaluation_type'],
            ]);
    }

    return back()->with('status', 'Result performa updated successfully');
}

}
