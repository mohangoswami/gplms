<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResultEntryPermission extends Model
{
    protected $table = 'result_entry_permissions';

    protected $fillable = [
        'teacher_id',
        'class',
        'term',
        'component_id',
    ];

    /* ==========================
     * Relationships
     * ========================== */

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }
}
