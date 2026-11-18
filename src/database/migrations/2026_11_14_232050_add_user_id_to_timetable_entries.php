<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('timetable_entries', function (Blueprint $table) {

            // 以前のUNIQUE制約を削除
            $table->dropUnique(['course_id']);

            // ユーザーIDを追加
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // 同じユーザーによる同じ授業の二重登録を禁止
            $table->unique(['user_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::table('timetable_entries', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'course_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');

            $table->unique('course_id');
        });
    }
};