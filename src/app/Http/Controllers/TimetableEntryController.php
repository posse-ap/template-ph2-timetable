<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\TimetableEntry;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TimetableEntryController extends Controller
{
    public function index()
    {
        $entries = TimetableEntry::query()
            ->select('timetable_entries.*')
            ->with('course')
            ->join('courses', 'courses.id', '=', 'timetable_entries.course_id')
            ->orderBy('courses.day')
            ->orderBy('courses.period')
            ->orderBy('courses.code')
            ->get();

        return view('entries.index', [
            'entries' => $entries,
        ]);
    }

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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => [
                'bail',
                'required',
                'integer',
                Rule::exists('courses', 'id'),
                Rule::unique('timetable_entries', 'course_id'),
            ],
        ], [
            'course_id.required' => '授業を一覧から選んでください',
            'course_id.integer' => '授業を一覧から選んでください',
            'course_id.exists' => '授業を一覧から選んでください',
            'course_id.unique' => 'この授業はすでに登録されています',
        ]);

        $course = Course::findOrFail($validated['course_id']);

        $sameTimeEntryExists = TimetableEntry::whereHas(
            'course',
            fn ($query) => $query
                ->where('day', $course->day)
                ->where('period', $course->period)
        )->exists();

        if ($sameTimeEntryExists) {
            throw ValidationException::withMessages([
                'course_id' => 'その曜日・時限にはすでに授業が登録されています',
            ]);
        }

        TimetableEntry::create($validated);

        return redirect('/entries')->with('success', '授業を登録しました');
    }

    public function destroy(int $id)
    {
        $entry = TimetableEntry::findOrFail($id);

        $entry->delete();

        return redirect('/entries')->with('success', '授業を削除しました');
    }
}
