<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_database_responsible_documents', function (Blueprint $table) {
            $table->enum('document_type', [
                'resolucion_directoral', 'resolucion_jefatural', 'memorando',
                'oficio', 'contrato', 'acta', 'otro',
            ])->nullable()->after('description');
            $table->string('document_number', 100)->nullable()->after('document_type');
            $table->date('document_date')->nullable()->after('document_number');
            $table->string('document_notes', 500)->nullable()->after('document_date');
        });
    }

    public function down(): void
    {
        Schema::table('system_database_responsible_documents', function (Blueprint $table) {
            $table->dropColumn(['document_type', 'document_number', 'document_date', 'document_notes']);
        });
    }
};
