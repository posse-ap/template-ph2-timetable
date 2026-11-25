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
        Schema::create('announcements', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();   // user_id：ユーザーが消えたらお知らせも消える
    $table->foreignId('course_id')->constrained();            // course_id
    $table->string('title');                               // 短い文字列
    $table->text('body');                                // 長い文
    $table->timestamp('published_at');                        // 発信日時。NULL は許さない
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
