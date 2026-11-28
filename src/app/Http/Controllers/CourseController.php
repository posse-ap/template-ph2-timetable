<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $tagId = $request->query('tag');

        $query = Course::query()
            ->with([
                'tags',
                'timetableEntries' => function ($builder) use ($request) {
                    $builder->where('user_id', $request->user()->id);
                },
            ])
            ->withCount('favoritedByUsers');

        if ($q !== '') {
            $escaped = str_replace(
                ['\\', '%', '_'],
                ['\\\\', '\\%', '\\_'],
                $q
            );
            $pattern = '%'.$escaped.'%';

            $query->where(function ($builder) use ($pattern) {
                $builder->where('name', 'ilike', $pattern)
                    ->orWhere('teacher', 'ilike', $pattern);
            });
        }

        if ($tagId !== null && $tagId !== '') {
            $request->validate([
                'tag' => ['integer', 'exists:tags,id'],
            ]);

            $query->whereHas('tags', function ($builder) use ($tagId) {
                $builder->where('tags.id', $tagId);
            });
        }

        $courses = $query
            ->orderBy('day')
            ->orderBy('period')
            ->orderBy('code')
            ->get();

        $favoriteIds = $request->user()
            ->favoriteCourses()
            ->pluck('courses.id')
            ->all();

        $recentAnnouncements = Announcement::query()
            ->whereIn(
                'course_id',
                $request->user()->entries()
                    ->where('status', 'approved')
                    ->select('course_id')
            )
            ->where('created_at', '>=', now()->subDay())
            ->with('course')
            ->latest()
            ->get();

        return view('courses.index', [
            'courses' => $courses,
            'favoriteIds' => $favoriteIds,
            'recentAnnouncements' => $recentAnnouncements,
        ]);
    }

    public function show(Course $course)
    {
        $course->load([
            'tags',
            'students' => function ($query) {
                $query->select('users.id', 'users.name');
            },
        ]);
        $course->loadCount('students');

        $reviews = $course->reviews()
            ->with('user')
            ->latest()
            ->get();

        $announcements = $course->announcements()
            ->where('created_at', '>=', now()->subDay())
            ->latest()
            ->get();

        return view('courses.show', [
            'course' => $course,
            'reviews' => $reviews,
            'announcements' => $announcements,
        ]);
    }

}
