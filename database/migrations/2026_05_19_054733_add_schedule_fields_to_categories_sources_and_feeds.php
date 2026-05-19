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
            $table->integer('fetch_frequency')->nullable()->after('featured');
        });

        Schema::table('sources', function (Blueprint $table) {
            $table->integer('fetch_frequency')->nullable()->after('freeze');
        });

        Schema::table('source_feeds', function (Blueprint $table) {
            $table->timestamp('last_fetched_at')->nullable()->after('status_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('fetch_frequency');
        });

        Schema::table('sources', function (Blueprint $table) {
            $table->dropColumn('fetch_frequency');
        });

        Schema::table('source_feeds', function (Blueprint $table) {
            $table->dropColumn('last_fetched_at');
        });
    }
};
