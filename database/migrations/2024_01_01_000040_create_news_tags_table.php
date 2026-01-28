<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('news_tags')) {
            Schema::create('news_tags', function (Blueprint $table) {
                $table->id();
                $table->foreignId('news_id')->nullable()->constrained('news')->cascadeOnDelete();
                $table->foreignId('tag_id')->nullable()->constrained('tags')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('news_tags');
    }
};
