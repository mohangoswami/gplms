<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\ResultPerforma;
use App\ResultPerformaItem;
use App\subCode;

class DefaultResultPerformaSeeder extends Seeder
{
    public function run()
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
    }
}
