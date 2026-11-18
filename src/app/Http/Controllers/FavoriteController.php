<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FavoriteController extends Controller
{
    // ☆ 気になる授業に追加する
    public function store(Request $request, Course $course)
    {
        // 同じ組がすでにあれば重複追加しない
        DB::table('favorites')->insertOrIgnore([
            'user_id' => $request->user()->id,
            'course_id' => $course->id,
        ]);

        return back();
    }

    // ★ 気になる授業から解除する
    public function destroy(Request $request, Course $course)
    {
        $request->user()
            ->favoriteCourses()
            ->detach($course->id);

        return back();
    }
}