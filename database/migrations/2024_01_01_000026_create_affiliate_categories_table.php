<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('affiliate_categories')) {
            Schema::create('affiliate_categories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('affiliate_id')->constrained('affiliates')->cascadeOnDelete();
                $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_categories');
    }
};
