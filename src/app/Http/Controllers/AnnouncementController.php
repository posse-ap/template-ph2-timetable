<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Mail\AnnouncementMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AnnouncementController extends Controller
{
    public function store(Request $request, Course $course)
    {
        // ① 入力内容を確認
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'body' => 'required|string|max:100',
        ]);

        // ② お知らせを保存
        $announcement = $course->announcements()
            ->create($validated);

        // ③ 承認済み学生にメールを送信
        foreach ($course->students as $student) {

            Mail::to($student->email)->send(
                new AnnouncementMail(
                    $announcement,
                    $student
                )
            );

        }

        // ④ 授業詳細ページに戻る
        return redirect('/courses/' . $course->id)
            ->with('success', 'お知らせを投稿しました。');
    }
}