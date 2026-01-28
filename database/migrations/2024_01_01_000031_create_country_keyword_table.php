<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('country_keyword')) {
            Schema::create('country_keyword', function (Blueprint $table) {
                $table->id();
                $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();
                $table->foreignId('keyword_id')->constrained('keywords')->cascadeOnDelete();
                // Removed timestamps as they were not present in SQL definition viewed, though some tables have them.
                // Re-checking SQL snippet: 322-330 no timestamps column mentioned.
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('country_keyword');
    }
};
