<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_service_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('system_service_id')->constrained('system_services')->cascadeOnDelete();
            $table->enum('document_type', ['solicitud', 'acta_entrega', 'oficio', 'contrato', 'memo', 'resolucion', 'otro']);
            $table->enum('direction', ['sent', 'received'])->default('sent');
            $table->string('original_name');
            $table->string('file_path');
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_service_documents');
    }
};
