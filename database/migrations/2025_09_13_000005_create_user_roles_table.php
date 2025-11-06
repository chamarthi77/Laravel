<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('user_roles')) {
            Schema::create('user_roles', function (Blueprint $t) {
                $t->id();
                $t->foreignId('user_id')->constrained()->cascadeOnDelete();
                $t->foreignId('role_id')->constrained()->cascadeOnDelete();
                $t->foreignId('community_id')->nullable()->constrained()->nullOnDelete();
                $t->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
                $t->timestamps();
            });
        }
    }
    public function down(): void { Schema::dropIfExists('user_roles'); }
};
