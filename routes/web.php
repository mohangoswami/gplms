<?php

use Illuminate\Support\Facades\Route;
use App\User;
use App\Http\Controllers\Fee\FeeController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\CashierLoginController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\Users\Student\StudentController;
use App\Http\Controllers\Fee\StudentFeeController;
use App\Http\Controllers\Users\Admin\AttendanceAdminController;
use App\Http\Controllers\Users\Teacher\TeacherController;
use App\Http\Controllers\Users\Teacher\TeacherResultController;
use App\Http\Controllers\Users\Teacher\ExamMarksController;
use App\Http\Controllers\Users\Teacher\teacherExamController;
use App\Http\Controllers\Users\Admin\TermController;
use App\Http\Controllers\Users\Admin\ExamController;
use App\Http\Controllers\Result\ResultController;
use App\Http\Controllers\Result\ResultPerformaController;
use App\Http\Controllers\Result\ResultPermissionController;
use App\Http\Controllers\Result\MarksEntryController;
use App\Http\Controllers\Result\ResultPerformaBuilderController;
use App\Http\Controllers\Result\ResultPerformaTermController;
use App\Http\Controllers\Result\ResultPerformaComponentController;
use App\Http\Controllers\Result\ResultSubjectComponentController;
use App\Http\Controllers\Result\Seeder;
use App\Http\Controllers\Users\Teacher\ResultEntryController;
use App\Http\Controllers\Result\ResultCoScholasticAreaController;
use App\Http\Controllers\Result\ResultPdfController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|

*/

Route::view('/GrowAiAutomation/privacy', 'GrowAI Automation.privacy');


Auth::routes();
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

/*
// Render perticular view file by foldername and filename and all passed in only one controller at a time
Route::get('{folder}/{file}', 'MetricaController@indexWithOneFolder');

// Render when Route Have 2 folder
Route::get('{folder1}/{folder2}/{file}', 'MetricaController@indexWithTwoFolder');

/*
// when render first time project redirect
Route::get('/home', function () {
    return redirect('/analytics/analytics-index');
});


// when render first time project redirect
Route::get('/', function () {
    return redirect('login');
});

*/

// routes/web.php
Route::get('/manifest.json', function () {
    return response()->file(public_path('manifest.json'));
});



Route::get('/crm/crm-index', function () {

    // User::all()->where('email',Auth::all()->email)->notify(new emailNotification);
   //  dd(User::where('email','bali4u2001@gmail.com') -> first());
   return view('login');
 });

 Route::get('/', function () {

    // User::all()->where('email',Auth::all()->email)->notify(new emailNotification);
   //  dd(User::where('email','bali4u2001@gmail.com') -> first());
   return view('login');
 });

 Route::get('/privacy-policy', function () {
    return view('privacy-policy');
 });
 Route::get('/account/delete-policy', function () {
    return view('account-delete-policy');
})->name('account.delete.policy');



Route::get('/home', 'HomeController@index')->name('home');

// Admin routes
    //Password Reset
    Route::post('admin-password/email','Users\Admin\ForgotPasswordController@sendResetLinkEmail')->name('admin.password.email');
    Route::get('admin-password/reset','Users\Admin\ForgotPasswordController@showLinkRequestForm')->name('admin.password.request');
    Route::post('admin-password/reset','Users\Admin\ResetPasswordController@reset')->name('admin.password.update');
    Route::get('admin-password/reset/{token}','Users\Admin\ResetPasswordController@showResetForm')->name('admin.password.reset');


