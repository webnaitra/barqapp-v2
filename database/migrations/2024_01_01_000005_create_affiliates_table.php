<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('affiliates')) {
            Schema::create('affiliates', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('image')->nullable();
                $table->string('url')->nullable();
                $table->string('description')->nullable();
                $table->string('price')->nullable();
                $table->string('selling_price', 191)->nullable();
                $table->foreignId('country_id')->nullable()->constrained('countries')->cascadeOnDelete();
                $table->timestamps();
                
                // Index from SQL dump
                $table->index('country_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliates');
    }
};
