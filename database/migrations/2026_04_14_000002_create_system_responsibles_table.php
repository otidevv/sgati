<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_responsibles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('system_id')
                  ->constrained('systems')
                  ->cascadeOnDelete();

            $table->foreignId('persona_id')
                  ->constrained('personas')
                  ->restrictOnDelete();

            $table->enum('level', [
                'principal',   // Responsable principal del sistema
                'soporte',     // Soporte técnico
                'supervision', // Supervisión / auditoría
                'operador',    // Operador con acceso limitado
            ])->default('soporte');

            $table->text('document_notes')->nullable();

            $table->date('assigned_at');
            $table->date('unassigned_at')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_responsibles');
    }
};