Route::prefix('admin')->group(function(){


    //seeder route for result performa components
    // Route::post('/result-performa/components/dataSeederRun',
    //         [Seeder::class, 'dataSeederRun']
    //     )->name('result-performa.seed')->middleware('auth:admin');
    // Route::get('/result-performa/seeder', function () {
    //      return view('admin.result_performa.seeder');
    //  })->name('result.performa.seeder');



    Route::get('/fee/dashboard', 'Fee\FeeController@dashboard')->name('admin.fee.dashboard')->middleware('auth:admin');
    Route::get('/', 'Users\Admin\AdminController@index')->name('admin.dashboard')->middleware('auth:admin');
    Route::get('/fee-chart-data', 'Users\Admin\AdminController@getFeeChartData')->name('admin.feeChartData')->middleware('auth:admin');;

    Route::get('/login', 'Auth\AdminLoginController@showLoginForm')->name('admin.login');
    Route::post('/login', 'Auth\AdminLoginController@login')->name('admin.login.submit');
    Route::get('/register', 'Auth\AdminRegisterController@showRegisterForm')->name('admin.register');
    Route::post('/register', 'Auth\AdminRegisterController@register')->name('admin.register.submit');

    Route::get('/student-update-password/{id}', 'Users\Admin\AdminController@studentUpdatePassword')->name('admin.student-update-password')->middleware('auth:admin');
    Route::post('/post-student-update-password', 'Users\Admin\AdminController@postStudentUpdatePassword')->name('post-student-update-password')->middleware('auth:admin');
    Route::get('/admin-update-password', 'Users\Admin\AdminController@adminUpdatePassword')->name('admin.admin-update-password')->middleware('auth:admin');
    Route::post('/post-admin-update-password', 'Users\Admin\AdminController@postAdminUpdatePassword')->name('post-admin-update-password')->middleware('auth:admin');
    Route::get('/cashier-update-password/{id}', 'Users\Admin\AdminController@cashierUpdatePassword')->name('admin.cashier-update-password')->middleware('auth:admin');
    Route::post('/post-cashier-update-password', 'Users\Admin\AdminController@postCashierUpdatePassword')->name('post-cashier-update-password')->middleware('auth:admin');


    Route::get('/phpinfo', 'Users\Admin\AdminController@phpinfo')->name('admin.phpinfo')->middleware('auth:admin');


    Route::get('/allStudentsRecord', 'Users\Admin\AdminController@allStudentsRecord')->name('admin.allStudentsRecord')->middleware('auth:admin');
    Route::get('/editStudentRecord/{id}', 'Users\Admin\AdminController@editStudentRecord')->name('admin.editStudentRecord')->middleware('auth:admin');
    Route::post('/editStudentRecord', 'Users\Admin\AdminController@post_editStudentRecord')->name('editStudentRecord')->middleware('auth:admin');
    Route::post('/student/send-fee-reminder/{id}', 'Users\Admin\AdminController@sendFeeReminder')->name('admin.student.sendFeeReminder')->middleware('auth:admin');
    Route::post('/student/send-fee-reminder-all-due', 'Users\Admin\AdminController@sendFeeReminderAllDue')->name('admin.student.sendFeeReminderAllDue')->middleware('auth:admin');
    Route::get('/deleteStudentRecord/{id}', 'Users\Admin\AdminController@deleteStudentRecord')->name('admin.deleteStudentRecord')->middleware('auth:admin');

    Route::get('/allTeachersRecord', 'Users\Admin\AdminController@allTeachersRecord')->name('admin.allTeachersRecord')->middleware('auth:admin');;
    Route::get('/editTeacherRecord/{id}', 'Users\Admin\AdminController@editTeacherRecord')->name('admin.editTeacherRecord')->middleware('auth:admin');;
    Route::post('/editTeacherRecord', 'Users\Admin\AdminController@post_editTeacherRecord')->name('editTeacherRecord')->middleware('auth:admin');
    Route::get('/deleteTeacherRecord/{id}', 'Users\Admin\AdminController@deleteTeacherRecord')->name('admin.deleteTeacherRecord')->middleware('auth:admin');

    Route::get('/allCashierRecord', 'Users\Admin\AdminController@allCashierRecord')->name('admin.allCashierRecord')->middleware('auth:admin');;
    Route::get('/deleteCashierRecord/{id}', 'Users\Admin\AdminController@deleteCashierRecord')->name('admin.deleteCashierRecord')->middleware('auth:admin');

    Route::get('/create_subCode', 'Users\Admin\AdminController@get_create_subCode')->name('admin.create_subCode')->middleware('auth:admin');;
    Route::post('/create_subCodes', 'Users\Admin\AdminController@post_create_subCode')->name('create_subCodes')->middleware('auth:admin');;
    Route::get('/allSubCodes', 'Users\Admin\AdminController@allSubCodes')->name('admin.allSubCodes')->middleware('auth:admin');;
    Route::get('/deleteSubCode/{id}', 'Users\Admin\AdminController@deleteSubCode')->name('admin.deleteSubCode')->middleware('auth:admin');

    Route::get('/createTerms', 'Users\Admin\AdminController@get_createTerms')->name('admin.createTerms')->middleware('auth:admin');;
    Route::post('/createTerms', 'Users\Admin\AdminController@post_createTerms')->name('createTerms')->middleware('auth:admin');;
    Route::get('/allTerms', 'Users\Admin\AdminController@allTerms')->name('admin.allTerms')->middleware('auth:admin');;
    Route::get('/deleteTerm/{id}', 'Users\Admin\AdminController@deleteTerm')->name('admin.deleteTerm')->middleware('auth:admin');

    Route::get('/liveClasses/editLiveClass/{id}', 'Users\Admin\AdminController@editLiveClass')->name('admin.liveClasses.editLiveClass')->middleware('auth:admin');
    Route::post('/liveClasses/editLiveClass', 'Users\Admin\AdminController@post_editLiveClass')->name('editLiveClass')->middleware('auth:admin');

    Route::get('/liveClasses/create_liveClass', 'Users\Admin\AdminController@get_create_liveClass')->name('admin.liveClasses.create_liveClass')->middleware('auth:admin');
    Route::post('/liveClasses/create_liveClass', 'Users\Admin\AdminController@post_create_liveClass')->name('create_liveClass')->middleware('auth:admin');
    Route::get('/liveClasses/allLiveClasses', 'Users\Admin\AdminController@allLiveClasses')->name('admin.liveClasses.allLiveClasses')->middleware('auth:admin');

    Route::get('/allFlashNews', 'Users\Admin\AdminController@allFlashNews')->name('admin.allFlashNews')->middleware('auth:admin');
    Route::get('/createFlashNews', 'Users\Admin\AdminController@createFlashNews')->name('admin.createFlashNews')->middleware('auth:admin');
    Route::post('/postFlashNews', 'Users\Admin\AdminController@postFlashNews')->name('admin.postFlashNews')->middleware('auth:admin');
    Route::get('/deleteFlashNews/{id}', 'Users\Admin\AdminController@deleteFlashNews')->name('admin.deleteFlashNews')->middleware('auth:admin');

    Route::get('/allClasswork', 'Users\Admin\AdminController@allClasswork')->name('admin.allClasswork')->middleware('auth:admin');;
    Route::get('/edit_classwork/{id}', 'Users\Admin\AdminController@edit_classwork')->name('admin.edit_classwork')->middleware('auth:admin');

    Route::post('/editPdfClasswork', 'Users\Admin\AdminController@editPdfClasswork')->name('admin.editPdfClasswork')->middleware('auth:admin');
    Route::post('/editImageClasswork', 'Users\Admin\AdminController@editImageClasswork')->name('admin.editImageClasswork')->middleware('auth:admin');
    Route::post('/editDocsClasswork', 'Users\Admin\AdminController@editDocsClasswork')->name('admin.editDocsClasswork')->middleware('auth:admin');
    Route::post('/editYoutubeLink', 'Users\Admin\AdminController@editYoutubeLink')->name('admin.editYoutubeLink')->middleware('auth:admin');

    Route::get('/classworkAttendence/{id}', 'Users\Admin\AdminController@classworkAttendence')->name('admin.classworkAttendence')->middleware('auth:admin');
    Route::get('/studentReturnWork/{id}', 'Users\Admin\AdminController@studentReturnWork')->name('admin.studentReturnWork')->middleware('auth:admin');
    Route::get('/classroom/{id}/delete', 'Users\Admin\AdminController@deletePost')->name('admin.deletePost')->middleware('auth:admin');

    //Calendar
    Route::get('/addHolidays', 'Users\Admin\Calendar@addHolidays')->name('admin.addHolidays')->middleware('auth:admin');;
    Route::post('/postHolidays', 'Users\Admin\Calendar@postHolidays')->name('admin.postHolidays')->middleware('auth:admin');

    Route::get('/calendar', 'Users\Admin\Calendar@calendar')->name('admin.calendar');
    Route::get('/teacher/teachersAttendance', 'Users\Admin\Calendar@teachersAttendance')->name('admin.teacher.teachersAttendance')->middleware('auth:admin');
    Route::get('/teacher/dayswiseAttendance', 'Users\Admin\Calendar@dayswiseAttendance')->name('admin.teacher.dayswiseAttendance')->middleware('auth:admin');

    Route::get('/studentsAttendance', 'Users\Admin\Calendar@studentsAttendance')->name('admin.studentsAttendance')->middleware('auth:admin');

    Route::get('/teacher/teachersAttendenceDatewise/{id}', 'Users\Admin\Calendar@teachersAttendenceDatewise')->name('admin.teacher.teachersAttendenceDatewise')->middleware('auth:admin');

    Route::get('/teacher/teacherCalendar/{id}', 'Users\Admin\Calendar@teacherCalendar')->name('admin.teacher.teacherCalendar')->middleware('auth:admin');
    Route::get('/teacher/teacherAttendance/{id}', 'Users\Admin\Calendar@teacherAttendance')->name('admin.teacher.teacherAttendance')->middleware('auth:admin');

    //student attendance
    Route::get('/student/dayswiseAttendance', 'Users\Admin\Calendar@studentDayswiseAttendance')->name('admin.student.dayswiseAttendance')->middleware('auth:admin');
    Route::get('/student/studentsAttendenceDatewise/{id}/{date}', 'Users\Admin\Calendar@studentsAttendenceDatewise')->name('admin.student.studentsAttendenceDatewise')->middleware('auth:admin');
    Route::get('/student/studentsAttendance', 'Users\Admin\Calendar@studentsAttendance')->name('admin.student.studentsAttendance')->middleware('auth:admin');
    Route::get('/student/studentCalendar/{id}', 'Users\Admin\Calendar@studentCalendar')->name('admin.student.studentCalendar')->middleware('auth:admin');
    Route::get('/student/studentAttendance/{id}', 'Users\Admin\Calendar@studentAttendance')->name('admin.student.studentAttendance')->middleware('auth:admin');
    Route::get('/student/classesList', 'Users\Admin\Calendar@classesList')->name('admin.student.classesList')->middleware('auth:admin');
    Route::get('/student/datesList/{id}', 'Users\Admin\Calendar@datesList')->name('admin.student.datesList')->middleware('auth:admin');
    Route::get('/student/studentsAttendenceClasswise/{id}', 'Users\Admin\Calendar@studentsAttendenceClasswise')->name('admin.student.studentsAttendenceClasswise')->middleware('auth:admin');


    Route::get('/terms', [App\Http\Controllers\Users\Admin\TermController::class, 'index'])->name('admin.term.index')->middleware('auth:admin');
    Route::post('/terms', [App\Http\Controllers\Users\Admin\TermController::class, 'store'])->name('admin.term.store')->middleware('auth:admin');
    Route::delete('/terms/{id}', [App\Http\Controllers\Users\Admin\TermController::class, 'destroy'])->name('admin.term.delete')->middleware('auth:admin');

    //Exam Management
    Route::get('exams', [App\Http\Controllers\Users\Admin\ExamController::class, 'index'])->name('admin.exams.index')->middleware('auth:admin');
    Route::get('exams/create', [App\Http\Controllers\Users\Admin\ExamController::class, 'create'])->name('admin.exams.create')->middleware('auth:admin');
    Route::post('exams', [App\Http\Controllers\Users\Admin\ExamController::class, 'store'])->name('admin.exams.store')->middleware('auth:admin');
    Route::get('exams/{exam}/edit', [App\Http\Controllers\Users\Admin\ExamController::class, 'edit'])->name('admin.exams.edit')->middleware('auth:admin');
    Route::put('exams/{exam}', [App\Http\Controllers\Users\Admin\ExamController::class, 'update'])->name('admin.exams.update')->middleware('auth:admin');
    Route::delete('exams/{exam}', [App\Http\Controllers\Users\Admin\ExamController::class, 'destroy'])->name('admin.exams.destroy')->middleware('auth:admin');

    // optionally view exam details
    Route::get('exams/{exam}', [App\Http\Controllers\Users\Admin\ExamController::class, 'show'])->name('admin.exams.show')->middleware('auth:admin');

    Route::get('/examMarks', [App\Http\Controllers\Users\Admin\AdminExamMarksController::class, 'index'])->name('admin.examMarks.index')->middleware('auth:admin');
    Route::get('/examMarks/{examId}/enter', [App\Http\Controllers\Users\Admin\AdminExamMarksController::class, 'enterMarks'])->name('admin.examMarks.enter')->middleware('auth:admin');
    Route::post('/examMarks/{examId}/save', [App\Http\Controllers\Users\Admin\AdminExamMarksController::class, 'saveMarks'])->name('admin.examMarks.save')->middleware('auth:admin');

    // ATTENDANCE MANAGEMENT
    Route::get('attendance', [App\Http\Controllers\Users\Admin\AttendanceAdminController::class, 'index'])->name('admin.attendance.index')->middleware('auth:admin');
    Route::get('attendance/view', [AttendanceAdminController::class, 'viewForm'])->name('admin.attendance.view')->middleware('auth:admin');
    Route::post('attendance/view', [AttendanceAdminController::class, 'viewRedirect'])->name('admin.attendance.view.post')->middleware('auth:admin');

    Route::post('attendance/show-students', [AttendanceAdminController::class, 'showStudents'])->name('admin.attendance.showStudents')->middleware('auth:admin');
    Route::post('attendance/save', [AttendanceAdminController::class, 'save'])->name('admin.attendance.save')->middleware('auth:admin');

    Route::get('attendance/month/{class}/{year}/{month}', [AttendanceAdminController::class, 'monthView'])->name('admin.attendance.month')->middleware('auth:admin');
    Route::get('attendance/day/select', [AttendanceAdminController::class, 'dayForm'])->name('admin.attendance.day.select')->middleware('auth:admin');
    Route::post('attendance/day/select', [AttendanceAdminController::class, 'dayRedirect'])->name('admin.attendance.day.select.post')->middleware('auth:admin');
    Route::get('attendance/day/{class}/{date}', [AttendanceAdminController::class, 'dayView'])->name('admin.attendance.day')->middleware('auth:admin');
    Route::get('attendance/day/{class}/{date}/absent/pdf', [AttendanceAdminController::class, 'absentPdf'])->name('admin.attendance.day.pdf')->middleware('auth:admin');

    // show the day selection form (GET)
    Route::get('attendance/day/form', [AttendanceAdminController::class, 'dayForm'])
        ->name('admin.attendance.day.form')->middleware('auth:admin');

    // handle the form submit and redirect to day view (POST)
    Route::post('attendance/day/form', [AttendanceAdminController::class, 'dayRedirect'])
        ->name('admin.attendance.day.form.post')->middleware('auth:admin');


    Route::get('attendance/continuous', [AttendanceAdminController::class, 'continuousAbsentForm'])
        ->name('admin.attendance.continuous.form')->middleware('auth:admin');

    Route::get('attendance/continuous/results', [AttendanceAdminController::class, 'continuousAbsentResults'])
        ->name('admin.attendance.continuous.results')->middleware('auth:admin');

    Route::get('attendance/classes/status', [AttendanceAdminController::class, 'classesStatus'])
    ->name('admin.attendance.classes.status')
    ->middleware('auth:admin');

    Route::get('attendance/class/csv', [AttendanceAdminController::class, 'classCsv'])
    ->name('admin.attendance.class.csv')
    ->middleware('auth:admin');

        // Class wise result preview
    Route::get('/results/class/{class}',
        [ResultController::class, 'classResult'])->middleware('auth:admin');

    // Single student PDF
    Route::get('/results/student/{studentId}/pdf',
        [ResultController::class, 'studentPdf'])->middleware('auth:admin');

    // Optional: Full class PDF
    Route::get('/results/class/{class}/pdf',
        [ResultController::class, 'classPdf'])->middleware('auth:admin');



    // // Result Performa Editor
    // Route::get('/result-performa/{class}',
    //   [ResultPerformaController::class, 'edit'])->middleware('auth:admin');

    // Route::post('/result-performa/{class}',
    //          [ResultPerformaController::class, 'update'])->middleware('auth:admin');


    // Permission management screen
        Route::get(
            '/result-permissions',
            [ResultPermissionController::class, 'index']
        )->name('admin.result.permissions')->middleware('auth:admin');

        // Save permissions (bulk)
        Route::post(
            '/result-permissions/save',
            [ResultPermissionController::class, 'save']
        )->name('admin.result.permissions.save')->middleware('auth:admin');

        Route::get(
            '/admin/result/permissions/fetch',
            [ResultPermissionController::class, 'fetch']
        )->name('admin.result.permissions.fetch')->middleware('auth:admin');


        Route::get(
            '/result-permissions/summary',
            [ResultPermissionController::class, 'summary']
        )->name('admin.result.permissions.summary')->middleware('auth:admin');





            // Admin Marks Entry Routes
        Route::get('/marks-entry', [MarksEntryController::class, 'index'])->middleware('auth:admin');
        Route::post('/marks-entry/save', [MarksEntryController::class, 'save'])->middleware('auth:admin');


        // Result Performa Builder 26 Dec 2025

        // Route::get('/result-performa-builder/{class}',
        //     [ResultPerformaBuilderController::class, 'edit']
        // )->middleware('auth:admin');

        // Route::post('/result-performa-builder/{class}',
        //     [ResultPerformaBuilderController::class, 'save']
        // )->middleware('auth:admin');


        // class selector + terms UI
       Route::get('/result-performa/terms',
            [ResultPerformaTermController::class, 'index']
        )->middleware('auth:admin');



        // save terms (same as before)
        Route::post('/result-performa/terms',
            [ResultPerformaTermController::class, 'save']
        )->middleware('auth:admin');



        Route::get('/result-performa/components',
            [ResultPerformaComponentController::class, 'index']
        )->middleware('auth:admin');

        Route::post('/result-performa/components',
            [ResultPerformaComponentController::class, 'save']
        )->middleware('auth:admin');


        Route::get('/result-performa/mapping',
            [ResultSubjectComponentController::class, 'index']
        )->middleware('auth:admin');

        Route::post('/result-performa/mapping',
            [ResultSubjectComponentController::class, 'save']
        )->middleware('auth:admin');


            // Class-wise student list for result entry
    Route::get(
        '/results/student-list',
        [MarksEntryController::class, 'index']
    )->name('admin.results.studentList')->middleware('auth:admin');


    // Single student result entry screen
    Route::get(
        '/results/{student}/entry',
        [MarksEntryController::class, 'create']
    )->name('admin.results.entry')->middleware('auth:admin');

    // Save single student result entry
    Route::post('/results/{student}/entry',
        [MarksEntryController::class, 'save']
    )->name('admin.results.entry.save')->middleware('auth:admin');

    // Co Scholastic Area Management

    Route::get(
        '/results/co-scholastic',
        [ResultCoScholasticAreaController::class, 'index']
    )->name('admin.result_performa.co_scholastic.index');

    Route::post(
        '/results/co-scholastic/store',
        [ResultCoScholasticAreaController::class, 'store']
    )->name('admin.result_performa.co_scholastic.store');

       // ✅ EDIT
    Route::get(
        '/co-scholastic/{id}/edit',
        [ResultCoScholasticAreaController::class, 'edit']
    )->name('admin.result_performa.co_scholastic.edit');

    // ✅ UPDATE
    Route::put(
        '/co-scholastic/{id}',
        [ResultCoScholasticAreaController::class, 'update']
    )->name('admin.result_performa.co_scholastic.update');
    // ✅ DELETE
    Route::delete(
        '/results/co-scholastic/{id}',
        [ResultCoScholasticAreaController::class, 'destroy']
    )->name('admin.result_performa.co_scholastic.delete');


    // Finalize Result and Reopen Routes
    Route::post('/results/{student}/finalize',
      [MarksEntryController::class, 'finalize']
    )->name('admin.results.finalize');

    Route::post('/results/{student}/reopen',
        [MarksEntryController::class, 'reopen']
    )->middleware('auth:admin')
    ->name('admin.results.reopen');

    Route::post('/results/class/{class}/finalize-all',
        [MarksEntryController::class, 'finalizeAll']
    )->middleware('auth:admin')
    ->name('admin.results.finalizeAll');

    Route::post('/results/class/{class}/reopen-all',
        [MarksEntryController::class, 'reopenAll']
    )->middleware('auth:admin')
    ->name('admin.results.reopenAll');



    // ===============================
    // RESULT PDF ROUTES
    // ===============================

    Route::middleware(['auth:admin'])->group(function () {
        Route::get(
            '/results/{student}/pdf',
            [ResultPdfController::class, 'adminPdf']
        )->name('admin.results.pdf');
    });




        // 📄 Annual Report Card PDF (single student)
    Route::get(
        '/results/{student}/annual-pdf',
        [ResultPdfController::class, 'annualPdf']
    )->name('admin.results.annual.pdf');

    // ☁️ Upload Result Card PDF to S3
    Route::post(
        '/results/{student}/upload-pdf',
        [ResultPdfController::class, 'uploadResultPDF']
    )->name('admin.results.upload.pdf');

    // 🔗 View uploaded Result Card PDF (generates signed S3 URL on demand)
    Route::get(
        '/results/{student}/view-pdf',
        [ResultPdfController::class, 'viewResultPDF']
    )->name('admin.results.view.pdf');

    // 📦 Bulk class PDF — all finalized students in one download
    Route::get(
        '/results/class/{grade}/bulk-pdf',
        [ResultPdfController::class, 'classBulkPdf']
    )->name('admin.results.class.bulk.pdf')->middleware('auth:admin');


        // ❌ Delete FULL result of a student for a term
    // Route::delete(
    //     '/results/{student}/term/{term}',
    //     [ResultController::class, 'deleteTermResult']
    // )->name('admin.results.delete.term');

        // ❌ DELETE FULL ANNUAL RESULT (ALL TERMS)
    Route::delete(
        '/results/{student}',
        [ResultController::class, 'deleteFullResult']
    )->name('admin.results.delete.full');


     // DEMO DATA FILLER ROUTE
    Route::get('/demodata/demoDataSeedingRun',
        [Seeder::class, 'demoDataSeedingRun'])->name('admin.demoDataSeedingRun')->middleware('auth:admin');

   // DEMO DATA FILLER ROUTE
    Route::get('/demodata/DefaultResultPerformaSeeder',
        [Seeder::class, 'DefaultResultPerformaSeeder'])->name('admin.DefaultResultPerformaSeeder')->middleware('auth:admin');


   // DEMO Co Scholastic AREA DATA FILLER ROUTE
    Route::get('/demodata/ResultCoScholasticSeed',
        [Seeder::class, 'ResultCoScholasticSeed'])->name('admin.ResultCoScholasticSeed')->middleware('auth:admin');


});


