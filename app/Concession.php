<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Concession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'fee_plan_id',
        'fee_type',
        'concession_fee',
        'reason',
    ];

    // Define the relationship with the FeePlan model
    public function feePlan()
    {
        return $this->belongsTo(FeePlan::class, 'fee_plan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }



}

