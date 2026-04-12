<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $docTypeEnum = ['resolucion_directoral', 'resolucion_jefatural', 'memorando', 'oficio', 'contrato', 'acta', 'otro'];

        // database_server_responsible_documents
        Schema::table('database_server_responsible_documents', function (Blueprint $table) use ($docTypeEnum) {
            $table->enum('document_type', $docTypeEnum)->nullable()->after('description');
            $table->string('document_number', 100)->nullable()->after('document_type');
            $table->date('document_date')->nullable()->after('document_number');
            $table->string('document_notes', 500)->nullable()->after('document_date');
        });

        // server_responsible_documents (por si tampoco tiene las columnas)
        if (! Schema::hasColumn('server_responsible_documents', 'document_type')) {
            Schema::table('server_responsible_documents', function (Blueprint $table) use ($docTypeEnum) {
                $table->enum('document_type', $docTypeEnum)->nullable()->after('description');
                $table->string('document_number', 100)->nullable()->after('document_type');
                $table->date('document_date')->nullable()->after('document_number');
                $table->string('document_notes', 500)->nullable()->after('document_date');
            });
        }
    }

    public function down(): void
    {
        Schema::table('database_server_responsible_documents', function (Blueprint $table) {
            $table->dropColumn(['document_type', 'document_number', 'document_date', 'document_notes']);
        });

        Schema::table('server_responsible_documents', function (Blueprint $table) {
            $table->dropColumn(['document_type', 'document_number', 'document_date', 'document_notes']);
        });
    }
};
