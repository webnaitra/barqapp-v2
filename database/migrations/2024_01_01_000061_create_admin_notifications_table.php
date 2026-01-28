<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('admin_notifications')) {
            Schema::create('admin_notifications', function (Blueprint $table) {
                $table->id();
                $table->text('notify_text');
                $table->string('notify_type')->nullable();
                $table->integer('notify_item_id')->nullable();
                $table->boolean('notify_read')->default(0);
                $table->string('notify_url')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_notifications');
    }
};
