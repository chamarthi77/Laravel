<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();   // SUPER_ADMIN, COMMUNITY_ADMIN, etc.
            $table->string('name');
            $table->unsignedTinyInteger('rank'); // 100, 70, 50, 10
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('roles');
    }
};
