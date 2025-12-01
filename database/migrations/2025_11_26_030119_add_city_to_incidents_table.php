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
            // Add city column only if it does NOT already exist
            if (!Schema::hasColumn('incidents', 'city')) {
                $table->string('city')->nullable()->after('project_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            // Remove city column only if it exists
            if (Schema::hasColumn('incidents', 'city')) {
                $table->dropColumn('city');
            }
        });
    }
};
