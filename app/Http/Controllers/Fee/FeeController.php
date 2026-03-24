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

class FeeController extends Controller
{
    // public function __construct()
    // {
    //   $this->middleware('auth:admin');
    // }

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::guard('admin')->check() && !Auth::guard('cashier')->check()) {
                return redirect()->route('login')->with('error', 'You are not authorized to access this page.');
            }
            return $next($request);
        });
    }


    public function dashboard()
    {
        // ✅ Months in Apr–Mar order
        $months = ['Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar'];

        $feeMonths   = [];
        $feeReceived = [];
        $feeBalance  = [];

        foreach ($months as $m) {
            // Convert month abbreviation (Apr, May etc.) to month number
            $monthNum = date('m', strtotime($m));
            // dd($monthNum);
            // ✅ Received: total receipts for that month
            $received = Receipt::whereMonth('created_at', $monthNum)
                ->sum('receivedAmt');
            // dd(strtolower($m));

            // ✅ Expected: calculate for this month (your own method)
            $monthKey = strtolower($m); // apr, may, jun etc.
            $expected = $this->calculateExpectedForMonth($monthKey);

            // ✅ Balance
            $balance = max(0, $expected - $received);

            // Push values
            $feeMonths[]   = $m;
            $feeReceived[] = (int) $received;
            $feeBalance[]  = (int) $balance;
        }
        // dd($feeReceived,$feeBalance,$feeMonths);
        $dueData = $this->calculateDueByMonth();
        dd($dueData);
        return view('admin.fee.dashboard', [
        'feeMonths'   => $dueData['months'],
        'feeReceived' => $dueData['received'],
        'feeBalance'  => $dueData['balance'],
    ]);
    }

    private function calculateDueByMonth() // It is used for dashboard chart
        {
            // Call the same logic as post_dueList
            // and return month-wise totals

            $months = ['Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec','Jan','Feb','Mar'];

            $feeMonths   = [];
            $feeReceived = [];
            $feeBalance  = [];

            foreach ($months as $m) {
                $monthKey  = strtolower($m);

                // ✅ Expected + Received from your existing due list logic
                $expected  = $this->calculateExpectedForMonth($monthKey);
                $received  = Receipt::whereMonth('created_at', date('m', strtotime($m)))
                                ->sum('receivedAmt');

                $balance   = max(0, $expected - $received);

                $feeMonths[]   = $m;
                $feeReceived[] = (int) $received;
                $feeBalance[]  = (int) $balance;
            }

            return [
                'months'      => $feeMonths,
                'received'    => $feeReceived,
                'balance'     => $feeBalance,
            ];
        }

    /**
     * ✅ Calculate expected fee for a given month (all classes + routes)
     */
    private function calculateExpectedForMonth($monthKey)
    {
        $categories = Category::all()->pluck('category', 'id');
        $classes    = subCode::distinct('class')->pluck('class');
        $routes     = routeFeePlan::distinct('routeName')->pluck('routeName');

        // Simulate request data
        $data = [$monthKey => 'on'];

        // Use your existing class + route fee calculators
        $classArray = $this->calculateClassFees($data, $categories, $classes, [$monthKey]);
        $routeArray = $this->calculateRouteFees($data, $routes, [$monthKey]);

        // Totals
        $classTotal = collect($classArray)->sum('value');
        $routeTotal = collect($routeArray)->sum('routeValue');

        return $classTotal + $routeTotal;
    }




    public function createFeeHead()
    {

        return view('admin.fee.createFeeHead');
    }


    public function post_createFeeHead(Request $request)
    {

        $rules = [
			'name' => 'required', 'string', 'max:255',
            'accountName' =>  'required', 'string', 'max:255',
            'frequency' =>  'required', 'string', 'max:255',

		];
		$validator = Validator::make($request->all(),$rules);
		if ($validator->fails()) {
			return redirect('fee/createFeeHead')
			->withInput()
			->withErrors($validator);
		}
		else{
            $data = $request->input();
			try{
        $name = strtoupper($data['name']);
        $accountName = $data['accountName'];
        $frequency = $data['frequency'];
        if(isset($data['january'])){
            $jan = 1;
        }else{
          $jan = 0;
        }
        if(isset($data['february'])){
            $feb = 1;
        }else{
          $feb = 0;
        }
        if(isset($data['march'])){
            $mar = 1;
        }else{
          $mar = 0;
        }
        if(isset($data['april'])){
            $apr = 1;
        }else{
          $apr = 0;
        }
        if(isset($data['may'])){
            $may = 1;
        }else{
          $may = 0;
        }
        if(isset($data['june'])){
            $jun = 1;
        }else{
          $jun = 0;
        }
        if(isset($data['july'])){
            $jul = 1;
        }else{
          $jul = 0;
        }
        if(isset($data['august'])){
            $aug = 1;
        }else{
          $aug = 0;
        }
        if(isset($data['september'])){
            $sep = 1;
        }else{
          $sep = 0;
        }
        if(isset($data['october'])){
            $oct = 1;
        }else{
          $oct = 0;
        }
        if(isset($data['november'])){
            $nov = 1;
        }else{
          $nov = 0;
        }
        if(isset($data['december'])){
            $dec = 1;
        }else{
          $dec = 0;
        }

				$feeHead = new feeHead;
                $feeHead->name = $name;
                $feeHead->accountName = $accountName;
                $feeHead->frequency = $frequency;
                $feeHead->jan = $jan;
                $feeHead->feb = $feb;
                $feeHead->mar = $mar;
                $feeHead->apr = $apr;
                $feeHead->may = $may;
                $feeHead->jun = $jun;
                $feeHead->jul = $jul;
                $feeHead->aug = $aug;
                $feeHead->sep = $sep;
                $feeHead->sep = $sep;
                $feeHead->oct = $oct;
                $feeHead->nov = $nov;
                $feeHead->dec = $dec;
				$feeHead->save();
				return redirect('fee/createFeeHead')->with('status','Insert successfully');
			}
			catch(Exception $e){
				return redirect('fee/createFeeHead')->with('failed',"operation failed");
			}
		}
    }


    public function viewFeeHead()
    {
        $feeHeads = FeeHead::all();
        return view('admin.fee.viewFeeHead', compact('feeHeads'));

    }


    public function editFeeHead(Request $request, $id)
    {
        $feeHeads  = FeeHead::all()->WHERE('id',$id);
        return view('admin.fee.editFeeHead', compact('feeHeads', 'id'));
    }

    public function post_editFeeHead(Request $request){
        $data = $request->input();
        $id = $data['id'];
        $name = strtoupper($data['name']);
        if(isset($data['january'])){
            $jan = 1;
        }else{
          $jan = 0;
        }
        if(isset($data['february'])){
            $feb = 1;
        }else{
          $feb = 0;
        }
        if(isset($data['march'])){
            $mar = 1;
        }else{
          $mar = 0;
        }
        if(isset($data['april'])){
            $apr = 1;
        }else{
          $apr = 0;
        }
        if(isset($data['may'])){
            $may = 1;
        }else{
          $may = 0;
        }
        if(isset($data['june'])){
            $jun = 1;
        }else{
          $jun = 0;
        }
        if(isset($data['july'])){
            $jul = 1;
        }else{
          $jul = 0;
        }
        if(isset($data['august'])){
            $aug = 1;
        }else{
          $aug = 0;
        }
        if(isset($data['september'])){
            $sep = 1;
        }else{
          $sep = 0;
        }
        if(isset($data['october'])){
            $oct = 1;
        }else{
          $oct = 0;
        }
        if(isset($data['november'])){
            $nov = 1;
        }else{
          $nov = 0;
        }
        if(isset($data['december'])){
            $dec = 1;
        }else{
          $dec = 0;
        }
              try{

        DB::table('fee_heads')
        ->where('id', $id)
        ->update([
        'name' => $name,
        'accountName' => $data['accountName'],
        'frequency' => $data['frequency'],
        'jan' => $jan,
        'feb' => $feb,
        'mar' => $mar,
        'apr' => $apr,
        'may' => $may,
        'jun' => $jun,
        'jul' => $jul,
        'aug' => $aug,
        'sep' => $sep,
        'oct' => $oct,
        'nov' => $nov,
        'dec' => $dec,
        ]);
          return redirect('fee/viewFeeHead')->with('status','Record updated successfully');
                  }

        catch(Exception $e){
          return redirect('fee/viewFeeHead')->with('failed',"operation failed");

      }
    }


    public function deleteFeeHead($id){
        try{
            $record = FeeHead::find($id);

            $record->delete($record->id);

            return redirect('fee/viewFeeHead')->with('delete','Record deleted successfully');
        }
        catch(Exception $e){
            return redirect('fee/viewFeeHead'.$id)->with('failed',"operation failed");

        }
      }

      public function category()
    {
        $categories = Category::all();
        return view('admin.fee.category', compact('categories'));
    }

    public function addCategory(Request $request)
    {

        $rules = [
			'category' => 'required', 'string', 'max:255',
        	];
		$validator = Validator::make($request->all(),$rules);
		if ($validator->fails()) {
			return redirect('fee/category')
			->withInput()
			->withErrors($validator);
		}
		else{
            $data = $request->input();
			try{
        $categoryName = strtoupper($data['category']);

				$category = new category;
                $category->category = $categoryName;

				$category->save();
				return redirect('fee/category')->with('status','Insert successfully');
			}
			catch(Exception $e){
				return redirect('fee/category')->with('failed',"operation failed");
			}
		}
    }

    public function deleteCategory($id){
        try{
            $record = Category::find($id);

            $record->delete($record->id);

            return redirect('fee/category')->with('delete','Record deleted successfully');
        }
        catch(Exception $e){
            return redirect('fee/category'.$id)->with('failed',"operation failed");

        }
      }

      public function editCategory(Request $request, $id)
    {
        $categories  = Category::all()->WHERE('id',$id);
        return view('admin.fee.editCategory', compact('categories', 'id'));
    }

    public function post_editCategory(Request $request){
        $data = $request->input();
        $id = $data['id'];
        $category = strtoupper($data['category']);
        try{

        DB::table('categories')
        ->where('id', $id)
        ->update([
        'category' => $category,

        ]);
          return redirect('fee/category')->with('status','Record updated successfully');
                  }

        catch(Exception $e){
          return redirect('fee/category')->with('failed',"operation failed");

      }
    }

    public function feePlan()
    {
        $categories = Category::all();
        $classes = subCode::all('class')->unique('class');
        $feeHeads = FeeHead::all();
        $feePlans = FeePlan::all();
        return view('admin.fee.feePlan', compact('categories','feeHeads','classes','feePlans'));
    }

    public function post_feePlan(Request $request)
    {
        $rules = [
			'value' => 'required', 'integer',
        	];
		$validator = Validator::make($request->all(),$rules);
		if ($validator->fails()) {
			return redirect('fee/feePlan')
			->withInput()
			->withErrors($validator);
		}
		else{
            $data = $request->input();
            $categories[] = $data['category'];
            $classes[] = $data['class'];

			try{
                foreach($request->category as $j => $categoryName){
                    foreach($request->class as $i => $classValue){
                        // Save feePlan with category name (as per your design)
                        $feePlan = new FeePlan;
                        $feePlan->category = $categoryName; // name
                        $feePlan->class = $classValue;
                        $feePlan->feeHead_id = $data['feeHead'];
                        $feePlan->value = $data['value'];
                        $feePlan->save();

                        // Get category ID from name
                        $category = Category::where('category', $categoryName)->first();

                        if (!$category) {
                            return redirect('fee/feePlan')->with('failed', "Category Not Found ");
                        }

                        // Fetch students matching class and category ID
                        $students = User::where('grade', $classValue)
                                        ->where('category_id', $category->id)
                                        ->get();

                        foreach ($students as $student) {
                            $feePlan->users()->syncWithoutDetaching($student->id);
                        }
                    }
                }


				return redirect('fee/feePlan')->with('status','Insert successfully');
			}
			catch(Exception $e){
                return redirect('fee/feePlan')->with('failed', "operation failed: " . $e->getMessage());
			}
		}
    }

    public function editfeePlan(Request $request, $id)
    {
        $categories = Category::all();
        $classes = subCode::all('class')->unique('class');
        $feeHeads = FeeHead::all();
        $feePlan = FeePlan::findOrFail($id); // Just one record, not all

        return view('admin.fee.editFeePlan', compact('feePlan', 'id', 'categories', 'classes', 'feeHeads'));
    }

    public function post_editfeePlan(Request $request)
    {
        $data = $request->input();
        $request->validate([
            'feeHead' => 'required|exists:fee_heads,id',
            'value' => 'required|integer',
            'category' => 'required',
            'class' => 'required',
            'id' => 'required|exists:fee_plans,id',
        ]);

        $id = $data['id'];

        try {
            // Update the fee plan
            DB::table('fee_plans')
                ->where('id', $id)
                ->update([
                    'category' => $data['category'],
                    'class' => $data['class'],
                    'feeHead_id' => $data['feeHead'],
                    'value' => $data['value'],
                ]);

            // Get the feePlan instance
            $feePlan = FeePlan::findOrFail($id);

            // Get the matching category ID from name
            $category = Category::where('category', $data['category'])->first();

            if ($category) {
                // Find all matching students
                $students = User::where('grade', $data['class'])
                                ->where('category_id', $category->id)
                                ->pluck('id')
                                ->toArray();

                // Sync the pivot table (replace old users with new matching ones)
                $feePlan->users()->sync($students);
            }

            return redirect('fee/feePlan')->with('status', 'Record updated successfully');
        } catch (Exception $e) {
            return redirect('fee/feePlan')->with('failed', $e->getMessage());
        }
    }


    public function deleteFeePlan($id)
        {
            try {
                $record = FeePlan::findOrFail($id);

                // Detach related users first (if relationship exists)
                $record->users()->detach();

                // Delete the fee plan
                $record->delete();

                return redirect('fee/feePlan')->with('delete', 'Record deleted successfully');
            } catch (Exception $e) {
                return redirect('fee/feePlan')->with('failed', 'Operation failed: ' . $e->getMessage());
            }
        }


      public function allStudentsRecord()
      {
          $users  = User::all()->sortBy('class');
          $flashNews = flashNews::all()->sortByDesc('created_at');
          return view('admin.fee.allStudentsRecord', compact('users','flashNews'));
      }

    //   public function feeDetail(Request $request, $id)
    //   {
    //       $users  = User::all()->WHERE('id',$id);
    //       $routes = RouteName::all();
    //       $classes = subCode::all()->unique()->sortBy("class");
    //       $receipts = Receipt::all()->where('user_id',$id);
    //       $feeHeads = FeeHead::all();
    //       $routeNames = RouteName::all();
    //       $balances  = Receipt::all()->where('user_id',$id)->sortByDesc('created_at');
    //       foreach($users as $user){
    //         $userRouteName = $user->route;
    //         break;
    //       }
    //       $routeNames = RouteName::all()->where('routeName',$userRouteName)->first();
    //       if(!(isset($classes))){
    //           return redirect('admin/fee/feeDetail')->with('failed',"Please create class and Subject first.");
    //       }
    //       $grade = NULL;
    //       $balance = null;
    //         foreach($balances as $balanceRecord){
    //             $balance =  $balanceRecord->balance;
    //             break;
    //         }
    //       foreach($classes as $class){
    //           if($grade!=$class->class){
    //               $grades[] = $class->class;
    //           }
    //           $grade=$class->class;
    //       }
    //       if($user->route!=null){$janT = true;}else{$janT = false;}
    //       if($user->route!=null){$febT = true;}else{$febT = false;}
    //       if($user->route!=null){$marT = true;}else{$marT = false;}
    //       if($user->route!=null){$aprT = true;}else{$aprT = false;}
    //       if($user->route!=null){$mayT = true;}else{$mayT = false;}
    //       if($user->route!=null){$junT = true;}else{$junT = false;}
    //       if($user->route!=null){$julT = true;}else{$julT = false;}
    //       if($user->route!=null){$augT = true;}else{$augT = false;}
    //       if($user->route!=null){$sepT = true;}else{$sepT = false;}
    //       if($user->route!=null){$octT = true;}else{$octT = false;}
    //       if($user->route!=null){$novT = true;}else{$novT = false;}
    //       if($user->route!=null){$decT = true;}else{$decT = false;}
    //       $jan = true;  $feb = true;  $mar = true;  $apr = true; $may = true; $jun = true; $jul = true; $aug = true; $sep = true; $oct = true; $nov = true; $dec = true;

    //         foreach($feeHeads as $feeHead){

    //                 if($feeHead->jan == 1){
    //                     foreach($receipts as $receipt){
    //                         if(isset($routeNames) && $receipt->feeHead == $routeNames->routeName){
    //                             if($receipt->january != null){
    //                                 $janT = false;
    //                             }
    //                         }
    //                             if($receipt->january != null){
    //                                 $jan = false;
    //                             }

    //                     }
    //                 }
    //                 if($feeHead->feb == 1){
    //                     foreach($receipts as $receipt){
    //                         if(isset($routeNames) && $receipt->feeHead == $routeNames->routeName){
    //                             if($receipt->february != null){
    //                                 $febT = false;
    //                             }
    //                         }
    //                             if($receipt->february != null){
    //                             $feb = false;
    //                         }
    //                     }
    //                 }
    //                 if($feeHead->mar == 1){
    //                     foreach($receipts as $receipt){
    //                         if(isset($routeNames) && $receipt->feeHead == $routeNames->routeName){
    //                             if($receipt->march != null){
    //                                 $marT = false;
    //                             }
    //                         }
    //                             if($receipt->march != null){
    //                             $mar = false;
    //                         }
    //                     }
    //                 }
    //                 if($feeHead->apr == 1 ){
    //                     foreach($receipts as $receipt){
    //                         if(isset($routeNames) && $receipt->feeHead == $routeNames->routeName){

    //                             if($receipt->april != null){
    //                                 $aprT = false;
    //                             }
    //                         }

    //                         if($receipt->feeHead == $feeHead->name){
    //                                     if($receipt->april != null){
    //                                     $apr = false;
    //                                 }
    //                         }
    //                     }
    //                 }
    //                 if($feeHead->may == 1){
    //                     foreach($receipts as $receipt){
    //                         if(isset($routeNames) && $receipt->feeHead == $routeNames->routeName){
    //                             if($receipt->may != null){
    //                                 $mayT = false;
    //                             }
    //                         }
    //                             if($receipt->may != null){
    //                             $may = false;
    //                         }
    //                     }
    //                 }
    //                 if($feeHead->jun == 1){
    //                     foreach($receipts as $receipt){
    //                         if(isset($routeNames) && $receipt->feeHead == $routeNames->routeName){
    //                             if($receipt->june != null){
    //                                 $junT = false;
    //                             }
    //                         }
    //                             if($receipt->june != null){
    //                             $jun = false;
    //                         }
    //                     }
    //                 }
    //                 if($feeHead->jul == 1){
    //                     foreach($receipts as $receipt){
    //                         if(isset($routeNames) && $receipt->feeHead == $routeNames->routeName){
    //                             if($receipt->july != null){
    //                                 $julT = false;
    //                             }
    //                         }
    //                             if($receipt->july != null){
    //                             $jul = false;
    //                         }
    //                     }
    //                 }
    //                 if($feeHead->aug == 1){
    //                     foreach($receipts as $receipt){
    //                         if(isset($routeNames) && $receipt->feeHead == $routeNames->routeName){
    //                             if($receipt->august != null){
    //                                 $augT = false;
    //                             }
    //                         }
    //                             if($receipt->august == null){
    //                             $aug = false;
    //                         }
    //                     }
    //                 }
    //                 if($feeHead->sep == 1){
    //                     foreach($receipts as $receipt){
    //                         if(isset($routeNames) && $receipt->feeHead == $routeNames->routeName){
    //                             if($receipt->september != null){
    //                                 $sepT = false;
    //                             }
    //                         }
    //                             if($receipt->september != null){
    //                             $sep = false;
    //                         }
    //                     }
    //                 }
    //                 if($feeHead->oct == 1){
    //                     foreach($receipts as $receipt){
    //                         if(isset($routeNames) && $receipt->feeHead == $routeNames->routeName){
    //                             if($receipt->october != null){
    //                                 $octT = false;
    //                             }
    //                         }
    //                             if($receipt->october != null){
    //                             $oct = false;
    //                         }
    //                     }
    //                 }
    //                 if($feeHead->nov == 1){
    //                     foreach($receipts as $receipt){
    //                         if(isset($routeNames) && $receipt->feeHead == $routeNames->routeName){
    //                             if($receipt->november != null){
    //                                 $novT = false;
    //                             }
    //                         }
    //                             if($receipt->november != null){
    //                             $nov = false;
    //                         }
    //                     }
    //                 }
    //                 if($feeHead->dec == 1){
    //                     foreach($receipts as $receipt){
    //                         if(isset($routeNames) && $receipt->feeHead == $routeNames->routeName){
    //                             if($receipt->december != null){
    //                                 $decT = false;
    //                             }
    //                         }
    //                             if($receipt->december != null){
    //                             $dec = false;
    //                         }
    //                     }
    //                 }

    //             }
    //       return view('admin.fee.feeDetail', compact('users', 'id','grades','routes','jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec','janT','febT','marT','aprT','mayT','junT','julT','augT','sepT','octT','novT','decT','balance'));
    //   }


