<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AuthenticationTest extends TestCase
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
    }

    public function test_guest_is_redirected_to_login_from_protected_pages(): void
    {
        $this->get('/entries')->assertRedirect('/login');
        $this->get('/courses')->assertRedirect('/login');
    }

    public function test_user_can_register_and_is_logged_in(): void
    {
        $response = $this->post('/register', [
            'name' => '山田 太郎',
            'email' => 'taro@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/entries');
        $this->assertAuthenticated();

        $user = User::where('email', 'taro@example.com')->firstOrFail();
        $this->assertTrue(Hash::check('password123', $user->password));
        $this->assertNotSame('password123', $user->password);
    }

    public function test_registration_uses_the_specified_japanese_messages(): void
    {
        $this->from('/register')->post('/register', [
            'name' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => '',
        ])->assertRedirect('/register')->assertSessionHasErrors([
            'name' => '名前を入力してください',
            'email' => 'メールアドレスを入力してください',
            'password' => 'パスワードは8文字以上で入力してください',
        ]);

        User::create([
            'name' => '登録済み',
            'email' => 'used@example.com',
            'password' => Hash::make('password123'),
        ]);

        $this->from('/register')->post('/register', [
            'name' => '山田 太郎',
            'email' => 'used@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSessionHasErrors([
            'email' => 'このメールアドレスはすでに登録されています',
        ]);

        $this->from('/register')->post('/register', [
            'name' => '山田 太郎',
            'email' => 'invalid-email',
            'password' => 'short',
            'password_confirmation' => 'different',
        ])->assertSessionHasErrors([
            'email' => 'メールアドレスの形式で入力してください',
            'password' => 'パスワードは8文字以上で入力してください',
        ]);

        $this->from('/register')->post('/register', [
            'name' => '山田 太郎',
            'email' => 'new@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different-password',
        ])->assertSessionHasErrors([
            'password' => 'パスワードが確認用と一致しません',
        ]);
    }

    public function test_login_failure_keeps_email_and_successful_login_regenerates_session(): void
    {
        $user = User::create([
            'name' => '山田 太郎',
            'email' => 'taro@example.com',
            'password' => Hash::make('correct-password'),
        ]);

        $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertRedirect('/login')
            ->assertSessionHasErrors([
                'email' => 'メールアドレスかパスワードが違います',
            ])
            ->assertSessionHasInput('email', $user->email);

        $oldSessionId = session()->getId();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'correct-password',
        ])->assertRedirect('/entries');

        $this->assertAuthenticatedAs($user);
        $this->assertNotSame($oldSessionId, session()->getId());
    }

    public function test_user_can_logout(): void
    {
        $user = User::create([
            'name' => '山田 太郎',
            'email' => 'taro@example.com',
            'password' => Hash::make('password123'),
        ]);

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect('/login');

        $this->assertGuest();
    }
}
