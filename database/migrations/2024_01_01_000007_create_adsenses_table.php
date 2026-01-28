<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SQL dump didn't explicitly show 'products' table in the snippet viewed, 
        // but it was part of previous tasks. Assuming it exists or will check file next.
        // For now, I'll create 'adsenses' which was in the view.
        if (!Schema::hasTable('adsenses')) {
            Schema::create('adsenses', function (Blueprint $table) {
                $table->id();
                $table->string('adsense_name')->nullable();
                $table->text('adsense_code')->nullable();
                $table->string('adsense_area')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('adsenses');
    }
};
