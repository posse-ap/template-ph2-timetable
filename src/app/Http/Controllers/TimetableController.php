<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class TimetableController extends Controller
{
    public function index()
    {
        // 月曜日（day = 1）の授業を、時限の順に取り出す
        $courses = DB::table('courses')
            ->select('period', 'name', 'teacher', 'room')
            ->where('day', 1)
            ->orderBy('period')
            ->get();

        // 必修科目を、授業コードの順に取り出す
        $requiredCourses = DB::table('courses')
            ->select('name', 'credits')
            ->where('required', true)
            ->orderBy('code')
            ->get();

        return view('timetable', [
            'courses' => $courses,
            'requiredCourses' => $requiredCourses,
        ]);
    }
}
