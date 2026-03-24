<?php

namespace App\Http\Controllers\Users\Admin;

use App\Category;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Auth;
use Illuminate\Support\Facades\Hash;
use App\RouteName;
use Carbon\Carbon;
use App\Receipt;
use App\Concession;
use App\routeFeePlan as RouteFeePlan;

class AdminController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth:admin');
    }

    public function index()
    {
        return view('admin');
    }

    public function allStudentsRecord()
    {
        $users = User::orderBy('grade')->get();
        $flashNews = flashNews::latest()->get();

        return view('admin.allStudentsRecord', compact('users', 'flashNews'));
    }



    public function editStudentRecord(Request $request, $id)
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
            return redirect('admin/create_subCode')->with('failed', "Please create class and Subject first.");
        }

        // Extract unique grades from classes
        $grades = $classes->pluck('class')->unique()->values();

        // Return the view with the required data
        return view('admin.editStudentRecord', compact('user', 'id', 'grades', 'routes', 'categories'));
    }


    public function post_editStudentRecord(Request $request)
    {
        $data = $request->input();
        // dd($data);
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
                'oldBalance' => $data['oldBalance'],


                'dob' => (!isset($data['dob']) || trim($data['dob']) === '' ||
                        strtolower($data['dob']) === 'null' || $data['dob'] === '0000-00-00')
                    ? null
                    : Carbon::parse($data['dob'])->format('Y-m-d'),


                'route_id' => $route->id,
                'category_id' => $category->id ?? null,
                'aadhar' => $data['aadhar'],
                'pen' => $data['pen'],
                'apaar' => $data['apaar'],
                'address' => $data['address'],
                'mobile' => $data['mobile'],
                'rfid' => $data['rfid'],
                'email' => $data['editEmail'],
                'grade' => $data['editClass'],
                'section' => $data['section'],
                'app_permission' => $data['editAppPermission'],
                'exam_permission' => $data['editExamPermission'],
            ]);



            // Find applicable fee plans based on updated grade and category
            $feePlans = FeePlan::where('class', $data['editClass'])
                               ->where('category', $data['category'])
                               ->pluck('id'); // Get fee plan IDs

            // Sync fee plans (Remove old and assign new)
            $user->feePlans()->sync($feePlans);

            return redirect('admin/allStudentsRecord')->with('status', 'Record updated successfully');
        } catch (Exception $e) {
            return redirect('admin/allStudentsRecord')->with('failed', "Operation failed");
        }
    }



  public function deleteStudentRecord($id){
    try{
        $record = User::find($id);

        $record->delete($record->id);

        return redirect('admin/allStudentsRecord')->with('delete','Student deleted successfully');
    }
        catch(\Illuminate\Database\QueryException $ex){
            if((int)$ex->getCode() == 23000){
                return redirect('admin/allStudentsRecord')->with('failed',"Delete All Fee Reciept before Deleteting User");
            };

            return redirect('admin/allStudentsRecord')->with('failed',$ex->getMessage());
    }
  }


  public function studentUpdatePassword($id)
  {

    $user = User::where('id', $id)->first();
    $flashNews = flashNews::latest()->get();

          return view('admin.student-update-password', compact('user', 'flashNews'));
  }

  public function postStudentUpdatePassword(Request $request)
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

    public function adminUpdatePassword()
  {

    $user = Auth::user();
    $flashNews = flashNews::latest()->get();

          return view('admin.admin-update-password', compact('user', 'flashNews'));
  }

  public function postAdminUpdatePassword(Request $request)
    {
        $request->validate([
            'new_password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
        $data = $request->all();
        $user = Auth::user();
        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'User password updated successfully');
    }

    public function cashierUpdatePassword($id)
    {

      $cashier = Cashier::where('id', $id)->first();
      $flashNews = flashNews::latest()->get();

            return view('admin.cashier-update-password', compact('cashier', 'flashNews'));
    }

    public function postCashierUpdatePassword(Request $request)
      {
          $request->validate([
              'new_password' => ['required', 'string', 'min:6', 'confirmed'],
          ]);
          $data = $request->all();
          $user = Cashier::findOrFail($data['id']);
          $user->password = Hash::make($request->new_password);
          $user->save();

          return back()->with('success', 'User password updated successfully');
      }

    public function allCashierRecord()
    {
        $cashiers  = Cashier::all();
        $subCodes = subCode::all()->sortBy('class');
        return view('admin.allCashierRecord', compact('cashiers','subCodes'));
    }



  public function deleteCashierRecord($id){
    try{
        $record = Cashier::find($id);

        $record->delete($record->id);

        return redirect('admin/allCashierRecord')->with('delete','Cashier deleted successfully');
    }
    catch(Exception $e){
        return redirect('teacher/allCashierRecord/'.$id)->with('failed',"operation failed");

    }
  }

  public function allTeachersRecord()
    {
        $teachers  = Teacher::all();
        $subCodes = subCode::all()->sortBy('class');
        return view('admin.allTeachersRecord', compact('teachers','subCodes'));
    }

    public function editTeacherRecord(Request $request, $id)
    {
        $teachers  = Teacher::all()->WHERE('id',$id);

        $classes = subCode::all()->unique()->sortBy("class");
        if(!(isset($classes))){
            return redirect('admin/create_subCode')->with('failed',"Please create class and Subject first.");
        }

        return view('admin.editTeacherRecord', compact('teachers', 'id','classes'));
    }

public function post_editTeacherRecord(Request $request){
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

        return redirect('admin/allTeachersRecord')->with('status', 'Record updated successfully');
    } catch (Exception $e) {
        return redirect('admin/allTeachersRecord')->with('failed', "Operation failed");
    }
}



  public function deleteTeacherRecord($id){
    try{
        $record = Teacher::find($id);

        $record->delete($record->id);

        return redirect('admin/allTeachersRecord')->with('delete','Teacher deleted successfully');
    }
    catch(Exception $e){
        return redirect('teacher/allTeachersRecord/'.$id)->with('failed',"operation failed");

    }
  }

    public function get_create_subCode()
    {
        return view('admin.create_subCode');
    }


    public function insert(){
        $urlData = getURLList();
        return view('admin.create_subCode');
    }

    public function post_create_subCode(Request $request)
    {

        $rules = [
			      'grade' => 'required', 'string', 'max:255',
            'subject' =>  'required', 'string', 'max:255',

		];
		$validator = Validator::make($request->all(),$rules);
		if ($validator->fails()) {
			return redirect('admin/create_subCode')
			->withInput()
			->withErrors($validator);
		}
		else{
            $data = $request->input();
			try{
        $class = strtoupper($data['grade']);
        $subject = strtoupper($data['subject']);
              $classSubjects = subCode::all();
              foreach($classSubjects as $classSubject){
                if($classSubject->class == $class && $classSubject->subject == $subject){
                  return redirect('admin/create_subCode')->with('failed',"Duplicate Entry");
                }
              }

				        $subCode = new subCode;
                $subCode->class = $class;
                $subCode->subject = $subject;
				$subCode->save();
				return redirect('admin/create_subCode')->with('status','Insert successfully');
			}
			catch(Exception $e){
				return redirect('admin/create_subCode')->with('failed',"operation failed");
			}
		}
    }

    public function get_create_liveClass()
    {
        $subCodes  = subCode::all()->sortBy('class');
        return view('admin.liveClasses.create_liveClass', compact('subCodes'));
    }

    public function allLiveClasses()
    {
        $subCodes  = subCode::all()->sortBy('class');
        return view('admin.liveClasses.allLiveClasses', compact('subCodes'));
    }

    public function post_create_liveClass(Request $request)
    {


            $data = $request->input();

			try{
                if(isset($data['Monday'])){
                    $Monday = $data['Monday'];
                }else{
                  $Monday = NULL;
                }
                if(isset($data['Tuesday'])){
                  $Tuesday = $data['Tuesday'];
                }else{
                $Tuesday = NULL;
                }
                if(isset($data['Wednesday'])){
                  $Wednesday = $data['Wednesday'];
                }else{
                $Wednesday = NULL;
                }
                if(isset($data['Thursday'])){
                  $Thursday = $data['Thursday'];
                }else{
                $Thursday = NULL;
                }
                if(isset($data['Friday'])){
                  $Friday = $data['Friday'];
                }else{
                $Friday = NULL;
                }
                if(isset($data['Saturday'])){
                  $Saturday = $data['Saturday'];
                }else{
                $Saturday = NULL;
                }
                if(isset($data['Sunday'])){
                  $Sunday = $data['Sunday'];
                }else{
                $Sunday = NULL;
                }

                $subId = $data['selectClass'];

                //dd($subId);
                DB::table('sub_codes')
            ->where('id', $subId)
            ->update(['link_url' => $data['link'],
            'start_time' => $data['startTime'],
            'end_time' => $data['endTime'],
            'Monday' => $Monday,
            'Tuesday' => $Tuesday,
            'Wednesday' => $Wednesday,
            'Thursday' => $Thursday,
            'Friday' => $Friday,
            'Saturday' => $Saturday,
            'Sunday' => $Sunday, ]);
				return redirect('admin/liveClasses/create_liveClass')->with('status','Link created successfully');
                }

			catch(Exception $e){
				return redirect('admin/liveClasses/create_liveClass')->with('failed',"operation failed");
			}

    }

    public function post_editLiveClass(Request $request)
    {


            $data = $request->input();

			try{
                if(isset($data['Monday'])){
                    $Monday = $data['Monday'];
                }else{
                  $Monday = NULL;
                }
                if(isset($data['Tuesday'])){
                  $Tuesday = $data['Tuesday'];
                }else{
                $Tuesday = NULL;
                }
                if(isset($data['Wednesday'])){
                  $Wednesday = $data['Wednesday'];
                }else{
                $Wednesday = NULL;
                }
                if(isset($data['Thursday'])){
                  $Thursday = $data['Thursday'];
                }else{
                $Thursday = NULL;
                }
                if(isset($data['Friday'])){
                  $Friday = $data['Friday'];
                }else{
                $Friday = NULL;
                }
                if(isset($data['Saturday'])){
                  $Saturday = $data['Saturday'];
                }else{
                $Saturday = NULL;
                }
                if(isset($data['Sunday'])){
                  $Sunday = $data['Sunday'];
                }else{
                $Sunday = NULL;
                }

                $subId = $data['selectClass'];

                //dd($subId);
                DB::table('sub_codes')
            ->where('id', $subId)
            ->update(['link_url' => $data['link'],
            'start_time' => $data['startTime'],
            'end_time' => $data['endTime'],
            'Monday' => $Monday,
            'Tuesday' => $Tuesday,
            'Wednesday' => $Wednesday,
            'Thursday' => $Thursday,
            'Friday' => $Friday,
            'Saturday' => $Saturday,
            'Sunday' => $Sunday, ]);
				return redirect('admin/liveClasses/create_liveClass')->with('status','Link updated successfully');
                }

			catch(Exception $e){
				return redirect('admin/liveClasses/create_liveClass')->with('failed',"operation failed");
			}

    }
    public function editLiveClass(Request $request, $id)
    {
        $subCodes  = subCode::all()->WHERE('id',$id);
        foreach($subCodes as $subCode){
          $id =$subCode->id;
          $class =$subCode->class;
          $subject =$subCode->subject;
          $Monday =$subCode->Monday;
          $Tuesday =$subCode->Tuesday;
          $Wednesday =$subCode->Wednesday;
          $Thursday =$subCode->Thursday;
          $Friday =$subCode->Friday;
          $Saturday =$subCode->Saturday;
          $Sunday =$subCode->Sunday;
          $start_time =$subCode->start_time;
          $end_time =$subCode->end_time;
          $link_url =$subCode->link_url;
        }

        return view('admin.liveClasses.editLiveClass', compact('subCodes', 'id'));
    }

    public function createFlashNews(){
      return view('admin.createFlashNews');
    }


    public function postFlashNews(Request $request){
      $data = $request->input();
      try{
				$flashNews = new flashNews;
        $flashNews->news = $data['inputNews'];
				$flashNews->save();
        $this->sendFlashNewsNotificationsToStudents($flashNews);
				return redirect('admin/createFlashNews')->with('status','Insert successfully');
			}
			catch(Exception $e){
				return redirect('admin/createFlashNews')->with('failed',"operation failed");
			}
    }

    public function allFlashNews(){
      $flashNews = flashNews::all()->sortByDesc('created_at');
      return view('admin.allFlashNews',compact('flashNews'));
    }

    public function sendFeeReminder($id)
    {
        try {
            $student = User::with(['route', 'feePlans.feeHead'])->findOrFail($id);
            $fcmContext = $this->buildFcmContext();
            if ($fcmContext['error']) {
                return redirect('admin/allStudentsRecord')->with('failed', $fcmContext['error']);
            }

            $summary = $this->calculateStudentDashboardFeeSummary($student);
            $totalPayable = (float) ($summary['totalPayable'] ?? 0);

            if ($totalPayable <= 0) {
                return redirect('admin/allStudentsRecord')->with(
                    'failed',
                    'No pending fee for this student. Reminder not sent.'
                );
            }

            $sent = $this->sendFeeReminderPush($student, $totalPayable, $fcmContext);
            if (! $sent) {
                return redirect('admin/allStudentsRecord')->with(
                    'failed',
                    'Device token not found for this student. Ask student to login once in app.'
                );
            }

            $amount = number_format($totalPayable, 2, '.', '');

            return redirect('admin/allStudentsRecord')->with(
                'status',
                'Fee reminder sent to ' . ($student->name ?? 'student') . ' (Rs ' . $amount . ').'
            );
        } catch (\Throwable $e) {
            Log::error('Failed to send fee reminder', [
                'student_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return redirect('admin/allStudentsRecord')->with('failed', 'Fee reminder failed.');
        }
    }

    public function sendFeeReminderAllDue()
    {
        try {
            @set_time_limit(300);

            $fcmContext = $this->buildFcmContext();
            if ($fcmContext['error']) {
                return redirect('admin/allStudentsRecord')->with('failed', $fcmContext['error']);
            }

            $students = User::with(['route', 'feePlans.feeHead'])->get();
            $dueStudents = 0;
            $sentStudents = 0;
            $tokenMissing = 0;

            foreach ($students as $student) {
                $summary = $this->calculateStudentDashboardFeeSummary($student);
                $totalPayable = (float) ($summary['totalPayable'] ?? 0);

                if ($totalPayable <= 0) {
                    continue;
                }

                $dueStudents++;

                $sent = $this->sendFeeReminderPush($student, $totalPayable, $fcmContext);
                if ($sent) {
                    $sentStudents++;
                } else {
                    $tokenMissing++;
                }
            }

            return redirect('admin/allStudentsRecord')->with(
                'status',
                'Fee reminders sent: ' . $sentStudents . '/' . $dueStudents
                . ($tokenMissing > 0 ? ' (no token: ' . $tokenMissing . ')' : '')
            );
        } catch (\Throwable $e) {
            Log::error('Failed to send bulk fee reminders', ['error' => $e->getMessage()]);
            return redirect('admin/allStudentsRecord')->with('failed', 'Bulk fee reminder failed.');
        }
    }

    public function deleteFlashNews($id){
      try{
          $record = flashNews::find($id);

          $record->delete($record->id);

          return redirect('admin/allFlashNews')->with('delete','News deleted successfully');
      }
      catch(Exception $e){
          return redirect('teacher/allFlashNews/'.$id)->with('failed',"operation failed");

      }
    }

    public function allsubCodes()
    {
        $subCodes  = subCode::all()->sortBy('class');
        return view('admin.allsubCodes', compact('subCodes'));
    }

    public function deletesubCode($id){
      try{
          $record = subCode::find($id);

          $record->delete($record->id);

          return redirect('admin/allsubCodes')->with('delete','Subject deleted successfully');
      }
      catch(Exception $e){
          return redirect('teacher/allFlashNews/'.$id)->with('failed',"operation failed");

      }
    }

    public function get_createTerms(){
      return view('admin.createTerms');
    }

    public function post_createTerms(Request $request){
      $data = $request->input();
      try{
				$term = new Term;
        $term->term = $data['term'];
				$term->save();
				return redirect('admin/createTerms')->with('status','Insert successfully');
			}
			catch(Exception $e){
				return redirect('admin/createTerms')->with('failed',"operation failed");
			}
    }

    public function allTerms(){
      $terms = Term::all()->sortByDesc('created_at');
      return view('admin.allTerms',compact('terms'));
    }

    private function sendFlashNewsNotificationsToStudents(flashNews $flashNews): void
    {
        try {
            $projectId = env('FCM_PROJECT_ID');
            $serviceAccountPath = env('FCM_SERVICE_ACCOUNT');

            if (empty($projectId) || empty($serviceAccountPath)) {
                Log::warning('Flash news notification skipped: missing FCM_PROJECT_ID/FCM_SERVICE_ACCOUNT');
                return;
            }

            $studentIds = User::query()->pluck('id')->toArray();
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
                Log::error('Flash news notification skipped: failed to build FCM access token');
                return;
            }

            $endpoint = 'https://fcm.googleapis.com/v1/projects/' . $projectId . '/messages:send';
            $title = 'New Flash News';
            $body = mb_substr((string) $flashNews->news, 0, 140);

            foreach ($tokens as $token) {
                $response = Http::timeout(10)->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ])->post($endpoint, [
                    'message' => [
                        'token' => $token,
                        'notification' => [
                            'title' => $title,
                            'body' => $body,
                        ],
                        'data' => [
                            'type' => 'flash_news',
                            'news_id' => (string) $flashNews->id,
                            'news' => (string) $flashNews->news,
                        ],
                        'android' => [
                            'priority' => 'HIGH',
                        ],
                    ],
                ]);

                if (! $response->successful()) {
                    Log::error('Flash news notification request failed', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                        'news_id' => $flashNews->id,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            Log::error('Failed to send flash news notifications', [
                'news_id' => $flashNews->id ?? null,
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

    private function buildFcmContext(): array
    {
        $projectId = env('FCM_PROJECT_ID');
        $serviceAccountPath = env('FCM_SERVICE_ACCOUNT');
        if (empty($projectId) || empty($serviceAccountPath)) {
            return ['error' => 'FCM_PROJECT_ID or FCM_SERVICE_ACCOUNT missing in .env'];
        }

        $accessToken = $this->getFcmAccessToken($serviceAccountPath);
        if (empty($accessToken)) {
            return ['error' => 'Unable to generate FCM access token. Check service account file.'];
        }

        return [
            'error' => null,
            'endpoint' => 'https://fcm.googleapis.com/v1/projects/' . $projectId . '/messages:send',
            'accessToken' => $accessToken,
        ];
    }

    private function sendFeeReminderPush(User $student, float $totalPayable, array $fcmContext): bool
    {
        $tokens = DB::table('device_tokens')
            ->where('user_id', $student->id)
            ->pluck('token')
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        if (empty($tokens)) {
            return false;
        }

        $amount = number_format($totalPayable, 2, '.', '');
        $title = 'Fee Reminder';
        $body = 'Dear ' . ($student->name ?? 'Student') . ', your total payable fee is Rs ' . $amount . '.';

        foreach ($tokens as $token) {
            $response = Http::timeout(10)->withHeaders([
                'Authorization' => 'Bearer ' . $fcmContext['accessToken'],
                'Content-Type' => 'application/json',
            ])->post($fcmContext['endpoint'], [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'data' => [
                        'type' => 'fee_reminder',
                        'student_id' => (string) $student->id,
                        'admission_number' => (string) ($student->admission_number ?? ''),
                        'total_payable' => (string) $amount,
                    ],
                    'android' => [
                        'priority' => 'HIGH',
                    ],
                ],
            ]);

            if (! $response->successful()) {
                Log::error('Fee reminder notification request failed', [
                    'student_id' => $student->id,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        }

        return true;
    }

    private function calculateStudentDashboardFeeSummary(User $student): array
    {
        $monthKeys = ['apr','may','jun','jul','aug','sep','oct','nov','dec','jan','feb','mar'];
        $currentMonthIndex = ((int) date('n') + 8) % 12; // Apr = 0
        $selectedMonthKeys = array_slice($monthKeys, 0, $currentMonthIndex + 1);

        $allReceipts = Receipt::where('user_id', $student->id)
            ->select(array_merge(['feeHead', 'created_at', 'balance'], $monthKeys))
            ->get();

        $lastReceipt = $allReceipts->sortByDesc('created_at')->first();
        $previousBalance = $lastReceipt
            ? (float) $lastReceipt->balance
            : (float) ($student->oldBalance ?? 0);

        $paidMonthKeys = [];
        foreach ($allReceipts as $receipt) {
            foreach ($monthKeys as $mk) {
                if (!is_null($receipt->{$mk})) {
                    $paidMonthKeys[] = $mk;
                }
            }
        }
        $paidMonthKeys = array_unique($paidMonthKeys);
        $chargeableMonths = array_values(array_diff($selectedMonthKeys, $paidMonthKeys));

        $totalFee = 0.0;
        $totalConcession = 0.0;

        foreach ($student->feePlans as $feePlan) {
            $feeHead = $feePlan->feeHead;
            if (!$feeHead) {
                continue;
            }
            if (strtoupper((string) $feeHead->name) === 'LATE FEE') {
                continue;
            }

            $applicable = [];
            foreach ($monthKeys as $mk) {
                if ((int) ($feeHead->{$mk} ?? 0) === 1) {
                    $applicable[] = $mk;
                }
            }

            $common = array_intersect($chargeableMonths, $applicable);
            if (empty($common)) {
                continue;
            }

            $lineAmount = (float) $feePlan->value * count($common);
            $totalFee += $lineAmount;

            $concession = Concession::where('user_id', $student->id)
                ->where('fee_plan_id', $feePlan->id)
                ->value('concession_fee') ?? 0;

            $totalConcession += ((float) $concession) * count($common);
        }

        $totalRouteFee = 0.0;
        $routeName = optional($student->route)->routeName;
        if ($routeName && strtoupper($routeName) !== 'NA') {
            $routePlan = RouteFeePlan::where('routeName', $routeName)->first();
            if ($routePlan) {
                $routePaid = [];
                foreach ($allReceipts->where('feeHead', 'Transport') as $r) {
                    foreach ($monthKeys as $mk) {
                        if (!is_null($r->{$mk})) {
                            $routePaid[] = $mk;
                        }
                    }
                }
                $routePaid = array_unique($routePaid);
                $routeMonthsToPay = array_values(array_diff($chargeableMonths, $routePaid));
                if (!empty($routeMonthsToPay)) {
                    $totalRouteFee = (float) $routePlan->value * count($routeMonthsToPay);
                }
            }
        }
        $totalFee += $totalRouteFee;

        $lateFeePerMonth = (float) (
            FeePlan::whereHas('feeHead', function ($q) {
                $q->where('name', 'Late Fee');
            })->value('value') ?? 0
        );

        $lateFee = collect($chargeableMonths)
            ->filter(function ($mk) use ($monthKeys, $currentMonthIndex) {
                $idx = array_search($mk, $monthKeys, true);
                return $idx !== false && $idx < $currentMonthIndex;
            })
            ->count() * $lateFeePerMonth;

        $netFee = $totalFee - $totalConcession;
        $totalPayable = $netFee + $previousBalance + $lateFee - $totalConcession;

        return [
            'netFee' => round($netFee, 2),
            'oldBalance' => round($previousBalance, 2),
            'lateFee' => round($lateFee, 2),
            'concession' => round($totalConcession, 2),
            'totalPayable' => round($totalPayable, 2),
        ];
    }

    public function deleteTerm($id){
      try{
          $record = Term::find($id);

          $record->delete($record->id);

          return redirect('admin/allTerms')->with('delete','Term deleted successfully');
      }
      catch(Exception $e){
          return redirect('admin/allTerms/'.$id)->with('failed',"operation failed");

      }
    }

    public function allClasswork(){
      try{
        $classDatas = classwork::all()->sortByDesc('created_at');

        return view('admin.allClasswork', compact('classDatas'));
    }
    catch(Exception $e){
        return redirect('admin/allClasswork')->with('failed',"operation failed");

    }
    }

    public function edit_classwork(Request $request, $id ){
      try{
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

          return view('admin.edit_classwork', compact( 'classDatas','class','subject','title','id','terms','type','youtubeLink','studentReturn'));
        }

        catch(Exception $e){
      return redirect('admin/allClasswork/'.$id)->with('failed',"operation failed");
          }
        }

        public function editPdfClasswork(Request $request)
    {
            $data = $request->input();
            $id = $data['id'];
            $term = $data['selectTerm'];
            if(!(isset($data['selectTitle']))){
                return redirect('admin/edit_classwork/'.$id)->with('failed',"Try again, Please select title");
            }
           $title = $data['selectTitle'];

            try{
                $getClassSubs = DB::select('SELECT * FROM classworks WHERE id = ?' , [$data['id']]);
                //  dd($getClassSub->class);
                  foreach ($getClassSubs as $getClassSub) {
                      $class = $getClassSub->class;
                      $subject = $getClassSub->subject;
                  }
                  if(isset($data['studentWorkIsrequire'])){
                    $studentReturn = 1;
                    }else{
                    $studentReturn = 0;
                    }

                DB::table('classworks')
            ->where('id', $id)
            ->update([  'term' => $term,
                        'title' =>  $title,

                        'fileUrl' => 'https://gplmschool-dev-storage-vkmjgjn4dvol.s3.amazonaws.com/' . $class . '/' . $subject . '/' . $title . '/' . $data['fileName'],
                        'fileSize' => $request->file('file')->getSize(),
                        'studentReturn' => $studentReturn,
                        'type' => 'PDF',]);



                $file = $request->file('file');
                $imageName = $class . '/' . $subject . '/' . $title . '/' .  $data['fileName'];

                Storage::disk('s3')->put($imageName, file_get_contents($file));
                Storage::disk('s3')->setVisibility($imageName, 'public');

				return redirect('admin/allClasswork')->with('status','Record edited successfully');
			}
			catch(Exception $e){
				return redirect('admin/allClasswork')->with('failed',"operation failed");
			}

    }

    public function editImageClasswork(Request $request)
    {
            $data = $request->input();
            $id = $data['id'];
            $term = $data['selectTerm'];
            if(!(isset($data['selectTitle']))){
                return redirect('admin/edit_classwork/'.$id)->with('failed',"Try again, Please select title");
            }
           $title = $data['selectTitle'];

            try{
                $getClassSubs = DB::select('SELECT * FROM classworks WHERE id = ?' , [$data['id']]);
                //  dd($getClassSub->class);
                  foreach ($getClassSubs as $getClassSub) {
                      $class = $getClassSub->class;
                      $subject = $getClassSub->subject;
                  }
                  if(isset($data['imgStudentWorkIsrequire'])){
                    $studentReturn = 1;
                    }else{
                    $studentReturn = 0;
                    }

                DB::table('classworks')
            ->where('id', $id)
            ->update([  'term' => $term,

                        'title' =>  $title,
                        'fileUrl' => 'https://gplmschool-dev-storage-vkmjgjn4dvol.s3.amazonaws.com/' . $class . '/' . $subject . '/' . $title . '/' . $data['fileName'],
                        'fileSize' => $request->file('file')->getSize(),
                        'studentReturn' => $studentReturn,
                        'type' => 'IMG',]);



                $file = $request->file('file');
                $imageName = $class . '/' . $subject . '/' . $title . '/' .  $data['fileName'];

                Storage::disk('s3')->put($imageName, file_get_contents($file));
                Storage::disk('s3')->setVisibility($imageName, 'public');

				return redirect('admin/allClasswork')->with('status','Record edited successfully');
			}
			catch(Exception $e){
				return redirect('admin/allClasswork')->with('failed',"operation failed");
			}

    }

    public function editDocsClasswork(Request $request)
    {
            $data = $request->input();
            $id = $data['id'];
            $term = $data['selectTerm'];
            if(!(isset($data['selectTitle']))){
                return redirect('admin/edit_classwork/'.$id)->with('failed',"Try again, Please select title");
            }
           $title = $data['selectTitle'];

            try{
                $getClassSubs = DB::select('SELECT * FROM classworks WHERE id = ?' , [$data['id']]);
                //  dd($getClassSub->class);
                  foreach ($getClassSubs as $getClassSub) {
                      $class = $getClassSub->class;
                      $subject = $getClassSub->subject;
                  }
                  if(isset($data['docStudentWorkIsrequire'])){
                    $studentReturn = 1;
                    }else{
                    $studentReturn = 0;
                    }

                DB::table('classworks')
            ->where('id', $id)
            ->update([  'term' => $term,
                        'title' =>  $title,
                        'fileUrl' => 'https://gplmschool-dev-storage-vkmjgjn4dvol.s3.amazonaws.com/' . $class . '/' . $subject . '/' . $title . '/' . $data['fileName'],
                        'fileSize' => $request->file('file')->getSize(),
                        'studentReturn' => $studentReturn,
                        'type' => 'DOCS',]);



                $file = $request->file('file');
                $imageName = $class . '/' . $subject . '/' . $title . '/' .  $data['fileName'];

                Storage::disk('s3')->put($imageName, file_get_contents($file));
                Storage::disk('s3')->setVisibility($imageName, 'public');

				return redirect('admin/allClasswork')->with('status','Record edited successfully');
			}
			catch(Exception $e){
				return redirect('admin/allClasswork')->with('failed',"operation failed");
			}

    }


    public function  editYoutubeLink(Request $request)
    {
            $data = $request->input();
            $id = $data['id'];
            $term = $data['selectTerm'];
            if(!(isset($data['selectTitle']))){
                return redirect('admin/edit_classwork/'.$id)->with('failed',"Try again, Please select title");
            }
           $title = $data['selectTitle'];

            try{
                $getClassSubs = DB::select('SELECT * FROM classworks WHERE id = ?' , [$data['id']]);
                //  dd($getClassSub->class);
                  foreach ($getClassSubs as $getClassSub) {
                      $class = $getClassSub->class;
                      $subject = $getClassSub->subject;
                  }
                  if(isset($data['ytStudentWorkIsrequire'])){
                    $studentReturn = 1;
                    }else{
                    $studentReturn = 0;
                    }

                DB::table('classworks')
            ->where('id', $id)
            ->update([  'term' => $term,
                        'title' =>  $title,
                        'youtubeLink' => $data['youtubeLink'],
                        'studentReturn' => $studentReturn,
                        'type' => 'YOUTUBE',]);

				return redirect('admin/allClasswork')->with('status','Record edited successfully');
			}
			catch(Exception $e){
				return redirect('admin/allClasswork')->with('failed',"operation failed");
			}

    }

    public function classworkAttendence($id){


      $classworks= classwork::all()->where('id',$id);
      foreach($classworks as $classwork){
          $class = $classwork->class;
      }
      $users = User::all()->where('grade',$class);

      foreach($users as $user){
      foreach($user->readnotifications as $notification){
             $readNotications[] = $notification;
      }
      if(!(isset($readNotications))){
          $readNotications = NULL;
      }
      foreach($user->unreadnotifications as $notification){
          $unreadNotications[] = $notification;
   }
   if(!(isset($unreadNotications))){
      $unreadNotications = NULL;
  }
      }
          //dd($attendenceNotications);
          return view('admin/classworkAttendence', compact('readNotications','unreadNotications','id','users'));
  //        return view('teacher.createTitle', compact('subCodes','classCodes','classworks','subCode','classDatas','class','subject','id'));

      }

      public function studentReturnWork($id){

        $stuHomeworkUploads = stuHomeworkUpload::all()->where('titleId',$id)->sortBy('email');
       // dd($stuHomeworkUpload);
        foreach($stuHomeworkUploads as $stuHomeworkUpload){
            $class = $stuHomeworkUpload->class;
        }
        if(!(isset($class))){
            return back()->with('failed',"No record found");

        }
        $users = User::all()->where('grade',$class);

            return view('admin/studentReturnWork', compact('id','users','stuHomeworkUploads'));

        }

        public function deletePost($id){
          try{


                  $record = classwork::find($id);

                  $record->delete($record->id);

                  return redirect('admin/allClasswork')->with('delete','Record deleted successfully');
              }
              catch(Exception $e){
                  return redirect('admin/allClasswork')->with('failed',"operation failed");
              }
          }

          public function phpinfo(){
            return view('admin.phpinfo');

          }


}
