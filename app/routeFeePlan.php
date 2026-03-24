<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class routeFeePlan extends Model
{
    protected $guard = 'admin';

    protected $fillable = [
        'routeName', 'value'
    ];
}
