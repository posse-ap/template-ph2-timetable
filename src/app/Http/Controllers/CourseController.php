<?php

namespace App\Http\Controllers;

use App\Models\Course;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::query()
            ->with('timetableEntries')
            ->orderBy('day')
            ->orderBy('period')
            ->orderBy('code')
            ->get();

        return view('courses.index', [
            'courses' => $courses,
        ]);
    }
}
