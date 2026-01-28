<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('source_feeds')) {
            Schema::create('source_feeds', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('source_url')->nullable();
                $table->foreignId('source_id')->default(0)->constrained('sources')->cascadeOnDelete();
                $table->foreignId('category_id')->nullable()->constrained('categories')->cascadeOnDelete();
                $table->boolean('status_id')->default(0);
                $table->boolean('freeze')->default(0);
                $table->timestamps();

                $table->index('source_id');
                $table->index('category_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('source_feeds');
    }
};
