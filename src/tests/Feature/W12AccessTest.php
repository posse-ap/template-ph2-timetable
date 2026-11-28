<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class W12AccessTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('is_admin')->default(false);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('teacher')->nullable();
            $table->integer('day');
            $table->integer('period');
            $table->string('room')->nullable();
            $table->integer('credits');
            $table->boolean('required');
            $table->integer('capacity')->default(40);
        });

        Schema::create('timetable_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id');
            $table->foreignId('user_id');
            $table->string('status')->default('pending');
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();
        });

        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id');
            $table->string('title', 100);
            $table->text('body');
            $table->timestamps();
        });
    }

    public function test_tests_use_an_isolated_in_memory_database(): void
    {
        $this->assertSame('testing', app()->environment());
        $this->assertSame('sqlite', DB::connection()->getDriverName());
        $this->assertSame(':memory:', DB::connection()->getDatabaseName());
    }

    public function test_guest_cannot_open_timetable(): void
    {
        $this->get('/entries')->assertRedirect('/login');
    }

    public function test_student_cannot_open_admin_page(): void
    {
        $student = $this->user('student@example.com');

        $this->actingAs($student)
            ->get('/admin/entries')
            ->assertForbidden();
    }

    public function test_admin_can_open_admin_page(): void
    {
        $admin = $this->user('admin@example.com', true);

        $this->actingAs($admin)
            ->get('/admin/entries')
            ->assertOk();
    }

    public function test_student_cannot_post_announcement(): void
    {
        Mail::fake();

        $student = $this->user('student@example.com');
        $course = $this->course();

        $this->actingAs($student)
            ->post('/courses/'.$course->id.'/announcements', [
                'title' => 'テストのお知らせ',
                'body' => '学生は投稿できない',
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('announcements', 0);
        Mail::assertNothingSent();
    }

    private function user(string $email, bool $isAdmin = false): User
    {
        $user = User::create([
            'name' => 'テストユーザー',
            'email' => $email,
            'password' => 'password123',
        ]);
        $user->is_admin = $isAdmin;
        $user->save();

        return $user;
    }

    private function course(): Course
    {
        $id = DB::table('courses')->insertGetId([
            'code' => 'CS101',
            'name' => 'プログラミング入門',
            'teacher' => '担当教員',
            'day' => 1,
            'period' => 1,
            'room' => '教室',
            'credits' => 2,
            'required' => true,
            'capacity' => 40,
        ]);

        return Course::findOrFail($id);
    }
}
