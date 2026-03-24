<?php

namespace App\Http\Controllers\Users\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Holiday;
use App\TeacherAttendance;
use Auth;

class CalendarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:teacher');
    }
    public function calendar()
    {
        $holidays = Holiday::all();
        $TeacherAttendances = TeacherAttendance::all()->where('email',Auth::user()->email);
        $events = array();
        foreach($holidays as $holiday){
            $events[] = [
                'title' => $holiday->title,
                'start' => $holiday->start,
                'end' => $holiday->end,
                'className' => 'bg-warning'
            ];
        }
        foreach($TeacherAttendances as $TeacherAttendance){
            $name = $TeacherAttendance->name;
            $start = null;
            if(isset($TeacherAttendance->att0)){
                $olddate = strtotime($TeacherAttendance->att0);
                $out = strtotime($TeacherAttendance->att1);
                $start = date('Y-m-d',$olddate);
                $inTime = date('H:i',$olddate);
                $outTime = date('H:i',$out);
            }
         //   if(isset($TeacherAttendance->departure)){
          //      $olddate = strtotime($TeacherAttendance->departure);
         //       $start = date('Y-m-d',$olddate);
         //   }
         if(isset($TeacherAttendance->att0)){
            $events[] = [
                'title' => $inTime,
                'start' => $start,
                'className' => 'bg-success'
            ];
        }
            if(isset($TeacherAttendance->att1)){
            $events[] = [
                'title' => $outTime,
                'start' => $start,
                'className' => 'bg-info'
            ];
            }
        }
        if(!isset($TeacherAttendance)){
            $TeacherAttendance = null;
        }
            $events[] = [
                'daysOfWeek' => [0,7], //Sundays and saturdays
                'rendering' => 'background',
                'className' => 'bg-info',
                'overLap' => false,
                'allDay' => true
            ];
        return view('teacher.calendar', compact('events','TeacherAttendance','name'));
    } 
}
