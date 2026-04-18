<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
            $table->dropColumn('room_id');
        });

        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('rooms');
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // ...
    }
};
