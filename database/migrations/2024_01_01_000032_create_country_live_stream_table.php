<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('country_live_stream')) {
            Schema::create('country_live_stream', function (Blueprint $table) {
                $table->id();
                $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();
                $table->foreignId('live_stream_id')->constrained('live_streams')->cascadeOnDelete();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('country_live_stream');
    }
};
