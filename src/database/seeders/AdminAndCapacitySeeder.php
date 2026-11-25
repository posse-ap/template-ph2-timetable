<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Course;
use Illuminate\Support\Facades\Hash;

class AdminAndCapacitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 管理者（あれば更新、なければ作る）
    User::updateOrCreate(
        ['email' => 'admin@example.com'],
        [
            'name' => '教務課',
            'password' => Hash::make('password123'),
            'is_admin' => true,
        ]
    );

    // ゼミの定員を3人にする
    Course::where('code', 'SE300')->update(['capacity' => 3]);    //
    }
}
