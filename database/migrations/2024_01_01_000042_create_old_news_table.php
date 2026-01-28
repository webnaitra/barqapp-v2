<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('old_news')) {
            Schema::create('old_news', function (Blueprint $table) {
                $table->id();
                $table->integer('news_id')->nullable();
                $table->string('news_title')->nullable(); // Assuming varchar based on context, actual dump snippet end was cut off but looked like news_archieves
                $table->string('news_database')->default('self');
                $table->timestamps();
                
                $table->index('news_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('old_news');
    }
};
