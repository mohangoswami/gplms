<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouteName extends Model
{
    use HasFactory;

    // Optional: Guard definition for role-specific logic (not mandatory for relationships)
    protected $guard = 'admin';

    // Define mass assignable fields
    protected $fillable = [
        'routeName',
        'accountName',
        'frequency',
        'jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'
    ];

    /**
     * Relationship: A route can have many users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class, 'route_id', 'id'); // 'route_id' is the foreign key in users
    }
}
