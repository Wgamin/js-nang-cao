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
        Schema::create('personal_access_tokens', function (Blueprint $box) {
            $box->id();
            $box->morphs('tokenable');
            $box->string('name');
            $box->string('token', 64)->unique();
            $box->text('abilities')->nullable();
            $box->timestamp('last_used_at')->nullable();
            $box->timestamp('expires_at')->nullable();
            $box->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
    }
};
