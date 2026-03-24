<?php

namespace App\Http\Controllers\Users\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Attendance;
use App\User;
use App\subCode;
use App\Section;
use App\StudentMaster;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::guard('admin')->check() && !Auth::guard('cashier')->check()) {
                return redirect()->route('login')->with('error', 'You are not authorized to access this page.');
            }
            return $next($request);
        });
    }

    /**
     * Admin index - shows available classes (unique from subCode)
     */
    public function index()
    {
        $subCodes = subCode::all();
        $classes  = $subCodes->pluck('class')->unique()->values();

        return view('admin.attendance.index', compact('classes'));
    }

    /**
     * Show "View Attendance" form (choose class + month)
     */
    public function viewForm()
    {
        $subCodes = subCode::all();
        $classes  = $subCodes->pluck('class')->unique()->values();

        return view('admin.attendance.view', compact('classes'));
    }

    /**
     * Handle POST from view form and redirect to month view
     */
    public function viewRedirect(Request $request)
    {
        $data = $request->validate([
            'class' => 'required|string',
            'month' => 'required|date_format:Y-m',
            'section' => 'nullable|integer|exists:sections,id',
        ]);

        [$year, $month] = explode('-', $data['month']);

        return redirect()->route('admin.attendance.month', [
            'class' => $data['class'],
            'year'  => intval($year),
            'month' => intval($month),
            'section' => $data['section'] ?? null,
        ]);
    }

    /**
     * Show students to mark attendance for a given class + date
     * POST input expected: class (string), date (date), status[], leave[], absent[]
     */
    public function showStudents(Request $request)
    {
        $data = $request->validate([
            'class' => 'required|string',
            'date'  => 'required|date',
            'section' => 'nullable|integer|exists:sections,id',
        ]);

        $class = $data['class'];
        $date  = Carbon::parse($data['date'])->toDateString();
        $sectionId = $data['section'] ?? null;

        // fetch students for the requested class (and section if provided)
        $studentsQuery = User::where('grade', $class);
        if ($sectionId) $studentsQuery->where('section_id', $sectionId);
        $students = $studentsQuery->orderBy('name')->get();

        // existing attendance rows keyed by student_id
        $existing = Attendance::where('class', $class)
            ->when($sectionId, fn($q) => $q->where('section_id', $sectionId))
            ->whereDate('date', $date)
            ->get()
            ->keyBy('student_id');

        return view('admin.attendance.mark', compact('class', 'date', 'students', 'existing', 'sectionId'));
    }

    /**
     * Save attendance — admin can mark for any class (works like Teacher.save)
     */
    public function save(Request $request)
    {
        $data = $request->validate([
            'class'   => 'required|string',
            'date'    => 'required|date',
            'status'  => 'array',
            'leave'   => 'array',
            'absent'  => 'array',
            'section' => 'nullable|integer|exists:sections,id',
        ]);


        $class = $data['class'];
        $date  = Carbon::parse($data['date'])->toDateString();
        $sectionId = $data['section'] ?? null;
        $statuses = $data['status'] ?? [];
        $leaveList = $data['leave'] ?? [];
        $absentList = $data['absent'] ?? [];

        // get list of student ids for the class/section
        $students = User::where('grade', $class)
            ->when($sectionId, fn($q) => $q->where('section_id', $sectionId))
            ->pluck('id')
            ->toArray();

        DB::transaction(function () use ($students, $statuses, $leaveList, $absentList, $class, $date, $sectionId) {
            foreach ($students as $studentId) {
                if (isset($statuses[$studentId])) {
                    $status = $statuses[$studentId];
                } elseif (in_array($studentId, $absentList, true)) {
                    $status = 'A';
                } elseif (in_array($studentId, $leaveList, true)) {
                    $status = 'L';
                } else {
                    $status = 'P';
                }

                if (! in_array($status, ['P','A','L'], true)) $status = 'P';

                Attendance::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'class'      => $class,
                        'date'       => $date,
                    ],
                    [
                        'teacher_id' => null, // admin id here; keep field semantics
                        'section_id' => $sectionId,
                        'status'     => $status,
                    ]
                );
            }
        });

        // redirect to month view
        $year = Carbon::parse($date)->year;
        $month = Carbon::parse($date)->month;

        return redirect()->route('admin.attendance.month', [
            'class' => $class,
            'year'  => $year,
            'month' => $month,
            'section' => $sectionId,
        ])->with('status', 'Attendance saved successfully.');
    }

    /**
     * Show monthly matrix for admin (similar to teacher.monthView)
     */
    public function monthView($class, $year, $month)
    {
        $sectionId = request()->query('section', null);

        $studentsQuery = User::all()->where('grade', $class);
        if ($sectionId) $studentsQuery->where('section_id', $sectionId);

        // Normalize and sort $studentsQuery whether it's a Builder or a Collection.
        if ($studentsQuery instanceof \Illuminate\Database\Eloquent\Builder) {
            // It's a query builder — sort in the DB and fetch
            $students = $studentsQuery->orderBy('name')->get();
        } else {
            // It's already a Collection (or array) — convert to Collection and sort in PHP
            $students = collect($studentsQuery)->sortBy(function($s) {
                // if student has a related user, prefer that name; otherwise fallback to name property
                if (isset($s->user) && isset($s->user->name)) {
                    return strtolower($s->user->name);
                }
                return strtolower($s->name ?? $s->roll_no ?? '');
            })->values();
        }
        $start = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();
        $daysInMonth = $start->daysInMonth;
        $days = range(1, $daysInMonth);

        $attendances = Attendance::where('class', $class)
            ->when($sectionId, fn($q) => $q->where('section_id', $sectionId))
            ->whereDate('date', '>=', $start->toDateString())
            ->whereDate('date', '<=', $end->toDateString())
            ->get();

        $attByStudent = $attendances->groupBy('student_id')->map(function ($rows) {
            return $rows->keyBy(function ($r) { return Carbon::parse($r->date)->day; })
                        ->map(function ($r) { return $r->status; })
                        ->toArray();
        })->toArray();

        $presentTotals = [];
        $dayTotals = array_fill(1, $daysInMonth, 0);

        foreach ($students as $student) {
            $sid = $student->id;
            $presentCount = 0;
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $status = $attByStudent[$sid][$d] ?? null;
                if ($status === 'P') {
                    $presentCount++;
                    $dayTotals[$d] = ($dayTotals[$d] ?? 0) + 1;
                }
            }
            $presentTotals[$sid] = $presentCount;
        }

        return view('admin.attendance.month', compact(
            'class', 'year', 'month', 'students', 'days', 'attByStudent', 'start', 'end', 'presentTotals', 'dayTotals'
        ));
    }

    /**
     * Day form (select class+date)
     */
    public function dayForm()
    {
        $subCodes = subCode::all();
        $classes  = $subCodes->pluck('class')->unique()->values();
        return view('admin.attendance.day_select', compact('classes'));
    }

    /**
     * Day redirect
     */
    public function dayRedirect(Request $request)
    {
        $data = $request->validate([
            'class' => 'required|string',
            'date'  => 'required|date',
            'section' => 'nullable|integer|exists:sections,id',
        ]);

        $date = Carbon::parse($data['date'])->toDateString();

        return redirect()->route('admin.attendance.day', [
            'class' => $data['class'],
            'date'  => $date,
            'section' => $data['section'] ?? null,
        ]);
    }

    /**
     * Day view (list)
     */
    public function dayView($class, $date)
    {
        $sectionId = request()->query('section', null);
        $studentsQuery = User::all()->where('grade', $class);
        if ($sectionId) $studentsQuery->where('section_id', $sectionId);
// Normalize and sort $studentsQuery whether it's a Builder or a Collection.
        if ($studentsQuery instanceof \Illuminate\Database\Eloquent\Builder) {
            // It's a query builder — sort in the DB and fetch
            $students = $studentsQuery->orderBy('name')->get();
        } else {
            // It's already a Collection (or array) — convert to Collection and sort in PHP
            $students = collect($studentsQuery)->sortBy(function($s) {
                // if student has a related user, prefer that name; otherwise fallback to name property
                if (isset($s->user) && isset($s->user->name)) {
                    return strtolower($s->user->name);
                }
                return strtolower($s->name ?? $s->roll_no ?? '');
            })->values();
        }
        $date = Carbon::parse($date)->toDateString();

        $attendances = Attendance::where('class', $class)
            ->when($sectionId, fn($q) => $q->where('section_id', $sectionId))
            ->whereDate('date', $date)
            ->get()
            ->keyBy('student_id')
            ->map(fn($r) => $r->status)
            ->toArray();

        $totals = ['P' => 0, 'L' => 0, 'A' => 0];
        foreach ($attendances as $status) {
            if (isset($totals[$status])) $totals[$status]++;
        }

        return view('admin.attendance.day', compact('class', 'date', 'students', 'attendances', 'totals'));
    }

    /**
     * Absent PDF (admin)
     */
    public function absentPdf($class, $date)
    {
        Log::info("AdminAttendanceController@absentPdf called", ['class' => $class, 'date' => $date]);
        $date = Carbon::parse($date)->toDateString();

        $absentRows = Attendance::with('student')
            ->where('attendances.class', $class)
            ->whereDate('attendances.date', $date)
            ->where('attendances.status', 'A')
            ->join('users', 'attendances.student_id', '=', 'users.id')
            ->orderByRaw('LOWER(users.name) ASC')
            ->select('attendances.*')
            ->get();

        if ($absentRows->isEmpty()) {
            return redirect()->route('admin.attendance.index')->with('failed', "No absent students found for {$class} on {$date}, or attendance not marked yet.");
        }

        if (! class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            return response()->view('admin.attendance.absent_pdf', [
                'class' => $class,
                'date' => $date,
                'absentRows' => $absentRows,
            ], 200)->header('Content-Type', 'text/html');
        }

        $pdf = PDF::loadView('admin.attendance.absent_pdf', [
            'class' => $class,
            'date' => $date,
            'absentRows' => $absentRows,
        ])->setPaper('A4', 'portrait');

        $fileName = "Absent_{$class}_{$date}.pdf";

        return $pdf->download($fileName);
    }



