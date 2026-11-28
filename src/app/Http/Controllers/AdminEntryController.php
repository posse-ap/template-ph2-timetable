<?php

namespace App\Http\Controllers;

use App\Models\TimetableEntry;
use Illuminate\Support\Facades\DB;

class AdminEntryController extends Controller
{
    public function index()
    {
        $entries = TimetableEntry::with([
            'user',
            'course' => function ($query) {
                $query->withCount('approvedEntries');
            },
        ])
            ->where('status', 'pending')
            ->orderBy('id')
            ->get();

        return view('admin.entries.index', [
            'entries' => $entries,
        ]);
    }

    public function approve(TimetableEntry $entry)
    {
        return DB::transaction(function () use ($entry) {
            $course = DB::table('courses')
                ->where('id', $entry->course_id)
                ->lockForUpdate()
                ->first();

            if (! $course) {
                abort(404, '授業が見つかりません。');
            }

            $latestEntry = TimetableEntry::whereKey($entry->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($latestEntry->status !== 'pending') {
                return back()->withErrors([
                    'entry' => '申請中の授業だけ承認できます。',
                ]);
            }

            $approvedCount = TimetableEntry::where('course_id', $course->id)
                ->where('status', 'approved')
                ->count();

            if ($approvedCount >= $course->capacity) {
                return back()->withErrors([
                    'entry' => '定員に達しているため承認できません。',
                ]);
            }

            $latestEntry->update([
                'status' => 'approved',
                'decided_at' => now(),
            ]);

            return back()->with('success', '承認しました。');
        });
    }

    public function reject(TimetableEntry $entry)
    {
        return DB::transaction(function () use ($entry) {
            $latestEntry = TimetableEntry::whereKey($entry->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($latestEntry->status !== 'pending') {
                return back()->withErrors([
                    'entry' => '申請中の授業だけ却下できます。',
                ]);
            }

            $latestEntry->update([
                'status' => 'rejected',
                'decided_at' => now(),
            ]);

            return back()->with('success', '却下しました。');
        });
    }
}
