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
use App\flashNews;
use App\stuHomeworkUpload;
use App\liveClassAttendence;
use Illuminate\Support\Facades\Hash;
use App\Category;
use App\RouteName;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\routeFeePlan;
use App\Concession;
use App\FeePlan;
use App\FeeHead;
use App\studentExams;

class TeacherController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:teacher');

    }



    public function teacherSelfUpdatePassword()
    {
        $user = Auth::user();
        return view('teacher.teacherSelf-update-password', compact('user'));
    }

    public function postTeacherSelfUpdatePassword(Request $request)
    {
        $request->validate([
            'new_password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
        $user = Auth::user();
        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password updated successfully');
    }

    public function index()
    {
        $subCodes = $this->getTeachersubCodes();
        $classCodes = $this->getAllClassCodes();
        $flashNews = flashNews::all()->sortByDesc('created_at');

        $classworks  = classwork::all()->where('email',Auth::user()->email)->sortByDesc('created_at');
        $exams = Exam::all()->where('email',Auth::user()->email)->sortByDesc('created_at');

        return view('/teacher/dashboard', compact('subCodes','classworks','exams','classCodes','flashNews'));
    }

    public function liveClass()
    {
        $subCodes = $this->getTeachersubCodes();
        $classCodes = $this->getAllClassCodes();

        return view('teacher.liveClass', compact('subCodes','classCodes'));
    }

    public function addMaterial($id)
    {
        $subCodes = $this->getTeachersubCodes();
        $classCodes = $this->getAllClassCodes();
        $subCode = subCode::all()->where('id',$id);
        $terms = Term::all()->sortBy("term");

        foreach($subCode as $classSub){
            $subject =  $classSub->subject;
            $class  =   $classSub->class;
        }

        $classDatas = classwork::all()->where('class',$class)->where('subject',$subject)->sortByDesc('created_at');
        $classworks  = classwork::all()->sortBy("class");

        return view('teacher.addMaterial', compact('subCodes','classCodes','classworks','subCode','classDatas','class','subject','id','terms'));
    }

    public function edit_classwork(Request $request, $id )
    {
        $subCodes = $this->getTeachersubCodes();
        $classCodes = $this->getAllClassCodes();
        $terms = Term::all()->sortBy("term");

        $classworks  = classwork::all()->WHERE('id',$id);

        foreach($classworks as $classwork){
            $class = $classwork->class;
            $subject = $classwork->subject;
            $title = $classwork->title;
            $type = $classwork->type;
            $youtubeLink = $classwork->youtubeLink;
            $studentReturn = $classwork->studentReturn;
        }
        $classDatas = classwork::all()->where('class',$class)->where('subject',$subject)->sortByDesc('created_at');

        $subIds = subCode::all()->where('class',$class)->where('subject',$subject);
        $teacherCode=false;
        $checkId = null;
        $subId = null;

        foreach($subIds as $forSubId){
            $checkId = $forSubId->id;
            $subId = $forSubId->id;
        }

        foreach($subCodes as $subCode){
            if($checkId == $subCode){
                $teacherCode = true;
            }
        }

        if($teacherCode==true){
            return view('teacher.edit_classwork', compact('subCodes','classCodes', 'classDatas','class','subject','title','id','terms','type','youtubeLink','studentReturn','subId'));
        } else {
            return redirect('teacher/inner_classroom/'.$id)->with('failed',"operation failed");
        }
    }

    public function pdfClasswork(Request $request)
    {
        $data = $request->input();
        $id = $data['id'];
        $term = $data['selectTerm'];
        if(!(isset($data['selectTitle']))){
            return redirect('teacher/addMaterial/'.$id)->with('failed',"Try again, Please select title");
        }
        $title = $data['selectTitle'];

        try{
            $getClassSubs = DB::select('SELECT * FROM sub_codes WHERE id = ?' , [$data['id']]);
            foreach ($getClassSubs as $getClassSub) {
                $class = $getClassSub->class;
                $subject = $getClassSub->subject;
            }
            $classwork = new classwork;
            $classwork->term = $term;
            $classwork->name = Auth::guard('teacher')->user()->name;
            $classwork->email = Auth::guard('teacher')->user()->email;
            $classwork->title =$title;
            $classwork->subject = $subject;
            $classwork->class = $class;
            $classwork->fileUrl = 'https://gplmschool-dev-storage-vkmjgjn4dvol.s3.amazonaws.com/' . $class . '/' . $subject . '/' . $title . '/' . $data['fileName'];
            $classwork->fileSize = $request->file('file')->getSize();
            if(isset($data['studentWorkIsrequire'])){
                $classwork->studentReturn = 1;
            }
            $classwork->type = 'PDF';
            $classwork->save();

            $classworkId = $classwork->id;
            $file = $request->file('file');
            $imageName = $class . '/' . $subject . '/' . $title . '/' .  $data['fileName'];

            Storage::disk('s3')->put($imageName, file_get_contents($file));
            Storage::disk('s3')->setVisibility($imageName, 'public');

            $this->sendClassworkUploadNotifications($class, $subject, $title, 'PDF');

            return redirect('teacher/addMaterial/'.$id)->with('status','Insert successfully');
        }
        catch(Exception $e){
            return redirect('teacher/addMaterial/'.$id)->with('failed',"operation failed");
        }
    }

    public function imageClasswork(Request $request)
    {
        $data = $request->input();
        $term = $data['selectTerm'];
        $id = $data['id'];
        if(!(isset($data['selectTitle']))){
            return redirect('teacher/addMaterial/'.$id)->with('failed',"Try again, Please select title");
        }
        $title = $data['selectTitle'];

        try{
            $getClassSubs = DB::select('SELECT * FROM sub_codes WHERE id = ?' , [$data['id']]);
            foreach ($getClassSubs as $getClassSub) {
                $class = $getClassSub->class;
                $subject = $getClassSub->subject;
            }
            $classwork = new classwork;
            $classwork->term = $term;
            $classwork->name = Auth::guard('teacher')->user()->name;
            $classwork->email = Auth::guard('teacher')->user()->email;
            $classwork->title =$title;
            $classwork->subject = $subject;
            $classwork->class = $class;
            $classwork->fileUrl = 'https://gplmschool-dev-storage-vkmjgjn4dvol.s3.amazonaws.com/' . $class . '/' . $subject . '/' . $title . '/' . $data['fileName'];
            $classwork->fileSize = $request->file('file')->getSize();
            if(isset($data['imgStudentWorkIsrequire'])){
                $classwork->studentReturn = 1;
            }
            $classwork->type = 'IMG';
            $classwork->save();

            $file = $request->file('file');
            $imageName = $class . '/' . $subject . '/' . $title . '/' .  $data['fileName'];

            Storage::disk('s3')->put($imageName, file_get_contents($file));
            Storage::disk('s3')->setVisibility($imageName, 'public');

            $classworkId = $classwork->id;
            $this->sendClassworkUploadNotifications($class, $subject, $title, 'IMG');
            return redirect('teacher/addMaterial/'.$id)->with('status','Insert successfully');
        }
        catch(Exception $e){
            return redirect('teacher/addMaterial/'.$id)->with('failed',"operation failed");
        }
    }

    public function docsClasswork(Request $request)
    {
        $data = $request->input();
        $term = $data['selectTerm'];
        $id = $data['id'];
        if(!(isset($data['selectTitle']))){
            return redirect('teacher/addMaterial/'.$id)->with('failed',"Try again, Please select title");
        }
        $title = $data['selectTitle'];

        try{
            $getClassSubs = DB::select('SELECT * FROM sub_codes WHERE id = ?' , [$data['id']]);
            foreach ($getClassSubs as $getClassSub) {
                $class = $getClassSub->class;
                $subject = $getClassSub->subject;
            }
            $classwork = new classwork;
            $classwork->term = $term;
            $classwork->name = Auth::guard('teacher')->user()->name;
            $classwork->email = Auth::guard('teacher')->user()->email;
            $classwork->title =$title;
            $classwork->subject = $subject;
            $classwork->class = $class;
            $classwork->fileUrl = 'https://gplmschool-dev-storage-vkmjgjn4dvol.s3.amazonaws.com/' . $class . '/' . $subject . '/' . $title . '/' . $data['fileName'];
            $classwork->fileSize = $request->file('file')->getSize();
            if(isset($data['docStudentWorkIsrequire'])){
                $classwork->studentReturn = 1;
            }
            $classwork->type = 'DOCS';
            $classwork->save();

            $file = $request->file('file');
            $imageName = $class . '/' . $subject . '/' . $title . '/' .  $data['fileName'];

            Storage::disk('s3')->put($imageName, file_get_contents($file));
            Storage::disk('s3')->setVisibility($imageName, 'public');

            $this->sendClassworkUploadNotifications($class, $subject, $title, 'DOCS');
            return redirect('teacher/addMaterial/'.$id)->with('status','Insert successfully');
        }
        catch(Exception $e){
            return redirect('teacher/addMaterial/'.$id)->with('failed',"operation failed");
        }
    }

    public function youtubeLink(Request $request)
    {
        $data = $request->input();
        $term = $data['selectTerm'];
        $id = $data['id'];
        if(!(isset($data['selectTitle']))){
            return redirect('teacher/addMaterial/'.$id)->with('failed',"Try again, Please select title");
        }
        $title = $data['selectTitle'];

        try{
            $getClassSubs = DB::select('SELECT * FROM sub_codes WHERE id = ?' , [$data['id']]);
            foreach ($getClassSubs as $getClassSub) {
                $class = $getClassSub->class;
                $subject = $getClassSub->subject;
            }
            $classwork = new classwork;
            $classwork->term = $term;
            $classwork->name = Auth::guard('teacher')->user()->name;
            $classwork->email = Auth::guard('teacher')->user()->email;
            $classwork->title = $title;
            $classwork->subject = $subject;
            $classwork->class = $class;
            $classwork->youtubeLink = $data['youtubeLink'];
            if(isset($data['ytStudentWorkIsrequire'])){
                $classwork->studentReturn = 1;
            }
            $classwork->type = 'YOUTUBE';
            $classwork->save();

            $this->sendClassworkUploadNotifications($class, $subject, $title, 'YOUTUBE');
            return redirect('teacher/addMaterial/'.$id)->with('status','Insert successfully');
        }
        catch(Exception $e){
            return redirect('teacher/addMaterial/'.$id)->with('failed',"operation failed");
        }
    }

    public function editPdfClasswork(Request $request)
    {
        $data = $request->input();
        $id = $data['id'];
        $term = $data['selectTerm'];
        if(!(isset($data['selectTitle']))){
            return redirect('teacher/edit_classwork/'.$id)->with('failed',"Try again, Please select title");
        }
        $title = $data['selectTitle'];

        try{
            $getClassSubs = DB::select('SELECT * FROM classworks WHERE id = ?' , [$data['id']]);
            foreach ($getClassSubs as $getClassSub) {
                $class = $getClassSub->class;
                $subject = $getClassSub->subject;
            }
            $studentReturn = isset($data['studentWorkIsrequire']) ? 1 : 0;

            DB::table('classworks')
                ->where('id', $id)
                ->update([
                    'term' => $term,
                    'name' => Auth::guard('teacher')->user()->name,
                    'email' => Auth::guard('teacher')->user()->email,
                    'title' =>  $title,
                    'subject' => $subject,
                    'class' => $class,
                    'fileUrl' => 'https://gplmschool-dev-storage-vkmjgjn4dvol.s3.amazonaws.com/' . $class . '/' . $subject . '/' . $title . '/' . $data['fileName'],
                    'fileSize' => $request->file('file')->getSize(),
                    'studentReturn' => $studentReturn,
                    'type' => 'PDF',
                ]);

            $file = $request->file('file');
            $imageName = $class . '/' . $subject . '/' . $title . '/' .  $data['fileName'];

            Storage::disk('s3')->put($imageName, file_get_contents($file));
            Storage::disk('s3')->setVisibility($imageName, 'public');

            return redirect('teacher/inner_classroom/'.$id)->with('status','Record edited successfully');
        }
        catch(Exception $e){
            return redirect('teacher/inner_classroom/'.$id)->with('failed',"operation failed");
        }
    }

    public function editImageClasswork(Request $request)
    {
        $data = $request->input();
        $id = $data['id'];
        $term = $data['selectTerm'];
        if(!(isset($data['selectTitle']))){
            return redirect('teacher/edit_classwork/'.$id)->with('failed',"Try again, Please select title");
        }
        $title = $data['selectTitle'];

        try{
            $getClassSubs = DB::select('SELECT * FROM classworks WHERE id = ?' , [$data['id']]);
            foreach ($getClassSubs as $getClassSub) {
                $class = $getClassSub->class;
                $subject = $getClassSub->subject;
            }
            $studentReturn = isset($data['imgStudentWorkIsrequire']) ? 1 : 0;

            DB::table('classworks')
                ->where('id', $id)
                ->update([
                    'term' => $term,
                    'name' => Auth::guard('teacher')->user()->name,
                    'email' => Auth::guard('teacher')->user()->email,
                    'title' =>  $title,
                    'subject' => $subject,
                    'class' => $class,
                    'fileUrl' => 'https://gplmschool-dev-storage-vkmjgjn4dvol.s3.amazonaws.com/' . $class . '/' . $subject . '/' . $title . '/' . $data['fileName'],
                    'fileSize' => $request->file('file')->getSize(),
                    'studentReturn' => $studentReturn,
                    'type' => 'IMG',
                ]);

            $file = $request->file('file');
            $imageName = $class . '/' . $subject . '/' . $title . '/' .  $data['fileName'];

            Storage::disk('s3')->put($imageName, file_get_contents($file));
            Storage::disk('s3')->setVisibility($imageName, 'public');

            return redirect('teacher/inner_classroom/'.$id)->with('status','Record edited successfully');
        }
        catch(Exception $e){
            return redirect('teacher/inner_classroom/'.$id)->with('failed',"operation failed");
        }
    }

    public function editDocsClasswork(Request $request)
    {
        $data = $request->input();
        $id = $data['id'];
        $term = $data['selectTerm'];
        if(!(isset($data['selectTitle']))){
            return redirect('teacher/edit_classwork/'.$id)->with('failed',"Try again, Please select title");
        }
        $title = $data['selectTitle'];

        try{
            $getClassSubs = DB::select('SELECT * FROM classworks WHERE id = ?' , [$data['id']]);
            foreach ($getClassSubs as $getClassSub) {
                $class = $getClassSub->class;
                $subject = $getClassSub->subject;
            }
            $studentReturn = isset($data['docStudentWorkIsrequire']) ? 1 : 0;

            DB::table('classworks')
                ->where('id', $id)
                ->update([
                    'term' => $term,
                    'name' => Auth::guard('teacher')->user()->name,
                    'email' => Auth::guard('teacher')->user()->email,
                    'title' =>  $title,
                    'subject' => $subject,
                    'class' => $class,
                    'fileUrl' => 'https://gplmschool-dev-storage-vkmjgjn4dvol.s3.amazonaws.com/' . $class . '/' . $subject . '/' . $title . '/' . $data['fileName'],
                    'fileSize' => $request->file('file')->getSize(),
                    'studentReturn' => $studentReturn,
                    'type' => 'DOCS',
                ]);

            $file = $request->file('file');
            $imageName = $class . '/' . $subject . '/' . $title . '/' .  $data['fileName'];

            Storage::disk('s3')->put($imageName, file_get_contents($file));
            Storage::disk('s3')->setVisibility($imageName, 'public');

            return redirect('teacher/inner_classroom/'.$id)->with('status','Record edited successfully');
        }
        catch(Exception $e){
            return redirect('teacher/inner_classroom/'.$id)->with('failed',"operation failed");
        }
    }

    public function editYoutubeLink(Request $request)
    {
        $data = $request->input();
        $id = $data['id'];
        $term = $data['selectTerm'];
        if(!(isset($data['selectTitle']))){
            return redirect('teacher/edit_classwork/'.$id)->with('failed',"Try again, Please select title");
        }
        $title = $data['selectTitle'];

        try{
            $getClassSubs = DB::select('SELECT * FROM classworks WHERE id = ?' , [$data['id']]);
            foreach ($getClassSubs as $getClassSub) {
                $class = $getClassSub->class;
                $subject = $getClassSub->subject;
            }
            $studentReturn = isset($data['ytStudentWorkIsrequire']) ? 1 : 0;

            DB::table('classworks')
                ->where('id', $id)
                ->update([
                    'term' => $term,
                    'name' => Auth::guard('teacher')->user()->name,
                    'email' => Auth::guard('teacher')->user()->email,
                    'title' =>  $title,
                    'subject' => $subject,
                    'class' => $class,
                    'youtubeLink' => $data['youtubeLink'],
                    'studentReturn' => $studentReturn,
                    'type' => 'YOUTUBE',
                ]);

            return redirect('teacher/inner_classroom/'.$id)->with('status','Record edited successfully');
        }
        catch(Exception $e){
            return redirect('teacher/inner_classroom/'.$id)->with('failed',"operation failed");
        }
    }

    // original classroom (without id) — keep minimal to not change core
    public function classroom()
    {
        $subCodes = $this->getTeachersubCodes();
        $classCodes = $this->getAllClassCodes();
        $classworks  = classwork::all()->sortBy("class");
        $subCode = null; // keep same compact variable name expected by views
        return view('teacher.classroom', compact('subCodes','classCodes','classworks','subCode'));
    }

    public function classroom_id($id)
    {
        $subCodes = $this->getTeachersubCodes();
        $classCodes = $this->getAllClassCodes();
        $subCode = subCode::all()->where('id',$id);
        foreach($subCode as $classSub){
            $subject =  $classSub->subject;
            $class  =   $classSub->class;
        }

        $classDatas = classwork::all()->where('class',$class)->where('subject',$subject)->sortByDesc('created_at');
        $classworks  = classwork::all()->sortBy("class");
        return view('teacher.classroom', compact('subCodes','classCodes','classworks','subCode','classDatas','class','subject','id'));
    }

    public function inner_classroom_id($id)
    {
        $subCodes = $this->getTeachersubCodes();
        $classCodes = $this->getAllClassCodes();

        $DBtopics = classwork::all()->where('id',$id)->sortByDesc('created_at');
        foreach($DBtopics as $topic){
          $title = $topic->title;
          $teacherName = $topic->name;
          $subject= $topic->subject;
          $class= $topic->class;
        }
        $DBtitles = classwork::all()->where('class',$class)->where('subject',$subject)->where('title',$title)->sortByDesc('created_at');

        return view('teacher.inner_classroom', compact('DBtitles','title','teacherName','subject','subCodes','classCodes'));
    }

    public function createTitle($id)
    {
        $subCodes = $this->getTeachersubCodes();
        $classCodes = $this->getAllClassCodes();
        $subCode = subCode::all()->where('id',$id);
        foreach($subCode as $classSub){
            $subject =  $classSub->subject;
            $class  =   $classSub->class;
        }

        $classDatas = classwork::all()->where('class',$class)->where('subject',$subject)->sortByDesc('created_at');
        $classworks  = classwork::all()->sortBy("class");
        return view('teacher.createTitle', compact('subCodes','classCodes','classworks','subCode','classDatas','class','subject','id'));
    }

    public function createTitlePost(Request $request)
    {
        $data = $request->input();
        $id = $data['id'];
        if($data['inputTitle']==""){
            return redirect('teacher/createTitle/'.$id)->with('failed',"Try again, Please select only one title");
        }

        try{
            $getClassSubs = DB::select('SELECT * FROM sub_codes WHERE id = ?' , [$data['id']]);
            foreach ($getClassSubs as $getClassSub) {
                $class = $getClassSub->class;
                $subject = $getClassSub->subject;
            }
            $classwork = new classwork;
            $classwork->name = Auth::guard('teacher')->user()->name;
            $classwork->email = Auth::guard('teacher')->user()->email;
            $classwork->title = $data['inputTitle'];
            $classwork->discription = $data['discription'];
            $classwork->subject = $subject;
            $classwork->class = $class;
            $classwork->type = 'TOPIC';

            $classwork->save();

            return redirect('teacher/createTitle/'.$id)->with('status','Insert successfully');
        }
        catch(Exception $e){
            return redirect('teacher/createTitle/'.$id)->with('failed',"operation failed");
        }
    }

    public function deletePost($id)
    {
        try{
            $classworks  = classwork::all()->WHERE('id',$id);
            foreach($classworks as $classwork){
                $class = $classwork->class;
                $subject = $classwork->subject;
            }

            $subIds = subCode::all()->where('class',$class)->where('subject',$subject);
            foreach($subIds as $forSubId){
                $subId = $forSubId->id;
            }

            $record = classwork::find($id);
            $record->delete($record->id);

            return redirect('teacher/classroom/'.$subId)->with('delete','Record deleted successfully');
        }
        catch(Exception $e){
            return redirect('teacher/createTitle/'.$id)->with('failed',"operation failed");
        }
    }

    public function classworkAttendence($id)
    {
        $subCodes = $this->getTeachersubCodes();
        $classCodes = $this->getAllClassCodes();

        $classworks= classwork::all()->where('id',$id);
        foreach($classworks as $classwork){
            $class = $classwork->class;
        }
        $users = User::all()->where('grade',$class);

        // collect notifications safely
        $readNotications = []; $unreadNotications = [];
        foreach($users as $user){
            foreach($user->readnotifications as $notification){
                $readNotications[] = $notification;
            }
            foreach($user->unreadnotifications as $notification){
                $unreadNotications[] = $notification;
            }
        }

        return view('teacher/classworkAttendence', compact('subCodes','classCodes','readNotications','unreadNotifications','id','users'));
    }

    public function studentReturnWork($id)
    {
        $subCodes = $this->getTeachersubCodes();
        $classCodes = $this->getAllClassCodes();

        $stuHomeworkUploads = stuHomeworkUpload::all()->where('titleId',$id)->sortBy('email');
        foreach($stuHomeworkUploads as $stuHomeworkUpload){
            $class = $stuHomeworkUpload->class;
        }
        if(!(isset($class))){
            return back()->with('failed',"No record found");
        }
        $users = User::all()->where('grade',$class);

        return view('teacher/studentReturnWork', compact('subCodes','classCodes','id','users','stuHomeworkUploads'));
    }

    public function liveClassAttendence($id)
    {
        $subCodes = $this->getTeachersubCodes();
        $classCodes = $this->getAllClassCodes();

        $livesubCodes = subCode::all()->where('id',$id);
        foreach($livesubCodes as $livesubCode){
            $class = $livesubCode->class;
            $subject = $livesubCode->subject;
        }

        if(!(isset($class))){
            return back()->with('failed',"No record found");
        }
        $users = liveClassAttendence::all()->where('class',$class)->where('subject',$subject)->sortBy('created_at');

        return view('teacher/liveClassAttendence', compact('subCodes','classCodes','users','class','subject'));
    }

//    public function teacherDueList()
// {
//     $subCodes = $this->getTeachersubCodes();
//     $classCodes = $this->getAllClassCodes();

//     // Filter sub_codes that belong to this teacher
//     $teacherClassCodes = $classCodes->whereIn('id', $subCodes);

//     // Keep entire subCode models (not just class names)
//     $classes = $teacherClassCodes->unique('class')->values();

//     $categories = Category::all();
//     $routes = RouteName::all();

//     return view('teacher.teacherDueList', compact('categories', 'classes', 'routes', 'subCodes', 'classCodes'));
// }



    public function post_teacherDueList(Request $request)
    {
        $data = $request->input();

        // Define all months
        $months = ['apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar'];

        // Get selected months
        $selectedMonths = array_filter($data, function ($value, $key) use ($months) {
            return $value === 'on' && in_array($key, $months);
        }, ARRAY_FILTER_USE_BOTH);

        $selectedMonthNames = array_keys($selectedMonths); // Extract only the keys (month names)

        // Fetch users with dynamic filtering
        $users = User::with(['receipts', 'route', 'category'])->where(function ($query) use ($data) {
            if ($data['route'] !== 'all') {
                $query->where('route', $data['route']);
            }
            if ($data['category'] !== 'all') {
                $category = Category::where('category', $data['category'])->first();
                if ($category) {
                    $query->where('category_id', $category->id);
                }
            }
            if ($data['class'] !== 'all') {
                $query->where('grade', $data['class']);
            }
        })->get();

        // Cache data
        $categories = Cache::remember("category_{$data['category']}", 3600, function () use ($data) {
            return $data['category'] === 'all'
                ? Category::all()->pluck('category', 'id')
                : Category::where('category', $data['category'])->pluck('category', 'id');
        });

        $classes = Cache::remember("classes_{$data['class']}", 3600, function () use ($data) {
            return $data['class'] === 'all'
                ? subCode::distinct('class')->pluck('class')
                : subCode::where('class', $data['class'])->distinct('class')->pluck('class');
        });

        $routes = Cache::remember("routes_{$data['route']}", 3600, function () use ($data) {
            return $data['route'] === 'all'
                ? routeFeePlan::distinct('routeName')->pluck('routeName')
                : routeFeePlan::where('routeName', $data['route'])->distinct('routeName')->pluck('routeName');
        });

        // Calculate fees
        $classArray = $this->calculateClassFees($data, $categories, $classes, $selectedMonthNames);
        $routeArray = $this->calculateRouteFees($data, $routes, $selectedMonthNames);

        // Filter students who missed at least one selected month
        $students = $users->filter(function ($user) use ($selectedMonthNames) {
            foreach ($selectedMonthNames as $month) {
                if ($user->receipts->where('month', $month)->sum('receivedAmt') == 0) {
                    return true; // Include if any selected month is unpaid
                }
            }
            return false;
        })->map(function ($user) use ($classArray, $routeArray, $selectedMonthNames) {
            // Start with old balance
            $due = ($user->oldBalance ?? 0);

            // Add class fees
            foreach ($classArray as $class) {
                if ($class['class'] === $user->grade && $class['category'] === $user->category->category) {
                    $due += $class['value'];
                }
            }

            // Add route fees
            foreach ($routeArray as $route) {
                if (!empty($user->route->routeName) && $route['routeName'] === $user->route->routeName) {
                    $due += $route['routeValue'];
                }
            }

            // **Subtract receipt amounts only ONCE**
            $receivedAmount = $user->receipts
                ->unique('receiptId') // Ensures only unique receipts are counted
                ->sum('receivedAmt');

            // Get Receipts late fee
            $pastLateFee = $user->receipts
                ->unique('receiptId')
                ->sum('lateFee') ?? 0;

            // Get Receipts Concession
            $pastConcession = $user->receipts
                ->unique('receiptId')
                ->sum('concession') ?? 0;

            // Fetch applicable concession (Only for unpaid months)
            $totalConcession = Concession::where('user_id', $user->id)
                ->sum('concession_fee');

            // Define all months at the beginning
            $months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];

            // Get paid months to exclude
            $paidMonths = collect($months)->filter(function ($month) use ($user) {
                return $user->receipts->sum($month) > 0; // Check if the user has paid for this month
            })->toArray();

            $unpaidMonths = array_diff($selectedMonthNames, $paidMonths);

            // Adjust concession only for unpaid months
            $concessionAmount = $totalConcession * count($unpaidMonths) ; // Assuming concession is annual, adjust accordingly

            // Update due amount
            $due += $pastLateFee;   // Add past late fees
            $due -= $pastConcession; // Deduct already applied concession
            $due -= $receivedAmount; // Deduct received amount
            $due -= $concessionAmount; // Deduct applicable concession for unpaid months

            // **Add Late Fee (Fixed to Use $selectedMonthNames)**
            $lateFee = $this->calculateLateFee($user, $unpaidMonths);
            $due += $lateFee;

            return [
                'id' => $user->id,
                'class' => $user->grade,
                'name' => $user->name,
                'fName' => $user->fName,
                'mobile' => $user->mobile,
                'due' => max(0, $due), // Ensure no negative due amount
                'routeName' => $user->route->routeName ?? null,
                'category' => $user->category->category ?? null,
            ];
        });

        // Get teacher's class codes for menu
        $subCodes = $this->getTeachersubCodes();
        $classCodes = $this->getAllClassCodes();

        return view('teacher.teacherDueListAllRecords', [
            'students' => $students,
            'selectedMonths' => $selectedMonths,
            'selectedMonthNames' => $selectedMonthNames,
            'subCodes'  => $subCodes,
            'classCodes'  => $classCodes,
        ]);
    }

    /**
     * Calculate Late Fee
     */
    private function calculateLateFee($user, $selectedMonths)
    {
        // Define the month mapping (adjust to match 'apr' = index 0)
        $months = ['apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar'];

        // Calculate the current month index
        $currentMonthIndex = (date('n') + 8) % 12; // Adjusting to match April as index 0

        // Get the late fee per month value
        $lateFeePerMonth = FeePlan::whereHas('feeHead', function ($query) {
            $query->where('name', 'Late Fee');
        })->value('value') ?? 0;

        // Calculate the late fee for past months only
        $lateFee = collect($selectedMonths)->filter(function ($month) use ($months, $currentMonthIndex) {
            $monthIndex = array_search($month, $months);
            return $monthIndex !== false && $monthIndex < $currentMonthIndex; // Only past months
        })->count() * $lateFeePerMonth;

        return $lateFee;
    }

    private function sendClassworkUploadNotifications(string $class, string $subject, string $title, string $type): void
    {
        try {
            $projectId = env('FCM_PROJECT_ID');
            $serviceAccountPath = env('FCM_SERVICE_ACCOUNT');

            if (empty($projectId) || empty($serviceAccountPath)) {
                Log::warning('Classwork notification skipped: missing FCM_PROJECT_ID/FCM_SERVICE_ACCOUNT');
                return;
            }

            $studentIds = User::where('grade', $class)->pluck('id')->toArray();
            if (empty($studentIds)) {
                return;
            }

            $tokens = DB::table('device_tokens')
                ->whereIn('user_id', $studentIds)
                ->pluck('token')
                ->filter()
                ->unique()
                ->values()
                ->toArray();

            if (empty($tokens)) {
                return;
            }

            $accessToken = $this->getFcmAccessToken($serviceAccountPath);
            if (empty($accessToken)) {
                Log::error('Classwork notification skipped: failed to build FCM access token');
                return;
            }

            $endpoint = 'https://fcm.googleapis.com/v1/projects/' . $projectId . '/messages:send';
            $notificationTitle = 'New Classwork Uploaded';
            $notificationBody = sprintf('%s: %s (%s)', $subject, $title, $class);

            foreach ($tokens as $token) {
                $response = Http::timeout(10)->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ])->post($endpoint, [
                    'message' => [
                        'token' => $token,
                        'notification' => [
                            'title' => $notificationTitle,
                            'body' => $notificationBody,
                        ],
                        'data' => [
                            'type' => 'classwork_uploaded',
                            'class' => (string) $class,
                            'subject' => (string) $subject,
                            'title' => (string) $title,
                            'material_type' => (string) $type,
                        ],
                        'android' => [
                            'priority' => 'HIGH',
                        ],
                    ],
                ]);

                if (! $response->successful()) {
                    Log::error('Classwork notification request failed', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                        'class' => $class,
                        'subject' => $subject,
                        'title' => $title,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            Log::error('Failed to send classwork upload notifications', [
                'class' => $class,
                'subject' => $subject,
                'title' => $title,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function getFcmAccessToken(string $serviceAccountPath): ?string
    {
        try {
            $absolutePath = str_starts_with($serviceAccountPath, '/')
                ? $serviceAccountPath
                : base_path($serviceAccountPath);

            if (! file_exists($absolutePath)) {
                Log::error('FCM service account file not found', ['path' => $absolutePath]);
                return null;
            }

            $json = json_decode(file_get_contents($absolutePath), true);
            if (! is_array($json)) {
                Log::error('Invalid FCM service account JSON');
                return null;
            }

            $clientEmail = $json['client_email'] ?? null;
            $privateKey = $json['private_key'] ?? null;
            $tokenUri = $json['token_uri'] ?? 'https://oauth2.googleapis.com/token';

            if (empty($clientEmail) || empty($privateKey)) {
                Log::error('FCM service account JSON missing client_email/private_key');
                return null;
            }

            $now = time();
            $header = ['alg' => 'RS256', 'typ' => 'JWT'];
            $payload = [
                'iss' => $clientEmail,
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud' => $tokenUri,
                'iat' => $now,
                'exp' => $now + 3600,
            ];

            $base64Header = $this->base64UrlEncode(json_encode($header));
            $base64Payload = $this->base64UrlEncode(json_encode($payload));
            $signingInput = $base64Header . '.' . $base64Payload;

            $signature = '';
            $ok = openssl_sign($signingInput, $signature, $privateKey, OPENSSL_ALGO_SHA256);
            if (! $ok) {
                Log::error('Unable to sign JWT for FCM');
                return null;
            }

            $jwt = $signingInput . '.' . $this->base64UrlEncode($signature);

            $response = Http::asForm()->post($tokenUri, [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

            if (! $response->successful()) {
                Log::error('Failed to fetch FCM OAuth token', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $token = $response->json('access_token');
            return is_string($token) && $token !== '' ? $token : null;
        } catch (\Throwable $e) {
            Log::error('Exception while creating FCM access token', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function calculateClassFees($data, $categories, $classes, $selectedMonths)
    {
        $classArray = [];

        foreach ($categories as $category) {
            foreach ($classes as $class) {
                $value = FeePlan::where('class', $class)
                    ->where('category', $category)
                    ->get()
                    ->reduce(function ($carry, $feePlan) use ($data) {
                        $frequency = FeeHead::where('name', $feePlan->feeHead->name)->first();

                        if (!$frequency) return $carry;

                        foreach (array_keys($data) as $month) {
                            if (isset($data[$month]) && $data[$month] === "on" && isset($frequency->{$month}) && $frequency->{$month} == 1 && $feePlan->feeHead->name !== "LATE FEE") {
                                $carry += $feePlan->value;
                            }
                        }

                        return $carry;
                    }, 0);

                $classArray[] = ['class' => $class, 'category' => $category, 'value' => $value];
            }
        }
        return $classArray;
    }

    private function calculateRouteFees($data, $routes, $selectedMonths)
    {
        $routeArray = [];

        foreach ($routes as $routeName) {
            if ($routeName == 'NA') {
                continue;
            }

            $routeFrequency = RouteName::where('routeName', $routeName)->first();
            $routeValue = 0;

            if ($routeFrequency) {
                $routeFeePlans = routeFeePlan::where('routeName', $routeName)->first();

                switch ($routeFrequency->frequency) {
                    case 'MONTHLY':
                        $routeValue = $routeFeePlans->value * count($selectedMonths);
                        break;

                    case 'QUARTERLY':
                        $routeValue = $routeFeePlans->value * ceil(count($selectedMonths) / 3);
                        break;

                    case 'HALFYEARLY':
                        $routeValue = $routeFeePlans->value * ceil(count($selectedMonths) / 6);
                        break;

                    case 'YEARLY':
                    case 'ONETIME':
                        $routeValue = $routeFeePlans->value;
                        break;
                }
            }

            $routeArray[] = [
                'routeName' => $routeName,
                'routeValue' => $routeValue,
            ];
        }

        return $routeArray;
    }

  // put these methods in your TeacherController (near other helper methods)

private function getTeachersubCodes(): array
{
    $codes = [];
    $teacher = Auth::guard('teacher')->user();

    // Collect class_code0..class_code11 from teacher safely
    for ($i = 0; $i <= 11; $i++) {
        $prop = "class_code{$i}";
        if (isset($teacher->{$prop}) && $teacher->{$prop} !== null && $teacher->{$prop} !== '') {
            // ensure numeric id (or cast to int if stored as string id)
            $codes[] = (int) $teacher->{$prop};
        }
    }

    // unique and reindex
    return collect($codes)->filter()->unique()->values()->all();
}

private function getAllClassCodes()
{
    // return collection of subCode models sorted by class
    return subCode::all()->sortBy('class')->values();
}

public function teacherDueList()
{
    // teacher subCode ids (e.g. [12, 34, 55])
    $subCodes = $this->getTeachersubCodes();

    // all subCode models
    $classCodes = $this->getAllClassCodes();

    // Filter subCode models that belong to this teacher
    $teacherClassCodes = $classCodes->filter(function ($subCode) use ($subCodes) {
        return in_array((int) $subCode->id, $subCodes, true);
    })->values();

    // Now produce a collection where each unique 'class' has one representative model
    // e.g. [ subCode(id=12, class='10TH', subject='Math'), subCode(id=20, class='11TH', ...) ]
    $classes = $teacherClassCodes->unique('class')->values();

    $categories = Category::all();
    $routes = RouteName::all();


    // If your view expects classCodes (all) and subCodes (ids) still — keep both
    return view('teacher.teacherDueList', compact('categories', 'classes', 'routes', 'subCodes', 'classCodes'));
}

}
