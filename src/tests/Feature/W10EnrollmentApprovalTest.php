<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\TimetableEntry;
use App\Models\User;
use Database\Seeders\W10InitialDataSeeder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class W10EnrollmentApprovalTest extends TestCase
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
    }

    public function test_non_admin_cannot_open_or_post_to_admin_approval_routes(): void
    {
        $student = $this->user();
        $course = $this->course();
        $entry = $this->entry($student, $course);

        $this->actingAs($student)
            ->get('/admin/entries')
            ->assertForbidden();

        $this->actingAs($student)
            ->post('/admin/entries/'.$entry->id.'/approve')
            ->assertForbidden();

        $this->assertDatabaseHas('timetable_entries', [
            'id' => $entry->id,
            'status' => 'pending',
        ]);
    }

    public function test_only_pending_entries_can_change_state(): void
    {
        $admin = $this->user(true);
        $student = $this->user();
        $course = $this->course();

        $rejected = $this->entry($student, $course);
        $this->actingAs($admin)
            ->post('/admin/entries/'.$rejected->id.'/reject')
            ->assertRedirect();

        $this->actingAs($admin)
            ->from('/admin/entries')
            ->post('/admin/entries/'.$rejected->id.'/approve')
            ->assertRedirect('/admin/entries')
            ->assertSessionHasErrors([
                'entry' => '申請中の授業だけ承認できます。',
            ]);

        $approvedCourse = $this->course('DB210', 2);
        $approved = $this->entry($student, $approvedCourse, 'approved');

        $this->actingAs($student)
            ->from('/entries')
            ->delete('/entries/'.$approved->id)
            ->assertRedirect('/entries')
            ->assertSessionHasErrors([
                'entry' => '申請中の授業だけ取り消せます。',
            ]);

        $this->actingAs($student)
            ->from('/courses')
            ->post('/entries', ['course_id' => $course->id])
            ->assertRedirect('/courses')
            ->assertSessionHasErrors('course_id');
    }

    public function test_capacity_is_never_exceeded(): void
    {
        $admin = $this->user(true);
        $course = $this->course('SE300', 3);

        for ($i = 0; $i < 3; $i++) {
            $this->entry($this->user(), $course, 'approved');
        }

        $pending = $this->entry($this->user(), $course);

        $this->actingAs($admin)
            ->from('/admin/entries')
            ->post('/admin/entries/'.$pending->id.'/approve')
            ->assertRedirect('/admin/entries')
            ->assertSessionHasErrors([
                'entry' => '定員に達しているため承認できません。',
            ]);

        $this->assertSame(
            3,
            TimetableEntry::where('course_id', $course->id)
                ->where('status', 'approved')
                ->count()
        );
        $this->assertDatabaseHas('timetable_entries', [
            'id' => $pending->id,
            'status' => 'pending',
        ]);
    }

    public function test_admin_can_approve_when_a_seat_is_available(): void
    {
        $admin = $this->user(true);
        $course = $this->course('SE300', 3);

        $this->entry($this->user(), $course, 'approved');
        $this->entry($this->user(), $course, 'approved');
        $pending = $this->entry($this->user(), $course);

        $this->actingAs($admin)
            ->post('/admin/entries/'.$pending->id.'/approve')
            ->assertRedirect()
            ->assertSessionHas('success', '承認しました。');

        $pending->refresh();
        $this->assertSame('approved', $pending->status);
        $this->assertNotNull($pending->decided_at);
    }

    public function test_w10_seeder_is_idempotent(): void
    {
        $this->course('SE300', 40);

        $this->seed(W10InitialDataSeeder::class);
        $this->seed(W10InitialDataSeeder::class);

        $this->assertSame(
            1,
            User::where('email', 'admin@example.com')->count()
        );
        $this->assertTrue(
            User::where('email', 'admin@example.com')->firstOrFail()->is_admin
        );
        $this->assertDatabaseHas('courses', [
            'code' => 'SE300',
            'capacity' => 3,
        ]);
    }

    private function user(bool $isAdmin = false): User
    {
        static $number = 0;
        $number++;

        $user = User::create([
            'name' => 'ユーザー'.$number,
            'email' => 'user'.$number.'@example.com',
            'password' => Hash::make('password123'),
        ]);
        $user->is_admin = $isAdmin;
        $user->save();

        return $user;
    }

    private function course(string $code = 'CS101', int $capacity = 40): Course
    {
        DB::table('courses')->insert([
            'code' => $code,
            'name' => $code.' 授業',
            'teacher' => '担当教員',
            'day' => 1,
            'period' => DB::table('courses')->count() + 1,
            'room' => '教室',
            'credits' => 2,
            'required' => true,
            'capacity' => $capacity,
        ]);

        return Course::where('code', $code)->firstOrFail();
    }

    private function entry(
        User $user,
        Course $course,
        string $status = 'pending'
    ): TimetableEntry {
        return TimetableEntry::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => $status,
        ]);
    }
}
