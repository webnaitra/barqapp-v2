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
        Schema::create('article_fetch_logs', function (Blueprint $table) {
            $table->id();
            $table->string('batch_id')->index();
            $table->string('source_name')->nullable();
            $table->text('feed_url')->nullable();
            $table->text('title')->nullable();
            $table->text('link')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_fetch_logs');
    }
};
