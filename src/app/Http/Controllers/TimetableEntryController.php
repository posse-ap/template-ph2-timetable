<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\TimetableEntry;
use Illuminate\Http\Request;

class TimetableEntryController extends Controller
{
public function index()
    {
        $entries = TimetableEntry::with('course')
            ->get()
            ->sortBy(function (TimetableEntry $entry) {
                return [
                    $entry->course->day,
                    $entry->course->period,
                ];
            })
            ->values();
			$requiredClasses = collect();

        return view('timetable', [
            'entries' => $entries,
			'requiredClasses' => $requiredClasses,
        ]);
    }
	public function create()
	{
		$courses = Course::orderBy('day')
        ->orderBy('period')
        ->get();
		return view('entries.create', [
        'courses' => $courses,
    ]);
	}

	public function store(Request $request)
	{
		$validated = $request->validate([
			'course_id' => ['required', 'integer', 'exists:courses,id'],
		],[
			'course_id.required' => '授業を一覧から選んでください。',
			'course_id.integer' => '授業を一覧から選んでください。',
			'course_id.exists' => '授業を一覧から選んでください。',
		]);

		$course = Course::findOrFail($validated['course_id']);

    $hasConflict = TimetableEntry::whereHas('course', function ($query) use ($course) {
        $query->where('day', $course->day)
            ->where('period', $course->period);
    })->exists();

    if ($hasConflict) {
        return back()
            ->withInput()
            ->withErrors([
                'course_id' => '同じ曜日・時限には、すでに授業が登録されています。',
            ]);
	}

		TimetableEntry::create([
        'course_id' => $validated['course_id'],
    ]);


		return redirect('/entries');
    }

    public function destroy(int $id)
    {
        $entry = TimetableEntry::findOrFail($id);
        $entry->delete();

        return redirect('/entries');
    }
}
