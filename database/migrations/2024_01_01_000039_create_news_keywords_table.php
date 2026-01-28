<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('news_keywords')) {
            Schema::create('news_keywords', function (Blueprint $table) {
                $table->id();
                $table->foreignId('news_id')->nullable()->constrained('news')->cascadeOnDelete();
                $table->foreignId('keyword_id')->nullable()->constrained('keywords')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('news_keywords');
    }
};
