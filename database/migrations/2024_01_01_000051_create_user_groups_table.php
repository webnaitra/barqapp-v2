<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('user_groups')) {
            Schema::create('user_groups', function (Blueprint $table) {
                $table->id(); // int(11) in dump
                $table->string('group_name')->nullable();
                $table->text('group_privilages')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_groups');
    }
};
