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
        Schema::table('users', function (Blueprint $table) {
            // Add latitude and longitude fields
            $table->decimal('latitude', 10, 6)->nullable()->after('email');
            $table->decimal('longitude', 10, 6)->nullable()->after('latitude');
            $table->string('address')->nullable()->after('longitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove the columns if the migration is rolled back
            $table->dropColumn(['latitude', 'longitude', 'address']);
        });
    }
};
