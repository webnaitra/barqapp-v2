<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('whatsapps')) {
            Schema::create('whatsapps', function (Blueprint $table) {
                $table->id();
                $table->string('whatsapp_number')->unique();
                $table->integer('whatsapp_user_id')->nullable();
                $table->timestamps();
                
                $table->index('whatsapp_user_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapps');
    }
};
