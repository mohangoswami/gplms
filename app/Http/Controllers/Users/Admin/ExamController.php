<?php

namespace App\Http\Controllers\Users\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Category;
use Illuminate\Support\Facades\Validator;
use App\Exam;
use App\subCode;
use App\User;
use App\FeePlan;
use App\Term;
use App\Teacher;
use App\Cashier;
use App\flashNews;
use App\classwork;
use App\Holiday;
use DB;
use App\stuHomeworkUpload;
use Illuminate\Support\Facades\Storage;
use Auth;
use Illuminate\Support\Facades\Hash;
use App\RouteName;
use Carbon\Carbon;
use App\Receipt;


class ExamController extends Controller
{
     public function __construct()
    {
        $this->middleware('auth:admin'); // ensure admin guard
    }

    public function index()
    {

        // show all exams (or paginated)
        $exams = Exam::with('term')->orderByDesc('created_at')->paginate(25);

        return view('admin.exams.index', compact('exams'));
    }

    public function create()
    {
        // All terms for dropdown
        $terms = Term::all();

        // Fetch classes and subjects like teacher did
        $classCodes = \App\subCode::all()->sortBy('class');

        // Extract distinct class names
        $classes = $classCodes->pluck('class')->unique()->values();

        // Extract distinct subject names
        $subjects = $classCodes->pluck('subject')->unique()->values();

        return view('admin.exams.create', compact('terms', 'classes', 'subjects', 'classCodes'));
    }


    public function store(Request $request)
        {
            $data = $request->validate([
                'term_id'  => 'required|exists:terms,id',
                'class'    => 'required|string|max:50',
                'subject'  => 'required|string|max:100',
                // allow decimal marks (e.g. 12.5)
                'maxMarks' => 'required|numeric|min:0',
                'type'     => 'nullable|string|max:50', // e.g. Written/Oral
            ]);

            $exam = new Exam();
            $exam->term_id = $data['term_id'];
            $exam->class = $data['class'];
            $exam->subject = $data['subject'];
            $exam->maxMarks = $data['maxMarks'];
            $exam->type = $data['type'] ?? 'Written';
            $exam->name = Auth::guard('admin')->user()->name;
            $exam->email = Auth::guard('admin')->user()->email;
            $exam->admin_id = Auth::guard('admin')->id();
            $exam->save();

            return redirect()->route('admin.exams.index')->with('status', 'Exam created successfully.');
        }


    public function edit(Exam $exam)
    {
       // All terms for dropdown
    $terms = Term::all();

    // Fetch classes and subjects like teacher did
    $classCodes = \App\subCode::all()->sortBy('class');

    // Extract distinct class names
    $classes = $classCodes->pluck('class')->unique()->values();

    // Extract distinct subject names
    $subjects = $classCodes->pluck('subject')->unique()->values();

        return view('admin.exams.edit', compact('terms', 'classes', 'subjects', 'classCodes', 'exam'));

    }

    public function update(Request $request, Exam $exam)
    {
          $data = $request->validate([
                'term_id'  => 'required|exists:terms,id',
                'class'    => 'required|string|max:50',
                'subject'  => 'required|string|max:100',
                // allow decimal marks (e.g. 12.5)
                'maxMarks' => 'required|numeric|min:0',
                'type'     => 'nullable|string|max:50', // e.g. Written/Oral
            ]);
        $exam->update($data);

        return redirect()->route('admin.exams.index')->with('status','Exam updated.');
    }

    public function destroy(Exam $exam)
    {
        $exam->delete();
        return redirect()->route('admin.exams.index')->with('status','Exam deleted.');
    }

    public function show(Exam $exam)
    {
        return view('admin.exams.show', compact('exam'));
    }
}
