<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResultTerm extends Model
{
    protected $fillable = [
        'performa_id',
        'name',
        'order_no',
    ];

    /* =====================
     * Relationships
     * ===================== */

    public function performa()
    {
        return $this->belongsTo(ResultPerforma::class, 'performa_id');
    }

    public function components()
    {
        return $this->hasMany(ResultComponent::class, 'term_id')
                    ->orderBy('order_no');
    }
}
