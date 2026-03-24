<?php

namespace App\Http\Controllers\Fee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\RouteName;
use Illuminate\Support\Facades\Validator;
use DB;
use App\routeFeePlan;

class TransportController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth:admin');
    }



    public function createRoute()
    {

        return view('admin.fee.transport.createRoute');
    }


    public function post_createRoute(Request $request)
    {

        $rules = [
			'routeName' => 'required', 'string', 'max:255',
            'frequency' =>  'required', 'string', 'max:255',

		];
		$validator = Validator::make($request->all(),$rules);
		if ($validator->fails()) {
			return redirect('fee/createRoute')
			->withInput()
			->withErrors($validator);
		}
		else{
            $data = $request->input();
			try{
        $routeName = strtoupper($data['routeName']);
        $accountName = 'TRANSPORT';
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

				$feeHead = new RouteName;
                $feeHead->routeName = $routeName;
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
				return redirect('fee/createRoute')->with('status','Insert successfully');
			}
			catch(Exception $e){
				return redirect('fee/createRoute')->with('failed',"operation failed");
			}
		}
    }


    public function viewRoute()
    {
        $routeNames = RouteName::all();
        return view('admin.fee.transport.viewRoute', compact('routeNames'));

    }


    public function editRoute(Request $request, $id)
    {
        $routeNames  = RouteName::all()->WHERE('id',$id);
        return view('admin.fee.transport.editRoute', compact('routeNames', 'id'));
    }

    public function post_editRoute(Request $request){
        $data = $request->input();
        $id = $data['id'];
        $routeName = strtoupper($data['routeName']);

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

        DB::table('route_names')
        ->where('id', $id)
        ->update([
        'routeName' => $routeName,
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
          return redirect('fee/viewRoute')->with('status','Record updated successfully');
                  }

        catch(Exception $e){
          return redirect('fee/viewRoute')->with('failed',"operation failed");

      }
    }


    public function deleteRouteName($id){
        try{
            $record = RouteName::find($id);

            $record->delete($record->id);

            return redirect('fee/viewRoute')->with('delete','Route deleted successfully');
        }
        catch(Exception $e){
            return redirect('fee/viewRoute'.$id)->with('failed',"operation failed");

        }
      }


    public function routeFeePlan()
    {

        $routeNames = RouteName::all();
        $routeFeePlans = routeFeePlan::all();
        return view('admin.fee.transport.routeFeePlan', compact('routeNames','routeFeePlans'));
    }

    public function post_routeFeePlan(Request $request)
    {
        $rules = [
			'value' => 'required', 'integer',
        	];
		$validator = Validator::make($request->all(),$rules);
		if ($validator->fails()) {
			return redirect('fee/routeFeePlan')
			->withInput()
			->withErrors($validator);
		}
		else{
            $data = $request->input();
            $route[] = $data['route'];
            $value[] = $data['value'];

			try{
                        $routeFeePlan = new routeFeePlan;
                        $routeFeePlan->routeName = $data['route'];
                        $routeFeePlan->value = $data['value'];
                        $routeFeePlan->save();

				return redirect('fee/routeFeePlan')->with('status','Insert successfully');
			}
			catch(Exception $e){
				return redirect('fee/routeFeePlan')->with('failed',"operation failed");
			}
		}
    }

    public function editRouteFeePlan(Request $request, $id)
    {
        $routeNames = RouteName::all();
        $routeFeePlans  = routeFeePlan::all()->WHERE('id',$id);

        return view('admin.fee.transport.editRouteFeePlan', compact('routeNames', 'routeFeePlans','id'));
    }

    public function post_editRouteFeePlan(Request $request){
        $data = $request->input();
        $id = $data['id'];
        try{
        DB::table('route_fee_plans')
        ->where('id', $id)
        ->update([
            'routeName' => $data['routeName'],
            'value' => $data['value'],
        ]);
          return redirect('fee/routeFeePlan')->with('status','Record updated successfully');
                  }
        catch(Exception $e){
          return redirect('fee/routeFeePlan')->with('failed',"operation failed");

      }
    }

    public function deleteRouteFeePlan($id){
        try{
            $record = routeFeePlan::find($id);

            $record->delete($record->id);

            return redirect('fee/routeFeePlan')->with('delete','Record deleted successfully');
        }
        catch(Exception $e){
            return redirect('fee/routeFeePlan'.$id)->with('failed',"operation failed");

        }
      }
}
