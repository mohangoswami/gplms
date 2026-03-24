<?php

namespace App\Http\Controllers\Users\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Holiday;
use App\TeacherAttendance;
use App\StudentAttendance;
use App\Teacher;
use App\User;
use DB;


class Calendar extends Controller
{
    public function calendar()
    {
        $events = Holiday::all();
        
        return view('admin/calendar/calendar', compact('events'));
    }

    public function addHolidays(){
        return view('admin/calendar/addHolidays');
    }

  public function postHolidays(Request $request){
    $data = $request->input();
    try{
      $holidays = new Holiday;
      $holidays->title = $data['title'];

      $daterange = $data['datetimes'];        
      $split = explode('-', $daterange);
      $start = date("Y-m-d ", strtotime($split[0]));
      $end = date("Y-m-d ", strtotime($split[1]));
      $holidays->start = $start;
      $holidays->end = $end;
      $holidays->save();
      return redirect('admin/addHolidays')->with('status','Insert successfully');
    }
    catch(Exception $e){
      return redirect('admin/addHolidays')->with('failed',"operation failed");
    }
  }

  public function dayswiseAttendance()
  {
      $attendances  = TeacherAttendance::all()->sortByDesc('att0');;
      $teachers  = Teacher::all();   
   
      return view('admin.calendar.teacher.dayswiseAttendance', compact('attendances','teachers'));
  }


  public function teachersAttendenceDatewise($data)
  {
    $date = date('Y-m-d', strtotime($data));
    $attendances = DB::table('teacher_attendances')
    ->select('*')
    ->where(DB::raw('CAST(att0 as date)'), '=', $date) 
    ->get()->sortByDesc('att0');    
    foreach($attendances as $attendance){
        $presents[] = $attendance->name;
    }

    $absents  = DB::table('teachers')->whereNotIn('name',$presents)->get();

    return view('admin.calendar.teacher.teachersAttendenceDatewise', compact('attendances','absents'));

    foreach($teachers as $teacher){

    }
    }

    public function teachersAttendance()
    {
        $teachers  = Teacher::all();   
     
        return view('admin.calendar.teacher.teachersAttendance', compact('teachers'));
    }

    public function teacherCalendar($rfid)
    {
        $holidays = Holiday::all();
        $TeacherAttendances = TeacherAttendance::all()->where('rfid',$rfid);
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
        return view('admin.calendar.teacher.teacherCalendar', compact('events','TeacherAttendance','name'));
    }
    
    public function teacherAttendance($rfid)
    {
        $attendances  = TeacherAttendance::all()->where('rfid',$rfid)->sortByDesc('att0');;
     
        return view('admin.calendar.teacher.teacherAttendance', compact('attendances'));
    }
    
    public function studentDayswiseAttendance()
    {
        $attendances  = StudentAttendance::all()->sortByDesc('att0');;
        $students  = User::all();   
     
        return view('admin.calendar.student.dayswiseAttendance', compact('attendances','students'));
    }

   

    public function studentsAttendance()
    {
        $students  = User::all();   
     
        return view('admin.calendar.student.studentsAttendance', compact('students'));
    }

    public function studentCalendar($rfid)
    {
        $holidays = Holiday::all();
        $studentAttendances = studentAttendance::all()->where('rfid',$rfid);
        $events = array();
        foreach($holidays as $holiday){
            $events[] = [
                'title' => $holiday->title,
                'start' => $holiday->start,
                'end' => $holiday->end,
                'className' => 'bg-warning'
            ];
        }
        foreach($studentAttendances as $studentAttendance){
            $name = $studentAttendance->name;
            $start = null;
            if(isset($studentAttendance->att0)){
                $olddate = strtotime($studentAttendance->att0);
                $out = strtotime($studentAttendance->att1);
                $start = date('Y-m-d',$olddate);
                $inTime = date('H:i',$olddate);
                $outTime = date('H:i',$out);
            }
         //   if(isset($studentAttendance->departure)){
          //      $olddate = strtotime($studentAttendance->departure);
         //       $start = date('Y-m-d',$olddate);
         //   }
         if(isset($studentAttendance->att0)){
            $events[] = [
                'title' => $inTime,
                'start' => $start,
                'className' => 'bg-success'
            ];
        }
            if(isset($studentAttendance->att1)){
            $events[] = [
                'title' => $outTime,
                'start' => $start,
                'className' => 'bg-info'
            ];
            }
        }
        if(!isset($studentAttendance)){
            $studentAttendance = null;
        }
            $events[] = [
                'daysOfWeek' => [0,7], //Sundays and saturdays
                'rendering' => 'background',
                'className' => 'bg-info',
                'overLap' => false,
                'allDay' => true
            ];
        return view('admin.calendar.student.studentCalendar', compact('events','studentAttendance','name'));
    }

    public function studentAttendance($rfid)
    {
        $attendances  = studentAttendance::all()->where('rfid',$rfid)->sortByDesc('att0');;
     
        return view('admin.calendar.student.studentAttendance', compact('attendances'));
    }

    public function classesList()
    {
        $classes  = User::all()->unique('grade')->sortBy('grade');   

        return view('admin.calendar.student.classesList', compact('classes'));
    }
    
    public function datesList($grade)
    {
        $dates  = StudentAttendance::all()->unique('att0')->where('class', $grade)->sortBy('grade');   

        return view('admin.calendar.student.datesList', compact('dates'));
    }

      
      public function studentsAttendenceDatewise($grade, $date)
      {
        $date1 = date('Y-m-d', strtotime($date));
        $attendances = DB::table('student_attendances')
        ->select('*')
        ->where(DB::raw('CAST(att0 as date)'), '=', $date1) 
        ->where('class',$grade)
        ->get();    
        foreach($attendances as $attendance){
            $presents[] = $attendance->name;
        }
    
        $absents  = DB::table('users')
                    ->where('grade',$grade)
                    ->whereNotIn('name',$presents)
                    ->get();
    
        return view('admin.calendar.student.studentsAttendenceDatewise', compact('attendances','absents'));
        }
}
