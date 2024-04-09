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
        Schema::create('aggregate_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('presentation_id')->nullable()->index()->constrained();
            $table->string('adhoc_slug')->nullable()->index();
            $table->integer('total_count')->index()->default(0);
            $table->integer('unique_count')->index()->default(0);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aggregate_views');
    }
};
