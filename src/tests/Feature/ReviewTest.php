<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ReviewTest extends TestCase
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

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('course_id');
            $table->text('body');
            $table->timestamps();
        });
    }

    public function test_other_user_cannot_delete_a_review(): void
    {
        $author = $this->user('author@example.com');
        $other = $this->user('other@example.com');
        $course = $this->course();
        $review = $this->review($author, $course, '板書が見やすい');

        $this->actingAs($other)
            ->delete('/reviews/'.$review->id)
            ->assertForbidden();

        $this->assertDatabaseHas('reviews', ['id' => $review->id]);
    }

    public function test_author_can_delete_a_review(): void
    {
        $author = $this->user('author@example.com');
        $course = $this->course();
        $review = $this->review($author, $course, '授業が分かりやすかった');

        $this->actingAs($author)
            ->delete('/reviews/'.$review->id)
            ->assertRedirect('/courses/'.$course->id);

        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
    }

    public function test_empty_review_is_not_saved(): void
    {
        $student = $this->user('student@example.com');
        $course = $this->course();

        $this->actingAs($student)
            ->from('/courses/'.$course->id)
            ->post('/courses/'.$course->id.'/reviews', ['body' => ''])
            ->assertRedirect('/courses/'.$course->id)
            ->assertSessionHasErrors('body');

        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_guest_cannot_post_a_review(): void
    {
        $course = $this->course();

        $this->post('/courses/'.$course->id.'/reviews', [
            'body' => '未ログインの投稿',
        ])->assertRedirect('/login');

        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_review_with_1000_characters_is_saved(): void
    {
        $student = $this->user('student@example.com');
        $course = $this->course();
        $body = str_repeat('あ', 1000);

        $this->actingAs($student)
            ->post('/courses/'.$course->id.'/reviews', ['body' => $body])
            ->assertRedirect('/courses/'.$course->id);

        $this->assertDatabaseHas('reviews', [
            'user_id' => $student->id,
            'course_id' => $course->id,
            'body' => $body,
        ]);
    }

    public function test_review_with_1001_characters_is_not_saved(): void
    {
        $student = $this->user('student@example.com');
        $course = $this->course();

        $this->actingAs($student)
            ->from('/courses/'.$course->id)
            ->post('/courses/'.$course->id.'/reviews', [
                'body' => str_repeat('あ', 1001),
            ])
            ->assertRedirect('/courses/'.$course->id)
            ->assertSessionHasErrors('body');

        $this->assertDatabaseCount('reviews', 0);
    }

    private function user(string $email): User
    {
        return User::create([
            'name' => 'テストユーザー',
            'email' => $email,
            'password' => 'password123',
        ]);
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

    private function review(User $user, Course $course, string $body): Review
    {
        return $user->reviews()->create([
            'course_id' => $course->id,
            'body' => $body,
        ]);
    }
}
