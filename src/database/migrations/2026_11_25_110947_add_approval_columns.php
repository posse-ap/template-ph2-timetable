<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('timetable_entries', function (Blueprint $table) {
        $table->string('status')->default('pending');   // 申請の状態
        $table->timestamp('approved_at')->nullable();           // 決まった日時
    });

    Schema::table('courses', function (Blueprint $table) {
        $table->integer('capacity')->default(40);            // 定員
    });

    Schema::table('users', function (Blueprint $table) {
        $table->boolean('is_admin')->default(false);         // 管理者かどうか
    });

    // W9までの行を承認済みにする（ここを忘れない）
    DB::table('timetable_entries')->update([ 'status' => 'approved', 'approved_at' => now() ]);
}

public function down(): void
{
    Schema::table('timetable_entries', function (Blueprint $table) {
        $table->dropColumn([ 'status', 'approved_at' ]);
    });
    // courses と users も同じ形で、足した列を消す
    Schema::table('courses', function (Blueprint $table) {
        $table->dropColumn('capacity');
    });
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('is_admin');
    });
}
};
