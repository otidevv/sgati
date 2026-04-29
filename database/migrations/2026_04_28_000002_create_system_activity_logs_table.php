<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('system_id')->constrained()->cascadeOnDelete();
            $table->foreignId('causer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('subject_type', 80);    // versión, repositorio, servicio…
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('event', 20);            // creado | actualizado | eliminado
            $table->json('properties')->nullable(); // {field: {old: x, new: y}, …}
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_activity_logs');
    }
};
