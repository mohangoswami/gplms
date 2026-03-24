<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultStudentAttendance extends Model
{
    use HasFactory;

        protected $fillable = [
        'student_id',
        'term_id',
        'days_present',
        'working_days',
    ];
}
