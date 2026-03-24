<?php

namespace App\Http\Controllers\Users\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\subCode;
use App\classwork;
use App\Exam;
use App\Term;
use App\studentExams;
use App\studentExamWorks;
use Auth;
use Illuminate\Support\Facades\Validator;
use DB;
use Illuminate\Support\Facades\Storage;
class ExamController extends Controller
{
  public function __construct()
  {
      $this->middleware('auth:web');
  }

    public function allExams(){
        $subCodes  = subCode::all()->where('class',Auth::user()->grade);

        $exams = Exam::all()->where('class',Auth::user()->grade)->sortByDesc('startExam');
        return view('student.exams.allExams', compact('exams','subCodes'));
      }

      public function upcomingExams(){
        $subCodes  = subCode::all()->where('class',Auth::user()->grade);

        $exams = Exam::all()->where('class',Auth::user()->grade)->sortByDesc('startExam');
        return view('student.exams.upcomingExams', compact('exams','subCodes'));
      }

      public function todayExams(){
        $subCodes  = subCode::all()->where('class',Auth::user()->grade);

        $exams = Exam::all()->where('class',Auth::user()->grade)->sortByDesc('startExam');
        return view('student.exams.todayExams', compact('exams','subCodes'));
      }

      public function attemptExam($id){

        foreach(Auth::user()->unreadNotifications as $notification){
          if($notification->data['classworkId']==$id && $notification->data['workType']=='Exam'){
            $notification->markAsRead();
          }
        }
        $subCodes  = subCode::all()->where('class',Auth::user()->grade);

        $exams = Exam::all()->where('id',$id);

        $studentExams = studentExams::all()->where('titleId',$id)->where('admission_number',Auth::user()->admission_number);
        $finalSubmit = false;
        foreach($studentExams as $studentExam){
          if($studentExam->admission_number!=""){
            $finalSubmit = true;
          }
        }

        foreach($exams as $exam){
          $examId =$exam->id;
          $uploadFiles = studentExamWorks::all()->where('admission_number',Auth::user()->admission_number)->where('titleId',$examId);


          $users = User::all()->where('admission_number',Auth::user()->admission_number);
          foreach($users as $user){
            if($user->exam_permission == 0){
              return view('student.exams.examBlock', compact('exams','subCodes','id','finalSubmit'));
            }
          }

        if($exam->type =='FORM'){
            return view('student.exams.formExam', compact('exams','subCodes','id','finalSubmit'));
      }else{
        return view('student.exams.fileExam', compact('exams','uploadFiles','subCodes','id','finalSubmit'));
    }
    }
    }

 public function FileExam(Request $request){
        $data = $request->input();
        $id = $data['id'];


      try{
          $getClassSubs = DB::select('SELECT * FROM exams WHERE id = ?' , [$data['id']]);

          foreach ($getClassSubs as $getClassSub) {
         //   dd($getClassSub);
              $teacherEmail = $getClassSub->email;
              $class = $getClassSub->class;
              $subject = $getClassSub->subject;
              $title = $getClassSub->title;

            }

          $stuWork = new studentExamWorks;
          $stuWork->titleId = $data['id'];
          $stuWork->class = $class;
          $stuWork->name = Auth::user()->name;
          $stuWork->admission_number = Auth::user()->admission_number;
          $stuExam->teacherEmail = $teacherEmail;
          $stuWork->subject = $subject;
          $stuWork->title = $title;
          $userName = Auth::user()->name;
          $fileUrl = 'https://gplmschool-dev-storage-vkmjgjn4dvol.s3.amazonaws.com/' . $class . '/' . $subject . '/' . 'exams' . '/' . $title . '/' . $userName . '/' . $request->file->getClientOriginalName();
          $stuWork->fileUrl = $fileUrl;
          $stuWork->fileSize = $request->file('file')->getSize();
                   $stuWork->save();

          $file = $request->file('file');
          $imageName = $class . '/' . $subject . '/' . 'exams' . '/' . $title . '/' . $userName . '/' .  $file->getClientOriginalName();

          Storage::disk('s3')->put($imageName, file_get_contents($file));
          Storage::disk('s3')->setVisibility($imageName, 'public');


          return redirect('student/exams/attemptExam/'.$id)->with('status','File uploaded successfully');
      }
      catch(Exception $e){
          return redirect('student/exams/attemptExam/'.$id)->with('failed',"operation failed");
      }
  }

