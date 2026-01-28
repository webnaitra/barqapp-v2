<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('contacts')) {
            Schema::create('contacts', function (Blueprint $table) {
                $table->id(); // int(11) in SQL, but assume standard ID for Laravel
                $table->string('contact_name')->nullable();
                $table->string('contact_email')->nullable();
                $table->string('contact_message')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
