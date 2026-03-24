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
use App\Admin;


class teacherExamController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:teacher');
    }

    public function createExam(){
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

        $classworks  = classwork::all()->sortBy("class");
        return view('teacher.createExam', compact('subCodes','classCodes','classworks'));
    }


    public function editExam($id){
        $exams = Exam::all()->where('id',$id);

        foreach($exams as $exam){
            $id = $exam->id;
            $class = $exam->class;
            $subject = $exam->subject;
            $title = $exam->title;
            $discription = $exam->discription;
            $startExam = $exam->startExam;
            $endExam = $exam->endExam;
            $maxMarks = $exam->maxMarks;
        }
        return view('teacher.editExam', compact('id','class','subject','title','discription','startExam','endExam','maxMarks'));
    }

    public function postEditExam(Request $request ){
        $data = $request->input();
        $id = $data['id'];

        $daterange = $data['datetimes'];
        $split = explode('-', $daterange);
        $startExam = date("Y-m-d H:i:s ", strtotime($split[0]));
        $endExam = date("Y-m-d H:i:s ", strtotime($split[1]));

        try{
          DB::table('exams')
            ->where('id', $id)
            ->update([  'startExam' => $startExam,
                        'endExam' => $endExam,
                        'maxMarks' => $data['maxMarks'],
                        ]);
            return redirect('teacher/allExams')->with('status','Exam edited successfully');
        }
        catch(Exception $e){
            return redirect('teacher/allExams')->with('failed',"operation failed");
        }
    }

    public function deleteExam($id){
        try{
            $record = Exam::find($id);

            $record->delete($record->id);

            return redirect('teacher/allExams')->with('delete','Record deleted successfully');
        }
        catch(Exception $e){
            return redirect('teacher/createTitle/'.$id)->with('failed',"operation failed");

        }

    }


    public function allExams(){
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

        $classworks  = classwork::all()->sortBy("class");

        $exams = Exam::all()->where('email',Auth::user()->email);

        return view('teacher.allExams', compact('subCodes','classCodes','classworks','exams'));
    }


    public function pdfExam(Request $request)
    {
        $rules = [
			'title' => 'required', 'string', 'max:255',

		];
		$validator = Validator::make($request->all(),$rules);
		if ($validator->fails()) {
		echo "validator fail";
		}
		else{

            $data = $request->input();

            $daterange = $data['datetimes'];
            $split = explode('-', $daterange);
            $startExam = date("Y-m-d H:i:s ", strtotime($split[0]));
            $endExam = date("Y-m-d H:i:s ", strtotime($split[1]));
            //dd($startExam);
			try{
                $getClassSubs = DB::select('SELECT * FROM sub_codes WHERE id = ?' , [$data['grade']]);
              //  dd($getClassSub->class);
                foreach ($getClassSubs as $getClassSub) {
                    $class = $getClassSub->class;
                    $subject = $getClassSub->subject;
                }
                $exam = new exam;
                $exam->name = Auth::guard('teacher')->user()->name;
                $exam->email = Auth::guard('teacher')->user()->email;
                $exam->title = $data['title'];
                $exam->discription = $data['discription'];
                $exam->subject = $subject;
                $exam->class = $class;
                $exam->fileUrl = 'https://gplmschool-dev-storage-vkmjgjn4dvol.s3.amazonaws.com/' . $class . '/' . $subject . '/' . 'exams' . '/' . $data['title'] . '/' . $request->file->getClientOriginalName();
                $exam->fileSize = $request->file('file')->getSize();
                $exam->startExam = $startExam;
                $exam->endExam = $endExam;
                $exam->maxMarks = $data['demo0'];
                $exam->studentReturn = 1;
                $exam->type = 'PDF';
                $exam->save();


                $file = $request->file('file');
                $imageName = $class . '/' . $subject . '/' . 'exams' . '/' . $data['title'] . '/' .  $file->getClientOriginalName();

                Storage::disk('s3')->put($imageName, file_get_contents($file));
                Storage::disk('s3')->setVisibility($imageName, 'public');

                $title = $data['title'];
                $examId = $exam->id;
                $type = "PDF";
                $workType = "Exam";
                //   User::where('email','bali4u2001@gmail.com') -> first()->notify(new emailNotification);
                //    User::all()->where('grade',$class)->each(function (User $user) use ($workType,$examId,$class,$subject,$title,$type){
                //        $user->notify(new emailNotification($workType,$examId,$class,$subject,$title,$type));
                //    });
                //    Admin::all()->each(function (Admin $user) use ($workType,$examId,$class,$subject,$title,$type){
                //     $admin->notify(new emailNotification($workType,$examId,$class,$subject,$title,$type));
                // });

				return redirect('teacher/createExam')->with('status','Insert successfully');
			}
			catch(Exception $e){
				return redirect('teacher/createExam')->with('failed',"operation failed");
			}
		}
    }

    public function imageExam(Request $request)
    {
        $rules = [
			'imgTitle' => 'required', 'string', 'max:255',

		];
		$validator = Validator::make($request->all(),$rules);
		if ($validator->fails()) {
		echo "validator fail";
		}
		else{

            $data = $request->input();

            $daterange = $data['datetimes'];
            $split = explode('-', $daterange);
            $startExam = date("Y-m-d H:i:s ", strtotime($split[0]));
            $endExam = date("Y-m-d H:i:s ", strtotime($split[1]));

			try{
                $getClassSubs = DB::select('SELECT * FROM sub_codes WHERE id = ?' , [$data['imgGrade']]);
              //  dd($getClassSub->class);
                foreach ($getClassSubs as $getClassSub) {
                    $class = $getClassSub->class;
                    $subject = $getClassSub->subject;
                }
                $exam = new Exam;
                $exam->name = Auth::guard('teacher')->user()->name;
                $exam->email = Auth::guard('teacher')->user()->email;
                $exam->title = $data['imgTitle'];
                $exam->discription = $data['imgDiscription'];
                $exam->subject = $subject;
                $exam->class = $class;
                $exam->fileUrl = 'https://gplmschool-dev-storage-vkmjgjn4dvol.s3.amazonaws.com/' . $class . '/' . $subject . '/' . 'exams' . '/' . $data['imgTitle'] . '/' . $request->file->getClientOriginalName();
                $exam->fileSize = $request->file('file')->getSize();
                $exam->startExam = $startExam;
                $exam->endExam = $endExam;
                $exam->maxMarks = $data['demo0'];
                $exam->studentReturn = 1;
                $exam->type = 'IMG';
                $exam->save();


                $file = $request->file('file');
                $imageName = $class . '/' . $subject . '/' . 'exams' . '/' . $data['imgTitle'] . '/' .  $file->getClientOriginalName();

                Storage::disk('s3')->put($imageName, file_get_contents($file));
                Storage::disk('s3')->setVisibility($imageName, 'public');

                $title = $data['imgTitle'];
                $examId = $exam->id;
                $type = "IMG";
                $workType = "Exam";

                //   User::where('email','bali4u2001@gmail.com') -> first()->notify(new emailNotification);
                //    User::all()->where('grade',$class)->each(function (User $user) use ($workType,$examId,$class,$subject,$title,$type){
                //        $user->notify(new emailNotification($workType,$examId,$class,$subject,$title,$type));
                //    });
                //    Admin::all()->each(function (Admin $user) use ($workType,$examId,$class,$subject,$title,$type){
                //     $admin->notify(new emailNotification($workType,$examId,$class,$subject,$title,$type));
                // });

				return redirect('teacher/createExam')->with('status','Insert successfully');
			}
			catch(Exception $e){
				return redirect('teacher/createExam')->with('failed',"operation failed");
			}
		}
    }

    public function docsExam(Request $request)
    {
        $rules = [
			'docTitle' => 'required', 'string', 'max:255',

		];
		$validator = Validator::make($request->all(),$rules);
		if ($validator->fails()) {
		echo "validator fail";
		}
		else{

            $data = $request->input();

            $daterange = $data['datetimes'];
            $split = explode('-', $daterange);
            $startExam = date("Y-m-d H:i:s ", strtotime($split[0]));
            $endExam = date("Y-m-d H:i:s ", strtotime($split[1]));

			try{
                $getClassSubs = DB::select('SELECT * FROM sub_codes WHERE id = ?' , [$data['docGrade']]);
              // dd($data['docGrade']);
                foreach ($getClassSubs as $getClassSub) {
                    $class = $getClassSub->class;
                    $subject = $getClassSub->subject;
                }
                $exam = new Exam;
                $exam->name = Auth::guard('teacher')->user()->name;
                $exam->email = Auth::guard('teacher')->user()->email;
                $exam->title = $data['docTitle'];
                $exam->discription = $data['docDiscription'];
                $exam->subject = $subject;
                $exam->class = $class;
                $exam->fileUrl = 'https://gplmschool-dev-storage-vkmjgjn4dvol.s3.amazonaws.com/' . $class . '/' . $subject . '/' . 'exams' . '/' . $data['docTitle'] . '/' . $request->file->getClientOriginalName();
                $exam->fileSize = $request->file('file')->getSize();
                $exam->startExam = $startExam;
                $exam->endExam = $endExam;
                $exam->maxMarks = $data['demo0'];
                $exam->studentReturn = 1;
                $exam->type = 'DOCS';
                $exam->save();

                $file = $request->file('file');
                $imageName = $class . '/' . $subject . '/' . 'exams' . '/' . $data['docTitle'] . '/' .  $file->getClientOriginalName();

                Storage::disk('s3')->put($imageName, file_get_contents($file));
                Storage::disk('s3')->setVisibility($imageName, 'public');

                $title = $data['docTitle'];
                $examId = $exam->id;
                $type = "DOCS";
                $workType = "Exam";
                //   User::where('email','bali4u2001@gmail.com') -> first()->notify(new emailNotification);
                //    User::all()->where('grade',$class)->each(function (User $user) use ($workType,$examId,$class,$subject,$title,$type){
                //        $user->notify(new emailNotification($workType,$examId,$class,$subject,$title,$type));
                //    });
                //    Admin::all()->each(function (Admin $user) use ($workType,$examId,$class,$subject,$title,$type){
                //     $admin->notify(new emailNotification($workType,$examId,$class,$subject,$title,$type));
                // });

                return redirect('teacher/createExam')->with('status','Insert successfully');
			}
			catch(Exception $e){
				return redirect('teacher/createExam')->with('failed',"operation failed");
			}
		}
    }

    public function formLink(Request $request)
    {
        $rules = [
			'formTitle' => 'required', 'string', 'max:255',

		];
		$validator = Validator::make($request->all(),$rules);
		if ($validator->fails()) {
		echo "validator fail";
		}
		else{

            $data = $request->input();

            $daterange = $data['datetimes'];
            $split = explode('-', $daterange);
            $startExam = date("Y-m-d H:i:s ", strtotime($split[0]));
            $endExam = date("Y-m-d H:i:s ", strtotime($split[1]));

			try{
                $getClassSubs = DB::select('SELECT * FROM sub_codes WHERE id = ?' , [$data['formGrade']]);
              // dd($data['ytGrade']);
                foreach ($getClassSubs as $getClassSub) {
                    $class = $getClassSub->class;
                    $subject = $getClassSub->subject;
                }
                $exam = new Exam;
                $exam->name = Auth::guard('teacher')->user()->name;
                $exam->email = Auth::guard('teacher')->user()->email;
                $exam->title = $data['formTitle'];
                $exam->discription = $data['formDiscription'];
                $exam->subject = $subject;
                $exam->class = $class;
                $exam->examUrl = $data['formLink'];
                $exam->startExam = $startExam;
                $exam->endExam = $endExam;
                $exam->maxMarks = $data['demo0'];
                if(isset($data['formStudentWorkIsrequire'])){
                    $exam->studentReturn = 1;
                    }
                $exam->type = 'FORM';
                $exam->save();

                $title = $data['formTitle'];
                $examId = $exam->id;
                $type = "FORM";
                $workType = "Exam";
                //   User::where('email','bali4u2001@gmail.com') -> first()->notify(new emailNotification);
                //    User::all()->where('grade',$class)->each(function (User $user) use ($workType,$examId,$class,$subject,$title,$type){
                //        $user->notify(new emailNotification($workType,$examId,$class,$subject,$title,$type));
                //    });
                //    Admin::all()->each(function (Admin $admin) use ($workType,$examId,$class,$subject,$title,$type){
                //     $admin->notify(new emailNotification($workType,$examId,$class,$subject,$title,$type));
                // });

                return redirect('teacher/createExam')->with('status','Insert successfully');
			}
			catch(Exception $e){
				return redirect('teacher/createExam')->with('failed',"operation failed");
			}
		}
    }

    public function formExam($id){
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

        foreach($exams as $exam){
        if($exam->type!="FORM"){
            return view('teacher.allExams')->with('failed',"Wrong Selection,Select only form. ");
        }
            $id = $exam->id;
            $class = $exam->class;
            $subject = $exam->subject;
            $title = $exam->title;
            $discription = $exam->discription;
            $startExam = $exam->startExam;
            $endExam = $exam->endExam;
            $maxMarks = $exam->maxMarks;
        }
        return view('teacher.formExam', compact('id','exams','subCodes','classCodes'));
    }


}
