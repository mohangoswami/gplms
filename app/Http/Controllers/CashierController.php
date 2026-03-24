<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\FeeHead;
use App\Category;
use App\subCode;
use App\FeePlan;
use App\User;
use App\Teacher;
use App\flashNews;
use App\RouteName;
use App\Receipt;
use App\routeFeePlan;
use App\Concession;
use DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Auth;


class CashierController extends Controller
{
    public function cashierUpdatePassword()
    {

      $user = Auth::user();
      $flashNews = flashNews::latest()->get();

            return view('cashier.cashierSelf-update-password', compact('user', 'flashNews'));
    }

    public function postCashierUpdatePassword(Request $request)
      {
          $request->validate([
              'new_password' => ['required', 'string', 'min:6', 'confirmed'],
          ]);
          $data = $request->all();
          $user = Auth::user();
          $user->password = Hash::make($request->new_password);
          $user->save();

          return back()->with('success', 'Cashier password updated successfully');
      }



    public function dashboard()
    {
        return view('cashier.dashboard');
    }

    public function allStudentsRecord()
    {

        $users  = User::all()->sortBy('class');
        $flashNews = flashNews::all()->sortByDesc('created_at');
        return view('cashier.allStudentsRecord', compact('users','flashNews'));
    }


    public function cashierEditStudentRecord(Request $request, $id)
    {
        // Retrieve the user by ID
        $user = User::with(['route', 'category', 'feePlans.FeeHead'])->findOrFail($id);
        if (!$user) {
            return redirect()->back()->with('failed', "User not found.");
        }

        // Retrieve all categories, routes, and unique classes
        $categories = Category::all();
        $routes = RouteName::all();
        $classes = subCode::all()->unique('class')->sortBy("class");

        // Check if classes are empty and redirect if necessary
        if ($classes->isEmpty()) {
            return redirect('cashier/create_subCode')->with('failed', "Please create class and Subject first.");
        }

        // Extract unique grades from classes
        $grades = $classes->pluck('class')->unique()->values();

        // Return the view with the required data
        return view('cashier.cashierEditStudentRecord', compact('user', 'id', 'grades', 'routes', 'categories'));
    }


    public function post_cashierEditStudentRecord(Request $request)
    {
        $data = $request->input();


        $id = $data['id'];

        try {

            // Find the user
            $user = User::findOrFail($id);

            // Find the category and route
            $category = Category::where('category', $data['category'])->first();
            $route = RouteName::where('routeName', $data['route'])->first();
            // Update the user record

            $dob = trim($data['dob']); // Remove spaces

            $user->update([
                'admission_number' => $data['admission_number'],
                'name' => $data['editName'],
                'fName' => $data['fName'],
                'mName' => $data['mName'],

                'dob' => (!isset($data['dob']) || trim($data['dob']) === '' ||
                        strtolower($data['dob']) === 'null' || $data['dob'] === '0000-00-00')
                    ? null
                    : Carbon::parse($data['dob'])->format('Y-m-d'),


                'route_id' => $route->id,
                'category_id' => $category->id ?? null,
                'address' => $data['address'],
                'mobile' => $data['mobile'],
                'rfid' => $data['rfid'],
                'email' => $data['editEmail'],
                'grade' => $data['editClass'],
                'app_permission' => $data['editAppPermission'],
                'exam_permission' => $data['editExamPermission'],
            ]);



            // Find applicable fee plans based on updated grade and category
            $feePlans = FeePlan::where('class', $data['editClass'])
                               ->where('category', $data['category'])
                               ->pluck('id'); // Get fee plan IDs

            // Sync fee plans (Remove old and assign new)
            $user->feePlans()->sync($feePlans);

            return redirect('cashier/allStudentsRecord')->with('status', 'Record updated successfully');
        } catch (Exception $e) {
            return redirect('cashier/allStudentsRecord')->with('failed', "Operation failed");
        }
    }


    public function cashierStudentUpdatePassword($id)
    {

      $user = User::where('id', $id)->first();
      $flashNews = flashNews::latest()->get();

            return view('cashier.cashierStudent-update-password', compact('user', 'flashNews'));
    }

    public function postcashierStudentUpdatePassword(Request $request)
      {
        $request->validate([
              'new_password' => ['required', 'string', 'min:6', 'confirmed'],
          ]);
          $data = $request->all();
          $user = User::findOrFail($data['id']);
          $user->password = Hash::make($request->new_password);
          $user->save();

          return back()->with('success', 'User password updated successfully');
      }

      public function allTeachersRecord()
    {
        $teachers  = Teacher::all();
        $subCodes = subCode::all()->sortBy('class');
        return view('cashier.allTeachersRecord', compact('teachers','subCodes'));
    }

    public function cashierEditTeacherRecord(Request $request, $id)
    {
        $teachers  = Teacher::all()->WHERE('id',$id);

        $classes = subCode::all()->unique()->sortBy("class");
        if(!(isset($classes))){
            return redirect('admin/create_subCode')->with('failed',"Please create class and Subject first.");
        }

        return view('cashier.editTeacherRecord', compact('teachers', 'id','classes'));
    }

    public function post_cashierEditTeacherRecord(Request $request){
        $data = $request->input();
        $id = $data['id'];

        try {
            $teacherName = $data['editName'];
            $fileUrl = 'https://gplmschool-dev-storage-vkmjgjn4dvol.s3.amazonaws.com/' . $teacherName;

            // Fetch existing teacher data
            $teacher = DB::table('teachers')->where('id', $id)->first();

            // Prepare update data
            $updateData = [
                'name' => $data['editName'],
                'fName' => $data['fName'],
                'mName' => $data['mName'],
                'dob' => $data['dob'],
                'doj' => $data['doj'],
                'address' => $data['address'],
                'mobile' => $data['mobile'],
                'rfid' => $data['rfid'],
                'email' => $data['editEmail'],
                'class_code0' => $data['editCode0'],
                'class_code1' => $data['editCode1'],
                'class_code2' => $data['editCode2'],
                'class_code3' => $data['editCode3'],
                'class_code4' => $data['editCode4'],
                'class_code5' => $data['editCode5'],
                'class_code6' => $data['editCode6'],
                'class_code7' => $data['editCode7'],
                'class_code8' => $data['editCode8'],
                'class_code9' => $data['editCode9'],
                'class_code10' => $data['editCode10'],
                'class_code11' => $data['editCode11'],
                'teacherImg' => $fileUrl,
            ];

            // Check if a new password is provided and if it has changed
            if (!empty($data['editPassword'])) {
                if (!Hash::check($data['editPassword'], $teacher->password)) {
                    $updateData['password'] = Hash::make($data['editPassword']);
                }
            }

            // Update teacher record in the database
            DB::table('teachers')->where('id', $id)->update($updateData);

            // Handle image upload
            $file = $request->file('file');
            $imageName = 'teacherImg/' . $teacherName . '.jpg';

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filename = $teacherName . '.jpg';
                Storage::disk('s3')->put("teacherImg/$filename", file_get_contents($file));
            }
            // if ($request->hasFile('file')) {
            //     request()->file->move(public_path('assets/images/teacherImg'), $imageName);
            //     // Storage::disk('s3')->put($imageName, file_get_contents($file));
            //     // Storage::disk('s3')->setVisibility($imageName, 'public');
            // }

            return redirect('cashier/allTeachersRecord')->with('status', 'Record updated successfully');
        } catch (Exception $e) {
            return redirect('cashier/allTeachersRecord')->with('failed', "Operation failed");
        }
}

}
