<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('organizations')) {
            Schema::create('organizations', function (Blueprint $t) {
                $t->id();
                $t->foreignId('community_id')->constrained()->cascadeOnDelete();
                $t->string('name');
                $t->timestamps();
                $t->unique(['community_id','name']);
            });
        }
    }
    public function down(): void { Schema::dropIfExists('organizations'); }
};
