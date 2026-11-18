<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
        'name',
    ];

    // タグに紐付いている授業
    public function courses()
    {
        return $this->belongsToMany(
            Course::class,
            'course_tag',
            'tag_id',
            'course_id'
        );
    }
}