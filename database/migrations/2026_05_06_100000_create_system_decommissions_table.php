<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_decommissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('system_id')->constrained('systems')->cascadeOnDelete();

            // Motivo de la baja
            $table->string('reason_type', 30);
            $table->text('reason');

            // Fechas clave
            $table->date('effective_date');
            $table->date('shutdown_date')->nullable();

            // Sustento documental
            $table->string('resolution_number', 100)->nullable();
            $table->date('resolution_date')->nullable();
            $table->string('resolution_entity', 200)->nullable();
            $table->string('memo_number', 100)->nullable();
            $table->date('memo_date')->nullable();

            // Autorización
            $table->string('authorized_by_name', 200)->nullable();
            $table->string('authorized_by_position', 200)->nullable();
            $table->foreignId('registered_by_user_id')->nullable()->constrained('users')->nullOnDelete();

            // Sistema sucesor / migración de datos
            $table->foreignId('successor_system_id')->nullable()->constrained('systems')->nullOnDelete();
            $table->string('successor_description', 500)->nullable();
            $table->boolean('data_migrated')->default(false);
            $table->string('data_migration_destination', 500)->nullable();
            $table->date('data_retention_until')->nullable();

            // Impacto y auditoría
            $table->unsignedInteger('users_affected')->nullable();
            $table->text('audit_notes')->nullable();
            $table->text('reactivation_procedure')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_decommissions');
    }
};
