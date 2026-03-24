<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeePlan extends Model
{
    protected $guard = 'admin';

    protected $fillable = [
        'class', 'category', 'feeHead', 'value',
    ];

    /**
     * Relationship with FeeHead
     * Assuming 'feeHead' in FeePlan is a foreign key referencing 'id' in FeeHead
     */
    public function feeHead()
    {
        return $this->belongsTo(FeeHead::class, 'feeHead_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'fee_plan_user', 'fee_plan_id', 'user_id');
    }
}