//     public function getFeeDetail($id)
//         {
//             try {
//                 // Fetch the user and related route
//                 $user = User::with('route','feePlans.feeHead')->findOrFail($id);
//                 // Fetch other required data
//                 $routes = $user->route;
//                 $classes = subCode::select('class')->distinct()->orderBy('class')->get();
//                 $receipts = Receipt::where('user_id', $id)->get();
//                 $feeHeads = $user->feePlans->map(function ($feePlan){
//                     return $feePlan->feeHead;
//                 });

//                 $balances = $receipts->sortByDesc('created_at');

//                 // Determine user route details
//                 $userRouteName = $user->route ? $user->route->routeName : null;
//                 $routeNames = RouteName::where('routeName', $userRouteName)->first();

//                 // Redirect if no classes are available
//                 if ($classes->isEmpty()) {
//                     return redirect('admin/fee/feeDetail')->with('failed', "Please create class and Subject first.");
//                 }

//                 // Calculate grades and balance
//                 $grades = $classes->pluck('class')->toArray();
//                 $balance = optional($balances->first())->balance ?? null;

// // Initialize the month status to false (disabled by default)
// $months = ['apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar'];
// $monthStatus = array_fill_keys($months, false); // Default to false

// // Query the database to fetch paid months
// $paidMonthsQuery = Receipt::where('user_id', $user->id)
//     ->select(array_merge(['feeHead'], $months))
//     ->get()
//     ->mapWithKeys(function ($receipt) use ($months) {
//         $paidMonths = [];
//         foreach ($months as $month) {
//             if (!is_null($receipt->{$month})) { // If the month is paid
//                 $paidMonths[$receipt->feeHead][] = $month;
//             }
//         }
//         return [$receipt->feeHead => $paidMonths[$receipt->feeHead] ?? []];
//     });

