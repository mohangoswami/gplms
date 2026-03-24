<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResultSubjectComponent extends Model
{
    protected $fillable = [
        'performa_item_id',
        'component_id',
        'max_marks_override',
    ];

    /* =====================
     * Relationships
     * ===================== */

    public function performaItem()
    {
        return $this->belongsTo(ResultPerformaItem::class, 'performa_item_id');
    }

    public function component()
    {
        return $this->belongsTo(ResultComponent::class, 'component_id');
    }
}