// Teacher routes
Route::prefix('teacher')->group(function(){
    Route::get('/', 'Users\Teacher\TeacherController@index')->name('teacher.dashboard')->middleware('auth:teacher');
    Route::get('/login', 'Auth\TeacherLoginController@showLoginForm')->name('teacher.login');
    Route::post('/login', 'Auth\TeacherLoginController@login')->name('teacher.login.submit');
    Route::get('/register', 'Auth\TeacherRegisterController@showRegisterForm')->name('teacher.register');
    Route::post('/register', 'Auth\TeacherRegisterController@register')->name('teacher.register.submit');

    Route::get('/teacher-update-password', 'Users\Teacher\TeacherController@teacherSelfUpdatePassword')->name('teacher.teacherSelf-update-password')->middleware('auth:teacher');
    Route::post('/post-teacher-update-password', 'Users\Teacher\TeacherController@postTeacherSelfUpdatePassword')->name('post-teacherSelf-update-password')->middleware('auth:teacher');

    Route::get('/edit_classwork/{id}', 'Users\Teacher\TeacherController@edit_classwork')->name('teacher.edit_classwork')->middleware('auth:teacher');

    Route::get('/createTitle/{id}', 'Users\Teacher\TeacherController@createTitle')->name('teacher.createTitle')->middleware('auth:teacher');
    Route::post('/createTitlePost', 'Users\Teacher\TeacherController@createTitlePost')->name('teacher.createTitlePost')->middleware('auth:teacher');


    Route::post('/pdfClasswork', 'Users\Teacher\TeacherController@pdfClasswork')->name('teacher.pdfClasswork')->middleware('auth:teacher');
    Route::post('/imageClasswork', 'Users\Teacher\TeacherController@imageClasswork')->name('teacher.imageClasswork')->middleware('auth:teacher');
    Route::post('/docsClasswork', 'Users\Teacher\TeacherController@docsClasswork')->name('teacher.docsClasswork')->middleware('auth:teacher');
    Route::post('/youtubeLink', 'Users\Teacher\TeacherController@youtubeLink')->name('teacher.youtubeLink')->middleware('auth:teacher');

    Route::post('/editPdfClasswork', 'Users\Teacher\TeacherController@editPdfClasswork')->name('teacher.editPdfClasswork')->middleware('auth:teacher');
    Route::post('/editImageClasswork', 'Users\Teacher\TeacherController@editImageClasswork')->name('teacher.editImageClasswork')->middleware('auth:teacher');
    Route::post('/editDocsClasswork', 'Users\Teacher\TeacherController@editDocsClasswork')->name('teacher.editDocsClasswork')->middleware('auth:teacher');
    Route::post('/editYoutubeLink', 'Users\Teacher\TeacherController@editYoutubeLink')->name('teacher.editYoutubeLink')->middleware('auth:teacher');

    Route::get('/liveClass', 'Users\Teacher\TeacherController@liveClass')->name('teacher.liveClass')->middleware('auth:teacher');
    Route::get('/liveClassAttendence/{id?}', 'Users\Teacher\TeacherController@liveClassAttendence')->name('teacher.liveClassAttendence')->middleware('auth:teacher');

    Route::get('/createExam', 'Users\Teacher\teacherExamController@createExam')->name('teacher.createExam')->middleware('auth:teacher');
    Route::get('/allExams', 'Users\Teacher\teacherExamController@allExams')->name('teacher.allExams')->middleware('auth:teacher');
    Route::get('/editExam/{id}', 'Users\Teacher\teacherExamController@editExam')->name('teacher.editExam')->middleware('auth:teacher');
    Route::post('/postEditExam', 'Users\Teacher\teacherExamController@postEditExam')->name('teacher.postEditExam')->middleware('auth:teacher');
    Route::get('/deleteExam/{id}', 'Users\Teacher\teacherExamController@deleteExam')->name('teacher.deleteExam')->middleware('auth:teacher');
    Route::get('/formExam/{id}', 'Users\Teacher\teacherExamController@formExam')->name('teacher.formExam')->middleware('auth:teacher');

    Route::post('/pdfExam', 'Users\Teacher\teacherExamController@pdfExam')->name('teacher.pdfExam')->middleware('auth:teacher');
    Route::post('/imageExam', 'Users\Teacher\teacherExamController@imageExam')->name('teacher.imageExam')->middleware('auth:teacher');
    Route::post('/docsExam', 'Users\Teacher\teacherExamController@docsExam')->name('teacher.docsExam')->middleware('auth:teacher');
    Route::post('/formLink', 'Users\Teacher\teacherExamController@formLink')->name('teacher.formLink')->middleware('auth:teacher');


    Route::get('/classroom/{id}', 'Users\Teacher\TeacherController@classroom_id')->name('teacher.classroom')->middleware('auth:teacher');

    Route::get('/inner_classroom/{id}', 'Users\Teacher\TeacherController@inner_classroom_id')->name('teacher.inner_classroom')->middleware('auth:teacher');

    Route::get('/addMaterial/{id}', 'Users\Teacher\TeacherController@addMaterial')->name('teacher.addMaterial')->middleware('auth:teacher');

    Route::get('/classroom/{id}/delete', 'Users\Teacher\TeacherController@deletePost')->name('teacher.deletePost')->middleware('auth:teacher');

    Route::get('/classworkAttendence/{id}', 'Users\Teacher\TeacherController@classworkAttendence')->name('teacher.classworkAttendence')->middleware('auth:teacher');
    Route::get('/studentReturnWork/{id}', 'Users\Teacher\TeacherController@studentReturnWork')->name('teacher.studentReturnWork')->middleware('auth:teacher');

    Route::get('/resultList', 'Users\Teacher\TeacherResultController@resultList')->name('teacher.resultList')->middleware('auth:teacher');
    Route::get('/result/{id}', 'Users\Teacher\TeacherResultController@result')->name('teacher.result')->middleware('auth:teacher');
    Route::post('/postResult', 'Users\Teacher\TeacherResultController@postResult')->name('teacher.postResult')->middleware('auth:teacher');

    Route::get('/editStudentResult/{id}', 'Users\Teacher\TeacherResultController@editStudentResult')->name('teacher.editStudentResult')->middleware('auth:teacher');
    Route::post('/topperSwitch', 'Users\Teacher\TeacherResultController@topperSwitch')->name('teacher.topperSwitch')->middleware('auth:teacher');


    Route::get('/download/{id}', 'Users\Teacher\TeacherController@download')->name('teacher.download')->middleware('auth:teacher');

    Route::get('/attendance', 'Users\Teacher\CalendarController@Calendar')->middleware('auth:teacher')->name('teacher.attendance');

    //duelist
    Route::get('/teacherDueList', 'Users\Teacher\TeacherController@teacherDueList')
    ->middleware('auth:teacher')
    ->name('teacherDueList');

    Route::post('/teacherDueList', 'Users\Teacher\TeacherController@post_teacherDueList')
    ->middleware('auth:teacher');




   // Show list of exams (optionally filtered by term). If term is provided, URL: /examMarks/{termId}
    Route::get('/examMarks/{term?}', [App\Http\Controllers\Users\Teacher\ExamMarksController::class, 'index'])
        ->name('teacher.examMarks.index')
        ->middleware('auth:teacher');

    // Show marking screen for a given exam id
    Route::get('/exams/{exam}/mark', [App\Http\Controllers\Users\Teacher\ExamMarksController::class, 'markingForm'])
        ->name('teacher.exams.mark')
        ->middleware('auth:teacher');

    // Save marks (bulk) for an exam
    Route::post('/exams/{exam}/mark/save', [App\Http\Controllers\Users\Teacher\ExamMarksController::class, 'saveMarks'])
        ->name('teacher.exams.mark.save')
        ->middleware('auth:teacher');

    // If you still use this enter route (older), keep it. It should show same enter view
    Route::get('/examMarks/{examId}/enter', [App\Http\Controllers\Users\Teacher\ExamMarksController::class, 'enterMarks'])
        ->name('teacher.examMarks.enter')
        ->middleware('auth:teacher');



    // Attendance module (manage attendance / absent PDF)
    Route::get('/attendance/manage', [App\Http\Controllers\Users\Teacher\TeacherAttendanceController::class, 'index'])
        ->name('teacher.attendance.index')
        ->middleware('auth:teacher');

    // View attendance selection (select class + month -> show monthly matrix)
    Route::get('/attendance/view', [App\Http\Controllers\Users\Teacher\TeacherAttendanceController::class, 'viewForm'])
        ->name('teacher.attendance.view')
        ->middleware('auth:teacher');

    Route::post('/attendance/view', [App\Http\Controllers\Users\Teacher\TeacherAttendanceController::class, 'viewRedirect'])
        ->name('teacher.attendance.view.post')
        ->middleware('auth:teacher');

    // Day attendance selection and view
    Route::get('/attendance/day', [App\Http\Controllers\Users\Teacher\TeacherAttendanceController::class, 'dayForm'])
        ->name('teacher.attendance.day.form')
        ->middleware('auth:teacher');

    Route::post('/attendance/day', [App\Http\Controllers\Users\Teacher\TeacherAttendanceController::class, 'dayRedirect'])
        ->name('teacher.attendance.day.post')
        ->middleware('auth:teacher');

    // Allow GET and POST so users can open the URL directly or submit the form
    Route::match(['get', 'post'], '/attendance/manage/show', [App\Http\Controllers\Users\Teacher\TeacherAttendanceController::class, 'showStudents'])
        ->name('teacher.attendance.show')
        ->middleware('auth:teacher');

    Route::post('/attendance/manage/save', [App\Http\Controllers\Users\Teacher\TeacherAttendanceController::class, 'save'])
        ->name('teacher.attendance.save')
        ->middleware('auth:teacher');

    Route::get('/attendance/manage/{class}/{date}/absent-pdf', [App\Http\Controllers\Users\Teacher\TeacherAttendanceController::class, 'absentPdf'])
        ->name('teacher.attendance.absentPdf')
        ->middleware('auth:teacher');

    // Monthly attendance view (matrix)
    Route::get('/attendance/manage/{class}/{year}/{month}/month', [App\Http\Controllers\Users\Teacher\TeacherAttendanceController::class, 'monthView'])
        ->name('teacher.attendance.month')
        ->middleware('auth:teacher');

    // Single day attendance view
    Route::get('/attendance/manage/{class}/{date}/day', [App\Http\Controllers\Users\Teacher\TeacherAttendanceController::class, 'dayView'])
        ->name('teacher.attendance.day')
        ->middleware('auth:teacher');


    // Result Entry module
       Route::get('/results',
        [ResultEntryController::class, 'dashboard']
    )->name('teacher.results.dashboard');

    Route::get('/list',
        [ResultEntryController::class, 'studentList']
    )->name('teacher.results.list');

    Route::get('/entry/{student}',
        [ResultEntryController::class, 'entry']
    )->name('teacher.results.entry');

    Route::post('/entry/{student}',
        [ResultEntryController::class, 'save']
    )->name('teacher.results.save');

        // Finalize Result and Reopen Routes
    Route::post('/results/{student}/finalize',
      [ResultEntryController::class, 'finalize']
    )->name('teacher.results.finalize');


    Route::middleware(['auth:teacher'])->group(function () {
    Route::get(
    '/results/{student}/annual-pdf',
    [ResultPdfController::class, 'annualPdf']
    )->name('teacher.results.pdf');
});
});



