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
        Schema::create('server_containers', function (Blueprint $table) {
            $table->id();

            // Servidor donde corre el contenedor
            $table->foreignId('server_id')
                  ->constrained('servers')
                  ->cascadeOnDelete();

            // Sistema al que pertenece (nullable — contenedores compartidos como Redis, Nginx proxy)
            $table->foreignId('system_id')
                  ->nullable()
                  ->constrained('systems')
                  ->nullOnDelete();

            // Identificación del contenedor
            $table->string('name', 150);              // cepre-frontend, sgati-api
            $table->string('image', 150)->nullable();  // nginx:alpine, node:18, php:8.2-fpm

            // Rol del contenedor dentro de la arquitectura
            $table->enum('type', [
                'frontend',   // app web / SPA
                'backend',    // API / servidor de aplicación
                'database',   // motor de BD dentro de Docker
                'cache',      // Redis, Memcached
                'queue',      // worker de colas
                'proxy',      // Nginx / Traefik como reverse proxy
                'storage',    // MinIO, S3-compatible
                'other',
            ])->default('backend');

            // Red / puertos
            $table->unsignedSmallInteger('internal_port')->nullable(); // puerto dentro del contenedor
            $table->unsignedSmallInteger('external_port')->nullable(); // puerto expuesto en el host

            // Variables de entorno relevantes (sin secretos — para documentación)
            $table->json('env_vars')->nullable();

            // Volúmenes montados
            $table->json('volumes')->nullable();       // ['/data/cepre:/var/www/html']

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
        Schema::dropIfExists('server_containers');
    }
};
