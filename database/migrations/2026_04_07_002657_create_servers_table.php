<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servers', function (Blueprint $table) {
            $table->id();

            // ── Identificación ────────────────────────────────────────
            $table->string('name', 100);
            $table->string('slug', 100)->unique();

            // ── Acceso remoto (SSH / RDP) ─────────────────────────────
            $table->string('ssh_user', 100)->nullable();
            $table->text('ssh_password')->nullable();          // encriptado en el modelo

            // ── Sistema operativo ─────────────────────────────────────
            $table->string('operating_system', 150)->nullable();

            // ── Función principal ─────────────────────────────────────
            $table->enum('function', [
                'production', 'development', 'staging',
                'database', 'backup', 'testing',
            ])->default('production');

            // ── Tipo de servidor ──────────────────────────────────────
            $table->enum('host_type', ['physical', 'virtual', 'cloud'])->default('physical');

            // ── Recursos de hardware ──────────────────────────────────
            $table->unsignedSmallInteger('cpu_cores')->nullable();
            $table->unsignedSmallInteger('ram_gb')->nullable();
            $table->unsignedSmallInteger('storage_gb')->nullable();

            // ── Nube (solo si host_type = cloud) ──────────────────────
            $table->enum('cloud_provider', [
                'aws', 'gcp', 'azure', 'digitalocean', 'linode', 'other',
            ])->nullable();
            $table->string('cloud_region', 50)->nullable();
            $table->string('cloud_instance', 100)->nullable();

            // ── Servicios y configuración ─────────────────────────────
            $table->json('installed_services')->nullable();
            $table->string('web_root', 255)->nullable();

            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();

            // ── Guacamole ─────────────────────────────────────────────
            $table->string('guacamole_connection_id')->nullable();
            $table->unsignedSmallInteger('rdp_port')->default(3389);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servers');
    }
};