Route::get('/hospital/events', 'Users\Teacher\TeacherController@events')->name('hospital.events')->middleware('auth:teacher');


Route::prefix('student')->group(function(){
    Route::get('/', 'Users\Student\StudentController@index')->name('student.dashboard')->middleware('auth');

    Route::get('/student-update-password', [StudentController::class, 'studentSelfUpdatePassword'])->name('student.studentSelf-update-password')->middleware('auth');
    Route::post('/post-student-update-password', [StudentController::class, 'poststudentSelfUpdatePassword'])->name('post-studentSelf-update-password')->middleware('auth');

    Route::get('/classroom/{id}', 'Users\Student\StudentController@classroom_id')->name('student.classroom')->middleware('auth');

    Route::get('/inner_classroom/{id}', 'Users\Student\StudentController@inner_classroom_id')->name('student.inner_classroom')->middleware('auth');
    Route::get('/homework/{id}', 'Users\Student\StudentController@homework')->name('student.homework')->middleware('auth');
    Route::post('/stuUploadFile', 'Users\Student\StudentController@stuUploadFile')->name('student.stuUploadFile')->middleware('auth');
    Route::get('homework/{id}/{titleId}/delete', 'Users\Student\StudentController@deleteStuUploadFile')->name('student.homework.deletePost')->middleware('auth');

    Route::get('/liveClass', 'Users\Student\StudentController@liveClass')->name('student.liveClass')->middleware('auth');
    Route::get('/liveAttendence/{id}', 'Users\Student\StudentController@liveAttendence')->name('student.liveAttendence')->middleware('auth');

    Route::get('/exams/allExams', 'Users\Student\ExamController@allExams')->name('student.exams.allExams')->middleware('auth');
    Route::get('/exams/upcomingExams', 'Users\Student\ExamController@upcomingExams')->name('student.exams.upcomingExams')->middleware('auth');
    Route::get('/exams/todayExams', 'Users\Student\ExamController@todayExams')->name('student.exams.todayExams')->middleware('auth');

    Route::get('/exams/attemptExam/{id}', 'Users\Student\ExamController@attemptExam')->name('student.exams.attemptExam')->middleware('auth');
    Route::post('/exams/fileExam', 'Users\Student\ExamController@fileExam')->name('student.exams.fileExam')->middleware('auth');
    Route::post('/exams/formExam', 'Users\Student\ExamController@formExam')->name('student.exams.formExam')->middleware('auth');
    Route::get('/exams/attemptExam/{id}/{examId}/delete', 'Users\Student\ExamController@deleteStuExamWroks')->name('student.exams.deletePost')->middleware('auth');
    Route::get('/exams/attemptExam/{id}/submittedDone', 'Users\Student\ExamController@submittedDone')->name('student.exams.submittedDone')->middleware('auth');


    Route::get('/notificationClasswork/{id}/{notificationId}', 'Users\Student\StudentController@notificationClasswork')->name('student.notificationClasswork')->middleware('auth');
    Route::get('/notificationExam/{id}/{notificationId}', 'Users\Student\StudentController@notificationExam')->name('student.notificationExam')->middleware('auth');

    Route::get('/results', 'Users\Student\StudentController@results')->name('student.results')->middleware('auth');

    Route::get('/calendar', 'Users\Student\CalendarController@Calendar');

    Route::get('/getStudentFeeDetail', 'Fee\StudentFeeController@getStudentFeeDetail')->name('getStudentFeeDetail')->middleware('auth');
    // For submitting fee details
    Route::post('/postStudentFeeDetail', 'Fee\StudentFeeController@postStudentFeeDetail')->name('postStudentFeeDetail')->middleware('auth');

    // Route::post('/post_FeeReceipt', 'Fee\StudentFeeController@post_FeeReceipt')->name('post_FeeReceipt')->middleware('auth');


    Route::post('/post_FeeReceipt', [StudentFeeController::class, 'storeFeeReceiptForm'])
    ->name('post_FeeReceipt')
    ->middleware('auth');


    Route::post('/razorpay/payment/confirm', [StudentFeeController::class, 'confirmRazorpayPayment'])
        ->name('razorpay.payment.confirm')
        ->middleware('auth');  // Ensure the user is authenticated



    // Route::get('/feeInvoice/{id}/{receiptId}', 'Fee\StudentFeeController@getFeeInvoice')
    // ->name('student.feeInvoice')
    // ->middleware('auth');


     //Fee Card
     Route::get('/studentFeeCard', 'Fee\StudentFeeController@studentFeeCard')->name('student.studentFeeCard')->middleware('auth');
     Route::get('/printReceipt/{id}', 'Fee\StudentFeeController@printReceipt')->name('student.printReceipt')->middleware('auth');;

    //  Route::get('/studentFeeCard/{id}', [StudentFeeController::class, 'studentFeeCard'])
    //  ->name('studentFeeCard')
    //  ->middleware('auth');

});

