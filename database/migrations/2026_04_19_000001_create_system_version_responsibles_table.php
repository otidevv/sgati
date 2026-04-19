<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_version_responsibles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('system_version_id')->constrained('system_versions')->cascadeOnDelete();
            $table->foreignId('persona_id')->constrained('personas')->restrictOnDelete();
            $table->enum('role', [
                'lider_tecnico', 'desarrollador', 'analista',
                'tester', 'despliegue', 'aprobador',
            ])->default('desarrollador');
            $table->string('document_notes', 500)->nullable();
            $table->date('assigned_at');
            $table->date('unassigned_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_version_responsibles');
    }
};
