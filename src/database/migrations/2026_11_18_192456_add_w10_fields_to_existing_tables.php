<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ① 時間割に申請状態を追加
        Schema::table('timetable_entries', function (Blueprint $table) {
            $table->string('status')->default('pending');
            $table->timestamp('decided_at')->nullable();
        });

        // ② 既存のW9の履修は承認済みにする
        DB::table('timetable_entries')->update([
            'status' => 'approved',
            'decided_at' => now(),
        ]);

        // ③ 授業に定員を追加
        Schema::table('courses', function (Blueprint $table) {
            $table->integer('capacity')->default(40);
        });

        // ④ 管理者かどうかを追加
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('timetable_entries', function (Blueprint $table) {
            $table->dropColumn(['status', 'decided_at']);
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('capacity');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};