// // Determine non-paid months for each fee head
// $nonPaidApplicableMonths = [];
// foreach ($feeHeads as $feeHead) {
//     if ($feeHead->name !== 'LATE FEE') {
//         $nonPaidMonths = [];
//         foreach ($months as $month) {
//             if ($feeHead->{$month} == 1 && // Fee is applicable
//                 (!isset($paidMonthsQuery[$feeHead->name]) || !in_array($month, $paidMonthsQuery[$feeHead->name]))) {
//                 $nonPaidMonths[] = $month; // Add unpaid month
//             }
//         }
//         $nonPaidApplicableMonths[$feeHead->name] = $nonPaidMonths;
//     }
// }

// // Flatten unpaid applicable months into `$monthStatus`
// foreach ($nonPaidApplicableMonths as $feeHead => $unpaidMonths) {
//     foreach ($unpaidMonths as $month) {
//         $monthStatus[$month] = true; // Mark unpaid months as available
//     }
// }



// // // Convert the query result into an array of paid months
// // $paidMonths = [];
// // foreach ($paidMonthsQuery as $receipt) {
// //     foreach ($months as $month) {
// //         if ($receipt->{$month} !== null) { // If a month is paid
// //             $paidMonths[$receipt->feeHead][] = $month;
// //         }
// //     }
// // }




