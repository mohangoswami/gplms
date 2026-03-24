<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\subCode;
use App\classwork;
use Auth;
use App\Teacher;
use App\flashNews;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('guest:admin');
        $this->middleware('guest:teacher');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $subCodes  = subCode::all()->where('class',Auth::user()->grade);
        $subjects  = subCode::all()->where('class',Auth::user()->grade);
        $class = Auth::user()->grade;
        $classWorks  = classwork::all()->where('class',Auth::user()->grade)->sortByDesc('created_at');
        $user = Auth::user();
        $flashNews = flashNews::all()->sortByDesc('created_at');
       //dd(Auth::user()->email);
         return view('student.dashboard',compact('classWorks','subjects','subCodes','user','class','flashNews'));

    }
}
