<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request; 

class CourseController extends Controller
{
    // 授業一覧・検索
    public function index(Request $request)
    {
        // ★追加① 検索フォームの文字を受け取る
        $q = trim((string) $request->query('q', ''));

        // 既存の授業一覧取得処理
        $query = Course::query()
            ->with('timetableEntries');

        // ★追加② 検索文字があれば授業を絞り込む
        if ($q !== '') {
            $query->where(function ($builder) use ($q) {
                $builder->where('name', 'ilike', "%{$q}%")
                    ->orWhere('teacher', 'ilike', "%{$q}%");
            });
        }

        // 既存の並び替え処理は残す
        $courses = $query
            ->orderBy('day')
            ->orderBy('period')
            ->orderBy('code')
            ->get();

        return view('courses.index', [
            'courses' => $courses,
        ]);
    }
    // 授業の詳細とレビューを表示する
    public function show(Course $course)
    {
        $reviews = $course->reviews()
            ->with('user')
            ->latest()
            ->get();

        return view('courses.show', [
            'course' => $course,
            'reviews' => $reviews,
        ]);
    }
}