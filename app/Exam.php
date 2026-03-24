<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $guard = 'teacher';

    protected $fillable = [
        'class', 'name', 'email', 'subject', 'title', 'discription', 'fileSize', 'fileUrl', 'examUrl', 'maxMarks', 'startExam', 'endExam', 'studentReturn', 'topperShown', 'type',
    ];

    /**
     * Casts
     * Ensure maxMarks is treated as a decimal with 2 places when retrieved
     */
    protected $casts = [
        'maxMarks' => 'decimal:2',
    ];

    public function term()
        {
            return $this->belongsTo(\App\Term::class, 'term_id');
        }

        // Admin who created the exam
    public function admin()
        {
            return $this->belongsTo(\App\Admin::class, 'admin_id');
        }

        // if you still keep teacher_id for any reason:
        public function teacher()
        {
            return $this->belongsTo(\App\Teacher::class, 'teacher_id');
        }
        // Exams associated with this title
        public function studentExams()
        {
            return $this->hasMany(\App\studentExams::class, 'titleId');
        }

}
