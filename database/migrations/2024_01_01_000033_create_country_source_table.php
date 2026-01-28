<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('country_source')) {
            Schema::create('country_source', function (Blueprint $table) {
                $table->id();
                $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();
                $table->foreignId('source_id')->constrained('sources')->cascadeOnDelete();
                // No timestamps in SQL snippet 354-362
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('country_source');
    }
};
