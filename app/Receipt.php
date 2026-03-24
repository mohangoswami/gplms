<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    protected $guard = 'admin';

    protected $fillable = [
        'user_id', 'receiptId', 'feeHead', 'date', 'oldBalance', 'total', 'lateFee',
        'concession', 'netFee', 'receivedAmt', 'balance', 'paymentMode',
        'bankName', 'chequeNo', 'chequeDate', 'remarks', 'submission_token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function feePlans()
    {
        return $this->hasMany(FeePlan::class);
    }
}
