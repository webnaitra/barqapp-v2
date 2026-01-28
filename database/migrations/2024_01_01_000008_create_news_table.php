<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('news')) {
            Schema::create('news', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('slug', 200)->nullable();
                $table->longText('content')->nullable();
                $table->string('date')->nullable();
                $table->string('image')->nullable();
                $table->foreignId('category_id')->nullable()->constrained('categories')->cascadeOnDelete();
                $table->foreignId('sourcefeed_id')->nullable()->constrained('source_feeds')->cascadeOnDelete();
                $table->integer('views')->default(0);
                $table->integer('shares')->default(0);
                $table->integer('likes')->default(0);
                $table->integer('urgent')->default(0);
                $table->string('video')->nullable();
                $table->foreignId('source_id')->nullable()->constrained('sources')->cascadeOnDelete();
                $table->string('source_link')->nullable();
                $table->boolean('user_id')->default(1);
                $table->string('excerpt', 191)->default('0');
                $table->boolean('run_cron')->default(0);
                $table->boolean('is_updated')->default(0);
                $table->timestamps();
                
                // Indexes from SQL
                $table->index('category_id');
                $table->index('source_id');
                $table->index('sourcefeed_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
