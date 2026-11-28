<?php

namespace App\Http\Controllers;

use App\Models\Course;

class CourseController extends Controller
{
    // 授業一覧を表示する
    public function index()
    {
        $courses = Course::query()
            ->with('timetableEntries')
            ->orderBy('day')
            ->orderBy('period')
            ->orderBy('code')
            ->get();

        return view('courses.index', [
            'courses' => $courses,
        ]);
    }

    // ★ ここから新しく追加する
    // 授業詳細とお知らせを表示する
    public function show(Course $course)
    {
        $announcements = $course->announcements()
            ->where('created_at', '>=', now()->subDay())
            ->latest()
            ->get();

        return view('courses.show', [
            'course' => $course,
            'announcements' => $announcements,
        ]);
    }
}
