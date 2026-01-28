<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('advertisers')) {
            Schema::create('advertisers', function (Blueprint $table) {
                $table->id();
                $table->string('adv_first_name')->nullable();
                $table->string('adv_last_name')->nullable();
                $table->string('adv_username');
                $table->string('adv_email')->nullable()->unique();
                $table->integer('adv_age')->nullable();
                $table->string('adv_password')->nullable();
                $table->string('adv_mobile', 20)->nullable();
                $table->integer('image')->nullable(); // Original
                $table->string('adv_lang', 25)->default('ar');
                $table->enum('adv_login_type', ['default', 'social']);
                $table->string('adv_social')->nullable();
                $table->string('adv_forgot_password_code')->nullable();
                $table->boolean('email_notifications_enabled')->default(0);
                $table->boolean('push_notifications_enabled')->default(0);
                $table->dateTime('last_email_digest_sent')->nullable();
                $table->string('adv_verify_token', 191)->nullable();
                $table->string('adv_reset_token', 191)->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('advertisers');
    }
};
