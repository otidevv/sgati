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
        Schema::create('database_servers', function (Blueprint $table) {
            $table->id();

            // Servidor físico donde corre este motor (nullable — podría ser externo/cloud)
            $table->foreignId('server_id')
                  ->nullable()
                  ->constrained('servers')
                  ->nullOnDelete();

            // Motor de base de datos
            $table->enum('engine', [
                'postgresql', 'mysql', 'mariadb',
                'oracle', 'sqlserver', 'sqlite', 'mongodb', 'other',
            ]);
            $table->string('version', 50)->nullable();      // 16.2, 8.0.35...

            // Conexión
            $table->string('host', 150)->nullable();        // IP o hostname
            $table->unsignedSmallInteger('port')->nullable(); // 5432, 3306...

            // Credenciales de administrador
            $table->string('admin_user', 100)->nullable();  // postgres, root
            $table->text('admin_password')->nullable();     // encriptado en modelo

            // Alias para identificar la instancia
            $table->string('name', 100)->nullable();        // "PostgreSQL Producción"

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
        Schema::dropIfExists('database_servers');
    }
};
