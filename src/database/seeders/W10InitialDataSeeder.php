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
        // 管理者がいなければ作成、いれば更新
        $admin = User::firstOrNew([
            'email' => 'admin@example.com'
        ]);

        $admin->name = '教務課';
        $admin->password = Hash::make('password123');
        $admin->is_admin = true;
        $admin->save();

        // ゼミSE300の定員を3人にする
        DB::table('courses')
            ->where('code', 'SE300')
            ->update([
                'capacity' => 3
            ]);
    }
}