<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_gateway_key_documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('gateway_key_id')
                  ->constrained('service_gateway_keys')
                  ->cascadeOnDelete();

            $table->string('original_name');
            $table->string('file_path');
            $table->string('description', 255)->nullable();
            $table->enum('document_type', [
                'solicitud',
                'resolucion_directoral',
                'resolucion_jefatural',
                'memorando',
                'oficio',
                'contrato',
                'acta',
                'convenio',
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
        Schema::dropIfExists('service_gateway_key_documents');
    }
};
