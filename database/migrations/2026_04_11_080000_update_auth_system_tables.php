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
        // Cập nhật enum role cho users
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'staff', 'teacher', 'student', 'parent'])->default('staff')->change();
        });

        // Thêm user_id vào teachers
        Schema::table('teachers', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });

        // Thêm user_id vào guardians
        Schema::table('guardians', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });

        // Thêm user_id vào students
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });

        Schema::table('guardians', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });

        Schema::table('teachers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'staff', 'teacher'])->default('staff')->change();
        });
    }
};
