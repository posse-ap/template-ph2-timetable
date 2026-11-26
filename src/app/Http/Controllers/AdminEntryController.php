<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TimetableEntry;
use App\Models\Course;
use Illuminate\Support\Facades\DB;

class AdminEntryController extends Controller
{
    public function index(Request $request)
    {
        $entries = TimetableEntry::with(['user', 'course' => function ($q) {
                $q->withCount('users');
            }])
            ->where('status', TimetableEntry::PENDING)
            ->orderBy('created_at')
            ->get();

        return view('admin.entries.index', ['entries' => $entries]);
    }

    public function approve($id)
    {
        return DB::transaction(function () use ($id) {
            $entry = TimetableEntry::findOrFail($id);

            // 授業の行をロックする（approve / reject / destroy は、すべて同じ授業の行をロックする）
            Course::where('id', $entry->course_id)->lockForUpdate()->first();

            // ロックを取ったあとの、最新の状態に読み直す
            $entry->refresh();

            if ($entry->status !== TimetableEntry::PENDING) {
                return back()->withErrors(['entry' => '申請中の授業だけを承認できます']);
            }

            $approved = $entry->course->users()->count();
            if ($approved >= $entry->course->capacity) {
                return back()->withErrors(['entry' => '定員に達しているため承認できません']);
            }

            $entry->status = TimetableEntry::APPROVED;
            $entry->decided_at = now();
            $entry->save();

            return redirect('/admin/entries');
        });
    }

    public function reject($id)
    {
        return DB::transaction(function () use ($id) {
            $entry = TimetableEntry::findOrFail($id);

            // approve と同じ授業の行をロックする
            Course::where('id', $entry->course_id)->lockForUpdate()->first();

            $entry->refresh();

            if ($entry->status !== TimetableEntry::PENDING) {
                return back()->withErrors(['entry' => '申請中の授業だけを却下できます']);
            }

            $entry->status = TimetableEntry::REJECTED;
            $entry->decided_at = now();
            $entry->save();

            return redirect('/admin/entries');
        });
    }
}
