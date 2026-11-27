<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'course_id',
        'body',
    ];

    // 投稿したユーザーとの関係
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // レビュー対象の授業との関係
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}