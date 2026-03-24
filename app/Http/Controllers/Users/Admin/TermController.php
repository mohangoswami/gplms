<?php

namespace App\Http\Controllers\Users\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Term;

class TermController extends Controller
{
    public function index()
    {
        $terms = Term::all();
        return view('admin.terms.index', compact('terms'));
    }

    public function store(Request $request)
    {
        $request->validate(['term' => 'required|string|max:50']);

        Term::create(['term' => $request->term]);
        return back()->with('status', 'Term added successfully!');
    }

    public function destroy($id)
    {
        Term::findOrFail($id)->delete();
        return back()->with('status', 'Term deleted.');
    }
}