// // // Loop through fee heads and determine pending months
// // foreach ($months as $month) {
// //     $isPending = false; // Assume all fee heads for this month are paid (default to false)

// //     foreach ($feeHeads as $feeHead) {
// //         // Check if the fee head is applicable for this month and is unpaid
// //         if (isset($feeHead->{$month}) && $feeHead->{$month} == 1 && !in_array($month, $allPaidMonths)) {
// //             $isPending = true; // At least one fee head is pending for this month
// //             break; // No need to check further for this month
// //         }
// //     }

// //     // Set the month status based on the pending status
// //     $monthStatus[$month] = $isPending;
// // }

// // Debug to verify the updated month status



//                 return view('admin.fee.feeDetail', [
//                     'user' => $user,
//                     'id' => $id,
//                     'grades' => $grades,
//                     'routes' => $routes,
//                     'balance' => $balance,
//                     'monthStatus' => $monthStatus,
//                     // 'transportMonthStatus' => $transportMonthStatus,
//                 ]);

//             } catch (Exception $e) {
//                 return redirect()->back()->with('failed', 'An error occurred while fetching fee details.');
//             }
//         }

public function getFeeDetail($id)
{

    try {
        // Fetch the user and related route
        $user = User::with('route', 'feePlans.feeHead')->findOrFail($id);

        // Fetch other required data
        $routes = $user->route;
        $classes = subCode::select('class')->distinct()->orderBy('class')->get();
        $receipts = Receipt::where('user_id', $id)->get();
        $feeHeads = $user->feePlans->map(function ($feePlan) {
            return $feePlan->feeHead;
        });
        // $balances = $receipts->sortByDesc('created_at') ?? $user->oldBalance;
        $balances = $receipts && $receipts->isNotEmpty()
        ? $receipts->sortByDesc('created_at')
        : collect([$user->oldBalance ?? 0]);
        // Determine user route details
        $userRouteName = optional($user->route)->routeName;
        $routeNames = RouteName::where('routeName', $userRouteName)->first();

        // Redirect if no classes are available
        if ($classes->isEmpty()) {
            return redirect('admin/fee/feeDetail')->with('failed', "Please create class and Subject first.");
        }

        // Calculate grades and balance
        $grades = $classes->pluck('class')->toArray();
        $balance1 = $balances->first();
        $balance  = $balance1->balance ?? $user->oldBalance ?? 0;
        // Initialize the month status to false (disabled by default)
        $months = ['apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar'];
        $monthStatus = array_fill_keys($months, false); // Default to false

            // Query the database to fetch paid months
            $paidMonthsQuery = Receipt::where('user_id', $user->id)
            ->select(array_merge(['feeHead'], $months))
            ->get()
            ->reduce(function ($carry, $receipt) use ($months) {
                // Initialize fee head if not already in the result
                if (!isset($carry[$receipt->feeHead])) {
                    $carry[$receipt->feeHead] = [];
                }

                // Collect paid months
                foreach ($months as $month) {
                    if (!is_null($receipt->{$month}) && !in_array($month, $carry[$receipt->feeHead])) {
                        $carry[$receipt->feeHead][] = $month;
                    }
                }
                return $carry;
            }, []);


        // Determine non-paid months for each fee head
        $nonPaidApplicableMonths = [];
        foreach ($feeHeads as $feeHead) {
            if ($feeHead->name !== 'LATE FEE') {
                $nonPaidMonths = [];
                foreach ($months as $month) {
                    if (isset($feeHead->{$month}) && $feeHead->{$month} == 1 && // Fee is applicable
                        (!isset($paidMonthsQuery[$feeHead->name]) || !in_array($month, $paidMonthsQuery[$feeHead->name]))) {
                        $nonPaidMonths[] = $month; // Add unpaid month
                    }
                }
                $nonPaidApplicableMonths[$feeHead->name] = $nonPaidMonths;
            }
        }

        // Flatten unpaid applicable months into `$monthStatus`
        foreach ($nonPaidApplicableMonths as $feeHead => $unpaidMonths) {
            foreach ($unpaidMonths as $month) {
                $monthStatus[$month] = true; // Mark unpaid months as available
            }
        }

        // Transport-specific logic
        if (optional($user->route)->routeName !== "NA")
        {
            $routePaidMonths = Receipt::where('user_id', $user->id)
                ->where('feeHead', "Transport")
                ->select($months)
                ->get()
                ->flatMap(function ($receipt) use ($months) {
                    $paid = [];
                    foreach ($months as $month) {
                        if (!is_null($receipt->{$month})) {
                            $paid[] = $month;
                        }
                    }
                    return $paid;
                })
                ->toArray();
            // Adjust transport month status
            foreach ($months as $month) {
                if (!in_array($month, $routePaidMonths)) {
                    $monthStatus[$month] = true; // Mark as unpaid for transport
                }
            }
        }

        // Pass the statuses to the view
        return view('admin.fee.feeDetail', [
            'user' => $user,
            'id' => $id,
            'grades' => $grades,
            'routes' => $routes,
            'balance' => $balance,
            'monthStatus' => $monthStatus,
        ]);
    } catch (Exception $e) {
        return redirect()->back()->with('failed', 'An error occurred while fetching fee details.');
    }
}

