<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('favorites')) {
            Schema::create('favorites', function (Blueprint $table) {
                $table->id();
                $table->foreignId('fav_user_id')->nullable()->constrained('users')->cascadeOnDelete();
                $table->foreignId('fav_news_id')->nullable()->constrained('news')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
