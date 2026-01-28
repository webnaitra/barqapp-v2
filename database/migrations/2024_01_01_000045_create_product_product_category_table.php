<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('product_product_category')) {
            Schema::create('product_product_category', function (Blueprint $table) {
                $table->id();
                $table->integer('product_id');
                $table->integer('product_category_id');
                // No timestamps
                
                $table->index('product_id');
                $table->index('product_category_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_product_category');
    }
};