public function postFeeDetail(Request $request)
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
        return view('admin.fee.deposit', compact(
            'user', 'id', 'oldBalance', 'selectedMonths', 'lateFee', 'totalFee', 'routeName', 'monthsToPay', 'routeHeadTotal',
            'totalConcession', 'netFee', 'totalRouteFee', 'concessionDetails','feePlans', 'feeHeadTotal',
            'routeFeePlan', 'allReceipts', 'months', 'paidMonths', 'routeMonthsToPay'
        ));
        return redirect()->back()->with('failed', 'An error occurred while processing the fee details.');
    }




    public function post_receipt(Request $request)
        {
                // Debug incoming data
                $data = $request->all();
                // Ensure request is POST
            if ($request->method() !== 'POST') {
                return response()->json(['error' => 'Invalid request method. Use POST instead.'], 405);
            }
                // // Check if feeDetails exists
                // if (!isset($data['feeDetails'])) {
                //     return redirect()->back()->with('error', 'No fee details provided.');
                // }

                // Decode selectedMonths if necessary
                $selectedMonths = json_decode($data['selectedMonths'], true);
                if (!is_array($selectedMonths)) {
                    return redirect()->back()->withErrors(['failed' => 'Invalid months selection.']);
                }

                // Fetch user and their fee plans
                $user = User::with(['receipts', 'feePlans.feeHead'])->findOrFail($data['id']);
                $feePlans = $user->feePlans;

                // Generate a new receipt ID
                $receiptId = Receipt::latest('receiptId')->value('receiptId') + 1 ?? 1;



                // Map abbreviated months to full column names
                $monthMap = [
                    'jan' => 'jan',
                    'feb' => 'feb',
                    'mar' => 'mar',
                    'apr' => 'apr',
                    'may' => 'may',
                    'jun' => 'jun',
                    'jul' => 'jul',
                    'aug' => 'aug',
                    'sep' => 'sep',
                    'oct' => 'oct',
                    'nov' => 'nov',
                    'dec' => 'dec',
                ];

                // Step 1: Check for duplicate fee submissions
                    if ($request->has('feeDetails')) {
                        $feeDetails = $data['feeDetails'];

                        // Collect all existing months in user receipts
                        $existingMonths = $user->receipts->flatMap(function ($receipt) use ($monthMap) {
                            $submittedMonths = [];
                            foreach ($monthMap as $dbColumn) {
                                if ($receipt->{$dbColumn}) {
                                    $submittedMonths[] = $dbColumn;
                                }
                            }
                            return $submittedMonths;
                        })->toArray();

                        // Step 2: Check for missing fee heads and duplicate months
                        $duplicateMonths = [];
                        $missingMonths = [];

                        foreach ($feeDetails as $feeHead => $details) {
                            foreach ($details as $month => $value) {
                                if (isset($monthMap[$month])) {
                                    $monthColumn = $monthMap[$month];

                                    // Check for duplicates
                                    $existingReceipt = Receipt::where('user_id', $request->id)
                                        ->where('feeHead', $feeHead)
                                        ->where($monthColumn, $value)
                                        ->exists();

                                    if ($existingReceipt) {
                                        $duplicateMonths[$feeHead][] = ucfirst($month);
                                    }

                                    // Check if any other fee head is submitted for this month
                                    $isAnotherFeeHeadSubmitted = $user->receipts->contains(function ($receipt) use ($monthColumn) {
                                        return $receipt->{$monthColumn};
                                    });

                                    if ($isAnotherFeeHeadSubmitted && !in_array($monthColumn, $existingMonths)) {
                                        $missingMonths[$feeHead][] = ucfirst($month);
                                    }
                                }
                            }
                        }

                        // Step 3: Return errors for duplicates
                        // Handle duplicate fee errors
                        if (!empty($duplicateMonths)) {
                            $errorMessages = [];
                            foreach ($duplicateMonths as $feeHead => $months) {
                                $errorMessages[] = "Fee already submitted for fee head '{$feeHead}' in months: " . implode(', ', $months);
                            }

                            // Redirect to GET route with errors
                            return redirect()->route('fee.form')
                                ->withErrors(['failed' => implode('. ', $errorMessages)])
                                ->withInput();
                        }


                        // Step 4: Save new receipts
                        $allReceipts = []; // Track all saved receipts
                        foreach ($feeDetails as $feeHead => $details) {
                            $total = null;

                            $receipt = new Receipt([
                                'user_id' => $request->id,
                                'receiptId' => $receiptId,
                                'date' => $request->date,
                                'feeHead' => $feeHead,
                                'oldBalance' => $request->oldBalance,
                                'total' => 0, // Placeholder
                                'lateFee' => $request->lateFee,
                                'concession' => $request->concession,
                                'netFee' => $request->netFee,
                                'receivedAmt' => $request->receivedAmt,
                                'balance' => $request->balance,
                                'paymentMode' => $request->paymentType,
                                'bankName' => $request->bankName,
                                'chequeNo' => $request->chequeNo,
                                'chequeDate' => $request->chqDate,
                                'remarks' => $request->remark,
                            ]);

                            $paidMonths = []; // Track months for this fee head
                            $save = false;
                            foreach ($details as $month => $value) {
                                if (isset($monthMap[$month]) && is_numeric($value)) {
                                    $save = true;
                                    $monthColumn = $monthMap[$month];
                                    $receipt->{$monthColumn} = $value; // Assign value to the month's column
                                    $paidMonths[] = ucfirst($month); // Add human-readable month name
                                    $total += $value;
                                }
                            }
                            $receipt->total = $total; // Assign value to the month's column
                            if ($save) {
                                $receipt->save();
                                $allReceipts[] = [
                                    'receipt' => $receipt,
                                    'paidMonths' => implode(', ', $paidMonths), // Combine months into a printable string
                                ];
                            }
                        }

                        // Step 5: Notify about missing fee submissions
                        if (!empty($missingMonths)) {
                            foreach ($missingMonths as $feeHead => $months) {
                                echo "Reminder: Submit '{$feeHead}' for months: " . implode(', ', $months) . ".\n";
                            }
                        }
                    }
                    else {
                    // Default case: Create receipt without feeDetails

                    $receipt = new Receipt([
                        'user_id' => $request->id,
                        'receiptId' => $receiptId,
                        'date' => $request->date,
                        'oldBalance' => $request->oldBalance,
                        'total' => 0, // Placeholder
                        'lateFee' => $request->lateFee,
                        'concession' => $request->concession,
                        'netFee' => $request->netFee,
                        'receivedAmt' => $request->receivedAmt,
                        'balance' => $request->balance,
                        'paymentMode' => $request->paymentType,
                        'bankName' => $request->bankName,
                        'chequeNo' => $request->chequeNo,
                        'chequeDate' => $request->chqDate,
                        'remarks' => $request->remark,
                    ]);
                    $receipt->save();
                }

                // Redirect to the invoice view
                return redirect()->route('fee.invoice', ['id' => $user->id, 'receiptId' => $receiptId])
                    ->with('status', "Fee Submitted Successfully.");

        }


        public function showForm(Request $request)
            {
                $user = User::find($request->id); // Fetch the user data if needed
                return view('admin.fee.form', compact('user'));
            }


        // public function getInvoice(Request $request)
        // {

        //     // Fetch the user and their receipts
        //     $user = User::with(['receipts'])->findOrFail($request->id);

        //     // Fetch the specific receipt using receiptId
        //     $receipt = Receipt::where('receiptId', $request->receiptId)->firstOrFail();

        //     // Prepare `prints` data from the receipt
        //     $prints = collect([$receipt])->map(function ($receipt) {
        //         // Extract paid months dynamically from the receipt columns
        //         $months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
        //         $paidMonths = [];
        //         foreach ($months as $month) {
        //             if (!is_null($receipt->{$month})) {
        //                 $paidMonths[] = ucfirst($month); // Convert to human-readable month name
        //             }
        //         }
        //         return [
        //             'feeHead' => $receipt->feeHead,
        //             'value' => $receipt->total, // Total fee value
        //             'receiptId' => $receipt->receiptId,
        //             'date' => $receipt->date,
        //             'paidMonths' => implode(', ', $paidMonths), // Combine paid months into a string
        //             'oldBalance' => $receipt->oldBalance,
        //             'gTotal' => $receipt->total,
        //             'lateFee' => $receipt->lateFee,
        //             'concession' => $receipt->concession,
        //             'netFee' => $receipt->netFee,
        //             'receivedAmt' => $receipt->receivedAmt,
        //             'balance' => $receipt->balance,
        //             'paymentType' => $receipt->paymentMode,
        //             'bankName' => $receipt->bankName,
        //             'chequeNo' => $receipt->chequeNo,
        //             'chqDate' => $receipt->chequeDate,
        //             'remark' => $receipt->remarks,
        //         ];
        //     });

        //     // Pass user and invoice details to the view
        //     return view('admin.fee.payment-invoice', compact('user', 'prints'))
        //         ->with('status', "Fee Submitted Successfully.");
        // }

        public function getInvoice(Request $request)
        {
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
            // Pass user and invoice details to the view
            return view('admin.fee.payment-invoice', compact('user', 'prints', 'receipt', 'receipts'))
                ->with('status', "Fee Submitted Successfully.");
        }


    public function feeCard($id)
    {
        $users  = User::all()->WHERE('id',$id);
        $receipts = Receipt::all()->where('user_id',$id);

    return view('admin.fee.feeCard', compact('users', 'id','receipts'));

    }

    // public function editFeeReceipt($id)
    // {

    //     $receipt = Receipt::where('receiptId', $id)->firstOrFail();
    //     $user = User::with(['route', 'category', 'feePlans.feeHead'])->findOrFail($receipt->user_id);
    //     $feePlans = $user->feePlans()->with('feeHead')->get();
    //     $months = ['apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar'];

    //     $receipts = Receipt::where('user_id', $user->id)
    //     ->where('receiptId', $id) //
    //     ->get();
    //     // Collect all paid months from previous receipts along with their feeHead
    //     $previousReceipts = Receipt::where('user_id', $user->id)
    //         ->where('receiptId', '<>', $id) // Exclude the current receipt
    //         ->get();

    //     // Structure to store feeHeads and their paid months for previous receipts
    //     $paidMonthsFromPreviousReceipts = [];
    //     $previousReceipts->each(function ($receipt) use ($months, &$paidMonthsFromPreviousReceipts) {
    //         foreach ($months as $month) {
    //             if (!is_null($receipt->{$month})) {
    //                 // Assuming feeHead is part of the feePlans for this receipt
    //                 $feeHead = $receipt->feeHead;

    //                 if ($feeHead) {
    //                     $paidMonthsFromPreviousReceipts[$feeHead][] = $month;
    //                 }
    //             }
    //         }
    //     });
    //     // Extract paid months from the current receipt (for editing purposes)
    //     $selectedMonths = [];
    //     foreach($receipts as $receipt){

    //         foreach ($months as $month) {
    //             // Check if the month is paid in the current receipt
    //             if (!is_null($receipt->{$month}) && $receipt->{$month} != 0) {
    //                 // Ensure the feeHead for this month is also considered if needed
    //                 $feeHead = $receipt->feeHead;

    //                 // Add the month if the feeHead is found and it's considered paid
    //                 if ($feeHead) {
    //                     $selectedMonths[$feeHead][] = $month;
    //                 }
    //             }
    //         }
    //     }
    //     // Determine applicable months for each feeHead
    //     $feeHeadApplicableMonths = $feePlans->mapWithKeys(function ($feePlan) use ($months) {
    //         $applicableMonths = [];
    //         foreach ($months as $month) {
    //             if ($feePlan->feeHead->{$month} == 1) { // Check if this month is applicable
    //                 $applicableMonths[] = $month;
    //             }
    //         }
    //         return [$feePlan->feeHead->name => $applicableMonths];
    //     });

    //     return view('admin.fee.editFeeReceipt', compact(
    //         'receipt', 'user', 'feePlans', 'paidMonthsFromPreviousReceipts', 'selectedMonths', 'months', 'feeHeadApplicableMonths', 'id',
    //     ));
    // }

    public function editFeeReceipt($id)
    {
        $receipt = Receipt::where('receiptId', $id)->firstOrFail();
        $user = User::with(['route', 'category', 'feePlans.feeHead'])->findOrFail($receipt->user_id);
        $feePlans = $user->feePlans()->with('feeHead')->get();
        $months = ['apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar'];

        $receipts = Receipt::where('user_id', $user->id)
            ->where('receiptId', $id)
            ->get();

        $previousReceipts = Receipt::where('user_id', $user->id)
            ->where('receiptId', '<>', $id)
            ->get();

        // Store feeHeads and their paid months for previous receipts
        $paidMonthsFromPreviousReceipts = [];
        $previousReceipts->each(function ($receipt) use ($months, &$paidMonthsFromPreviousReceipts) {
            foreach ($months as $month) {
                if (!is_null($receipt->{$month})) {
                    $feeHead = $receipt->feeHead;
                    if ($feeHead) {
                        $paidMonthsFromPreviousReceipts[$feeHead][] = $month;
                    }
                }
            }
        });

        // Extract paid months from the current receipt (for editing purposes)
        $selectedMonths = [];
        foreach ($receipts as $receipt) {
            foreach ($months as $month) {
                if (!is_null($receipt->{$month}) && $receipt->{$month} != 0) {
                    $feeHead = $receipt->feeHead;
                    if ($feeHead) {
                        $selectedMonths[$feeHead][] = $month;
                    }
                }
            }
        }

        // Determine applicable months for each feeHead
        $feeHeadApplicableMonths = $feePlans->mapWithKeys(function ($feePlan) use ($months) {
            $applicableMonths = [];
            foreach ($months as $month) {
                if ($feePlan->feeHead->{$month} == 1) {
                    $applicableMonths[] = $month;
                }
            }
            return [$feePlan->feeHead->name => $applicableMonths];
        });

        // Fetch transport fees
        $transportFee = optional($user->route)->fee ?? 0;
        $transportPaidMonths = [];

        foreach ($receipts as $receipt) {
            if ($receipt->feeHead === "Transport"){
                foreach ($months as $month) {
                    if (!is_null($receipt->{$month}) && $receipt->{$month} != 0) {
                        $transportPaidMonths[] = $month;
                        $transportFee = $receipt->{$month};

                    }
                }
            }
        }

        // Ensure transport fee calculation is valid
        $totalTransportFee = 0;
        if ($transportFee > 0) {
            foreach ($months as $month) {
                if (in_array($month, $transportPaidMonths)) {
                    echo $transportFee . ", ";

                    $totalTransportFee += $transportFee;

                }
            }
        }

        // dd($user->route,$transportFee, $transportPaidMonths, $totalTransportFee);

        return view('admin.fee.editFeeReceipt', compact(
            'receipt', 'user', 'feePlans', 'paidMonthsFromPreviousReceipts', 'selectedMonths', 'months', 'feeHeadApplicableMonths', 'id', 'transportFee', 'transportPaidMonths', 'totalTransportFee'
        ));
    }



    public function post_editFeeReceipt(Request $request)
    {
        $data = $request->input();
        $id = $data['id'];
        $feeDetails = $data['feeDetails']; // Fee details for each fee head and month
        try {
            DB::transaction(function () use ($data, $id, $feeDetails) {
                // Update the receipt meta-information
                DB::table('receipts')
                    ->where('receiptId', $id)
                    ->update([
                        'date' => $data['date'],
                        'lateFee' => $data['lateFee'],
                        'concession' => $data['concession'],
                        'netFee' => $data['netFee'],
                        'receivedAmt' => $data['receivedAmt'],
                        'balance' => $data['balance'],
                        'paymentMode' => $data['paymentType'],
                        'bankName' => $data['bankName'],
                        'chequeNo' => $data['chequeNo'],
                        'chequeDate' => $data['chqDate'],
                        'remarks' => $data['remark'],
                    ]);

                // Update fee details based on feeHead and months
                foreach ($feeDetails as $feeHead => $months) {

                    // if ($feeHead === 'MONTHLY') {
                    //     continue; // Skip this iteration and move to the next one
                    // }
                    $updateData = array_fill_keys(['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'], NULL);

                    $total = 0;
                    foreach ($months as $month => $value) {
                        if($value === "null"){
                            $value = null;
                        }
                        $total +=  $value;
                        $updateData[strtolower($month)] = $value; // Map month to the corresponding column
                    }
                    // Check if all values in $updateData are NULL
                    $allNull = array_filter($updateData, fn($value) => $value !== NULL) === [];

                    if ($allNull) {
                        // Ensure the update query still runs to set all months to NULL
                        DB::table('receipts')
                            ->where('receiptId', $id)
                            ->where('feeHead', $feeHead)
                            ->update(array_merge($updateData, ['total' => $total]));

                    } else {
                        // Perform the regular update
                        DB::table('receipts')
                            ->where('receiptId', $id)
                            ->where('feeHead', $feeHead)
                            ->update(array_merge($updateData, ['total' => $total]));

                    }
                }
            });

            return redirect('fee/feeCard/' . $data['user_id'])->with('status', 'Record updated successfully');
        } catch (\Exception $e) {
            return redirect('fee/feeCard/' . $data['user_id'])->with('failed', $e->getMessage());
        }
    }

    // public function post_editFeeReceipt(Request $request)
    // {
    //     $data = $request->input();
    //     $id = $data['id'];
    //     $feeDetails = $data['feeDetails']; // Fee details for each fee head and month


    //     try {
    //         DB::transaction(function () use ($data, $id, $feeDetails) {
    //             // Update the receipt meta-information
    //             DB::table('receipts')
    //                 ->where('receiptId', $id)
    //                 ->update([
    //                     'date' => $data['date'],
    //                     'lateFee' => $data['lateFee'],
    //                     'concession' => $data['concession'],
    //                     'netFee' => $data['netFee'],
    //                     'receivedAmt' => $data['receivedAmt'],
    //                     'balance' => $data['balance'],
    //                     'paymentMode' => $data['paymentType'],
    //                     'bankName' => $data['bankName'],
    //                     'chequeNo' => $data['chequeNo'],
    //                     'chequeDate' => $data['chqDate'],
    //                     'remarks' => $data['remark'],
    //                 ]);
    //                 dd($feeDetails);
    //             // Update fee details based on feeHead and months
    //             foreach ($feeDetails as $feeHead => $months) {

    //                 // Define all months in the table
    //                 $allMonths = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];

    //                 // Initialize all months to NULL
    //                 $updateData = array_fill_keys($allMonths, NULL);

    //                 // Overwrite with values from feeDetails
    //                 foreach ($months as $month => $value) {
    //                     $updateData[strtolower($month)] = $value; // Map month to the corresponding column
    //                 }

    //                 // Debug to ensure proper data structure
    //                 // dd($updateData);

    //                 // Update the receipt table with the dynamic month data
    //                 DB::table('receipts')
    //                     ->where('receiptId', $id)
    //                     ->where('feeHead', $feeHead)
    //                     ->update($updateData);
    //             }
    //         });

    //         return redirect('fee/feeCard/' . $data['user_id'])->with('status', 'Record updated successfully');
    //     } catch (\Exception $e) {
    //         return redirect('fee/feeCard/' . $data['user_id'])->with('failed', $e->getMessage());
    //     }
    // }

        public function deleteFeeReceipt($receiptId,$user_id){
            try{
                $records = Receipt::all()->where('receiptId',$receiptId);
              //  print_r($records);
                foreach($records as $record){
                $record->delete($record->id);
                }
                return redirect('fee/feeCard/' . $user_id)->with('delete','Record deleted successfully');
            }
            catch(Exception $e){
                return redirect('fee/feeCard/' . $user_id)->with('failed',"operation failed");

            }
        }

        public function dayBook(){

            return view('admin.fee.dayBook');
        }

        public function searchDaybook(Request $request){
            $data = $request->input();
            $receipts = Receipt::whereDate('date', '>=', $data['from'])
            ->whereDate('date', '<=', $data['to'])
            ->get();
            return view('admin.fee.dayBook', compact('receipts'));

        }

        public function dueList(){
            $categories = Category::all();
            $classes = subCode::all('class')->unique('class');
            $routes = RouteName::all();
            $user = User::first();

            return view('admin.fee.dueList', compact('categories', 'classes', 'routes'));
        }



        // public function post_dueList(Request $request)
        //     {
        //         $data = $request->input();

        //         // Define all months
        //         $months = ['apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar'];

        //         // Get selected months
        //         $selectedMonths = array_filter($data, function ($value, $key) use ($months) {
        //             return $value === 'on' && in_array($key, $months);
        //         }, ARRAY_FILTER_USE_BOTH);

        //         $selectedMonthNames = array_keys($selectedMonths); // Extract only the keys (month names)

        //         // Fetch users with dynamic filtering
        //         $users = User::with(['receipts', 'route', 'category'])->where(function ($query) use ($data) {
        //             if ($data['route'] !== 'all') {
        //                 $query->where('route', $data['route']);
        //             }
        //             if ($data['category'] !== 'all') {
        //                 $category = Category::where('category', $data['category'])->first();
        //                 if ($category) {
        //                     $query->where('category_id', $category->id);
        //                 }
        //             }
        //             if ($data['class'] !== 'all') {
        //                 $query->where('grade', $data['class']);
        //             }
        //         })->get();

        //         // Cache data
        //         $categories = Cache::remember("category_{$data['category']}", 3600, function () use ($data) {
        //             return $data['category'] === 'all'
        //                 ? Category::all()->pluck('category', 'id')
        //                 : Category::where('category', $data['category'])->pluck('category', 'id');
        //         });

        //         $classes = Cache::remember("classes_{$data['class']}", 3600, function () use ($data) {
        //             return $data['class'] === 'all'
        //                 ? subCode::distinct('class')->pluck('class')
        //                 : subCode::where('class', $data['class'])->distinct('class')->pluck('class');
        //         });

        //         $routes = Cache::remember("routes_{$data['route']}", 3600, function () use ($data) {
        //             return $data['route'] === 'all'
        //                 ? routeFeePlan::distinct('routeName')->pluck('routeName')
        //                 : routeFeePlan::where('routeName', $data['route'])->distinct('routeName')->pluck('routeName');
        //         });

        //         // Calculate fees
        //         $classArray = $this->calculateClassFees($data, $categories, $classes, $selectedMonthNames);
        //         $routeArray = $this->calculateRouteFees($data, $routes, $selectedMonthNames);

        //         // Filter students who missed at least one selected month
        //         $students = $users->filter(function ($user) use ($selectedMonthNames) {
        //             foreach ($selectedMonthNames as $month) {
        //                 if ($user->receipts->where('month', $month)->sum('receivedAmt') == 0) {
        //                     return true; // Include if any selected month is unpaid
        //                 }
        //             }
        //             return false;
        //         })->map(function ($user) use ($classArray, $routeArray, $selectedMonthNames) {
        //             // Start with old balance
        //             $due = ($user->oldBalance ?? 0);

        //             // Add class fees
        //             foreach ($classArray as $class) {
        //                 if ($class['class'] === $user->grade && $class['category'] === $user->category->category) {
        //                     $due += $class['value'];
        //                 }
        //             }

        //             // Add route fees
        //             foreach ($routeArray as $route) {
        //                 if (!empty($user->route->routeName) && $route['routeName'] === $user->route->routeName) {
        //                     $due += $route['routeValue'];
        //                 }
        //             }

        //             // **Subtract receipt amounts only ONCE**
        //             $receivedAmount = $user->receipts
        //                 ->unique('receiptId') // Ensures only unique receipts are counted
        //                 ->sum('receivedAmt');

        //             // Get Receipts late fee
        //             $pastLateFee = $user->receipts
        //                 ->unique('receiptId')
        //                 ->sum('lateFee') ?? 0;

        //             // Get Receipts Concession
        //             $pastConcession = $user->receipts
        //                 ->unique('receiptId')
        //                 ->sum('concession') ?? 0;

        //             $due += $pastLateFee;
        //             $due -= $pastConcession;
        //             $due -= $receivedAmount;

        //             // **Add Late Fee (Fixed to Use $selectedMonthNames)**
        //             $lateFee = $this->calculateLateFee($user, $selectedMonthNames);
        //             $due += $lateFee;

        //             return [
        //                 'id' => $user->id,
        //                 'class' => $user->grade,
        //                 'name' => $user->name,
        //                 'fName' => $user->fName,
        //                 'mobile' => $user->mobile,
        //                 'due' => max(0, $due), // Ensure no negative due amount
        //                 'routeName' => $user->route->routeName ?? null,
        //                 'category' => $user->category->category ?? null,
        //             ];
        //         });

        //         return view('admin.fee.dueListAllRecords', [
        //             'students' => $students,
        //             'selectedMonths' => $selectedMonths,
        //             'selectedMonthNames' => $selectedMonthNames,
        //         ]);
        //     }


        public function post_dueList(Request $request)
            {

                $data = $request->input();

                $dueData = $this->calculateDueByMonth(); // It is use for dashboard charts purpose

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

                    // if($user->name === "Yuvraj"){
                    //     // dd($paidMonths, $unpaidMonths);
                    //     // dd($due, $pastLateFee, $pastConcession,$receivedAmount,$concessionAmount,$totalConcession,count($unpaidMonths,),$paidMonths);
                    // }
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
                // dd($students,$selectedMonths,$selectedMonthNames);
                return view('admin.fee.dueListAllRecords', [
                    'students' => $students,
                    'selectedMonths' => $selectedMonths,
                    'selectedMonthNames' => $selectedMonthNames,
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





    public function printReceipt($id){


    //   //  $users =  User::where('id',$id)->with('getReceipt')->get();
    //     $receipts = Receipt::all()->where('receiptId', $id);
    //   //  print_r(($receipts));
    //     $i=0;
    //     // foreach ($receipts as $receipt) {
        //     $user = User::where('id',$receipt->user_id)->first();
        //     $receipt = $receipt;
        // $prints[$i]['feeHead'] = $receipt->feeHead;
        // $prints[$i]['receiptId']= $receipt->receiptId;
        // $prints[$i]['date']= $receipt->date;
        // $prints[$i]['oldBalance']= $receipt->oldBalance;
        // $prints[$i]['gTotal']= $receipt->netFee;
        // $prints[$i]['lateFee']= $receipt->lateFee;
        // $prints[$i]['concessionP']= $receipt->concessionP;
        // $prints[$i]['concession']= $receipt->concession;
        // $prints[$i]['netFee']= $receipt->netFee;
        // $prints[$i]['receivedAmt']= $receipt->receivedAmt;
        // $prints[$i]['balance']= $receipt->balance;
        // $prints[$i]['paymentType']= $receipt->paymentMode;
        // $prints[$i]['bankName']= $receipt->bankName;
        // $prints[$i]['chequeNo']= $receipt->chequeNo;
        // $prints[$i]['chqDate']= $receipt->chqDate;
        // $prints[$i]['remark']= $receipt->remarks;
        // $prints[$i]['january']= $receipt->january;
        // $prints[$i]['february']= $receipt->february;
        // $prints[$i]['march']= $receipt->march;
        // $prints[$i]['april']= $receipt->april;
        // $prints[$i]['may']= $receipt->may;
        // $prints[$i]['june']= $receipt->june;
        // $prints[$i]['july']= $receipt->july;
        // $prints[$i]['august']= $receipt->august;
        // $prints[$i]['september']= $receipt->september;
        // $prints[$i]['october']= $receipt->october;
        // $prints[$i]['november']= $receipt->november;
        // $prints[$i]['december']= $receipt->december;
        // $prints[$i]['value'] = null;

        // $i++;
        // }

    //    return view('admin.fee.printReceipt', compact('user','prints',));

     // Fetch the user and their receipts

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
       return view('admin.fee.payment-invoice', compact('user', 'prints', 'receipt', 'receipts'))
       ->with('status', "Fee Submitted Successfully.");
    }


    public function applyConcession(Request $request)
        {


            if ($request->isMethod('post')) {

                    $data = $request->validate([
                        'user_id' => 'required|exists:users,id',
                        'fee_plan_id' => 'required|exists:fee_plans,id',
                        'concession_fee' => 'nullable|numeric:concession_fee',
                        'concession_type' => 'nullable|string',
                        'reason' => 'nullable|string',
                    ]);
                    echo $data['concession_type'];
                                    try {
                    // Fetch fee plan details
                    $feePlan = FeePlan::with("feeHead")->findOrFail($data['fee_plan_id']);

                    // Calculate concession fee
                    $concessionFee = $data['concession_fee'];

                    if ($data['concession_type'] === 'Percentage') {
                        $concessionFee = ($feePlan->value * $data['concession_fee']) / 100;
                    }

                    // Save concession record
                    Concession::create([
                        'user_id' => $data['user_id'],
                        'fee_plan_id' => $feePlan->id,
                        'fee_type' => $feePlan->feeHead->name,
                        'concession_fee' => $concessionFee,
                        'reason' => $data['reason'],
                    ]);

                    return redirect()->back()->with('status', 'Concession applied successfully.');
                } catch (\Exception $e) {
                    // Log the error for debugging
                    Log::error('Error applying concession: ', [
                        'message' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                    return redirect()->back()->with('failed', 'An error occurred while applying the concession.');
                }
            }
            if ($request->isMethod('get')) {
                // Fetch users with past concessions
                $usersWithConcessions = Concession::with('user', 'feePlan')
                    ->orderBy('created_at', 'desc')
                    ->get();

                // Fetch fee plans for selection
                $feePlans = FeePlan::with('feeHead')->get();

                return view('admin.fee.concession', compact('usersWithConcessions', 'feePlans'));
            }
        }



        public function getUserFeePlans(Request $request, $userId)
            {
                try {
                    // Fetch the user with relationships
                    $user = User::with('category')->findOrFail($userId);

                    // Get the user's class and category
                    $userClass = $user->grade;
                    $userCategory = $user->category->category ?? null;

                    // Get fee_plan_ids already conceded for the user
                    $concededFeePlans = Concession::where('user_id', $userId)
                        ->pluck('fee_plan_id')
                        ->toArray();

                    // Filter fee plans based on class, category, and exclude already conceded fee plans
                    $feePlans = FeePlan::where('class', $userClass)
                        ->when($userCategory, function ($query) use ($userCategory) {
                            return $query->where('category', $userCategory);
                        })
                        ->whereNotIn('id', $concededFeePlans) // Exclude conceded fee plans
                        ->get();

                    return response()->json([
                        'status' => 'success',
                        'feePlans' => $feePlans,
                    ], 200);
                } catch (\Exception $e) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Error fetching fee plans for the user.',
                        'error' => $e->getMessage(),
                    ], 500);
                }
            }

            public function editConcession($id)
                {
                    try {
                        // Fetch the concession record
                        $concession = Concession::with('user', 'feePlan')->findOrFail($id);

                        // Fetch all fee plans for dropdown
                        $feePlans = FeePlan::all();

                        return view('admin.fee.edit-concession', compact('concession', 'feePlans'));
                    } catch (\Exception $e) {
                        return redirect()->back()->with('failed', 'Error fetching concession details.');
                    }
                }

            public function updateConcession(Request $request, $id)
                {
                    $data = $request->validate([
                        'fee_plan_id' => 'required|exists:fee_plans,id',
                        'concession_fee' => 'required|numeric|min:0',
                        'concession_type' => 'required|string|in:Percentage,Amount',
                        'reason' => 'nullable|string',
                    ]);

                    try {
                        $concession = Concession::findOrFail($id);
                        $feePlan = FeePlan::findOrFail($data['fee_plan_id']);

                        // Calculate the updated concession fee
                        $concessionFee = $data['concession_fee'];
                        if ($data['concession_type'] === 'Percentage') {
                            $concessionFee = ($feePlan->value * $data['concession_fee']) / 100;
                        }

                        // Update the concession record
                        $concession->update([
                            'fee_plan_id' => $feePlan->id,
                            'fee_type' => $feePlan->feeHead,
                            'concession_fee' => $concessionFee,
                            'reason' => $data['reason'],
                        ]);

                        return redirect()->route('applyConcession')->with('status', 'Concession updated successfully.');
                    } catch (\Exception $e) {
                        return redirect()->back()->with('failed', 'Error updating the concession.');
                    }
                }

                public function deleteConcession($id)
                    {
                        try {
                            // Find the concession record
                            $concession = Concession::findOrFail($id);

                            // Delete the record
                            $concession->delete();

                            // Redirect back with success message
                            return redirect()->back()->with('status', 'Concession deleted successfully.');
                        } catch (\Exception $e) {
                            // Log the error for debugging
                            Log::error('Error deleting concession: ', [
                                'message' => $e->getMessage(),
                                'trace' => $e->getTraceAsString(),
                            ]);

                            // Redirect back with error message
                            return redirect()->back()->with('failed', 'An error occurred while deleting the concession.');
                        }
                    }


                    private function calculateConcessions($userId, $selectedMonths)
                    {
                        // Fetch concessions for the student
                        $concessions = Concession::where('user_id', $userId)->get();
                        $feePlans = FeePlan::whereIn('id', $concessions->pluck('fee_plan_id'))->get();

                        $concessionDetails = [];
                        $totalConcession = 0;

                        foreach ($concessions as $concession) {
                            $feePlan = $feePlans->where('id', $concession->fee_plan_id)->first();
                            if ($feePlan) {
                                // Fetch the related feeHead and its applicable months
                                $feeHead = FeeHead::where('name', $feePlan->feeHead)->first();
                                if ($feeHead) {
                                    // Determine the applicable months for the feeHead
                                    $applicableMonths = [];
                                    foreach (['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'] as $month) {
                                        if ($feeHead->$month) {
                                            $applicableMonths[] = $month;
                                        }
                                    }

                                    // Intersect applicable months with selected months
                                    $commonMonths = array_intersect($selectedMonths, $applicableMonths);

                                    // Calculate the concession for the common months
                                    $feePerMonth = $feePlan->value; // Distribute fee over applicable months
                                    $monthlyConcession = $concession->concession_fee;

                                    $totalConcession += $monthlyConcession * count($commonMonths);

                                    $concessionDetails[] = [
                                        'fee_plan' => $feePlan->feeHead,
                                        'applicable_months' => $commonMonths,
                                        'monthly_concession' => $monthlyConcession,
                                        'total_concession' => $monthlyConcession * count($commonMonths),
                                    ];
                                }
                            }
                        }

                        return [
                            'concessionDetails' => $concessionDetails,
                            'totalConcession' => $totalConcession,
                        ];
                    }



    public function attachFeePlansToUsers()
        {
            try {
                // Fetch all users along with their category
                $users = User::with('category')->get();

                foreach ($users as $user) {
                    // Find fee plans matching the user's grade and category
                    $matchingFeePlans = FeePlan::where('class', $user->grade)
                        ->when($user->category, function ($query, $category) {
                            $query->where('category', $category->category ?? null);
                        })
                        ->pluck('id')
                        ->toArray();

                    // Attach fee plans to the user without detaching existing ones
                    if (!empty($matchingFeePlans)) {
                        $user->feePlans()->syncWithoutDetaching($matchingFeePlans);
                    }
                }

                return response()->json(['status' => 'success', 'message' => 'Fee plans attached to users successfully.']);
            } catch (\Exception $e) {
                // Log the error for debugging
                \Log::error('Error attaching fee plans: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

                return response()->json(['status' => 'error', 'message' => 'An error occurred while attaching fee plans.']);
            }
        }



    }
