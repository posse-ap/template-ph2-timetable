<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('favorites', function (Blueprint $table) {
        $table->foreignId('user_id')
            ->constrained('users')
            ->cascadeOnDelete();

        $table->foreignId('course_id')
            ->constrained('courses')
            ->cascadeOnDelete();

        // 同じユーザーが同じ授業を二重登録できない
        $table->primary(['user_id', 'course_id']);
    });
}

public function down(): void
{
    Schema::dropIfExists('favorites');
}
};
