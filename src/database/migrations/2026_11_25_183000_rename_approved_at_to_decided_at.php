<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 承認だけでなく却下にも使うので、列の名前を approved_at から decided_at に変える。
     */
    public function up(): void
    {
        Schema::table('timetable_entries', function (Blueprint $table) {
            $table->renameColumn('approved_at', 'decided_at');
        });
    }

    public function down(): void
    {
        Schema::table('timetable_entries', function (Blueprint $table) {
            $table->renameColumn('decided_at', 'approved_at');
        });
    }
};
