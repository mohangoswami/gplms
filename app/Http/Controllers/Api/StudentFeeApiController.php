<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Receipt;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\subCode;
use App\FeePlan;
use App\User;
use App\flashNews;
use App\RouteName;
use App\routeFeePlan;
use App\Concession;
use Illuminate\Support\Facades\Log;

class StudentFeeApiController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user(); // authenticated with token

        $receipts = Receipt::where('user_id', $user->id)
            ->orderBy('receiptId', 'desc')
            ->get()
            ->groupBy('receiptId');

        $data = [];

        foreach ($receipts as $receiptId => $group) {

            $first = $group->first(); // one record for meta

            // collect paid months from any row
            $monthColumns = [
                'jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'
            ];

            $months = [];
            foreach ($monthColumns as $m) {
                if (!empty($group->first()->{$m}) && $group->sum($m) > 0) {
                    $months[] = strtoupper($m);
                }
            }

            $data[] = [
                'receiptId'      => $receiptId,
                'date'           => Carbon::parse($first->date)->format('d M Y'),
                'monthLabel'     => Carbon::parse($first->date)->format('F Y'),
                'months'         => $months,
                'oldBalance'     => (float) $first->oldBalance,
                'balance'        => $first->balance ?? 0,
                'receivedAmount' => (float) $first->receivedAmt,
                'concession'     => (float) $first->concession,
                'lateFee'        => (float) $first->lateFee,
                'netFee'         => (float) $first->netFee,
                'viewUrl'        => url("/student/printReceipt/{$receiptId}")
            ];
        }

        return response()->json([
            'ok'   => true,
            'data' => $data
        ], 200);
    }


    public function pdf($id, Request $request)
{
    try {
        $user = $request->user(); // Sanctum token user

        // All receipts for this ID
        $receipts = Receipt::where('receiptId', $id)->get();
        if ($receipts->isEmpty()) {
            return response()->json(['ok' => false, 'message' => 'Receipt not found'], 404);
        }

        $receipt = $receipts->first();

        // ❗ Security: Ensure the logged-in student owns this receipt
        if ($receipt->user_id != $user->id) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 403);
        }

        // months list
        $monthsList = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];

        // Prepare "prints" array same as web view
        $prints = collect($receipts)->map(function ($r) use ($monthsList) {
            $paidMonths = [];
            foreach ($monthsList as $m) {
                if (!is_null($r->{$m})) {
                    $paidMonths[] = strtoupper($m);
                }
            }

            return [
                'feeHead' => $r->feeHead,
                'receiptId' => $r->receiptId,
                'date' => $r->date,
                'paidMonths' => implode(', ', $paidMonths),
                'oldBalance' => $r->oldBalance,
                'gTotal' => $r->total,
                'lateFee' => $r->lateFee,
                'concession' => $r->concession,
                'netFee' => $r->netFee,
                'receivedAmt' => $r->receivedAmt,
                'balance' => $r->balance,
                'paymentType' => $r->paymentMode,
                'bankName' => $r->bankName,
                'chequeNo' => $r->chequeNo,
                'chqDate' => $r->chequeDate,
                'remark' => $r->remarks,
            ];
        });

        // Render same blade as HTML
        $html = view('student.fee.payment-invoice', [
            'user'      => $user,
            'prints'    => $prints,
            'receipt'   => $receipt,
            'receipts'  => $receipts
        ])->render();

        // Convert to PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('a4', 'portrait');

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="receipt_'.$id.'.pdf"',
        ]);

    } catch (\Throwable $e) {
        return response()->json([
            'ok' => false,
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ], 500);
    }
}


        public function feeDetailsApi(Request $request)
{
    /* =====================================================
     * 1️⃣ Validate request
     * ===================================================== */
    $data = $request->validate([
        'admission_no' => ['required', 'string'],
        'months'       => ['required', 'array', 'min:1'],
        'months.*'     => ['string'],   // ['Apr','May',...]
        'oldBalance'   => ['nullable', 'numeric'],
    ]);

    /* =====================================================
     * 2️⃣ Resolve student (only self-access)
     * ===================================================== */
    $student = User::with(['route', 'category', 'feePlans.feeHead'])
        ->where('admission_number', $data['admission_no'])
        ->first();

    if (!$student) {
        $student = Auth::user();
    }

    abort_unless($student, 404);

    // 🔐 Security: student can access only own data
    if (Auth::check() && Auth::id() !== $student->id) {
        abort(403, 'Unauthorized');
    }

    /* =====================================================
     * 3️⃣ Month mapping (Apr–Mar academic cycle)
     * ===================================================== */
    $labels    = ['Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec','Jan','Feb','Mar'];
    $monthKeys = ['apr','may','jun','jul','aug','sep','oct','nov','dec','jan','feb','mar'];
    $labelToKey = array_combine($labels, $monthKeys);

    // Selected months → DB keys
    $selectedMonthKeys = collect($data['months'])
        ->map(fn ($m) => $labelToKey[$m] ?? null)
        ->filter()
        ->unique()
        ->values()
        ->all();

    /* =====================================================
     * 4️⃣ Fetch all receipts (with balance)
     * ===================================================== */
    $allReceipts = Receipt::where('user_id', $student->id)
        ->select(array_merge(['feeHead', 'created_at', 'balance'], $monthKeys))
        ->get();

    // Last running balance
    $lastReceipt    = $allReceipts->sortByDesc('created_at')->first();
    $baseOldBalance = (float) ($data['oldBalance']
                        ?? $lastReceipt->balance
                        ?? $student->oldBalance
                        ?? 0);

    /* =====================================================
     * 5️⃣ Detect already PAID months
     * ===================================================== */
    $paidMonthKeys = [];

    foreach ($allReceipts as $receipt) {
        foreach ($monthKeys as $mk) {
            if (!is_null($receipt->{$mk})) {
                $paidMonthKeys[] = $mk;
            }
        }
    }

    $paidMonthKeys = array_unique($paidMonthKeys);

    // ❗ Only unpaid selected months should be charged
    $chargeableMonths = array_values(array_diff($selectedMonthKeys, $paidMonthKeys));

    /* =====================================================
     * 6️⃣ Fee calculation (excluding paid months)
     * ===================================================== */
    $totalFee        = 0.0;
    $totalConcession = 0.0;
    $feeHeadsBreakdown = [];

    foreach ($student->feePlans as $feePlan) {

        $feeHead = $feePlan->feeHead;
        if (!$feeHead) continue;

        // Late fee handled separately
        if (strtoupper($feeHead->name) === 'LATE FEE') continue;

        // Applicable months for this fee head
        $applicable = [];
        foreach ($monthKeys as $mk) {
            if ((int) ($feeHead->{$mk} ?? 0) === 1) {
                $applicable[] = $mk;
            }
        }
        if (empty($applicable)) continue;

        // ✅ FINAL intersection: applicable ∩ chargeable
        $common = array_values(array_intersect($chargeableMonths, $applicable));
        if (empty($common)) continue;

        $lineAmount = (float) $feePlan->value * count($common);
        $totalFee  += $lineAmount;

        // Concession (per plan, per month)
        $concession = Concession::where('user_id', $student->id)
            ->where('fee_plan_id', $feePlan->id)
            ->first();

        if ($concession) {
            $totalConcession += ((float) $concession->concession_fee) * count($common);
        }

        $feeHeadsBreakdown[] = [
            'name'             => $feeHead->name,
            'number_of_months' => count($common),
            'fee_per_month'    => (float) $feePlan->value,
            'amount'           => round($lineAmount, 2),
        ];
    }

    /* =====================================================
     * 7️⃣ Late Fee (only for unpaid past months)
     * ===================================================== */
    $currentMonthIndex = ((int) date('n') + 8) % 12; // Apr = 0

    $lateFeePerMonth = (float) (
        FeePlan::whereHas('feeHead', fn ($q) => $q->where('name', 'Late Fee'))
            ->value('value') ?? 0
    );

    $lateFee = collect($chargeableMonths)
        ->filter(function ($mk) use ($monthKeys, $currentMonthIndex) {
            $idx = array_search($mk, $monthKeys, true);
            return $idx !== false && $idx < $currentMonthIndex;
        })
        ->count() * $lateFeePerMonth;

    /* =====================================================
     * 8️⃣ Transport Fee (unpaid months only)
     * ===================================================== */
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

                $feeHeadsBreakdown[] = [
                    'name'             => 'Transport',
                    'number_of_months' => count($routeMonthsToPay),
                    'fee_per_month'    => (float) $routePlan->value,
                    'amount'           => round($totalRouteFee, 2),
                ];
            }
        }
    }

    /* =====================================================
     * 9️⃣ Net Fee
     * ===================================================== */
    $hasReceipt = !empty($lastReceipt);

        if ($hasReceipt) {
            // Receipt exists → running balance
            $previousBalance = (float) $lastReceipt->balance;
        } else {
            // No receipt ever → opening balance
            $previousBalance = (float) ($student->oldBalance ?? 0);
        }

        if ($hasReceipt) {
            // 🔐 Receipt already carries outstanding
            $netFee = ($totalFee - $totalConcession)
                    + $totalRouteFee;
        } else {
            // 🔐 No receipt → add opening balance
            $netFee = ($totalFee - $totalConcession)
                    + $totalRouteFee
                    + $previousBalance;
        }


    /* =====================================================
     * 🔟 Monthly status cards (UI)
     * ===================================================== */
    $monthly = collect($labels)->map(function ($label, $idx) use (
        $labelToKey, $paidMonthKeys, $currentMonthIndex, $selectedMonthKeys
    ) {
        $key = $labelToKey[$label];

        if (in_array($key, $paidMonthKeys, true)) {
            return ['label' => $label, 'status' => 'paid', 'progress' => 1];
        }

        if (in_array($key, $selectedMonthKeys, true)) {
            return ['label' => $label, 'status' => 'due', 'progress' => 0];
        }

        if ($idx <= $currentMonthIndex) {
            return ['label' => $label, 'status' => 'due', 'progress' => 0];
        }

        return ['label' => $label, 'status' => 'upcoming', 'progress' => 0];
    })->values();

    /* =====================================================
     * ✅ JSON response (Flutter-ready)
     * ===================================================== */
    return response()->json([
        'ok' => true,

        'student' => [
            'admission' => $student->admission_number,
            'name'      => $student->name,
            'class'     => $student->grade,
            'category'  => optional($student->category)->category,
            'father'    => $student->fName,
            'route'     => optional($student->route)->routeName ?? 'NA',
        ],

        'summary' => [
            'oldBalance' => round($previousBalance, 2),
            'lateFee'    => round($lateFee, 2),
            'concession' => round($totalConcession, 2),
            'netFee'     => round($netFee, 2),
            'received'   => 0.00,
        ],

        'monthly'  => $monthly,
        'feeHeads' => $feeHeadsBreakdown,
        'lastReceipt' => $lastReceipt,

    ]);
}

