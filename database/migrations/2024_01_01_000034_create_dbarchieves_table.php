<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('dbarchieves')) {
            Schema::create('dbarchieves', function (Blueprint $table) {
                $table->id();
                $table->string('db_name');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('dbarchieves');
    }
};
