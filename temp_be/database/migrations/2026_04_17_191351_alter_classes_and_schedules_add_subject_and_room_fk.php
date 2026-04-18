<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->nullOnDelete();
        });

        Schema::table('schedules', function (Blueprint $table) {
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
            $table->dropColumn('room_id');
        });

        Schema::table('classes', function (Blueprint $table) {
            $table->dropForeign(['subject_id']);
            $table->dropColumn('subject_id');
        });
    }
};
