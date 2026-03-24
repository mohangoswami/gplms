<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultStudentHealthRecord extends Model
{
    use HasFactory;

        protected $fillable = [
        'student_id',
        'term_id',
        'height',
        'weight',
        'remark',
    ];
}