  public function getFileExam($id){
    $subCodes  = subCode::all()->where('class',Auth::user()->grade);

    $exams = Exam::all()->where('id',$id);

    return view('student.exams.fileExam', compact('exams','subCodes','id'));
}


public function deleteStuExamWroks($id,$examId){
  try{

          $record = studentExamWorks::find($id);

          $record->delete($record->id);

          return redirect('student/exams/attemptExam/'.$examId)->with('delete','File deleted successfully');
      }

      catch(Exception $e){
          return redirect('student/exams/attemptExam/'.$examId)->with('failed',"operation failed");
      }
}

      public function submittedDone($id){
        try{
          $getClassSubs = DB::select('SELECT * FROM exams WHERE id = ?' , [$id]);
          //  dd($getClassSub->class);
            foreach ($getClassSubs as $getClassSub) {
                $teacherEmail = $getClassSub->email;
                $examId = $getClassSub->id;
                $class = $getClassSub->class;
                $subject = $getClassSub->subject;
                $title = $getClassSub->title;
                $maxMarks = $getClassSub->maxMarks;
            }

            $stuExam = new studentExams;
            $stuExam->titleId = $examId;
            $stuExam->class = $class;
            $stuExam->name = Auth::user()->name;
            $stuExam->admission_number = Auth::user()->admission_number;
            $stuExam->teacherEmail = $teacherEmail;
            $stuExam->subject = $subject;
            $stuExam->title = $title;
            $stuExam->submittedDone = 1;
            $stuExam->maxMarks = $maxMarks;
                     $stuExam->save();

                return redirect('student/exams/attemptExam/'.$examId)->with('status','Final Submitted Done');
            }

            catch(Exception $e){
                return redirect('student/exams/attemptExam/'.$examId)->with('failed',"operation failed");
            }
      }

      public function showResult($studentId, $examId)
        {
            // example data extraction (adjust to your models)
            $student = Student::findOrFail($studentId);
            $exam = Exam::findOrFail($examId);

            // subjects in the exam/class
            $subjects = Subject::whereIn('id', $exam->subject_ids ?? [])->get()->map(function($s){
                return ['id' => $s->id, 'name' => $s->name];
            })->toArray();

            // example marks shape: marks['term1'][subjectId] = [...]
            $marks = [
                'term1' => [],
                'term2' => []
            ];

            // Replace this with real queries to marks table
            foreach ($subjects as $sub) {
                $marks['term1'][$sub['id']] = [
                    'per_test' => 6,
                    'notebook' => 5,
                    'enrich' => 4,
                    'exam' => 46,
                    'total' => 61,
                    'grade' => 'B2'
                ];
                $marks['term2'][$sub['id']] = [
                    'per_test' => 6,
                    'notebook' => 5,
                    'enrich' => 5,
                    'exam' => 51,
                    'total' => 67,
                    'grade' => 'B1'
                ];
            }

            $co_scholastic = [
                ['name' => 'Computer', 'term1' => 'A', 'term2' => 'A'],
                ['name' => 'Art Education', 'term1' => 'A', 'term2' => 'A'],
                ['name' => 'Health & Physical Education', 'term1' => 'A', 'term2' => 'A']
            ];

            $data = [
                'school' => ['name'=>'Your School Name','address'=>'Address Line','place'=>'Haridwar'],
                'session' => '2024 - 25',
                'student' => [
                    'id' => $student->id,
                    'name' => $student->name,
                    'father' => $student->father_name,
                    'mother' => $student->mother_name,
                    'admission_number' => $student->admission_number,
                    'dob' => $student->date_of_birth,
                    'class' => $student->class_name
                ],
                'subjects' => $subjects,
                'marks' => $marks,
                'co_scholastic' => $co_scholastic,
                'grand_total' => 842,
                'grand_total_max' => 1400,
                'aggregate_percent' => 60.14,
                'teacher_remarks' => 'Follows classroom rules consistently. He has a positive attitude and is a joy to teach.',
                'promote_to' => 'Vth',
                'attendance_total' => 208,
                'attendance_present' => 95,
                'report_date' => '25-03-2025'
            ];

            return view('reports.student_result', $data);
        }

}
