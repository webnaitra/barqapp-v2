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
        if (!Schema::hasTable('admin_ads')) {
            Schema::create('admin_ads', function (Blueprint $table) {
                $table->id(); // int(11) auto_increment primary key
                $table->string('name')->nullable();
                $table->integer('image')->nullable(); // Original. Updated to string in later migration.
                $table->integer('source_icon')->nullable(); // Original. Updated to string in later migration.
                $table->integer('fav_count')->default(0);
                $table->integer('view_count')->default(0);
                $table->integer('share_count')->default(0);
                $table->string('url')->nullable();
                $table->string('help_text')->nullable();
                $table->string('type')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_ads');
    }
};
