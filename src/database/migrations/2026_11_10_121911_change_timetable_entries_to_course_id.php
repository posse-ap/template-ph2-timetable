<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('timetable_entries', function (Blueprint $table) {

            // 曜日・時限の重複禁止を解除
            $table->dropUnique(['day', 'period']);

            // Week5までのカラムを削除
            $table->dropColumn([
                'day',
                'period',
                'title',
                'room',
            ]);

            // coursesテーブルと関連付ける
            $table->foreignId('course_id')
                ->unique()
                ->constrained('courses');
        });
    }

    public function down(): void
    {
        Schema::table('timetable_entries', function (Blueprint $table) {

            // 外部キー制約とUNIQUE制約を削除
            $table->dropForeign(['course_id']);
            $table->dropUnique(['course_id']);

            // course_idを削除
            $table->dropColumn('course_id');

            // Week5までのカラムを復元
            $table->integer('day');
            $table->integer('period');
            $table->string('title');
            $table->string('room')->nullable();

            $table->unique(['day', 'period']);
        });
    }
};
