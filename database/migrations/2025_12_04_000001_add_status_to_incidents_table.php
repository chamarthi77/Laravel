<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds status column to incidents table for filtering by status in Flutter app.
     * Possible values: 'active', 'pending', 'resolved', 'closed'
     */
    public function up(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            // Add status column only if it does NOT already exist
            if (!Schema::hasColumn('incidents', 'status')) {
                $table->string('status', 20)->default('active')->after('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            // Remove status column only if it exists
            if (Schema::hasColumn('incidents', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
