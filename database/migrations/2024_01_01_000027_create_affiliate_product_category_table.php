<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('affiliate_product_category')) {
            Schema::create('affiliate_product_category', function (Blueprint $table) {
                $table->id();
                $table->integer('affiliate_id');
                $table->integer('product_category_id');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_product_category');
    }
};
