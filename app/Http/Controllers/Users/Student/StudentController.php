<?php

namespace App\Http\Controllers\Users\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\subCode;
use App\classwork;
use App\Exam;
use App\Term;
use App\stuHomeworkUpload;
use App\liveClassAttendence;
use Auth;
use Illuminate\Support\Facades\Validator;
use DB;
use Illuminate\Support\Facades\Storage;
use App\Notifications\emailNotification;
use App\studentExams;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\ResultPerforma;
use App\ResultTerm;
use App\ResultPerformaItem;
use Exception;
use App\Attendance;
use App\flashNews;



class StudentController extends Controller
{

  public function __construct()
  {
      $this->middleware('auth:web')->except('index','attendanceMonthly','profile','announcements','resetPasswordApi');
  }

  public function index(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    "ok" => false,
                    "message" => "Unauthorized",
                ], 401);
            }

            $grade = $user->grade ?? null;

            if (!$grade) {
                return response()->json([
                    "ok" => false,
                    "message" => "Student grade not found",
                ], 422);
            }

            $classworks = classwork::where("class", $grade)
                ->where(function ($q) {
                    $q->whereNull("title")
                      ->orWhereRaw("LOWER(TRIM(title)) != ?", ["topic"]);
                })
                ->orderByDesc("created_at")
                ->limit(30)
                ->get();

            $items = $classworks->map(function ($cw) {
                $createdAt = null;
                $dateLabel = "Unknown";

                if (!empty($cw->created_at)) {
                    try {
                        $parsed = Carbon::parse($cw->created_at);
                        $createdAt = $parsed->toDateTimeString();
                        $dateLabel = $parsed->format("d M Y");
                    } catch (\Throwable $e) {
                        $createdAt = (string) $cw->created_at;
                    }
                }

                return [
                    "id" => $cw->id,
                    "class" => $cw->class,
                    "subject" => $cw->subject ?? "NA",
                    "title" => $cw->title ?? "NA",
                    "note" => $cw->discription ?? "NA",
                    "category" => $cw->type ?? "Other",
                    "fileUrl" => $cw->fileUrl,
                    "fileSize" => $cw->fileSize,
                    "youtubeLink" => $cw->youtubeLink,
                    "teacherName" => $cw->name,
                    "studentReturn" => (int) ($cw->studentReturn ?? 0),
                    "createdAt" => $createdAt,
                    "dateLabel" => $dateLabel,
                ];
            });

            $dateWise = $items
                ->groupBy("dateLabel")
                ->map(function ($group, $date) {
                    return [
                        "date" => $date,
                        "items" => $group->values(),
                    ];
                })
                ->values();

            $subjectWise = $items
                ->groupBy("subject")
                ->map(function ($group, $subject) {
                    return [
                        "subject" => $subject,
                        "items" => $group->values(),
                    ];
                })
                ->values();

            return response()->json([
                "ok" => true,
                "total" => $items->count(),
                "dateWise" => $dateWise,
                "subjectWise" => $subjectWise,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                "ok" => false,
                "message" => "Homework API failed",
                "error" => $e->getMessage(),
                "line" => $e->getLine(),
            ], 500);
        }
    }

  public function attendanceMonthly(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    "ok" => false,
                    "message" => "Unauthenticated",
                ], 401);
            }

            $year = (int) ($request->query("year") ?? Carbon::now()->year);
            $month = (int) ($request->query("month") ?? Carbon::now()->month);

            if ($month < 1 || $month > 12) {
                return response()->json([
                    "ok" => false,
                    "message" => "Invalid month",
                ], 422);
            }

            $start = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $end = $start->copy()->endOfMonth();

            $rows = Attendance::where("student_id", $user->id)
                ->whereDate("date", ">=", $start->toDateString())
                ->whereDate("date", "<=", $end->toDateString())
                ->get();

            $dayStatuses = [];
            foreach ($rows as $row) {
                $day = Carbon::parse($row->date)->day;
                $dayStatuses[(string) $day] = $row->status;
            }

            $present = $rows->where("status", "P")->count();
            $absent = $rows->where("status", "A")->count();
            $leave = $rows->where("status", "L")->count();
            $totalMarked = $present + $absent + $leave;
            $percentage = $totalMarked > 0
                ? round(($present / $totalMarked) * 100, 2)
                : 0;

            $overallRows = Attendance::where("student_id", $user->id)->get();
            $overallPresent = $overallRows->where("status", "P")->count();
            $overallAbsent = $overallRows->where("status", "A")->count();
            $overallLeave = $overallRows->where("status", "L")->count();
            $overallTotalMarked = $overallPresent + $overallAbsent + $overallLeave;
            $overallPercentage = $overallTotalMarked > 0
                ? round(($overallPresent / $overallTotalMarked) * 100, 2)
                : 0;

            return response()->json([
                "ok" => true,
                "month" => $month,
                "year" => $year,
                "summary" => [
                    "present" => $present,
                    "absent" => $absent,
                    "leave" => $leave,
                    "totalMarked" => $totalMarked,
                    "percentage" => $percentage,
                ],
                "overallSummary" => [
                    "present" => $overallPresent,
                    "absent" => $overallAbsent,
                    "leave" => $overallLeave,
                    "totalMarked" => $overallTotalMarked,
                    "percentage" => $overallPercentage,
                ],
                "dayStatuses" => $dayStatuses,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                "ok" => false,
                "message" => "Attendance API failed",
                "error" => $e->getMessage(),
                "line" => $e->getLine(),
            ], 500);
        }
    }

  public function profile(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    "ok" => false,
                    "message" => "Unauthenticated",
                ], 401);
            }

            $studentClass = trim((string) ($user->grade ?? ""));
            $section = trim((string) ($user->section ?? ""));
            $classLabel = trim($studentClass);

            return response()->json([
                "ok" => true,
                "data" => [
                    "name" => $user->name,
                    "class" => $classLabel,
                    "section" => $section,
                    "admission_number" => (string) ($user->admission_number ?? ""),
                    "studentDetails" => [
                        "date_of_admission" => $user->created_at
                            ? Carbon::parse($user->created_at)->format("d-m-Y")
                            : null,
                        "dob" => $user->dob
                            ? Carbon::parse($user->dob)->format("d-m-Y")
                            : null,
                        "roll_number" => $user->rollNo,
                        "aadhar_number" => $user->aadhar,
                        "pen_number" => $user->pen,
                        "apaar_id" => $user->apaar,
                        "gender" => $user->gender,
                        "student_type" => $user->category_id ? "CATEGORY " . $user->category_id : null,
                        "blood_group" => null,
                        "email" => $user->email,
                        "mobile_number" => $user->mobile,
                        "mode_of_transport" => optional($user->route)->name,
                        "house" => $user->house,
                        "class_teacher" => null,
                        "teacher_contact" => null,
                    ],
                    "parentDetails" => [
                        "father_name" => $user->fName,
                        "father_mobile" => $user->mobile,
                        "father_email" => $user->email,
                        "mother_name" => $user->mName,
                        "mother_mobile" => null,
                        "mother_email" => null,
                    ],
                    "addressDetails" => [
                        "permanent_address" => $user->address,
                    ],
                ],
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                "ok" => false,
                "message" => "Profile API failed",
                "error" => $e->getMessage(),
                "line" => $e->getLine(),
            ], 500);
        }
    }

  public function announcements(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    "ok" => false,
                    "message" => "Unauthenticated",
                ], 401);
            }

            $query = flashNews::orderByDesc("created_at");
            $all = (string) $request->query("all", "0");
            if ($all !== "1") {
                $query->limit(3);
            }
            $rows = $query->get();

            $items = $rows->map(function ($row) {
                return [
                    "id" => $row->id,
                    "news" => (string) ($row->news ?? ""),
                    "createdAt" => $row->created_at
                        ? Carbon::parse($row->created_at)->toDateTimeString()
                        : null,
                    "dateLabel" => $row->created_at
                        ? Carbon::parse($row->created_at)->format("d M Y")
                        : "",
                ];
            })->values();

            return response()->json([
                "ok" => true,
                "total" => $items->count(),
                "announcements" => $items,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                "ok" => false,
                "message" => "Announcements API failed",
                "error" => $e->getMessage(),
                "line" => $e->getLine(),
            ], 500);
        }
    }

  public function resetPasswordApi(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    "ok" => false,
                    "message" => "Unauthenticated",
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'new_password' => ['required', 'string', 'min:6', 'confirmed'],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "ok" => false,
                    "message" => $validator->errors()->first(),
                    "errors" => $validator->errors(),
                ], 422);
            }

            $user->password = Hash::make($request->new_password);
            $user->save();

            return response()->json([
                "ok" => true,
                "message" => "Password updated successfully",
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                "ok" => false,
                "message" => "Reset password failed",
                "error" => $e->getMessage(),
                "line" => $e->getLine(),
            ], 500);
        }
    }
  public function create(User $student)
    {
        // 1️⃣ Fetch default performa for student's class
        $performa = ResultPerforma::where('class', $student->class)
            ->where('is_default', 1)
            ->firstOrFail();

        // 2️⃣ Load terms with components
        $terms = ResultTerm::with('components')
            ->where('performa_id', $performa->id)
            ->orderBy('order_no')
            ->get();

        // 3️⃣ Load subjects (from performa items)
        $subjects = ResultPerformaItem::with('subCode')
            ->where('performa_id', $performa->id)
            ->where('is_included', 1)
            ->orderBy('subject_order')
            ->get();

        // 4️⃣ Co-Scholastic areas (config-driven)
        $coScholasticAreas = [
            'Music & Dance',
            'Art Education',
            'Health & Physical Education',
            'Discipline',
        ];

        // 5️⃣ Default term (optional: first term)
        $term = $terms->first();

        return view(
            'results.student_result_entry',
            compact(
                'student',
                'performa',
                'terms',
                'subjects',
                'coScholasticAreas',
                'term'
            )
        );
    }





  public function studentSelfUpdatePassword()
  {

    $user = Auth::user();

          return view('student.studentSelf-update-password', compact('user'));
  }

  public function postStudentSelfUpdatePassword(Request $request)
    {
        $request->validate([
            'new_password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
        $data = $request->all();
        $user = Auth::user();
        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password updated successfully');
    }

    public function classroom_id($id){
      $subCodes  = subCode::all()->where('class',Auth::user()->grade);
      $classCodes = subCode::all()->sortBy("class");
      $subCode = subCode::all()->where('id',$id);
      foreach($subCode as $classSub){
          $subject =  $classSub->subject;
          $class  =   $classSub->class;
      }

      $classDatas = classwork::all()->where('class',$class)->where('subject',$subject)->sortByDesc('created_at');
      //dd($classDatas);
      return view('student.classroom', compact('classCodes','subCode','classDatas','class','subject','subCodes'));

    }


    public function editClassroom_id($topicId){

      $subCodes  = subCode::all()->where('class',Auth::user()->grade);
      $DBtopics = classwork::all()->where('id',$topicId)->sortByDesc('created_at');
      foreach($DBtopics as $topic){
        $title = $topic->title;
        $teacherName = $topic->name;
        $subject= $topic->subject;
      }
      $DBtitles = classwork::all()->where('title',$title)->sortByDesc('created_at');

      return view('student.inner_classroom', compact('DBtitles','title','teacherName','subject','subCodes'));

    }


    public function inner_classroom_id($topicId){

      $subCodes  = subCode::all()->where('class',Auth::user()->grade);
      $DBtopics = classwork::all()->where('id',$topicId)->sortByDesc('created_at');
      foreach($DBtopics as $topic){
        $title = $topic->title;
        $teacherName = $topic->name;
        $subject= $topic->subject;
        $class= $topic->class;
      }
      $DBtitles = classwork::all()->where('class',$class)->where('subject',$subject)->where('title',$title)->sortByDesc('created_at');

      return view('student.inner_classroom', compact('DBtitles','title','teacherName','subject','subCodes'));

    }

    public function homework($topicId){
     foreach(Auth::user()->unreadNotifications as $notification){
       if($notification->data['classworkId']==$topicId && $notification->data['workType']=='Classwork'){
         $notification->markAsRead();
       }
     }


      $subCodes  = subCode::all()->where('class',Auth::user()->grade);
      $DBtopics = classwork::all()->where('id',$topicId);
      foreach($DBtopics as $topic){
        $id = $topic->id;
        $title = $topic->title;
        $teacherName = $topic->name;
        $subject= $topic->subject;
        $fileUrl = $topic->fileUrl;
        $filename=basename($topic->fileUrl);
        $fileSizes=intval(($topic->fileSize)/1000);
        $studentReturn	=  $topic->studentReturn;
      }
      $DBtitles = classwork::all()->where('title',$title)->sortByDesc('created_at');
      $stuHomeworkUploads = stuHomeworkUpload::all()->where('titleId',$id)->where('admission_number',Auth::user()->admission_number);

      return view('student.homework', compact('id','DBtitles','title','teacherName','subject','subCodes','fileUrl','stuHomeworkUploads','filename','fileSizes','studentReturn'));

    }

    public function stuUploadFile(Request $request){
      $data = $request->input();
      $id = $data['id'];
    try{
        $getClassSubs = DB::select('SELECT * FROM classworks WHERE id = ?' , [$data['id']]);

        foreach ($getClassSubs as $getClassSub) {
       //   dd($getClassSub);
            $class = $getClassSub->class;
            $subject = $getClassSub->subject;
            $title = $getClassSub->title;

          }

        $stuWork = new stuHomeworkUpload;
        $stuWork->titleId = $data['id'];
        $stuWork->class = $class;
        $stuWork->name = Auth::user()->name;
        $stuWork->admission_number = Auth::user()->admission_number;
        $stuWork->subject = $subject;
        $stuWork->title = $title;
        $userName = Auth::user()->name;
        $fileUrl = 'https://gplmschool-dev-storage-vkmjgjn4dvol.s3.amazonaws.com/' . $class . '/' . $subject . '/' . $title . '/' . $userName . '/' . $request->file->getClientOriginalName();
        $stuWork->fileUrl = $fileUrl;
        $stuWork->fileSize = $request->file('file')->getSize();
                 $stuWork->save();

        $file = $request->file('file');
        $imageName = $class . '/' . $subject . '/' . $title . '/' . $userName . '/' .  $file->getClientOriginalName();

        Storage::disk('s3')->put($imageName, file_get_contents($file));
        Storage::disk('s3')->setVisibility($imageName, 'public');

       // request()->user()->notify(new emailNotification($class,$subject,$title));

        return redirect('student/homework/'.$id)->with('status','File uploaded successfully');
    }
    catch(Exception $e){
        return redirect('student/homework/'.$id)->with('failed',"operation failed");
    }
}

public function deleteStuUploadFile($id,$topicId){
  try{

          $record = stuHomeworkUpload::find($id);

          $record->delete($record->id);

          return redirect('student/homework/'.$topicId)->with('delete','File deleted successfully');
      }

      catch(Exception $e){
          return redirect('student/homework/'.$topicId)->with('failed',"operation failed");
      }
}



    public function liveClass(){
        $subCodes  = subCode::all()->where('class',Auth::user()->grade);
        return view('student.liveClass', compact('subCodes'));
    }

    public function liveAttendence($id){
      $subCodes  = subCode::all()->where('class',Auth::user()->grade);
      $rows =  DB::select('SELECT * FROM sub_codes WHERE id = ?' , [$id]);
      foreach($rows as $row){
        $link = $row->link_url;
        $class = $row->class;
        $subject = $row->subject;
      }

      $liveClassAttendence = new liveClassAttendence;
      $liveClassAttendence->type = 'STUDENT';
      $liveClassAttendence->name = Auth::user()->name;
      $liveClassAttendence->admission_number = Auth::user()->admission_number;
      $liveClassAttendence->class =$class;
      $liveClassAttendence->subject = $subject;

          $liveClassAttendence->save();


      return redirect($link);

    }


    public function notificationClasswork($id,$notificationId){
        $user = Auth::user();

        foreach ($user->unreadNotifications as $notification) {
          Auth::user()->notifications->find($notificationId)->markAsRead();
        }
        return redirect('student/homework/'.$id);
          }

    public function notificationExam($id,$notificationId){
        $user = Auth::user();

        foreach ($user->unreadNotifications as $notification) {
          Auth::user()->notifications->find($notificationId)->markAsRead();
        }
        return redirect('/student/exams/upcomingExams');
    }

    public function results(){
      $results  = studentExams::all()->where('admission_number',Auth::user()->admission_number)->sortByDesc('created_at');
      $toppers =    studentExams::all()->where('class',Auth::user()->grade)->sortByDesc('marksObtain');
      $exams = Exam::all();
      return view('student.results', compact('results','toppers','exams'));
  }


}
