<?php

namespace App\Http\Controllers\Users\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Holiday;
use App\StudentAttendance;
use Auth;


class CalendarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web');
    }
    public function calendar()
    {
        $holidays = Holiday::all();
        $stuAttendances = StudentAttendance::all()->where('rfid',Auth::user()->rfid);
        $events = array();
        foreach($holidays as $holiday){
            $events[] = [
                'title' => $holiday->title,
                'start' => $holiday->start,
                'end' => $holiday->end,
                'className' => 'bg-warning'
            ];
        }
        foreach($stuAttendances as $stuAttendance){

            $start = null;
            if(isset($stuAttendance->att0)){
                $olddate = strtotime($stuAttendance->att0);
                $start = date('Y-m-d',$olddate);
            }
         //   if(isset($stuAttendance->departure)){
          //      $olddate = strtotime($stuAttendance->departure);
         //       $start = date('Y-m-d',$olddate);
         //   }
            $events[] = [
                'title' => 'Present',
                'start' => $start,
                'className' => 'bg-success'
            ];
        }
        if(!isset($stuAttendance)){
            $stuAttendance = null;
        }
            $events[] = [
                'daysOfWeek' => [0,7], //Sundays and saturdays
                'rendering' => 'background',
                'className' => 'bg-info',
                'overLap' => false,
                'allDay' => true
            ];
        return view('student/calendar', compact('events','stuAttendance'));
    }
}