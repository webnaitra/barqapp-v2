<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('category_source')) {
            Schema::create('category_source', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
                $table->foreignId('source_id')->constrained('sources')->cascadeOnDelete();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('category_source');
    }
};