/**
 * Show form to select number of days and end date.
 */
public function continuousAbsentForm()
{
    // default to last 3 days
    $defaultDays = 3;
    $defaultEnd = now()->toDateString();

    return view('admin.attendance.continuous_absent', [
        'days' => $defaultDays,
        'end_date' => $defaultEnd,
        'results' => null,
    ]);
}


/**
 * Show students who were absent for ALL of the last N days (ending at end_date).
 * Request: days (int), end_date (date)
 */
public function continuousAbsentResults(Request $request)
{
    $data = $request->validate([
        'days' => 'required|integer|min:1|max:30',
        'end_date' => 'required|date',
    ]);

    $days = (int) $data['days'];
    $endDate = Carbon::parse($data['end_date'])->startOfDay();
    $startDate = $endDate->copy()->subDays($days - 1)->toDateString(); // inclusive
    $endDateStr = $endDate->toDateString();

    // quick diagnostic: build the date array (for debug or showing)
    $dates = [];
    for ($i = 0; $i < $days; $i++) {
        $dates[] = Carbon::parse($startDate)->addDays($i)->toDateString();
    }

    // We know your attendances table has student_id (not student_master_id).
    // Count DISTINCT DATE(date) per student where status indicates absent.
    $absentStudentIds = DB::table('attendances')
        ->select('student_id', DB::raw('COUNT(DISTINCT DATE(`date`)) as cnt'))
        ->whereBetween(DB::raw('DATE(`date`)'), [$startDate, $endDateStr])
        ->where(function($q){
            $q->where('status', 'A')
              ->orWhereRaw('LOWER(status) = ?', ['absent'])
              ->orWhereRaw('LOWER(status) = ?', ['a']);
        })
        ->groupBy('student_id')
        ->havingRaw('cnt = ?', [$days])
        ->pluck('student_id')
        ->toArray();

    // If none found, return empty results
    if (empty($absentStudentIds)) {
        return view('admin.attendance.continuous_absent', [
            'days' => $days,
            'end_date' => $endDateStr,
            'results' => collect(),
            'dates' => $dates,
        ]);
    }

    // Fetch user details (name, father name fName, grade as class, mobile)
    $users = \App\User::whereIn('id', $absentStudentIds)
        ->select('id','name','fName','grade','mobile')
        ->orderBy('grade')->orderBy('name')
        ->get();

    // Optional: fetch the exact absence dates per student for verification
    $absDatesByStudent = DB::table('attendances')
        ->select('student_id', DB::raw('DATE(`date`) as d'))
        ->whereIn('student_id', $absentStudentIds)
        ->whereBetween(DB::raw('DATE(`date`)'), [$startDate, $endDateStr])
        ->where(function($q){
            $q->where('status', 'A')
              ->orWhereRaw('LOWER(status) = ?', ['absent'])
              ->orWhereRaw('LOWER(status) = ?', ['a']);
        })
        ->orderBy('student_id')->orderBy('d')
        ->get()
        ->groupBy('student_id')
        ->map(function($rows){
            return $rows->pluck('d')->toArray();
        })->toArray();

    // Build results for the view
    $results = $users->map(function($u) use ($absDatesByStudent, $days) {
        return (object)[
            'id' => $u->id,
            'name' => $u->name,
            'father_name' => $u->fName ?? '',
            'class' => $u->grade ?? '',
            'mobile' => $u->mobile ?? '',
            'days' => $days,
            'absent_dates' => $absDatesByStudent[$u->id] ?? [],
        ];
    });

    return view('admin.attendance.continuous_absent', [
        'days' => $days,
        'end_date' => $endDateStr,
        'results' => $results,
        'dates' => $dates,
    ]);
}


