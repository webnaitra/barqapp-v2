<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pages')) {
            Schema::create('pages', function (Blueprint $table) {
                $table->id();
                $table->string('page_name')->nullable();
                $table->text('page_content')->nullable();
                $table->integer('image')->nullable();
                $table->string('page_slug')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
