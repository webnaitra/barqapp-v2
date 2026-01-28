<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('news_archieves')) {
            Schema::create('news_archieves', function (Blueprint $table) {
                // Note: The SQL dump showed 'id' int(11) NOT NULL but NO AUTO_INCREMENT and NO PRIMARY KEY in the CREATE definition (lines 635-641).
                // However usually ID is primary. I'll stick to the dump which didn't show Primary Key in that specific snippet block, 
                // but standard Laravel practice is ID. 
                // Wait, typical dump format might have Constraints at the end.
                // Looking at lines 636: `id` int(11) NOT NULL
                // It does NOT say AUTO_INCREMENT.
                // It does NOT say PRIMARY KEY inside the CREATE TABLE.
                // It might be intended as a log table without PK?
                // I will create it as integer 'id' but not auto-increment primary unless standard practice overrides.
                // Actually, let's make it standard enough to be useful but respect the schema.
                
                $table->integer('id'); 
                $table->text('news_title');
                $table->string('db_name');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('news_archieves');
    }
};
