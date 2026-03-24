<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentExamEntry extends Model
{
    protected $table = 'student_exam_entries';

    protected $fillable = [
        'student_id',
        'result_performa_item_id',
        'component_id',     // ✅ ADD THIS
        'term_id',          // ✅ ADD THIS
        'marks',
        'grade',
        'entered_by_id',
        'entered_by_role',
    ];

    /* ==========================
     * Relationships
     * ========================== */

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function performaItem()
    {
        return $this->belongsTo(
            ResultPerformaItem::class,
            'result_performa_item_id'
        );
    }

    public function component()
    {
        return $this->belongsTo(ResultComponent::class, 'component_id');
    }

    public function term()
    {
        return $this->belongsTo(ResultTerm::class, 'term_id');
    }


public function subjectComponent()
{
    return $this->hasOne(
        \App\ResultSubjectComponent::class,
        'component_id',
        'component_id'
    );
}

}
