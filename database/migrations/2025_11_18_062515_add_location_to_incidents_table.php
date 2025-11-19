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
        Schema::table('incidents', function (Blueprint $table) {
            $table->decimal('latitude', 10, 6)->nullable()->after('project_id');
            $table->decimal('longitude', 10, 6)->nullable()->after('latitude');
            $table->string('location')->nullable()->after('longitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'location']);
        });
    }
};
