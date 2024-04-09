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
        Schema::create('daily_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('presentation_id')->nullable()->index()->constrained();
            $table->string('adhoc_slug')->nullable()->index();
            $table->string('session_id')->index();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['presentation_id', 'session_id']);
            $table->index(['adhoc_slug', 'session_id']);
            $table->index(['presentation_id', 'adhoc_slug', 'session_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_views');
    }
};
