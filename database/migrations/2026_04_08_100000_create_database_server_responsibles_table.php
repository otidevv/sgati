<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('database_server_responsibles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('database_server_id')
                  ->constrained('database_servers')
                  ->cascadeOnDelete();

            $table->foreignId('persona_id')
                  ->constrained('personas')
                  ->restrictOnDelete();

            $table->enum('level', [
                'principal',
                'soporte',
                'supervision',
                'operador',
            ])->default('soporte');

            // Documento de respaldo
            $table->enum('document_type', [
                'resolucion_directoral',
                'resolucion_jefatural',
                'memorando',
                'oficio',
                'contrato',
                'acta',
                'otro',
            ])->nullable();

            $table->string('document_number', 100)->nullable();
            $table->date('document_date')->nullable();
            $table->text('document_notes')->nullable();

            // Control de auditoría
            $table->date('assigned_at');
            $table->date('unassigned_at')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('database_server_responsibles');
    }
};
