<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\TimetableEntry;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Gate;
use App\Models\Announcement;

class TimetableEntryController extends Controller
{
    // ① 自分の時間割を表示
    public function index(Request $request)
    {
        $entries = $request->user()
            ->entries()
            ->select('timetable_entries.*')
            ->with([
            'course' => function ($query) {
                $query->withCount('students');
            }
            ])

            ->join(
                'courses',
                'courses.id',
                '=',
                'timetable_entries.course_id'
            )
            ->orderBy('courses.day')
            ->orderBy('courses.period')
            ->orderBy('courses.code')
            ->get();
        $recentAnnouncements = Announcement::query()

        ->whereIn(
        'course_id',
        $request->user()->entries()
            ->where('status', 'approved')
            ->select('course_id')
        )

        ->where('created_at', '>=', now()->subDay())

        ->with('course')

        ->latest()

        ->get();

        return view('entries.index', [
            'entries' => $entries,
            'recentAnnouncements' => $recentAnnouncements,
        ]);
    }

    // ② 授業登録画面を表示
    public function create()
    {
        $courses = Course::query()
            ->orderBy('day')
            ->orderBy('period')
            ->orderBy('code')
            ->get();

        return view('entries.create', [
            'courses' => $courses,
        ]);
    }

    // ③ 履修を申請する
    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => [
                'bail',
                'required',
                'integer',
                Rule::exists('courses', 'id'),
            ],
        ], [
            'course_id.required' => '授業を一覧から選んでください',
            'course_id.integer' => '授業を一覧から選んでください',
            'course_id.exists' => '授業を一覧から選んでください',
        ]);

        $course = Course::findOrFail(
            $validated['course_id']
        );

        // 同じ授業をすでに申請・登録しているか
        $alreadyApplied = $request->user()
            ->entries()
            ->where('course_id', $course->id)
            ->exists();

        if ($alreadyApplied) {
            throw ValidationException::withMessages([
                'course_id' =>
                    'この授業はすでに申請済み、または履修履歴があります。',
            ]);
        }

        // 自分の時間割で曜日・時限が重複していないか
        $sameTimeEntryExists = $request->user()
            ->entries()
            ->whereIn('status', ['pending', 'approved'])
            ->whereHas('course', function ($query) use ($course) {
                $query
                    ->where('day', $course->day)
                    ->where('period', $course->period);
            })
            ->exists();

        if ($sameTimeEntryExists) {
            throw ValidationException::withMessages([
                'course_id' =>
                    'その曜日・時限にはすでに授業が申請・登録されています。',
            ]);
        }

        // ログインユーザーの申請として保存
        $request->user()
            ->entries()
            ->create([
                'course_id' => $course->id,
                'status' => 'pending',
            ]);

        return redirect('/entries')
            ->with('success', '履修を申請しました');
    }

    // ④ 申請中の授業を取り消す
    public function destroy(Request $request, $id)
    {
        // 削除対象を取得
        $entry = TimetableEntry::findOrFail($id);

        // 本人以外は403で拒否
        Gate::authorize('delete', $entry);

        // 申請中か確認
        if ($entry->status !== 'pending') {
            return back()->withErrors([
                'entry' => '申請中の授業だけ取り消せます。',
            ]);
        }

        // 申請を削除
        $entry->delete();

        return redirect('/entries')
            ->with('success', '申請を取り消しました。');
    }
}
