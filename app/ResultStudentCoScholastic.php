<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResultStudentCoScholastic extends Model
{
    protected $fillable = [
        'student_id',
        'term_id',
        'co_scholastic_area_id',
        'grade',
    ];

    // ✅ Area belongs to master
    public function area()
    {
        return $this->belongsTo(
            ResultCoScholasticArea::class,
            'co_scholastic_area_id'
        );
    }
}
