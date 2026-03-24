<?php

namespace App\Http\Controllers\Result;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\subCode;
use App\ResultPerforma;
use App\ResultTerm;
use App\ResultPerformaItem;
use App\ResultSubjectComponent;
use App\Exam;
use DB;


class Seeder extends Controller
{
    public function dataSeederRun()
{
    $classes = subCode::distinct()
        ->pluck('class')
        ->filter()
        ->unique();

    foreach ($classes as $class) {
        ResultPerforma::firstOrCreate(
            [
                'class' => $class,
                'is_default' => 1,
            ],
            [
                'academic_year' => '2025-26',
                'name' => 'Default Result Performa',
            ]
        );
    }
}


public function demoDataSeedingRun()
    {
        /* ==========================
         * 1️⃣ Fetch students of class
         * ========================== */
        $students = DB::table('users')
            ->where('grade', '10TH')
            ->select('id', 'name', 'email', 'grade')
            ->get();
        /* ==========================
         * 2️⃣ Fetch exams of class
         * ========================== */
        $exams = Exam::where('class', '10TH')
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

                $marks = rand(0, $max);


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
                    'class'         => $student->grade,
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
        return 'Demo student exam data inserted for class -' . $student->grade;
    }




public function DefaultResultPerformaSeeder()
    {
    DB::transaction(function () {

            /* ==========================
             * TERM SEQUENCE
             * ========================== */
            $terms = [
                'P1' => 'Periodic I',
                'HY' => 'Half Yearly',
                'P2' => 'Periodic II',
                'AN' => 'Annual',
            ];

            /* ==========================
             * COMPONENT STRUCTURE
             * ========================== */
            $components = [
                'P1' => [
                    ['name' => 'PT',       'max' => 10, 'order' => 1],
                    ['name' => 'Notebook', 'max' => 5,  'order' => 2],
                    ['name' => 'SE',       'max' => 5,  'order' => 3],
                ],
                'HY' => [
                    ['name' => 'Written',  'max' => 80, 'order' => 1],
                ],
                'P2' => [
                    ['name' => 'PT',       'max' => 10, 'order' => 1],
                    ['name' => 'Notebook', 'max' => 5,  'order' => 2],
                    ['name' => 'SE',       'max' => 5,  'order' => 3],
                ],
                'AN' => [
                    ['name' => 'Written',  'max' => 80, 'order' => 1],
                ],
            ];

            /* ==========================
             * ALL CLASSES FROM sub_codes
             * ========================== */
            $classes = subCode::select('class')
                ->distinct()
                ->pluck('class');

            foreach ($classes as $class) {

                /* ==========================
                 * CREATE / FETCH DEFAULT PERFORMA
                 * ========================== */
                $performa = ResultPerforma::firstOrCreate(
                    [
                        'class'      => $class,
                        'is_default' => 1
                    ],
                    [
                        'name' => 'Default CBSE Result Performa'
                    ]
                );

                /* ==========================
                 * FETCH SUBJECTS OF CLASS
                 * ========================== */
                $subjects = subCode::where('class', $class)
                    ->orderBy('id')
                    ->get();

                $subjectOrder = 1;

                foreach ($subjects as $sub) {

                    $subjectName = strtoupper(trim($sub->subject));

                    /* ==========================
                     * EXCLUDE HOMEWORK SUBJECTS
                     * ========================== */
                    $isIncluded = !(
                        strpos($subjectName, 'HOMEWORK') !== false ||
                        strpos($subjectName, 'HOLIDAY') !== false
                    );

                    /* ==========================
                     * GRADE-ONLY SUBJECTS
                     * ========================== */
                    $isGradeOnly = in_array($subjectName, [
                        'GK',
                        'G.K.',
                        'MORAL SCIENCE',
                        'MS',
                        'ART',
                        'COMPUTER'
                    ]);

                    foreach ($terms as $termCode => $termLabel) {

                        if ($isGradeOnly) {

                            /* ==========================
                             * GRADE SUBJECT (ONE ROW PER TERM)
                             * ========================== */
                            ResultPerformaItem::updateOrCreate(
                                [
                                    'performa_id' => $performa->id,
                                    'sub_code_id' => $sub->id,
                                    'term'        => $termCode,
                                    'component'   => null,
                                ],
                                [
                                    'evaluation_type' => 'GRADE',
                                    'max_marks'       => null,
                                    'subject_order'   => $subjectOrder,
                                    'component_order' => 0,
                                    'is_included'     => $isIncluded,
                                ]
                            );

                        } else {

                            /* ==========================
                             * MARKS SUBJECT (MULTI COMPONENT)
                             * ========================== */
                            foreach ($components[$termCode] as $comp) {

                                ResultPerformaItem::updateOrCreate(
                                    [
                                        'performa_id' => $performa->id,
                                        'sub_code_id' => $sub->id,
                                        'term'        => $termCode,
                                        'component'   => $comp['name'],
                                    ],
                                    [
                                        'evaluation_type' => 'MARKS',
                                        'max_marks'       => $comp['max'],
                                        'subject_order'   => $subjectOrder,
                                        'component_order' => $comp['order'],
                                        'is_included'     => $isIncluded,
                                    ]
                                );
                            }
                        }
                    }

                    $subjectOrder++;
                }
            }
        });
        return 'Demo student exam data inserted for class -' ;

    }




public function ResultCoScholasticSeed()
    {
            DB::table('result_co_scholastic_areas')->insert([
    [
        'performa_id' => 1,
        'class' => 'Nursery',
        'area_name' => 'Poem',
        'display_order' => 1,
    ],
    [
        'performa_id' => 1,
        'class' => 'Nursery',
        'area_name' => 'Rhymes',
        'display_order' => 2,
    ],
    [
        'performa_id' => 1,
        'class' => '8',
        'area_name' => 'Computer',
        'display_order' => 1,
    ],
    [
        'performa_id' => 1,
        'class' => '8',
        'area_name' => 'Discipline',
        'display_order' => 2,
    ],
]);
    }
}

