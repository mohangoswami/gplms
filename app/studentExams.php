<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class studentExams extends Model
{
    protected $fillable = [
    'titleId','studentId', 'class', 'name', 'email', 'subject', 'title','submittedDone','marksObtain','maxMarks', 'teacherId', 'remark'
    ];


    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacherId');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'studentId');
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'titleId');
    }

}
