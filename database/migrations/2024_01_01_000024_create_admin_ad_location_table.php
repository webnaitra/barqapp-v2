<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('admin_ad_location')) {
            Schema::create('admin_ad_location', function (Blueprint $table) {
                $table->id();
                $table->foreignId('admin_ad_id')->constrained('admin_ads')->cascadeOnDelete();
                $table->foreignId('location_id')->constrained('locations')->cascadeOnDelete();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_ad_location');
    }
};
