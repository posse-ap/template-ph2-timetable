<?php

namespace App\Http\Controllers;

use App\Mail\AnnouncementMail;
use App\Models\Announcement;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AdminAnnouncementController extends Controller
{
    // お知らせを出す画面（存在しない授業は、{course} のバインドで 404 になる）
    public function create(Course $course)
    {
        return view('admin.announcements.create', ['course' => $course]);
    }

    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:40'],
            'body' => ['required', 'string', 'max:200'],
        ], [
            'title.required' => 'タイトルを入力してください',
            'title.max' => 'タイトルは40文字以内で入力してください',
            'body.required' => '本文を入力してください',
            'body.max' => '本文は200文字以内で入力してください',
        ]);

        $announcement = $course->announcements()->create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'body' => $validated['body'],
            'published_at' => now(),
        ]);

        $students = $course->users;  // 承認済みだけを返す関係

        foreach ($students as $student) {
            try {
                Mail::to($student->email)->send(new AnnouncementMail($announcement));
            } catch (\Throwable $e) {
                // 1人の送信失敗で、残りの学生に送れなくならないよう、記録して次へ進む
                Log::error('お知らせメールの送信に失敗', [
                    'email' => $student->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return redirect('/courses/' . $course->id);
    }
}
