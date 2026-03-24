<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResultComponent extends Model
{
    protected $fillable = [
        'term_id',
        'name',
        'evaluation_type',
        'max_marks',
        'order_no',
        'is_included',
    ];

    protected $casts = [
        'is_included' => 'boolean',
    ];

    /* =====================
     * Relationships
     * ===================== */

    public function term()
    {
        return $this->belongsTo(ResultTerm::class, 'term_id');
    }

    public function subjectMappings()
    {
        return $this->hasMany(ResultSubjectComponent::class, 'component_id');
    }
}
