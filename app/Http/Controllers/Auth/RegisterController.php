<?php

namespace App\Http\Controllers\Auth;

use App\Category;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use App\FeePlan;
use App\RouteName;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    // protected $redirectTo = RouteServiceProvider::RegisterUser;
    protected function redirectTo()
        {
            if (Auth::guard('admin')->check()) {
                return route('admin.dashboard');
            } elseif (Auth::guard('cashier')->check()) {
                return route('cashier.dashboard');
            }
            return '/';
        }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth:admin');
    // }

    public function __construct()
        {
            $this->middleware(function ($request, $next) {
                if (!Auth::guard('admin')->check() && !Auth::guard('cashier')->check()) {
                    return redirect()->route('login'); // Redirect to login if neither admin nor cashier is authenticated
                }
                return $next($request);
            });
        }

    // protected function guard()
    // {
    //     return Auth::guard('admin');
    // }

    protected function guard()
        {
            if (Auth::guard('admin')->check()) {
                return Auth::guard('admin');
            } elseif (Auth::guard('cashier')->check()) {
                return Auth::guard('cashier');
            }
            return Auth::guard(); // Fallback to default guard
        }


    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {

        return Validator::make($data, [
            // 'admission_number' => ['required', 'string', 'max:255'],
            // 'name' => ['required', 'string', 'max:255'],
            // 'fName' => ['required', 'string', 'max:255'],
            // 'mName' => ['required', 'string', 'max:255'],
            // 'dob' => ['required', 'date'],
            // 'address' => ['required', 'string', 'max:255'],
            // 'mobile' => ['required', 'numeric', 'min:10'],
            // 'rfid' => [ 'string', 'max:12'],
            // 'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            // 'grade' => ['required', 'string', 'max:255'],
            // 'password' => ['required', 'string', 'min:8', 'confirmed'],

            'admission_number' => ['required', 'string', 'max:255'],
            'rollNo' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'fName' => ['required', 'string', 'max:255'],
            'mName' => ['required', 'string', 'max:255'],
            'dob' => ['nullable', 'date'],
            'address' => ['required', 'string', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:15'],
            'rfid' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users'],
            'grade' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer'],
            'section' => ['nullable', 'string', 'max:255'],
            'aadhar' => ['nullable', 'digits_between:12,16'],
            'pen' => ['nullable', 'digits_between:12,16'],
            'apaar' => ['nullable', 'digits_between:12,16'],
            'house' => ['nullable', 'string', 'max:255'],
            'caste' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'in:Male,Female,Other'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'school_status' => ['nullable', 'string', 'max:255'],
            'date_of_admission' => ['nullable', 'date'],
            'blood_group' => ['nullable', 'string', 'max:255'],
            'height' => ['nullable', 'numeric'],
            'weight' => ['nullable', 'numeric'],
            'family' => ['nullable', 'string'],
            'vision_left' => ['nullable', 'string', 'max:255'],
            'vision_right' => ['nullable', 'string', 'max:255'],
            'dental_hygiene' => ['nullable', 'string', 'max:255'],

        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
{

    $category = Category::where('category', $data['category'])->firstOrFail();
    $route = RouteName::where('routeName', $data['route'])->firstOrFail();

    if (!$category || !$route) {
        abort(400, 'Invalid category or route selected.');
    }

    // 1️⃣ Create the user first
    $user = User::create([
        'admission_number' => $data['admission_number'],
        'rollNo' => $data['rollNo'] ?? null,
        'name' => $data['name'],
        'fName' => $data['fName'],
        'mName' => $data['mName'],
        'dob' => $data['dob'] ?? null,
        'address' => $data['address'],
        'mobile' => $data['mobile'] ?? null,
        'rfid' => $data['rfid'] ?? null,
        'email' => $data['email'] ?? null,
        'grade' => $data['grade'] ?? null,
        'category_id' => $data['category_id'] ?? null,
        'section' => $data['section'] ?? null,
        'aadhar' => $data['aadhar'] ?? null,
        'pen' => $data['pen'] ?? null,
        'apaar' => $data['apaar'] ?? null,
        'house' => $data['house'] ?? null,
        'caste' => $data['caste'] ?? null,
        'gender' => $data['gender'] ?? null,
        'city' => $data['city'] ?? null,
        'state' => $data['state'] ?? null,
        'school_status' => $data['school_status'] ?? null,
        'date_of_admission' => $data['date_of_admission'] ?? null,
        'blood_group' => $data['blood_group'] ?? null,
        'height' => $data['height'] ?? null,
        'weight' => $data['weight'] ?? null,
        'family' => $data['family'] ?? null,
        'vision_left' => $data['vision_left'] ?? null,
        'vision_right' => $data['vision_right'] ?? null,
        'dental_hygiene' => $data['dental_hygiene'] ?? null,
        'password' => Hash::make($data['password']), // Ensure password is hashed
        'admission_number' => $data['admission_number'],
        'category_id' => $category->id,
        'route_id' => $route->id,
        'app_permission' => 1,
        'exam_permission' => 1
    ]);

    // 2️⃣ Find applicable fee plans based on user's grade and category
    $feePlans = FeePlan::where('class', $data['grade'])
                        ->where('category', $data['category'])
                        ->pluck('id'); // Get fee plan IDs

    // 3️⃣ Attach fee plans to the user
    if ($feePlans->isNotEmpty()) {
        $user->feePlans()->attach($feePlans);
    }

    return $user;
}


}
