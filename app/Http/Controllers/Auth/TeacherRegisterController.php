<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Teacher;
use App\Admin;
use App\subCode;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Validator;



class TeacherRegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function showRegisterForm()
    {
        $subCodes = subCode::all()->sortBy("class");

        return view('auth.teacher-register', compact('subCodes'));
    }

    public function register(Request $request)
    {
        
      $validator = Validator::make($request->all(), [
         'name' => ['required', 'string', 'max:255'],
         'fName' => ['required', 'string', 'max:255'],
         'srNo' => ['required', 'string', 'max:255'],
         'mName' => ['required', 'string', 'max:255'],
         'dob' => ['required', 'date'],
         'doj' => ['required', 'date'],
         'address' => ['required', 'string', 'max:255'],
         'mobile' => ['required', 'numeric', 'min:10'],
         'rfid' => [ 'string', 'max:12'],
          'email' => ['required', 'string', 'email', 'max:255', 'unique:teachers'],
         'password' => ['required', 'string', 'min:8', 'confirmed'],
            
        ]);
        if ($validator->fails()) {
         return redirect('teacher/register')
                     ->withErrors($validator)
                     ->withInput();
         }
        try{

        $request['password'] = Hash::make($request->password);
        if(isset($request->subCodes[0])){
            $request['class_code0'] =  $request->subCodes[0];
        }
        if(isset($request->subCodes[1])){
            $request['class_code1'] =  $request->subCodes[1];
         }
         if(isset($request->subCodes[2])){
            $request['class_code2'] =  $request->subCodes[2];
         }
         if(isset($request->subCodes[3])){
            $request['class_code3'] =  $request->subCodes[3];
         }
         if(isset($request->subCodes[4])){
            $request['class_code4'] =  $request->subCodes[4];
         }
         if(isset($request->subCodes[5])){
            $request['class_code5'] =  $request->subCodes[5];
         }
         if(isset($request->subCodes[6])){
            $request['class_code6'] =  $request->subCodes[6];
         }
         if(isset($request->subCodes[7])){
            $request['class_code7'] =  $request->subCodes[7];
         }
         if(isset($request->subCodes[8])){
            $request['class_code8'] =  $request->subCodes[8];
         }
         if(isset($request->subCodes[9])){
            $request['class_code9'] =  $request->subCodes[9];
         }
         if(isset($request->subCodes[10])){
            $request['class_code10'] =  $request->subCodes[10];
         }
         if(isset($request->subCodes[11])){
            $request['class_code11'] =  $request->subCodes[11];
         }
         if(isset($request->subCodes[12])){
            return redirect('teacher/register')->with('error','Failed -> Only 12 Subject wiil be allowed to a Teacher.');
        }
        $teacherName = $request->name;
        $fileUrl = 'https://gplschool.s3.ap-south-1.amazonaws.com/assets/images/teacherImg/' . $teacherName;
        $request['teacherImg'] = $fileUrl;
        
        $request['srNo'] = $request->srNo;
        $request['fName'] = $request->fName;
        $request['mName'] = $request->mName;
        $request['dob'] = $request->dob;
        $request['doj'] = $request->doj;
        $request['address'] = $request->address;
        $request['mobile'] = $request->mobile;
        $request['rfid'] = $request->rfid;

        Teacher::create($request->all());
        $file = $request->file('file');
        $imageName = 'teacherImg/' . $teacherName . '.jpg';
        if ($request->hasFile('file')) {
         request()->file->move(public_path('assets/images/teacherImg'), $imageName);

         //     Storage::disk('s3')->put($imageName, file_get_contents($file));
   //     Storage::disk('s3')->setVisibility($imageName, 'public');
        }
        return redirect()->intended(route('teacher.register'))->with('status','Congrats! Teacher register successfully');
    }
    catch(Exception $e){
        return redirect('teacher/register')->with('error',"operation failed");
    }
    }
}