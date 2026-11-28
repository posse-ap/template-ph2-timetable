<?php

namespace Tests\Feature;

use App\Mail\AnnouncementMail;
use App\Models\Announcement;
use App\Models\Course;
use App\Models\TimetableEntry;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class W11AnnouncementTest extends TestCase
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
            $table->unique(['user_id', 'course_id']);
        });

        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id');
            $table->string('title', 100);
            $table->text('body');
            $table->timestamps();
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('course_tag', function (Blueprint $table) {
            $table->foreignId('course_id');
            $table->foreignId('tag_id');
            $table->primary(['course_id', 'tag_id']);
        });

        Schema::create('favorites', function (Blueprint $table) {
            $table->foreignId('user_id');
            $table->foreignId('course_id');
            $table->primary(['user_id', 'course_id']);
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('course_id');
            $table->text('body');
            $table->timestamps();
        });
    }

    public function test_announcement_is_mailed_only_to_approved_students_of_the_course(): void
    {
        Mail::fake();

        $admin = $this->user('教務課', 'admin@example.com', true);
        $approved = $this->user('承認済み', 'approved@example.com');
        $pending = $this->user('申請中', 'pending@example.com');
        $rejected = $this->user('却下済み', 'rejected@example.com');
        $other = $this->user('別授業', 'other@example.com');
        $course = $this->course('CS101', 'プログラミング入門', 1);
        $otherCourse = $this->course('DB210', 'データベース論', 2);

        $this->entry($approved, $course, 'approved');
        $this->entry($pending, $course, 'pending');
        $this->entry($rejected, $course, 'rejected');
        $this->entry($other, $otherCourse, 'approved');

        $this->actingAs($admin)
            ->post('/courses/'.$course->id.'/announcements', [
                'title' => '休講のお知らせ',
                'body' => '次回の授業は休講です。',
            ])
            ->assertRedirect('/courses/'.$course->id);

        $this->assertDatabaseHas('announcements', [
            'course_id' => $course->id,
            'title' => '休講のお知らせ',
        ]);

        Mail::assertSent(AnnouncementMail::class, 1);
        Mail::assertSent(
            AnnouncementMail::class,
            fn (AnnouncementMail $mail) => $mail->hasTo('approved@example.com')
        );
        Mail::assertNotSent(
            AnnouncementMail::class,
            fn (AnnouncementMail $mail) => $mail->hasTo('pending@example.com')
                || $mail->hasTo('rejected@example.com')
                || $mail->hasTo('other@example.com')
        );
    }

    public function test_course_page_shows_its_recent_announcements_newest_first(): void
    {
        $student = $this->user('学生', 'student@example.com');
        $course = $this->course('CS101', 'プログラミング入門', 1);
        $otherCourse = $this->course('DB210', 'データベース論', 2);

        $old = $this->announcement($course, '古いお知らせ', now()->subHours(2));
        $new = $this->announcement($course, '新しいお知らせ', now()->subHour());
        $this->announcement($otherCourse, '別授業のお知らせ', now());
        $this->announcement($course, '期限切れのお知らせ', now()->subDays(2));

        $this->actingAs($student)
            ->get('/courses/'.$course->id)
            ->assertOk()
            ->assertSeeInOrder([$new->title, $old->title])
            ->assertDontSee('別授業のお知らせ')
            ->assertDontSee('期限切れのお知らせ');
    }

    public function test_timetable_shows_recent_announcements_for_approved_courses_only(): void
    {
        $student = $this->user('学生', 'student@example.com');
        $approvedCourse = $this->course('CS101', '承認済み授業', 1);
        $pendingCourse = $this->course('DB210', '申請中授業', 2);
        $otherCourse = $this->course('EN101', '未登録授業', 3);

        $this->entry($student, $approvedCourse, 'approved');
        $this->entry($student, $pendingCourse, 'pending');
        $this->announcement($approvedCourse, '表示されるお知らせ', now());
        $this->announcement($pendingCourse, '申請中のお知らせ', now());
        $this->announcement($otherCourse, '別授業のお知らせ', now());

        $this->actingAs($student)
            ->get('/entries')
            ->assertOk()
            ->assertSee('表示されるお知らせ')
            ->assertDontSee('申請中のお知らせ')
            ->assertDontSee('別授業のお知らせ');
    }

    public function test_student_cannot_post_an_announcement(): void
    {
        Mail::fake();

        $student = $this->user('学生', 'student@example.com');
        $course = $this->course('CS101', 'プログラミング入門', 1);

        $this->actingAs($student)
            ->post('/courses/'.$course->id.'/announcements', [
                'title' => '不正なお知らせ',
                'body' => '保存されない本文',
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('announcements', 0);
        Mail::assertNothingSent();
    }

    public function test_course_index_receives_recent_announcements(): void
    {
        $student = $this->user('学生', 'student@example.com');
        $course = $this->course('CS101', 'プログラミング入門', 1);
        $this->entry($student, $course, 'approved');
        $this->announcement($course, '一覧のお知らせ', now());

        $this->actingAs($student)
            ->get('/courses')
            ->assertOk()
            ->assertSee('一覧のお知らせ');
    }

    private function user(string $name, string $email, bool $isAdmin = false): User
    {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => 'password123',
        ]);
        $user->is_admin = $isAdmin;
        $user->save();

        return $user;
    }

    private function course(string $code, string $name, int $period): Course
    {
        $id = DB::table('courses')->insertGetId([
            'code' => $code,
            'name' => $name,
            'teacher' => '担当教員',
            'day' => 1,
            'period' => $period,
            'room' => '教室',
            'credits' => 2,
            'required' => true,
            'capacity' => 40,
        ]);

        return Course::findOrFail($id);
    }

    private function entry(User $user, Course $course, string $status): TimetableEntry
    {
        return TimetableEntry::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => $status,
        ]);
    }

    private function announcement(Course $course, string $title, $createdAt): Announcement
    {
        $announcement = Announcement::create([
            'course_id' => $course->id,
            'title' => $title,
            'body' => $title.'の本文',
        ]);
        $announcement->created_at = $createdAt;
        $announcement->updated_at = $createdAt;
        $announcement->save();

        return $announcement;
    }
}
