<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimetableEntry extends Model
{
    protected $fillable = [
        'course_id',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