/**
 * Show classes attendance status for a given date (defaults to today).
 * Renders the blade at resources/views/admin/attendance/classes_status.blade.php
 */
public function classesStatus(Request $request)
{
    // selected date (Y-m-d)
    $date = $request->query('date', Carbon::today()->toDateString());

    // get classes list: prefer subCode classes, fallback to distinct grades from users
    $classes = \App\subCode::pluck('class')->unique()->filter()->values();
    if ($classes->isEmpty()) {
        $classes = \App\User::select('grade')->distinct()->pluck('grade')->filter()->values();
    }

    // fetch attendance counts grouped by class and status for the date
    $rows = DB::table('attendances')
        ->select('class', 'status', DB::raw('COUNT(*) as cnt'))
        ->whereDate('date', $date)
        ->groupBy('class', 'status')
        ->get();

    // build quick lookup: $counts[class][status] = cnt
    $counts = [];
    foreach ($rows as $r) {
        $c = (string) $r->class;
        $s = (string) $r->status;
        $counts[$c][$s] = (int) $r->cnt;
    }

    // fetch student totals per class in one query
    $totals = \App\User::select('grade', DB::raw('COUNT(*) as cnt'))
        ->groupBy('grade')
        ->pluck('cnt', 'grade')
        ->toArray();

    // build attendanceSummary structure expected by the blade
    $attendanceSummary = [];
    foreach ($classes as $class) {
        $present = ($counts[$class]['P'] ?? 0) + ($counts[$class]['p'] ?? 0);
        // treat 'A' and case-insensitive 'absent' as absent counts
        $absent = ($counts[$class]['A'] ?? 0) + ($counts[$class]['a'] ?? 0) + ($counts[$class]['absent'] ?? 0);
        $totalRecorded = array_sum($counts[$class] ?? []);
        $attendanceSummary[$class] = [
            'done' => $totalRecorded > 0,
            'present' => $present,
            'absent' => $absent,
            'total' => array_key_exists($class, $totals) ? (int) $totals[$class] : null,
        ];
    }

    return view('admin.attendance.classes_status', [
        'date' => $date,
        'classes' => $classes,
        'attendanceSummary' => $attendanceSummary,
    ]);
}

