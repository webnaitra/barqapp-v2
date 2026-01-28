<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('contactus')) {
            Schema::create('contactus', function (Blueprint $table) {
                $table->id();
                $table->string('contact_name')->nullable();
                $table->string('contact_title')->nullable();
                $table->text('contact_body')->nullable();
                $table->string('contact_email')->nullable();
                $table->string('contact_mobile')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('contactus');
    }
};
