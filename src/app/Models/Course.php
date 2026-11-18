<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    public $timestamps = false;

    public function timetableEntries(): HasMany
    {
        return $this->hasMany(TimetableEntry::class);
    }

    public function getDayNameAttribute(): string
    {
        return match ($this->day) {
            1 => '月曜日',
            2 => '火曜日',
            3 => '水曜日',
            4 => '木曜日',
            5 => '金曜日',
            default => '不明',
        };
    }
    // ① この授業に付いているタグ
public function tags()
{
    return $this->belongsToMany(
        Tag::class,
        'course_tag',
        'course_id',
        'tag_id'
    );
}

// ② この授業を履修している学生
public function students()
{
    return $this->belongsToMany(
        User::class,
        'timetable_entries',
        'course_id',
        'user_id'
    );
}

// ③ この授業を気になる登録したユーザー
public function favoritedByUsers()
{
    return $this->belongsToMany(
        User::class,
        'favorites',
        'course_id',
        'user_id'
    );
}
}