Route::prefix('cashier')->group(function () {
    Route::get('/login', [CashierLoginController::class, 'showLoginForm'])->name('cashier.login');
    Route::post('/login', [CashierLoginController::class, 'login'])->name('cashier.login.submit');
    Route::group(['middleware' => ['auth:cashier']], function () {
        Route::get('/dashboard', [CashierController::class, 'dashboard'])->name('cashier.dashboard')->middleware('multi.auth:admin,cashier');

        Route::post('/logout', [CashierLoginController::class, 'logout'])->name('cashier.logout');
    });

    Route::get('/cashier-update-password', [CashierController::class, 'cashierUpdatePassword'])->name('cashier.cashierSelf-update-password')->middleware('auth:cashier');
    Route::post('/post-cashier-update-password', [CashierController::class, 'postcashierUpdatePassword'])->name('post-cashierSelf-update-password')->middleware('auth:cashier');

    Route::get('/allStudentsRecord', [CashierController::class, 'allStudentsRecord'])->name('cashier.allStudentsRecord')->middleware('multi.auth:admin,cashier');
    Route::get('/cashierEditStudentRecord/{id}', [CashierController::class, 'cashierEditStudentRecord'])->name('admin.cashierEditStudentRecord')->middleware('multi.auth:admin,cashier');
    Route::post('/cashierEditStudentRecord', [CashierController::class, 'post_cashierEditStudentRecord'])->name('cashierEditStudentRecord')->middleware('multi.auth:admin,cashier');

    Route::get('/cashierStudent-update-password/{id}', [CashierController::class, 'cashierStudentUpdatePassword'])->name('cashier.cashierStudent-update-password')->middleware('multi.auth:admin,cashier');
    Route::post('/post-cashierStudent-update-password', [CashierController::class, 'postcashierStudentUpdatePassword'])->name('post-cashierStudent-update-password')->middleware('multi.auth:admin,cashier');

    Route::get('/allTeachersRecord', [CashierController::class, 'allTeachersRecord'])->name('cashier.allTeachersRecord')->middleware('multi.auth:admin,cashier');
    Route::get('/cashierEditTeacherRecord/{id}', [CashierController::class, 'cashierEditTeacherRecord'])->name('admin.cashierEditTeacherRecord')->middleware('multi.auth:admin,cashier');
    Route::post('/post_cashierEditTeacherRecord', [CashierController::class, 'post_cashierEditTeacherRecord'])->name('post_cashierEditTeacherRecord')->middleware('multi.auth:admin,cashier');

    // Student result view (browser view)
    Route::get('/student/{student}/exam/{exam}/result', [App\Http\Controllers\Users\Student\ExamController::class, 'showResult'])
        ->name('student.result.show')
        ->middleware('auth');  // optional if only logged-in teachers/students should see


     // ATTENDANCE MANAGEMENT
    Route::get('attendance', [App\Http\Controllers\Users\Admin\AttendanceAdminController::class, 'index'])->name('cashier.attendance.index')->middleware('multi.auth:admin,cashier');
    Route::get('attendance/view', [AttendanceAdminController::class, 'viewForm'])->name('cashier.attendance.view')->middleware('multi.auth:admin,cashier');
    Route::post('attendance/view', [AttendanceAdminController::class, 'viewRedirect'])->name('cashier.attendance.view.post')->middleware('multi.auth:admin,cashier');

    Route::post('attendance/show-students', [AttendanceAdminController::class, 'showStudents'])->name('cashier.attendance.showStudents')->middleware('multi.auth:admin,cashier');
    Route::post('attendance/save', [AttendanceAdminController::class, 'save'])->name('cashier.attendance.save')->middleware('multi.auth:admin,cashier');

    Route::get('attendance/month/{class}/{year}/{month}', [AttendanceAdminController::class, 'monthView'])->name('cashier.attendance.month')->middleware('multi.auth:admin,cashier');
    Route::get('attendance/day/select', [AttendanceAdminController::class, 'dayForm'])->name('cashier.attendance.day.select')->middleware('multi.auth:admin,cashier');
    Route::post('attendance/day/select', [AttendanceAdminController::class, 'dayRedirect'])->name('cashier.attendance.day.select.post')->middleware('multi.auth:admin,cashier');
    Route::get('attendance/day/{class}/{date}', [AttendanceAdminController::class, 'dayView'])->name('cashier.attendance.day')->middleware('multi.auth:admin,cashier');
    Route::get('attendance/day/{class}/{date}/absent/pdf', [AttendanceAdminController::class, 'absentPdf'])->name('cashier.attendance.day.pdf')->middleware('multi.auth:admin,cashier');

    // show the day selection form (GET)
    Route::get('attendance/day/form', [AttendanceAdminController::class, 'dayForm'])
        ->name('cashier.attendance.day.form');

    // handle the form submit and redirect to day view (POST)
    Route::post('attendance/day/form', [AttendanceAdminController::class, 'dayRedirect'])
        ->name('cashier.attendance.day.form.post');


    Route::get('attendance/continuous', [AttendanceAdminController::class, 'continuousAbsentForm'])
        ->name('cashier.attendance.continuous.form');

    Route::get('attendance/continuous/results', [AttendanceAdminController::class, 'continuousAbsentResults'])
        ->name('cashier.attendance.continuous.results');

    Route::get('attendance/classes/status', [AttendanceAdminController::class, 'classesStatus'])
    ->name('cashier.attendance.classes.status')
    ->middleware('multi.auth:admin,cashier');

    Route::get('attendance/class/csv', [AttendanceAdminController::class, 'classCsv'])
    ->name('cashier.attendance.class.csv')
    ->middleware('multi.auth:admin,cashier');
});