public function dashboardSummary(Request $request)
{
    \Log::info('Dashboard summary API hit');

    $student = Auth::guard('sanctum')->user();

    if (!$student) {
        return response()->json([
            'ok' => false,
            'message' => 'Unauthorized'
        ], 401);
    }

    // ===== Academic months till now =====
    $labels = ['Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec','Jan','Feb','Mar'];
    $monthKeys = ['apr','may','jun','jul','aug','sep','oct','nov','dec','jan','feb','mar'];

    $currentMonthIndex = ((int) date('n') + 8) % 12; // Apr = 0
    $selectedMonthKeys = array_slice($monthKeys, 0, $currentMonthIndex + 1);

    // ===== All receipts =====
    $allReceipts = Receipt::where('user_id', $student->id)
        ->select(array_merge(['feeHead', 'created_at', 'balance'], $monthKeys))
        ->get();

    $lastReceipt = $allReceipts->sortByDesc('created_at')->first();

    $previousBalance = $lastReceipt
        ? (float) $lastReceipt->balance
        : (float) ($student->oldBalance ?? 0);

    // ===== Detect paid months =====
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

    // ===== Fee calculation =====
    $totalFee = 0;
    $totalConcession = 0;

    foreach ($student->feePlans as $feePlan) {
        $feeHead = $feePlan->feeHead;
        if (!$feeHead) continue;

        if (strtoupper($feeHead->name) === 'LATE FEE') continue;

        $applicable = [];
        foreach ($monthKeys as $mk) {
            if ((int) ($feeHead->{$mk} ?? 0) === 1) {
                $applicable[] = $mk;
            }
        }

        $common = array_intersect($chargeableMonths, $applicable);
        if (empty($common)) continue;

        $lineAmount = (float) $feePlan->value * count($common);
        $totalFee += $lineAmount;

        $concession = Concession::where('user_id', $student->id)
            ->where('fee_plan_id', $feePlan->id)
            ->value('concession_fee') ?? 0;

        $totalConcession += $concession * count($common);
    }

       /* =====================================================
     * 8️⃣ Transport Fee (unpaid months only)
     * ===================================================== */
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

                $feeHeadsBreakdown[] = [
                    'name'             => 'Transport',
                    'number_of_months' => count($routeMonthsToPay),
                    'fee_per_month'    => (float) $routePlan->value,
                    'amount'           => round($totalRouteFee, 2),
                ];
            }
        }
    }

    $totalFee += $totalRouteFee;

    // ===== Late fee =====
    $lateFeePerMonth = (float) (
        FeePlan::whereHas('feeHead', fn ($q) => $q->where('name', 'Late Fee'))
            ->value('value') ?? 0
    );

    $lateFee = collect($chargeableMonths)
        ->filter(function ($mk) use ($monthKeys, $currentMonthIndex) {
            $idx = array_search($mk, $monthKeys, true);
            return $idx !== false && $idx < $currentMonthIndex;
        })
        ->count() * $lateFeePerMonth;

    $netFee = $totalFee - $totalConcession;

    return response()->json([
        'ok' => true,
        'netFee' => round($netFee, 2),
        'oldBalance' => round($previousBalance, 2),
        'lateFee' => round($lateFee, 2),
        'concession' => round($totalConcession, 2),
        'totalPayable' => round(
            $netFee + $previousBalance + $lateFee - $totalConcession,
            2
        ),
    ]);
}
}
