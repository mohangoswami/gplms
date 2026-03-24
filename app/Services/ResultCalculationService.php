<?php

namespace App\Services;

use App\StudentExamEntry;
use App\ResultTerm;
use Illuminate\Support\Collection;

class ResultCalculationService
{
    protected int $studentId;
    protected Collection $terms;

    public function __construct(int $studentId, $terms)
    {
        $this->studentId = $studentId;

        if (is_numeric($terms)) {
            $this->terms = ResultTerm::where('id', $terms)->get();
        } elseif ($terms instanceof Collection || is_array($terms)) {
            $this->terms = collect($terms);
        } else {
            throw new \InvalidArgumentException(
                'ResultCalculationService expects term id or collection'
            );
        }
    }

    /* =====================================================
        MAIN CALCULATOR
    ===================================================== */
    public function calculate(): array
    {
        $result = [
            'terms'   => [],
            'subjects_overall' => [],
            'overall' => [],
        ];

        $subjectAccumulator = [];

        foreach ($this->terms as $term) {

            $termSubjects = $this->calculateSubjects($term->id);

            // ❌ No entries → TERM ABSENT
            if (empty($termSubjects)) {
                $result['terms'][$term->id] = [
                    'term_id'    => $term->id,
                    'term_name'  => $term->name,
                    'status'     => 'AB',
                    'subjects'   => [],
                    'term_total' => null,
                    'term_max'   => null,
                    'percentage' => null,
                    'result'     => 'FAIL',
                ];
                continue;
            }

            $termTotal = 0;
            $termMax   = 0;
            $termPass  = true;

            foreach ($termSubjects as $itemId => $subject) {

                // ❌ Incomplete subject → TERM FAIL
                if ($subject['status'] === 'INCOMPLETE') {
                    $termPass = false;
                }

                // ✅ ONLY add numeric obtained
                if ($subject['obtained'] !== null) {
                    $termTotal += $subject['obtained'];
                }

                $termMax += $subject['max'];

                /* ==========================
                   YEARLY ACCUMULATOR
                ========================== */
                if (!isset($subjectAccumulator[$itemId])) {
                    $subjectAccumulator[$itemId] = [
                        'subject'  => $subject['subject'],
                        'obtained' => 0,
                        'max'      => 0,
                    ];
                }

                if ($subject['obtained'] !== null) {
                    $subjectAccumulator[$itemId]['obtained'] += $subject['obtained'];
                }

                $subjectAccumulator[$itemId]['max'] += $subject['max'];
            }

            $percentage = $termMax > 0
                ? round(($termTotal / $termMax) * 100, 2)
                : null;

            $result['terms'][$term->id] = [
                'term_id'    => $term->id,
                'term_name'  => $term->name,
                'status'     => 'OK',
                'subjects'   => $termSubjects,
                'term_total' => $termTotal,
                'term_max'   => $termMax,
                'percentage' => $percentage,
                'result'     => $termPass ? 'PASS' : 'FAIL',
            ];
        }

        /* =====================================================
           SUBJECT OVERALL (YEARLY)
        ===================================================== */
        foreach ($subjectAccumulator as $itemId => $row) {

            $percent = $row['max'] > 0
                ? round(($row['obtained'] / $row['max']) * 100, 2)
                : null;

            $result['subjects_overall'][$itemId] = [
                'subject'    => $row['subject'],
                'total'      => $row['obtained'],
                'max'        => $row['max'],
                'percentage' => $percent,
                'grade'      => $this->gradeFromPercentage($percent),
            ];
        }

        /* =====================================================
           GRAND OVERALL
        ===================================================== */
        $result['overall'] = $this->calculateGrandOverall($result['terms']);

        return $result;
    }

