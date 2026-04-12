<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_database_responsibles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('system_database_id')->constrained('system_databases')->cascadeOnDelete();
            $table->foreignId('persona_id')->constrained('personas')->restrictOnDelete();
            $table->enum('level', ['principal', 'soporte', 'supervision', 'operador'])->default('principal');

            // Motivo de baja (se rellena al dar de baja)
            $table->string('document_notes', 500)->nullable();

            // Vigencia
            $table->date('assigned_at');
            $table->date('unassigned_at')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });

        // Quitar el campo de texto libre una vez creada la tabla relacional
        Schema::table('system_databases', function (Blueprint $table) {
            $table->dropColumn('responsible');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_database_responsibles');

        Schema::table('system_databases', function (Blueprint $table) {
            $table->string('responsible', 100)->nullable()->after('schema_name');
        });
    }
};
