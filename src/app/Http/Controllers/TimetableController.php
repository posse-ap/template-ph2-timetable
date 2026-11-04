<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class TimetableController extends Controller
{
    public function index()
    {
        // 区画1：月曜日の授業（w01.sql の SQL をコピペ）
        $mondayCourses = DB::select("
            SELECT period, name, teacher, room FROM courses
            WHERE day = 1
            ORDER BY period
        ");

        // 区画2：必修科目（w01.sql の SQL をコピペ）
        $requiredCourses = DB::select("
            SELECT name, credits FROM courses
            WHERE required = true
            ORDER BY code
        ");

        return view('timetable', [
            'mondayCourses' => $mondayCourses,
            'requiredCourses' => $requiredCourses,
        ]);
    }
}
