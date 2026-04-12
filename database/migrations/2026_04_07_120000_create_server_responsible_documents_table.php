<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('server_responsible_documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('server_responsible_id')
                  ->constrained('server_responsibles')
                  ->cascadeOnDelete();

            $table->string('original_name');        // Nombre original del archivo
            $table->string('file_path');            // Ruta en disco (local privado)
            $table->string('description', 255)->nullable();
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
            $table->string('document_notes', 500)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('server_responsible_documents');
    }
};
