<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
class TimetableController extends Controller
{
    public function index()
    {
        // 区画1：月曜日の授業
        $courses1 = DB::select("
            SELECT period, name, teacher, room
            FROM courses
            WHERE day = 1
            ORDER BY period ASC, id ASC
        ");

        // 区画2：必修科目
        $courses2 = DB::select("
            SELECT name, credits
            FROM courses
            WHERE required = true
            ORDER BY code ASC
        ");

        return view('timetable', [
            'courses1' => $courses1,
            'courses2' => $courses2
        ]);
    }
}




