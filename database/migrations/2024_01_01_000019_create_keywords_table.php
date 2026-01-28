<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('keywords')) {
            Schema::create('keywords', function (Blueprint $table) {
                $table->id();
                $table->string('keyword_name');
                $table->integer('image')->nullable();
                $table->integer('category_id')->nullable();
                $table->string('short_description', 191)->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('keywords');
    }
};
