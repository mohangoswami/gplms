<?php

namespace App\Http\Controllers\Result;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ResultCoScholasticArea;
use App\ResultPerforma;
use App\subCode;

class ResultCoScholasticAreaController extends Controller
{
    public function index(Request $request)
    {
        $performas = ResultPerforma::orderBy('class')->get();

        $classes = subCode::distinct()
            ->pluck('class')
            ->filter()
            ->unique();

        $areas = ResultCoScholasticArea::with('performa')
            ->orderBy('class')
            ->orderBy('display_order')
            ->get();

            return view(
            'admin.result_performa.co_scholastic.index',
            compact('performas', 'areas', 'classes')
        );
    }

    public function store(Request $request)
{
    $request->validate([
        'performa_id' => 'required|exists:result_performas,id',
        'area_name'   => 'required|string|max:100',
    ]);

    $performa = ResultPerforma::findOrFail($request->performa_id);

    ResultCoScholasticArea::create([
        'performa_id'   => $performa->id,
        'class'         => $performa->class,   // ✅ AUTO CLASS
        'area_name'     => $request->area_name,
        'display_order' => ResultCoScholasticArea::where([
            'performa_id' => $performa->id,
            'class'       => $performa->class,
        ])->count() + 1,
    ]);

    return back()->with('success', 'Co-Scholastic Area added');
}

    public function destroy($id)
    {
        ResultCoScholasticArea::findOrFail($id)->delete();
        return back()->with('success', 'Area deleted');
    }


    public function edit($id)
{
    $area = ResultCoScholasticArea::with('performa')->findOrFail($id);
    $performas = ResultPerforma::orderBy('class')->get();

    return view(
        'admin.result_performa.co_scholastic.edit',
        compact('area', 'performas')
    );
}

public function update(Request $request, $id)
{
    $request->validate([
        'performa_id' => 'required|exists:result_performas,id',
        'area_name'   => 'required|string|max:100',
        'is_active'   => 'required|boolean',
    ]);

    $area = ResultCoScholasticArea::findOrFail($id);
    $performa = ResultPerforma::findOrFail($request->performa_id);

    $area->update([
        'performa_id' => $performa->id,
        'class'       => $performa->class, // ✅ auto-sync class
        'area_name'   => $request->area_name,
        'is_active'   => $request->is_active,
    ]);

    return redirect()
        ->route('admin.result_performa.co_scholastic.index')
        ->with('success', 'Co-Scholastic Area updated successfully');
}

}
