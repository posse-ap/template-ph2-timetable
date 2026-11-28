<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class W10InitialDataSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => '教務課',
                'password' => Hash::make('password123'),
                'is_admin' => true,
            ]
        );

        DB::table('courses')
            ->where('code', 'SE300')
            ->update(['capacity' => 3]);
    }
}
