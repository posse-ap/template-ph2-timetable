<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class TimetableController extends Controller
{
    public function index()
    {
        // 区画1：月曜日の授業
        $mondayCourses = DB::select('
            SELECT period, name, teacher, room
            FROM courses
            WHERE day = 1
            ORDER BY period ASC, id ASC
        ');

        // 区画2：必修科目
        $requiredCourses = DB::select('
            SELECT code, required, name, credits
            FROM courses
            WHERE required = true
            ORDER BY code ASC
        ');

        // ビューにデータを渡す
        return view('timetable', [
            'mondayCourses' => $mondayCourses,
            'requiredCourses' => $requiredCourses,
        ]);
    }
}