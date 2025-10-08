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
        Schema::create('presentation_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('presentation_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->string('invite_status')->default('pending');
            $table->datetime('invited_at')->useCurrent();
            $table->datetime('accepted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presentation_user');
    }
};
