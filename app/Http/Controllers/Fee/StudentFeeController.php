<?php

namespace App\Http\Controllers\Fee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\FeeHead;
use App\Category;
use App\subCode;
use App\FeePlan;
use App\User;
use App\flashNews;
use App\RouteName;
use App\Receipt;
use App\routeFeePlan;
use App\Concession;
use DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;
use function PHPUnit\Framework\returnCallback;
use Illuminate\Support\Facades\Auth;

class StudentFeeController extends Controller
{
    public function getStudentFeeDetail()
    {
        try {
            // Fetch the authenticated user and related data
            $user = User::with('route', 'feePlans.feeHead')->findOrFail(Auth::id());

            // Fetch other required data
            $routes = $user->route;
            $classes = subCode::select('class')->distinct()->orderBy('class')->get();
            $receipts = Receipt::where('user_id', $user->id)->get();
            $feeHeads = $user->feePlans->map(fn($feePlan) => $feePlan->feeHead);

            // Get balance details
            $balances = $receipts->isNotEmpty() ? $receipts->sortByDesc('created_at') : collect([$user->oldBalance ?? 0]);
            $balance1 = $balances->first();
            $balance  = $balance1->balance ?? $user->oldBalance ?? 0;

            // Define months array
            $months = ['apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar'];

            // Get the current month dynamically
            $currentMonth = strtolower(date('M')); // Example: 'mar' for March

           // Initialize all months as visible but unticked
            $monthStatus = array_fill_keys($months, false);

            // Fetch paid months
            $paidMonthsQuery = Receipt::where('user_id', $user->id)
                ->select(array_merge(['feeHead'], $months))
                ->get()
                ->reduce(function ($carry, $receipt) use ($months) {
                    if (!isset($carry[$receipt->feeHead])) {
                        $carry[$receipt->feeHead] = [];
                    }
                    foreach ($months as $month) {
                        if (!is_null($receipt->{$month})) {
                            $carry[$receipt->feeHead][] = $month;
                        }
                    }
                    return $carry;
                }, []);

            // Set monthStatus to show unpaid + applicable months
            foreach ($feeHeads as $feeHead) {
                if ($feeHead->name !== 'LATE FEE') {
                    foreach ($months as $month) {
                        if (
                            $feeHead->{$month} == 1 && // Fee applicable
                            (!isset($paidMonthsQuery[$feeHead->name]) || !in_array($month, $paidMonthsQuery[$feeHead->name])) // Not paid
                        ) {
                            $monthStatus[$month] = false; // Show, but unticked
                        }
                    }
                }
            }


            // Transport-specific logic
            $transportMonthStatus = array_fill_keys($months, false);
            if ($user->route && $user->route->routeName !== "NA") {
                $routePaidMonths = Receipt::where('user_id', $user->id)
                    ->where('feeHead', "Transport")
                    ->select($months)
                    ->get()
                    ->flatMap(fn($receipt) => array_filter($months, fn($month) => !is_null($receipt->{$month})))
                    ->toArray();

                    foreach ($months as $month) {
                        if (!in_array($month, $routePaidMonths)) {
                            $transportMonthStatus[$month] = false; // Show unpaid, unticked
                        }
                    }

            }
            $subCodes  = subCode::all()->where('class',Auth::user()->grade);

            // Return the fee detail view
            return view('student.fee.feeDetail', [
                'user' => $user,
                'id' => $user->id,
                'grades' => $classes->pluck('class')->toArray(),
                'routes' => $routes,
                'balance' => $balance,
                'monthStatus' => $monthStatus,
                'transportMonthStatus' => $transportMonthStatus,
                'subCodes' => $subCodes,
            ]);
        } catch (Exception $e) {
            Log::error("Error fetching fee details: " . $e->getMessage());
            return redirect()->back()->with('failed', 'An error occurred while fetching fee details.');
        }
    }



public function postStudentFeeDetail(Request $request)
{
    try {

        if ($request->method() !== 'POST') {
            return response()->json(['error' => 'Invalid request method. Use POST instead.'], 405);
        }

        // Validate the incoming request data
        $data = $request->validate([
            'id' => 'required|exists:users,id',
            'oldBalance' => 'nullable|numeric',
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {

        \Log::error('Validation Error in post_feeDetail', ['errors' => $e->errors()]);
        return redirect()->back()->with('failed', 'Invalid data provided. Please check the inputs.');
    }
    $id = $data['id'];
    $oldBalance = $data['oldBalance'] ?? 0;
    // Extract selected months
    $months = ['apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar'];
    $selectedMonths = array_filter($months, fn($month) => $request->has($month));

        // Fetch the user with related data
        $user = User::with(['route', 'category', 'feePlans.feeHead'])->findOrFail($id);

        $feePlans = $user->feePlans()->with('feeHead')->get();

        // Fetch receipts for the user
        $allReceipts = Receipt::where('user_id', $id)->select(array_merge(['feeHead'], $months))->get();
        // Initialize variables


        $totalFee = 0;
        $totalConcession = 0;
        $concessionDetails = [];

        foreach ($user->feePlans as $feePlan) {
            $feeHead = $feePlan->feeHead;
            // Find applicable months for the FeeHead
            $applicableMonths = [];
            foreach ($months as $month) {
                if ($feeHead->$month === 1) {
                    $applicableMonths[] = $month;
                }
            }

            // Calculate intersection of selected months and applicable months
            $commonMonths = array_intersect($selectedMonths, $applicableMonths);


            // Calculate the fee for common months
             $monthlyFee = $feePlan->value / count($applicableMonths);
            $totalFee += $feePlan->value  * count($commonMonths);

            // Fetch applicable concession
            $concession = Concession::where('user_id', $id)
                ->where('fee_plan_id', $feePlan->id)
                ->first();

            if ($concession) {
                $monthlyConcession = $concession->concession_fee ;

                $totalConcession += $concession->concession_fee * count($commonMonths);

                $concessionDetails[] = [
                    'fee_plan' => $feePlan->id,
                    'feeHead' => $feeHead->name,
                    'monthly_fee' => $monthlyFee,
                    'monthly_concession' => $monthlyConcession,
                    'total_concession' => $monthlyConcession * count($commonMonths),
                ];
            }
        }

        // (Late Fee) Define the month mapping (adjust to match 'apr' = index 0)
            $months = ['apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar'];

            // Calculate the current month index
            $currentMonthIndex = (date('n') + 8) % 12; // Adjusting to match April as index 0

            // Get the late fee per month value
            $lateFeePerMonth = FeePlan::whereHas('feeHead', fn($query) => $query->where('name', 'Late Fee'))->value('value') ?? 0;

            // Calculate the late fee for past months only
            $lateFee = collect($selectedMonths)->filter(function ($month) use ($months, $currentMonthIndex) {
                $monthIndex = array_search($month, $months);
                return $monthIndex !== false && $monthIndex < $currentMonthIndex; // Only past months
            })->count() * $lateFeePerMonth;


        // Calculate the net fee
        $netFee = $totalFee - $totalConcession;

        // Fetch route fee plan if applicable
        $routeName = $user->route->routeName ?? null;
        $routeFeePlan = $routeName ? RouteFeePlan::where('routeName', $routeName)->first() : null;
        if($routeFeePlan->routeName === "NA"){
            $routeFeePlan = null;
        }
        $totalRouteFee = 0;
        $monthsToPay = [];
        $routeMonthsToPay = [];

        if ($routeFeePlan != null) {
            // Check if the user has already paid for the selected months
            $routePaidMonths = [];
            $routeReceipts = DB::table('receipts')
                ->where('user_id', $user->id)
                ->where('feeHead', 'Transport')
                ->get();

            foreach ($routeReceipts as $receipt) {
                foreach ($selectedMonths as $month) {
                    if (!is_null($receipt->$month) && $receipt->$month > 0) {
                        $routePaidMonths[] = $month;
                    }
                }
            }

            // Filter out paid months
            $routeMonthsToPay = array_diff($selectedMonths, $routePaidMonths);

            // Calculate the total route fee for the unpaid months
            $totalRouteFee = $routeFeePlan->value * count($routeMonthsToPay);
        }
         // Calculate the net fee
        $netFee = $totalFee - $totalConcession + $totalRouteFee  + $oldBalance;
        $feeHeadTotal = null;
        $routeHeadTotal = null;

        $months = ['apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar'];
         // Collect all paid months from receipts
        $paidMonths = $allReceipts->groupBy('feeHead')->map(function ($receiptGroup) use ($months) {
            $paid = [];
            foreach ($receiptGroup as $receipt) {
                foreach ($months as $month) {
                    if (!is_null($receipt->{$month})) {
                        $paid[] = $month;
                    }
                }
            }
            return array_unique($paid);
        });

        $subCodes  = subCode::all()->where('class',Auth::user()->grade);

        return view('student.fee.deposit', compact(
            'user', 'id', 'oldBalance', 'selectedMonths', 'lateFee', 'totalFee', 'routeName', 'monthsToPay', 'routeHeadTotal',
            'totalConcession', 'netFee', 'totalRouteFee', 'concessionDetails','feePlans', 'feeHeadTotal',
            'routeFeePlan', 'allReceipts', 'months', 'paidMonths', 'routeMonthsToPay', 'subCodes'
        ));
        return redirect()->back()->with('failed', 'An error occurred while processing the fee details.');
    }


//     public function confirmRazorpayPayment(Request $request)
// {
//     try {
//         // Log the request data for debugging
//         \Log::info('Razorpay payment confirmation request:', $request->all());

//         // 1. Validate the request data
//         $validatedData = $request->validate([
//             'razorpay_payment_id' => 'sometimes|string', // Make this optional
//             'id' => 'required|exists:users,id',
//             'netFee' => 'required|numeric',
//             'date' => 'required|date',
//             'paymentType' => 'sometimes|string',   // Make this optional
//         ]);

//         // 2. Fetch student details
//         $user = \App\Models\User::findOrFail($validatedData['id']); // Use fully qualified class name

//         // 3. Generate new receipt ID
//         $receiptId = \App\Models\Receipt::latest('receiptId')->value('receiptId'); // Use fully qualified class name
//         $receiptId = $receiptId ? $receiptId + 1 : 1;

//         // 4. Create new receipt entry
//         $receipt = new \App\Models\Receipt([  // Use fully qualified class name
//             'user_id' => $validatedData['id'],
//             'receiptId' => $receiptId,
//             'date' => $validatedData['date'],
//             'netFee' => $validatedData['netFee'],
//             'receivedAmt' => $validatedData['netFee'],
//             'balance' => 0,
//             'paymentMode' => $validatedData['paymentType'] ?? 'Razorpay',
//             'payment_id' => $validatedData['razorpay_payment_id'] ?? null,
//         ]);

//         $receipt->save();

//         // 5. Return a JSON response
//         return response()->json([
//             'success' => true,
//             'message' => 'Payment successful!',
//             'redirect_url' => route('student.feeInvoice', ['id' => $user->id, 'receiptId' => $receiptId])
//         ]);
//     } catch (\Exception $e) {
//         \Log::error("🚨 Error in confirmRazorpayPayment: " . $e->getMessage(), [
//             'request_data' => $request->all(),  // Log the request data
//             'exception' => $e,                   // Log the full exception
//         ]);

//         return response()->json([
//             'success' => false,
//             'message' => 'An error occurred while processing the payment.',
//         ], 500);
//     }
// }




//  public function confirmRazorpayPayment(Request $request)
//  {
//      // Log the request data for debugging
//      Log::info('Razorpay payment confirmation request:', $request->all());

//      // 1. Validate the request data (important!)
//      $validatedData = $request->validate([
//          'razorpay_payment_id' => 'required|string',
//          'id' => 'required|exists:users,id',
//          'netFee' => 'required|numeric',
//          'date' => 'required|date',
//          'paymentType' => 'required|string',
//      ]);

//      // 2. Process the payment and save data to the database
//      try {
//          // ... Your logic to save the fee receipt details using $validatedData ...

//          // 3. Return a JSON response with 'success' => true
//          return response()->json([
//              'success' => true,
//              'message' => 'Payment successful!',
//              'redirect_url' => '/fee/allStudentsRecord', // Change this to your desired redirect URL
//          ]);

//      } catch (\Exception $e) {
//          // Log the error
//          Log::error('Error saving fee receipt: ' . $e->getMessage());

//          // 4. If there's an error, return a JSON response with 'success' => false
//          return response()->json([
//              'success' => false,
//              'message' => 'Payment failed. Please try again.',
//          ], 500); // Use a 500 status code for server errors
//      }
//  }



    // public function getFeeInvoice(Request $request)
    //     {

    //     // Ensure Razorpay payment ID is present
    //     if (!isset($data['razorpay_payment_id'])) {
    //         return response()->json(['success' => false, 'message' => 'Payment ID missing!']);
    //     }

    //         // Fetch the user and their receipts
    //         $user = User::with(['receipts'])->findOrFail($request->id);

    //         // Fetch the specific receipt using receiptId
    //         $receipt = Receipt::where('receiptId', $request->receiptId)->firstOrFail();
    //         $receipts = Receipt::where('receiptId', $request->receiptId)->get();

    //         // Define months for processing
    //         $months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];

    //         // Prepare `prints` data
    //         $prints = collect([$receipt])->map(function ($receipt) use ($months) {
    //             $paidMonths = [];

    //             foreach ($months as $month) {
    //                 if (!is_null($receipt->{$month})) {
    //                     $paidMonths[] = ucfirst($month); // Convert to human-readable month name
    //                 }
    //             }

    //             return [
    //                 'feeHead' => $receipt->feeHead,
    //                 'receiptId' => $receipt->receiptId,
    //                 'date' => $receipt->date,
    //                 'paidMonths' => implode(', ', $paidMonths), // Combine paid months into a string
    //                 'oldBalance' => $receipt->oldBalance,
    //                 'gTotal' => $receipt->total,
    //                 'lateFee' => $receipt->lateFee,
    //                 'concession' => $receipt->concession,
    //                 'netFee' => $receipt->netFee,
    //                 'receivedAmt' => $receipt->receivedAmt,
    //                 'balance' => $receipt->balance,
    //                 'paymentType' => $receipt->paymentMode,
    //                 'bankName' => $receipt->bankName,
    //                 'chequeNo' => $receipt->chequeNo,
    //                 'chqDate' => $receipt->chequeDate,
    //                 'remark' => $receipt->remarks,
    //             ];
    //         });
    //         // Pass user and invoice details to the view
    //         return view('student.fee.payment-invoice', compact('user', 'prints', 'receipt', 'receipts'))
    //             ->with('status', "Fee Submitted Successfully.");
    //     }


    public function confirmRazorpayPayment(Request $request)
    {
        try {
            // Force Laravel to return JSON
            if (!$request->expectsJson()) {
                return response()->json(['error' => 'Invalid request format. Use JSON.'], 400);
            }

            // Validate required fields
            $request->validate([
                'razorpay_payment_id' => 'required|string',
                'id' => 'required|integer',
                'netFee' => 'required|numeric',
                'date' => 'required|date',
                'paymentType' => 'required|string',
            ]);

            // Fetch student
            $user = User::findOrFail($request->id);

            // Save payment record (dummy logic, update as per DB)
            $payment = new Payment();
            $payment->user_id = $user->id;
            $payment->razorpay_payment_id = $request->razorpay_payment_id;
            $payment->amount = $request->netFee;
            $payment->date = $request->date;
            $payment->payment_type = $request->paymentType;
            $payment->status = 'Success';
            $payment->save();

            // Send JSON response
            return response()->json([
                'success' => true,
                'message' => 'Payment confirmed successfully!',
                'redirect_url' => route('student.feeInvoice', ['id' => $user->id, 'receiptId' => $payment->id])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getFeeInvoice(Request $request)
    {


        \Log::info('getFeeInvoice request:', [
            'id' => $request->id,
            'receiptId' => $request->receiptId,
        ]);

        // Fetch the user and their receipts
        $user = User::with(['receipts'])->findOrFail($request->id);

        // Fetch the specific receipt using receiptId
        $receipt = Receipt::where('receiptId', $request->receiptId)->firstOrFail();
        $receipts = Receipt::where('receiptId', $request->receiptId)->get();

        // Define months for processing
        $months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];

        // Prepare `prints` data
        $prints = collect([$receipt])->map(function ($receipt) use ($months) {
            $paidMonths = [];

            foreach ($months as $month) {
                if (!is_null($receipt->{$month})) {
                    $paidMonths[] = ucfirst($month); // Convert to human-readable month name
                }
            }

            return [
                'feeHead' => $receipt->feeHead,
                'receiptId' => $receipt->receiptId,
                'date' => $receipt->date,
                'paidMonths' => implode(', ', $paidMonths), // Combine paid months into a string
                'oldBalance' => $receipt->oldBalance,
                'gTotal' => $receipt->total,
                'lateFee' => $receipt->lateFee,
                'concession' => $receipt->concession,
                'netFee' => $receipt->netFee,
                'receivedAmt' => $receipt->receivedAmt,
                'balance' => $receipt->balance,
                'paymentType' => $receipt->paymentMode,
                'bankName' => $receipt->bankName,
                'chequeNo' => $receipt->chequeNo,
                'chqDate' => $receipt->chequeDate,
                'remark' => $receipt->remarks,
            ];
        });

        $subCodes  = subCode::all()->where('class',Auth::user()->grade);

        // Pass user and invoice details to the view
        return view('student.fee.payment-invoice', compact('user', 'prints', 'receipt', 'receipts', 'subCodes'))
            ->with('status', "Fee Submitted Successfully.");
    }

    public function storeFeeReceiptForm(Request $request)
    {
        dd($request);
        // ... Your logic to handle the HTML form submission ...
        // Validate, save data, etc.

        // After processing, redirect the user (or return a view)
        return redirect('/fee/allStudentsRecord')->with('success', 'Fee receipt saved successfully!'); // Example
    }



    public function studentFeeCard()
    {
        $user  = Auth::User();
        $receipts = Receipt::all()->where('user_id',$user->id);
        $subCodes  = subCode::all()->where('class',Auth::user()->grade);

    return view('student.fee.studentFeeCard', compact('user', 'receipts', 'subCodes'));

    }

    public function printReceipt($id){


        // Fetch the specific receipt using receiptId
        $receipt = Receipt::where('receiptId', $id)->firstOrFail();

        $receipts = Receipt::where('receiptId', $id)->get();

        $user = User::with(['receipts'])->findOrFail($receipt->user_id);

        // Define months for processing
        $months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];

       $prints = collect([$receipt])->map(function ($receipt) use ($months) {
           $paidMonths = [];

           foreach ($months as $month) {
               if (!is_null($receipt->{$month})) {
                   $paidMonths[] = ucfirst($month); // Convert to human-readable month name
               }
           }

           return [
               'feeHead' => $receipt->feeHead,
               'receiptId' => $receipt->receiptId,
               'date' => $receipt->date,
               'paidMonths' => implode(', ', $paidMonths), // Combine paid months into a string
               'oldBalance' => $receipt->oldBalance,
               'gTotal' => $receipt->total,
               'lateFee' => $receipt->lateFee,
               'concession' => $receipt->concession,
               'netFee' => $receipt->netFee,
               'receivedAmt' => $receipt->receivedAmt,
               'balance' => $receipt->balance,
               'paymentType' => $receipt->paymentMode,
               'bankName' => $receipt->bankName,
               'chequeNo' => $receipt->chequeNo,
               'chqDate' => $receipt->chequeDate,
               'remark' => $receipt->remarks,
           ];
       });
          // Pass user and invoice details to the view
          return view('student.fee.payment-invoice', compact('user', 'prints', 'receipt', 'receipts'))
          ->with('status', "Fee Submitted Successfully.");
       }


}

