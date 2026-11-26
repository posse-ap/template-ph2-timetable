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
        Schema::table('timetable_entries', function (Blueprint $table) {
            //
            $table->dropUnique(['day', 'period']);
            $table->dropColumn(['day', 'period', 'title', 'room']);
            
            $table->foreignId('course_id')
                ->unique()
                ->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('timetable_entries', function (Blueprint $table) {
            //
            $table->dropForeign(['course_id']);
            $table->dropColumn('course_id');
            $table->unsignedTinyInteger('day');
            $table->unsignedTinyInteger('period');
            $table->string('title');
            $table->string('room')->nullable();
            $table->unique(['day', 'period']);
        });
    }
};
