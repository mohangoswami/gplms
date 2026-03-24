<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'admission_number', 'rollNo', 'name', 'fName', 'mName','oldBalance', 'dob',
        'address', 'mobile', 'rfid', 'email', 'password', 'grade', 'section',
        'aadhar', 'pen', 'apaar', 'house', 'caste', 'gender', 'app_permission', 'exam_permission',
        'category_id', 'route_id',
    ];

    /**
     * Define a one-to-many relationship with Receipt.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function receipts()
    {
        return $this->hasMany(Receipt::class, 'user_id', 'id'); // 'user_id' is the foreign key, 'id' is the local key
    }

    public function route()
    {
        return $this->belongsTo(RouteName::class, 'route_id', 'id'); // Adjust foreign key as per your schema
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function concessions()
        {
            return $this->hasMany(Concession::class, 'user_id');
        }

    // public function feePlans()
    //     {
    //         return $this->belongsToMany(FeePlan::class, 'fee_plan_user', 'user_id', 'fee_plan_id');
    //     }
    public function feePlans()
    {
        return $this->belongsToMany(FeePlan::class, 'fee_plan_user', 'user_id', 'fee_plan_id');
    }



    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'dob' => 'date',
    ];
}
