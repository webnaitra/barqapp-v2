<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('videos')) {
            Schema::create('videos', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->integer('image')->nullable();
                $table->string('video')->nullable();
                $table->string('source_id')->nullable();
                $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
                $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();
                $table->string('source_link')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
