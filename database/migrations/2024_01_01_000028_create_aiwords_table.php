<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('aiwords')) {
            Schema::create('aiwords', function (Blueprint $table) {
                $table->id();
                $table->text('words')->nullable();
                $table->integer('category_id')->nullable();
                $table->timestamps();
                
                // Key length issues in MySQL might occur with text indexes, skipping raw index creation here to assume standard behavior or manageable by user. 
                // SQL had: KEY `word_word` (`word_words`(768))
                $table->index('category_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('aiwords');
    }
};
