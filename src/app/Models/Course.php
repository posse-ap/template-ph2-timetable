<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    public $timestamps = false;

    public function timetableEntries(): HasMany
    {
        return $this->hasMany(TimetableEntry::class);
    }

    public function approvedEntries(): HasMany
    {
        return $this->hasMany(TimetableEntry::class)
            ->where('status', 'approved');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(
            Tag::class,
            'course_tag',
            'course_id',
            'tag_id'
        );
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'timetable_entries',
            'course_id',
            'user_id'
        )->wherePivot('status', 'approved');
    }

    public function favoritedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'favorites',
            'course_id',
            'user_id'
        );
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
}
