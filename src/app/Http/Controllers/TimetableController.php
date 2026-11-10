<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class TimetableController extends Controller
{
    public function index()
    {
        // 区画1：月曜日の授業
        $mondayCourses = DB::select("
            SELECT
                c.period,
                c.name,
                c.teacher AS teacher_name,
                c.room
            FROM courses c
            WHERE c.day = 1
            ORDER BY c.period, c.code
        ");

        // 区画2：必修科目
        $requiredCourses = DB::select("
            SELECT
                c.name,
                c.credits
            FROM courses c
            WHERE c.required = TRUE
            ORDER BY c.code
        ");

        return view('timetable', [
            'mondayCourses' => $mondayCourses,
            'requiredCourses' => $requiredCourses,
        ]);
    }
}

