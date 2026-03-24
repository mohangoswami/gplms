<?php

namespace App\Http\Controllers\Users\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\subCode;
use App\classwork;
use App\Exam;
use App\Term;
use Auth;
use Illuminate\Support\Facades\Validator;
use DB;
use Illuminate\Support\Facades\Storage;
use App\Notifications\emailNotification;
use App\User;
use App\stuHomeworkUpload;
use App\studentExams;

class TeacherResultController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:teacher');
    }


    public function resultList()
    {
        $subCodes[] =  Auth::guard('teacher')->user()->class_code0;
        $subCodes[] =  Auth::guard('teacher')->user()->class_code1;
        $subCodes[] =  Auth::guard('teacher')->user()->class_code2;
        $subCodes[] =  Auth::guard('teacher')->user()->class_code3;
        $subCodes[] =  Auth::guard('teacher')->user()->class_code4;
        $subCodes[] =  Auth::guard('teacher')->user()->class_code5;
        $subCodes[] =  Auth::guard('teacher')->user()->class_code6;
        $subCodes[] =  Auth::guard('teacher')->user()->class_code7;
        $subCodes[] =  Auth::guard('teacher')->user()->class_code8;
        $subCodes[] =  Auth::guard('teacher')->user()->class_code9;
        $subCodes[] =  Auth::guard('teacher')->user()->class_code10;
        $subCodes[] =  Auth::guard('teacher')->user()->class_code11;

        $classCodes = subCode::all()->sortBy("class");

        $resultLists  = Exam::all()->where('admission_number',Auth::user()->admission_number)->sortByDesc('created_at');

        return view('/teacher/resultList',compact('subCodes','resultLists','classCodes'));
    }

    public function result($id)
    {
        $subCodes[] =  Auth::guard('teacher')->user()->class_code0;
        $subCodes[] =  Auth::guard('teacher')->user()->class_code1;
        $subCodes[] =  Auth::guard('teacher')->user()->class_code2;
        $subCodes[] =  Auth::guard('teacher')->user()->class_code3;
        $subCodes[] =  Auth::guard('teacher')->user()->class_code4;
        $subCodes[] =  Auth::guard('teacher')->user()->class_code5;
        $subCodes[] =  Auth::guard('teacher')->user()->class_code6;
        $subCodes[] =  Auth::guard('teacher')->user()->class_code7;
        $subCodes[] =  Auth::guard('teacher')->user()->class_code8;
        $subCodes[] =  Auth::guard('teacher')->user()->class_code9;
        $subCodes[] =  Auth::guard('teacher')->user()->class_code10;
        $subCodes[] =  Auth::guard('teacher')->user()->class_code11;

        $classCodes = subCode::all()->sortBy("class");
        $exams = Exam::all()->where('id',$id);
        $results  = studentExams::all()->where('titleId',$id)->sortByDesc('created_at');

        return view('/teacher/result',compact('subCodes','results','classCodes','id','exams'));
    }

    public function postResult(Request $request){
        $data = $request->input();
        $id = $data['id'];
        $editId = $data['editId'];
        try{
                DB::table('student_exams')
            ->where('id', $editId)
            ->update([  'marksObtain' => $data['editMarksObtain'],
                        ]);
                        return redirect('teacher/result/'.$id)->with('status','Marks Inserted successfully');
			}
			catch(Exception $e){
				return redirect('teacher/result/'.$id)->with('failed',"operation failed");
			}
    }

    public function editStudentResult($id){
        $studentResults = studentExams::all()->where('id',$id);
        return view('teacher.editResult', compact('studentResults','id'));
      }

      public function topperSwitch(Request $request){
        $data = $request->input();
        $id = $data['id'];
        try{
            DB::table('exams')
        ->where('id', $id)
        ->update([  'topperShown' => $data['topperShown'],
                    ]);
                    return redirect('teacher/result/'.$id)->with('status','Marks Inserted successfully');
        }
        catch(Exception $e){
            return redirect('teacher/result/'.$id)->with('failed',"operation failed");
        }
      }




}
