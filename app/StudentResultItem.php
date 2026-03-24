<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentResultItem extends Model
{
    protected $table = 'student_result_items';

    protected $fillable = [
        'student_id',
        'performa_item_id',
        'marks',
        'grade',
    ];

    /* ==========================
     * Relationships
     * ========================== */

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function performaItem()
    {
        return $this->belongsTo(ResultPerformaItem::class, 'performa_item_id');
    }
}