// Fee routes
Route::prefix('fee')->group(function(){


        Route::get('/', 'Fee\FeeController@dashboard') ->name('fee.dashboard')->middleware('multi.auth:admin,cashier');
        Route::get('/fee-chart-data', 'Fee\FeeController@getFeeChartData')->name('fee.feeChartData')->middleware('multi.auth:admin,cashier');;
        //Fee Head
        Route::get('/createFeeHead', 'Fee\FeeController@createFeeHead')->name('admin.createFeeHead')->middleware('auth:admin');
        Route::post('/post_createFeeHead', 'Fee\FeeController@post_createFeeHead')->name('post_createFeeHead')->middleware('auth:admin');
        Route::get('/viewFeeHead', 'Fee\FeeController@viewFeeHead')->middleware('multi.auth:admin,cashier');
        Route::get('/editFeeHead/{id}', 'Fee\FeeController@editFeeHead')->name('admin.editFeeHead')->middleware('auth:admin');
        Route::post('/editFeeHead', 'Fee\FeeController@post_editFeeHead')->name('editFeeHead')->middleware('auth:admin');
        Route::get('/deleteFeeHead/{id}', 'Fee\FeeController@deleteFeeHead')->name('admin.deleteFeeHead')->middleware('auth:admin');
        //Transport
        Route::get('/viewRoute', 'Fee\TransportController@viewRoute')->middleware('multi.auth:admin,cashier');
        Route::get('/createRoute', 'Fee\TransportController@createRoute')->name('admin.createRoute')->middleware('auth:admin'); ;
        Route::post('/post_createRoute', 'Fee\TransportController@post_createRoute')->name('post_createRoute')->middleware('auth:admin'); ;
        Route::get('/editRoute/{id}', 'Fee\TransportController@editRoute')->name('admin.editRoute')->middleware('auth:admin');
        Route::post('/editRoute', 'Fee\TransportController@post_editRoute')->name('editRoute')->middleware('auth:admin');
        Route::get('/deleteRouteName/{id}', 'Fee\TransportController@deleteRouteName')->name('admin.deleteRouteName')->middleware('auth:admin');
        //category
        Route::get('/category', 'Fee\FeeController@category')->name('admin.category')->middleware('auth:admin');
        Route::post('/addCategory', 'Fee\FeeController@addCategory')->name('addCategory')->middleware('auth:admin');
        Route::get('/deleteCategory/{id}', 'Fee\FeeController@deleteCategory')->name('admin.deleteCategory')->middleware('auth:admin');
        Route::get('/editCategory/{id}', 'Fee\FeeController@editCategory')->name('admin.editCategory')->middleware('auth:admin');
        Route::post('/editCategory', 'Fee\FeeController@post_editCategory')->name('editCategory')->middleware('auth:admin');
        //Fee Plan
        Route::get('/feePlan', 'Fee\FeeController@feePlan')->name('admin.feePlan')->middleware('auth:admin');
        Route::post('/post_feePlan', 'Fee\FeeController@post_feePlan')->middleware('auth:admin');
        Route::get('/editFeePlan/{id}', 'Fee\FeeController@editFeePlan')->name('admin.editFeePlan')->middleware('auth:admin');
        Route::post('/editFeePlan', 'Fee\FeeController@post_editFeePlan')->name('editFeePlan')->middleware('auth:admin');
        Route::get('/deleteFeePlan/{id}', 'Fee\FeeController@deleteFeePlan')->name('admin.deleteFeePlan')->middleware('auth:admin');
        //Route Fee Plan
        Route::get('/routeFeePlan', 'Fee\TransportController@routeFeePlan')->name('admin.routeFeePlan')->middleware('auth:admin');
        Route::post('/post_routeFeePlan', 'Fee\TransportController@post_routeFeePlan')->middleware('auth:admin');
        Route::get('/editRouteFeePlan/{id}', 'Fee\TransportController@editRouteFeePlan')->name('admin.editRouteFeePlan')->middleware('auth:admin');
        Route::post('/editRouteFeePlan', 'Fee\TransportController@post_editRouteFeePlan')->name('editRouteFeePlan')->middleware('auth:admin');
        Route::get('/deleteRouteFeePlan/{id}', 'Fee\TransportController@deleteRouteFeePlan')->name('admin.deleteRouteFeePlan')->middleware('auth:admin');
        //All Students Record
        Route::get('/allStudentsRecord', 'Fee\FeeController@allStudentsRecord')->name('fee.allStudentsRecord')->middleware('multi.auth:admin,cashier');
        //Deposit
       // For viewing the fee detail page
        Route::get('/getFeeDetail/{id}', 'Fee\FeeController@getFeeDetail')->name('getFeeDetail')->middleware('multi.auth:admin,cashier');

        // For submitting fee details
        Route::post('/postFeeDetail', 'Fee\FeeController@postFeeDetail')->name('postFeeDetail')->middleware('multi.auth:admin,cashier');
        Route::get('/form', [FeeController::class, 'showForm'])->name('fee.form')->middleware('multi.auth:admin,cashier');

        Route::post('/post_receipt', 'Fee\FeeController@post_receipt')->name('post_receipt')->middleware('multi.auth:admin,cashier');
        //Fee Card
        Route::get('/feeCard/{id}', 'Fee\FeeController@feeCard')->name('admin.feeCard')->middleware('multi.auth:admin,cashier');
        //Edit Fee Receipt
        Route::get('/editFeeReceipt/{id}', 'Fee\FeeController@editFeeReceipt')->name('admin.editFeeReceipt')->middleware('multi.auth:admin,cashier');
        Route::put('/editFeeReceipt', 'Fee\FeeController@post_editFeeReceipt')->name('editFeeReceipt')->middleware('multi.auth:admin,cashier');
        //Delete Fee Receipt
        Route::get('/deleteFeeReceipt/{receiptId}/{user_id}', 'Fee\FeeController@deleteFeeReceipt')->name('admin.deleteFeeReceipt')->middleware('multi.auth:admin,cashier');
        //Fee card
        Route::get('/dayBook', 'Fee\FeeController@dayBook')->middleware('multi.auth:admin,cashier');
        Route::post('/searchDaybook', 'Fee\FeeController@searchDaybook')->name('searchDaybook')->middleware('multi.auth:admin,cashier');
       // Due List
        Route::get('/dueList', 'Fee\FeeController@dueList')
        ->middleware('multi.auth:admin,cashier')
        ->name('dueList');

        Route::post('/dueList', 'Fee\FeeController@post_dueList')
        ->middleware('multi.auth:admin,cashier');
        //print receipt from allStudentRecords
        Route::get('/printReceipt/{id}', 'Fee\FeeController@printReceipt')->name('admin.printReceipt')->middleware('multi.auth:admin,cashier');

        //Concession
        Route::match(['get', 'post'], '/apply-concession', [FeeController::class, 'applyConcession'])->name('applyConcession')->middleware('auth:admin');
        Route::get('/api/get-user-fee-plans/{id}', [FeeController::class, 'getUserFeePlans'])->middleware('auth:admin');

        //Edit Concession
        Route::get('/edit-concession/{id}', [FeeController::class, 'editConcession'])->name('editConcession')->middleware('auth:admin');
        Route::post('/update-concession/{id}', [FeeController::class, 'updateConcession'])->name('updateConcession')->middleware('auth:admin');
        Route::delete('/concessions/{id}', [FeeController::class, 'deleteConcession'])->name('deleteConcession')->middleware('auth:admin');

        //To attach feePlan to User for relationship table fee_plan_user
        Route::get('/attach-fee-plans', [FeeController::class, 'attachFeePlansToUsers'])->middleware('auth:admin');

        //payment-invoice
        // Route::get('/payment-invoice', function () {

        //     // User::all()->where('email',Auth::all()->email)->notify(new emailNotification);
        //    //  dd(User::where('email','bali4u2001@gmail.com') -> first());
        //    return view('admin.fee.payment-invoice');
        //  });
        Route::get('/invoice', [FeeController::class, 'getInvoice'])->name('fee.invoice')->middleware('multi.auth:admin,cashier');


    });
