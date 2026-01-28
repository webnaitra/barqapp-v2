<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tags')) {
            Schema::create('tags', function (Blueprint $table) {
                $table->id();
                $table->string('tag_name');
                $table->integer('image')->nullable();
                $table->string('type')->nullable();
                $table->timestamps();
                
                $table->index('tag_name');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
