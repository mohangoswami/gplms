<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultPerforma extends Model
{

protected $fillable = [
    'class',
    'academic_year',
    'name',
    'is_default',
];


public function terms()
{
    return $this->hasMany(\App\ResultTerm::class, 'performa_id')
                ->orderBy('order_no');
}

public function items()
{
    return $this->hasMany(\App\ResultPerformaItem::class, 'performa_id')
                ->orderBy('subject_order');
}

}

