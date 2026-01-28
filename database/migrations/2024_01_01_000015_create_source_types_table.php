<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('source_types')) {
            Schema::create('source_types', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->nullable()->unique();
                $table->integer('status_id')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('source_types');
    }
};
