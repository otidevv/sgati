<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('server_responsibles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('server_id')
                  ->constrained('servers')
                  ->cascadeOnDelete();

            $table->foreignId('persona_id')
                  ->constrained('personas')
                  ->restrictOnDelete();

            // Nivel de responsabilidad
            $table->enum('level', [
                'principal',   // Responsable principal del servidor
                'soporte',     // Soporte técnico
                'supervision', // Supervisión / auditoría
                'operador',    // Operador con acceso limitado
            ])->default('soporte');

            // Motivo de baja (se rellena al dar de baja)
            $table->text('document_notes')->nullable();

            // Control
            $table->date('assigned_at');
            $table->date('unassigned_at')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('server_responsibles');
    }
};
