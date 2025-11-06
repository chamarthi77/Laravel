<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $t) {
            if (!Schema::hasColumn('users','email_verified')) $t->boolean('email_verified')->default(false);
            if (!Schema::hasColumn('users','community_id')) $t->foreignId('community_id')->nullable()->constrained()->nullOnDelete();
            if (!Schema::hasColumn('users','organization_id')) $t->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $t) {
            if (Schema::hasColumn('users','organization_id')) $t->dropConstrainedForeignId('organization_id');
            if (Schema::hasColumn('users','community_id')) $t->dropConstrainedForeignId('community_id');
            if (Schema::hasColumn('users','email_verified')) $t->dropColumn('email_verified');
        });
    }
};
