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
        Schema::table('system_databases', function (Blueprint $table) {
            // FK al motor de BD que aloja esta base de datos
            $table->foreignId('database_server_id')
                  ->nullable()
                  ->after('system_id')
                  ->constrained('database_servers')
                  ->nullOnDelete();

            // Credenciales propias de esta BD (usuario de app, no admin)
            $table->string('db_user', 100)->nullable()->after('schema_name');
            $table->text('db_password')->nullable()->after('db_user'); // encriptado en modelo

            // Eliminar campos que ahora viven en database_servers
            $table->dropColumn(['server_host', 'port']);
        });
    }

    public function down(): void
    {
        Schema::table('system_databases', function (Blueprint $table) {
            $table->dropForeign(['database_server_id']);
            $table->dropColumn(['database_server_id', 'db_user', 'db_password']);

            $table->string('server_host', 100)->nullable();
            $table->integer('port')->nullable();
        });
    }
};