    /* =====================================================
        TERM → SUBJECT → COMPONENT
    ===================================================== */
    protected function calculateSubjects(int $termId): array
    {
        $rows = StudentExamEntry::with([
                'component',
                'performaItem.subCode',
                'subjectComponent',
            ])
            ->where('student_id', $this->studentId)
            ->where('term_id', $termId)
            ->get();

        if ($rows->isEmpty()) return [];

        $subjects = [];

        foreach ($rows->groupBy('result_performa_item_id') as $itemId => $entries) {

            $subjectName = optional(
                $entries->first()->performaItem->subCode
            )->subject;

            $obtained = 0;
            $max      = 0;
            $status   = 'OK';
            $components = [];

            foreach ($entries as $e) {

                // ❌ BLANK → INCOMPLETE
                if ($e->marks === null && $e->grade === null) {
                    $status = 'INCOMPLETE';
                }

                // ✅ ABSENT CHECK
                $isAbsent = ($e->grade === 'AB');

                if ($isAbsent) {
                    $marks = null; // 🚫 DO NOT convert to 0
                } else {
                    $marks = $e->marks !== null ? (float) $e->marks : null;
                }

                $componentMax = $e->subjectComponent && $e->subjectComponent->max_marks_override
                    ? (float) $e->subjectComponent->max_marks_override
                    : (float) optional($e->component)->max_marks;

                // ✅ ADD ONLY VALID MARKS
                if ($marks !== null) {
                    $obtained += $marks;
                }

                $max += $componentMax;

                $components[] = [
                    'component_id'   => $e->component_id,
                    'component_name' => optional($e->component)->name,
                    'obtained'       => $isAbsent ? 'AB' : $marks,
                    'max'            => $componentMax,
                    'percent'        => ($marks === null || $componentMax == 0)
                        ? null
                        : round(($marks / $componentMax) * 100, 2),
                    'grade'          => $e->grade,
                    'absent'         => $isAbsent,
                ];
            }

            $percent = $max > 0 && $obtained !== null
                ? round(($obtained / $max) * 100, 2)
                : null;

            $subjects[$itemId] = [
                'subject'    => $subjectName,
                'components' => $components,
                'obtained'   => $obtained,
                'max'        => $max,
                'percent'    => $percent,
                'grade'      => $this->gradeFromPercentage($percent),
                'pass'       => $percent !== null && $percent >= 33,
                'status'     => $status,
            ];
        }

        return $subjects;
    }

    /* =====================================================
        GRAND TOTAL (ALL TERMS)
    ===================================================== */
    protected function calculateGrandOverall(array $terms): array
    {
        $grandObtained = 0;
        $grandMax      = 0;
        $finalResult   = 'PASS';

        foreach ($terms as $term) {

            if ($term['status'] === 'AB' || $term['result'] === 'FAIL') {
                $finalResult = 'FAIL';
            }

            if ($term['term_total'] !== null) {
                $grandObtained += $term['term_total'];
            }

            if ($term['term_max'] !== null) {
                $grandMax += $term['term_max'];
            }
        }

        $percentage = $grandMax > 0
            ? round(($grandObtained / $grandMax) * 100, 2)
            : null;

        return [
            'grand_total' => $grandObtained,
            'grand_max'   => $grandMax,
            'percentage'  => $percentage,
            'grade'       => $percentage !== null
                ? self::gradeFromPercentage($percentage)
                : null,
            'result'      => $finalResult,
        ];
    }

    /* =====================================================
        HELPER (PDF)
    ===================================================== */
    public static function componentValue(array $components, string $name)
    {
        foreach ($components as $c) {
            if (($c['component_name'] ?? null) === $name) {
                return $c['obtained'] ?? '-';
            }
        }
        return '-';
    }

    /* =====================================================
        GRADE SCALE (CBSE)
    ===================================================== */
    public static function gradeFromPercentage($percent)
    {
        if ($percent === null) return 'AB';
        if ($percent >= 90) return 'A1';
        if ($percent >= 80) return 'A2';
        if ($percent >= 70) return 'B1';
        if ($percent >= 60) return 'B2';
        if ($percent >= 50) return 'C1';
        if ($percent >= 40) return 'C2';
        if ($percent >= 33) return 'D';
        return 'E';
    }
}
