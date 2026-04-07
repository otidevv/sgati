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
        Schema::create('servers', function (Blueprint $table) {
            $table->id();

            // Identificación
            $table->string('name', 100);
            $table->string('slug', 100)->unique();

            // Red
            $table->string('ip_private', 45)->nullable();        // 192.168.254.xx
            $table->string('ip_public', 45)->nullable();

            // Acceso SSH
            $table->string('ssh_user', 100)->nullable();         // root
            $table->text('ssh_password')->nullable();            // encriptado en el modelo

            // Sistema operativo
            $table->string('operating_system', 150)->nullable(); // Ubuntu Server 24.04.2 LTS

            // Función principal del servidor
            $table->enum('function', [
                'production', 'development', 'staging',
                'database', 'backup', 'testing',
            ])->default('production');

            // Servicios instalados (Docker, Nginx, etc.) — array JSON
            $table->json('installed_services')->nullable();

            // Ruta raíz del servidor web
            $table->string('web_root', 255)->nullable();

            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servers');
    }
};
