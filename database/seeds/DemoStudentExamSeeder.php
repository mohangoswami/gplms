<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Exam;

class DemoStudentExamSeeder extends Seeder
{
    public function run()
    {
        /* ==========================
         * 1️⃣ Fetch students of class
         * ========================== */
        $students = DB::table('users')
            ->where('class', '5TH')
            ->select('id', 'name', 'email', 'class')
            ->get();

        /* ==========================
         * 2️⃣ Fetch exams of class
         * ========================== */
        $exams = Exam::where('class', '5TH')
            ->select(
                'id',
                'subject',
                'title',
                'email',
                'maxMarks',
                'teacher_id'
            )
            ->get();

        /* ==========================
         * 3️⃣ Loop students × exams
         * ========================== */
        foreach ($students as $student) {

            foreach ($exams as $exam) {

                $max = (float) $exam->maxMarks;

                /* ==========================
                 * 4️⃣ Realistic CBSE marks
                 * ========================== */
                if ($max <= 10) {
                    $marks = rand(4, 10);           // PT / Oral
                } elseif ($max <= 20) {
                    $marks = rand(6, $max);         // Notebook / SE
                } elseif ($max <= 80) {
                    $marks = rand(25, $max);        // HY / Annual
                } else {
                    $marks = rand(0, $max);
                }

                /* ==========================
                 * 5️⃣ Prevent duplicate demo data
                 * ========================== */
                $exists = DB::table('student_exams')
                    ->where('studentId', $student->id)
                    ->where('titleId', $exam->id)
                    ->exists();

                if ($exists) {
                    continue;
                }

                /* ==========================
                 * 6️⃣ Insert demo marks
                 * ========================== */
                DB::table('student_exams')->insert([
                    'titleId'       => $exam->id,
                    'studentId'     => $student->id,
                    'class'         => $student->class,
                    'name'          => $student->name,
                    'email'         => $student->email,
                    'teacherEmail'  => $exam->email,
                    'subject'       => $exam->subject,
                    'title'         => $exam->title,
                    'marksObtain'   => $marks,
                    'maxMarks'      => $max,
                    'submittedDone' => 1,
                    'teacherId'     => $exam->teacher_id,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }
    }
}
