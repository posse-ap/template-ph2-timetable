<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $courses = Course::query()
            ->orderBy('day')
            ->orderBy('period')
            ->orderBy('code')
            ->get();

        $registeredIds = $request->user()
            ->entries()
            ->pluck('course_id')
            ->all();

        return view('courses.index', [
            'courses' => $courses,
            'registeredIds' => $registeredIds,
        ]);
    }
}
