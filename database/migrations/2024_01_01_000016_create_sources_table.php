<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('sources')) {
            Schema::create('sources', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('arabic_name')->nullable();
                $table->text('description')->nullable();
                $table->foreignId('country_id')->nullable()->constrained('countries')->cascadeOnDelete();
                $table->foreignId('source_type_id')->nullable()->constrained('source_types')->cascadeOnDelete();
                $table->boolean('freeze')->default(0);
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->string('website')->nullable();
                $table->string('placeholder_image')->nullable();
                $table->string('logo')->nullable();
                $table->text('filter_classes')->nullable();
                $table->text('content_classes')->nullable();
                $table->text('image_classes')->nullable();
                $table->timestamps();

                $table->index('source_type_id');
                $table->index('country_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sources');
    }
};
