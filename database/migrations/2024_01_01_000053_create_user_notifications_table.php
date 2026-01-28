<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('user_notifications')) {
            Schema::create('user_notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('news_id')->constrained('news')->cascadeOnDelete();
                $table->enum('type', ['email', 'push']);
                $table->enum('status', ['sent', 'failed', 'pending'])->default('pending');
                $table->timestamp('sent_at')->nullable();
                $table->longText('content')->nullable(); // check json_valid constraint in MySQL, Laravel handles text
                $table->timestamps();
                
                $table->index(['user_id', 'type', 'sent_at']);
                $table->index('created_at');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_notifications');
    }
};
