<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Cashier extends Authenticatable
{
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