/**
 * CSV export for a particular class and date.
 * Query params: class (required), date (optional, defaults to today)
 */
public function classCsv(Request $request)
{
    $class = $request->query('class');
    if (! $class) {
        return redirect()->back()->with('failed', 'Please specify class parameter.');
    }

    $date = $request->query('date', Carbon::today()->toDateString());

    // Fetch attendance records for the class/date joined with users
    $rows = DB::table('attendances as a')
        ->leftJoin('users as u', 'a.student_id', '=', 'u.id')
        ->select('a.student_id', 'u.name', 'u.fName', 'u.grade', 'u.mobile', DB::raw('DATE(a.date) as date'), 'a.status')
        ->where('a.class', $class)
        ->whereDate('a.date', $date)
        ->orderBy('u.grade')->orderBy('u.name')
        ->get();

    $filename = "attendance_{$class}_" . str_replace('-', '', $date) . ".csv";

    $response = new StreamedResponse(function() use ($rows) {
        $handle = fopen('php://output', 'w');
        // header row
        fputcsv($handle, ['Student ID', 'Name', 'Father Name', 'Class', 'Mobile', 'Date', 'Status']);
        foreach ($rows as $r) {
            fputcsv($handle, [
                $r->student_id,
                $r->name,
                $r->fName,
                $r->grade,
                $r->mobile,
                $r->date,
                $r->status,
            ]);
        }
        fclose($handle);
    });

    $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
    $response->headers->set('Content-Disposition', "attachment; filename={$filename}");

    return $response;
}


}
