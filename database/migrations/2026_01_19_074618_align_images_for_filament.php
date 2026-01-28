<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('admin_ads', function (Blueprint $table) {
            $table->string('image')->nullable()->change();
            $table->string('source_icon')->nullable()->change();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->string('image')->nullable()->change();
        });

        Schema::table('advertisers', function (Blueprint $table) {
            $table->string('image')->nullable()->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('image')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_ads', function (Blueprint $table) {
            $table->integer('image')->nullable()->change();
            $table->integer('source_icon')->nullable()->change();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->integer('image')->nullable()->change();
        });

        Schema::table('advertisers', function (Blueprint $table) {
            $table->integer('image')->nullable()->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->integer('image')->nullable()->change();
        });
    }
};
