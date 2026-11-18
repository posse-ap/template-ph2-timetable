<?php

namespace Tests\Feature;

use App\Models\TimetableEntry;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TimetableOwnershipTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->string('teacher')->nullable();
            $table->integer('day');
            $table->integer('period');
            $table->string('room');
            $table->integer('credits');
            $table->boolean('required');
        });

        Schema::create('timetable_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id');
            $table->foreignId('user_id');
            $table->timestamps();
            $table->unique(['user_id', 'course_id']);
        });

        $this->insertCourse(1, 'CS101', 'プログラミング入門', 1, 1);
        $this->insertCourse(2, 'DB210', 'データベース論', 2, 1);
    }

    public function test_two_users_can_register_the_same_course(): void
    {
        [$firstUser, $secondUser] = $this->users();

        $this->actingAs($firstUser)
            ->post('/entries', ['course_id' => 1])
            ->assertRedirect('/entries');

        $this->actingAs($secondUser)
            ->post('/entries', ['course_id' => 1])
            ->assertRedirect('/entries');

        $this->assertDatabaseHas('timetable_entries', [
            'user_id' => $firstUser->id,
            'course_id' => 1,
        ]);
        $this->assertDatabaseHas('timetable_entries', [
            'user_id' => $secondUser->id,
            'course_id' => 1,
        ]);
    }

    public function test_entries_and_registered_status_are_limited_to_the_current_user(): void
    {
        [$firstUser, $secondUser] = $this->users();

        TimetableEntry::create(['user_id' => $firstUser->id, 'course_id' => 1]);
        TimetableEntry::create(['user_id' => $secondUser->id, 'course_id' => 2]);

        $this->actingAs($firstUser)
            ->get('/entries')
            ->assertOk()
            ->assertSee('プログラミング入門')
            ->assertDontSee('データベース論');

        $this->actingAs($firstUser)
            ->get('/courses')
            ->assertOk()
            ->assertSeeInOrder(['CS101', '登録済み', 'DB210', '登録']);
    }

    public function test_user_cannot_delete_another_users_entry(): void
    {
        [$firstUser, $secondUser] = $this->users();
        $entry = TimetableEntry::create([
            'user_id' => $firstUser->id,
            'course_id' => 1,
        ]);

        $this->actingAs($secondUser)
            ->delete('/entries/'.$entry->id)
            ->assertNotFound();

        $this->assertDatabaseHas('timetable_entries', ['id' => $entry->id]);
    }

    private function users(): array
    {
        return [
            User::create([
                'name' => 'ユーザー1',
                'email' => 'first@example.com',
                'password' => Hash::make('password123'),
            ]),
            User::create([
                'name' => 'ユーザー2',
                'email' => 'second@example.com',
                'password' => Hash::make('password123'),
            ]),
        ];
    }

    private function insertCourse(int $id, string $code, string $name, int $day, int $period): void
    {
        Schema::getConnection()->table('courses')->insert([
            'id' => $id,
            'code' => $code,
            'name' => $name,
            'teacher' => '担当教員',
            'day' => $day,
            'period' => $period,
            'room' => '教室',
            'credits' => 2,
            'required' => true,
        ]);
    }
}
