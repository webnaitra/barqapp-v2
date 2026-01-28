<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('files')) {
            Schema::create('files', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('name');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
