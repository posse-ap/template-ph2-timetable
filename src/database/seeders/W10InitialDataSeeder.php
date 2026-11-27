<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class W10InitialDataSeeder extends Seeder
{
    public function run(): void
    {
        // 同じメールアドレスの管理者がいれば更新し、重複作成しない
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => '教務課',
                'password' => Hash::make('password123'),
                'is_admin' => true,
            ]
        );

        // ゼミSE300の定員を3人にする
        DB::table('courses')
            ->where('code', 'SE300')
            ->update([
                'capacity' => 3
            ]);
    }
}
