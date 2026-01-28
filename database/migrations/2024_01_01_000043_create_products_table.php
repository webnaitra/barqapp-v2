<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->id(); // int(11) increment
                $table->string('name')->nullable();
                $table->string('image')->nullable();
                $table->string('description')->nullable();
                $table->string('price')->nullable();
                $table->string('selling_price', 191);
                $table->string('url')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
