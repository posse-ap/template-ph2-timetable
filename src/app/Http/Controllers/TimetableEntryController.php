<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TimetableEntry;
use App\Models\Course;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\UniqueConstraintViolationException;

class TimetableEntryController extends Controller
{
    public function index(Request $request)
    {
        $entries = $request->user()->entries()->with(['course' => function ($query) {
            $query->withCount('users'); }])
            ->join('courses', 'timetable_entries.course_id', '=', 'courses.id')
            ->select('timetable_entries.*')
            ->orderBy('courses.day')
            ->orderBy('courses.period')
            ->get();
        
        return view('entries.index', ['entries' => $entries]);
    }

    public function create()
    {
        return view('entries.create', ['courses' => Course::orderBy('day')->orderBy('period')->get()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
        ],[
            'course_id.required' => '授業を一覧から選んでください',
            'course_id.exists' => '授業を一覧から選んでください',
        ]);
        
        $course = Course::find($validated['course_id']);
        $myEntry = $request->user()->entries()->where('course_id', $course->id)->first();
        if ($myEntry && $myEntry->status === TimetableEntry::REJECTED) {
            return back()->withErrors(['course_id' => '却下された授業には申請し直せません'])->withInput();
        }
        if ($myEntry) {
            return back()->withErrors(['course_id' => 'すでに申請しています'])->withInput();
        }
        $exists = $request->user()->entries()
        ->where('status', '!=', TimetableEntry::REJECTED)
        ->whereHas('course', function ($query) use ($course) {
            $query->where('day', $course->day)
                ->where('period', $course->period);
        })->exists();

        if ($exists) {
            return back()->withErrors(['course_id' => 'その曜日・時限にはすでに授業が登録されています'])
                ->withInput();
        }

        $validated['user_id'] = $request->user()->id;

        try {
            TimetableEntry::create($validated);
        } catch (UniqueConstraintViolationException $e) {
            return back()->withErrors(['course_id' => 'すでに申請しています'])->withInput();
        }

        return redirect('/entries');
    }

    public function destroy($id)
    {
        $entry = TimetableEntry::findOrFail($id);
        Gate::authorize('delete', $entry);

        return DB::transaction(function () use ($entry) {
            // approve / reject と同じ授業の行をロックする
            Course::where('id', $entry->course_id)->lockForUpdate()->first();

            $entry->refresh();

            if ($entry->status !== TimetableEntry::PENDING) {
                return back()->withErrors(['entry' => '取り消せるのは申請中の授業だけです']);
            }

            $entry->delete();

            return redirect('/entries');
        });
    }
}
