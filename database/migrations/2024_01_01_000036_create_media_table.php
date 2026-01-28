<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Legacy media table from V2 structure, kept for backward compatibility if needed
        if (!Schema::hasTable('media')) {
            Schema::create('media', function (Blueprint $table) {
                $table->id();
                $table->string('type');
                $table->string('title');
                $table->string('alt')->nullable();
                $table->string('status')->nullable();
                $table->string('url')->nullable();
                $table->string('slug')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
