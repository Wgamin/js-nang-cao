<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Thêm parent_id vào bảng users
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('parent_id')
                ->nullable()
                ->after('address')
                ->constrained('users')
                ->nullOnDelete();
        });

        // 2. Thêm cột status vào attendances (thay thế is_present)
        Schema::table('attendances', function (Blueprint $table) {
            $table->enum('status', ['present', 'absent', 'late'])
                ->default('absent')
                ->after('is_present');
        });

        // 3. Tạo bảng tuitions (Quản lý học phí)
        Schema::create('tuitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('Học sinh');
            $table->foreignId('study_class_id')
                ->constrained('classes')
                ->onDelete('cascade')
                ->comment('Lớp học');
            $table->decimal('amount', 12, 0)->default(0)->comment('Số tiền phải đóng');
            $table->decimal('amount_paid', 12, 0)->default(0)->comment('Số tiền đã đóng');
            $table->enum('status', ['unpaid', 'partially_paid', 'paid'])->default('unpaid');
            $table->date('payment_date')->nullable()->comment('Ngày đóng tiền');
            $table->text('note')->nullable()->comment('Ghi chú: tiền mặt, chuyển khoản...');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tuitions');
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });
    }
};
