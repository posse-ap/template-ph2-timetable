<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // user_idがない場合だけ追加する
        if (!Schema::hasColumn('timetable_entries', 'user_id')) {

            // 既存データにユーザーを勝手に割り当てない
            if (DB::table('timetable_entries')->exists()) {
                throw new RuntimeException(
                    '既存の履修データがあります。user_idの移行方法を先に確認してください。'
                );
            }

            Schema::table('timetable_entries', function (Blueprint $table) {
                $table->foreignId('user_id')
                    ->constrained('users')
                    ->cascadeOnDelete();
            });
        }

        // 古いcourse_id単独のUNIQUEを削除する
        if (Schema::hasIndex('timetable_entries', 'timetable_entries_course_id_unique')) {
            Schema::table('timetable_entries', function (Blueprint $table) {
                $table->dropUnique('timetable_entries_course_id_unique');
            });
        }

        // 同じ学生による同じ授業の二重登録を防止する
        if (!Schema::hasIndex('timetable_entries', 'timetable_entries_user_id_course_id_unique')) {
            Schema::table('timetable_entries', function (Blueprint $table) {
                $table->unique(['user_id', 'course_id']);
            });
        }
    }

    public function down(): void
    {
        // 既存データ保護のため、自動的な制約の巻き戻しは行わない
    }
};