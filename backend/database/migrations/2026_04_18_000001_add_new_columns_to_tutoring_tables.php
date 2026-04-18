<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Thêm cột mới vào bảng users
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('password');
            $table->text('address')->nullable()->after('phone');
        });

        // Thêm cột mới vào bảng classes
        Schema::table('classes', function (Blueprint $table) {
            $table->text('description')->nullable()->after('teacher_id');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('description');
        });

        // Thêm cột mới vào bảng schedules
        Schema::table('schedules', function (Blueprint $table) {
            $table->dateTime('end_time')->nullable()->after('start_time');
            $table->string('note')->nullable()->after('room');
        });

        // Thêm cột mới vào bảng attendances
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('note')->nullable()->after('is_present');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'address']);
        });
        Schema::table('classes', function (Blueprint $table) {
            $table->dropColumn(['description', 'status']);
        });
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn(['end_time', 'note']);
        });
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('note');
        });
    }
};
