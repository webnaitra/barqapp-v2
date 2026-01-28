<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('live_streams')) {
            Schema::create('live_streams', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('description')->nullable();
                $table->string('image')->nullable();
                $table->string('video')->nullable();
                $table->string('url')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('live_streams');
    }
};
