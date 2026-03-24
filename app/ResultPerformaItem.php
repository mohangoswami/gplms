<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultPerformaItem extends Model
{
    protected $fillable = [
        'performa_id',
        'sub_code_id',
        'term',
        'evaluation_type',
        'component',
        'max_marks',
        'subject_order',
        'component_order',
        'is_included'
    ];

    public function performa()
    {
        return $this->belongsTo(ResultPerforma::class, 'performa_id');
    }



    public function subCode()
    {
        return $this->belongsTo(\App\subCode::class, 'sub_code_id');
    }

    public function subjectComponents()
    {
        return $this->hasMany(
            ResultSubjectComponent::class,
            'performa_item_id'
        );
    }
}

