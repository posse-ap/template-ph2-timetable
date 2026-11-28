<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ReviewController extends Controller
{
    // レビューを投稿する
    public function store(Request $request, Course $course)
    {
        // ① 入力内容を確認する
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
        ], [
            'body.required' => 'レビューを入力してください',
            'body.max' => 'レビューは1000文字以内で入力してください',
        ]);

        // ② ログイン中のユーザーとして投稿
        $request->user()->reviews()->create([
            'course_id' => $course->id,
            'body' => $validated['body'],
        ]);

        // ③ 授業詳細ページに戻る
        return redirect('/courses/' . $course->id);
    }

    // レビューを削除する
    public function destroy(Review $review)
    {
        // ① 投稿者本人か確認
        Gate::authorize('delete', $review);

        // ② 授業IDを保存してから削除
        $courseId = $review->course_id;

        $review->delete();

        // ③ 授業詳細ページに戻る
        return redirect('/courses/' . $courseId);
    }
}
