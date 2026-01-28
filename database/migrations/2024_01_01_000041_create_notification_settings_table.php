<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('notification_settings')) {
            Schema::create('notification_settings', function (Blueprint $table) {
                $table->id();
                $table->integer('setting_user_id')->nullable();
                $table->text('setting_cats')->nullable();
                $table->text('setting_sub_cats')->nullable();
                $table->integer('setting_urgent')->default(0);
                $table->timestamps();
                
                $table->index('setting_user_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};
