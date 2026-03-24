<?php

namespace App\Http\Controllers\Users\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Attendance;
use App\User;
use App\subCode;
use App\Teacher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TeacherAttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:teacher');
    }

    public function index()
    {
        $teacher = Auth::guard('teacher')->user();

        $subCodes = method_exists($teacher, 'subCodes') ? $teacher->subCodes() : subCode::where('teacher_id', $teacher->id)->get();
        $classes  = collect($subCodes)->pluck('class')->unique()->values();

        return view('teacher.attendance.index', compact('classes'));
    }

    /**
     * Show the "View Attendance" selection form (choose class + month)
     */
    public function viewForm()
    {
        $teacher = Auth::guard('teacher')->user();
        $subCodes = method_exists($teacher, 'subCodes') ? $teacher->subCodes() : subCode::where('teacher_id', $teacher->id)->get();
        $classes  = collect($subCodes)->pluck('class')->unique()->values();

        return view('teacher.attendance.view', compact('classes'));
    }

    /**
     * Handle POST from viewForm and redirect to the monthly attendance view.
     */
    public function viewRedirect(Request $request)
    {
        $data = $request->validate([
            'class' => 'required|string',
            'month' => 'required|date_format:Y-m',
        ]);

        [$year, $month] = explode('-', $data['month']);

        return redirect()->route('teacher.attendance.month', [
            'class' => $data['class'],
            'year'  => intval($year),
            'month' => intval($month),
        ]);
    }

    public function showStudents(Request $request)
    {
        $teacher = Auth::guard('teacher')->user();

        $data = $request->validate([
            'class' => 'required|string',
            'date'  => 'required|date',
        ]);
        $class = $data['class'];
        $date  = Carbon::parse($data['date'])->toDateString();

        $teacherClasses = method_exists($teacher, 'subCodes') ? $teacher->subCodes()->pluck('class')->unique()->toArray() : subCode::where('teacher_id', $teacher->id)->pluck('class')->toArray();
        if (! in_array($class, $teacherClasses, true)) {
            abort(403, 'You are not assigned to this class.');
        }

        $students = User::where('grade', $class)->orderBy('name')->get();

        $existing = Attendance::where('class', $class)
            ->whereDate('date', $date)
            ->get()
            ->keyBy('student_id');

        return view('teacher.attendance.mark', compact('class', 'date', 'students', 'existing'));
    }

    public function save(Request $request)
    {
        // Log authentication and session info at the start of the save flow to help
        // debug redirect/loop issues when the POST appears to lose the teacher session.
        // Log::info("TeacherAttendanceController@save: start", [
        //     'teacher_authenticated' => Auth::guard('teacher')->check(),
        //     'session_id' => session()->getId(),
        //     'cookies_header' => $request->header('cookie'),
        // ]);


        $teacher = Auth::guard('teacher')->user();

        $data = $request->validate([
            'class'   => 'required|string',
            'date'    => 'required|date',
            'status'  => 'array',
            'leave'   => 'array',
            'absent'  => 'array',
            // 'status.*' => 'in:P,A,L',
        ]);
        $class = $data['class'];
        $date  = Carbon::parse($data['date'])->toDateString();

        // Primary source: hidden status inputs -> associative array student_id => status
        $statuses = $data['status'] ?? [];
        $leaveList = $data['leave'] ?? [];
        $absentList = $data['absent'] ?? [];

        $teacherClasses = method_exists($teacher, 'subCodes') ? $teacher->subCodes()->pluck('class')->unique()->toArray() : subCode::where('teacher_id', $teacher->id)->pluck('class')->toArray();
        if (! in_array($class, $teacherClasses, true)) {
            abort(403, 'You are not assigned to this class.');
        }

        $students = User::where('grade', $class)->pluck('id')->toArray();
        $absentStudentIds = [];

        DB::transaction(function () use ($students, $statuses, $leaveList, $absentList, $class, $date, $teacher, &$absentStudentIds) {
            foreach ($students as $studentId) {
                // Determine status preference:
                // 1) If hidden status provided, use it.
                // 2) Else if student id is in absent list, 'A'.
                // 3) Else if in leave list, 'L'.
                // 4) Else default to 'P'.
                if (isset($statuses[$studentId])) {
                    $status = $statuses[$studentId];
                } elseif (in_array($studentId, $absentList, true)) {
                    $status = 'A';
                } elseif (in_array($studentId, $leaveList, true)) {
                    $status = 'L';
                } else {
                    $status = 'P';
                }

                // Sanitize/normalize allowable statuses
                if (! in_array($status, ['P', 'A', 'L'], true)) {
                    $status = 'P';
                }

                Attendance::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'class'      => $class,
                        'date'       => $date,
                    ],
                    [
                        'teacher_id' => $teacher->id,
                        'status'     => $status,
                    ]
                );

                if ($status === 'A') {
                    $absentStudentIds[] = (int) $studentId;
                }
            }
        });

        $absentStudentIds = array_values(array_unique($absentStudentIds));
        if (! empty($absentStudentIds)) {
            $this->sendAbsentNotifications($absentStudentIds, $class, $date);
        }

        Log::info("TeacherAttendanceController@save: redirecting to absentPdf", ['teacher_id' => $teacher->id ?? null, 'class' => $class, 'date' => $date]);

        // After saving, redirect to the monthly attendance view for the given
        // class and month instead of triggering a PDF download.
        $year = Carbon::parse($date)->year;
        $month = Carbon::parse($date)->month;

        return redirect()->route('teacher.attendance.month', [
            'class' => $class,
            'year'  => $year,
            'month' => $month,
        ]);
    }

    private function sendAbsentNotifications(array $studentIds, string $class, string $date): void
    {
        $projectId = env('FCM_PROJECT_ID');
        $serviceAccountPath = env('FCM_SERVICE_ACCOUNT');

        if (empty($projectId) || empty($serviceAccountPath)) {
            Log::warning('FCM_PROJECT_ID or FCM_SERVICE_ACCOUNT is missing. Skipping absent notifications.');
            return;
        }

        $accessToken = $this->getFcmAccessToken($serviceAccountPath);
        if (empty($accessToken)) {
            Log::error('Failed to build FCM access token. Skipping absent notifications.');
            return;
        }

        $tokenRows = DB::table('device_tokens')
            ->whereIn('user_id', $studentIds)
            ->select('user_id', 'token')
            ->get();

        if ($tokenRows->isEmpty()) {
            return;
        }

        $tokensByUser = $tokenRows
            ->groupBy('user_id')
            ->map(function ($rows) {
                return $rows->pluck('token')->filter()->unique()->values()->toArray();
            });

        $students = User::whereIn('id', $studentIds)->get(['id', 'name'])->keyBy('id');
        $formattedDate = Carbon::parse($date)->format('d M Y');
        $endpoint = 'https://fcm.googleapis.com/v1/projects/' . $projectId . '/messages:send';

        foreach ($studentIds as $studentId) {
            $tokens = $tokensByUser->get($studentId, []);
            if (empty($tokens)) {
                continue;
            }

            $studentName = optional($students->get($studentId))->name ?? 'Student';

            try {
                foreach ($tokens as $token) {
                    $response = Http::timeout(10)->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ])->post($endpoint, [
                    'message' => [
                        'token' => $token,
                        'notification' => [
                            'title' => 'Attendance Alert',
                            'body' => $studentName . ' marked absent on ' . $formattedDate,
                        ],
                        'data' => [
                            'type' => 'attendance_absent',
                            'class' => $class,
                            'date' => $date,
                            'student_id' => (string) $studentId,
                        ],
                        'android' => [
                            'priority' => 'HIGH',
                        ],
                    ],
                ]);

                    if (! $response->successful()) {
                        Log::error('Absent notification request failed', [
                            'student_id' => $studentId,
                            'status' => $response->status(),
                            'body' => $response->body(),
                        ]);
                    }
                }
            } catch (\Throwable $e) {
                Log::error('Failed to send absent notification', [
                    'student_id' => $studentId,
                    'error' => $e->getMessage(),
                ]);
            }
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

    private function base64UrlEncode(string $input): string
    {
        return rtrim(strtr(base64_encode($input), '+/', '-_'), '=');
    }

    /**
     * Show a monthly attendance matrix for a class.
     * URL: /teacher/attendance/manage/{class}/{year}/{month}/month
     */
    public function monthView($class, $year, $month)
    {
        $teacher = Auth::guard('teacher')->user();

        $teacherClasses = method_exists($teacher, 'subCodes') ? $teacher->subCodes()->pluck('class')->unique()->toArray() : subCode::where('teacher_id', $teacher->id)->pluck('class')->toArray();
        if (! in_array($class, $teacherClasses, true)) {
            abort(403, 'You are not assigned to this class.');
        }

        $students = User::where('grade', $class)->orderBy('name')->get();

        $start = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $daysInMonth = $start->daysInMonth;
        $days = range(1, $daysInMonth);

        $attendances = Attendance::where('class', $class)
            ->whereDate('date', '>=', $start->toDateString())
            ->whereDate('date', '<=', $end->toDateString())
            ->get();
        // Group attendances by student_id then by day of month for quick lookup
        $attByStudent = $attendances->groupBy('student_id')->map(function ($rows) {
            return $rows->keyBy(function ($r) {
                return Carbon::parse($r->date)->day;
            })->map(function ($r) {
                return $r->status;
            })->toArray();
        })->toArray();

        // Compute per-student present totals and per-day totals using only DB records
        $presentTotals = [];
        $dayTotals = array_fill(1, $daysInMonth, 0);

        foreach ($students as $student) {
            $sid = $student->id;
            $presentCount = 0;
            for ($d = 1; $d <= $daysInMonth; $d++) {
                // Only consider actual attendance records from DB
                $status = $attByStudent[$sid][$d] ?? null;

                if ($status === 'P') {
                    $presentCount++;
                    $dayTotals[$d] = ($dayTotals[$d] ?? 0) + 1;
                }
            }
            $presentTotals[$sid] = $presentCount;
        }

        return view('teacher.attendance.month', compact('class', 'year', 'month', 'students', 'days', 'attByStudent', 'start', 'end', 'presentTotals', 'dayTotals'));
    }

    /**
     * Show a simple form to pick class + date to view a single day's attendance.
     */
    public function dayForm()
    {
        $teacher = Auth::guard('teacher')->user();
        $subCodes = method_exists($teacher, 'subCodes') ? $teacher->subCodes() : subCode::where('teacher_id', $teacher->id)->get();
        $classes  = collect($subCodes)->pluck('class')->unique()->values();

        return view('teacher.attendance.day_select', compact('classes'));
    }

    /**
     * Handle the day form POST and redirect to the dayView route.
     */
    public function dayRedirect(Request $request)
    {
        $data = $request->validate([
            'class' => 'required|string',
            'date'  => 'required|date',
        ]);

        $date = Carbon::parse($data['date'])->toDateString();

        return redirect()->route('teacher.attendance.day', [
            'class' => $data['class'],
            'date'  => $date,
        ]);
    }

    /**
     * Show attendance for a single selected date for a class.
     * URL: /attendance/manage/{class}/{date}/day
     */
    public function dayView($class, $date)
    {
        $teacher = Auth::guard('teacher')->user();

        $teacherClasses = method_exists($teacher, 'subCodes') ? $teacher->subCodes()->pluck('class')->unique()->toArray() : subCode::where('teacher_id', $teacher->id)->pluck('class')->toArray();
        if (! in_array($class, $teacherClasses, true)) {
            abort(403, 'You are not assigned to this class.');
        }

        $students = User::where('grade', $class)->orderBy('name')->get();

        $date = Carbon::parse($date)->toDateString();

        $attendances = Attendance::where('class', $class)
            ->whereDate('date', $date)
            ->get()
            ->keyBy('student_id')
            ->map(function ($r) { return $r->status; })
            ->toArray();

        // compute totals for the day
        $totals = ['P' => 0, 'L' => 0, 'A' => 0];
        foreach ($attendances as $status) {
            if (isset($totals[$status])) $totals[$status]++;
        }

        return view('teacher.attendance.day', compact('class', 'date', 'students', 'attendances', 'totals'));
    }

    public function absentPdf($class, $date)
    {
        Log::info("TeacherAttendanceController@absentPdf called", ['class' => $class, 'date' => $date]);
        $date = Carbon::parse($date)->toDateString();

        // Order by the student's name (stored on users.name). Use a join so
        // the ORDER BY is executed by the database instead of trying to sort
        // by a non-existent `name` column on the attendances table.
        $absentRows = Attendance::with('student')
            ->where('attendances.class', $class)
            ->whereDate('attendances.date', $date)
            ->where('attendances.status', 'A')
            ->join('users', 'attendances.student_id', '=', 'users.id')
            ->orderByRaw('LOWER(users.name) ASC')
            ->select('attendances.*')
            ->get();

        if ($absentRows->isEmpty()) {
            Log::info("TeacherAttendanceController@absentPdf: no absent rows, redirecting to index", ['class' => $class, 'date' => $date]);
            // Redirect to the attendance index rather than 'back' to avoid potential redirect loops
            // where the previous URL may be a POST or a guarded URL that triggers auth redirects.
            return redirect()->route('teacher.attendance.index')->with('failed', "No absent students found for {$class} on {$date}, or attendance not marked yet.");
        }

        // If the PDF facade / package is not installed, fall back to rendering the
        // same view as HTML so the user can at least see the absent list and
        // avoid a fatal "Class not found" error. Recommend installing
        // barryvdh/laravel-dompdf to enable PDF downloads.
        if (! class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            Log::warning('TeacherAttendanceController@absentPdf: Barryvdh\DomPDF not installed; returning HTML view instead of PDF.');
            // Return the view so the user sees the absent list in the browser.
            return response()->view('teacher.attendance.absent_pdf', [
                'class'      => $class,
                'date'       => $date,
                'absentRows' => $absentRows,
            ], 200)->header('Content-Type', 'text/html');
        }

        $pdf = PDF::loadView('teacher.attendance.absent_pdf', [
            'class'      => $class,
            'date'       => $date,
            'absentRows' => $absentRows,
        ])->setPaper('A4', 'portrait');

        $fileName = "Absent_{$class}_{$date}.pdf";

        return $pdf->download($fileName);
    }
}
