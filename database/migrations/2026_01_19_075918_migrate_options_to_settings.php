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
        $options = DB::table('options')->get();

        foreach ($options as $option) {
            $key = $option->option_name;
            $value = $option->option_value;
            $groupName = 'general'; // Force all to 'general' group for single Settings class

            // Insert into settings
            DB::table('settings')->insert([
                'group' => $groupName,
                'name' => $key,
                'locked' => false,
                'payload' => json_encode($value), // Spatie settings stores payload as JSON
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            //
        });
    }
};
