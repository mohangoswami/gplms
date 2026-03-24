<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResultCoScholasticArea extends Model
{
    protected $fillable = [
        'performa_id',
        'class',
        'area_name',
        'display_order',
        'is_active',
    ];

    // ✅ Correct relationship
    public function performa()
    {
        return $this->belongsTo(
            ResultPerforma::class,
            'performa_id'
        );
    }
}
