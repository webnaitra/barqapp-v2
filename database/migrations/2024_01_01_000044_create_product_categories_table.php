<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('product_categories')) {
            Schema::create('product_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug');
                $table->string('color')->nullable();
                $table->integer('parent_id')->default(0);
                $table->integer('image')->nullable();
                $table->integer('order')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_categories');
    }
};
