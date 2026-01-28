<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('social_media')) {
            Schema::create('social_media', function (Blueprint $table) {
                $table->id();
                $table->string('social_name')->nullable();
                $table->string('social_url')->nullable();
                $table->foreignId('source_id')->constrained('sources')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('social_media');
    }
};
