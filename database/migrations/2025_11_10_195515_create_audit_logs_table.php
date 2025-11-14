<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('user_email')->nullable();
            $table->string('action'); // e.g., CREATE, UPDATE, DELETE
            $table->string('entity_type'); // e.g., Incident, Project
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->json('details')->nullable(); // optional payload (changes, etc.)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
