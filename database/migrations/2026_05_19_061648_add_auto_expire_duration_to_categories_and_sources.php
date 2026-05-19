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
        Schema::table('categories', function (Blueprint $table) {
            $table->integer('auto_expire_duration')->nullable()->after('fetch_frequency');
        });

        Schema::table('sources', function (Blueprint $table) {
            $table->integer('auto_expire_duration')->nullable()->after('fetch_frequency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('auto_expire_duration');
        });

        Schema::table('sources', function (Blueprint $table) {
            $table->dropColumn('auto_expire_duration');
        });
    }
};
