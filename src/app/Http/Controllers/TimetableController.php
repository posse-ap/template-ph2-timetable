<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class TimetableController extends Controller
{
   public function index ()
    {

   $courses = DB::select('
    SELECT * FROM courses
    ORDER BY day, period
');

return view('timetable', [
    'courses' => $courses,
]);
    }
}
