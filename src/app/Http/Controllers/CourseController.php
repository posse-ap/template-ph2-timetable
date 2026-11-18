<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    // 授業一覧・検索・タグ・お気に入り
    public function index(Request $request)
    {
        // 検索キーワード
        $q = trim((string) $request->query('q', ''));

        // 選択されたタグID
        $tagId = $request->query('tag');

        // ① 授業とタグをまとめて取得
        // ② 自分の時間割登録だけを取得
        // ③ 気になる登録者数をまとめて取得
        $query = Course::query()
            ->with([
                'tags',
                'timetableEntries' => function ($builder) use ($request) {
                    $builder->where(
                        'user_id',
                        $request->user()->id
                    );
                }
            ])
            ->withCount('favoritedByUsers');

        // W8の授業名・教員名検索を残す
        if ($q !== '') {

            $escaped = str_replace(
                ['\\', '%', '_'],
                ['\\\\', '\\%', '\\_'],
                $q
            );

            $pattern = '%' . $escaped . '%';

            $query->where(function ($builder) use ($pattern) {
                $builder->where('name', 'ilike', $pattern)
                    ->orWhere('teacher', 'ilike', $pattern);
            });
        }

        // W9追加：指定されたタグの授業だけを残す
        if ($tagId !== null && $tagId !== '') {

            // タグIDが数値か確認する
            $request->validate([
                'tag' => ['integer', 'exists:tags,id']
            ]);

            $query->whereHas('tags', function ($builder) use ($tagId) {
                $builder->where('tags.id', $tagId);
            });
        }

        // 既存の並び順は維持
        $courses = $query
            ->orderBy('day')
            ->orderBy('period')
            ->orderBy('code')
            ->get();

        // ログインユーザーがお気に入り登録した授業ID
        $favoriteIds = $request->user()
            ->favoriteCourses()
            ->pluck('courses.id')
            ->all();

        return view('courses.index', [
            'courses' => $courses,
            'favoriteIds' => $favoriteIds,
        ]);
    }

    // 授業詳細・レビュー・タグ・受講者
    public function show(Course $course)
    {
        // W9追加：タグと受講者を取得
        $course->load([
            'tags',
            'students' => function ($query) {
                $query->select('users.id', 'users.name');
            }
        ]);

        // W9追加：受講者数
        $course->loadCount('students');

        // W8のレビュー取得処理を維持
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
