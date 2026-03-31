<?php
// app/Models/ResultFinalization.php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResultFinalization extends Model
{
    protected $fillable = [
        'student_id',
        'performa_id',
        'status',
        'finalized_by_id',
        'finalized_by_role',
        'finalized_at',
        'pdf_path',
    ];

    public static function isFinal($studentId, $performaId)
    {
        return self::where([
            'student_id'  => $studentId,
            'performa_id' => $performaId,
            'status'      => 'FINAL',
        ])->exists();
    }
}
